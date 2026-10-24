<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/book-validation.php";

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {

    try  {
        $pdo->beginTransaction();

        $request = getBookRequest($_POST);
        $errors = validateBook($request, $request['id']);

        if (count($errors) > 0 ) {
            $pdo->rollBack();
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $request;

            header('Location: ../../pages/books/edit.php?id=' .urlencode($request['id']));
            exit;
        }

        

        $query = "update books set title = :title, isbn = :isbn, year = :year, stock = :stock, category_id = :category_id, description = :description where id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $request['title'],
            'isbn' => $request['isbn'],
            'year' => $request['year'],
            'stock' => $request['stock'],
            'category_id' => $request['category_id'],
            'description' => $request['description'],
            'id' => $request['id']
        ]);

        //delete
        $query = "delete from book_author where book_id = :book_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'book_id' => $request['id']
        ]);

        //insert
        $query = "insert into book_author (book_id, author_id) values (:book_id, :author_id)";
        $stmt = $pdo->prepare($query);
        
        $authorIds = isset($request['author_ids']) ? $request['author_ids'] : [];
        foreach($authorIds as $authorId) {
            $stmt->execute([
                'book_id' => $request['id'],
                'author_id' => htmlspecialchars($authorId)
            ]);
        }

        $pdo->commit();

        header('Location: ../../pages/books/index.php');
        exit;
    
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
    
}

?>

<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../validations/book-validation.php";


if (isset($_POST['store']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $pdo->beginTransaction();

        $request = getBookRequest($_POST);
        $errors = validateBook($request);

        if (count($errors) > 0 ) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $request;

            header('Location: ../../pages/books/create.php');
            exit;
        }

        $title = htmlspecialchars($_POST['title']);
        $isbn = htmlspecialchars($_POST['isbn']);
        $year = htmlspecialchars($_POST['year']);
        $stock = htmlspecialchars($_POST['stock']);
        $description = htmlspecialchars($_POST['description']);
        $categoryId = htmlspecialchars($_POST['category_id']);

        $query = "insert into books (title, isbn, year, stock, description, category_id) 
                  values (:title, :isbn, :year, :stock, :description, :category_id)
                  ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $request['title'],
            'isbn' => $request['isbn'],
            'year' => $request['year'],
            'stock' => $request['stock'],
            'description' => $request['description'],
            'category_id' => $request['categoryId']
        ]);

        $bookId = $pdo->lastInsertId();

        $query = "insert into book_author (book_id, author_id) values (:book_id, :author_id)";
        $stmt = $pdo->prepare($query);

        $authorIds = $_POST['author_ids'];

        $authorIds = $request['author_ids'];

        foreach($authorIds as $authorId) {
            $stmt->execute([
                'book_id' => $bookId,
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
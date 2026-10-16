<?php

function getBookRequest($request) 
{
    return [
        'title' => htmlspecialchars($request['title']),
        'isbn' => htmlspecialchars($request['isbn']),
        'year' => htmlspecialchars($request['year']),
        'stock' => htmlspecialchars($request['stock']),
        'description' => htmlspecialchars($request['description']),
        'categoryId' => htmlspecialchars($request['category_id']),
        'author_ids' => $request['author_ids'] ?? [],
    ];
}

function validateBook($request, $ignoreBookId = 0)    
{
    $errors =[];
    
    if($request['title'] === '') {
        $errors['title'] = 'Judul Buku wajib diisi';
    } elseif (mb_strlen($request['title']) > 100) {
        $errors['title'] = "Judul buku tidak boleh melebihi dari 100 karakter";
    }

    return $errors;
}
?>
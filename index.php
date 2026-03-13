  <?php

    $books = [
        [
            'name' => "Matarelist",
            'author' => "Dakota Jhonson",
            'releaseYear' => "2025"
        ],
        [
            'name' => "Fifty shades of gray",
            'author' => "Dakota Jhonson",
            'releaseYear' => "2015"
        ],
        [
            'name' => "Do android stream",
            'author' => "Kiven metnic",
            'releaseYear' => "2000"
        ]
    ];

    $filterdBooks = array_filter($books, function ($book) {
        return $book['author'] === 'Dakota Jhonson';
    });
    
    require "index.view.php";

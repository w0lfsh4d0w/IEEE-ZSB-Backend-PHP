<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo</title>
</head>

<body>
    <h1>Recommended Books</h1>
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
   
    $filterdBooks = array_filter($books, function($book){
        return $book['author']==='Dakota Jhonson';
    });
    ?>
    <ul>
        <?php foreach ($filterdBooks as $book): ?>
            <li>
                <?= $book['name'] ?> <?= "Written BY : {$book['author']} ." ?>
            </li>


        <?php endforeach ?>
    </ul>

</body>

</html>
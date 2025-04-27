<?php 
    // read JSON file
    $json = file_get_contents('data/data1.json');
    // decode data using json_decode function
    $data = json_decode($json, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/template1.css">
    <title>Template 1</title>
</head>
<body>
    <h1>Rendering JSON Content Below...</h1>
    <?php foreach ($data["posts"] as $post) { ?>
        <h2><?= $post["title"]?></h2>  
        <p><strong><?= $post["summary"] ?></strong></p> 
        <p><?= $post["post"] ?></p>
    <?php } ?>
</body>
</html>
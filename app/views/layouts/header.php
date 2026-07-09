<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'E Learning' ?></title>

    <?php 
        if(isset($styles)) {
            css(['base/main', ...$styles]);
        } else {
            css(['base/main']);
        }
    ?>


</head>
<body>

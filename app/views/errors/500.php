<!-- 500.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>

    <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/error.css">
</head>
<body>

<div class="error-container">
    <div class="error-card">
        <h1>500</h1>
        <h2>Internal Server Error</h2>

        <p>
            Something went wrong on our server.
            Please try again later.
        </p>

        <a href="<?= BASEURL . '/'  ?>" class="btn">Back To Home</a>
    </div>
</div>

</body>
</html>
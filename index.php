<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Upload PHP and MySQL</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }
        input {
            font-size: 1.5rem;
        }
        a {
            text-decoration: none;
            color: #006CFF;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <a href="view.php">Videos</a>
    <?php if (isset($_GET['error'])) { ?>
        <p><?php echo $_GET['error']; ?></p>
    <?php } ?>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="my_video" required>
        <button type="submit" name="submit">Upload Video</button>
    </form>
</body>
</html>

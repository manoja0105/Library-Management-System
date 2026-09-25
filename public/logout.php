<?php

session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
//kkkk
<div class="login-page">

    <div class="login-container">

        <h1>Logged Out</h1>

        <p style="text-align: center;">
            You have been successfully logged out.
        </p>

        <br>

        <a href="index.php" 
            class="btn primary full"
            style="text-align: center;">
            Go to Public Library
        </a>

        <br><br>

        <a href="login.php" 
            class="btn full"
            style="text-align: center;">
            Login Again
        </a>

    </div>
</div>

</body>

</html>
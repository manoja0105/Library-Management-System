<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

require_once "../config/db.php";

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    
    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

    
        $stmt = $conn->prepare(
            "SELECT id, username, password, role
             FROM users
             WHERE username = ?
             LIMIT 1"
        );

        if (!$stmt) {

            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();

        
            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();

                
                if (password_verify($password, $user["password"])) {

                    
                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["role"] = $user["role"];

                
                    header("Location: dashboard.php");
                    exit();

                } else {

                    $error = "Invalid username or password.";
                }

            } else {

                $error = "Invalid username or password.";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Librarian Login | Library Management System</title>


    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body class="login-page">

    <div class="login-container">

    
        <div class="login-header">

            <h1>Library Management System</h1>

            <p>Librarian Login</p>

        </div>


        
        <?php if ($error !== ""): ?>

            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


    
        <form method="POST"
              action=""
              class="login-form">

            
            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST["username"] ?? "") ?>"
                    required
                    autofocus
                >

            </div>


            
            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    autocomplete="current-password"
                    required>

            </div>


            
            <button
                type="submit"
                class="btn primary full">
                Login
            </button>

        </form>


        
        <div class="login-info">

            <p>
                <strong>Library Management System</strong>
            </p>

            <p>
                Librarian access only
            </p>

        </div>

    </div>

</body>

</html>
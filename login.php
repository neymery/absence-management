<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] == 'directeur') {
            header("Location: directeur/dashboard.php");
        } else {
            header("Location: surveillant/record_absence.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- <link rel="stylesheet" href="./css/styles.css"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body class=" d-flex aligh-items-center" style="height:100vh;">
<? include('./includes/header.php'); ?>
    <div class="text-center m-auto bg-light py-5 px-3 h-auto border border-top border-info"style="width: 500px;">
        <h4 class="text-muted">Gestion d'abscence des stagiaires</h4>
        <img src="./assets/ofppt-logo.png" alt="logo" height="100" width="100" class="my-2">
        <form method="POST" action="" class="">
        <input type="email" id="username" name="username" class="my-3 rounded outline-info form-control" placeholder="Entrer Username" required>
        <input type="password" id="password" name="password" class="my-3 rounded outline-info form-control" placeholder="Enter Password" required>
        <button type="submit" class=" rounded btn btn-primary btn-lg btn-block">S'authentifier</button>
        <?php if (isset($error)) { echo "<p class='text-danger'>$error</p>"; } ?>
    </form>
    <p>Pas de compte?<a href="register.php" class=""> S'inscrire</a></p>
</div>
</body>
</html>

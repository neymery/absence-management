<?php
session_start();
require 'includes/db.php';
require 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body class=" d-flex align-items-center" style="height:100vh;">
<div class="text-center m-auto bg-light py-5 px-3 h-auto border border-top border-info"style="width: 500px;">
<h4 class="text-muted">Gestion d'abscence des stagiaires</h4>
        <img src="./assets/ofppt-logo.png" alt="logo" height="100" width="100" class="my-2">    
    <form method="POST" action="">
        <input type="email" id="username" name="username" class="my-3 rounded form-control" placeholder="Entrer Username" required>
        <input type="password" id="password" name="password" class="my-3 rounded form-control" placeholder="Entrer Password" required>
        <select id="role" name="role" class="my-3 rounded form-control">
            <option value="surveillant">Surveillant</option>
            <option value="directeur">Directeur</option>
        </select>
        <button type="submit" class=" rounded btn btn-primary btn-lg btn-block">S'inscrire</button>
    </form>
    <p>j'ai un compte?<a href="./login.php">Login</a></p>
    </div>
</body>
</html>

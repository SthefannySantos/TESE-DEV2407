<?php
session_start();
require_once('db/conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameInput = $_POST['username'];
    $password = $_POST['password'];

    $username = $conn->real_escape_string($usernameInput);
    $password = $conn->real_escape_string($password);

    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();
        header("Location: index.php");
        exit();
    } else {
        echo '<script language="javascript" type="text/javascript">';
        echo 'alert("Usuário e/ou senha incorretos");';
        echo 'window.location.href="login.php";';
        echo '</script>';
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="src/styles/style.css">
    <link rel="stylesheet" href="src/styles/login-style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login:</h1>
        <form action="login.php" method="POST" class="form-container">
            <label>Usuário:</label>
            <input type="text" name="username" id="username" required>
            <label>Senha:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit" name="entrar" value="entrar" id="entrar">Entrar</button>
        </form>
    </div>
</body>
</html>

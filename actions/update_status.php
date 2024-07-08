<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.html");
    exit();
}

require_once('../db/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['id'];
    $status = $_POST['status'];
    $username = $_SESSION['username'];

    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
    } else {
        echo "Usuário não encontrado.";
        exit();
    }
    $stmt->close();

    $query = "UPDATE task SET completed = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $status, $taskId, $userId);

    if ($stmt->execute()) {
        header("Location: ../index.php");
    } else {
        echo "Erro ao atualizar status da tarefa: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>

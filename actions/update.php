<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.html");
    exit();
}

require_once('../db/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['task-date']) || empty($_POST['task-date'])) {
        echo "A data da tarefa não foi enviada ou está vazia.";
        exit();
    }

    $taskId = $_POST['id'];
    $taskDesc = $_POST['task-desc'];
    $taskDate = $_POST['task-date'];
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

    $query = "UPDATE task SET description = ?, task_date = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("ssii", $taskDesc, $taskDate, $taskId, $userId);

    if ($stmt->execute()) {
        header("Location: ../index.php");
    } else {
        echo "Erro ao atualizar tarefa: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>

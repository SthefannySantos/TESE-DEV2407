<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: ../login.html");
    exit();
}

require_once('../db/conexao.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID da tarefa não foi enviado ou está vazio.";
    exit();
}

$taskId = $_GET['id'];
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

$query = "DELETE FROM task WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($query);

$stmt->bind_param("ii", $taskId, $userId);

if ($stmt->execute()) {
    header("Location: ../index.php");
} else {
    echo "Erro ao deletar tarefa: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
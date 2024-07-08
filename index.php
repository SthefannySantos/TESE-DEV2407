<?php
session_start();

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit();
}

require_once('db/conexao.php');

$dateFilter = isset($_POST['filter-date']) ? $_POST['filter-date'] : '';
$clearFilter = isset($_POST['clear-filter']);

if ($clearFilter) {
    $dateFilter = '';
}

$tasks = [];

$query = "SELECT * FROM task WHERE user_id = ?";

if ($dateFilter) {
    $query .= " AND task_date = ?";
}

$stmt = $conn->prepare($query);

if ($dateFilter) {
    $stmt->bind_param("is", $user_id, $dateFilter);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    throw new Exception("Erro na consulta SQL: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    } 
}

$stmt->close();
$conn->close(); 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarefas</title>
    <link rel="stylesheet" href="src/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="src/scripts/script.js" defer></script>
</head>
<body>
    <div class="main-todo">
        <h1>Lista de Tarefas</h1>

        <form action="actions/create.php" method="POST" class="todo-form">
            <input type="text" name="task-desc" placeholder="Adicionar uma tarefa?" required>
            <input type="date" name="task-date" placeholder="Data da tarefa" required>
            <button class="form-btn" type="submit">
                <i class="fas fa-plus"></i>
            </button>
        </form>

        <form action="" method="POST" class="filter-form">
            <p>Filtrar:</p>
            <input type="date" class="filter-style" name="filter-date" value="<?= $dateFilter ?>">
            <button class="filter-style" type="submit">Filtrar</button>
            <button class="filter-style" type="submit" name="clear-filter" value="true">Limpar Filtro</button>
        </form>

        <div id="tasks">
            <?php if (empty($tasks)): ?>
                <p>Nenhuma tarefa encontrada.</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task">
                        <form action="actions/update_status.php" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">
                            <input type="hidden" name="status" value="<?= $task['completed'] ? 0 : 1 ?>">
                            <input type="checkbox" name="status-checkbox" class="status" <?= $task['completed'] ? 'checked' : '' ?> onchange="this.form.submit()">
                        </form>

                        <p class="task-desc"><?= $task['description']?></p>
                        <p class="task-date"><?= $task['task_date']?></p>

                        <div class="task-act">
                            <a class="act-btn edit-btn">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            <a href="actions/delete.php?id=<?= $task['id']?>" class="act-btn del-btn">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </div>

                        <form action="actions/update.php" method="POST" class="todo-form edit-task hidden">
                            <input type="hidden" name="id" value="<?= $task['id']?>">
                            <input type="text" name="task-desc" class="desc" placeholder="Edite sua tarefa" value="<?= $task['description']?>">
                            <input type="date" name="task-date" id="task-date" value="<?= $task['task_date'] ?>">
                            <button class="form-btn confirm-btn" type="submit">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</body>
</html>

<?php
// Inclui a conexão com o banco de dados
include 'db.php';

// Recupera as tarefas do banco de dados, agrupando por status
try {
    // Alteração no ORDER BY para usar o CASE
    $sql = "SELECT t.id, t.descricao, t.setor, t.prioridade, t.status, u.nome as usuario 
            FROM tarefa t
            INNER JOIN usuario u ON t.usuario_id = u.id
            ORDER BY 
                CASE 
                    WHEN t.status = 'a fazer' THEN 1
                    WHEN t.status = 'fazendo' THEN 2
                    WHEN t.status = 'pronto' THEN 3
                    ELSE 4
                END";
    $stmt = $pdo->query($sql);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erro ao recuperar as tarefas: " . $e->getMessage();
}

// Excluir tarefa
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $sql = "DELETE FROM tarefa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: gerenciar_tarefas.php');
            exit;
        } else {
            $error_message = "Erro ao excluir a tarefa.";
        }
    } catch (PDOException $e) {
        $error_message = "Erro ao excluir a tarefa: " . $e->getMessage();
    }
}

// Atualizar status da tarefa
if (isset($_POST['update_status'])) {
    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'];

    try {
        $sql = "UPDATE tarefa SET status = :new_status WHERE id = :task_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: gerenciar_tarefas.php');
            exit;
        } else {
            $error_message = "Erro ao atualizar o status da tarefa.";
        }
    } catch (PDOException $e) {
        $error_message = "Erro ao atualizar o status: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Tarefas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav>
    <ul>
        <li><a href="index.php">Cadastro de Usuário</a></li>
        <li><a href="cadastro_tarefa.php">Cadastro de Tarefas</a></li>
        <li><a href="gerenciar_tarefas.php">Gerenciar Tarefas</a></li>
    </ul>
</nav>

<!-- Exibe Mensagens de Erro ou Sucesso -->
<?php if (isset($error_message)): ?>
    <div class="alert error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Gerenciamento de Tarefas -->
<div class="task-container">
    <div class="task-column">
        <h3>A Fazer</h3>
        <?php foreach ($tarefas as $tarefa): ?>
            <?php if ($tarefa['status'] == 'a fazer'): ?>
                <div class="task-card">
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa['descricao']); ?></p>
                    <p><strong>Setor:</strong> <?php echo htmlspecialchars($tarefa['setor']); ?></p>
                    <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa['prioridade']); ?></p>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($tarefa['usuario']); ?></p>
                    <form action="cadastro_tarefa.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $tarefa['id']; ?>">
                        <button type="submit">Editar</button>
                    </form>
                    <a href="gerenciar_tarefas.php?delete_id=<?php echo $tarefa['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    <form action="gerenciar_tarefas.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $tarefa['id']; ?>">
                        <select name="new_status">
                            <option value="fazendo">Fazendo</option>
                            <option value="pronto">Pronto</option>
                        </select>
                        <button type="submit" name="update_status">Alterar Status</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="task-column">
        <h3>Fazendo</h3>
        <?php foreach ($tarefas as $tarefa): ?>
            <?php if ($tarefa['status'] == 'fazendo'): ?>
                <div class="task-card">
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa['descricao']); ?></p>
                    <p><strong>Setor:</strong> <?php echo htmlspecialchars($tarefa['setor']); ?></p>
                    <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa['prioridade']); ?></p>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($tarefa['usuario']); ?></p>
                    <form action="cadastro_tarefa.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $tarefa['id']; ?>">
                        <button type="submit">Editar</button>
                    </form>
                    <a href="gerenciar_tarefas.php?delete_id=<?php echo $tarefa['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    <form action="gerenciar_tarefas.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $tarefa['id']; ?>">
                        <select name="new_status">
                            <option value="a fazer">A Fazer</option>
                            <option value="pronto">Pronto</option>
                        </select>
                        <button type="submit" name="update_status">Alterar Status</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="task-column">
        <h3>Pronto</h3>
        <?php foreach ($tarefas as $tarefa): ?>
            <?php if ($tarefa['status'] == 'pronto'): ?>
                <div class="task-card">
                    <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa['descricao']); ?></p>
                    <p><strong>Setor:</strong> <?php echo htmlspecialchars($tarefa['setor']); ?></p>
                    <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa['prioridade']); ?></p>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($tarefa['usuario']); ?></p>
                    <form action="cadastro_tarefa.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $tarefa['id']; ?>">
                        <button type="submit">Editar</button>
                    </form>
                    <a href="gerenciar_tarefas.php?delete_id=<?php echo $tarefa['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    <form action="gerenciar_tarefas.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $tarefa['id']; ?>">
                        <select name="new_status">
                            <option value="a fazer">A Fazer</option>
                            <option value="fazendo">Fazendo</option>
                        </select>
                        <button type="submit" name="update_status">Alterar Status</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

<?php
// Inclui a conexão com o banco de dados
include 'db.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $usuario_id = $_POST['usuario_id'];
    $descricao = $_POST['descricao'];
    $prioridade = $_POST['prioridade'];
    $setor = $_POST['setor'];

    // Valida os dados de entrada
    $errors = [];

    if (empty($usuario_id)) {
        $errors[] = "O campo 'Usuário' é obrigatório.";
    }

    if (empty($descricao)) {
        $errors[] = "O campo 'Descrição' é obrigatório.";
    }

    if (empty($prioridade)) {
        $errors[] = "O campo 'Prioridade' é obrigatório.";
    }

    if (empty($setor)) {
        $errors[] = "O campo 'Setor' é obrigatório.";
    }

    // Se não houver erros, tenta inserir os dados no banco de dados
    if (empty($errors)) {
        try {
            // Insere a nova tarefa no banco de dados
            $sql = "INSERT INTO tarefa (usuario_id, descricao, prioridade, setor, status) 
                    VALUES (:usuario_id, :descricao, :prioridade, :setor, 'a fazer')";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':prioridade', $prioridade);
            $stmt->bindParam(':setor', $setor);

            if ($stmt->execute()) {
                $success_message = "Cadastro de tarefa concluído com sucesso!";
            } else {
                $error_message = "Erro ao cadastrar a tarefa. Tente novamente.";
            }
        } catch (PDOException $e) {
            $error_message = "Erro ao conectar ou executar o SQL: " . $e->getMessage();
        }
    } else {
        // Armazena as mensagens de erro
        $error_message = implode("<br>", $errors);
    }
}

// Recupera os usuários cadastrados no banco de dados
try {
    $sql = "SELECT * FROM usuario";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erro ao recuperar os usuários: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Tarefa</title>
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
<?php if (isset($success_message)): ?>
    <div class="alert success">
        <?php echo $success_message; ?>
    </div>
<?php elseif (isset($error_message)): ?>
    <div class="alert error">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!-- Formulário de Cadastro de Tarefa -->
<div class="form-container">
    <h2>Cadastro de Tarefa</h2>
    <form action="cadastro_tarefa.php" method="POST">
        <label for="usuario_id">Usuário:</label>
        <select id="usuario_id" name="usuario_id" required>
            <option value="">Selecione um Usuário</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nome']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required></textarea>

        <label for="prioridade">Prioridade:</label>
        <select id="prioridade" name="prioridade" required>
            <option value="">Selecione a Prioridade</option>
            <option value="baixa">Baixa</option>
            <option value="média">Média</option>
            <option value="alta">Alta</option>
        </select>

        <label for="setor">Setor:</label>
        <input type="text" id="setor" name="setor" required>

        <button type="submit">Cadastrar</button>
    </form>
</div>

</body>
</html>

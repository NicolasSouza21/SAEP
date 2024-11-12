<?php
// Inclui a conexão com o banco de dados
include 'db.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Valida os dados de entrada
    $errors = [];

    if (empty($nome)) {
        $errors[] = "O campo 'Nome' é obrigatório.";
    }
    
    if (empty($email)) {
        $errors[] = "O campo 'Email' é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O email fornecido não é válido.";
    }

    // Se não houver erros, verifica se o email já está cadastrado
    if (empty($errors)) {
        try {
            // Verifica se o email já existe no banco de dados
            $sql = "SELECT * FROM usuario WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Se o email já existir, adiciona erro
            if ($stmt->rowCount() > 0) {
                $errors[] = "O email '$email' já está cadastrado.";
            }

        } catch (PDOException $e) {
            $errors[] = "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }
    }

    // Se não houver erros, tenta inserir os dados no banco de dados
    if (empty($errors)) {
        try {
            // Insere o novo usuário no banco de dados
            $sql = "INSERT INTO usuario (nome, email) VALUES (:nome, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                $success_message = "Usuário cadastrado com sucesso!";
            } else {
                $errors[] = "Erro ao cadastrar o usuário. Tente novamente.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erro ao executar a inserção no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
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

<!-- Formulário de Cadastro de Usuário -->
<div class="form-container">
    <h2>Cadastro de Usuário</h2>
    <form action="index.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        
        <button type="submit">Cadastrar</button>
    </form>
</div>

<!-- Exibe Mensagens de Erro ou Sucesso (Abaixo do Formulário) -->
<?php if (isset($success_message)): ?>
    <div class="alert success">
        <?php echo $success_message; ?>
    </div>
<?php elseif (!empty($errors)): ?>
    <div class="alert error">
        <?php echo implode('<br>', $errors); ?>
    </div>
<?php endif; ?>

</body>
</html>

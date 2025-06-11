<?php
// Ativar exibição de erros para depuração (pode remover depois de resolver o problema)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once 'database/conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adicione este VAR_DUMP AQUI para verificar o $pdo NO MOMENTO DO ERRO
    // Se ele ainda aparecer como 'NULL' ou 'Undefined variable', a variável $pdo está sendo perdida
    // ou não inicializada corretamente neste contexto POST.
    // var_dump($pdo); // <<<<<< ADICIONE ESTA LINHA PARA DEBUGAR APÓS A SUBMISSÃO

    $username = trim($_POST['username']);
    $password = $_POST['password']; // Senha em texto puro, será hashed
    $email = trim($_POST['email']);

    // Validação básica de entrada
    if (empty($username) || empty($password) || empty($email)) {
        $mensagem = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Formato de e-mail inválido.";
    } else {
        // Hashing da senha: ESSENCIAL para segurança!
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // AQUI ESTÁ A LINHA QUE ESTAVA DANDO ERRO (LINHA 22 NO SEU CÓDIGO ORIGINAL)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $mensagem = "Nome de usuário ou e-mail já cadastrado.";
            } else {
                // Inserir novo usuário no banco de dados
                $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashed_password, $email])) {
                    $mensagem = "Cadastro realizado com sucesso! Você já pode fazer login.";
                    // Opcional: redirecionar para a página de login
                    // header("Location: login.php");
                    // exit();
                } else {
                    $mensagem = "Erro ao cadastrar usuário. Tente novamente.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema Lanchonete</title>
    <link rel="stylesheet" href="css/geral.css"> <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group input[type="email"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #228B22;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #1a6b1a;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .link {
            display: block;
            margin-top: 15px;
            color: #228B22;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastre-se</h2>
        <?php if ($mensagem): ?>
            <div class="message <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'success' : 'error'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        <form action="cadastro.php" method="POST">
            <div class="form-group">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Cadastrar</button>
        </form>
        <a href="login.php" class="link">Já tem uma conta? Faça login aqui.</a>
        <a href="index.php" class="link">Voltar para a página inicial</a>
    </div>
</body>
</html>
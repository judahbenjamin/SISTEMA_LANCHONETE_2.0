<?php
// Ativar exibição de erros para depuração (REMOVER EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requer o arquivo de verificação de login, se existir e for necessário
// require_once('verifica_login.php');

// Inclui o arquivo de conexão com o banco de dados.
// Assumimos que 'conexao.php' define a variável $pdo como o objeto de conexão PDO.
require_once('../database/conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebLanche - Status do Pedido</title>
    <link rel="stylesheet" href="../css/geral.css">
    <style>
        /* Estilos específicos para a página de status */
        .message-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-sizing: border-box;
            margin-top: 50px; /* Espaço entre o h1 e a caixa de mensagem */
        }

        .message-container h2 {
            margin-top: 0;
            font-size: 1.8em;
            color: #333;
        }

        .success-message {
            color: #28a745; /* Verde para sucesso */
            font-weight: bold;
            font-size: 1.2em;
        }

        .error-message {
            color: #dc3545; /* Vermelho para erro */
            font-weight: bold;
            font-size: 1.2em;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background-color: #007bff; /* Azul para o botão de voltar */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .back-link:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        }
        /* Ajuste para o body no processa_pedido para centralizar apenas um elemento */
        body {
            display: flex;
            flex-direction: column; /* Coloca os elementos em coluna */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #ffbf0e 0%, #fc7425 100%);
        }
    </style>
</head>
<body>
    <h1>WebLanche - Status do Pedido</h1>
    <div class="message-container">
        <?php
        // Verifica se os dados foram enviados via POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                // Coleta e sanitiza os dados do formulário
                // PDO com prepared statements não precisa de real_escape_string
                $nomeCliente = trim($_POST['nomeCliente'] ?? '');
                $bairroCliente = trim($_POST['bairroCliente'] ?? '');
                $qtde = (int)($_POST['qtde'] ?? 0); // Converte para inteiro, com valor padrão 0
                $idLanche = (int)($_POST['idLanche'] ?? 0); // Converte para inteiro, com valor padrão 0

                // Validação básica dos dados
                if (empty($nomeCliente) || empty($bairroCliente) || $qtde <= 0 || $idLanche <= 0) {
                    echo "<h2>Ocorreu um Erro!</h2>";
                    echo "<p class='error-message'>Todos os campos são obrigatórios e a quantidade deve ser maior que zero.</p>";
                } else {
                    // Prepara a instrução SQL para inserção usando PDO
                    $sql = "INSERT INTO Pedidos (idLanche, nomeCliente, bairroCliente, qtde) VALUES (?, ?, ?, ?)";

                    // Prepara a declaração para evitar injeção SQL usando PDO
                    $stmt = $pdo->prepare($sql); // <<<<<<< Corrigido: usando $pdo

                    // Executa a declaração, passando os parâmetros como um array
                    if ($stmt->execute([$idLanche, $nomeCliente, $bairroCliente, $qtde])) { // <<<<<<< Corrigido: parâmetros para execute
                        echo "<h2>Sucesso!</h2>";
                        echo "<p class='success-message'>Pedido cadastrado com sucesso!</p>";
                    } else {
                        echo "<h2>Ocorreu um Erro!</h2>";
                        // Para ver o erro específico do PDO, você pode usar $stmt->errorInfo()
                        $errorInfo = $stmt->errorInfo();
                        echo "<p class='error-message'>Erro ao cadastrar pedido: " . htmlspecialchars($errorInfo[2] ?? 'Erro desconhecido') . "</p>";
                    }

                    // Em PDO, $stmt->close() não é estritamente necessário, mas pode ser incluído se desejar
                    // $stmt->closeCursor(); // Libera o cursor do PDO
                }
            } catch (PDOException $e) {
                // Captura exceções do PDO (erros de conexão ou consulta)
                echo "<h2>Ocorreu um Erro!</h2>";
                echo "<p class='error-message'>Erro no banco de dados: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            // Se a página for acessada diretamente sem POST
            echo "<h2>Acesso Inválido!</h2>";
            echo "<p class='error-message'>Por favor, preencha o formulário de pedido.</p>";
        }

        // Em PDO, a conexão é automaticamente fechada quando o script termina
        // $pdo = null; // Opcional: desconectar explicitamente
        ?>
        <a href='form_lanche.php' class='back-link'>Voltar para o Formulário</a>
    </div>
</body>
</html>
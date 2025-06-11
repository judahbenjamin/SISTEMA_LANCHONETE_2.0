<?php
// Ativar exibição de erros para depuração (REMOVER EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o arquivo de conexão com o banco de dados.
// Assumimos que 'conexao.php' define a variável $pdo como o objeto de conexão PDO.
require_once('../database/conexao.php');

// Se você tiver um sistema de login e quiser proteger esta página
// require_once('verifica_login.php');
?>
<!DOCTYPE html>
<html lang="pt-br"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebLanche - Excluir Pedido</title>
    <link rel="stylesheet" href="../css/geral.css">
    <style>
        /* Estilos específicos para a página de exclusão (similar à de status) */
        .message-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-sizing: border-box;
            margin-top: 50px;
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
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #ffbf0e 0%, #fc7425 100%); /* Adicionei o background para corresponder às outras páginas */
        }
    </style>
</head>
<body>
    <h1>WebLanche - Excluir Pedido</h1>
    <div class="message-container">
        <?php
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $idPedido = (int)$_GET['id']; // Garante que é um número inteiro

            // Validação básica do ID
            if ($idPedido <= 0) {
                echo "<h2>Erro!</h2>";
                echo "<p class='error-message'>ID de pedido inválido.</p>";
            } else {
                try {
                    // Prepara a instrução SQL para exclusão usando PDO
                    $sql = "DELETE FROM Pedidos WHERE idPedido = ?";

                    // Prepara a declaração usando PDO
                    $stmt = $pdo->prepare($sql); // <<<<<<< Corrigido: usando $pdo

                    // Executa a declaração, passando o ID como parâmetro em um array
                    if ($stmt->execute([$idPedido])) { // <<<<<<< Corrigido: parâmetros para execute
                        // affected_rows em PDO é obtido via rowCount() no objeto statement
                        if ($stmt->rowCount() > 0) {
                            echo "<h2>Sucesso!</h2>";
                            echo "<p class='success-message'>Pedido excluído com sucesso!</p>";
                        } else {
                            echo "<h2>Atenção!</h2>";
                            echo "<p class='error-message'>Nenhum pedido encontrado com o ID " . htmlspecialchars($idPedido) . " ou o pedido já foi excluído.</p>";
                        }
                    } else {
                        echo "<h2>Ocorreu um Erro!</h2>";
                        // Para ver o erro específico do PDO, você pode usar $stmt->errorInfo()
                        $errorInfo = $stmt->errorInfo();
                        echo "<p class='error-message'>Erro ao excluir pedido: " . htmlspecialchars($errorInfo[2] ?? 'Erro desconhecido') . "</p>";
                    }

                    // Em PDO, $stmt->close() não é estritamente necessário
                    // $stmt->closeCursor(); // Opcional: Libera o cursor do PDO
                } catch (PDOException $e) {
                    // Captura exceções do PDO (erros de conexão ou consulta)
                    echo "<h2>Ocorreu um Erro!</h2>";
                    echo "<p class='error-message'>Erro no banco de dados: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        } else {
            echo "<h2>Erro!</h2>";
            echo "<p class='error-message'>ID do pedido não especificado para exclusão.</p>";
        }

        // Em PDO, a conexão é automaticamente fechada quando o script termina
        // $pdo = null; // Opcional: desconectar explicitamente
        ?>
        <a href='pesquisar_pedidos.php' class='back-link'>Voltar para Pesquisa de Pedidos</a>
    </div>
</body>
</html>
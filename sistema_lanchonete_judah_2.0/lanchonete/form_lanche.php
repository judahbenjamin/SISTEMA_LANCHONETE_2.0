<?php
// Ativar exibição de erros para depuração (pode remover depois de resolver o problema)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('verifica_login.php');
require_once('../database/conexao.php'); // Certifique-se de que este caminho está correto para o seu conexao.php

// Lógica para buscar os lanches do banco de dados usando PDO
$lanches = [];
try {
    // Utilize o objeto $pdo para a consulta
    $stmt = $pdo->query("SELECT idLanche, nomeLanche, valor FROM Lanches ORDER BY nomeLanche");
    $lanches = $stmt->fetchAll(PDO::FETCH_ASSOC); // FetchAll para obter todos os resultados
} catch (PDOException $e) {
    // Em caso de erro na consulta, exibe uma mensagem
    // Em produção, você pode querer logar isso em vez de exibir
    echo "Erro ao buscar lanches: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebLanche - Cadastro de Pedidos</title>
    <link rel="stylesheet" href="../css/geral.css">
    <style>
        /* Estilos inline para o botão de voltar e background */
        .botao-voltar {
            margin-top: 25px;
            display: inline-block;
            padding: 12px 25px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .botao-voltar:hover {
            background-color: #0056b3; /* Um tom de azul mais escuro */
        }
        body {
            background: linear-gradient(to right, #ffbf0e 0%, #fc7425 100%);
        }
        /* Adicione estilos para centralizar o formulário e dar um visual melhor */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column; /* Para empilhar o h1, form e botões */
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-bottom: 20px; /* Espaço entre o form e os botões */
        }
        h1 {
            color: #fff; /* Cor do título */
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3); /* Sombra para o título */
            margin-bottom: 25px;
        }
        .nome, .bairro, .quantidade, .lanche {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: calc(100% - 22px); /* Ajuste para padding e border */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Inclui padding e borda no width */
        }
        .button-container {
            display: flex;
            justify-content: space-around;
            gap: 10px;
            margin-top: 20px;
        }
        .button-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            flex: 1; /* Para que os botões ocupem o mesmo espaço */
            transition: background-color 0.3s ease;
        }
        .button-container button[type="reset"] {
            background-color: #6c757d; /* Cinza para limpar */
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
        .button-container button[type="reset"]:hover {
            background-color: #5a6268;
        }
        .welcome-message {
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>WebLanche - Cadastro de Pedidos</h1>
    <div class="welcome-message">
        Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>!
    </div>
    <form action="processa_pedido.php" method="post">
        <div class="nome">
            <label for="name">Nome do Cliente:</label>
            <input type="text" id="name" name="nomeCliente" required>
        </div>
        <div class="bairro">
            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairroCliente" required>
        </div>
        <div class="quantidade">
            <label for="quant">Quantidade:</label>
            <input type="number" id="quant" name="qtde" required min="1">
        </div>
        <div class="lanche">
            <label for="listLanche">Lanche:</label>
            <select name="idLanche" id="listLanche" required>
                <option value="">Selecione</option>
                <?php
                // Itera sobre os lanches obtidos do banco de dados e cria as opções
                if (!empty($lanches)) {
                    foreach ($lanches as $lanche) {
                        echo "<option value='" . htmlspecialchars($lanche["idLanche"]) . "'>" . htmlspecialchars($lanche["nomeLanche"]) . " (R$ " . number_format($lanche['valor'], 2, ',', '.') . ")</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum lanche encontrado</option>";
                }
                // Não é necessário fechar a conexão com $conn->close() aqui, pois PDO gerencia isso automaticamente
                // e a conexão pode ser reutilizada por outros scripts ou no final da execução.
                ?>
            </select>
        </div>
        <div class="button-container">
            <button type="submit"><span>Fazer Pedido</span></button>
            <button type="reset"><span>Limpar Campos</span></button>
        </div>
    </form>

    <div>
        <a href="pesquisar_pedidos.php" class="botao-voltar">Voltar para ver os Pedidos</a>
        <a href="../index.php" class="botao-voltar">Voltar para a Página Inicial</a>
    </div>
</body>
</html>
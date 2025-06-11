<?php
// Ativar exibição de erros para depuração (REMOVER EM PRODUÇÃO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('verifica_login.php');
require_once('../database/conexao.php'); // Inclui a conexão PDO ($pdo)

$pedidos = [];
$mensagem_pesquisa = '';

// Lógica de pesquisa ou exibição de todos os pedidos
if (isset($_GET['tipo_pesquisa']) && !empty($_GET['valor_pesquisa'])) {
    $tipo_pesquisa = $_GET['tipo_pesquisa'];
    $valor_pesquisa = trim($_GET['valor_pesquisa']); // Remove espaços em branco

    $sql = "SELECT p.idPedido, p.nomeCliente, p.bairroCliente, p.qtde, l.nomeLanche, l.valor
            FROM Pedidos p
            JOIN Lanches l ON p.idLanche = l.idLanche";

    $params = [];

    if ($tipo_pesquisa == 'cliente') {
        $sql .= " WHERE p.nomeCliente LIKE ?";
        $params[] = '%' . $valor_pesquisa . '%';
    } elseif ($tipo_pesquisa == 'bairro') {
        $sql .= " WHERE p.bairroCliente LIKE ?";
        $params[] = '%' . $valor_pesquisa . '%';
    }
    // Você pode adicionar mais opções de pesquisa aqui (ex: por nome do lanche)

    $sql .= " ORDER BY p.idPedido ASC";

    try {
        $stmt = $pdo->prepare($sql); // Prepara a consulta usando PDO
        $stmt->execute($params); // Executa a consulta passando os parâmetros
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtém todos os resultados

        if (empty($pedidos)) {
            $mensagem_pesquisa = "Nenhum pedido encontrado para o termo: '" . htmlspecialchars($valor_pesquisa) . "'.";
        }

    } catch (PDOException $e) {
        $mensagem_pesquisa = "Erro ao buscar pedidos: " . $e->getMessage();
    }
} else {
    // Se não houver termo de pesquisa ou a página for carregada pela primeira vez, exibe todos os pedidos
    try {
        $sql_todos = "SELECT p.idPedido, p.nomeCliente, p.bairroCliente, p.qtde, l.nomeLanche, l.valor
                      FROM Pedidos p
                      JOIN Lanches l ON p.idLanche = l.idLanche
                      ORDER BY p.idPedido ASC";
        $stmt_todos = $pdo->query($sql_todos);
        $pedidos = $stmt_todos->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pedidos)) {
            $mensagem_pesquisa = "Nenhum pedido cadastrado no sistema.";
        }
    } catch (PDOException $e) {
        $mensagem_pesquisa = "Erro ao carregar todos os pedidos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebLanche - Pesquisar Pedidos</title>
    <link rel="stylesheet" href="../css/geral.css">
    <style>
        /* Estilos específicos para a página de pesquisa */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #ffbf0e 0%, #fc7425 100%);
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            margin-top: 20px;
            font-size: 2.8em;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        }

        .search-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            margin-bottom: 30px;
            transition: transform 0.3s ease-in-out;
        }

        .search-container:hover {
            transform: translateY(-5px);
        }

        .search-form-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            margin-bottom: 0;
        }

        .search-form-group label {
            white-space: nowrap;
            font-weight: bold;
            color: #444;
            font-size: 1.1em;
        }

        .search-form-group select,
        .search-form-group input[type="text"] {
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            font-size: 1.05em;
            box-sizing: border-box;
            flex-grow: 1;
            min-width: 120px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-form-group select:focus,
        .search-form-group input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .search-form-group button {
            padding: 12px 25px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .search-form-group button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        /* Estilos da Tabela de Resultados */
        .results-container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 850px;
            box-sizing: border-box;
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 10px;
            overflow: hidden;
        }

        table th,
        table td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        table th {
            background-color: #fc7425;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.95em;
        }

        table tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        table tr:hover {
            background-color: #e0f2f7;
        }

        .no-results {
            text-align: center;
            padding: 25px;
            color: #666;
            font-size: 1.2em;
            font-style: italic;
        }

        /* Estilo do botão de Excluir na tabela */
        .delete-icon {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 32px;
            height: 32px;
            background-color: #dc3545;
            border-radius: 50%;
            color: white;
            font-size: 1.3em;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        }

        .delete-icon:hover {
            background-color: #c82333;
            transform: scale(1.15);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* Estilo para os botões de Navegação (Voltar para o Cadastro, Voltar para a Página Inicial) */
        /* Reutilizando a classe .botao-voltar que já está definida no seu CSS */
        .navigation-buttons {
            display: flex;
            gap: 20px; /* Espaçamento entre os botões */
            margin-top: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Permite que os botões quebrem a linha em telas menores */
            justify-content: center;
        }

        .btn-link { /* Nova classe para os links que serão estilizados como botões */
            display: inline-block;
            padding: 14px 30px;
            background-color: #007bff; /* Azul */
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 17px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .btn-link:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 123, 255, 0.4);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .search-container, .results-container {
                padding: 20px;
                margin-left: 15px;
                margin-right: 15px;
            }
            h1 {
                font-size: 2.2em;
                margin-left: 15px;
                margin-right: 15px;
            }
            .search-form-group {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            .search-form-group select,
            .search-form-group input[type="text"],
            .search-form-group button {
                width: 100%;
            }
            .navigation-buttons {
                flex-direction: column; /* Coloca os botões em coluna em telas menores */
                align-items: stretch;
            }
            .btn-link {
                width: 100%; /* Ocupa a largura total */
            }
        }

        @media (max-width: 480px) {
            .search-container, .results-container {
                padding: 15px;
            }
            h1 {
                font-size: 1.8em;
            }
            table th, table td {
                padding: 10px;
                font-size: 0.9em;
            }
            .delete-icon {
                width: 25px;
                height: 25px;
                font-size: 1.1em;
                line-height: 25px;
            }
        }
    </style>
</head>
<body>
    <h1>WebLanche - Pesquisar Pedidos</h1>

    <div class="search-container">
        <form action="pesquisar_pedidos.php" method="GET">
            <div class="search-form-group">
                <label for="tipo_pesquisa">Pesquisar por:</label>
                <select name="tipo_pesquisa" id="tipo_pesquisa">
                    <option value="cliente" <?php if (isset($_GET['tipo_pesquisa']) && $_GET['tipo_pesquisa'] == 'cliente') echo 'selected'; ?>>Cliente</option>
                    <option value="bairro" <?php if (isset($_GET['tipo_pesquisa']) && $_GET['tipo_pesquisa'] == 'bairro') echo 'selected'; ?>>Bairro</option>
                </select>
                <label for="valor_pesquisa">Valor:</label>
                <input type="text" id="valor_pesquisa" name="valor_pesquisa" placeholder="Digite o termo de pesquisa" value="<?php echo isset($_GET['valor_pesquisa']) ? htmlspecialchars($_GET['valor_pesquisa']) : ''; ?>">
                <button type="submit">Pesquisar</button>
            </div>
        </form>
    </div>

    <div class="results-container">
        <h2>Resultados da Pesquisa</h2>
        <?php if ($mensagem_pesquisa): ?>
            <p class="message"><?php echo htmlspecialchars($mensagem_pesquisa); ?></p>
        <?php endif; ?>

        <?php if (!empty($pedidos)): ?>
            <p>Total de pedidos encontrados: <?php echo count($pedidos); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>CÓDIGO</th>
                        <th>NOME</th>
                        <th>BAIRRO</th>
                        <th>QTDE</th>
                        <th>LANCHE</th>
                        <th>VALOR (unitário)</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["idPedido"]); ?></td>
                        <td><?php echo htmlspecialchars($row["nomeCliente"]); ?></td>
                        <td><?php echo htmlspecialchars($row["bairroCliente"]); ?></td>
                        <td><?php echo htmlspecialchars($row["qtde"]); ?></td>
                        <td><?php echo htmlspecialchars($row["nomeLanche"]); ?></td>
                        <td>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                        <td><a href='excluir_pedido.php?id=<?php echo htmlspecialchars($row["idPedido"]); ?>' class='delete-icon' onclick='return confirm("Tem certeza que deseja excluir este pedido?");'>X</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (!empty($_GET['valor_pesquisa'])): // Só mostra "Nenhum resultado" se um termo de pesquisa foi enviado ?>
            <p class="no-results">Nenhum pedido encontrado para a pesquisa.</p>
        <?php endif; ?>
    </div>

    <div class="navigation-buttons">
        <a href="form_lanche.php" class="btn-link">Voltar para o Cadastro</a>
        <a href="../index.php" class="btn-link">Voltar para a Página Inicial</a>
    </div>
</body>
</html>
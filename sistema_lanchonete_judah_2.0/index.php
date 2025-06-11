<?php
session_start(); // Inicia a sessão aqui para verificar o status
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Lanchonete</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/menu.css">
    <style>
        /* CSS para organizar os botões */
        .nav-buttons {
            display: flex;
            gap: 10px; /* Espaçamento entre os botões */
            justify-content: flex-end; /* Alinha à direita */
            align-items: center; /* Centraliza verticalmente */
            flex-wrap: wrap; /* Permite quebrar linha em telas menores */
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
            white-space: nowrap; /* Impede quebras de linha dentro do botão */
        }
        .btn-register {
            background-color: #228B22;
        }
        .btn-register:hover {
            background-color: #1a6b1a;
        }
        .btn-orders {
            background-color: #FFA500;
        }
        .btn-orders:hover {
            background-color: #cc8400;
        }
        .btn-login {
            background-color: #007bff;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .btn-signup {
            background-color: #6c757d;
        }
        .btn-signup:hover {
            background-color: #5a6268;
        }
        .btn-logout {
            background-color: #dc3545;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }
        .welcome-message {
            color: white;
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="page-wrapper">
        <div class="background-svg-container">
            <svg class="svg-yellow-wave" width="992" height="495" viewBox="0 0 992 495" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_g_1_25)">
                    <path
                        d="M177.799 420.114C159.805 486.029 51.0547 488.597 0 481.642V-1L980.075 0.0679594C980.843 0.045166 981.488 0.0452875 982 0.0700569L980.075 0.0679594C964.807 0.521379 901.047 10.0426 867.916 14.99L864.5 15.5C832.636 20.2559 786.708 30.6629 768.5 74C750.292 117.337 637 148 552.142 170.305C461.295 194.183 378.626 167.519 307.935 182.5C237.244 197.481 200.292 337.72 177.799 420.114Z"
                        fill="#FFA500" />
                </g>
                <defs>
                    <filter id="filter0_g_1_25" x="-10" y="-11" width="1002" height="505.278"
                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                        <feTurbulence type="fractalNoise" baseFrequency="0.1428571492433548 0.1428571492433548"
                            numOctaves="3" seed="8880" />
                        <feDisplacementMap in="shape" scale="20" xChannelSelector="R" yChannelSelector="G"
                            result="displacedImage" width="100%" height="100%" />
                        <feMerge result="effect1_texture_1_25">
                            <feMergeNode in="displacedImage" />
                        </feMerge>
                    </filter>
                </defs>
            </svg>

            <svg class="svg-green-wave" width="787" height="375" viewBox="0 0 787 375" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_g_1_24)">
                    <path
                        d="M157.752 309.993C141.786 368.556 45.2981 370.838 0 364.658V0.0633132L772.448 0.0633391C775.093 -0.0241797 777.32 -0.0179897 779 0.0633393L772.448 0.0633391C759.636 0.48735 737.03 3.11091 719.385 10.0457C692.776 20.5035 688.975 23.3556 672.82 61.8591C656.665 100.363 559.733 134.113 480.857 147.422C401.981 160.732 335.935 134.113 273.214 147.422C210.494 160.732 177.708 236.789 157.752 309.993Z"
                        fill="#228B22" />
                </g>
                <defs>
                    <filter id="filter0_g_1_24" x="-8" y="-8" width="795" height="383" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                        <feTurbulence type="fractalNoise" baseFrequency="0.125 0.125" numOctaves="3" seed="1782" />
                        <feDisplacementMap in="shape" scale="16" xChannelSelector="R" yChannelSelector="G"
                            result="displacedImage" width="100%" height="100%" />
                        <feMerge result="effect1_texture_1_24">
                            <feMergeNode in="displacedImage" />
                        </feMerge>
                    </filter>
                </defs>
            </svg>

            </div>

        <header class="header">
            <nav class="nav-buttons">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <span class="welcome-message">Olá, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="lanchonete/form_lanche.php" class="btn btn-register">Cadastrar pedidos</a>
                    <a href="lanchonete/pesquisar_pedidos.php" class="btn btn-orders">Ver pedidos</a>
                    <a href="logout.php" class="btn btn-logout">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-login">Login</a>
                    <a href="cadastro.php" class="btn btn-signup">Cadastre-se</a>
                <?php endif; ?>
            </nav>
        </header>

        <main class="main-content">
            <div class="text-block">
                <h1>SISTEMA LANCHONETE</h1>
                <p>Lorem ipsum é simplesmente um texto simulado da indústria de impressão e composição. Lorem Ipsum tem
                    sido o texto simulado padrão da indústria desde os anos 1500, quando um impressor desconhecido pegou
                    uma galeria de tipos e os embaralhou para fazer um livro de amostras de tipos. Ele sobreviveu não
                    apenas cinco séculos, mas também o salto para a composição eletrônica, permanecendo essencialmente
                    inalterado.</p>
            </div>
            <div class="image-block">
                <img src="assets/hamburguer_v2.png" alt="Hambúrguer Delicioso">
            </div>
        </main>

        <footer class="footer">
            <p>Desenvolvido por Judah Benjamin - 2025</p>
        </footer>
    </div>

</body>

</html>
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login/login.php");
    exit();
}

include 'verify/conexao.php';

if (!isset($_SESSION['nome_usuario'])) {
    try {
        $sql = "SELECT nome FROM usuarios WHERE id = :usuario_id AND cargo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $_SESSION['nome_usuario'] = $row['nome'];
        } else {
            $_SESSION['nome_usuario'] = "Nome do Usuário";
        }
    } catch (PDOException $e) {
        echo "Erro na consulta: " . $e->getMessage();
        exit();
    }
}

try {
    $sql = "SELECT COUNT(*) AS total_agendamentos FROM agendamentos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_agendamentos = $row["total_agendamentos"];
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

try {
    $sql = "SELECT COUNT(*) AS total_clientes FROM usuarios WHERE cargo = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_clientes = $row["total_clientes"];
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            font-family: 'Nunito', sans-serif;
        }

        .header {
            width: 100%;
            height: 3rem;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: #343a40;
            z-index: 100;
            transition: 0.3s;
        }

        .header_toggle {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .header_img {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            border-radius: 50%;
            overflow: hidden;
        }

        .header_img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            padding: 0.5rem 1rem 0;
            transition: 0.3s;
            z-index: 99;
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 0;
        }

        .nav_logo,
        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: 0.5rem 1.5rem;
        }

        .nav_logo {
            margin-bottom: 2rem;
        }

        .nav_logo-icon {
            font-size: 1.25rem;
            color: white;
        }

        .nav_logo-name {
            color: white;
            font-weight: 700;
        }

        .nav_link {
            position: relative;
            color: #c2c7d0;
            margin-bottom: 1.5rem;
            transition: 0.3s;
        }

        .nav_link:hover {
            color: white;
        }

        .nav_icon {
            font-size: 1.25rem;
        }

        .show {
            left: 0;
        }

        .active {
            color: white;
        }

        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 1rem;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .l-navbar {
                left: -250px;
                width: 80px;
            }

            .show {
                width: 250px;
            }

            .main-content {
                margin-left: 0;
                padding-top: 65px;
            }

            .main-content.shifted {
                margin-left: 250px;
            }

            .header_toggle {
                display: block;
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
            }

            .nav_link .text {
                display: none;
            }

            .nav_link .icon {
                margin-right: 0;
            }
        }
    </style>
</head>

<body>
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bi bi-list' id="header-toggle"></i> </div>
        <?php echo "<p class='fs-4 text-white mb-0'>" . $_SESSION['nome_usuario'] ."</p>"; ?>
    </header>

    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                    <i class='bi bi-layers nav_logo-icon'></i>
                    <span class="nav_logo-name">Logo</span>
                </a>
                <div class="nav_list">
                    <a href="#" class="nav_link active">
                        <i class='bi bi-house-door nav_icon'></i>
                        <span class="nav_name">Início</span>
                    </a>
                    <a href="agendamentos/index.php" class="nav_link">
                        <i class='bi bi-calendar-week nav_icon'></i>
                        <span class="nav_name">Agendamentos</span>
                    </a>
                    <a href="clienteLista/index.php" class="nav_link">
                        <i class='bi bi-people nav_icon'></i>
                        <span class="nav_name">Clientes</span>
                    </a>
                    <a href="todosPrecos/index.php" class="nav_link">
                        <i class='bi bi-currency-dollar nav_icon'></i>
                        <span class="nav_name">Preços</span>
                    </a>
                </div>
            </div>
            <a href="" class="nav_link" onclick="SairConta()">
                <i class='bi bi-box-arrow-right nav_icon'></i>
                <span class="nav_name">Sair</span>
            </a>
        </nav>
    </div>

    <div class="container-fluid main-content">
        <h2 class="mb-4 mt-5">Barba & Navalha</h2>
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-4 card-custom">
                <div class="card">
                    <div class="card-header">Agendamentos</div>
                    <div class="card-body">
                        <p class="card-text">Número de Agendamentos: <?php echo $total_agendamentos; ?></p>
                        <a href="agendamentos/index.php" class="btn btn-primary">Ver Agendamentos</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 card-custom">
                <div class="card">
                    <div class="card-header">Clientes</div>
                    <div class="card-body">
                        <p class="card-text">Total de Clientes: <?php echo $total_clientes; ?></p>
                        <a href="clienteLista/index.php" class="btn btn-primary">Ver Lista de Clientes</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 card-custom">
                <div class="card">
                    <div class="card-header">Preços</div>
                    <div class="card-body">
                        <p class="card-text">Lista de Preços: <?php echo $total_agendamentos; ?></p>
                        <a href="todosPrecos index.php" class="btn btn-primary">Ver Preços</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const toggle = document.getElementById('header-toggle');
            const nav = document.getElementById('nav-bar');
            const bodypd = document.querySelector('body');
            const headerpd = document.getElementById('header');

            if (toggle && nav && bodypd && headerpd) {
                toggle.addEventListener('click', () => {
                    nav.classList.toggle('show');
                    toggle.classList.toggle('bx-x');
                    bodypd.classList.toggle('body-pd');
                    headerpd.classList.toggle('body-pd');
                });
            }
        });

        const toggleSidebar = () => {
            const sidebar = document.querySelector('.l-navbar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('shifted');
        }

        const SairConta = () => {
            Swal.fire({
                title: 'Tem certeza que deseja sair?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, sair!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'verify/logout.php';
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>

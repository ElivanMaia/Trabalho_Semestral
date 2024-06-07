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
        $row = $stmt->fetch();

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
            transition: padding-left 0.3s;
        }

        .header {
            width: 100%;
            height: 80px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: #343a40;
            z-index: 100;
            transition: left 0.3s;
        }

        .header_toggle {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: 20px;
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            padding: 0.5rem 1rem 0;
            transition: left 0.3s;
            z-index: 99;
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 0;
        }

        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: 0.5rem 1.5rem;
            position: relative;
            color: #c2c7d0;
            margin-bottom: 1.5rem;
            transition: color 0.3s;
        }

        .nav_link:hover {
            color: white;
        }

        .nav_icon {
            font-size: 1.25rem;
        }

        .show {
            left: 0 !important;
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

        .body-pd {
            padding-left: 250px;
        }

        .body-pd .header {
            padding-left: 250px;
        }

        .body-no-pd {
            padding-left: 0;
        }

        .body-no-pd .header {
            padding-left: 0;
        }

        .nav-hidden {
            left: -250px !important;
        }

        @media (max-width: 768px) {
            .header_toggle {
                position: absolute;
                right: 1rem;
            }

            .body-pd {
                padding-left: 0;
            }

            .body-pd .header {
                padding-left: 0;
            }

            .l-navbar {
                left: -250px;
            }
        }
    </style>
</head>
<body class="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bi bi-list' id="header-toggle"></i> </div>
        <?php echo "<p class='fs-4 text-white mb-0'>" . $_SESSION['nome_usuario'] ."</p>"; ?>
    </header>

    <div class="l-navbar show" id="nav-bar">
        <nav class="nav">
            <div style="margin-top: 30px;">
                <img src="images/logoReal.png" alt="Logo da Barbearia" class="img-fluid mb-4" style="width: auto; max-height: 90px;">
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
            <a href="javascript:void(0);" class="nav_link" onclick="SairConta(event)">
                <i class='bi bi-box-arrow-right nav_icon'></i>
                <span class="nav_name">Sair</span>
            </a>
        </nav>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const toggle = document.getElementById('header-toggle');
            const nav = document.getElementById('nav-bar');
            const bodypd = document.querySelector('body');

            if (toggle && nav && bodypd) {
                toggle.addEventListener('click', () => {
                    nav.classList.toggle('nav-hidden');
                    bodypd.classList.toggle('body-no-pd');
                });
            }
        });

        function SairConta(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Você realmente deseja sair da conta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'verify/logout.php';
                } else {
                    console.log('Operação de saída da conta cancelada pelo usuário.');
                }
            });
        }

        if (window.innerWidth <= 768) {
                    nav.classList.remove('show');
                    bodypd.classList.add('body-no-pd');
                }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

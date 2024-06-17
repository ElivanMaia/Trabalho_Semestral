<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include 'verify/conexao.php';

// Verifica e atribui nome do usuário à sessão se ainda não estiver definido
if (!isset($_SESSION['nome_usuario'])) {
    try {
        $sql = "SELECT nome FROM usuarios WHERE id = :usuario_id AND cargo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
        $stmt->execute();
        $row = $stmt->fetch();

        $_SESSION['nome_usuario'] = $row ? $row['nome'] : "Nome do Usuário";
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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">

    <style>
        body {
            height: 100vh;
            font-family: 'Nunito', sans-serif;
            transition: padding-left 0.3s;
            margin: 0;
            overflow-x: hidden;
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
            background-color: #808080;
            z-index: 101;
            transition: left 0.3s;
            background-color: #fff;
        }

        .header_toggle {
            font-size: 2rem;
            cursor: pointer;
            margin-left: 20px;
            margin-top: 10px;
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
            z-index: 100; /* Ajustado para 99 para ficar abaixo do modal */
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

        .l-navbar-show {
            left: 0 !important;
            z-index: 101;
        }

        .body-pd {
            padding-left: 250px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
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

        .nav_name {
            font-size: 15px;
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
                z-index: 101;
            }
        }
    </style>
</head>

<body class="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bi bi-list' id="header-toggle"></i> </div>
    </header>

    <div class="l-navbar l-navbar-show" id="nav-bar">
        <nav class="nav">
            <div style="margin-top: 30px;">
                <img src="../images/logoReal.png" alt="Logo da Barbearia" class="img-fluid mb-4" style="width: auto; max-height: 110px;">
                <div class="nav_list">
                    <a href="../inicioAdmin/inicioAdm.php" class="nav_link active">
                        <i class='bi bi-house-door nav_icon'></i>
                        <span class="nav_name">Início</span>
                    </a>
                    <a href="../agendamentos/index.php" class="nav_link">
                        <i class='bi bi-calendar-week nav_icon'></i>
                        <span class="nav_name">Agendamentos</span>
                    </a>
                    <a href="../clienteLista/index.php" class="nav_link">
                        <i class='bi bi-people nav_icon'></i>
                        <span class="nav_name">Clientes</span>
                    </a>
                </div>
                <br>
                <hr style="border-color: white;">
                <br>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle text-white" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../images/perfil.png" alt="Imagem de Perfil" class="img-fluid me-2" style="width: 50px;">
                            <?php echo $_SESSION['nome_usuario']; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" onclick="SairConta(event)">Sair</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const toggle = document.getElementById('header-toggle');
            const nav = document.getElementById('nav-bar');
            const body = document.querySelector('body');

            if (toggle && nav && body) {
                toggle.addEventListener('click', () => {
                    nav.classList.toggle('nav-hidden');
                    body.classList.toggle('body-no-pd');
                });
            }

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
        });
    </script>

</body>

</html>


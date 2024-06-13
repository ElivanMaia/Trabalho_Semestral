<?php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login/login.php");
    exit();
}

include 'verify/conexao.php';

if (!isset($_SESSION['nome_usuario_cliente'])) {
    try {
        $sql = "SELECT nome FROM usuarios WHERE id = :usuario_id AND cargo = 0";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row) {
            $_SESSION['nome_usuario_cliente'] = $row['nome'];
        } else {
            $_SESSION['nome_usuario_cliente'] = "Nome do Usuário";
        }
    } catch (PDOException $e) {
        echo "Erro na consulta: " . $e->getMessage();
        exit();
    }
}

if (isset($_GET['senha_atualizada']) && $_GET['senha_atualizada'] == 1) {
    $_SESSION['senha_atualizada'] = true;
}

if (isset($_GET['erro_senha_atual']) && $_GET['erro_senha_atual'] == 1) {
    $_SESSION['erro_senha_atual'] = true;
}

if (isset($_GET['senhas_nao_coincidem']) && $_GET['senhas_nao_coincidem'] == 1) {
    $_SESSION['senhas_nao_coincidem'] = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <style>
        .navbar-nav .nav-item {
            margin-left: 17px;
        }

        .navbar-custom {
            background-color: #343a40;
            min-height: 60px;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-nav .nav-link {
            color: #ffffff;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top navbar-custom">
            <div class="container-fluid">
                <img src="images/logoReal.png" alt="Logo da Barbearia" style="width: auto; max-height: 90px;">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#inicio"><i class="bi bi-info-circle-fill me-1"></i>Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="#equipe"><i class="bi bi-people-fill me-1"></i>Equipe</a></li>
                        <li class="nav-item"><a class="nav-link" href="#servicos"><i class="bi bi-tools me-1"></i>Serviços</a></li>
                        <li class="nav-item"><a class="nav-link" href="#horarios"><i class="bi bi-calendar me-1"></i>Horários</a></li>
                        <li class="nav-item"><a class="nav-link" href="#agendar"><i class="bi bi-calendar3 me-1"></i>Agendamento</a></li>
                        <li class="nav-item dropdown">
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo $_SESSION['nome_usuario_cliente']; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="bi bi-arrow-clockwise me-1"></i>Redefinir Senha</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="confirmarExclusaoConta()"><i class="bi bi-trash me-1"></i>Excluir Conta</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" onclick="confirmarSairConta()"><i class="bi bi-box-arrow-right me-1"></i>Sair</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Redefinir Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['senha_atualizada']) && $_SESSION['senha_atualizada']) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Senha atualizada com sucesso!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['senha_atualizada']); ?>
                    <?php } ?>

                    <?php if (isset($_SESSION['erro_senha_atual']) && $_SESSION['erro_senha_atual']) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Senha atual incorreta. Por favor, tente novamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['erro_senha_atual']); ?>
                    <?php } ?>

                    <?php if (isset($_SESSION['senhas_nao_coincidem']) && $_SESSION['senhas_nao_coincidem']) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            As novas senhas não coincidem. Por favor, verifique e tente novamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['senhas_nao_coincidem']); ?>
                    <?php } ?>

                    <form id="formRedefinirSenha" method="POST" action="verify/redefinirSenha.php">
                        <div class="mb-3">
                            <label for="senhaAtual" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="senhaAtual" name="senhaAtual" placeholder="Digite sua senha atual" required>
                        </div>
                        <div class="mb-3">
                            <label for="novaSenha" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="novaSenha" name="novaSenha" placeholder="Digite sua nova senha" required>
                                <button class="btn btn-outline-secondary" type="button" id="mostrarNovaSenha" onclick="mostrarSenha('novaSenha')">Mostrar Senha</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmarSenha" class="form-label">Confirmar Nova Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" placeholder="Confirme sua nova senha" required>
                                <button class="btn btn-outline-secondary" type="button" id="mostrarConfirmarSenha" onclick="mostrarSenha('confirmarSenha')">Mostrar Senha</button>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/parsleyjs/dist/parsley.min.js"></script>
    <script src="node_modules/parsleyjs/dist/i18n/pt-br.js"></script>
    <link rel="stylesheet" href="node_modules/parsleyjs/src/parsley.css">

    <script>
        function mostrarSenha(idCampo) {
            const campoSenha = document.getElementById(idCampo);
            const tipo = campoSenha.getAttribute('type');

            if (tipo === 'password') {
                campoSenha.setAttribute('type', 'text');
            } else {
                campoSenha.setAttribute('type', 'password');
            }
        }

        function confirmarExclusaoConta() {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá reverter isso!",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#3085d6',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sim, excluir!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Excluído!',
                        'Sua conta foi excluída com sucesso.',
                        'success'
                    ).then(() => {
                        window.location.href = 'verify/excluirConta.php';
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado!',
                        'A exclusão da conta foi cancelada com sucesso.',
                        'info'
                    );
                }
            });
        }

        function confirmarSairConta() {
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

        $(document).ready(function() {
            if ($('.alert').length > 0) {
                var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                myModal.show();

                if (window.location.search.indexOf('senha_atualizada') !== -1 ||
                    window.location.search.indexOf('erro_senha_atual') !== -1 ||
                    window.location.search.indexOf('senhas_nao_coincidem') !== -1) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
        });
    </script>
</body>

</html>
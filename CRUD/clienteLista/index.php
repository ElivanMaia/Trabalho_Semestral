<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.html");
    exit();
}

include '../verify/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if(isset($_POST['id_cliente'])) {
            $sql = "UPDATE usuarios SET 
                        nome = :nome, 
                        email = :email, 
                        senha = :senha 
                    WHERE id = :id_cliente";
            
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(':nome', $_POST['nome_usuario']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':senha', $_POST['senha']);
            $stmt->bindParam(':id_cliente', $_POST['id_cliente']);
            
            $stmt->execute();

            header("Location: {$_SERVER['PHP_SELF']}?atualizacao=sucesso");
            exit();
        } else {
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            
            $stmt = $conn->prepare($sql);
            
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            
            $stmt->execute();

            header("Location: {$_SERVER['PHP_SELF']}?insercao=sucesso");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE cargo = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

try {
    $sql = "SELECT nome FROM usuarios WHERE id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nomeUsuario = $row['nome'];
    } else {
        $nomeUsuario = "Nome do Usuário";
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

try {
    $sql = "SELECT COUNT(*) AS total_clientes FROM usuarios WHERE cargo = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
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
    <title>Admin Dashboard - Listar Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <link rel="stylesheet" href="node_modules/parsleyjs/src/parsley.css">
    <style>
        .navbar-custom {
            background-color: #343a40;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-nav .nav-link {
            color: #fff;
            font-size: 22px;
            margin-right: 20px;
        }

        .navbar-custom .navbar-brand:hover,
        .navbar-custom .navbar-nav .nav-link:hover {
            color: #ff0;
        }

        .main-content {
            padding-top: 150px;
            padding-bottom: 20px;
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }

        .divider {
            border-top: 5px solid #000;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top navbar-custom">
        <div class="container-fluid">
            <img src="../images/logoReal.png" alt="Logo da Barbearia" style="width: auto; max-height: 90px;">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../inicioAdm.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../agendamentos/index.php">Agendamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../clienteLista/index.php">Listar Clientes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" onclick="confirmarSairConta()"> <i class="bi bi-box-arrow-right me-1"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-content position-relative">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Lista de Clientes</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadastrarModal">Cadastrar</button>
        </div>
        <div class="card-body">
        <h5 class="card-text">Número de Clientes: <?php echo $total_clientes; ?></h5>
    </div>
        <br>
        <hr class="divider">
        <br>
        <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Senha</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente) : ?>
                    <tr>
                        <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo $cliente['nome']; ?></td>
                        <td><?php echo $cliente['email']; ?></td>
                        <td><?php echo $cliente['senha']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning me-2" onclick="formEditarClientes(this)" data-id="<?php echo $cliente['id']; ?>" 
                            data-nome="<?php echo $cliente['nome']; ?>" 
                            data-email="<?php echo $cliente['email']; ?>" 
                            data-senha="<?php echo $cliente['senha']; ?>"
                            >Editar</button>

                            <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $cliente['id']; ?>)">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    </div>




    <div class="modal fade" id="editarModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel">Editar Clientes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarClientes" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="modal-body">
                        <input type="hidden" id="id_cliente" name="id_cliente">
                        <div class="mb-3">
                            <label for="nome_usuario" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome_usuario" name="nome_usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Endereço de e-mail</label>
                            <input type="email" class="form-control" id="email" name="email"></input>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha">
                        </div>
                        <div class="mb-3 form-check pt-3">
                            <input type="checkbox" class="form-check-input" id="mos" onclick="mostrarsenha()">
                            <label class="form-check-label" for="mostrarSenhaCheckbox">Mostrar Senha</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cadastrarModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="cadastrarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cadastrarModalLabel">Cadastrar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCadastrarUsuario" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senhaCadastro" name="senha">
                    </div>
                    <div class="mb-3 form-check pt-3">
                        <input type="checkbox" class="form-check-input" id="mostrarSenhaCadastro" onclick="mostrar('senhaCadastro')">
                        <label class="form-check-label" for="mostrarSenhaCheckbox">Mostrar Senha</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="node_modules/parsleyjs/dist/parsley.min.js"></script>
    <script src="node_modules/parsleyjs/dist/i18n/pt-br.js"></script>

    <script>
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
                    window.location.href = '../verify/logout.php';
                } else {
                    console.log('Operação de saída da conta cancelada pelo usuário.');
                }
            });
        }

        function confirmarExclusao(id) {
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
                        'Deletado!',
                        'Seu item foi deletado com sucesso.',
                        'success'
                    ).then(() => {
                        window.location.href = `../verify/deletarCliente.php?id=${id}`;
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado!',
                        'A exclusão foi cancelada com sucesso.',
                        'info'
                    );
                }
            });
        }

        function formEditarClientes(button) {
            const id = button.getAttribute('data-id');
            const nome = button.getAttribute('data-nome');
            const email = button.getAttribute('data-email');
            const senha = button.getAttribute('data-senha');

            document.getElementById('id_cliente').value = id;
            document.getElementById('nome_usuario').value = nome;
            document.getElementById('email').value = email;
            document.getElementById('senha').value = senha;

            var editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
            editarModal.show();
        }


        function mostrarsenha() {
            var senha = document.getElementById("senha");
            if (senha.type === "password") {
                senha.type = "text";
            } else {
                senha.type = "password";
            }
        }

        function mostrar(idCampoSenha) {
        var senha = document.getElementById(idCampoSenha);
        var checkbox = document.getElementById("mostrarSenhaCadastro");
        
        if (senha.type === "password") {
            senha.type = "text";
            checkbox.checked = true;
        } else {
            senha.type = "password";
            checkbox.checked = false;
        }
    }

        window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sucessoAtualizacao = urlParams.get('atualizacao');
    if (sucessoAtualizacao === 'sucesso') {
        Swal.fire({
            title: 'Sucesso!',
            text: 'A atualização foi concluída com sucesso!',
            icon: 'success'
        }).then(() => {
            window.history.replaceState(null, null, window.location.pathname);
        });
    }
};
    </script>
</body>

</html>
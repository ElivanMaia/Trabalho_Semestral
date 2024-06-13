<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.html");
    exit();
}

include '../verify/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['id_cliente'])) {
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

    <?php
        error_reporting(0);
        ini_set('display_errors', 0);
        require('../sidebar.php');
    ?>

        <div class="container">
    <div class="main-content position-relative">
        <div class=" justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-center">Lista de Clientes</h2>
        </div>
        <div class="card-body">
            <h5 class="card-text text-center">Número de Clientes: <?php echo $total_clientes; ?></h5>
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
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente) : ?>
                            <tr>
                                <td><?php echo $cliente['id']; ?></td>
                                <td><?php echo $cliente['nome']; ?></td>
                                <td><?php echo $cliente['email']; ?></td>
                                <td>

                                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $cliente['id']; ?>)">Excluir</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
    </script>
</body>

</html>
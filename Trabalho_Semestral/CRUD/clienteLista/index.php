<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include '../verify/conexao.php';

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <link rel="stylesheet" href="node_modules/parsleyjs/src/parsley.css">
    <style>
        body {
            background-color: #f0f0f0;
            color: #333;
        }

        .container-box {
            background-color: #f8f9fa;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            margin-top: 100px;
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
            font-weight: bold;
        }

        .table td {
            vertical-align: middle;
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

    <div class="container-fluid py-4">
        <div class="container-box">
            <h2 class="mb-4 text-center">Lista de Clientes</h2>
            <div class="card-body">
                <h5 class="card-text text-center">Número de Clientes: <?php echo $total_clientes; ?></h5>
            </div>
            <br>
            <hr class="divider">
            <br>

            <div class="table-responsive scroll-container">
                <?php if (empty($clientes)) : ?>
                    <div class="alert alert-warning text-center" role="alert">
                        <span style="font-weight: bold;">NENHUM CLIENTE CADASTRADO</span>
                    </div>
                <?php else : ?>
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
                                        <button type="button" class="btn btn-danger btn-excluir" onclick="confirmarExclusao(<?php echo $cliente['id']; ?>)">Excluir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>


            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            </script>
</body>

</html>

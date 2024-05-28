<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include '../verify/conexao.php';

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

    $sql = "SELECT 
                a.id AS id_agendamento,
                u.nome AS nome_usuario,
                c.nome AS nome_corte,
                c.preco AS preco_corte,
                a.telefone_cliente,
                a.horario_agendamento,
                a.observacoes,
                a.referencia
            FROM 
                agendamentos a
            JOIN 
                usuarios u ON a.id_usuario = u.id
            JOIN 
                cortes c ON a.id_corte = c.id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE agendamentos SET 
                    nome_usuario = :nome, 
                    telefone_cliente = :telefone, 
                    horario_agendamento = :horario, 
                    id_corte = :servico, 
                    observacoes = :observacoes, 
                    referencia = :referencia 
                WHERE id = :id_agendamento";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':nome', $_POST['nome_usuario']);
        $stmt->bindParam(':telefone', $_POST['telefone_cliente']);
        $stmt->bindParam(':horario', $_POST['horario_agendamento']);
        $stmt->bindParam(':servico', $_POST['id_corte']);
        $stmt->bindParam(':observacoes', $_POST['observacoes']);
        $stmt->bindParam(':referencia', $_POST['referencia']);
        $stmt->bindParam(':id_agendamento', $_POST['id_agendamento']);
        
        $stmt->execute();

        header("Location: ".$_SERVER['PHP_SELF']."?atualizacao=sucesso");
        exit();
    } catch(PDOException $e) {
        echo "Erro na atualização: " . $e->getMessage();
    }
}

if (isset($_GET['atualizacao']) && $_GET['atualizacao'] === 'sucesso') {
    echo '<div class="alert alert-success" role="alert">
            A atualização foi concluída com sucesso!
          </div>';
}

$conn = null;
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
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
            font-size: 18px;
            margin-right: 20px;
        }

        .navbar-custom .navbar-brand:hover,
        .navbar-custom .navbar-nav .nav-link:hover {
            color: #ff0;
        }

        .main-content {
            padding-top: 105px;
            padding-bottom: 20px;
        }

        .table-responsive {
            margin-bottom: 20px;
        }

        .table th {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top navbar-custom">
        <div class="container-fluid">
        <a class="navbar-brand" href="../inicio_funcio.php">Voltar para tela inicial</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../inicio_funcio.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../agendamentos/index.php">Agendamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../todosPrecos/index.php">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../clienteLista/index.php">Listar Clientes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" onclick="SairConta()"><i class="bi bi-box-arrow-right me-1"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-content">
        <h2 class="mb-4">Agendamentos</h2>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Horário</th>
                        <th>Serviço</th>
                        <th>Preço</th>
                        <th>Observações</th>
                        <th>Referência</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agendamento['id_agendamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['nome_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['telefone_cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['horario_agendamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['nome_corte'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>R$ <?php echo number_format($agendamento['preco_corte'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($agendamento['referencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <button type="button" class="btn btn-warning me-2" onclick="editarAgendamento(<?php echo $agendamento['id_agendamento']; ?>)">Editar</button>
                                <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $agendamento['id_agendamento']; ?>)">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function SairConta() {
            if (window.confirm("Tem certeza de que deseja sair da conta?")) {
                window.location.href = '../verify/logout.php';
            } else {
                console.log("Operação de saída da conta cancelada pelo usuário.");
            }
        }

        function editarAgendamento(id) {
            window.location.href = '../verify/editarAgen.php?id=' + id;
        }

        function confirmarExclusao(id) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você realmente deseja excluir este agendamento?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../verify/deletarAgen.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>

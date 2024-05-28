<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.html");
    exit();
}

include '../verify/conexao.php';

try {
    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE cargo = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

$conn = null;
?>
<?php
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

    } catch(PDOException $e) {
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
            padding-top: 65px;
            padding-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
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
                        <a class="nav-link" href="clienteLista/index.php">Listar Clientes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <?php echo $nomeUsuario; ?>
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
        <h2 class="mb-4 mt-5">Lista de Clientes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Senha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['id']; ?></td>
                        <td><?php echo $cliente['nome']; ?></td>
                        <td><?php echo $cliente['email']; ?></td>
                        <td><?php echo $cliente['senha']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function SairConta() {
            if (window.confirm("Tem certeza de que deseja sair da conta?")) {
                window.location.href = '../verify/logout.php';
            } else {
                console.log("Operação de saída da conta cancelada pelo usuário.");
            }
        }
    </script>
</body>

</html>

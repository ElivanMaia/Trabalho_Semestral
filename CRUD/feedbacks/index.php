<?php
//     SELECT 
//     a.id AS id_agendamento,
//     u.nome AS nome_usuario,
//     c.nome AS nome_corte,
//     c.preco AS preco_corte,
//     a.telefone_cliente,
//     a.horario_agendamento,
//     a.observacoes
// FROM 
//     agendamentos a
// JOIN 
//     usuarios u ON a.id_usuario = u.id
// JOIN 
//     cortes c ON a.id_corte = c.id;
?>

<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.html");
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
    <title>Admin Dashboard</title>
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
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
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
                        <a class="nav-link" href="todosPrecos/index.php">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../clienteLista/index.php">Listar Clientes</a>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function SairConta() {
            if (window.confirm("Tem certeza de que deseja sair da conta?")) {
                window.location.href = 'verify/logout.php';
            } else {
                console.log("Operação de saída da conta cancelada pelo usuário.");
            }
        }
    </script>
</body>

</html>

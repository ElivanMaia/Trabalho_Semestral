<?php
include 'verify/conexao.php';

try {
    $sql = "SELECT COUNT(*) AS total_agendamentos FROM agendamentos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch();
    $total_agendamentos = $row["total_agendamentos"];
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

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #a0a0a0;
            overflow: hidden;
        }

        .main-content {
            margin-top: 90px;
            padding: 0 15px;
            overflow-y: auto;
            height: calc(100vh - 90px);
        }

        .card-custom {
            margin-bottom: 1.5rem;
            max-width: 300px;
            margin-right: auto;
            margin-left: auto;
        }

        .table-container {
            margin-top: 30px;
            overflow-x: auto;
        }

        .table thead th {
            background-color: #343a40;
            color: #fff;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <?php
    require('sidebar.php');
    ?>

    <div class="container-fluid main-content">
        <h2 class="mb-4 text-center">Barba & Navalha</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <div class="col card-custom">
                <div class="card">
                    <div class="card-header">Agendamentos</div>
                    <div class="card-body">
                        <p class="card-text">Número de Agendamentos: <?php echo $total_agendamentos; ?></p>
                        <a href="agendamentos/index.php" class="btn btn-primary">Ver Agendamentos</a>
                    </div>
                </div>
            </div>

            <div class="col card-custom">
                <div class="card">
                    <div class="card-header">Clientes</div>
                    <div class="card-body">
                        <p class="card-text">Total de Clientes: <?php echo $total_clientes; ?></p>
                        <a href="clienteLista/index.php" class="btn btn-primary">Ver Lista de Clientes</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <?php
                try {
                    $sqlMaisPedidos = "SELECT 
                                        c.nome AS nome_servico, 
                                        COUNT(*) AS quantidade_agendamentos,
                                        SUM(c.preco) AS preco_total
                                    FROM 
                                        agendamentos a 
                                    JOIN 
                                        cortes c ON a.id_corte = c.id 
                                    GROUP BY 
                                        a.id_corte 
                                    ORDER BY 
                                        COUNT(*) DESC 
                                    LIMIT 3";

                    $stmtMaisPedidos = $conn->prepare($sqlMaisPedidos);
                    $stmtMaisPedidos->execute();
                    $resultadosMaisPedidos = $stmtMaisPedidos->fetchAll();

                    echo "<br> . <br>";
                    echo "<div class='container'>";
                    echo "   <div class='row'>";
                    echo "       <div class='col'>";
                    echo "           <h2 class='text-center fs-5 fw-bold'>Relatório: Top 3 Serviços Mais Agendados</h2>";
                    echo "       </div>";
                    echo "   </div>";
                    echo "</div>";
                    echo "<div class='table-container'>";
                    echo "<table class='table table-bordered table-responsive'>";
                    echo "<thead style='color: #808080;'><tr><th>Serviço</th><th>Quantidade de Agendamentos</th><th>Preço Total</th></tr></thead>";

                    echo "<tbody>";
                    foreach ($resultadosMaisPedidos as $resultado) {
                        echo "<tr>";
                        echo "<td>" . $resultado['nome_servico'] . "</td>";
                        echo "<td>" . $resultado['quantidade_agendamentos'] . "</td>";
                        echo "<td>R$ " . number_format($resultado['preco_total'], 2, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } catch (PDOException $e) {
                    echo "Erro na consulta: " . $e->getMessage();
                    exit();
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
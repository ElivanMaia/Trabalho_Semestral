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
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nomeUsuario = $row ? $row['nome'] : "Nome do Usuário";

    $sql = "SELECT 
                a.id AS id_agendamento,
                u.nome AS nome_usuario,
                c.id AS id_corte,
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
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_agendamento'])) {
        try {
            $sql = "UPDATE agendamentos SET 
                        horario_agendamento = :horario, 
                        id_corte = :servico, 
                        observacoes = :observacoes, 
                        referencia = :referencia 
                    WHERE id = :id_agendamento";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':horario', $_POST['horario_agendamento'], PDO::PARAM_STR);
            $stmt->bindParam(':servico', $_POST['id_corte'], PDO::PARAM_INT);
            $stmt->bindParam(':observacoes', $_POST['observacoes'], PDO::PARAM_STR);
            $stmt->bindParam(':referencia', $_POST['referencia'], PDO::PARAM_STR);
            $stmt->bindParam(':id_agendamento', $_POST['id_agendamento'], PDO::PARAM_INT);
            $stmt->execute();
            header("Location: ../agendamentos/index.php?atualizacao=sucesso");
            exit();
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }
}

try {
    $sql = "SELECT COUNT(*) AS total_agendamentos FROM agendamentos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_agendamentos = $row["total_agendamentos"];
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
    <title>Admin Dashboard - Agendamentos</title>
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
        }

        .scroll-container {
            max-height: 500px;
            overflow: auto;
        }

        .divider {
            border-top: 5px solid #000;
        }
    </style>
</head>

<body>

    <?php
    require('../sidebar.php');
    ?>

    <div class="container-fluid py-4">
        <div class="container-box">
            <h2 class="mb-4 text-center">Agendamentos</h2>
            <div class="card-body">
                <h5 class="card-text text-center">Número de Agendamentos: <?php echo $total_agendamentos; ?></h5>
            </div>
            <br>
            <hr class="divider">
            <br>

            <div class="table-responsive scroll-container">
                <?php if (empty($agendamentos)) : ?>
                    <div class="alert alert-warning text-center" role="alert">
                        <span style="font-weight: bold;">NENHUM AGENDAMENTO CADASTRADO</span>
                    </div>
                <?php else : ?>
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
                            <?php foreach ($agendamentos as $agendamento) : ?>
                                <tr>
                                    <td><?php echo $agendamento['id_agendamento']; ?></td>
                                    <td><?php echo $agendamento['nome_usuario']; ?></td>
                                    <td><?php echo $agendamento['telefone_cliente']; ?></td>
                                    <td><?php echo $agendamento['horario_agendamento']; ?></td>
                                    <td><?php echo $agendamento['nome_corte']; ?></td>
                                    <td>R$ <?php echo number_format($agendamento['preco_corte'], 2, ',', '.'); ?></td>
                                    <td><?php echo $agendamento['observacoes']; ?></td>
                                    <td><?php echo $agendamento['referencia']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editarModal" onclick="editarAgendamento(this)" data-id="<?php echo $agendamento['id_agendamento']; ?>" data-horario="<?php echo $agendamento['horario_agendamento']; ?>" data-corte="<?php echo $agendamento['id_corte']; ?>" data-observacoes="<?php echo $agendamento['observacoes']; ?>" data-referencia="<?php echo $agendamento['referencia']; ?>">Editar</button>
                                        <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $agendamento['id_agendamento']; ?>)">Excluir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarModalLabel">Editar Agendamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formEditarAgendamento" method="POST" action="">
                            <div class="modal-body">
                                <input type="hidden" id="id_agendamento" name="id_agendamento">
                                <div class="mb-3">
                                    <label for="horario_agendamento" class="form-label">Horário do Agendamento</label>
                                    <input type="datetime-local" class="form-control" id="horario_agendamento" name="horario_agendamento" required>
                                </div>
                                <div class="mb-3">
                                    <label for="id_corte" class="form-label">Serviço</label>
                                    <select class="form-select" id="id_corte" name="id_corte" required>
                                        <option value="" disabled selected hidden>Selecione o serviço desejado</option>
                                        <?php
                                        $sql_servicos = "SELECT id, nome FROM cortes";
                                        $resultado_servicos = $conn->prepare($sql_servicos);
                                        $resultado_servicos->execute();
                                        $cortes = $resultado_servicos->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($cortes as $corte) {
                                            echo '<option value="' . $corte['id'] . '">' . $corte['nome'] . '</option>';
                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="referencia" class="form-label">Referência</label>
                                    <input type="text" class="form-control" id="referencia" name="referencia">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                function editarAgendamento(button) {
                    var id_agendamento = button.getAttribute('data-id');
                    var horario_agendamento = button.getAttribute('data-horario');
                    var id_corte = button.getAttribute('data-corte');
                    var observacoes = button.getAttribute('data-observacoes');
                    var referencia = button.getAttribute('data-referencia');

                    document.getElementById('id_agendamento').value = id_agendamento;
                    document.getElementById('horario_agendamento').value = horario_agendamento;
                    document.getElementById('id_corte').value = id_corte;
                    document.getElementById('observacoes').value = observacoes;
                    document.getElementById('referencia').value = referencia;

                    $('#editarModal').modal('show');
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
                                window.location.href = `../verify/deletarAgen.php?id=${id}`;
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

                function confirmarSairConta() {
                    Swal.fire({
                        title: 'Tem certeza?',
                        text: 'Você realmente deseja sair da conta?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim, sair',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../verify/logout.php';
                        } else {
                            console.log('Operação de saída da conta cancelada pelo usuário.');
                        }
                    });
                }

                window.onload = function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const atualizacao = urlParams.get('atualizacao');

                    if (atualizacao === 'sucesso') {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'A atualização foi concluída com sucesso!',
                            icon: 'success'
                        }).then(() => {
                            window.history.replaceState(null, null, window.location.pathname);
                        });
                    } else if (atualizacao === 'falha') {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Ocorreu um erro durante a atualização.',
                            icon: 'error'
                        }).then(() => {
                            window.history.replaceState(null, null, window.location.pathname);
                        });
                    }
                }
            </script>

</body>

</html>
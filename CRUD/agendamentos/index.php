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
    $row = $stmt->fetch();

    if ($row) {
        $nomeUsuario = $row['nome'];
    } else {
        $nomeUsuario = "Nome do Usuário";
    }

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
    $agendamentos = $stmt->fetchAll();
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

            $stmt->bindParam(':horario', $_POST['horario_agendamento']);
            $stmt->bindParam(':servico', $_POST['id_corte']);
            $stmt->bindParam(':observacoes', $_POST['observacoes']);
            $stmt->bindParam(':referencia', $_POST['referencia']);
            $stmt->bindParam(':id_agendamento', $_POST['id_agendamento']);

            $stmt->execute();

            header("Location: ../agendamentos/index.php?atualizacao=sucesso");
            exit();
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    } else {
        try {
            $telefone_cliente = $_POST['telefone_cliente'];
            $horario_agendamento = $_POST['horario_agendamento'];
            $id_corte = $_POST['id_corte'];
            $observacoes = $_POST['observacoes'];
            $referencia = $_POST['referencia'];

            $sql = "INSERT INTO agendamentos (id_usuario, telefone_cliente, horario_agendamento, id_corte, observacoes, referencia) 
        VALUES (:id_usuario, :telefone_cliente, :horario_agendamento, :id_corte, :observacoes, :referencia)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_usuario', $_SESSION['usuario_id']);
            $stmt->bindParam(':telefone_cliente', $telefone_cliente);
            $stmt->bindParam(':horario_agendamento', $horario_agendamento);
            $stmt->bindParam(':id_corte', $id_corte);
            $stmt->bindParam(':observacoes', $observacoes);
            $stmt->bindParam(':referencia', $referencia);

            $stmt->execute();

            header("Location: ../agendamentos/index.php?agendamento_sucesso=true");
            exit();
        } catch (PDOException $e) {
            echo "Erro ao salvar o agendamento: " . $e->getMessage();
            exit();
        }
    }
}

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
            <h2 class="mb-0">Agendamentos</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agendarModal">Agendar</button>
        </div>
        <div class="card-body">
            <h5 class="card-text">Número de Agendamentos: <?php echo $total_agendamentos; ?></h5>
        </div>
        <br>
        <hr class="divider">
        <br>


        <div class="container-fluid">
            <div class="table-responsive scroll-container">
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
                                <td><?php echo htmlspecialchars($agendamento['id_agendamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['nome_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['telefone_cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['horario_agendamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['nome_corte'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>R$ <?php echo number_format($agendamento['preco_corte'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['referencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning me-2" onclick="editarAgendamento(this)" data-id="<?php echo $agendamento['id_agendamento']; ?>" data-nome="<?php echo htmlspecialchars($agendamento['nome_usuario'], ENT_QUOTES, 'UTF-8'); ?>" data-telefone="<?php echo htmlspecialchars($agendamento['telefone_cliente'], ENT_QUOTES, 'UTF-8'); ?>" data-horario="<?php echo htmlspecialchars($agendamento['horario_agendamento'], ENT_QUOTES, 'UTF-8'); ?>" data-corte="<?php echo htmlspecialchars($agendamento['id_corte'], ENT_QUOTES, 'UTF-8'); ?>" data-observacoes="<?php echo htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8'); ?>" data-referencia="<?php echo htmlspecialchars($agendamento['referencia'], ENT_QUOTES, 'UTF-8'); ?>">Editar</button>

                                    <button type="button" class="btn btn-danger" onclick="confirmarExclusao(<?php echo $agendamento['id_agendamento']; ?>)">Excluir</button>
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
                                $cortes = $resultado_servicos->fetchAll();
                                foreach ($cortes as $corte) {
                                    echo '<option value="' . htmlspecialchars($corte['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($corte['nome'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                ?>
                             </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="referencia" class="form-label">Referência</label>
                            <input type="text" class="form-control" id="referencia" name="referencia">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-warning">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="agendarModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="agendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agendarModalLabel">Agendar Novo Horário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgendar" method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome_cliente_agendar" class="form-label">Nome do Cliente</label>
                            <input type="text" class="form-control" id="nome_cliente_agendar" name="nome_cliente_agendar" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone_agendar" class="form-label">Telefone do Cliente</label>
                            <input type="tel" class="form-control" id="telefone_agendar" name="telefone_cliente" onkeypress="$(this).mask('(00) 0000-0000')" placeholder="(00) 0000-0000" required>
                        </div>
                        <div class="mb-3">
                            <label for="horario_agendar" class="form-label">Horário do Agendamento</label>
                            <input type="datetime-local" class="form-control" id="horario_agendar" name="horario_agendamento" required>
                        </div>
                        <div class="mb-3">
                            <label for="corte_agendar" class="form-label">Serviço</label>
                            <select class="form-select" id="corte_agendar" name="id_corte" required>
                                <option value="" disabled selected hidden>Selecione o serviço desejado</option>
                                <?php
                                foreach ($cortes as $corte) {
                                    echo '<option value="' . htmlspecialchars($corte['id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($corte['nome'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes_agendar" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes_agendar" name="observacoes"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="referencia_agendar" class="form-label">Referência</label>
                            <input type="text" class="form-control" id="referencia_agendar" name="referencia">
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="node_modules/parsleyjs/dist/parsley.min.js"></script>
    <script src="node_modules/parsleyjs/dist/i18n/pt-br.js"></script>

    <script>
        function editarAgendamento(button) {
            const id = button.getAttribute('data-id');
            const horario = button.getAttribute('data-horario');
            const corte = button.getAttribute('data-corte');
            const observacoes = button.getAttribute('data-observacoes');
            const referencia = button.getAttribute('data-referencia');

            document.getElementById('id_agendamento').value = id;
            document.getElementById('horario_agendamento').value = horario;
            document.getElementById('id_corte').value = corte;
            document.getElementById('observacoes').value = observacoes;
            document.getElementById('referencia').value = referencia;

            var editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
            editarModal.show();
        }

        $(document).ready(function () {
            $('#formEditarAgendamento').on('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Agendamento atualizado com sucesso.',
                            icon: 'success'
                        }).then(() => {
                            var editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
                            editarModal.hide();

                            window.location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Ocorreu um erro ao atualizar o agendamento.',
                            icon: 'error'
                        });
                    }
                });
            });
        });

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

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sucessoAgendamento = urlParams.get('agendamento_sucesso');
            if (sucessoAgendamento === 'true') {
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'O agendamento foi salvo com sucesso!',
                    icon: 'success'
                }).then(() => {
                    window.history.replaceState(null, null, window.location.pathname);
                });
            }
        };
    </script>

</body>

</html>
<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agendamento = $_POST['id_agendamento'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $horario = $_POST['horario'];
    $servico = $_POST['servico'];
    $observacoes = $_POST['observacoes'];
    $referencia = $_POST['referencia'];

    $sql = "UPDATE agendamentos SET nome = :nome, telefone_cliente = :telefone, horario_agendamento = :horario, id_corte = :servico, observacoes = :observacoes, referencia = :referencia WHERE id = :id_agendamento";

    try {
        $resultado = $conn->prepare($sql);

        $resultado->bindParam(':nome', $nome);
        $resultado->bindParam(':telefone', $telefone);
        $resultado->bindParam(':horario', $horario);
        $resultado->bindParam(':servico', $servico);
        $resultado->bindParam(':observacoes', $observacoes);
        $resultado->bindParam(':referencia', $referencia);
        $resultado->bindParam(':id_agendamento', $id_agendamento);

        $resultado->execute();

        header("Location: ../agendamentos/index.php?atualizacao=sucesso");
        exit();
    } catch (PDOException $e) {
        echo "Erro na atualização: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="modal fade" id="editarModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel">Editar Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarAgendamento" method="POST" action="../agendamentos/index.php">
                    <div class="modal-body">
                        <input type="hidden" id="id_agendamento" name="id_agendamento">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" onkeypress="$(this).mask('(00) 0000-0000')" placeholder="(00) 0000-0000" required>
                        </div>
                        <div class="mb-3">
                            <label for="horario" class="form-label">Horário</label>
                            <input type="datetime-local" class="form-control" id="horario" name="horario" required>
                        </div>
                        <div class="mb-3">
                            <label for="servico" class="form-label">Serviço</label>
                            <select class="form-select" id="servico" name="servico" required>
                                <option value="" disabled selected hidden>Selecione o serviço desejado</option>
                                <?php
                                $sql_servicos = "SELECT id, nome FROM cortes";
                                $resultado_servicos = $conn->prepare($sql_servicos);
                                $resultado_servicos->execute();
                                $servicos = $resultado_servicos->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($servicos as $servico) {
                                    echo "<option value='{$servico['id']}'>{$servico['nome']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" placeholder="Digite suas observações aqui" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="referencia" class="form-label">Como ficou sabendo da barbearia?</label>
                            <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Digite sua resposta aqui" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Mudanças</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['id'])) {
        include 'conexao.php';

        $id_agendamento = $_GET['id'];

        $sql = "SELECT a.*, u.nome AS nome_usuario 
    FROM agendamentos a
    JOIN usuarios u ON a.id_usuario = u.id
    WHERE a.id = :id_agendamento";

        try {
            $resultado = $conn->prepare($sql);
            $resultado->bindParam(':id_agendamento', $id_agendamento);
            $resultado->execute();
            $agendamento = $resultado->fetch(PDO::FETCH_ASSOC);

            echo "<script>
                    document.getElementById('id_agendamento').value = '{$agendamento['id']}';
                    document.getElementById('nome').value = '{$agendamento['nome_usuario']}';
                    document.getElementById('telefone').value = '{$agendamento['telefone_cliente']}';
                    document.getElementById('horario').value = '{$agendamento['horario_agendamento']}';
                    document.getElementById('servico').value = '{$agendamento['id_corte']}';
                    document.getElementById('observacoes').value = '{$agendamento['observacoes']}';
                    document.getElementById('referencia').value = '{$agendamento['referencia']}';
                </script>";
        } catch (PDOException $e) {
            echo "Erro na consulta: " . $e->getMessage();
        }

        $conn = null;
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#editarModal').modal('show');
        });
    </script>
</body>

</html>
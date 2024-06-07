<?php
session_start();
include 'verify/conexao.php';

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
                c.nome AS nome_corte,
                a.telefone_cliente,
                a.horario_agendamento,
                a.observacoes,
                a.referencia
            FROM 
                agendamentos a
            JOIN 
                usuarios u ON a.id_usuario = u.id
            JOIN 
                cortes c ON a.id_corte = c.id
            WHERE 
                a.id_usuario = :usuario_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll();

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
    <title>Barbearia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        #inicio {
            position: relative;
            min-height: 100vh;
            background-image: url('images/imagemInicial1.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .section-dark-bg {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
        }

        #submit {
            background-color: #fff;
            color: #000;
            text-decoration: none;
            padding: 18px 40px;
            border-radius: 8px;
        }

        #submit:hover {
            background-color: rgba(255, 255, 255, 0.5);
        }

        #submit:active {
            background-color: #666666;
        }

        #servicos {
            min-height: 100vh;
            background-image: url('images/imagemInicial1.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.6) !important;
            border: none;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

    <?php
    require('navbar.php');

    if (isset($_GET['login']) && $_GET['login'] == 'success'  && !isset($_SESSION['login_success_alerta'])) {
        echo "<script>
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Bem-Vindo de Volta " . $_SESSION['nome_usuario_cliente'] . "',
          showConfirmButton: false,
          timer: 2000
        });
        </script>";
        $_SESSION['login_success_alerta'] = true;
    }

    if (isset($_GET['cadastro_success']) && $_GET['cadastro_success'] == '1' && !isset($_SESSION['cadastro_success_alerta'])) {
        echo "<script>
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Bem-Vindo ao Barba & Navalha " . $_SESSION['nome_usuario_cliente'] . "',
          showConfirmButton: false,
          timer: 2000
        });
        </script>";
        $_SESSION['cadastro_success_alerta'] = true;
    }
    ?>

    <section id="inicio" class="d-flex justify-content-center align-items-center">
        <div class="container text-center text-white">
            <h1 class="pb-3" style="font-size: 45px">Bem Vindo ao Barba & Navalha</h1>
            <p class="pb-4" style="font-size: 16px">Oferecemos cortes de cabelo e barba de qualidade, além de serviços de estética masculina.</p>
        </div>
    </section>

    <section id="equipe" class="pt-4 pb-5 section-dark-bg">
        <div class="container">
            <div class="row text-center">
                <div class="col">
                    <h2 class="pb-2 section-title">Equipe</h2>
                    <p class="pb-4" style="font-size: 18px;">Conheça a nossa equipe de profissionais qualificados e experientes.</p>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="images/equipe1.jpg" class="card-img-top" alt="João Silva">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 20px;">João Silva</h5>
                            <p class="card-text" style="font-size: 18px;">Especialista em cortes clássicos e modernos. Muita experiência em barbas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="images/equipe2.jpg" class="card-img-top" alt="Carlos Souza">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 20px;">Carlos Souza</h5>
                            <p class="card-text" style="font-size: 18px;">Barbeiro com experiência em barbas e cortes artísticos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <img src="images/equipe3.jpg" class="card-img-top" alt="Ricardo Pereira">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 20px;">Ricardo Pereira</h5>
                            <p class="card-text" style="font-size: 18px;">Expert em tratamentos capilares e estética masculina.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="servicos" class="py-5">
        <div class="container pt-4">
            <h1 class="section-title text-center mb-4 py-4 text-white">Nossos Serviços</h1>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 py-4 g-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-scissors"></i>Corte de Cabelo</h4>
                            <p class="card-text">R$30.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-person-badge"></i>Corte de Cabelo + Barba</h4>
                            <p class="card-text">R$40.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-emoji-smile"></i>Barboterapia</h4>
                            <p class="card-text">R$25.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-droplet"></i>Pigmentação de Barba</h4>
                            <p class="card-text">R$35.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-house-door"></i>Relaxamento Capilar</h4>
                            <p class="card-text">R$40.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-brush"></i>Progressiva</h4>
                            <p class="card-text">R$50.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-eyeglasses"></i>Design de Sobrancelhas</h4>
                            <p class="card-text">R$15.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-stars"></i>Limpeza de Pele</h4>
                            <p class="card-text">R$30.00</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><i class="bi bi-droplet-half"></i>Hidratação</h4>
                            <p class="card-text">R$30.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="horarios" class="py-5 section-dark-bg">
        <div class="container pt-4">
            <h2 class="section-title text-center mb-4 py-4 text-white">Datas e Horários de Agendamento</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100 text-center">
                        <div class="card-body d-flex flex-column justify-content-center" style="height: 300px;">
                            <h4 class="card-title" style="font-size: 24px;"><i class="bi bi-calendar-day icon"></i> Dias de Agendamento</h4>
                            <br>
                            <ul class="list-unstyled styled-list">
                                <li style="font-size: 20px;">Segunda-feira</li>
                                <li style="font-size: 20px;">Terça-feira</li>
                                <li style="font-size: 20px;">Quarta-feira</li>
                                <li style="font-size: 20px;">Quinta-feira</li>
                                <li style="font-size: 20px;">Sábado</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 text-center">
                        <div class="card-body d-flex flex-column justify-content-center" style="height: 300px;">
                            <h4 class="card-title" style="font-size: 24px;"><i class="bi bi-clock icon"></i> Horários de Agendamento</h4>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <ul class="list-unstyled styled-list">
                                        <li style="font-size: 20px;">08:00</li>
                                        <li style="font-size: 20px;">09:30</li>
                                        <li style="font-size: 20px;">11:00</li>
                                        <li style="font-size: 20px;">13:00</li>
                                    </ul>
                                </div>
                                <div class="col">
                                    <ul class="list-unstyled styled-list">
                                        <li style="font-size: 20px;">14:30</li>
                                        <li style="font-size: 20px;">16:00</li>
                                        <li style="font-size: 20px;">17:30</li>
                                        <li style="font-size: 20px;">19:00</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section id="agendar" class="vh-100 d-flex justify-content-center align-items-center section-dark-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center">Agendar Corte</h2>
                    <form method="post" action="verify/agendar.php" data-parsley-validate>
                        <div class="form-group mb-4">
                            <label for="telefone">Telefone<span style="color: red">*</span></label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" onkeypress="$(this).mask('(00) 0000-0000')" placeholder="(00) 0000-0000" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="data">Data e Hora<span style="color: red">*</span></label>
                            <input type="datetime-local" class="form-control" id="data" name="data" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="service">Serviço<span style="color: red">*</span></label>
                            <select class="form-control" id="service" name="service" required>
                                <option value="" disabled selected hidden>Selecione o serviço desejado</option>
                                <option value="1">Corte de Cabelo Masculino</option>
                                <option value="2">Corte de Cabelo + Barba</option>
                                <option value="3">Barboterapia</option>
                                <option value="4">Pigmentação de Barba</option>
                                <option value="5">Relaxamento Capilar</option>
                                <option value="6">Progressiva</option>
                                <option value="7">Design de Sobrancelhas</option>
                                <option value="8">Limpeza de Pele Masculina</option>
                                <option value="9">Hidratação</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="observacao">Especificações</label>
                            <textarea class="form-control" id="observacao" name="observacao" placeholder="Digite suas observações aqui" rows="5"></textarea>
                        </div>
                        <div class="form-group mb-4">
                            <label for="referencia">Como ficou sabendo da barbearia?</label>
                            <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Digite sua resposta aqui">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Agendar</button>
                        <button type="button" id="meuAgendamentoBtn" class="btn btn-secondary btn-block" data-bs-toggle="modal" data-bs-target="#agendamentoModal">Meu agendamento</button>
                    </form>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="agendamentoModal" tabindex="-1" aria-labelledby="agendamentoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark" id="agendamentoModalLabel">Meus Agendamentos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php if (!empty($agendamentos)) : ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Horário</th>
                                                <th>Serviço</th>
                                                <th>Observações</th>
                                                <th>Referência</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($agendamentos as $agendamento) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($agendamento['nome_usuario'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($agendamento['horario_agendamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($agendamento['nome_corte'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($agendamento['observacoes'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($agendamento['referencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p class="text-center text-dark">Nenhum agendamento encontrado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    require('footer.php');
    ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="node_modules/parsleyjs/dist/parsley.min.js"></script>
    <script src="node_modules/parsleyjs/dist/i18n/pt-br.js"></script>
    <link rel="stylesheet" href="node_modules/parsleyjs/src/parsley.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#editarModal').modal('show');
        });

        document.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            const successParam = urlParams.get('success');

            if (successParam === 'true') {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Agendamento realizado com sucesso!',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    </script>

</body>

</html>
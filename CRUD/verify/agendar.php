<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conexao.php';

    $telefone = $_POST['telefone'];
    $data = $_POST['data'];
    $service = $_POST['service'];
    $observacao = $_POST['observacao'];
    $referencia = $_POST['referencia'];
    $usuario_id = $_SESSION['usuario_id'];

    try {
        if (!$conn) {
            throw new PDOException('Falha na conexão com o banco de dados.');
        }

        $sql_verificar = "SELECT COUNT(*) AS total_agendamentos FROM agendamentos WHERE id_usuario = :id_usuario";
        $resultado_verificar = $conn->prepare($sql_verificar);
        $resultado_verificar->bindParam(':id_usuario', $usuario_id);
        $resultado_verificar->execute();
        $row = $resultado_verificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total_agendamentos'] > 0) {
            header("Location: ../inicio_cli.php");
            exit();
        }

        $sql = "INSERT INTO agendamentos (id_usuario, telefone_cliente, horario_agendamento, id_corte, observacoes, referencia) 
                VALUES (:id_usuario, :telefone, :horario, :id_corte, :observacao, :referencia)";

        $resultado = $conn->prepare($sql);

        $resultado->bindParam(':id_usuario', $usuario_id);
        $resultado->bindParam(':telefone', $telefone);
        $resultado->bindParam(':horario', $data);
        $resultado->bindParam(':id_corte', $service);
        $resultado->bindParam(':observacao', $observacao);
        $resultado->bindParam(':referencia', $referencia);

        if ($resultado->execute()) {
            header("Location: ../inicio_cli.php");
            exit();
        } else {
            echo "Erro na inserção do agendamento.";
        }
    } catch(PDOException $e) {
        echo "Erro na inserção do agendamento: " . $e->getMessage();
    } finally {
        $conn = null;
    }
} 


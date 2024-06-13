<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_agendamento = $_GET['id'];

    try {
        $sql = "SELECT * FROM agendamentos WHERE id = :id_agendamento";
        $resultado = $conn->prepare($sql);
        $resultado->bindParam(':id_agendamento', $id_agendamento);
        $resultado->execute();
        $agendamento = $resultado->fetch();
    } catch (PDOException $e) {
        echo "Erro ao buscar o agendamento: " . $e->getMessage();
        exit();
    }

    if (!$agendamento) {
        echo "Agendamento não encontrado.";
        exit();
    }

    try {
        $sql = "DELETE FROM agendamentos WHERE id = :id_agendamento";
        $resultado = $conn->prepare($sql);
        $resultado->bindParam(':id_agendamento', $id_agendamento);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            echo "Agendamento excluído com sucesso.";
        } else {
            echo "Erro ao excluir o agendamento.";
        }

    } catch (PDOException $e) {
        echo "Erro ao excluir o agendamento: " . $e->getMessage();
        exit();
    }

    header("Location: ../agendamentos/index.php");
    exit();
} else {
    header("Location: ../agendamentos/index.php");
    exit();
}
?>

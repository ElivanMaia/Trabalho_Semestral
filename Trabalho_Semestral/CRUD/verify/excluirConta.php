<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

try {
    $sql = "DELETE FROM agendamentos WHERE id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();

    $sql = "DELETE FROM usuarios WHERE id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../login/login.php");
    exit();
} catch (PDOException $e) {
    echo "Erro ao excluir a conta: " . $e->getMessage();
    exit();
}

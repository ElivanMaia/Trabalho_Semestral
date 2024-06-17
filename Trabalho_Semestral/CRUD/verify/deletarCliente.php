<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    try {
        $conn->beginTransaction();

        $sqlAgendamentos = "DELETE FROM agendamentos WHERE id_usuario = :id_usuario";
        $stmtAgendamentos = $conn->prepare($sqlAgendamentos);
        $stmtAgendamentos->bindParam(':id_usuario', $id_usuario);
        $stmtAgendamentos->execute();

        $sqlUsuario = "SELECT * FROM usuarios WHERE id = :id_usuario";
        $stmtUsuario = $conn->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':id_usuario', $id_usuario);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->fetch();

        if (!$usuario) {
            echo "Usuário não encontrado.";
            $conn->rollBack();
            exit();
        }

        $sqlDeleteUsuario = "DELETE FROM usuarios WHERE id = :id_usuario";
        $stmtDeleteUsuario = $conn->prepare($sqlDeleteUsuario);
        $stmtDeleteUsuario->bindParam(':id_usuario', $id_usuario);
        $stmtDeleteUsuario->execute();

        $conn->commit();

        if ($stmtDeleteUsuario->rowCount() > 0) {
            echo "Usuário e seus agendamentos foram excluídos com sucesso.";
        } else {
            echo "Erro ao excluir o usuário.";
        }

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Erro ao excluir o usuário e seus agendamentos: " . $e->getMessage();
        exit();
    }

    header("Location: ../clienteLista/index.php");
    exit();
} else {
    header("Location: ../clienteLista/index.php");
    exit();
}
?>

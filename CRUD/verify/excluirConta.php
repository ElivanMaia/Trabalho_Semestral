<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmacao_exclusao']) && $_POST['confirmacao_exclusao'] === 'confirmado') {
    require 'conexao.php';

    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];

        $sql = "DELETE FROM usuarios WHERE id = :id";
        $resultado = $conn->prepare($sql);
        $resultado->bindValue(':id', $usuarioId);

        if ($resultado->execute()) {
            session_destroy();
            echo "success";
        } else {
            echo "Erro ao excluir a conta. Por favor, tente novamente.";
        }
    } else {
        echo "Sessão inválida. Faça login novamente.";
    }
} else {
    echo "Requisição inválida.";
}

exit();
?>

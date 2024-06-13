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
        $sql = "SELECT * FROM usuarios WHERE id = :id_usuario";
        $resultado = $conn->prepare($sql);
        $resultado->bindParam(':id_usuario', $id_usuario);
        $resultado->execute();
        $usuario = $resultado->fetch();
    } catch (PDOException $e) {
        echo "Erro ao buscar o usuário: " . $e->getMessage();
        exit();
    }

    if (!$usuario) {
        echo "Usuário não encontrado.";
        exit();
    }

    try {
        $sql = "DELETE FROM usuarios WHERE id = :id_usuario";
        $resultado = $conn->prepare($sql);
        $resultado->bindParam(':id_usuario', $id_usuario);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            echo "Usuário excluído com sucesso.";
        } else {
            echo "Erro ao excluir o usuário.";
        }

    } catch (PDOException $e) {
        echo "Erro ao excluir o usuário: " . $e->getMessage();
        exit();
    }

    header("Location: ../clienteLista/index.php");
    exit();
} else {
    header("Location: ../clienteLista/index.php");
    exit();
}
?>

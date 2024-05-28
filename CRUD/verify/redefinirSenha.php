<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['senhaAtual']) && isset($_POST['novaSenha']) && isset($_POST['confirmarSenha'])) {
        require 'conexao.php';
        
        $usuario_id = $_SESSION['usuario_id'];
        $senhaAtual = $_POST['senhaAtual'];
        $novaSenha = $_POST['novaSenha'];
        $confirmarSenha = $_POST['confirmarSenha'];

        $sql = "SELECT senha FROM usuarios WHERE id = :usuario_id";
        $resultado = $conn->prepare($sql);
        $resultado->bindParam(':usuario_id', $usuario_id);
        $resultado->execute();
        $row = $resultado->fetch();

        if ($row) {
            $senhaBanco = $row['senha'];
            if ($senhaAtual === $senhaBanco) {
                $sqlUpdate = "UPDATE usuarios SET senha = :novaSenha WHERE id = :usuario_id";
                $resultadoUpdate = $conn->prepare($sqlUpdate);
                $resultadoUpdate->bindParam(':novaSenha', $novaSenha);
                $resultadoUpdate->bindParam(':usuario_id', $usuario_id);
                $resultadoUpdate->execute();

                header('Location: ../inicio_cli.php?senha_atualizada=1');
                exit();
            } else {
                header('Location: ../inicio_cli.php?erro_senha_atual=1');
                exit();
            }
        } else {
            header('Location: ../inicio_cli.php?usuario_nao_encontrado=1');
            exit();
        }
    }
}
?>

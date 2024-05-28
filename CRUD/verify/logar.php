<?php
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty(trim($email)) && !empty(trim($senha))) {
        session_start();
        require 'conexao.php';

        $sql = "SELECT id, cargo FROM usuarios WHERE email = :email AND senha = :senha";
        $resultado = $conn->prepare($sql);
        $resultado->bindValue(":email", $email);
        $resultado->bindValue(":senha", $senha);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            $usuario = $resultado->fetch();

            $_SESSION['usuario_id'] = $usuario['id'];

            $cargo = $usuario['cargo'];

            if ($cargo == 1) {
                header('Location: ../inicio_funcio.php?login=success');
                exit();
            } else {
                header('Location: ../inicio_cli.php?login=success');
                exit();
            }
        } else {
            $erro = "Email ou senha incorretos.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }

    if (isset($erro)) {
        $_SESSION['erro_login'] = $erro;
        header('Location: ../login/login.php');
        exit();
    }
}
?>
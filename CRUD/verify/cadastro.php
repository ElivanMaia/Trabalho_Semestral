<?php
if (isset($_POST['submit'])) {
    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['senha']) && !empty($_POST['senha'])) {

        session_start();
        require 'conexao.php';
        
        $nome = $_POST['nome'];
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        if (strpos($email, ' ') !== false || strpos($senha, ' ') !== false) {
            header('Location: ../login/registro.php?cadastro_error_espaco=1');
            exit();
        }

        try {
            $sqlChecarEmail = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
            $resultado = $conn->prepare($sqlChecarEmail);
            $resultado->bindParam(':email', $email);
            $resultado->execute();
            $row = $resultado->fetch();

            if ($row['total'] > 0) {
                header('Location: ../login/registro.php?cadastro_error=email_duplicado');
                exit();
            }
        } catch(PDOException $e) {
            echo "Erro na verificação de e-mail: " . $e->getMessage();
            exit();
        }

        $sqlContarFuncionarios = "SELECT COUNT(*) as totalFuncionarios FROM usuarios WHERE cargo = 1";
        $sqlContarFuncionarios = $conn->prepare($sqlContarFuncionarios);
        $sqlContarFuncionarios->execute();

        $totalFuncionarios = $sqlContarFuncionarios->fetch()['totalFuncionarios'];

        $cargo = ($totalFuncionarios < 3) ? 1 : 0;

        $sql = "INSERT INTO usuarios(nome, email, senha, cargo) VALUES(:nome, :email, :senha, :cargo)";
        $resultado = $conn->prepare($sql);
        $resultado->bindValue(":nome", $nome);
        $resultado->bindValue(":email", $email);
        $resultado->bindValue(":senha", $senha);
        $resultado->bindValue(":cargo", $cargo);

        if ($resultado->execute()) {
            $usuario_id = $conn->lastInsertId();

            $_SESSION['usuario_id'] = $usuario_id;
        
            if ($cargo == 1) {
                header('Location: ../inicioAdm.php?cadastro_success=1');
            } else {
                header('Location: ../index.php?cadastro_success=1');
            }
            exit();
        } else {
            header('Location: ../login/registro.php?cadastro_error=1');
            exit();
        }
    } else {
        header('Location: ../login/registro.php?cadastro_error=1');
        exit();
    }
}

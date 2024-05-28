<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #707070;
        }

        .card-custom {
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.7);
        }

        .img-side {
            object-fit: cover;
            border-radius: 15px 0 0 15px;
            max-width: 100%;
            height: 100%;
        }

        .form-container {
            padding: 20px;
        }

        #submit {
            background-color: #343a40;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 12px;
            width: 100%;
        }

        #submit:hover {
            background-color: #8b8b8b;
        }

        #submit:active {
            background-color: #666666;
        }
        #logo {
            height: 170px;
        }
        #imagem{
            margin: 0;
            padding: 0;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <div class="card card-custom">
                    <div class="row g-0">

                        <div class="col-md-5 d-none d-md-block p-0">
                            <img src="../images/imagemLogin.png" alt="Imagem ao Lado" id="imagem" class="img-fluid img-side">
                        </div>

                        <div class="col-md-7">
                            <div class="form-container">
                                <div class="d-flex justify-content-center mb-3">
                                    <img src="../images/logoReal.png" alt="Imagem da Logo" class="img-fluid" id="logo">
                                </div>

                                <?php
                                    if (isset($_SESSION['erro_login'])) {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>E-mail ou senha incorretos!</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                        unset($_SESSION['erro_login']);
                                    }
                                    ?>
                                                                    
                                <form method="post" action="../verify/logar.php" data-parsley-validate>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Endereço de e-mail<span style="color: red">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Digite o seu e-mail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="senha" class="form-label">Senha<span style="color: red">*</span></label> 
                                        <input type="password" class="form-control" id="senha" name="senha" placeholder="Mínimo de 6 caracteres" required>
                                        <div class="mb-3 form-check pt-3">
                                            <input type="checkbox" class="form-check-input" id="mos" onclick="mostrar()">
                                            <label class="form-check-label" for="mostrarSenhaCheckbox">Mostrar Senha</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block" name="submit" id="submit">Entrar</button>
                                    </form>
                                    <p class="mt-3 text-center">Não tem uma conta? <a href="registro.php" class="text-danger">Registrar</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../node_modules/parsleyjs/dist/parsley.min.js"></script>
    <script src="../node_modules/parsleyjs/dist/i18n/pt-br.js"></script>
    <link rel="stylesheet" href="../node_modules/parsleyjs/src/parsley.css">
    <script>

    function mostrar() {
        let senha = document.getElementById("senha");
        let b = document.getElementById("mos");
    
        if (senha.type === "password") {
            senha.type = "text";
            b.textContent = "Ocultar Senha";
        } else {
            senha.type = "password";
            b.textContent = "Mostrar Senha";
        }
    }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p>&copy; <?php echo date("Y"); ?> Barba & Navalha. Todos os direitos reservados.</p>
        <p>Telefone: (88) 9970-9931</p>
        <p>Email: barbaenavalha@gmail.com</p>
        <div class="row">
            <div class="col-md-6">
                <strong>Dias de Agendamento:</strong>
                <ul class="list-inline">
                    <li class="list-inline-item">Segunda</li>
                    <li class="list-inline-item">Terça</li>
                    <li class="list-inline-item">Quarta</li>
                    <li class="list-inline-item">Quinta</li>
                    <li class="list-inline-item">Sábado</li>
                </ul>
            </div>
            <div class="col-md-6">
                <strong>Horários de Agendamento:</strong>
                <ul class="list-inline">
                    <li class="list-inline-item">08:00</li>
                    <li class="list-inline-item">09:30</li>
                    <li class="list-inline-item">11:00</li>
                    <li class="list-inline-item">13:00</li>
                    <li class="list-inline-item">14:30</li>
                    <li class="list-inline-item">16:00</li>
                    <li class="list-inline-item">17:30</li>
                    <li class="list-inline-item">19:00</li>
                </ul>
            </div>
        </div>
        <ul class="list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="text-white">Política de Privacidade</a></li>
            <li class="list-inline-item"><a href="#" class="text-white">Termos de Uso</a></li>
            <li class="list-inline-item"><a href="#" class="text-white">Contato</a></li>
        </ul>
    </div>
</footer>

<!-- Adicione os links dos seus scripts aqui, se necessário -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="node_modules/parsleyjs/dist/parsley.min.js"></script>
<script src="node_modules/parsleyjs/dist/i18n/pt-br.js"></script>
<link rel="stylesheet" href="node_modules/parsleyjs/src/parsley.css">
<script>
    $(document).ready(function(){
        $('#editarModal').modal('show');
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
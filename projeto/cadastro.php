<?php
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    require_once "conexao.php";

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($email || empty($password)));

    try{
        $hash_password = password_hash($password, PASSWORD_DEFAULT);


        $sql = "INSERT INTO usuarios (username,passwords,email) VALUES (?,?,?)";
        $comando = $conexao -> prepare($sql);

        $comando -> execute([$username,$hash_password, $email]);
        header("Location:uc_concluido.html");
        exit();
        //farone esse header é pra quando a pessoa fizer o cadastro ela ser redirecionada para um outro html que o christian e o matheus tem que fazer, é uma página estática falando que o cadastro foi feito e que pode fechar está guia
    }catch(PDOException $e){
        error_log($e -> getMessage());
        header("Location:uc_falha.html");
        //esse error_log eu fiz para que gere um log de erro falando o bug que deu no código e mandar pra gente que é os "funcionarios" e o header é pra direcionar o usuário pra uma página igual a de cadastro feito só que falando que deu erro
    }
}else{
    header("Location:index.html");
    exit();
    // e esse header é pra caso o usuario entrar direto nessa página e ser enviado pra página principal de uma vez
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="banner">
            <nav>
                <h1 class="titulo">ambipar</h1>
                <ul>
                    <li><a href="index.html" class="cabecalho">Home</a></li>
                    <li><a href="sobre.html" class="cabecalho">Sobre</a></li>
                    <li><a href="contato.html" class="cabecalho">Contato</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="cadastro-main">
        <h1 class="titulo-secao">Crie sua conta</h1>
        <p class="cadastro-introducao">Preencha seus dados para realizar o cadastro.</p>

        <section class="formulario-cadastro">
            <form method="POST" action="cadastro.php">
                <div class="campo-cadastro">
                    <label for="username">Nome de usuário</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>

                <div class="campo-cadastro">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>

                <div class="campo-cadastro">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" required>
                </div>

                <button class="botao" type="submit">Cadastrar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Ambipar. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
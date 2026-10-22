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
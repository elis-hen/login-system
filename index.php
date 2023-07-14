<?php
    require('config/conexao.php');

    //SE HÁ POST NOS CAMPOS E NAO SAO VAZIOS
    if (isset($_POST['input_email']) && isset($_POST['input_senha']) && !empty($_POST['input_email']) && !empty($_POST['input_senha'])){
        //RECEBER OS DADOS VINDO DO POST E LIMPAR
        $email = limpar_dado($_POST['input_email']);
        $senha = limpar_dado($_POST['input_senha']);
        $senha_cript = sha1($senha);
        //VERIFICAR SE EXISTE ESTE USUÁRIO
        $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND senha=? LIMIT 1");
        $sql->execute(array($email,$senha_cript));
        $usuario = $sql->fetch(PDO::FETCH_ASSOC);
        //SE EXISTE O USUARIO
        if($usuario){
            //VERIFICAR SE O CADASTRO FOI CONFIRMADO
            if($usuario['status']=="confirmado"){
                //CRIAR UM TOKEN
                $token = sha1(uniqid().date('d-m-Y-H-i-s'));
                //ATUALIZAR O TOKEN DESTE USUARIO NO BANCO
                $sql = $pdo->prepare("UPDATE usuarios SET token=? WHERE email=? AND senha=?");
                if($sql->execute(array($token,$email,$senha_cript))){
                    //ARMAZENAR ESTE TOKEN NA SESSAO (SESSION)
                    $_SESSION['TOKEN'] = $token;
                    header('location: restrita.php');
                }
            }else{
                $erro_login = "Please confirm your registration!";
            }        

        }else{
            $erro_login = "Username and/or password incorrect!";
        }
    }
    

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="./img/shield.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <title>Login System</title>
</head>

<body>
    <div>
        <form method="post">
            <h1>LOGIN</h1>
            <div class="div_icon">
                <img class="img_icon" src="img/user.png">
                <input name="input_email" type="text" placeholder="email" required>
            </div>
            <div class="div_icon">
                <img class="img_icon" src="img/password.png">
                <input name="input_senha" type="password" placeholder="password" required>
            </div>
            <button id="btn_entrar" type="submit">ENTER</button>
            <?php if(isset($erro_login)){ ?>
            <div class="div_erro animate__animated animate__shakeX">
                <?php echo $erro_login; ?>
            </div>
            <?php } ?>
            <a class="a_form" href="esqueci_senha.php">I FORGOT MY PASSWORD</a>
            <a class="a_form" href="cadastro.php">CREATE NEW USER</a>
        </form>
    </div>
</body>

</html>
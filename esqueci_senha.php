<?php
    require('config/conexao.php');

    //REQUERIMENTO DO PHPMAILER
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'config/PHPMailer/src/Exception.php';
    require 'config/PHPMailer/src/PHPMailer.php';
    require 'config/PHPMailer/src/SMTP.php';

    if(isset($_POST['input_email']) && !empty($_POST['input_email'])){
        //RECEBER OS DADOS VINDO DO POST E LIMPAR
        $email = limpar_dado($_POST['input_email']);
        $status="confirmado";

        //VERIFICAR SE EXISTE ESTE USUÁRIO COM STATUS CONFIRMADO
        $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND status=? LIMIT 1");
        $sql->execute(array($email,$status));
        $usuario = $sql->fetch(PDO::FETCH_ASSOC);
        if($usuario){
            //EXISTE O USUARIO
            //ENVIAR EMAIL PARA USUARIO FAZER NOVA SENHA
            $mail = new PHPMailer(true);
            $cod = sha1(uniqid());
            $a_recuperar = "recuperacao";
            //ATUALIZAR O CÓDIGO DE RECUPERACAO DESTE USUARIO NO BANCO
            $sql = $pdo->prepare("UPDATE usuarios SET recupera_senha=?, status=? WHERE email=?");
            if($sql->execute(array($cod,$a_recuperar,$email))){
                try {
                    //Recipients
                    $mail->setFrom('ecferu92@ecferus.com', 'Login System'); //QUEM ESTÁ MANDANDO O EMAIL
                    $mail->addAddress($email, $nome); //PESSOA PARA QUEM VAI O EMAIL
                    
                    //Content
                    $mail->isHTML(true);  //CORPO DO EMAIL COMO HTML
                    $mail->Subject = 'Login System'; //TITULO DO EMAIL
                    $mail->Body =
                            '<!DOCTYPE html>
                            <html lang="en">
                                <head>
                                    <style>
                                        h1 {
                                            color: black;
                                            font-family: Arial, Helvetica, sans-serif;
                                            font-weight: 900;
                                        }
                                        
                                        p {
                                            color: black;
                                            font-family: Arial, Helvetica, sans-serif;
                                        }
                                        
                                        a {
                                            color: black;
                                            text-decoration: none;
                                            font-family: Arial, Helvetica, sans-serif;
                                            padding: 10px;
                                            border-radius: 5px;
                                            font-size: 14px;
                                            font-weight: 550;
                                        }
                                        
                                        a:hover {
                                            border: 2px solid #002bae;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <h1>New Password</h1>
                                    <p>To create a new password, click on the link below.</p>
                                    <a href="'.$site.'nova_senha.php?cod='.$cod.'">CREATE NEW PASSWORD</a>
                                </body>
                            </html>';
                    
                    $mail->send();
                    header('location: confirmacao.php?recuperacao=ok');
            
                    } catch (Exception $e) {
                        echo "There was a problem sending the confirmation email: {$mail->ErrorInfo}";
                    }
            }
        }else{
            $erro_usuario = "There was a failure fetching this email. Try again!";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <title>Login System</title>
</head>
<body>
    <form method="post">
        <h1>LOGIN</h1>
        <div class="div_label">Enter your registered email to recover your password:</div>
        <div class="div_icon">
            <img class="img_icon" src="img/user.png">
            <input name="input_email" type="text" placeholder="email" required>
        </div>
        <button id="btn_enviar" type="submit">SEND</button>
        <?php if(isset($erro_usuario)){
            ?><div class="div_erro animate__animated animate__shakeX"><?php echo $erro_usuario; ?></div><?php
            } 
        ?>
        <a class="a_form" href="index.php">I HAVE REMEMBERED MY PASSWORD</a>
    </form>
</body>
</html>

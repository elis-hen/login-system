<?php
    require('config/conexao.php');

    //REQUERIMENTO DO PHPMAILER
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'config/PHPMailer/src/Exception.php';
    require 'config/PHPMailer/src/PHPMailer.php';
    require 'config/PHPMailer/src/SMTP.php';
    
    //SE A POSTAGEM EXISTE EM CADA UM DOS INPUT'S
    if (isset($_POST['input_email']) && isset($_POST['input_nome']) && isset($_POST['input_senha']) && isset($_POST['input_senha_rep'])){   
        //SE ESTAO VAZIOS
        if (empty($_POST['input_email']) or empty($_POST['input_nome']) or empty($_POST['input_senha']) or empty($_POST['input_senha_rep']) or empty($_POST['input_termos'])){
            $erro_geral = "All fields are required!";
        }else{
            //RECEBER VALORES VINDOS DO POST E LIMPAR
            $email = limpar_dado($_POST['input_email']);
            $nome = limpar_dado($_POST['input_nome']);
            $senha = limpar_dado($_POST['input_senha']);
            $senha_cript = sha1($senha);
            $senha_rep = limpar_dado($_POST['input_senha_rep']);
            $termos = limpar_dado($_POST['input_termos']);
            //VERIFICAR SE NOME É APENAS LETRAS E ESPAÇOS
            if (!preg_match("/^[a-zA-Z-' ]*$/",$nome)) {
                $erro_nome = "Only letters and blanks allowed!";
            }
            //VERIFICAR SE EMAIL É VÁLIDO
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro_email = "Invalid email format!";
            }
            //VERIFICAR SE SENHA TEM MAIS DE 6 DÍGITOS
            if(strlen($senha) < 6 ){
                $erro_senha = "Password must contain at least 6 characters!";
            }
            //VERIFICAR SE SENHA_REP É IGUAL A SENHA
            if($senha !== $senha_rep){
                $erro_senha_rep = "The passwords are different!";
            }
            //VERIFICAR SE TERMOS FOI MARCADO
            if($termos!=="concordo"){
                $erro_termos = "You must accept the terms!";
            }        
            //SE NAO HÁ ERROS
            if(!isset($erro_geral) && !isset($erro_email) && !isset($erro_nome) && !isset($erro_senha) && !isset($erro_senha_rep) && !isset($erro_termos)){
                //VERIFICAR SE ESTE EMAIL JÁ ESTÁ CADASTRADO NO BANCO
                $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? LIMIT 1");
                $sql->execute(array($email));
                $usuario = $sql->fetch();
                //SE NÃO EXISTIR O USUARIO - ADICIONAR NO BANCO
                if(!$usuario){
                    $recupera_senha="";
                    $token="";
                    $codigo_confirmacao = uniqid();
                    $status = "novo";
                    $data_cadastro = date('d/m/Y-H:i:s');
                    $sql = $pdo->prepare("INSERT INTO usuarios VALUES (null,?,?,?,?,?,?,?,?)");
                    if($sql->execute(array($nome,$email,$senha_cript,$recupera_senha,$token,$codigo_confirmacao,$status,$data_cadastro))){
                        //SE O MODO FOR LOCAL
                        if ($modo=="local"){
                            header("location:obrigado.php?usuario=$nome");
                        }
                        //SE O MODO FOR PRODUCAO
                        if($modo =="producao"){
                            //ENVIAR EMAIL PARA USUARIO
                            $mail = new PHPMailer(true);
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
                                            <h1>Registration</h1>
                                            <p>Your registration was successful! Please click on the link below to confirm your email.</p>
                                            <a href="'.$site.'confirmacao.php?cod_confirm='.$codigo_confirmacao.'">CONFIRM EMAIL</a>
                                        </body>
                                    </html>';
                            $mail->send();
                            header("location:confirmacao.php?usuario=$nome");
                            
                            } catch (Exception $e) {
                                echo "There was a problem sending the confirmation email: {$mail->ErrorInfo}";
                            }
                        }   
                    }
                }else{
                    //SE JÁ EXISTE USUARIO APRESENTAR ERRO
                    $erro_geral = "User already registered!";
                }
            }
        
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
        <h1>REGISTRATION</h1>     
        <!--EMAIL-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input <?php if(isset($erro_geral) or isset($erro_email)){echo 'class="erro_input"';} ?> name="input_email" type="email" placeholder="email" <?php if(isset($_POST['input_email'])){ echo "value='".$_POST['input_email']."'";}?> required>
            <?php if(isset($erro_email)){ ?>
            <div class="erro_ind"><?php echo $erro_email; ?></div>
            <?php } ?>     
        </div>
        <!--NOME-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input <?php if(isset($erro_geral) or isset($erro_nome)){echo 'class="erro_input"';}?> name="input_nome" type="text" placeholder="full name" <?php if(isset($_POST['input_nome'])){ echo "value='".$_POST['input_nome']."'";}?> required>
            <?php if(isset($erro_nome)){ ?>
            <div class="erro_ind"><?php echo $erro_nome; ?></div>
            <?php } ?>
        </div>
        <!--SENHA-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input <?php if(isset($erro_geral) or isset($erro_senha)){echo 'class="erro_input"';}?> name="input_senha" type="password" placeholder="minimum 6 characters password" <?php if(isset($_POST['input_senha'])){ echo "value='".$_POST['input_senha']."'";}?> required>
            <?php if(isset($erro_senha)){ ?>
            <div class="erro_ind"><?php echo $erro_senha; ?></div>
            <?php } ?>
        </div>  
        <!--SENHA_REP-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input <?php if(isset($erro_geral) or isset($erro_senha_rep)){echo 'class="erro_input"';}?> name="input_senha_rep" type="password" placeholder="repeat your password" <?php if(isset($_POST['input_senha_rep'])){ echo "value='".$_POST['input_senha_rep']."'";}?> required>
            <?php if(isset($erro_senha_rep)){ ?>
            <div class="erro_ind"><?php echo $erro_senha_rep; ?></div>
            <?php } ?>
        </div>
        <!--TERMOS-->
        <div <?php if(isset($erro_geral) or isset($erro_termos)){echo 'class="erro_input"';}?> class="div_termos">
        <input type="checkbox" name="input_termos" value="concordo" required>
        <label for="input_termos">By registering you agree with our <a class="a_concordar" href="#">Privacy Policy</a> and <a class="a_concordar" href="#">Terms of Use</a>.</label>
        </div>

        <button id="btn_cadastrar" type="submit">REGISTER</button>
        <?php if(isset($erro_geral)){ ?>
            <div class="div_erro animate__animated animate__shakeX"><?php echo $erro_geral;?></div>
        <?php } ?>        
        <a class="a_form" href="index.php">I ALREADY HAVE AN ACCOUNT</a>
    </form>

</body>

</html>
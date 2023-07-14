<?php
require('config/conexao.php');

// if(isset($_GET['cod_confirm']) && !empty($_GET['cod_confirm'])){
    
//     //LIMPAR O GET
//     $cod = limpar_dado($_GET['cod_confirm']);

//     //CONSULTAR SE ALGUM USUARIO TEM ESSE CODIGO DE CONFIRMACAO
//     //VERIFICAR SE EXISTE ESTE USUÁRIO
//     $sql = $pdo->prepare("SELECT * FROM usuarios WHERE codigo_confirmacao=? LIMIT 1");
//     $sql->execute(array($cod));
//     $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    // if($usuario){
    //     //ATUALIZAR O STATUS PARA CONFIRMADO
    //     $status = "confirmado";
    //     $sql = $pdo->prepare("UPDATE usuarios SET status=? WHERE codigo_confirmacao=?");
    //     if($sql->execute(array($status,$cod))){            
    //         header('location: index.php?result=ok');
    //     }
    // }else{
    //    echo "<h1>Código de confirmação inválido!</h1>";
    // }
//}
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
    <form>
        <h1>CONFIRMATION</h1>
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <lottie-player src="https://assets3.lottiefiles.com/private_files/lf30_2iidp4at.json"  background="transparent"  speed="1"  style="width: 150px; height: 150px;"  loop  autoplay></lottie-player>
        
        <?php
            if(isset($_GET['cod_confirm']) && !empty($_GET['cod_confirm'])){
                //LIMPAR O GET
                $cod = limpar_dado($_GET['cod_confirm']);
                $status_verifica = "novo";
                //CONSULTAR SE ALGUM USUARIO TEM ESSE CODIGO DE CONFIRMACAO
                //VERIFICAR SE EXISTE ESTE USUÁRIO
                $sql = $pdo->prepare("SELECT * FROM usuarios WHERE codigo_confirmacao=? AND status=? LIMIT 1");
                $sql->execute(array($cod,$status_verifica));
                $usuario = $sql->fetch(PDO::FETCH_ASSOC);
                //ATUALIZAR O STATUS PARA CONFIRMADO SE ENCONTRAR O USUARIO
                if($usuario){
                    $status = "confirmado";
                    $sql = $pdo->prepare("UPDATE usuarios SET status=? WHERE codigo_confirmacao=?");
                    if($sql->execute(array($status,$cod))){   
                        ?> <div class="div_sucesso animate__animated animate__flash">Your email has been successfully confirmed!</div> <?php         
                    }
                }else{
                    ?> <div class="div_erro animate__animated animate__shakeX">Invalid confirmation code!</div> <?php
                }
                
            }elseif (isset($_GET['usuario'])){
                ?><div class="div_sucesso animate__animated animate__flash">Thank you very much for registering <strong><?php echo $_GET['usuario']; ?></strong>. Now it is necessary to confirm your email.</div><?php
            }elseif (isset($_GET['recuperacao'])){
                ?><div class="div_sucesso animate__animated animate__flash">Recovery email successfully sent!</div><?php
            }elseif (isset($_GET['alteracao'])){
                ?><div class="div_sucesso animate__animated animate__flash">Password successfully changed!</div><?php
            }else{
                ?><div class="div_sucesso animate__animated animate__flash">Thanks for using our system!</div><?php
            }
        ?>
        
        <a class="a_form" href="index.php">RETURN</a>
    </form>
</body>
</html>
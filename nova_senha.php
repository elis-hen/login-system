<?php
require('config/conexao.php');

if(isset($_GET['cod']) && !empty($_GET['cod'])){
    //LIMPAR O GET
    $cod = limpar_dado($_GET['cod']);

    //VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
    if(isset($_POST['input_senha']) && isset($_POST['input_repete_senha'])){
        //VERIFICAR SE TODOS OS CAMPOS FORAM PREENCHIDOS
        if(empty($_POST['input_senha']) or empty($_POST['input_repete_senha'])){
            $erro_geral = "All fields are required!";
        }else{
            //RECEBER VALORES VINDOS DO POST E LIMPAR        
            $senha = limpar_dado($_POST['input_senha']);
            $senha_cript = sha1($senha);
            $repete_senha = limpar_dado($_POST['input_repete_senha']);
        
            //VERIFICAR SE SENHA TEM MAIS DE 6 DÍGITOS
            if(strlen($senha) < 6 ){
                $erro_senha = "Password must contain at least 6 characters!";
            }

            //VERIFICAR SE RETEPE SENHA É IGUAL A SENHA
            if($senha !== $repete_senha){
                $erro_repete_senha = "The passwords are different!";
            }
        
            if(!isset($erro_geral)  && !isset($erro_senha) && !isset($erro_repete_senha)){
                //VERIFICAR SE ESTE RECUPERACAO DE SENHA EXISTE
                $verifica_status="recuperacao";
                $sql = $pdo->prepare("SELECT * FROM usuarios WHERE recupera_senha=? AND status=? LIMIT 1");
                $sql->execute(array($cod,$verifica_status));
                $usuario = $sql->fetch();
                //SE NÃO EXISTIR O USUARIO - ADICIONAR NO BANCO
                if(!$usuario){
                    $erro_geral="Invalid password recovery!";
                }else{
                    //JÁ EXISTE USUARIO COM ESSE CÓDIGO DE RECUPERAÇÃO
                    //ATUALIZAR O TOKEN DESTE USUARIO NO BANCO
                    $atualiza_status="confirmado";
                    $sql = $pdo->prepare("UPDATE usuarios SET senha=?, status=? WHERE recupera_senha=?");
                    if($sql->execute(array($senha_cript,$atualiza_status,$cod))){
                        //REDIRECIONAR PARA LOGIN
                        header('location: confirmacao.php?alteracao=ok');
                    }
                
                }
            }

        }
}

}else{
    header('location:index.php');
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
        <h1>NEW PASSWORD</h1>     
        <div class="div_label">Enter your new password:</div>
        <!--SENHA-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input type="password" <?php if(isset($erro_geral) or isset($erro_senha)){echo 'class="erro_input"';}?> name="input_senha" placeholder="new password" <?php if(isset($_POST['input_senha'])){ echo "value='".$_POST['input_senha']."'";}?> required>
            <?php if(isset($erro_senha)){ ?>
            <div class="erro_ind"><?php echo $erro_senha; ?></div>
            <?php } ?>
        </div>  
        <!--REPETE_SENHA-->
        <div class="div_icon">
            <img class="img_icon" src="img/form.png">
            <input type="password" <?php if(isset($erro_geral) or isset($erro_repete_senha)){echo 'class="erro_input"';}?> name="input_repete_senha" placeholder="repeat the new password" <?php if(isset($_POST['input_repete_senha'])){ echo "value='".$_POST['input_repete_senha']."'";}?> required>
            <?php if(isset($erro_repete_senha)){ ?>
            <div class="erro_ind"><?php echo $erro_repete_senha; ?></div>
            <?php } ?> 
        </div>

        <button id="btn_cadastrar" type="submit">CHANGE PASSWORD</button>
        <?php if(isset($erro_geral)){ ?>
            <div class="div_erro animate__animated animate__shakeX"><?php echo $erro_geral;?></div>
        <?php } ?>        
        <a class="a_form" href="index.php">RETURN</a>
    </form>

</body>

</html>
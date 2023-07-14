<?php
require('config/conexao.php');

//VERIFICAÇÃO DE AUTENTICAÇÃO
$user = autorizacao($_SESSION['TOKEN']);
//SE NÃO ENCONTRAR O USUÁRIO
if (!$user){
    header('location: index.php');
}

//VERIFICAÇÃO DE AUTENTICAÇÃO
// $sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? LIMIT 1");
// $sql->execute(array($_SESSION['TOKEN']));
// $usuario = $sql->fetch(PDO::FETCH_ASSOC);
//SE NÃO ENCONTRAR O USUÁRIO
// if(!$usuario){
//     header('location: index.php');
// }

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
    <form>
        <h1>Restricted Page</h1>
        <div class="div_sucesso animate__animated animate__flash">Welcome <strong><?php echo $user['nome']; ?></strong></div>
        <a class="a_form" href="logout.php">LOGOUT</a>
    </form>
</body>
</html>
<?php
    session_start();

    /* COLOQUE AQUI A URL DO SEU SITE - DAÍ NÃO PRECISA ALTERAR NOS LUGARES DE ENVIO DE EMAIL */
    $site = "https://ecferus.com/login-system/"; // <--- troque pro seu site (não tire a barra final);

    //dois modos possíveis: local e producao
    $modo= 'local'; 
    if ($modo=='local'){
        $servidor="localhost";
        $usuario="root";
        $senha="";
        $banco="db_login";
    }
    if ($modo=='producao'){
        $servidor="localhost";
        $usuario="ecferu92_elis";
        $senha="";
        $banco="ecferu92_login_exemplo";
    }
    try{
        $pdo= new PDO("mysql:host=$servidor;dbname=$banco",$usuario,$senha);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Banco conectado com sucesso!";
    }catch(PDOException $erro){
        echo "Falha ao se conectar com o banco!";
    }

    function limpar_dado($dado){
        $dado = trim($dado);
        $dado = stripslashes($dado);
        $dado = htmlspecialchars($dado);
        return $dado;
    }

    function autorizacao($token_sessao){
        //VERIFICAR SE TEM AUTORIZAÇÃO
        global $pdo;
        $sql = $pdo->prepare("SELECT * FROM usuarios WHERE token=? LIMIT 1");
        $sql->execute(array($token_sessao));
        $usuario = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$usuario){
            return false;
        }else{
            return $usuario;
        }
    }




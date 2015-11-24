<?php

/**Verifica se o botão cadastrar foi pressionado
 * 
 */
if(isset($_POST['btn'])){
    
    //faz a requisiçao de dados para conexao com o banco de dados
    require_once 'dbconfig.php';
/*
 *Conexão com o banco de dados 
 */
try {//Criação do objeto $conn - conexão
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    //echo "Conectado ao banco $dbname em $host com sucesso.";
} catch (PDOException $pe) {
    die("Não foi possível se conectar ao banco $dbname :" . $pe->getMessage());
}

if(isset($_POST['email']) && !empty($_POST)['email'])){

echo "teste";
 
}else {
    echo "Botão pressionado sem e-mail";
}

$conn = null;
}else{
    //Botão cadastrar não foi pressionado
    //Redireciona para a página inicial
    header('Location: index.php');
}

/**
 * tarefa de casa
 * criar um e-mail HTML, enviando um link com o codigo, para a pessoa clicar
 * e confirmar seu email
 */
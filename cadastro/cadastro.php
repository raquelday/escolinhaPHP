<?php

//Faz a requisição de dados paraconexão com o BD
require_once 'dbconfig.php';
/*
 * Conexão com o banco de dados 
 */
try {//Criação do objeto $conn - conexão
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    //echo "Conectado ao banco $dbname em $host com sucesso.";
} catch (PDOException $pe) {
    die("Não foi possível se conectar ao banco $dbname :" . $pe->getMessage());
}

function gerarCodigo() {
    return sha1(mt_rand());
}

/**
 * funçao que converte uma data no formato MySQL
 * para o formato PHP
 * @PARAM TYPE $dataMySQL
 * @return type $dataPHP
 */
function converteDataMySQLPHP($dataMySql){
    $dataPHP = $dataMySQL;
    if($dataMySQL){
        
    }
}

/**
 * Verifica se o botão cadastrar foi pressionado
 * 
 */
if (isset($_POST['btn'])) {
    /**
     * Recepção de dados
     */
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        //Filtragem de entrada ded dados
        //$email = $_POST['email']; //Não é correto

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $cod = gerarCodigo();

        //String SQL
        $sql = "INSERT INTO lista(email,codigo,dtCadastro) "
                . "values(:email,:cod,now())";
        $parametros = array(':email' => $email,
            ':cod' => $cod);
        $p = $conn->prepare($sql);
        $q = $p->execute($parametros);

        /**
         * Tarefa de casa
         * Criar um e-mail HTML, enviando um link
         * com o código, para a pessoa clicar
         * e confirmar seu e-mail
         */
    } else {
        header('Location: index.php');
    }
} elseif (isset($_GET['cod'])) {
    //listagem de e-mails
    $sql = "SELECT email,codigo,status,dtCadastro,dtAtivacao from lista";

    $q = $conn->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);

    while ($r = $q->fetch()) {
        //desimpelhando os pratos
        echo "<p style='color:";
        echo $r['status']? 'green':'red';
        echo ";'>";
        echo $r['email'] . "\t";
        
        echo $r['status'] . "\t";
        echo $r['dtCadastro'] . "\t";
        echo $r['dtAtivacao'] . "\t";
        
        //link de exclusao
        echo "<a href='cadastro.php?cod=d$hash=$r[cod]'>";
        echo $r['codigo'];
        echo "</a>";
    }
}

    //exclusao de um registro
    elseif ($_GET['cod'] == 'd' && isset ($_GET['hash'])){
        $sql = "delete from lista where codigo = :hash";
        $hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);
        
        ECHO "<h1>$hash</h1>";
    

    header('Location: index.php');
}

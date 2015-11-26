<?php
//Faz a requisição de dados paraconexão com o BD
require_once 'dbconfig.php';

//inclusao de função que envia e-mail
include_once 'emailconfirma.php';
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
 * Função que converte uma data no formato MySQL
 * AAAA-MM-DD HH:II:SS -> DD/MM/AAAA HH:II:SS
 * para o formato PHP
 * @param type $dataMySQL
 * @return type $dataPHP
 */
function converteDataMySQLPHP($dataMySQL){
    $dataPHP = $dataMySQL;
    if($dataMySQL){
       $dataPHP = date('d/m/Y G:i:s',  strtotime($dataMySQL)) ;
    }
    return $dataPHP;
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
              
          $link = "<a href='http://"; 
            $link .= $_SERVER['SERVER_NAME'];        
            $link .= $_SERVER['PHP_SELF'];
            $link .= "?cod=e&hash=$cod' ";
            $link .= "title='Clique para confirmar o e-mail'>";
            $link .= "Clique para confirmar seu e-mail";
            $link .= "</a>";
            
            emailConfirma($email,$link);
            
    } else {
        header('Location: index.php');
    }
} elseif (isset($_GET['cod'])) {
    if ($_GET['cod'] == 'listar') {
        //LISTAGEM DE E-MAILS
        // select * from lista // desaconselhado
        
        $sql = "SELECT email,codigo,status,dtCadastro,dtAtivacao "
                . "from lista";
        
        $q = $conn->query($sql);
        $q->setFetchMode(PDO::FETCH_ASSOC);
        
        while ($r = $q->fetch()) {
            //desmpilhando os pratos
            echo "<p style='color:";
            echo $r['status'] ? 'green' : 'red';
            echo ";'>";
            echo $r['email'] . "\t";
            
            //Link de exclusão
            echo "<a href='cadastro.php?cod=d&hash=$r[codigo]' ";
            echo "title='Clique para excluir'>";
            echo $r['codigo'];
            echo "</a>" . "\t";
       
    
    //link para ser enviado por email
            
            $link = "<a href='".$_SERVER['PHP_SELF'];
            $link .= "?cod=e&hash=$r[codigo]' ";
            $link .= "title='Clique para confirmar o e-mail'>";
            $link .= $r['status']. "\t";
            $link .= "</a>";
            
            echo $link;
            
            echo converteDataMySQLPHP($r['dtCadastro']) . "\t";
            echo converteDataMySQLPHP($r['dtAtivacao']);
            echo "</p>\n";
        }
    }
    //Exclusão de um registro
    elseif($_GET['cod'] == 'd' && isset ($_GET['hash'])){
        $sql = "delete from lista where cod = :hash";
        $hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);
        
        //echo "<h1>$hash</h1>";
        
        $p = $conn->prepare($sql);
        $q = $p->execute(array(':hash'=>$hash));
        
        header("Location: cadastro.php?cod=listar");
    }
    elseif($_GET['cod'] == 'e' && isset ($_GET['hash'])){
            $sql = "update lista set status=1, dtAtivacao = now() where cod= :hash";
            $hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);

            //echo "<h1>$hash</h1>";

            $p = $conn->prepare($sql);
            $q = $p->execute(array(':hash'=>$hash));

            header("Location: cadastro.php?cod=listar");
    }
    
    //Validação do e-mail
} else {
    //Botão cadastrar não foi pressionado
    //E nem o código foi passado
    //Redireciona para a página inicial
    header('Location: index.php');
}
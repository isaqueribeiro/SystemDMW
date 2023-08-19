<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of configuracao
 *
 * @author Isaque
 */
abstract class Configuracao {
    private static $instancia = null;
    
    private $servidor = ''; 
    private $porta    = ''; 
    private $banco    = ''; 
    private $usuario  = ''; 
    private $senha    = ''; 
    private $charset  = ''; 

    function getServidor() {
        return $this->servidor;
    }

    function getPorta() {
        return $this->porta;
    }

    function getBanco() {
        return $this->banco;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getSenha() {
        return $this->senha;
    }

    function getCharset() {
        return $this->charset;
    }
    
    function getDsn() {
        return $this->servidor . '/' . $this->porta . ':' . $this->banco;
    }

    function getTns() {
        $tns  = '(DESCRIPTION = ';
        $tns .= '    (ADDRESS_LIST = ';
        $tns .= '      (ADDRESS = (PROTOCOL = TCP)(HOST = ' . $this->servidor . ')(PORT = ' . $this->porta . ')) ';
        $tns .= '    ) ';
        $tns .= '    (CONNECT_DATA = (SID = ' . $this->banco . ')';
        $tns .= '      (SERVICE_NAME = orcl) ';
        $tns .= '    ) ';
        $tns .= '  )';
        
        return $tns; 
    }
    
    private function __construct() {
        $file = 'conexao.ini';
        
        if (!$settings = parse_ini_file($file, TRUE)) {
            throw new exception('Não foi possível abrir o arquivo ' . $file . '.');
        }
        $this->servidor = $settings['database_agil']['servidor'];
        $this->porta    = $settings['database_agil']['porta'];
        $this->banco    = $settings['database_agil']['banco'];
        $this->usuario  = $settings['database_agil']['usuario'];
        $this->senha    = $settings['database_agil']['senha'];
        $this->charset  = $settings['database_agil']['charset'];
        /*
         * 
        $this->servidor = $settings['database_mv']['servidor'];
        $this->porta    = $settings['database_mv']['porta'];
        $this->banco    = $settings['database_mv']['banco'];
        $this->usuario  = $settings['database_mv']['usuario'];
        $this->senha    = 'etcas)(*'; 
        $this->charset  = $settings['database_mv']['charset'];
         * 
         */
    }
    
    /**
     * 
     * @return Configuracao
     */
    public static function getInstancia() {
        if (self::$instancia == NULL) {
            self::$instancia = new ConfiguracaoFirebird();
            //self::$instancia = new ConfiguracaoOracle();
        }
        
        return self::$instancia;
    }
    
    public abstract function db($usuario, $senha); 
}

class ConfiguracaoOracle extends Configuracao {
    public function db($login, $senha) {
        if (trim($login) == '') {
            $login = $this->getUsuario();
        }
        if (trim($senha) == '') {
            $senha = $this->getSenha();
        }
        
        //$str = 'oci:dbname='.$this->getTns().';charset='.$this->getCharset(); // Linha oficial para conexão PDO com o Oracle

        //$pdo = new PDO($str, $login, $senha) or die('Erro ao tentar conectar.');
        //$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // Forçar o acesso as colunas das tabelas com nome em minúsculo

        //return $pdo;  

        $conn = oci_pconnect($this->getUsuario(), $this->getSenha(), 'PRODUCAO');
        return $conn; 
    }
}

class ConfiguracaoFirebird extends Configuracao {
    public function db($login, $senha) {
        if (trim($login) === '') {
            $login = $this->getUsuario();
        }
        if (trim($senha) === '') {
            $senha = $this->getSenha();
        }
        
        $str = 'firebird:dbname='.$this->getDsn().';charset='.$this->getCharset().';dialect=3'; // Linha oficial para conexão PDO com o FireBird
        
        //$pdo = new PDO($str, $this->getUsuario(), $this->getSenha(), array(PDO::ATTR_CASE => PDO::CASE_LOWER)); // Esta forma funciona também
        $pdo = new PDO($str, $login, $senha) or die('Erro ao tentar conectar.');
        $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); // Forçar o acesso as colunas das tabelas com nome em minúsculo

        return $pdo; 
    }
}    


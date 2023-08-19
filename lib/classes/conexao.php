<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conexao
 *
 * @author Isaque Marinho Ribeiro
 */
class Conexao {
    
    public static $banco = array(
        'default' => array(
            'servidor' => 'localhost',
            'banco'    => 'AGIL_DOCUMENTOS',
            'porta'    => '3050',
            'usuario'  => 'sysdba',
            'senha'    => '',
            'driver'   => 'firebir',
            'charset'  => 'win1252'
        ),
//        'default' => array(
//            'servidor' => 'localhost',
//            'banco'    => 'AGIL_COMERCIO',
//            'porta'    => '3050',
//            'usuario'  => 'sysdba',
//            'senha'    => '',
//            'driver'   => 'firebir',
//            'charset'  => 'win1252'
//        ),
        
        'oracle' => array(
            'servidor' => '10.0.8.221',
            'banco'    => 'dbamv',
            'porta'    => '1521',
            'usuario'  => 'dbamv',
            'senha'    => 'etcas)(*',
            'driver'   => 'oracle',
            'charset'  => 'UTF8'
        ),
        
        'mysqli' => array(
            'servidor' => 'skynet.mysql.uhserver.com',
            'banco'    => 'jonh_conner',
            'porta'    => '3306',
            'usuario'  => 'jonh_conner',
            'senha'    => '',
            'driver'   => 'mysqli',
            'charset'  => 'utf-8'
        )
    );
    /*
     * 
     * 
    private $servidor = "localhost";
    private $porta    = 3050; 
    private $banco    = "HOPE";
    private $usuario  = "SYSDBA";
    private $senha    = "masterkey"; 
    
    public function set_servidor($value) {
        $this->servidor = $value;
    }
    
    public function set_porta($value) {
        $this->porta = $value;
    }
    
    public function set_banco($value) {
        $this->banco = $value;
    }
    
    public function set_usuario($value) {
        $this->usuario = $value;
    }
    
    public function set_senha($value) {
        $this->senha = $value;
    }
    
    public function get_host() {
        echo $this->servidor . "/" . $this->porta . ":" . $this->banco;
    }

    public function get_servidor() {
        return $this->servidor;
    }
    
    public function get_porta() {
        return $this->porta;
    }
    
    public function get_banco() {
        return $this->banco;
    }
    
    public function get_usuario() {
        return $this->usuario;    
    }
    
    public function get_senha() {
        return $this->senha;
    }
     * 
     * 
     */
    
}

class DB {
    private static $banco = array();
    
    /**
     * Método usado para instanciar um objeto de conexão com o banco de dados.
     * @param type $tipo
     * @return type
     */
    public static function criar($tipo) {
        if (!array_key_exists($tipo, Config::$banco)) {
            die('Configuração de banco de dados não encontrada!');
        }
        
        if (array_key_exists($tipo, self::$banco)) {
            return self::$banco[$tipo];
        }
        
        switch (Config::$banco[$tipo]['driver']) {

            case 'firebir': {
                self::$banco[$tipo] = new ibase_connect(
                        Conexao::$banco[$tipo]['servidor'] . '/' .
                        Conexao::$banco[$tipo]['porta'] . ':' .
                        Conexao::$banco[$tipo]['banco'],
                        Conexao::$banco[$tipo]['usuario'],
                        Conexao::$banco[$tipo]['senha']
                    ) or die ('Erro na conexão com a base de dados');

                if (Config::$banco[$tipo]['charset'] != '') {
                    self::$banco[$tipo]->set_charset(Conexao::$banco[$tipo]['charset']);
                }

                return self::$banco[$tipo];
            } break;
                
            case 'mysqli': {
                self::$banco[$tipo] = new mysqli(
                        Conexao::$banco[$tipo]['servidor'],
                        Conexao::$banco[$tipo]['usuario'],
                        Conexao::$banco[$tipo]['senha'],
                        Conexao::$banco[$tipo]['banco']
                    ) or die ('Erro na conexão com a base de dados');

                if (Config::$banco[$tipo]['charset'] != '') {
                    self::$banco[$tipo]->set_charset(Conexao::$banco[$tipo]['charset']);
                }

                return self::$banco[$tipo];
            } break;
                
        }
    }
}

?>
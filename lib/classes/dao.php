<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe responsável por executar chamadas comuns a objetos do banco como:
 * Generetor, GuiID, Max, ECT.
 * 
 * @author Isaque Marinho Ribeiro
 */
abstract class Dao {
    private static $isntancia = null;
    
    private function __construct() {}

    /**
     * 
     * @return Autenticador
     */
    public static function getInstancia() {
        if (self::$isntancia == NULL) {
            self::$isntancia = new DaoFireBird();
            //self::$isntancia = new DaoOracle();
        }
        
        return self::$isntancia;
    }
    
    public abstract function getCountID($table_name, $field_name);
    public abstract function getGeneratorID($generator_name);
    public abstract function getGuidIDFormat();
    public abstract function getDataHoraServidor();
    public abstract function getDataServidor();
    public abstract function getHoraServidor();
}

class DaoFireBird extends Dao {
    public function getCountID($table_name, $field_name) {
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');

            $sql = "Select max(coalesce({$field_name}, 0)) + 1 as id from {$table_name} "; 
            $res = $pdo->query($sql);

            $i = 0;

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $i = $obj->id; 
            } else {
                $i = 1;
            }

            return $i;
        } catch (Exception $ex) {
            return 0;
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getCountID() : </strong>" . $ex->getMessage();
        } 
    }

    public function getGeneratorID($generator_name) {
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');

            $sql = "Select first 1 gen_id({$generator_name}, 1) as id from SYS_SISTEMA";
            $res = $pdo->query($sql);

            $i = 0;

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $i = $obj->id;
                $pdo->commit();
            } else {
                $pdo->rollBack();
            }

            return $i;
        } catch (Exception $ex) {
            return 0;
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getGeneratorID() : </strong>" . $ex->getMessage();
        } 
    }

    public function getGuidIDFormat() {
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');

            $sql = "Select g.hex_uuid_format as id from GET_GUID_UUID_HEX g";
            $res = $pdo->query($sql);

            $guid = "{00000000-0000-0000-0000-000000000000}";

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $guid = $obj->id;
                $pdo->commit();
            } else {
                $pdo->rollBack();
            }

            return $guid;
        } catch (Exception $ex) {
            return "{00000000-0000-0000-0000-000000000000}";
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getGuidIDFormat() : </strong>" . $ex->getMessage();
        } 
    }

    public function getDataHoraServidor() {
        // Formato do retorno no FireBird : 2016-07-01 11:24:08
        $dh = date("Y-m-d H:i:s");
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');


            $sql = "Select dh_servidor from VW_INFORMACOES";
            $res = $pdo->query($sql);

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $dh = $obj->dh_servidor;
            }

            return $dh;
        } catch (Exception $ex) {
            return $dh;
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getDataHoraServidor() : </strong>" . $ex->getMessage();
        } 
    }

    public function getDataServidor() {
        // Formato do retorno no FireBird : 2016-07-01
        $dt = date("Y-m-d");
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');


            $sql = "Select dt_servidor from VW_INFORMACOES";
            $res = $pdo->query($sql);

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $dt = $obj->dt_servidor;
            }

            return $dt;
        } catch (Exception $ex) {
            return $dt;
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getDataServidor() : </strong>" . $ex->getMessage();
        } 
    }

    public function getHoraServidor() {
        // Formato do retorno no FireBird : 11:24:08
        $hr = date("H:i:s");
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');


            $sql = "Select hr_servidor from VW_INFORMACOES";
            $res = $pdo->query($sql);

            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $hr = $obj->hr_servidor;
            }

            return $hr;
        } catch (Exception $ex) {
            return $hr;
            echo $ex . "<br><br><strong>Error Dao::getInstancia->getDataServidor() : </strong>" . $ex->getMessage();
        } 
    }

}
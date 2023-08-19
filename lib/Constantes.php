<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Constantes
 *
 * @author Isaque
 */
class Constantes {
    //put your code here
    public $acao = '';
    
    private $copy_ano  = '2016';
    private $home_ham  = "http://www.castanhal.pa.gov.br";
    private $home_agil = "http://www.agilsoftwares.com.br";
    
    private $company_ham  = 'Secretaria Municipal de Saúde de Castanhal/PA';
    private $company_agil = 'Ágil Soluções em Softwares';
    
    private $data_default = '2016-01-01';
    private $hora_default = '00:00';

    public function get_acao() {
        return $this->acao;
    }
    
    public function get_copy_ano() {
        return $this->copy_ano;
    }
    
    public function get_home_ham() {
        return $this->home_ham;
    }
    
    function get_home_agil() {
        return $this->home_agil;
    }

    function get_company_ham() {
        return $this->company_ham;
    }

    function get_company_agil() {
        return $this->company_agil;
    }

    function get_data_default() {
        return $this->data_default;
    }
    
    function get_hora_default() {
        return $this->hora_default;
    }
    
    function UrlAtual(){
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        return $url;
    }

    function message_inform($mensagem) {
        $str = strip_tags($mensagem);
        $msg = "";
        
        $msg .= "<div class='alert alert-info alert-dismissable'>";
        $msg .= "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        $msg .= "   <h4><i class='icon fa fa-info'></i> Informe!</h4>";
        $msg .= "    {$str}.";
        $msg .= "</div>";
        return $msg;
    }

    function message_alert($mensagem) {
        $str = strip_tags($mensagem);
        $msg = "";
        
        $msg .= "<div class='alert alert-warning alert-dismissable'>";
        $msg .= "    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        $msg .= "    <h4><i class='icon fa fa-warning'></i> Alerta!</h4>";
        $msg .= "    {$str}";
        $msg .= "</div>";
        return $msg;
    }

    function message_error($mensagem) {
        $str = strip_tags($mensagem);
        $msg = "";
        
        $msg .= "<div class='alert alert-danger alert-dismissable'>";
        $msg .= "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        $msg .= "   <h4><i class='icon fa fa-ban'></i> Erro!</h4>";
        $msg .= "    {$str}.";
        $msg .= "</div>";
        return $msg;
    }
    
    function message_sucess($mensagem) {
        $str = strip_tags($mensagem);
        $msg = "";
        
        $msg .= "<div class='alert alert-success alert-dismissable'>";
        $msg .= "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        $msg .= "   <h4>	<i class='icon fa fa-check'></i> Sucesso!</h4>";
        $msg .= "    {$str}.";
        $msg .= "</div>";
        return $msg;
    }

    function remover_acentos($string) {
        return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
    }
}

?>
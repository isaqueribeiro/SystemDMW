<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

function IniciaisNome($nome){
    $str = strtoupper($nome); 
    
    $str = str_replace(" DE ",  " ", $str); 
    $str = str_replace(" DA ",  " ", $str); 
    $str = str_replace(" DI ",  " ", $str); 
    $str = str_replace(" DO ",  " ", $str); 
    $str = str_replace(" DU ",  " ", $str); 
    $str = str_replace(" E ",   " ", $str); 
    $str = str_replace(" DOS ", " ", $str); 
    $str = str_replace(" DAS ", " ", $str); 

    $arr = explode(" ", $str);
    $ini = "";
    
    for ($i = 0; $i < count($arr); $i++) {
        $ini .= substr($arr[$i], 0, 1);
    }
    
    return $ini; 
}

function validarCPF($cpf = null) {
 
    // Verifica se um número foi informado
    if(empty($cpf)) {
        return false;
    }
 
    // Elimina possivel mascara
    $cpf = ereg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        return false;
     // Calcula os digitos verificadores para verificar se o
     // CPF é válido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

function formatarTexto($mascara, $string) {
    $string = str_replace(" ", "", $string);
    for ($i = 0; $i < strlen($string); $i++) {
        $mascara[strpos($mascara, "#")] = $string[$i];
    }
    return $mascara;
}

function validarData($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = (int)substr($data, 0, 4);
            $m = (int)substr($data, 4, 2);
            $d = (int)substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = (int)substr($data, 0, 4);
            $d = (int)substr($data, 4, 2);
            $m = (int)substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    return checkdate($m, $d, $a);
}

function getDescricaoMes($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = substr($data, 0, 4);
            $m = substr($data, 4, 2);
            $d = substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = substr($data, 0, 4);
            $d = substr($data, 4, 2);
            $m = substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    $descricao = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return $descricao[(int)$m - 1];
}

function getDataExtenso($data_in) {
    $dt  = explode("/", $data_in);
    $mes = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return $dt[0] . " de " . $mes[intval($dt[1])] . " de " . $dt[2];
}

function getAbreviacaoMes($data, $formato = 'DD/MM/AAAA') {
    switch($formato) {
        case 'DD-MM-AAAA':
        case 'DD/MM/AAAA':
            $d = (int)substr($data, 0, 2);
            $m = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAA/MM/DD':
        case 'AAAA-MM-DD':
            $a = (int)substr($data, 0, 4); 
            $m = (int)substr($data, 5, 2);
            $d = (int)substr($data, 8, 2);
            break;

        case 'AAAA/DD/MM':
        case 'AAAA-DD-MM':
            $a = (int)substr($data, 0, 4); 
            $d = (int)substr($data, 5, 2);
            $m = (int)substr($data, 8, 2);
            break;

        case 'MM-DD-AAAA':
        case 'MM/DD/AAAA':
            $m = (int)substr($data, 0, 2);
            $d = (int)substr($data, 3, 2);
            $a = (int)substr($data, 6, 4); 
            break;

        case 'AAAAMMDD':
            $a = substr($data, 0, 4);
            $m = substr($data, 4, 2);
            $d = substr($data, 6, 2);
            break;

        case 'AAAADDMM':
            $a = substr($data, 0, 4);
            $d = substr($data, 4, 2);
            $m = substr($data, 6, 2);
            break;

        default:
            throw new Exception( "Formato de data inválido");
    }
    
    $descricao = array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
    return strtoupper($descricao[(int)$m - 1]);
}

function excluirArquivosJson($pasta, $token_user) {
    $filtro = (trim($token_user) === ""?md5(date("r") . "admin"):$token_user) . ".json";

    if(is_dir($pasta)) {
        $diretorio = dir($pasta);

        while($arquivo = $diretorio->read()) {
            if (($arquivo !== '.') && ($arquivo !== '..') && (strpos($arquivo, $filtro) !== false)) {
                unlink($pasta . $arquivo);
            }
        }

        $diretorio->close();
    }
}    

function leftStr($str, $length) {
     return substr($str, 0, $length);
}

function rightStr($str, $length) {
     return substr($str, -$length);
}

function getTagSelected($constante, $value) {
    if ( $constante === $value  ) {
      return "selected='selected'";
    } else {
        return "";
    }
}

function getGuidEmpty() {
    return "{00000000-0000-0000-0000-000000000000}";
}

function encript($value) {
    $chave   = "d033e22ae348aeb5660fc2140aec35850c4da997"; // admin (md5)
    $data    = base64_encode($value);
    $tam     = strlen($data);
    $posic   = rand(0, $tam);
    $retorno = "";
    if ( $posic === $tam ) {
        $retorno = leftStr($data, $posic) . $chave;
    } else {
        $retorno = leftStr($data, $posic) . $chave . rightStr($data, $tam - $posic);
    }
    return base64_encode($retorno);
}

function decript($value) {
    $chave   = "d033e22ae348aeb5660fc2140aec35850c4da997"; // admin (md5)
    $data    = base64_decode($value);
    $retorno = str_replace($chave, "", $data);
    return base64_decode($retorno);
}

function estaEncript($value) {
    $str = decript($value);
    return ($str !== "");
}
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include './classes/Conexao.php';
    
$conexao = new Conexao();
    
$conexao->set_servidor("localhost");
$conexao->set_porta(3050);
$conexao->set_banco("AGIL_DOCUMENTOS");
    
$dbh = ibase_connect ( $conexao->get_host(), $conexao->get_usuario(), $conexao->get_senha() ) or die ("Erro na conexão com a base.<br>" . ibase_errmsg());
    
?>
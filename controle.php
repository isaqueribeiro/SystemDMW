<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './lib/classes/usuario.php';
require_once './lib/classes/perfil.php';
require_once './lib/classes/autenticador.php';
require_once './lib/classes/sessao.php';
require_once './lib/classes/configuracao.php';
require_once './lib/funcoes.php';

switch ($_REQUEST['ac']) {
    case 'homeDesktop': {
        echo "<div id='loading'><i class='loading-img'></i></div>";
    } break;

    case 'logar': {
        $aut = Autenticador::getInstancia();
        
        if ( (trim($_REQUEST['login']) == '') || (trim($_REQUEST['senha']) == '') ) {
            header('location: index.php?ac=error_login');
        }
        elseif ($aut->logar($_REQUEST['login'], $_REQUEST['senha'])) {
            header('location: principal.php');
        } else {
            header('location: index.php?ac=error_login');
        }
    } break;

    case 'bloquear': {
        $aut = Autenticador::getInstancia();
        $aut->bloquear_sessao();
        header('location: bloquear_pagina.php');
    } break;

    case 'desbloquear': {
        $aut = Autenticador::getInstancia();
        $usr = $aut->pegar_usuario(); 
        if ($usr->getSenha() == $_REQUEST['senha']) {
            $aut->desbloquear_sessao();
            header('location: principal.php');
        } else {
            header('location: bloquear_pagina.php?ac=error_pwd');
        }
    } break;

    case 'sair' : {
        $aut = Autenticador::getInstancia();
        $usr = $aut->pegar_usuario(); 
        excluirArquivosJson("logs/", $usr->getToken_id());
        
        header('location: index.php');
    } break;
}

?>
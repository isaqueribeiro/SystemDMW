<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author Isaque Marinho Ribeiro
 */
class Usuario {
    // Atributos
    private $id = null;
    private $login         = null;
    private $email         = null;
    private $nome_completo = null;
    private $senha         = null;
    private $senhaAutoriza = null;
    private $tipo          = null;
    private $perfil        = null;
    private $perfil_codigo = null;
    private $data_cadastro = null;
    private $sexo          = null;
    private $imagem        = null;
    private $setor         = 0;
    private $setor_nome    = "";
    private $sessao_bloqueada = false;
    private $usuario_medico   = false;
    private $acesso_web       = false;
    private $sistema       = 1;
    private $sistema_nome  = "Document Manager Web | Sistema Gestor de Documentos";
    private $token_id      = null;
    
    function getId() {
        return $this->id;
    }

    function getLogin() {
        return $this->login;
    }

    function getEmail() {
        return $this->email;
    }

    function getNome_completo() {
        return $this->nome_completo;
    }

    function getSenha() {
        return $this->senha;
    }

    function getSenhaAutoriza() {
        return $this->senhaAutoriza;
    }

    function getTipo() {
        return $this->tipo;
    }
    
    function getPerfil() {
        return $this->perfil;
    }
    
    function getPerfilCodigo() {
        return $this->perfil_codigo;
    }
    
    function getData_cadastro() {
        return $this->data_cadastro;
    }
    
    function getSexo() {
        return $this->sexo;
    }
    
    function getImagem() {
        return $this->imagem;
    }
    
    function getSetor() {
        return $this->setor;
    }
    
    function getSetorNome() {
        return $this->setor_nome;
    }
    
    function getSessao_bloqueada() {
        return $this->sessao_bloqueada;
    }

    function getUsuario_medico() {
        return $this->usuario_medico;
    }
    
    function getAcessoWeb() {
        return $this->acesso_web;
    }
    
    function getSistema() {
        return $this->sistema;
    }
    
    function getSistemaNome() {
        return $this->sistema_nome;
    }
    
    function setId($id) {
        $this->id = $id;
    }

    function setLogin($login) {
        $this->login = $login;
        $this->token_id = md5(date("r") . $login);
    }

    function setEmail($email) {
        $this->email = $email;
    }
    
    function setNome_completo($nome_completo) {
        $this->nome_completo = $nome_completo;
    }

    function setSenha($senha) {
        $this->senha = $senha;
    }

    function setSenhaAutoriza($senhaAutoriza) {
        $this->senhaAutoriza = $senhaAutoriza;
    }

    function setPerfil($perfil) {
        $this->perfil = $perfil;
    }
    
    function setPerfilCodigo($perfil_codigo) {
        $this->perfil_codigo = $perfil_codigo;
    }
    
    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function setData_cadastro($data_cadastro) {
        $this->data_cadastro = $data_cadastro;
    }

    function setSexo($sexo) {
        $this->sexo = $sexo;
    }

    function setImagem($imagem) {
        $this->imagem = $imagem;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }

    function setSetorNome($setor_nome) {
        $this->setor_nome = $setor_nome;
    }

    function setSessao_bloqueada($sessao_bloqueada) {
        $this->sessao_bloqueada = $sessao_bloqueada;
    }

    function setUsuario_medico($usuario_medico) {
        $this->usuario_medico = $usuario_medico;
    }
    
    function setAcessoWeb($acesso_web) {
        $this->acesso_web = $acesso_web;
    }    
    
    function getToken_id() {
        return $this->token_id;
    }
    
    function getIcon128x128() {
        $img = '';
        if ( $this->sexo === 'M' ) {
          $img = 'user-man-128x128';    
        } else {
          $img = 'user-woman-128x128';      
        }
        return $img;
    }
    
    function getIcon160x160() {
        $img = '';
        if ( $this->sexo === 'M' ) {
          $img = 'user-man-160x160';    
        } else {
          $img = 'user-woman-160x160';      
        }
        return $img;
    }
    
    function getIcon256x256() {
        $img = '';
        if ( $this->sexo === 'M' ) {
          $img = 'user-man-256x256';    
        } else {
          $img = 'user-woman-256x256';      
        }
        return $img;
    }
    
    public function listar($condicoes = array()) {
        $db = DB::criar('default');
        
        $sql = 'select * from TBUSERS ';
        
        $where = array();
        foreach($condicoes as $campo => $valor) {
            $where = '{$campo} = {$valor}';
        }
        
        if ($where != array()) {
            $where = ' where ' . implode(' and ', $where);
        } else {
            $where = '';
        }
        
        $sql .= $where;
        
        $result = ibase_query($db, $sql); 
        $lista  = ibase_fetch_assoc($result);
        $result->free();
        
        return $lista;
    }
}

?>
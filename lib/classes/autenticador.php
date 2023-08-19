<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Autenticador
 *
 * @author Isaque
 */

abstract class Autenticador {
    private static $isntancia = null;
    
    private function __construct() {}

    /**
     * 
     * @return Autenticador
     */
    public static function getInstancia() {
        if (self::$isntancia == NULL) {
            self::$isntancia = new AutenticadorEmBanco();
            //self::$isntancia = new AutenticadorEmMemoria();
        }
        
        return self::$isntancia;
    }
    
    public abstract function expulsar();
    public abstract function bloquear();
    public abstract function logar($login, $senha); 
    public abstract function esta_logado();
    public abstract function pegar_usuario();
    public abstract function desbloquear_sessao();
    public abstract function bloquear_sessao();
}

class AutenticadorEmMemoria extends Autenticador {
    
    public function expulsar() {
        header('location: controle.php?ac=sair');
    }

    public function bloquear() {
        header('location: controle.php?ac=bloquear');
    }

    public function logar($login, $senha) {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if (($login == 'isaque.ribeiro') && ($senha == 'admin')) {
            $usuario = new Usuario();
            $perfil  = new Perfil();

            $usuario->setLogin($login);
            $usuario->setNome_completo('Isaque Marinho Ribeiro');
            $usuario->setSessao_bloqueada( false );
            $usuario->setUsuario_medico(false);
            $usuario->setEmail('isaque.ribeiro@outlook.com');
            $usuario->setData_cadastro('03/02/2016');
            $usuario->setSexo('M');
            
            $perfil->setCodigo('A');
            $perfil->setDescricao('Analista de Sistemas');
            
            $usuario->setTipo($perfil->getDescricao());
            $usuario->setPerfil($perfil);
            $usuario->setSenha($senha);
            
            $sess->set($key, $usuario);
            
            return true;
        } else {
            return false;
        }
    }

    public function esta_logado() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        return $sess->existe($key);
    }

    public function pegar_usuario() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            return $usuario;
        } else {
            return false;
        }
    }

    public function desbloquear_sessao() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            $usuario->setSessao_bloqueada(false);
            
            $sess->set($key, $usuario);
        }
    }

    public function bloquear_sessao() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            $usuario->setSessao_bloqueada(true);
            
            $sess->set($key, $usuario);
        }
    }
}

class AutenticadorEmBanco extends Autenticador {
    
    public function expulsar() {
        header('location: controle.php?ac=sair');
    }

    public function bloquear() {
        header('location: controle.php?ac=bloquear');
    }

    public function logar($login, $senha) {
        try {
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('sysdba', 'masterkey');

            $sql  = "Select ";
            $sql .= "    u.* ";
            $sql .= "  , cast(u.dh_cadastro as date) as dt_cadastro ";
            $sql .= "  , p.ds_perfil ";
            $sql .= "  , t.nm_tecnico ";
            $sql .= "  , t.nr_cpf ";
            $sql .= "from SYS_USUARIO u ";
            $sql .= "  inner join SYS_PERFIL p on (p.cd_perfil = u.cd_perfil) ";
            $sql .= "  left join TBTECNICO t on (t.id_tecnico = u.cd_tecnico) ";
            $sql .= "where (p.sn_ativo = 1) ";
            $sql .= "  and (u.sn_ativo = 1) ";
            $sql .= "  and lower(u.lg_usuario) = lower('{$login}') ";

            $res = $pdo->query($sql);
            $obj = $res->fetch(PDO::FETCH_OBJ);

            $pwdBase = decript($obj->pw_usuario);
            
            if ($senha === $pwdBase) {
                $usuario = new Usuario();
                $perfil  = new Perfil();
                
                // Buscar perfil no Hash de validação do registro
                $ds_hash   = decript($obj->ds_hash);
                $cd_perfil = str_replace($obj->id_usuario, "", $ds_hash);

                $perfil->setCodigo($cd_perfil); // $obj->cd_perfil);
                $perfil->setDescricao($obj->ds_perfil);

                $usuario->setId($obj->id_usuario);
                $usuario->setLogin(strtolower($obj->lg_usuario));
                $usuario->setSenha($senha);
                $usuario->setNome_completo(ucwords(strtolower( $obj->nm_usuario ))); 

                $data = date($obj->dt_cadastro);
                $data = explode('-', $data);
                
                $usuario->setSessao_bloqueada( false );
                $usuario->setData_cadastro($data[2] . '/' . $data[1] . '/' . $data[0]);
                $usuario->setSexo("M");
                $usuario->setTipo($perfil->getDescricao());
                $usuario->setPerfil($perfil);
                $usuario->setPerfilCodigo($cd_perfil); // $obj->cd_perfil);

                $sess = Sessao::getInstancia();
                $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $key  = 'user_dmweb';
                $sess->set($key, $usuario);

                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }  
    }

    public function esta_logado() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        return $sess->existe($key);
    }

    public function pegar_usuario() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            return $usuario;
        } else {
            return false;
        }
    }

    public function desbloquear_sessao() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            $usuario->setSessao_bloqueada(false);
            
            $sess->set($key, $usuario);
        }
    }

    public function bloquear_sessao() {
        $sess = Sessao::getInstancia();
        $keys = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $key  = 'user_dmweb';
        
        if ($this->esta_logado()) {
            $usuario = $sess->get($key);
            $usuario->setSessao_bloqueada(true);
            
            $sess->set($key, $usuario);
        }
    }
}

?>
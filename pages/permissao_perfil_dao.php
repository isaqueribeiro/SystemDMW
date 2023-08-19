<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//    $protocolo  = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
//    $host       = $_SERVER['HTTP_HOST'];
//    $script     = $_SERVER['SCRIPT_NAME'];
//    $parametros = $_SERVER['QUERY_STRING'];
//    $metodo     = $_SERVER['REQUEST_METHOD'];
//    $UrlAtual   = $protocolo . '://' . $host . $script . '?' . $parametros;
//
//    echo "<br>Protocolo: ".$protocolo;
//    echo "<br>Host: ".$host;
//    echo "<br>Script: ".$script;
//    echo "<br>Parametros: ".$parametros;
//    echo "<br>Metodo: ".$metodo;
//    echo "<br>Url: ".$UrlAtual."<br><br><br><br>";
//
    ini_set('display_errors', true);;
    error_reporting(E_ALL);
    
    require_once '../lib/Constantes.php';
    require_once '../lib/classes/usuario.php';
    require_once '../lib/classes/sessao.php';
    require_once '../lib/classes/autenticador.php';
    require_once '../lib/classes/configuracao.php';
    require_once '../lib/classes/dao.php';
    
    $aut = Autenticador::getInstancia();
    
    $usuario = null;
    if ($aut->esta_logado()) {
        $usuario = $aut->pegar_usuario();
        if ($usuario->getSessao_bloqueada()) {
            $aut->bloquear();
        }
    } else {
        $aut->expulsar();
    }
    
    $html = "";
    $home = new Constantes();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Verificar o Token de segurança
        $token = $_POST['token'];
                     
        if ( $token !== $usuario->getToken_id() ) {
            $funcao = new Constantes();
            echo $funcao->message_alert("TokenID de segurança inválido!");
            exit;
        }
            
        if (isset($_POST['ac'])) {
            switch ($_POST['ac']) {
                
                case 'carregar_permissao_perfil' : {
                    try {
                        $file = '../logs/perfil_permissao_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $cd_sistema = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_sistema']));
                        $cd_perfil  = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_perfil']));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db("sysdba", "masterkey");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   p.* ";
                        $sql .= "from SYS_PERFIL_PERMISSAO p ";
                        $sql .= "where p.cd_sistema = {$cd_sistema} ";
                        $sql .= "  and p.cd_perfil  = {$cd_perfil}  ";

                        $res = $pdo->query($sql);

                        $registros = array('permissao' => array());
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $registros['permissao'][0]['cd_sistema']   = $obj->cd_sistema;
                            $registros['permissao'][0]['cd_perfil']    = $obj->cd_perfil;
                            $registros['permissao'][0]['tx_permissao'] = $obj->tx_permissao;

                            echo "OK";
                        }

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                    
                case 'salvar_permissao_perfil' : {
                    try {
                        $cd_sistema   = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_sistema']));
                        $cd_perfil    = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_perfil']));
                        $tx_permissao = trim($_POST['tx_permissao']);
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db("sysdba", "masterkey");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   p.* ";
                        $sql .= "from SYS_PERFIL_PERMISSAO p ";
                        $sql .= "where p.cd_sistema = {$cd_sistema} ";
                        $sql .= "  and p.cd_perfil  = {$cd_perfil}  ";

                        $res = $pdo->query($sql);

                        // Gravar Dados (Update)
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $stm = $pdo->prepare(
                                  'Update SYS_PERFIL_PERMISSAO p Set  '
                                . '    p.tx_permissao = :tx_permissao '
                                . 'Where p.cd_sistema = :cd_sistema   '    
                                . '  and p.cd_perfil  = :cd_perfil    ');
                            $stm->execute(array(
                                ':tx_permissao' => $tx_permissao,
                                ':cd_sistema'   => $cd_sistema,
                                ':cd_perfil'    => $cd_perfil
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        // Gravando Dados (Insert)
                        } else {
                            $stm = $pdo->prepare(
                                  'Insert Into SYS_PERFIL_PERMISSAO ( '
                                . '    cd_sistema   '
                                . '  , cd_perfil    '
                                . '  , tx_permissao '
                                . ') values ( '
                                . '    :cd_sistema  '
                                . '  , :cd_perfil    '
                                . '  , :tx_permissao '
                                . ')');
                            $stm->execute(array(
                                ':cd_sistema'   => $cd_sistema,
                                ':cd_perfil'    => $cd_perfil,
                                ':tx_permissao' => $tx_permissao
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                    
                case 'sair' : {
                    header('location: principal.php');
                } break;
            }
        } else {
            echo "Erro ao tentar identificar ação!";
        }
    }


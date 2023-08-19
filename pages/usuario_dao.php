<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * Senha: ZDAzM2UyMmFlMzQ4YWViNTY2MGZjMjE0MGFlYzM1ODUwYzRkYTk5N1lXUnRhVzQ9 (padrão)
 *        WVdSZDAzM2UyMmFlMzQ4YWViNTY2MGZjMjE0MGFlYzM1ODUwYzRkYTk5N3RhVzQ9
 * 
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
    ini_set('display_errors', true);
    error_reporting(E_ALL);
    
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
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
                
                case 'encript' : {
                    echo encript($_POST['values']);
                } break;
            
                case 'decript' : {
                    echo decript($_POST['values']);
                } break;
            
                case 'alterar_senha' : {
                    try {
                        $id_usuario = $_POST['id_usuario'];
                        $pw_usuario = encript($_POST['pw_usuario']);
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    u.* ";
                        $sql .= "  , p.ds_perfil ";
                        $sql .= "from SYS_USUARIO u ";
                        $sql .= "  inner join SYS_PERFIL p on (p.cd_perfil = u.cd_perfil) ";
                        $sql .= "where (p.sn_ativo = 1) ";
                        $sql .= "  and (u.sn_ativo = 1) ";
                        $sql .= "  and (u.id_usuario = '{$id_usuario}') ";
                        
                        $res = $pdo->query($sql);
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $stm = $pdo->prepare('Update SYS_USUARIO Set pw_usuario = :pw_usuario Where id_usuario = :id_usuario');
                            $stm->execute(array(
                                ':pw_usuario' => $pw_usuario,
                                ':id_usuario' => $id_usuario
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        } else {
                            echo "Usuário e/ou senha inválidos!";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
            
                case 'pesquisar_usuario' : {
                    try {
                        $tipo_pesquisa = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['tipo_pesquisa']));                        
                        $pesquisa      = str_replace(" ", "%", trim(strtoupper($_POST['pesquisa'])));
                        
                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
                        $html .= "    <div class='box-header with-border'>";
                        $html .= "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
//                        $html .= "        <div class='box-tools pull-right'>";
//                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
//                        $html .= "        </div>";
                        $html .= "    </div>";
                        $html .= "    ";
                        $html .= "    <div class='box-body'>";
                        
                        $html .= "<a id='ancora_perfis'></a><table id='tb_usuarios' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Login</th>";
                        $html .= "        <th>Nome Completo</th>";
                        $html .= "        <th>E-mail</th>";
                        $html .= "        <th>Perfil</th>";
                        $html .= "        <th data-orderable='false'><center>Ativo</center></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";      // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    u.* ";
                        $sql .= "  , p.ds_perfil ";
                        $sql .= "from SYS_USUARIO u ";
                        $sql .= "  left join SYS_PERFIL p on (p.cd_perfil = u.cd_perfil) ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Apenas usuários ativos
                                $sql .= "  and (u.sn_ativo = 1) ";
                            } break;   

                            case 2: { // Filtrar por Login
                                $sql .= "  and (upper(u.lg_usuario) like '{$pesquisa}%') ";
                            } break;
                        
                            case 3: { // Filtrar por Nome
                                $sql .= "  and (upper(u.nm_usuario) like '{$pesquisa}%') ";
                            } break;   
                        } 
                            
                        $sql .= "order by ";
                        $sql .= "    u.lg_usuario ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $input      = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_usuario;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));
                            $cd_tecnico = $obj->cd_tecnico;
                            if (trim($cd_tecnico) === "") {
                                $cd_tecnico = getGuidEmpty();
                            }

                            $input  = "<input type='hidden' id='cell_id_usuario_{$referencia}'    value='{$obj->id_usuario}'/>"; 
                            $input .= "<input type='hidden' id='cell_lg_usuario_{$referencia}'    value='{$obj->lg_usuario}'/>"; 
                            $input .= "<input type='hidden' id='cell_pw_usuario_{$referencia}'    value='{$obj->pw_usuario}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_perfil_{$referencia}'     value='{$obj->cd_perfil}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_tecnico_{$referencia}'    value='{$cd_tecnico}'/>"; 
                            $input .= "<input type='hidden' id='cell_alterar_senha_{$referencia}' value='{$obj->sn_alterar_senha}'/>"; 
                            $input .= "<input type='hidden' id='cell_ativo_{$referencia}'         value='{$obj->sn_ativo}'/>"; 
                            
                            $excluir = "<a id='excluir_usuario_{$referencia}' href='javascript:preventDefault();' onclick='ExcluirUsuario( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->sn_ativo === 1) {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='usuario_{$referencia}' href='javascript:preventDefault();' onclick='EditarUsuario( this.id )'>  " . $obj->lg_usuario . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->nm_usuario . "</td>";
                            $html .= "        <td>" . ($obj->ds_email === ""?"...":$obj->ds_email) . "</td>";
                            $html .= "        <td>" . $obj->ds_perfil . "</td>";
                            $html .= "        <td align=center>" . $ativo  . "</td>";
                            $html .= "        <td align=center>" . $excluir  . "</td>";
                            $html .= "    </tr>";
                            
                            $num = $num + 1;
                        }
                        
                        $html .= "</tbody>";
                        $html .= "</table>";
                        
                        if ($num == 0) {
                            $funcao = new Constantes();
                            echo $funcao->message_alert("Não existem dados com os parâmetros de pesquisa informado!");
                            exit;
                        }
                        
                        $html .= "    </div>";
                        $html .= "</div>";
                        
                        echo $html;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . oci_error();
                    } 
                } break;
            
                case 'salvar_usuario' : {
                    try {
                        $file = '../logs/usuario_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $id_usuario = trim($_POST['id_usuario']);
                        $nm_usuario = strtoupper(trim($_POST['nm_usuario']));
                        $ds_email   = strtolower(trim($_POST['ds_email']));
                        $cd_perfil  = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_perfil']));
                        $lg_usuario = strtolower(trim($_POST['lg_usuario']));
                        $pw_usuario = trim($_POST['pw_usuario']);
                        $cd_tecnico = trim($_POST['cd_tecnico']);
                        $sn_alterar_senha = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['sn_alterar_senha']));
                        $sn_ativo         = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['sn_ativo']));
                        
                        if (estaEncript($pw_usuario) !== true ) {
                            $pw_usuario = encript($pw_usuario);
                        }
                        
                        if (($cd_tecnico === "") || ($cd_tecnico === getGuidEmpty())) {
                            $cd_tecnico = null;
                        }
                        
                        $ds_hash = "";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Verificar se o Login já está sendo utilizado por outro usuário
                        $sql  = "Select ";
                        $sql .= "   u.* ";
                        $sql .= "from SYS_USUARIO u ";
                        $sql .= "where (u.lg_usuario  = '{$lg_usuario}') ";
                        $sql .= "  and (u.id_usuario <> '{$id_usuario}') ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            echo "Login informado já utilizado por outro usuário!";
                        } else {
                            $sql  = "";
                            $sql .= "Select ";
                            $sql .= "   u.* ";
                            $sql .= "from SYS_USUARIO u ";
                            $sql .= "where (u.id_usuario = '{$id_usuario}') ";

                            $res = $pdo->query($sql);

                            // Gravar Dados (Update)
                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                $ds_hash = encript($id_usuario . $cd_perfil);
                                $stm = $pdo->prepare(
                                      'Update SYS_USUARIO u Set '
                                    . '    u.nm_usuario = :nm_usuario '
                                    . '  , u.ds_email   = :ds_email   '
                                    . '  , u.cd_perfil  = :cd_perfil  '
                                    . '  , u.lg_usuario = :lg_usuario '
                                    . '  , u.pw_usuario = :pw_usuario '
                                    . '  , u.cd_tecnico = :cd_tecnico '
                                    . '  , u.ds_hash    = :ds_hash    '
                                    . '  , u.sn_alterar_senha = :sn_alterar_senha '
                                    . '  , u.sn_ativo         = :sn_ativo         '
                                    . 'Where u.id_usuario = :id_usuario ');
                                $stm->execute(array(
                                    ':id_usuario' => $id_usuario,
                                    ':nm_usuario' => $nm_usuario,
                                    ':ds_email'   => $ds_email,
                                    ':cd_perfil'  => $cd_perfil,
                                    ':lg_usuario' => $lg_usuario,
                                    ':pw_usuario' => $pw_usuario,
                                    ':cd_tecnico' => $cd_tecnico,
                                    ':ds_hash'    => $ds_hash,
                                    ':sn_alterar_senha' => $sn_alterar_senha,
                                    ':sn_ativo'         => $sn_ativo
                                ));

                                $pdo->commit();
                                echo "OK";
                            // Gravando Dados (Insert)
                            } else {
                                $dao = Dao::getInstancia();
                                $id_usuario = $dao->getGuidIDFormat();
                                $ds_hash    = encript($id_usuario . $cd_perfil);                            

                                $stm = $pdo->prepare(
                                      'Insert Into SYS_USUARIO ( '
                                    . '    id_usuario  '
                                    . '  , nm_usuario  '
                                    . '  , ds_email    '
                                    . '  , cd_perfil   '
                                    . '  , lg_usuario  '
                                    . '  , pw_usuario  '
                                    . '  , cd_tecnico  '
                                    . '  , ds_hash     '
                                    . '  , dh_cadastro '
                                    . '  , sn_alterar_senha '
                                    . '  , sn_ativo         '
                                    . ') values ( '
                                    . '    :id_usuario '
                                    . '  , :nm_usuario '
                                    . '  , :ds_email   '
                                    . '  , :cd_perfil  '
                                    . '  , :lg_usuario '
                                    . '  , :pw_usuario '
                                    . '  , :cd_tecnico '
                                    . '  , :ds_hash    '
                                    . '  , current_timestamp '
                                    . '  , :sn_alterar_senha '
                                    . '  , :sn_ativo         '
                                    . ')');
                                $stm->execute(array(
                                    ':id_usuario' => $id_usuario,
                                    ':nm_usuario' => $nm_usuario,
                                    ':ds_email'   => $ds_email,
                                    ':cd_perfil'  => $cd_perfil,
                                    ':lg_usuario' => $lg_usuario,
                                    ':pw_usuario' => $pw_usuario,
                                    ':cd_tecnico' => $cd_tecnico,
                                    ':ds_hash'    => $ds_hash,
                                    ':sn_alterar_senha' => $sn_alterar_senha,
                                    ':sn_ativo'         => $sn_ativo
                                ));

                                $pdo->commit();
                                echo "OK";
                            }

                            $registros = array('formulario' => array());

                            $registros['formulario'][0]['id_usuario'] = $id_usuario;
                            $registros['formulario'][0]['pw_usuario'] = $pw_usuario;
                            $registros['formulario'][0]['ds_hash']    = $ds_hash;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_usuario' : {
                    try {
                        $id_usuario = trim($_POST['id_usuario']);
                        
                        if ($id_usuario === $usuario->getId()) {
                            echo "Usuário corrente não pode ser excluído!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stm = $pdo->prepare('Delete from SYS_USUARIO Where id_usuario = :id_usuario');
                            $stm->execute(array(
                                ':id_usuario' => $id_usuario
                            ));
                            $pdo->commit();

                            echo 'OK';
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
    
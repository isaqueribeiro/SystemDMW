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
                
                case 'pesquisar_tecnico' : {
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
                        
                        $html .= "<a id='ancora_perfis'></a><table id='tb_tecnicos' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>CPF</th>";
                        $html .= "        <th>Nome Completo</th>";
                        $html .= "        <th data-orderable='false'><center>Ativo</center></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";      // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    t.* ";
                        $sql .= "  , coalesce(count(u.id_usuario), 0) as nr_usuarios ";
                        $sql .= "from TBTECNICO t ";
                        $sql .= "  left join SYS_USUARIO u on (u.cd_tecnico = t.id_tecnico) ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Apenas técnicos ativos
                                $sql .= "  and (t.sn_ativo = 1) ";
                            } break;   

                            case 2: { // Filtrar por Nome
                                $sql .= "  and (upper(t.nm_tecnico) like '{$pesquisa}%') ";
                            } break;
                        
                            case 3: { // Filtrar por CFP
                                $sql .= "  and (upper(t.nr_cpf) like '{$pesquisa}%') ";
                            } break;   
                        } 
                            
                        
                        $sql .= "group by ";
                        $sql .= "    t.id_tecnico ";
                        $sql .= "  , t.nm_tecnico ";
                        $sql .= "  , t.nr_cpf ";
                        $sql .= "  , t.dh_cadastro ";
                        $sql .= "  , t.sn_ativo ";
                        $sql .= "order by ";
                        $sql .= "    t.nm_tecnico ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $input      = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_tecnico;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));

                            $input  = "<input type='hidden' id='cell_id_tecnico_{$referencia}'  value='{$obj->id_tecnico}'/>"; 
                            $input .= "<input type='hidden' id='cell_nr_cpf_{$referencia}'      value='" . formatarTexto('###.###.###-##', $obj->nr_cpf) . "'/>"; 
                            $input .= "<input type='hidden' id='cell_nr_usuarios_{$referencia}' value='{$obj->nr_usuarios}'/>"; 
                            $input .= "<input type='hidden' id='cell_ativo_{$referencia}'       value='{$obj->sn_ativo}'/>"; 
                            
                            $excluir = "<a id='excluir_tecnico_{$referencia}' href='javascript:preventDefault();' onclick='ExcluirTecnico( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->sn_ativo === 1) {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='tecnico_{$referencia}' href='javascript:preventDefault();' onclick='EditarTecnico( this.id )'>  " . formatarTexto('###.###.###-##', $obj->nr_cpf) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->nm_tecnico . "</td>";
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
            
                case 'salvar_tecnico' : {
                    try {
                        $file = '../logs/tecnico_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $id_tecnico = trim($_POST['id_tecnico']);
                        $nm_tecnico = trim($_POST['nm_tecnico']);
                        $nr_cpf     = preg_replace("/[^0-9]/", "", trim($_POST['nr_cpf']));
                        $sn_ativo   = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['sn_ativo']));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Verificar se o CPF já está sendo utilizado por outro técnico
                        $sql  = "Select ";
                        $sql .= "   t.* ";
                        $sql .= "from TBTECNICO t ";
                        $sql .= "where (t.nr_cpf = '{$nr_cpf}') ";
                        $sql .= "  and (t.id_tecnico <> '{$id_tecnico}') ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            echo "CPF informado já cadastrado!";
                        } else {
                            $sql  = "";
                            $sql .= "Select ";
                            $sql .= "   t.* ";
                            $sql .= "from TBTECNICO t ";
                            $sql .= "where (t.id_tecnico = '{$id_tecnico}') ";

                            $res = $pdo->query($sql);

                            // Gravar Dados (Update)
                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                $stm = $pdo->prepare(
                                      'Update TBTECNICO t Set '
                                    . '    t.nm_tecnico = :nm_tecnico '
                                    . '  , t.nr_cpf     = :nr_cpf     '
                                    . '  , t.sn_ativo   = :sn_ativo   '
                                    . 'Where t.id_tecnico = :id_tecnico ');
                                $stm->execute(array(
                                    ':id_tecnico' => $id_tecnico,
                                    ':nm_tecnico' => $nm_tecnico,
                                    ':nr_cpf'     => $nr_cpf,
                                    ':sn_ativo'   => $sn_ativo
                                ));

                                $pdo->commit();
                                echo "OK";
                            // Gravando Dados (Insert)
                            } else {
                                $dao = Dao::getInstancia();
                                $id_tecnico = $dao->getGuidIDFormat();

                                $stm = $pdo->prepare(
                                      'Insert Into TBTECNICO ( '
                                    . '    id_tecnico  '
                                    . '  , nm_tecnico  '
                                    . '  , nr_cpf      '
                                    . '  , dh_cadastro '
                                    . '  , sn_ativo         '
                                    . ') values ( '
                                    . '    :id_tecnico '
                                    . '  , :nm_tecnico '
                                    . '  , :nr_cpf     '
                                    . '  , current_timestamp '
                                    . '  , :sn_ativo         '
                                    . ')');
                                $stm->execute(array(
                                    ':id_tecnico' => $id_tecnico,
                                    ':nm_tecnico' => $nm_tecnico,
                                    ':nr_cpf'     => $nr_cpf,
                                    ':sn_ativo'   => $sn_ativo
                                ));

                                $pdo->commit();
                                echo "OK";
                            }

                            $registros = array('formulario' => array());

                            $registros['formulario'][0]['id_tecnico'] = $id_tecnico;
                            $registros['formulario'][0]['nm_tecnico'] = $nm_tecnico;
                            $registros['formulario'][0]['nr_cpf']     = $nr_cpf;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_tecnico' : {
                    try {
                        $id_tecnico = trim($_POST['id_tecnico']);
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from TBTECNICO Where id_tecnico = :id_tecnico');
                        $stm->execute(array(
                            ':id_tecnico' => $id_tecnico
                        ));
                        $pdo->commit();

                        echo 'OK';
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
    
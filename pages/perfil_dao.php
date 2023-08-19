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
                
                case 'pesquisar_perfil' : {
                    try {
                        $tipo_pesquisa = $_POST['tipo_pesquisa'];
                        $pesquisa      = str_replace(" ", "%", trim(strtoupper($_POST['pesquisa'])));
                        $cd_sistema    = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_sistema']));
                        
                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
                        $html .= "    <div class='box-header with-border'>";
                        $html .= "        <h3 class='box-title'><b>Registros Cadastrados</b></h3>";
//                        $html .= "        <div class='box-tools pull-right'>";
//                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
//                        $html .= "        </div>";
                        $html .= "    </div>";
                        $html .= "    ";
                        $html .= "    <div class='box-body'>";
                        
                        $html .= "<a id='ancora_perfis'></a><table id='tb_perfis' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Código</th>";
                        $html .= "        <th>Descrição</th>";
                        $html .= "        <th data-orderable='false'>Login's</th>";
                        $html .= "        <th data-orderable='false'>Acessos</th>";
                        $html .= "        <th data-orderable='false'>Ativo</th>"; // Desabilitar a ordenação nesta columa pelo jQuery.
                        $html .= "        <th data-orderable='false'></th>";      // Desabilitar a ordenação nesta columa pelo jQuery.
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "    p.cd_perfil ";
                        $sql .= "  , p.ds_perfil ";
                        $sql .= "  , p.sn_ativo ";
                        $sql .= "  , count(u.id_usuario) as qt_usuarios ";
                        $sql .= "  , s.tx_permissao ";
                        $sql .= "from SYS_PERFIL p ";
                        $sql .= "  left join SYS_PERFIL_PERMISSAO s on (s.cd_sistema = {$cd_sistema} and s.cd_perfil = p.cd_perfil) ";
                        $sql .= "  left join SYS_USUARIO u on (u.cd_perfil = p.cd_perfil) ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Todos os perfis ativos
                                $sql .= "  and (p.sn_ativo = 1) ";
                            } break;   

                            case 2: { // Filtrar por descrição
                                $sql .= "  and (upper(p.ds_perfil) like '{$pesquisa}%') ";
                            } break;   
                        }
                            
                        $sql .= "group by ";
                        $sql .= "    p.cd_perfil ";
                        $sql .= "  , p.ds_perfil ";
                        $sql .= "  , p.sn_ativo ";
                        $sql .= "  , s.tx_permissao ";
                        
                        $num = 0;
                        $res = $pdo->query($sql);

                        $codigo = "";
                        
                        while ((($obj = $res->fetch(PDO::FETCH_OBJ))) !== false) {
                            $codigo   = (int)$obj->cd_perfil;
                            $input_q  = "<input type='hidden' id='cell_usuarios_{$codigo}'  value='{$obj->qt_usuarios}'/>";  // Guardar campo oculto com valor dentro da célula
                            $input_p  = "<input type='hidden' id='cell_permissao_{$codigo}' value='{$obj->tx_permissao}'/>"; // Guardar campo oculto com valor dentro da célula
                            $input_a  = "<input type='hidden' id='cell_ativo_{$codigo}'     value='{$obj->sn_ativo}'/>";     // Guardar campo oculto com valor dentro da célula
                            $input    = $input_q . $input_p . $input_a;
                            $excluir = "<a id='excluir_perfil_{$codigo}' href='javascript:preventDefault();' onclick='ExcluirPerfil( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->sn_ativo === 1) {
                                $ativo = "<i id='img_ativo_{$codigo}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $ativo = "<i id='img_ativo_{$codigo}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            if (trim($obj->tx_permissao) === "") {
                                $acesso = "<a id='configurar_perfil_{$codigo}' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_{$codigo}' class='fa fa-cog' title='Configurar acessos do Perfil'>&nbsp;</i>";
                            } else {
                                $acesso = "<a id='configurar_perfil_{$codigo}' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_{$codigo}' class='fa fa-cogs' title='Configurar acessos do Perfil'>&nbsp;</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$codigo}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='perfil_{$codigo}' href='javascript:preventDefault();' onclick='EditarPerfil( this.id )'>  " . str_pad($codigo, 3, "0", STR_PAD_LEFT) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->ds_perfil . "</td>";
                            $html .= "        <td align=right>" . $obj->qt_usuarios . "</td>";
                            $html .= "        <td align=center>" . $acesso . "</td>";
                            $html .= "        <td align=center>" . $ativo . "</td>";
                            $html .= "        <td align=center>" . $excluir . "</td>";
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
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
            
                case 'salvar_perfil' : {
                    try {
                        $file = '../logs/perfil_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $cd_perfil = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_perfil']));
                        $ds_perfil = trim($_POST['ds_perfil']);
                        $sn_ativo  = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['sn_ativo']));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   p.* ";
                        $sql .= "from SYS_PERFIL p ";
                        $sql .= "where (p.cd_perfil = {$cd_perfil}) ";

                        $res = $pdo->query($sql);

                        // Gravar Dados (Update)
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $stm = $pdo->prepare(
                                  'Update SYS_PERFIL p Set '
                                . '    p.ds_perfil   = :ds_perfil '
                                . '  , p.sn_ativo    = :sn_ativo  '
                                . 'Where p.cd_perfil = :cd_perfil ');
                            $stm->execute(array(
                                ':ds_perfil' => $ds_perfil,
                                ':sn_ativo'  => $sn_ativo,
                                ':cd_perfil' => $cd_perfil
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        // Gravando Dados (Insert)
                        } else {
                            $dao = Dao::getInstancia();
                            $cd_perfil = $dao->getCountID("SYS_PERFIL", "cd_perfil");
                            
                            $stm = $pdo->prepare(
                                  'Insert Into SYS_PERFIL ( '
                                . '    cd_perfil '
                                . '  , ds_perfil '
                                . '  , sn_ativo  '
                                . ') values ( '
                                . '    :cd_perfil '
                                . '  , :ds_perfil '
                                . '  , :sn_ativo  '
                                . ')');
                            $stm->execute(array(
                                ':cd_perfil' => $cd_perfil,
                                ':ds_perfil' => $ds_perfil,
                                ':sn_ativo'  => $sn_ativo
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        }

                        $registros = array('formulario' => array());

                        $registros['formulario'][0]['cd_perfil'] = $cd_perfil;
                        $registros['formulario'][0]['ds_perfil'] = $ds_perfil;
                        $registros['formulario'][0]['sn_ativo']  = $sn_ativo;

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                  
                case 'excluir_perfil' : {
                    try {
                        $cd_perfil = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_perfil']));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from SYS_PERFIL Where cd_perfil = :cd_perfil');
                        $stm->execute(array(
                            ':cd_perfil' => $cd_perfil
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
    
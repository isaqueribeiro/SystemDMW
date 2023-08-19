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
                
                case 'pesquisar_cnae' : {
                    try {
                        $tipo_pesquisa = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tipo_pesquisa')));
                        $pesquisa      = str_replace(" ", "%", trim(strtoupper(filter_input(INPUT_POST, 'pesquisa'))));
                        
                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
                        $html .= "    <div class='box-header with-border'>";
                        $html .= "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
//                        $html .= "        <div class='box-tools pull-right'>";
//                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
//                        $html .= "        </div>";
                        $html .= "    </div>";
                        $html .= "    ";
                        $html .= "    <div class='box-body'>";
                        
                        $html .= "<a id='ancora_cnaes'></a><table id='tb_cnaes' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Código</th>";
                        $html .= "        <th>Descrição</th>";
                        $html .= "        <th>Seção</th>";
                        $html .= "        <th data-orderable='false'><center>IE</center></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";                    // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    c.* ";
                        $sql .= "  , s.ds_secao ";
                        $sql .= "  , (Select count(e.id_estabelecimento) from TBESTABELECIMENTO e where e.cd_cnae_principal = c.cd_cnae) as qt_estabelecimento ";
                        $sql .= "from TBCNAE c ";
                        $sql .= "  left join SYS_CNAE_SECAO s on (s.cd_secao = c.tp_secao) ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Apenas CNAE's com obrigatoriedade de IE
                                $sql .= "  and (c.sn_obriga_insc_est = 1) ";
                            } break;   

                            case 2: { // Descrição
                                $sql .= "  and (upper(c.ds_cnae) like '{$pesquisa}%') ";
                            } break;

                            case 3: { // Seção
                                $sql .= "  and (c.tp_secao = '{$pesquisa}') ";
                            } break;
                        } 
                            
                        
                        $sql .= "order by ";
                        $sql .= "    c.ds_cnae ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $input      = "";
                        $obriga_ie  = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->cd_cnae;

                            $input  = "<input type='hidden' id='cell_tp_secao_{$referencia}' value='{$obj->tp_secao}'/>"; 
                            $input .= "<input type='hidden' id='cell_nm_cnae_{$referencia}'  value='{$obj->nm_cnae}'/>"; 
                            $input .= "<input type='hidden' id='cell_qt_estabelecimento_{$referencia}'  value='{$obj->qt_estabelecimento}'/>"; 
                            $input .= "<input type='hidden' id='cell_sn_obriga_insc_est_{$referencia}' value='{$obj->sn_obriga_insc_est}'/>"; 
                            
                            $excluir = "<a id='excluir_cnae_{$referencia}' href='javascript:preventDefault();' onclick='ExcluirCnae( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->sn_obriga_insc_est === 1) {
                                $obriga_ie = "<i id='img_obriga_ie_{$referencia}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $obriga_ie = "<i id='img_obriga_ie_{$referencia}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='cnae_{$referencia}' href='javascript:preventDefault();' onclick='EditarCnae( this.id )'>  " . $obj->cd_cnae . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->ds_cnae . "</td>";
                            $html .= "        <td>" . $obj->tp_secao . " - "  . $obj->ds_secao . "</td>";
                            $html .= "        <td align=center>" . $obriga_ie  . "</td>";
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
            
                case 'salvar_cnae' : {
                    try {
                        $file = '../logs/cnae_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $operacao = trim(filter_input(INPUT_POST, 'operacao'));
                        $cd_cnae  = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cd_cnae')));
                        $ds_cnae  = trim(filter_input(INPUT_POST, 'ds_cnae'));
                        $tp_secao = trim(filter_input(INPUT_POST, 'tp_secao'));
                        $nm_cnae  = strtoupper(trim(filter_input(INPUT_POST, 'nm_cnae')));
                        $sn_obriga_insc_est = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sn_obriga_insc_est')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        if ($cd_cnae === "") {
                            $dao = Dao::getInstancia();
                            $cd_cnae = $dao->getCountID("TBCNAE", "cast(cd_cnae as bigint)");
                            $ds_cnae = strtoupper($ds_cnae);
                        }
                        
                        $sql  = "Select ";
                        $sql .= "   c.* ";
                        $sql .= "from TBCNAE c ";
                        $sql .= "where (c.cd_cnae = '{$cd_cnae}') ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            // Verificar se o Código Cnae já está sendo utilizado por outro registro
                            if ($operacao === "inserir") {
                                echo "O código CNAE informado já está sendo utilizado na atividade <strong>{$err->ds_cnae}</strong>.";
                                exit;
                            } else {
                                $stm = $pdo->prepare(
                                      'Update TBCNAE c Set '
                                    . '    c.ds_cnae  = :ds_cnae  '
                                    . '  , c.tp_secao = :tp_secao '
                                    . '  , c.nm_cnae  = :nm_cnae  '
                                    . '  , c.sn_obriga_insc_est = :sn_obriga_insc_est '
                                    . 'Where c.cd_cnae = :cd_cnae ');
                                $stm->execute(array(
                                    ':cd_cnae'  => $cd_cnae,
                                    ':ds_cnae'  => $ds_cnae,
                                    ':tp_secao' => $tp_secao,
                                    ':nm_cnae'  => $nm_cnae,
                                    ':sn_obriga_insc_est' => $sn_obriga_insc_est
                                ));
                                
                                $pdo->commit();
                                echo "OK";
                            }
                        } else {
                            $stm = $pdo->prepare(
                                  'Insert Into TBCNAE ( '
                                . '    cd_cnae  '
                                . '  , ds_cnae  '
                                . '  , tp_secao '
                                . '  , nm_cnae  '
                                . '  , sn_obriga_insc_est '
                                . ') values ( '
                                . '    :cd_cnae  '
                                . '  , :ds_cnae  '
                                . '  , :tp_secao '
                                . '  , :nm_cnae  '
                                . '  , :sn_obriga_insc_est '
                                . ')');
                            $stm->execute(array(
                                ':cd_cnae'  => $cd_cnae,
                                ':ds_cnae'  => $ds_cnae,
                                ':tp_secao' => $tp_secao,
                                ':nm_cnae'  => $nm_cnae,
                                ':sn_obriga_insc_est' => $sn_obriga_insc_est
                            ));

                            $pdo->commit();
                            echo "OK";
                        }
                        
                        $registros = array('formulario' => array());

                        $registros['formulario'][0]['cd_cnae']  = $cd_cnae;
                        $registros['formulario'][0]['ds_cnae']  = $ds_cnae;
                        $registros['formulario'][0]['tp_secao'] = $tp_secao;
                        $registros['formulario'][0]['nm_cnae']  = $nm_cnae;
                        $registros['formulario'][0]['sn_obriga_insc_est'] = $sn_obriga_insc_est;

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_cnae' : {
                    try {
                        $cd_cnae  = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cd_cnae')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from TBCNAE Where cd_cnae = :cd_cnae');
                        $stm->execute(array(
                            ':cd_cnae' => $cd_cnae
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
    
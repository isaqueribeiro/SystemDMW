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
                
                case 'pesquisar_categoria_licenca' : {
//                    try {
//                        $cd_estado     = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['estado_pesquisa']));
//                        $cd_cidade     = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cidade_pesquisa']));
//                        $tipo_pesquisa = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['tipo_pesquisa']));
//                        $pesquisa      = str_replace(" ", "%", trim(strtoupper($_POST['pesquisa'])));
//                        
//                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
//                        $html .= "    <div class='box-header with-border'>";
//                        $html .= "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
////                        $html .= "        <div class='box-tools pull-right'>";
////                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
////                        $html .= "        </div>";
//                        $html .= "    </div>";
//                        $html .= "    ";
//                        $html .= "    <div class='box-body'>";
//                        
//                        $html .= "<a id='ancora_perfis'></a><table id='tb_bairros' class='table table-bordered table-hover'  width='100%'>";
//
//                        $html .= "<thead>";
//                        $html .= "    <tr>";
//                        $html .= "        <th>Código</th>";
//                        $html .= "        <th>Descrição</th>";
//                        $html .= "        <th data-orderable='false'>Estab.</th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
//                        $html .= "        <th data-orderable='false'></th>";       // Desabilitar a ordenação nesta columa pelo jQuery. 
//                        $html .= "    </tr>";
//                        $html .= "</thead>";
//                        $html .= "<tbody>";
//
//                        $cnf = Configuracao::getInstancia();
//                        $pdo = $cnf->db('', '');
//                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                        
//                        $sql  = "Select ";
//                        $sql .= "    b.* ";
//                        $sql .= "  , c.cd_estado ";
//                        $sql .= "  , coalesce(count(e.id_estabelecimento), 0)  as qt_estabelecimento ";
//                        $sql .= "from SYS_BAIRROS b ";
//                        $sql .= "  left join SYS_CIDADES c on (c.cd_cidade = b.cd_cidade) ";
//                        $sql .= "  left join TBESTABELECIMENTO e on (e.cd_bairro = b.cd_bairro) ";
//                        $sql .= "where (1 = 1) ";
//                        
//                        if ($cd_estado !== 0) $sql .= "  and (c.cd_estado = {$cd_estado}) ";
//                        if ($cd_cidade !== 0) $sql .= "  and (c.cd_cidade = {$cd_cidade}) ";
//                        
//                        switch ($tipo_pesquisa) {
//                            case 1: { // Filtrar por Nome
//                                $sql .= "  and (upper(b.nm_bairro) like '{$pesquisa}%') ";
//                            } break;
//                        } 
//                            
//                        $sql .= "group by ";
//                        $sql .= "    b.cd_bairro ";
//                        $sql .= "  , b.nm_bairro ";
//                        $sql .= "  , b.cd_cidade ";
//                        $sql .= "  , c.cd_estado ";
//                        $sql .= "order by ";
//                        $sql .= "    b.nm_bairro ";
//
//                        $num = 0;
//                        $res = $pdo->query($sql);
//                        
//                        $codigo = "";
//                        $input  = "";
//                        
//                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
//                            $codigo = (int)$obj->cd_bairro;
//
//                            $input  = "<input type='hidden' id='cell_cd_bairro_{$codigo}'  value='{$obj->cd_bairro}'/>"; 
//                            $input .= "<input type='hidden' id='cell_cd_estado_{$codigo}'  value='{$obj->cd_estado}'/>"; 
//                            $input .= "<input type='hidden' id='cell_cd_cidade_{$codigo}'  value='{$obj->cd_cidade}'/>"; 
//                            $input .= "<input type='hidden' id='cell_qt_estabelecimento_{$codigo}' value='{$obj->qt_estabelecimento}'/>"; 
//                            
//                            $excluir = "<a id='excluir_bairro_{$codigo}' href='javascript:preventDefault();' onclick='ExcluirBairro( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
//                            
//                            $html .= "    <tr id='linha_{$codigo}'>"; // Para identificar a linha da tabela
//                            $html .= "        <td><a id='bairro_{$codigo}' href='javascript:preventDefault();' onclick='EditarBairro( this.id )'>  " . str_pad($codigo, 5, "0", STR_PAD_LEFT) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
//                            $html .= "        <td>" . $obj->nm_bairro . "</td>";
//                            $html .= "        <td align=right>" . $obj->qt_estabelecimento . "</td>";
//                            $html .= "        <td align=center>" . $input . $excluir  . "</td>";
//                            $html .= "    </tr>";
//                            
//                            $num = $num + 1;
//                        }
//                        
//                        $html .= "</tbody>";
//                        $html .= "</table>";
//                        
//                        if ($num == 0) {
//                            $funcao = new Constantes();
//                            echo $funcao->message_alert("Não existem dados com os parâmetros de pesquisa informado!");
//                            exit;
//                        }
//                        
//                        $html .= "    </div>";
//                        $html .= "</div>";
//                        
//                        echo $html;
//                    } catch (Exception $ex) {
//                        echo $ex . "<br><br>" . oci_error();
//                    } 
                } break;
            
                case 'salvar_categoria_licenca' : {
//                    try {
//                        $file = '../logs/bairro_' . $usuario->getToken_id() . '.json';
//                        if (file_exists($file)) {
//                            unlink($file);
//                        }
//                        
//                        $cd_bairro = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_bairro']));
//                        $nm_bairro = trim(strtoupper($_POST['nm_bairro']));
//                        $cd_cidade = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_cidade']));
//                        
//                        $cnf = Configuracao::getInstancia();
//                        $pdo = $cnf->db('', '');
//                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//                        // Verificar se o NOME já está sendo utilizado por outro bairro na mesma cidade
//                        $sql  = "Select ";
//                        $sql .= "   b.* ";
//                        $sql .= "from SYS_BAIRROS b ";
//                        $sql .= "where (b.nm_bairro  = '{$nm_bairro}') ";
//                        $sql .= "  and (b.cd_cidade  = '{$cd_cidade}') ";
//                        $sql .= "  and (b.cd_bairro <> '{$cd_bairro}') ";
//
//                        $ret = $pdo->query($sql);
//                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
//                            echo "Descrição de Bairro informado já cadastrada!";
//                        } else {
//                            $sql  = "";
//                            $sql .= "Select ";
//                            $sql .= "   b.* ";
//                            $sql .= "from SYS_BAIRROS b ";
//                            $sql .= "where (b.cd_bairro = '{$cd_bairro}') ";
//
//                            $res = $pdo->query($sql);
//
//                            // Gravar Dados (Update)
//                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
//                                $stm = $pdo->prepare(
//                                      'Update SYS_BAIRROS b Set '
//                                    . '    b.nm_bairro = :nm_bairro '
//                                    . '  , b.cd_cidade = :cd_cidade '
//                                    . 'Where b.cd_bairro = :cd_bairro ');
//                                $stm->execute(array(
//                                    ':cd_bairro' => $cd_bairro,
//                                    ':nm_bairro' => $nm_bairro,
//                                    ':cd_cidade' => $cd_cidade
//                                ));
//
//                                $pdo->commit();
//                                echo "OK";
//                            // Gravando Dados (Insert)
//                            } else {
//                                $dao = Dao::getInstancia();
//                                $cd_bairro = $dao->getGeneratorID('GEN_BAIRROS_ID');
//
//                                $stm = $pdo->prepare(
//                                      'Insert Into SYS_BAIRROS ( '
//                                    . '    cd_bairro '
//                                    . '  , nm_bairro '
//                                    . '  , cd_cidade '
//                                    . ') values ( '
//                                    . '    :cd_bairro '
//                                    . '  , :nm_bairro '
//                                    . '  , :cd_cidade '
//                                    . ')');
//                                $stm->execute(array(
//                                    ':cd_bairro' => $cd_bairro,
//                                    ':nm_bairro' => $nm_bairro,
//                                    ':cd_cidade' => $cd_cidade
//                                ));
//
//                                $pdo->commit();
//                                echo "OK";
//                            }
//
//                            $registros = array('formulario' => array());
//
//                            $registros['formulario'][0]['cd_bairro'] = $cd_bairro;
//                            $registros['formulario'][0]['nm_bairro'] = $nm_bairro;
//                            $registros['formulario'][0]['cd_cidade'] = $cd_cidade;
//
//                            $json = json_encode($registros);
//                            file_put_contents($file, $json);
//                        }
//                    } catch (Exception $ex) {
//                        echo $ex . "<br><br>" . $ex->getMessage();
//                    } 
//                    
                } break;
            
                case 'excluir_categoria_licenca' : {
//                    try {
//                        $cd_bairro = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_bairro']));
//                        
//                        $cnf = Configuracao::getInstancia();
//                        $pdo = $cnf->db('', '');
//                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//                        $stm = $pdo->prepare('Delete from SYS_BAIRROS Where cd_bairro = :cd_bairro');
//                        $stm->execute(array(
//                            ':cd_bairro' => $cd_bairro
//                        ));
//                        
//                        $pdo->commit();
//                        echo 'OK';
//                    } catch (Exception $ex) {
//                        echo $ex . "<br><br>" . $ex->getMessage();
//                    } 
                } break;
                    
                case 'validade_categoria_licenca' : {
                    try {
                        $file = '../logs/categoria_licenca_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $nr_exercicio = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'nr_exercicio')));
                        $dt_emissao   = explode("/", trim(filter_input(INPUT_POST, 'dt_emissao')));
                        $cd_categoria = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_categoria')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    c.cd_categoria  ";
                        $sql .= "  , c.ds_categoria  ";
                        $sql .= "  , coalesce(c.dt_vencimento, current_date + 30)        as dt_vencimento ";
                        $sql .= "  , case when c.dt_vencimento is null then 1 else 0 end as sn_provisoria ";
                        $sql .= "from TBCATEGORIA_LICENCA c ";
                        $sql .= "where (c.cd_categoria = {$cd_categoria}) ";
                        
                        $res = $pdo->query($sql);
                        
                        $registros = array('categoria' => array());
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $dt_retorno    = date("d/m/Y");
                            $dt_documento  = new DateTime($dt_emissao[2] . "-" . $dt_emissao[1] . "-" . $dt_emissao[0]);
                            $dt_validade   = new DateTime($obj->dt_vencimento);
                            $dt_vencimento = explode("-", $obj->dt_vencimento);
                            $sn_provisoria = $obj->sn_provisoria;
                            
                            if ( $dt_documento < $dt_validade ) {
                                $dt_retorno = $dt_vencimento[2] . "/" . $dt_vencimento[1] . "/" . $dt_vencimento[0];
                            } else {
                                $dt_retorno = $dt_vencimento[2] . "/" . $dt_vencimento[1] . "/" . $nr_exercicio;
                            }
                            
                            $registros['categoria'][0]['cd_categoria']  = $obj->cd_categoria;
                            $registros['categoria'][0]['ds_categoria']  = $obj->ds_categoria;
                            $registros['categoria'][0]['dt_vencimento'] = $dt_retorno;
                            $registros['categoria'][0]['sn_provisoria'] = $sn_provisoria;
                        }

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                        echo "OK";
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
    
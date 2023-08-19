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
                
                case 'pesquisar_estado' : {
                    try {
                        $tipo_pesquisa = $_POST['tipo_pesquisa'];
                        $pesquisa      = str_replace(" ", "%", trim(strtoupper($_POST['pesquisa'])));
                        
                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
                        $html .= "    <div class='box-header with-border'>";
                        $html .= "        <h3 class='box-title'><b>Registros Cadastrados</b></h3>";
//                        $html .= "        <div class='box-tools pull-right'>";
//                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
//                        $html .= "        </div>";
                        $html .= "    </div>";
                        $html .= "    ";
                        $html .= "    <div class='box-body'>";
                        
                        $html .= "<a id='ancora_estados'></a><table id='tb_estados' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Código</th>";
                        $html .= "        <th>Nome</th>";
                        $html .= "        <th>UF</th>";
                        $html .= "        <th data-orderable='false'>Siafi</th>";
                        $html .= "        <th data-orderable='false'>Municípios</th>"; // Desabilitar a ordenação nesta columa pelo jQuery.
                        $html .= "        <th data-orderable='false'>Icms</th>";     // Desabilitar a ordenação nesta columa pelo jQuery.
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "    e.cd_estado ";
                        $sql .= "  , e.nm_estado ";
                        $sql .= "  , e.uf_estado ";
                        $sql .= "  , coalesce(e.cd_siafi, 0) as cd_siafi ";
                        $sql .= "  , coalesce(e.pc_aliquota_icms, 0.0) as pc_aliquota_icms ";
                        $sql .= "  , (Select count(c.cd_cidade) from SYS_CIDADES c where c.cd_estado = e.cd_estado) as nr_municipio ";
                        $sql .= "from SYS_ESTADOS e ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Filtrar por Código
                                $pesquisa = (int)"0" . $pesquisa;
                                $sql .= "  and (e.cd_estado = {$pesquisa}) ";
                            } break;   

                            case 2: { // Filtrar por descrição
                                $sql .= "  and (upper(e.nm_estado) like '{$pesquisa}%') ";
                            } break;   
                        }
                            
                        $num = 0;
                        $res = $pdo->query($sql);

                        $codigo = "";
                        
                        while ((($obj = $res->fetch(PDO::FETCH_OBJ))) !== false) {
                            $codigo   = (int)$obj->cd_estado;
                            
                            $html .= "    <tr id='linha_{$codigo}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='estado_{$codigo}' href='javascript:preventDefault();' onclick='EditarEstado( this.id )'>  " . str_pad($codigo, 3, "0", STR_PAD_LEFT) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->nm_estado . "</td>";
                            $html .= "        <td>" . $obj->uf_estado . "</td>";
                            $html .= "        <td align=right>" . $obj->cd_siafi . "</td>";
                            $html .= "        <td align=right>" . $obj->nr_municipio . "</td>";
                            $html .= "        <td align=right>" . number_format($obj->pc_aliquota_icms, 2, ",", ".") . "</td>";
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
            
                case 'salvar_estado' : {
                    try {
                        $file = '../logs/estado_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $cd_estado = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_estado']));
                        $nm_estado = trim($_POST['nm_estado']);
                        $uf_estado = trim(strtoupper($_POST['uf_estado']));
                        $cd_siafi  = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_siafi']));
                        $pc_aliquota_icms = floatval(preg_replace("/[^0-9]/", "", "0".trim($_POST['pc_aliquota_icms'])));
                        
                        if ($cd_siafi === 0) $cd_siafi = null;
                        if ($pc_aliquota_icms === 0.0) $pc_aliquota_icms = null;
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   e.* ";
                        $sql .= "from SYS_ESTADOS e ";
                        $sql .= "where (e.cd_estado = {$cd_estado}) ";

                        $res = $pdo->query($sql);

                        // Gravar Dados (Update)
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $stm = $pdo->prepare(
                                  'Update SYS_ESTADOS e Set '
                                . '    e.nm_estado   = :nm_estado '
                                . '  , e.uf_estado   = :uf_estado  '
                                . '  , e.cd_siafi    = :cd_siafi  '
                                . '  , e.pc_aliquota_icms = :pc_aliquota_icms  '
                                . 'Where e.cd_estado = :cd_estado ');
                            $stm->execute(array(
                                ':cd_estado'        => $cd_estado,
                                ':nm_estado'        => $nm_estado,
                                ':uf_estado'        => $uf_estado,
                                ':cd_siafi'         => $cd_siafi,
                                ':pc_aliquota_icms' => $pc_aliquota_icms
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        // Gravando Dados (Insert)
                        } else {
                            $dao = Dao::getInstancia();
                            
                            $stm = $pdo->prepare(
                                  'Insert Into SYS_ESTADOS ( '
                                . '    cd_estado '
                                . '  , nm_estado '
                                . '  , uf_estado '
                                . '  , cd_siafi  '
                                . '  , pc_aliquota_icms '
                                . ') values ( '
                                . '    :cd_estado '
                                . '  , :nm_estado '
                                . '  , :uf_estado '
                                . '  , :cd_siafi  '
                                . '  , :pc_aliquota_icms '
                                . ')');
                            $stm->execute(array(
                                ':cd_estado'        => $cd_estado,
                                ':nm_estado'        => $nm_estado,
                                ':uf_estado'        => $uf_estado,
                                ':cd_siafi'         => $cd_siafi,
                                ':pc_aliquota_icms' => $pc_aliquota_icms
                            ));
                            
                            $pdo->commit();
                            echo "OK";
                        }

                        if ($cd_siafi === null) $cd_siafi = 0;
                        if ($pc_aliquota_icms === null) $pc_aliquota_icms = 0.0;
                        
                        $registros = array('formulario' => array());

                        $registros['formulario'][0]['cd_estado'] = $cd_estado;
                        $registros['formulario'][0]['nm_estado'] = $nm_estado;
                        $registros['formulario'][0]['uf_estado'] = $uf_estado;
                        $registros['formulario'][0]['cd_siafi']  = $cd_siafi;
                        $registros['formulario'][0]['pc_aliquota_icms'] = number_format($pc_aliquota_icms, 2, ",", ".");

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                  
                case 'excluir_estado' : {
                    try {
                        $cd_estado = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cd_estado']));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from SYS_ESTADOS Where cd_estado = :cd_estado');
                        $stm->execute(array(
                            ':cd_estado' => $cd_estado
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
    
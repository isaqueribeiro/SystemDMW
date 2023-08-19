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
                
                case 'pesquisar_cep' : {
                    try {
                        $cd_estado = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['estado_pesquisa']));
                        $cd_cidade = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['cidade_pesquisa']));
                        $cd_bairro = (int)preg_replace("/[^0-9]/", "", "0".trim($_POST['bairro_pesquisa']));
                        $pesquisa  = str_replace(" ", "%", trim(strtoupper($_POST['pesquisa'])));
                        
                        $html .= "<div class='box box-info' id='box_resultado_pesquisa'>";
                        $html .= "    <div class='box-header with-border'>";
                        $html .= "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
//                        $html .= "        <div class='box-tools pull-right'>";
//                        $html .= "            <button type='button' class='btn btn-box-tool' data-widget='collapse' id='btn_resultado_pesquisa_fa'><i class='fa fa-minus'></i>";
//                        $html .= "        </div>";
                        $html .= "    </div>";
                        $html .= "    ";
                        $html .= "    <div class='box-body'>";
                        
                        $html .= "<a id='ancora_perfis'></a><table id='tb_ceps' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Cep</th>";
                        $html .= "        <th>Endereço</th>";
                        $html .= "        <th>Bairro</th>";
                        $html .= "        <th>Município</th>";
                        $html .= "        <th data-orderable='false'></th>";       // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    c.* ";
                        $sql .= "from SYS_CEP c ";
                        $sql .= "where (1 = 1) ";
                        
                        if ($cd_estado !== 0) $sql .= "  and (c.cd_estado = {$cd_estado}) ";
                        if ($cd_cidade !== 0) $sql .= "  and (c.cd_cidade = {$cd_cidade}) ";
                        if ($cd_bairro !== 0) $sql .= "  and (c.cd_bairro = {$cd_bairro}) ";
                        if ($pesquisa !== "") $sql .= "  and (upper(trim(c.ds_endereco)) like '%{$pesquisa}%') ";
                            
                        $sql .= "order by ";
                        $sql .= "    trim(c.ds_endereco) ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $codigo = "";
                        $input  = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $codigo = $obj->nr_cep;

                            $input  = "<input type='hidden' id='cell_nr_cep_{$codigo}'        value='" . formatarTexto('##.###-###', $obj->nr_cep) . "'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_estado_{$codigo}'     value='{$obj->cd_estado}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_cidade_{$codigo}'     value='{$obj->cd_cidade}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_bairro_{$codigo}'     value='{$obj->cd_bairro}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_tipo_{$codigo}'       value='{$obj->cd_tipo}'/>"; 
                            $input .= "<input type='hidden' id='cell_ds_logradouro_{$codigo}' value='{$obj->ds_logradouro}'/>"; 
                            
                            $excluir = "<a id='excluir_cep_{$codigo}' href='javascript:preventDefault();' onclick='ExcluirCep( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            $html .= "    <tr id='linha_{$codigo}'>"; // Para identificar a linha da tabela
                            $html .= "        <td><a id='cep_{$codigo}' href='javascript:preventDefault();' onclick='EditarCep( this.id )'>  " . formatarTexto('##.###-###', $obj->nr_cep) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></td>";
                            $html .= "        <td>" . $obj->ds_endereco . "</td>";
                            $html .= "        <td>" . $obj->nm_bairro . "</td>";
                            $html .= "        <td>" . $obj->nm_cidade . " (" . $obj->uf . ")" . "</td>";
                            $html .= "        <td align=center>" . $input . $excluir  . "</td>";
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
            
                case 'buscar_endereco_cep' : {
                    try {
                        $file = '../logs/endereco_cep_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $nr_cep = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'nr_cep')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    c.nr_cep ";
                        $sql .= "  , c.cd_estado ";
                        $sql .= "  , c.cd_cidade ";
                        $sql .= "  , c.nm_cidade ";
                        $sql .= "  , c.cd_bairro ";
                        $sql .= "  , c.nm_bairro ";
                        $sql .= "  , c.cd_tipo   ";
                        $sql .= "  , t.cd_descricao as ds_tipo ";
                        $sql .= "  , c.ds_logradouro ";
                        $sql .= "  , c.ds_endereco   ";
                        $sql .= "from SYS_CEP c ";
                        $sql .= "  left join SYS_TIPO_LOGRADOURO t on (t.cd_tipo = c.cd_tipo) ";
                        $sql .= "where c.nr_cep = {$nr_cep} ";
                        
                        $res = $pdo->query($sql);
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $resultado = array('cep' => array());
                            
                            $resultado['cep'][0]['nr_cep']    = $obj->nr_cep;
                            $resultado['cep'][0]['cd_estado'] = $obj->cd_estado;
                            $resultado['cep'][0]['cd_cidade'] = $obj->cd_cidade;
                            $resultado['cep'][0]['nm_cidade'] = $obj->nm_cidade;
                            $resultado['cep'][0]['cd_bairro'] = $obj->cd_bairro;
                            $resultado['cep'][0]['nm_bairro'] = $obj->nm_bairro;
                            $resultado['cep'][0]['cd_tipo']   = $obj->cd_tipo;
                            $resultado['cep'][0]['ds_tipo']   = $obj->ds_tipo;
                            $resultado['cep'][0]['ds_logradouro'] = $obj->ds_logradouro;
                            $resultado['cep'][0]['ds_endereco']   = $obj->ds_endereco;

                            $json = json_encode($resultado);
                            file_put_contents($file, $json);
                            echo "OK";
                        } else {
                            echo "Não foi localizado em nossa base de dados um Endereço correspondente ao Número de Cep informado.";
                        }
                        
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    }
                } break;
            
                case 'salvar_cep' : {
                    try {
                        $file = '../logs/cep_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $operacao  = trim(filter_input(INPUT_POST, 'operacao')); 
                        $nr_cep    = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'nr_cep')));
                        $cd_estado = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_estado')));
                        $cd_cidade = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_cidade')));
                        $cd_bairro = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_bairro')));
                        $cd_tipo   = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_tipo')));
                        $ds_logradouro = trim(strtoupper(filter_input(INPUT_POST, 'ds_logradouro')));
                        
                        $nm_bairro = trim(strtoupper(filter_input(INPUT_POST, 'nm_bairro')));
                        $nm_cidade = trim(strtoupper(filter_input(INPUT_POST, 'nm_cidade')));
                        $uf        = trim(strtoupper(filter_input(INPUT_POST, 'uf')));
                        $ds_endereco = trim(strtoupper(filter_input(INPUT_POST, 'ds_endereco')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Verificar se o NOME já está sendo utilizado por outro bairro na mesma cidade
                        $sql  = "Select ";
                        $sql .= "   c.* ";
                        $sql .= "from SYS_CEP c ";
                        $sql .= "where (c.nr_cep = {$nr_cep}) ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            if ($operacao === "inserir") {
                                echo "Número de <strong>Cep já cadastrado</strong>!";
                                exit;
                            } else {
                                $stm = $pdo->prepare(
                                      'Update SYS_CEP c Set '
                                    . '    c.ds_endereco = :ds_endereco '
                                    . '  , c.nm_bairro   = :nm_bairro '
                                    . '  , c.nm_cidade   = :nm_cidade '
                                    . '  , c.uf      = :uf '
                                    . '  , c.cd_tipo = :cd_tipo '
                                    . '  , c.ds_logradouro = :ds_logradouro '
                                    . '  , c.cd_bairro = :cd_bairro '
                                    . '  , c.cd_cidade = :cd_cidade '
                                    . '  , c.cd_estado = :cd_estado '
                                    . 'Where c.nr_cep = :nr_cep ');
                                $stm->execute(array(
                                    ':nr_cep' => $nr_cep,
                                    ':ds_endereco' => $ds_endereco,
                                    ':nm_bairro'   => $nm_bairro,
                                    ':nm_cidade'   => $nm_cidade,
                                    ':uf'          => $uf,
                                    ':cd_tipo'       => $cd_tipo,
                                    ':ds_logradouro' => $ds_logradouro,
                                    ':cd_bairro' => $cd_bairro,
                                    ':cd_cidade' => $cd_cidade,
                                    ':cd_estado' => $cd_estado
                                ));

                                $pdo->commit();
                                echo "OK";
                            }
                        } else {
                                $stm = $pdo->prepare(
                                      'Insert Into SYS_CEP ( '
                                    . '    nr_cep '
                                    . '  , ds_endereco '
                                    . '  , nm_bairro   '
                                    . '  , nm_cidade   '
                                    . '  , uf      '
                                    . '  , cd_tipo '
                                    . '  , ds_logradouro '
                                    . '  , cd_bairro '
                                    . '  , cd_cidade '
                                    . '  , cd_estado '
                                    . ') values (  '
                                    . '    :nr_cep '
                                    . '  , :ds_endereco '
                                    . '  , :nm_bairro '
                                    . '  , :nm_cidade '
                                    . '  , :uf      '
                                    . '  , :cd_tipo '
                                    . '  , :ds_logradouro '
                                    . '  , :cd_bairro '
                                    . '  , :cd_cidade '
                                    . '  , :cd_estado '
                                    . ')');
                                $stm->execute(array(
                                    ':nr_cep' => $nr_cep,
                                    ':ds_endereco' => $ds_endereco,
                                    ':nm_bairro'   => $nm_bairro,
                                    ':nm_cidade'   => $nm_cidade,
                                    ':uf'          => $uf,
                                    ':cd_tipo'       => $cd_tipo,
                                    ':ds_logradouro' => $ds_logradouro,
                                    ':cd_bairro' => $cd_bairro,
                                    ':cd_cidade' => $cd_cidade,
                                    ':cd_estado' => $cd_estado
                               ));

                                $pdo->commit();
                                echo "OK";

                        }
                        
                        $registros = array('formulario' => array());

                        $registros['formulario'][0]['nr_cep']      = $nr_cep;
                        $registros['formulario'][0]['ds_endereco'] = $ds_endereco;
                        $registros['formulario'][0]['nm_bairro']   = $nm_bairro;
                        $registros['formulario'][0]['nm_cidade']   = $nm_cidade;
                        $registros['formulario'][0]['uf']      = $uf;
                        $registros['formulario'][0]['cd_tipo'] = $cd_tipo;
                        $registros['formulario'][0]['ds_logradouro'] = $ds_logradouro;
                        $registros['formulario'][0]['cd_bairro']     = $cd_bairro;
                        $registros['formulario'][0]['cd_cidade']     = $cd_cidade;
                        $registros['formulario'][0]['cd_estado']     = $cd_estado;

                        $json = json_encode($registros);
                        file_put_contents($file, $json);
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_cep' : {
                    try {
                        $nr_cep = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'nr_cep')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from SYS_CEP Where nr_cep = :nr_cep');
                        $stm->execute(array(
                            ':nr_cep' => $nr_cep
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
    
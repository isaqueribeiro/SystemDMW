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
    require_once './licenca_funcionamento_dao.php'; 
    
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
                
                case 'buscar_estabelecimento' : {
                    $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                    $nr_cnpj     = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_cnpj')));
                    
                    try {
                        $file = '../logs/estabelecimento_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    e.* ";
                        $sql .= "from TBESTABELECIMENTO e ";
                        
                        if ( ($id_estabelecimento !== getGuidEmpty()) && ($id_estabelecimento !== "") ) {
                            $sql .= "where (e.id_estabelecimento = '{$id_estabelecimento}') ";
                        } else {
                            $sql .= "where (e.nr_cnpj = '{$nr_cnpj}') ";
                        }
                        
                        $res = $pdo->query($sql);
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $registros = array('estabelecimento' => array());

                            $cd_cnae_principal  = (!empty($obj->cd_cnae_principal)?$obj->cd_cnae_principal:"0");
                            $cd_cnae_secundaria = (!empty($obj->cd_cnae_secundaria)?$obj->cd_cnae_secundaria:"0");
                            
                            $registros['estabelecimento'][0]['id_estabelecimento'] = $obj->id_estabelecimento;
                            $registros['estabelecimento'][0]['tp_pessoa']          = $obj->tp_pessoa;
                            $registros['estabelecimento'][0]['nm_razao']           = $obj->nm_razao;
                            $registros['estabelecimento'][0]['nm_fantasia']        = $obj->nm_fantasia;
                            $registros['estabelecimento'][0]['nr_cnpj']            = formatarTexto('##.###.###/####-##', $obj->nr_cnpj);
                            $registros['estabelecimento'][0]['nr_insc_est']        = $obj->nr_insc_est;
                            $registros['estabelecimento'][0]['nr_insc_mun']        = $obj->nr_insc_mun;
                            $registros['estabelecimento'][0]['cd_cnae_principal']  = $cd_cnae_principal;
                            $registros['estabelecimento'][0]['cd_cnae_secundaria'] = $cd_cnae_secundaria;
                            $registros['estabelecimento'][0]['sn_orgao_publico']   = $obj->sn_orgao_publico;
                            $registros['estabelecimento'][0]['sn_ativo']           = $obj->sn_ativo;
                            $registros['estabelecimento'][0]['tp_endereco']        = $obj->tp_endereco;
                            $registros['estabelecimento'][0]['ds_endereco']        = $obj->ds_endereco;
                            $registros['estabelecimento'][0]['nr_endereco']        = $obj->nr_endereco;
                            $registros['estabelecimento'][0]['ds_complemento']     = $obj->ds_complemento;
                            $registros['estabelecimento'][0]['cd_bairro']          = $obj->cd_bairro;
                            $registros['estabelecimento'][0]['nr_cep']             = formatarTexto('##.###-###', $obj->nr_cep);
                            $registros['estabelecimento'][0]['cd_cidade']          = $obj->cd_cidade;
                            $registros['estabelecimento'][0]['cd_uf']              = $obj->cd_uf;
                            $registros['estabelecimento'][0]['cd_estado']          = $obj->cd_estado;
                            $registros['estabelecimento'][0]['ds_email']           = $obj->ds_email;
                            $registros['estabelecimento'][0]['nm_contato']         = $obj->nm_contato;
                            $registros['estabelecimento'][0]['nr_telefone']        = $obj->nr_telefone;
                            $registros['estabelecimento'][0]['nr_comercial']       = $obj->nr_comercial;
                            $registros['estabelecimento'][0]['nr_celular']         = $obj->nr_celular;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                            echo "OK";
                        } else {
                            echo "CPF/CNPJ não cadastrado!";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
                
                case 'selecionar_estabelecimento' : {
                    try {
                        $html .= "<a id='ancora_estabelecimentos'></a><table id='tb_selecao_estabelecimentos' class='table table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>CPF/CNPJ</th>";
                        $html .= "        <th>Razão Social</th>";
                        $html .= "        <th>Fantasia</th>";
                        $html .= "        <th data-orderable='false'><center>Ativo</center></th>";    // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    e.* ";
                        $sql .= "  , coalesce(( ";
                        $sql .= "      Select ";
                        $sql .= "        count(l.id_licenca) as nr_licencas ";
                        $sql .= "      from TBLICENCA_FUNCIONAMENTO l ";
                        $sql .= "      where l.id_estabelecimento = e.id_estabelecimento ";
                        $sql .= "    ), 0) as nr_licencas ";
                        $sql .= "from TBESTABELECIMENTO e ";
                        $sql .= "where (1 = 1) ";
                        $sql .= "order by ";
                        $sql .= "    e.nm_razao ";

                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $input      = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_estabelecimento;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));
                            
                            $nr_cnpj = $obj->nr_cnpj; 
                            if (strlen($nr_cnpj) > 11) {
                                $nr_cnpj = formatarTexto('##.###.###/####-##', $obj->nr_cnpj);
                            } else {
                                $nr_cnpj = formatarTexto('###.###.###-##', $obj->nr_cnpj);
                            }
                            
                            $cd_cnae_principal  = (!empty($obj->cd_cnae_principal)?$obj->cd_cnae_principal:"0");
                            $cd_cnae_secundaria = (!empty($obj->cd_cnae_secundaria)?$obj->cd_cnae_secundaria:"0");
                            
                            $input  = "<input type='hidden' id='cell_id_estabelecimento_{$referencia}' value='{$obj->id_estabelecimento}'/>"; 
                            $input .= "<input type='hidden' id='cell_tp_pessoa_{$referencia}'          value='{$obj->tp_pessoa}'/>"; 
                            $input .= "<input type='hidden' id='cell_nr_cnpj_{$referencia}'            value='" . $nr_cnpj . "'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_cnae_principal_{$referencia}'  value='{$cd_cnae_principal}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_cnae_secundaria_{$referencia}' value='{$cd_cnae_secundaria}'/>"; 
	                    $input .= "<input type='hidden' id='cell_tp_endereco_{$referencia}'        value='{$obj->tp_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_ds_endereco_{$referencia}'        value='{$obj->ds_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_nr_endereco_{$referencia}'        value='{$obj->nr_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_ds_complemento_{$referencia}'     value='{$obj->ds_complemento}'/>";
                            $input .= "<input type='hidden' id='cell_cd_bairro_{$referencia}'          value='{$obj->cd_bairro}'/>";
                            $input .= "<input type='hidden' id='cell_nr_cep_{$referencia}'             value='" . formatarTexto('##.###-###', $obj->nr_cep) . "'/>";
                            $input .= "<input type='hidden' id='cell_cd_cidade_{$referencia}'          value='{$obj->cd_cidade}'/>";
                            $input .= "<input type='hidden' id='cell_cd_uf_{$referencia}'              value='{$obj->cd_uf}'/>";
                            $input .= "<input type='hidden' id='cell_cd_estado_{$referencia}'          value='{$obj->cd_estado}'/>";
                            $input .= "<input type='hidden' id='cell_nr_comercial_{$referencia}'       value='{$obj->nr_comercial}'/>";
                            $input .= "<input type='hidden' id='cell_nr_telefone_{$referencia}'        value='{$obj->nr_telefone}'/>";
                            $input .= "<input type='hidden' id='cell_nr_celular_{$referencia}'         value='{$obj->nr_celular}'/>";
                            $input .= "<input type='hidden' id='cell_nm_contato_{$referencia}'         value='{$obj->nm_contato}'/>";
                            $input .= "<input type='hidden' id='cell_ds_email_{$referencia}'           value='{$obj->ds_email}'/>";
                            $input .= "<input type='hidden' id='cell_nr_licencas_{$referencia}'        value='{$obj->nr_licencas}'/>"; 
                            $input .= "<input type='hidden' id='cell_sn_orgao_publico_{$referencia}'   value='{$obj->sn_orgao_publico}'/>"; 
                            $input .= "<input type='hidden' id='cell_sn_ativo_{$referencia}'           value='{$obj->sn_ativo}'/>"; 
                            
                            $selecionar = "<a id='estabelecimento_{$referencia}' href='javascript:preventDefault();' onclick='SelecionarEstabelecimentoParaLicenca( this.id )'>  " . $nr_cnpj . "&nbsp;&nbsp;&nbsp;<i class='fa fa-check-circle-o' title='Selecionar Registro'>";
                            
                            if ((int)$obj->sn_ativo === 1) {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td>" . $selecionar  . "</td>";
                            $html .= "        <td>" . $obj->nm_razao    . "</td>";
                            $html .= "        <td>" . $obj->nm_fantasia . "</td>";
                            $html .= "        <td align=center>" . $ativo    . "</td>";
                            $html .= "    </tr>";
                        }
                        
                        $html .= "</tbody>";
                        $html .= "</table>";
                        
                        echo $html;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
            
                case 'pesquisar_estabelecimento' : {
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
                        
                        $html .= "<a id='ancora_estabelecimentos'></a><table id='tb_estabelecimentos' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>CPF/CNPJ</th>";
                        $html .= "        <th>Razão Social</th>";
                        $html .= "        <th>Fantasia</th>";
                        $html .= "        <th>RG/IE</th>";
                        $html .= "        <th>IM</th>";
                        $html .= "        <th data-orderable='false'><center>Licenças</center></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'><center>Ativo</center></th>";    // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";                          // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    e.* ";
                        $sql .= "  , coalesce(( ";
                        $sql .= "      Select ";
                        $sql .= "        count(l.id_licenca) as nr_licencas ";
                        $sql .= "      from TBLICENCA_FUNCIONAMENTO l ";
                        $sql .= "      where l.id_estabelecimento = e.id_estabelecimento ";
                        $sql .= "    ), 0) as nr_licencas ";
                        $sql .= "from TBESTABELECIMENTO e ";
                        $sql .= "where (1 = 1) ";
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Apenas estabelecimentos ativos
                                $sql .= "  and (e.sn_ativo = 1) ";
                            } break;   

                            case 2: { // Filtrar Razão, Fantasia
                                $sql .= "  and ( (upper(e.nm_razao) like '{$pesquisa}%') or (upper(e.nm_fantasia) like '{$pesquisa}%') ) ";
                            } break;
                        
                            case 3: { // Filtrar por CNPJ
                                $sql .= "  and (upper(e.nr_cnpj) like '{$pesquisa}%') ";
                            } break;   
                        } 
                            
                        $sql .= "order by ";
                        $sql .= "    e.nm_razao ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $input      = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_estabelecimento;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));
                            
                            $nr_cnpj = $obj->nr_cnpj; 
                            if (strlen($nr_cnpj) > 11) {
                                $nr_cnpj = formatarTexto('##.###.###/####-##', $obj->nr_cnpj);
                            } else {
                                $nr_cnpj = formatarTexto('###.###.###-##', $obj->nr_cnpj);
                            }
                            
                            $cd_cnae_principal  = (!empty($obj->cd_cnae_principal)?$obj->cd_cnae_principal:"0");
                            $cd_cnae_secundaria = (!empty($obj->cd_cnae_secundaria)?$obj->cd_cnae_secundaria:"0");
                            
                            $input  = "<input type='hidden' id='cell_id_estabelecimento_{$referencia}' value='{$obj->id_estabelecimento}'/>"; 
                            $input .= "<input type='hidden' id='cell_tp_pessoa_{$referencia}'          value='{$obj->tp_pessoa}'/>"; 
                            $input .= "<input type='hidden' id='cell_nr_cnpj_{$referencia}'            value='" . $nr_cnpj . "'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_cnae_principal_{$referencia}'  value='{$cd_cnae_principal}'/>"; 
                            $input .= "<input type='hidden' id='cell_cd_cnae_secundaria_{$referencia}' value='{$cd_cnae_secundaria}'/>"; 
	                    $input .= "<input type='hidden' id='cell_tp_endereco_{$referencia}'        value='{$obj->tp_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_ds_endereco_{$referencia}'        value='{$obj->ds_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_nr_endereco_{$referencia}'        value='{$obj->nr_endereco}'/>";
                            $input .= "<input type='hidden' id='cell_ds_complemento_{$referencia}'     value='{$obj->ds_complemento}'/>";
                            $input .= "<input type='hidden' id='cell_cd_bairro_{$referencia}'          value='{$obj->cd_bairro}'/>";
                            $input .= "<input type='hidden' id='cell_nr_cep_{$referencia}'             value='" . formatarTexto('##.###-###', $obj->nr_cep) . "'/>";
                            $input .= "<input type='hidden' id='cell_cd_cidade_{$referencia}'          value='{$obj->cd_cidade}'/>";
                            $input .= "<input type='hidden' id='cell_cd_uf_{$referencia}'              value='{$obj->cd_uf}'/>";
                            $input .= "<input type='hidden' id='cell_cd_estado_{$referencia}'          value='{$obj->cd_estado}'/>";
                            $input .= "<input type='hidden' id='cell_nr_comercial_{$referencia}'       value='{$obj->nr_comercial}'/>";
                            $input .= "<input type='hidden' id='cell_nr_telefone_{$referencia}'        value='{$obj->nr_telefone}'/>";
                            $input .= "<input type='hidden' id='cell_nr_celular_{$referencia}'         value='{$obj->nr_celular}'/>";
                            $input .= "<input type='hidden' id='cell_nm_contato_{$referencia}'         value='{$obj->nm_contato}'/>";
                            $input .= "<input type='hidden' id='cell_ds_email_{$referencia}'           value='{$obj->ds_email}'/>";
                            $input .= "<input type='hidden' id='cell_nr_licencas_{$referencia}'        value='{$obj->nr_licencas}'/>"; 
                            $input .= "<input type='hidden' id='cell_sn_orgao_publico_{$referencia}'   value='{$obj->sn_orgao_publico}'/>"; 
                            $input .= "<input type='hidden' id='cell_sn_ativo_{$referencia}'           value='{$obj->sn_ativo}'/>"; 
                            
                            $editar  = "<a id='estabelecimento_{$referencia}' href='javascript:preventDefault();' onclick='EditarEstabelecimento( this.id )'>  " . $nr_cnpj . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
                            $excluir = "<a id='excluir_estabelecimento_{$referencia}' href='javascript:preventDefault();' onclick='ExcluirEstabelecimento( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->sn_ativo === 1) {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-check-square-o'>&nbsp;{$input}</i>";
                            } else {
                                $ativo = "<i id='img_ativo_{$referencia}' class='fa fa-circle-thin'>&nbsp;{$input}</i>";
                            }
                            
                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td>" . $editar  . "</td>";
                            $html .= "        <td>" . $obj->nm_razao    . "</td>";
                            $html .= "        <td>" . $obj->nm_fantasia . "</td>";
                            $html .= "        <td>" . ($obj->nr_insc_est === ""?"...":$obj->nr_insc_est) . "</td>";
                            $html .= "        <td>" . ($obj->nr_insc_mun === ""?"...":$obj->nr_insc_mun) . "</td>";
                            $html .= "        <td align=right>"  . $obj->nr_licencas . "</td>";
                            $html .= "        <td align=center>" . $ativo    . "</td>";
                            $html .= "        <td align=center>" . $excluir  . "</td>";
                            $html .= "    </tr>";
                            
                            $num = $num + 1;
                        }
                        
                        $html .= "</tbody>";
                        $html .= "</table>";
                        
                        if ($num == 0) {
                            $funcao = new Constantes();
                            echo $funcao->message_alert("Não existem dados com os parâmetros de pesquisa informados!");
                            exit;
                        }
                        
                        $html .= "    </div>";
                        $html .= "</div>";
                        
                        echo $html;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . oci_error();
                    } 
                } break;
            
                case 'salvar_estabelecimento' : {
                    try {
                        $file = '../logs/estabelecimento_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                        $tp_pessoa   = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tp_pessoa')));
                        $nm_razao    = trim(filter_input(INPUT_POST, 'nm_razao'));
                        $nm_fantasia = trim(filter_input(INPUT_POST, 'nm_fantasia'));
                        $nr_cnpj     = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_cnpj')));
                        $nr_insc_est = trim(filter_input(INPUT_POST, 'nr_insc_est'));
                        $nr_insc_mun = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_insc_mun')));
//                        $cd_cnae_principal  = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_cnae_principal')));
//                        $cd_cnae_secundaria = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_cnae_secundaria')));
                        $cd_cnae_principal  = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cd_cnae_principal')));
                        $cd_cnae_secundaria = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'cd_cnae_secundaria')));
                        $tp_endereco    = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tp_endereco')));
                        $ds_endereco    = trim(filter_input(INPUT_POST, 'ds_endereco'));
                        $nr_endereco    = trim(filter_input(INPUT_POST, 'nr_endereco'));
                        $ds_complemento = trim(filter_input(INPUT_POST, 'ds_complemento'));
                        $cd_uf     = trim(filter_input(INPUT_POST, 'cd_uf'));
                        $cd_estado = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_estado')));
                        $cd_cidade = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_cidade')));
                        $cd_bairro = preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'cd_bairro')));
                        $nr_cep    = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_cep')));
                        $nr_comercial = trim(filter_input(INPUT_POST, 'nr_comercial'));
                        $nr_telefone  = trim(filter_input(INPUT_POST, 'nr_telefone'));
                        $nr_celular   = trim(filter_input(INPUT_POST, 'nr_celular'));
                        $nm_contato   = trim(filter_input(INPUT_POST, 'nm_contato'));
                        $ds_email     = trim(filter_input(INPUT_POST, 'ds_email'));
                        $sn_orgao_publico = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sn_orgao_publico')));
                        $sn_ativo         = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sn_ativo')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        if (strlen($nr_cnpj) < 14) {
                           $tp_pessoa = 0; 
                        } else {
                           $tp_pessoa = 1;
                        }
                        
                        // Buscar UF do Estado informado
                        $euf = $pdo->query("Select e.uf_estado from SYS_ESTADOS e where e.cd_estado = {$cd_estado}");
                        if (($uf = $euf->fetch(PDO::FETCH_OBJ)) !== false) {
                            $cd_uf = trim($uf->uf_estado);
                        } else {
                            $cd_uf = "NULL";
                        }
                        
                        if ((int)$cd_cnae_secundaria === 0) {
                            $cd_cnae_secundaria = null;
                        } 
                        elseif ((int)$cd_cnae_secundaria === (int)$cd_cnae_principal) {
                            $cd_cnae_secundaria = null;
                        }
                        
                        // Verificar se o CNPJ já está sendo utilizado por outro Estabelecimento que não seja 
                        // um órgão público.
                        $sql  = "Select ";
                        $sql .= "   e.* ";
                        $sql .= "from TBESTABELECIMENTO e ";
                        $sql .= "where (e.nr_cnpj             = '{$nr_cnpj}') ";
                        $sql .= "  and (e.sn_orgao_publico    = 0) ";
                        $sql .= "  and ({$sn_orgao_publico}   = 0) ";
                        $sql .= "  and (e.id_estabelecimento <> '{$id_estabelecimento}') ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            echo "CPF/CNPJ informado já cadastrado!";
                        } else {
                            $dao  = Dao::getInstancia();
                            $dh   = $dao->getDataHoraServidor();
                            $us   = $usuario->getId();
                            
                            $sql  = "";
                            $sql .= "Select ";
                            $sql .= "   e.* ";
                            $sql .= "from TBESTABELECIMENTO e ";
                            $sql .= "where (e.id_estabelecimento = '{$id_estabelecimento}') ";

                            $res = $pdo->query($sql);

                            // Gravar Dados (Update)
                            if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                $hash = encript($id_estabelecimento . $dh . $us);
                                
                                $stm = $pdo->prepare(
                                      'Update TBESTABELECIMENTO e Set '
                                    . '    e.tp_pessoa         = :tp_pessoa         '
                                    . '  , e.nm_razao          = :nm_razao          '
                                    . '  , e.nm_fantasia       = :nm_fantasia       '
                                    . '  , e.nr_cnpj           = :nr_cnpj           '
                                    . '  , e.nr_insc_est       = :nr_insc_est       '
                                    . '  , e.nr_insc_mun       = :nr_insc_mun       '
                                    . '  , e.cd_cnae_principal  = :cd_cnae_principal  '
                                    . '  , e.cd_cnae_secundaria = :cd_cnae_secundaria '
                                    . '  , e.tp_endereco       = :tp_endereco       '
                                    . '  , e.ds_endereco       = :ds_endereco       '
                                    . '  , e.nr_endereco       = :nr_endereco       '
                                    . '  , e.ds_complemento    = :ds_complemento    '
                                    . '  , e.cd_bairro         = :cd_bairro         '
                                    . '  , e.nr_cep            = :nr_cep            '
                                    . '  , e.cd_cidade         = :cd_cidade         '
                                    . '  , e.cd_uf             = :cd_uf             '
                                    . '  , e.cd_estado         = :cd_estado         '
                                    . '  , e.ds_email          = :ds_email          '
                                    . '  , e.nm_contato        = :nm_contato        '
                                    . '  , e.nr_telefone       = :nr_telefone       '
                                    . '  , e.nr_comercial      = :nr_comercial      '
                                    . '  , e.nr_celular        = :nr_celular        '
                                    . '  , e.us_alteracao      = :us_alteracao      '
                                    . '  , e.dh_alteracao      = :dh_alteracao      '
                                    . '  , e.hash_alteracao    = :hash_alteracao    '
                                    . '  , e.sn_orgao_publico  = :sn_orgao_publico  '
                                    . '  , e.sn_ativo          = :sn_ativo          '
                                    . 'Where e.id_estabelecimento = :id_estabelecimento ');
                                $stm->execute(array(
                                    ':id_estabelecimento' => $id_estabelecimento,
                                    ':tp_pessoa'          => $tp_pessoa         ,
                                    ':nm_razao'           => $nm_razao          ,
                                    ':nm_fantasia'        => $nm_fantasia       ,
                                    ':nr_cnpj'            => $nr_cnpj           ,
                                    ':nr_insc_est'        => $nr_insc_est       ,
                                    ':nr_insc_mun'        => $nr_insc_mun       ,
                                    ':cd_cnae_principal'  => $cd_cnae_principal , 
                                    ':cd_cnae_secundaria' => $cd_cnae_secundaria, 
                                    ':tp_endereco'        => $tp_endereco       ,
                                    ':ds_endereco'        => $ds_endereco       ,
                                    ':nr_endereco'        => $nr_endereco       ,
                                    ':ds_complemento'     => $ds_complemento    ,
                                    ':cd_bairro'          => $cd_bairro         ,
                                    ':nr_cep'             => $nr_cep            ,
                                    ':cd_cidade'          => $cd_cidade         ,
                                    ':cd_uf'              => ($cd_uf === "NULL"?"":$cd_uf),
                                    ':cd_estado'          => $cd_estado         ,
                                    ':ds_email'           => $ds_email          ,
                                    ':nm_contato'         => $nm_contato        ,
                                    ':nr_telefone'        => $nr_telefone       ,
                                    ':nr_comercial'       => $nr_comercial      ,
                                    ':nr_celular'         => $nr_celular        ,
                                    ':us_alteracao'       => $us                ,
                                    ':dh_alteracao'       => $dh                ,
                                    ':hash_alteracao'     => $hash              ,
                                    ':sn_orgao_publico'   => $sn_orgao_publico  ,
                                    ':sn_ativo'           => $sn_ativo         
                                ));

                                $pdo->commit();
                            // Gravando Dados (Insert)
                            } else {
                                $dao = Dao::getInstancia();
                                $id_estabelecimento = $dao->getGuidIDFormat();
                                $hash = encript($id_estabelecimento . $dh . $us);

                                $stm = $pdo->prepare(
                                      'Insert Into TBESTABELECIMENTO ( '
                                    . '    id_estabelecimento '	
                                    . '  , tp_pessoa          '
                                    . '  , nm_razao           '
                                    . '  , nm_fantasia        '
                                    . '  , nr_cnpj            '
                                    . '  , nr_insc_est        '
                                    . '  , nr_insc_mun        '
                                    . '  , cd_cnae_principal  '
                                    . '  , cd_cnae_secundaria '
                                    . '  , tp_endereco        '
                                    . '  , ds_endereco        '
                                    . '  , nr_endereco        '
                                    . '  , ds_complemento     '
                                    . '  , cd_bairro          '
                                    . '  , nr_cep             '
                                    . '  , cd_cidade          '
                                    . '  , cd_uf              '
                                    . '  , cd_estado          '
                                    . '  , ds_email           '
                                    . '  , nm_contato         '
                                    . '  , nr_telefone        '
                                    . '  , nr_comercial       '
                                    . '  , nr_celular         '
                                    . '  , us_cadastro        '
                                    . '  , dh_cadastro        '
                                    . '  , hash_cadastro      '
                                    . '  , sn_orgao_publico   '    
                                    . '  , sn_ativo           '
                                    . ') values ( '
                                    . '    :id_estabelecimento '	
                                    . '  , :tp_pessoa          '
                                    . '  , :nm_razao           '
                                    . '  , :nm_fantasia        '
                                    . '  , :nr_cnpj            '
                                    . '  , :nr_insc_est        '
                                    . '  , :nr_insc_mun        '
                                    . '  , :cd_cnae_principal  '
                                    . '  , :cd_cnae_secundaria '
                                    . '  , :tp_endereco        '
                                    . '  , :ds_endereco        '
                                    . '  , :nr_endereco        '
                                    . '  , :ds_complemento     '
                                    . '  , :cd_bairro          '
                                    . '  , :nr_cep             '
                                    . '  , :cd_cidade          '
                                    . '  , :cd_uf              '
                                    . '  , :cd_estado          '
                                    . '  , :ds_email           '
                                    . '  , :nm_contato         '
                                    . '  , :nr_telefone        '
                                    . '  , :nr_comercial       '
                                    . '  , :nr_celular         '
                                    . '  , :us_cadastro        '
                                    . '  , :dh_cadastro        '
                                    . '  , :hash_cadastro      '
                                    . '  , :sn_orgao_publico   '
                                    . '  , :sn_ativo           '
                                    . ')');
                                $stm->execute(array(
                                    ':id_estabelecimento' => $id_estabelecimento,
                                    ':tp_pessoa'          => $tp_pessoa         ,
                                    ':nm_razao'           => $nm_razao          ,
                                    ':nm_fantasia'        => $nm_fantasia       ,
                                    ':nr_cnpj'            => $nr_cnpj           ,
                                    ':nr_insc_est'        => $nr_insc_est       ,
                                    ':nr_insc_mun'        => $nr_insc_mun       ,
                                    ':cd_cnae_principal'  => $cd_cnae_principal , 
                                    ':cd_cnae_secundaria' => $cd_cnae_secundaria, 
                                    ':tp_endereco'        => $tp_endereco       ,
                                    ':ds_endereco'        => $ds_endereco       ,
                                    ':nr_endereco'        => $nr_endereco       ,
                                    ':ds_complemento'     => $ds_complemento    ,
                                    ':cd_bairro'          => $cd_bairro         ,
                                    ':nr_cep'             => $nr_cep            ,
                                    ':cd_cidade'          => $cd_cidade         ,
                                    ':cd_uf'              => ($cd_uf === "NULL"?"":$cd_uf),
                                    ':cd_estado'          => $cd_estado         ,
                                    ':ds_email'           => $ds_email          ,
                                    ':nm_contato'         => $nm_contato        ,
                                    ':nr_telefone'        => $nr_telefone       ,
                                    ':nr_comercial'       => $nr_comercial      ,
                                    ':nr_celular'         => $nr_celular        ,
                                    ':us_cadastro'        => $us                ,
                                    ':dh_cadastro'        => $dh                ,
                                    ':hash_cadastro'      => $hash              ,
                                    ':sn_orgao_publico'   => $sn_orgao_publico  ,
                                    ':sn_ativo'           => $sn_ativo         
                                ));

                                $pdo->commit();
                            }

                            // Suspender as Licenças de Funcionamento do Estabelecimento quando este for desativado
                            if ($sn_ativo === 0) {
                                
                            }
                            
                            $registros = array('formulario' => array());

                            $registros['formulario'][0]['id_estabelecimento'] = $id_estabelecimento;
                            $registros['formulario'][0]['nm_razao']    = $nm_razao;
                            $registros['formulario'][0]['nm_fantasia'] = $nm_fantasia;
                            $registros['formulario'][0]['nr_cnpj']     = $nr_cnpj;
                            $registros['formulario'][0]['cd_uf']       = $cd_uf;
                            $registros['formulario'][0]['cd_cnae_principal']  = $cd_cnae_principal;
                            $registros['formulario'][0]['cd_cnae_secundaria'] = $cd_cnae_secundaria;
                            $registros['formulario'][0]['sn_orgao_publico']   = $sn_orgao_publico;
                            $registros['formulario'][0]['sn_ativo']           = $sn_ativo;

                            $json = json_encode($registros);
                            file_put_contents($file, $json);
                            echo "OK";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_estabelecimento' : {
                    try {
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stm = $pdo->prepare('Delete from TBESTABELECIMENTO Where id_estabelecimento = :id_estabelecimento');
                        $stm->execute(array(
                            ':id_estabelecimento' => $id_estabelecimento
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
    
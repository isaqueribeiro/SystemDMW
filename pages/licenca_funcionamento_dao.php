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
    
    $_SITUACAO_LICENCA_PENDENTE   = 0;
    $_SITUACAO_LICENCA_AGUARDANDO = 1;
    $_SITUACAO_LICENCA_APROVADA   = 2;
    $_SITUACAO_LICENCA_VENCIDA    = 3;
    $_SITUACAO_LICENCA_SUSPENSA   = 4;
    $_SITUACAO_LICENCA_CANCELADA  = 5;
    
    $_SITUACAO_LICENCA = array("Pendente", "Aguardando", "Aprovada", "Vencida", "Suspensa", "Cancelada");
    
    function setEventoLicenca($usuario, $ds_evento, $id_estabelecimento, $id_licenca) {
        try {
            $dao = Dao::getInstancia();
            $id  = $dao->getGuidIDFormat();
            $dt  = $dao->getDataServidor();
            $dh  = $dao->getDataHoraServidor();
            $us  = $usuario->getId();
            
            $hash = encript($id . $dh . $us);
                                    
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stm = $pdo->prepare(
                  'Insert Into TBEVENTO ('
                . '    id_evento '
                . '  , dt_evento '
                . '  , dh_evento '
                . '  , ds_evento '
                . '  , id_usuario '
                . '  , id_estabelecimento '
                . '  , id_licenca '
                . '  , hash_evento '
                . ') values ( '
                . '    :id_evento '
                . '  , :dt_evento '
                . '  , :dh_evento '
                . '  , :ds_evento '
                . '  , :id_usuario '
                . '  , :id_estabelecimento '
                . '  , :id_licenca '
                . '  , :hash_evento '
                . ')');
            $stm->execute(array(
                ':id_evento'  => $id,
                ':dt_evento'  => $dt,
                ':dh_evento'  => $dh,
                ':ds_evento'  => $ds_evento,
                ':id_usuario' => $us,
                ':id_estabelecimento' => $id_estabelecimento,
                ':id_licenca'  => (trim($id_licenca) === ""?null:$id_licenca),
                ':hash_evento' => $hash,
            ));

            $pdo->commit();
            return true;
        } catch (Exception $ex) {
            echo $ex . "<br><br>" . $ex->getMessage();
            return false;
        }
    }
    
    function cancelarLicencasFuncionamento() {
        try {
            $_SITUACAO_LICENCA_APROVADA   = 2;
            $_SITUACAO_LICENCA_VENCIDA    = 3;
            $_SITUACAO_LICENCA_CANCELADA  = 5;

            $_SITUACAO_LICENCA = array("Pendente", "Aguardando", "Aprovada", "Vencida", "Suspensa", "Cancelada");
            
            $dao = Dao::getInstancia();
            $id  = $dao->getGuidIDFormat();
            $dt  = $dao->getDataServidor();
            $dh  = $dao->getDataHoraServidor();
            
            $cnf = Configuracao::getInstancia();
            $pdo = $cnf->db('', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Buscar todas as loicenças vencidas que ainda não estão na
            // situação de "3. Vendida" ou "5. Cancelada"
            $sql = 
                  "Select "
                . "    l.id_licenca "
                . "  , l.id_estabelecimento "
                . "  , coalesce(l.dt_validade, current_date) + 1 as dt_validade "
                . "  , coalesce(l.dt_validade, current_date) + 1 + current_time as dh_validade "
                . "  , l.tp_situacao "
                . "from TBLICENCA_FUNCIONAMENTO l  "
                . "where l.dt_validade < current_date "
                . "  and l.nr_exercicio = " . date('Y') . " "
                . "  and l.tp_situacao not in ({$_SITUACAO_LICENCA_VENCIDA}, {$_SITUACAO_LICENCA_CANCELADA}) "; 
            
            $res = $pdo->query($sql);
            while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                $id_licenca         = $obj->id_licenca;
                $id_estabelecimento = $obj->id_estabelecimento;
                $dt_validade        = $obj->dt_validade;
                $dh_validade        = $obj->dh_validade;
                $tp_situacao        = $_SITUACAO_LICENCA_CANCELADA;
                
                if ( intval($obj->tp_situacao) === $_SITUACAO_LICENCA_APROVADA ) {
                    $tp_situacao = $_SITUACAO_LICENCA_VENCIDA;
                } else {
                    $tp_situacao = $_SITUACAO_LICENCA_CANCELADA;
                }
                
                $dt   = $dt_validade;
                $dh   = $dh_validade;
                $hash = encript($id . $dh . "sysdba");
                                    
                // Alterar situação da Licença
                $stm = $pdo->prepare('Update TBLICENCA_FUNCIONAMENTO l set l.tp_situacao = :tp_situacao where l.id_licenca = :id_licenca');
                $stm->execute(array(
                    ':id_licenca'  => $id_licenca,
                    ':tp_situacao' => $tp_situacao,
                ));

                $pdo->commit();
                
                $stm = $pdo->prepare(
                      'Insert Into TBEVENTO ('
                    . '    id_evento '
                    . '  , dt_evento '
                    . '  , dh_evento '
                    . '  , ds_evento '
                    . '  , id_usuario '
                    . '  , id_estabelecimento '
                    . '  , id_licenca '
                    . '  , hash_evento '
                    . ') values ( '
                    . '    :id_evento '
                    . '  , :dt_evento '
                    . '  , :dh_evento '
                    . '  , :ds_evento '
                    . '  , NULL '
                    . '  , :id_estabelecimento '
                    . '  , :id_licenca '
                    . '  , :hash_evento '
                    . ')');
                $stm->execute(array(
                    ':id_evento'  => $id,
                    ':dt_evento'  => $dt,
                    ':dh_evento'  => $dh,
                    ':ds_evento'  => "A Licença de Funcionamento foi colocada como '{$_SITUACAO_LICENCA[$tp_situacao]}' de forma automática pelo sistema.",
                    ':id_estabelecimento' => $id_estabelecimento,
                    ':id_licenca'  => (trim($id_licenca) === ""?null:$id_licenca),
                    ':hash_evento' => $hash,
                ));

                $pdo->commit();
            }
            
            return true;
        } catch (Exception $ex) {
            echo $ex . "<br><br>" . $ex->getMessage();
            return false;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Verificar o Token de segurança
        $token = filter_input(INPUT_POST, 'token');
                     
        if ( $token !== $usuario->getToken_id() ) {
            $funcao = new Constantes();
            echo $funcao->message_alert("TokenID de segurança inválido!");
            exit;
        }
            
        if (isset($_POST['ac'])) {
            
            switch ($_POST['ac']) {
                
                case 'pesquisar_licenca_funcionamento' : {
                    try {
                        cancelarLicencasFuncionamento();
                        
                        $ano_exercicio = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'ano_exercicio')));
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
                        
                        $html .= "<a id='ancora_licencas_funcionamento'></a><table id='tb_licencas_funcionamento' class='table table-bordered table-hover'  width='100%'>";

                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Controle</th>";
                        $html .= "        <th>Processo</th>";
                        $html .= "        <th>Estabelecimento</th>";
                        $html .= "        <th>CPF/CNPJ</th>";
                        $html .= "        <th>Validade</th>";
                        $html .= "        <th data-orderable='false'>Situação</th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";      // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "        <th data-orderable='false'></th>";      // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";

                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "Select ";
                        $sql .= "    l.* ";
                        $sql .= "  , e.nm_razao ";
                        $sql .= "  , e.nm_fantasia ";
                        $sql .= "  , e.nr_cnpj ";
                        $sql .= "  , case when c.dt_vencimento is null then 1 else 0 end as sn_provisoria ";
                        $sql .= "from TBLICENCA_FUNCIONAMENTO l ";
                        $sql .= "  left join TBESTABELECIMENTO e on (e.id_estabelecimento = l.id_estabelecimento) ";
                        $sql .= "  left join TBCATEGORIA_LICENCA c on (c.cd_categoria = l.cd_categoria) ";
                        $sql .= "where (1 = 1) ";
                        
                        if ($ano_exercicio !== 0) {
                            $sql .= "  and (l.nr_exercicio = {$ano_exercicio}) ";
                        }
                        
                        switch ($tipo_pesquisa) {
                            case 1: { // Apenas as Licenças de Funcionamento Pendentes
                                $sql .= "  and (l.tp_situacao < {$_SITUACAO_LICENCA_APROVADA}) ";
                            } break;   

                            case 2: { // Apenas as Licenças de Funcionamento Ativas
                                $sql .= "  and (l.tp_situacao = {$_SITUACAO_LICENCA_APROVADA}) ";
                            } break;   

                            case 3: { // Apenas as Licenças de Funcionamento Vendidas
                                $sql .= "  and (l.tp_situacao = {$_SITUACAO_LICENCA_VENCIDA}) ";
                            } break;
                        
                            case 4: { // Apenas as Licenças de Funcionamento Suspensas
                                $sql .= "  and (l.tp_situacao = {$_SITUACAO_LICENCA_SUSPENSA}) ";
                            } break;   
                        
                            case 5: { // Número de Controle
                                $sql .= "  and (l.nr_licenca = {$pesquisa}) ";
                            } break;   
                        
                            case 6: { // Número do Processo
                                $sql .= "  and (l.nr_processo like '{$pesquisa}%') ";
                            } break;   
                        
                            case 7: { // Nome do Estabelecimento
                                $sql .= "  and ((e.nm_razao like '{$pesquisa}%') or (e.nm_fantasia like '{$pesquisa}%')) ";
                            } break;   
                        
                            case 8: { // CNPJ do Estabelecimento
                                $pesquisa = preg_replace("/[^0-9]/", "", $pesquisa);
                                $sql .= "  and (e.nr_cnpj like '{$pesquisa}%') ";
                            } break;   
                        } 
                        
                        $sql .= "order by ";
                        $sql .= "    l.nr_exercicio ";
                        $sql .= "  , l.nr_licenca   ";

                        $num = 0;
                        $res = $pdo->query($sql);
                        
                        $referencia = "";
                        $ativo   = "";   
                        $input   = "";
                        $editar  = "";
                        $excluir = "";
                        $opcoes  = "";
                        
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_licenca;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));

                            $nr_cnpj = $obj->nr_cnpj; 
                            if (strlen($nr_cnpj) > 11) {
                                $nr_cnpj = formatarTexto('##.###.###/####-##', $obj->nr_cnpj);
                            } else {
                                $nr_cnpj = formatarTexto('###.###.###-##', $obj->nr_cnpj);
                            }
                            
                            $opcoes  = "<div class='btn-group'> ";
                            $opcoes .= "    <button type='button' class='btn btn-primary'><i class='fa fa-edit' title='Mais Opções'></i></button> ";
                            $opcoes .= "    <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'> ";
                            $opcoes .= "        <span class='caret'></span> ";
                            $opcoes .= "        <span class='sr-only'></span> ";
                            $opcoes .= "    </button> ";
                            $opcoes .= "    <ul class='dropdown-menu' role='menu'> ";
                            $opcoes .= "        <li><a href='javascript:preventDefault();' id='set_licenca_{$referencia}' onclick='setSituacaoLicencaFuncionamento(this.id, {$_SITUACAO_LICENCA_AGUARDANDO})'><i class='fa fa-calendar'></i> Aguardar</a></li> ";
                            $opcoes .= "        <li><a href='javascript:preventDefault();' id='set_licenca_{$referencia}' onclick='setSituacaoLicencaFuncionamento(this.id, {$_SITUACAO_LICENCA_APROVADA})'><i class='fa fa-check-square-o'></i> Aprovar</a></li> ";
                            $opcoes .= "        <li><a href='javascript:preventDefault();' id='set_licenca_{$referencia}' onclick='setSituacaoLicencaFuncionamento(this.id, {$_SITUACAO_LICENCA_SUSPENSA})'><i class='fa fa-circle-o'></i> Suspender</a></li> ";
                            $opcoes .= "        <li class='divider'></li> ";
                            $opcoes .= "        <li><a href='javascript:preventDefault();' id='emt_licenca_{$referencia}' onclick='EmitirLicencaFuncionamento(this.id)'><i class='fa fa-print'></i> Emitir Licença</a></li> ";
                            $opcoes .= "        <li><a href='javascript:preventDefault();' id='evt_licenca_{$referencia}' onclick='EventoLicencaFuncionamento(this.id)'><i class='fa fa-bell-o'></i> Registrar Eventos</a></li> ";
                            $opcoes .= "    </ul> ";
                            $opcoes .= "</div> ";
                            
                            $dt_emissao  = explode("-", $obj->dt_emissao);
                            $dt_validade = explode("-", $obj->dt_validade);
                            
                            $cd_atividade = intval("0". $obj->cd_atividade);
                            $cd_atividade_secundaria = intval("0". $obj->cd_atividade_secundaria);
                            $cd_responsavel = (trim(" ".$obj->cd_responsavel) === ""?getGuidEmpty():$obj->cd_responsavel);
                            $nm_responsavel_estabelecimento = (trim(" ".$obj->nm_responsavel_estabelecimento) === ""?getGuidEmpty():$obj->nm_responsavel_estabelecimento);
                            $nr_responsavel_estabelecimento = (trim(" ".$obj->nm_responsavel_estabelecimento) === ""?getGuidEmpty():$obj->nr_responsavel_estabelecimento);
                            $cn_responsavel_estabelecimento = (trim(" ".$obj->nm_responsavel_estabelecimento) === ""?getGuidEmpty():$obj->cn_responsavel_estabelecimento);
                            
                            $input  = "<input type='hidden' id='cell_id_licenca_{$referencia}'         value='{$obj->id_licenca}'/>";
                            $input .= "<input type='hidden' id='cell_nr_exercicio_{$referencia}'       value='{$obj->nr_exercicio}'/>";
                            $input .= "<input type='hidden' id='cell_nr_licenca_{$referencia}'         value='{$obj->nr_licenca}'/>";
                            $input .= "<input type='hidden' id='cell_nr_processo_{$referencia}'        value='{$obj->nr_processo}'/>";
                            $input .= "<input type='hidden' id='cell_dt_emissao_{$referencia}'         value='" . $dt_emissao[2]  . "/" . $dt_emissao[1]  . "/" . $dt_emissao[0]  . "'/>";
                            $input .= "<input type='hidden' id='cell_dt_validade_{$referencia}'        value='" . $dt_validade[2] . "/" . $dt_validade[1] . "/" . $dt_validade[0] . "'/>";
                            $input .= "<input type='hidden' id='cell_sn_provisoria_{$referencia}'      value='{$obj->sn_provisoria}'/>";
                            $input .= "<input type='hidden' id='cell_id_estabelecimento_{$referencia}' value='{$obj->id_estabelecimento}'/>";
                            $input .= "<input type='hidden' id='cell_nr_cnpj_{$referencia}'            value='{$obj->nr_cnpj}'/>";
                            $input .= "<input type='hidden' id='cell_nm_razao_{$referencia}'           value='{$obj->nm_razao}'/>";
                            $input .= "<input type='hidden' id='cell_cd_atividade_{$referencia}'       value='{$cd_atividade}'/>";
                            $input .= "<input type='hidden' id='cell_cd_atividade_secundaria_{$referencia}' value='{$cd_atividade_secundaria}'/>";
                            $input .= "<input type='hidden' id='cell_cd_categoria_{$referencia}'       value='{$obj->cd_categoria}'/>";
                            $input .= "<input type='hidden' id='cell_tp_situacao_{$referencia}'        value='{$obj->tp_situacao}'/>";
                            $input .= "<input type='hidden' id='cell_sn_licenca_publica_{$referencia}' value='{$obj->sn_licenca_publica}'/>";
                            $input .= "<input type='hidden' id='cell_cd_responsavel_{$referencia}'     value='{$cd_responsavel}'/>";
                            $input .= "<input type='hidden' id='cell_nm_responsavel_estabelecimento_{$referencia}' value='{$obj->nm_responsavel_estabelecimento}'/>";
                            $input .= "<input type='hidden' id='cell_nr_responsavel_estabelecimento_{$referencia}' value='{$obj->nr_responsavel_estabelecimento}'/>";
                            $input .= "<input type='hidden' id='cell_cn_responsavel_estabelecimento_{$referencia}' value='{$obj->cn_responsavel_estabelecimento}'/>";
                            $input .= "<input type='hidden' id='cell_ds_observacao_{$referencia}'      value='{$obj->ds_observacao}'/>";
                            
                            $editar  = "<a id='licenca_funcionamento_{$referencia}' href='javascript:preventDefault();' onclick='EditarLicencaFuncionamento( this.id )'>  " . $obj->nr_exercicio . "/" . str_pad($obj->nr_licenca, 5, "0", STR_PAD_LEFT) . "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'></i>";
                            $excluir = "<a id='excluir_licenca_funcionamento_{$referencia}' href='javascript:preventDefault();' onclick='ExcluirLicencaFuncionamento( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
                            
                            if ((int)$obj->tp_situacao === $_SITUACAO_LICENCA_APROVADA) {
                                $ativo = "<a href='#'><i id='img_ativo_{$referencia}' class='fa fa-check-square-o' title='{$_SITUACAO_LICENCA[(int)$obj->tp_situacao]}'>&nbsp;{$input}</i></a>";
                            } else {
                                $ativo = "<a href='#'><i id='img_ativo_{$referencia}' class='fa fa-circle-thin' title='{$_SITUACAO_LICENCA[(int)$obj->tp_situacao]}'>&nbsp;{$input}</i></a>";
                            }

                            $html .= "    <tr id='linha_{$referencia}'>"; // Para identificar a linha da tabela
                            $html .= "        <td>" . $editar . "</td>";
                            $html .= "        <td>" . $obj->nr_processo . "</td>";
                            $html .= "        <td>" . $obj->nm_razao . "</td>";
                            $html .= "        <td>" . $nr_cnpj . "</td>";
                            $html .= "        <td>" . $dt_validade[2] . "/" . $dt_validade[1] . "/" . $dt_validade[0] . "</td>";
                            $html .= "        <td><div id='situacao_{$referencia}'>" . $opcoes . "&nbsp;&nbsp;<strong>" . $_SITUACAO_LICENCA[intval($obj->tp_situacao)] . "</strong></div></td>";
                            $html .= "        <td align=center>" . $ativo   . "</td>";
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
            
                case 'carregar_eventos_licenca_funcionamento' : {
                    try {
                        $id_licenca = trim(filter_input(INPUT_POST, 'id_licenca'));

                        $html  = "<table id='tb_eventos_licenca' class='table table-hover bg-aqua' width='100%'>";
                        $html .= "<thead>";
                        $html .= "    <tr>";
                        $html .= "        <th>Data</th>";
                        $html .= "        <th>Descrição</th>";
                        $html .= "        <th data-orderable='false'></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
                        $html .= "    </tr>";
                        $html .= "</thead>";
                        $html .= "<tbody>";
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = 
                              "Select "
                            . "  e.*"
                            . "from TBEVENTO e "
                            . "where e.id_licenca = '{$id_licenca}' "
                            . "order by e.dh_evento DESC";

                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $referencia = $obj->id_evento;
                            $referencia = str_replace("{", "", str_replace("}", "", str_replace("-", "", $referencia)));
                            
                            $input  = "<input type='hidden' id='cell_id_evento_licenca_{$referencia}' value='{$obj->id_evento}'/>";
                            $editar = "<a id='evento_licenca_{$referencia}' href='javascript:preventDefault();' onclick='EditarEventoLicencaFuncionamento( this.id )'><i class='fa fa-edit' title='Editar Evento'></i>";
                            
                            $dt_evento  = explode("-", $obj->dt_evento);
                            $tr = "<tr>"
                                . "   <td align='center'>&nbsp;" . $dt_evento[2]  . "/" . $dt_evento[1]  . "/" . $dt_evento[0]  . "&nbsp;</td>"
                                . "   <td align='justify'>" . $obj->ds_evento  . "</td>"
                                . "   <td align='center'>"  . $editar  . "&nbsp;{$input}</td>"
                                . "</tr>";
                            $html .= $tr;
                        }
                         
                        $html .= "</tbody>";
                        $html .= "</table>";
                        
                        echo $html;
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                
                case 'salvar_licenca_funcionamento' : {
                    try {
                        $file = '../logs/licenca_funcionamento_' . $usuario->getToken_id() . '.json';
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        
                        $id_licenca   = trim(filter_input(INPUT_POST, 'id_licenca'));
                        $nr_exercicio = (int)trim("0".filter_input(INPUT_POST, 'nr_exercicio'));
                        $nr_licenca   = (int)trim("0".filter_input(INPUT_POST, 'nr_licenca'));
                        $nr_processo  = trim(filter_input(INPUT_POST, 'nr_processo'));
                        $dt_emissao   = explode("/", trim(filter_input(INPUT_POST, 'dt_emissao')));
                        $dt_validade  = explode("/", trim(filter_input(INPUT_POST, 'dt_validade')));
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                        $nr_cnpj      = preg_replace("/[^0-9]/", "", trim(filter_input(INPUT_POST, 'nr_cnpj')));
                        $nm_razao     = trim(filter_input(INPUT_POST, 'nm_razao'));
//                        $cd_atividade = (int)trim("0".filter_input(INPUT_POST, 'cd_atividade'));
//                        $cd_atividade_secundaria = (int)trim("0".filter_input(INPUT_POST, 'cd_atividade_secundaria'));
                        $cd_atividade = trim(filter_input(INPUT_POST, 'cd_atividade'));
                        $cd_atividade_secundaria = trim(filter_input(INPUT_POST, 'cd_atividade_secundaria'));
                        $cd_categoria = (int)trim("0".filter_input(INPUT_POST, 'cd_categoria'));
                        $ds_autenticacao = "";
                        $tp_situacao        = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tp_situacao')));
                        $sn_licenca_publica = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'sn_licenca_publica')));
                        $cd_responsavel = trim(filter_input(INPUT_POST, 'cd_responsavel'));
                        $nm_responsavel_estabelecimento = trim(filter_input(INPUT_POST, 'nm_responsavel_estabelecimento'));
                        $nr_responsavel_estabelecimento = trim(filter_input(INPUT_POST, 'nr_responsavel_estabelecimento'));
                        $cn_responsavel_estabelecimento = trim(filter_input(INPUT_POST, 'cn_responsavel_estabelecimento'));
                        $ds_observacao  = trim(filter_input(INPUT_POST, 'ds_observacao'));
                        
                        if ($cd_responsavel === getGuidEmpty()) $cd_responsavel = null;
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Verificar se o CNPJ já está cadastrado
                        $sql  = "Select ";
                        $sql .= "   e.* ";
                        $sql .= "from TBESTABELECIMENTO e ";
                        $sql .= "where (e.id_estabelecimento = '{$id_estabelecimento}') ";
                        $sql .= "  or  (e.nr_cnpj = '{$nr_cnpj}') ";

                        $ret = $pdo->query($sql);
                        if (($err = $ret->fetch(PDO::FETCH_OBJ)) !== false) {
                            if ( intval($err->sn_ativo) === 0 ) {
                                echo "O Cadastro do estabelecimento <strong>'{$nm_razao}'</strong> está inativo!";
                            } else {
                                $dao  = Dao::getInstancia();
                                $dh   = $dao->getDataHoraServidor();
                                $us   = $usuario->getId();

                                if ($nr_exercicio === 0) {
                                    $nr_exercicio = intval(date('Y'));
                                }

                                $sql  = "";
                                $sql .= "Select ";
                                $sql .= "     l.* ";
                                $sql .= "   , e.nr_cnpj ";
                                $sql .= "from TBLICENCA_FUNCIONAMENTO l ";
                                $sql .= "  inner join TBESTABELECIMENTO e on (e.id_estabelecimento = l.id_estabelecimento) ";
                                $sql .= "where (l.id_licenca  <> '{$id_licenca}') ";
                                $sql .= "  and (l.nr_exercicio = {$nr_exercicio}) ";
                                $sql .= "  and (l.id_estabelecimento = '{$id_estabelecimento}') ";
                                $sql .= "  and (l.tp_situacao not in ({$_SITUACAO_LICENCA_VENCIDA}, {$_SITUACAO_LICENCA_CANCELADA}))";

                                $reg = $pdo->query($sql);
                                if (($obj = $reg->fetch(PDO::FETCH_OBJ)) !== false) {
                                    if (strlen($nr_cnpj) > 11) {
                                        $nr_cnpj = formatarTexto('##.###.###/####-##', $obj->nr_cnpj);
                                    } else {
                                        $nr_cnpj = formatarTexto('###.###.###-##', $obj->nr_cnpj);
                                    }
                                    $nr_controle = $obj->nr_exercicio . "/" . str_pad($obj->nr_licenca, 5, "0", STR_PAD_LEFT);
                                    
                                    echo "Já existe um regitro para Pedido de Licença de Funcionamento para o estabelecimento '{$nr_cnpj}' gravado sobre o controle de Número '{$nr_controle}'.";
                                    exit;
                                }
                                
                                $sql  = "";
                                $sql .= "Select ";
                                $sql .= "   l.* ";
                                $sql .= "from TBLICENCA_FUNCIONAMENTO l ";
                                $sql .= "where (l.id_licenca = '{$id_licenca}') ";

                                $res = $pdo->query($sql);

                                if ($nr_licenca === 0) {
                                    $dao = Dao::getInstancia();
                                    $nr_licenca = $dao->getGeneratorID('GEN_LICENCA_FUNCIONAMENTO_' . $nr_exercicio);
                                }

                                if ($nr_processo === "") {
                                    $dao = Dao::getInstancia(); 
                                    $nr_processo = str_pad($dao->getGeneratorID('GEN_PROCESSO_' . $nr_exercicio), 6, "0", STR_PAD_LEFT) . "/" . $nr_exercicio;
                                }
                                
                                if ((int)$cd_atividade_secundaria === 0) $cd_atividade_secundaria = null;
                                
                                // Gravar Dados (Update)
                                if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                                    $hash = encript($id_licenca . $dh . $us);
                                    $ds_autenticacao = encript($id_licenca . $id_estabelecimento, $dt_validade[0] . "/" . $dt_validade[1] . "/" . $dt_validade[2]);
                                    $stm = $pdo->prepare(
                                          'Update TBLICENCA_FUNCIONAMENTO l Set '
                                        . '    l.nr_processo = :nr_processo '
                                        . '  , l.dt_emissao  = :dt_emissao  '
                                        . '  , l.dt_validade = :dt_validade '
                                        . '  , l.id_estabelecimento = :id_estabelecimento '
                                        . '  , l.cd_atividade    = :cd_atividade    '
                                        . '  , l.cd_atividade_secundaria = :cd_atividade_secundaria '
                                        . '  , l.cd_categoria    = :cd_categoria    '
                                        . '  , l.tp_situacao     = :tp_situacao     '
                                        . '  , l.sn_licenca_publica = :sn_licenca_publica '    
                                        . '  , l.cd_responsavel     = :cd_responsavel  '
                                        . '  , l.nm_responsavel_estabelecimento  = :nm_responsavel_estabelecimento  '
                                        . '  , l.nr_responsavel_estabelecimento  = :nr_responsavel_estabelecimento  '
                                        . '  , l.cn_responsavel_estabelecimento  = :cn_responsavel_estabelecimento  '
                                        . '  , l.ds_observacao   = :ds_observacao   '
                                        . '  , l.ds_autenticacao = :ds_autenticacao '
                                        . 'Where l.id_licenca    = :id_licenca ');
                                    $stm->execute(array(
                                        ':id_licenca'  => $id_licenca,
                                        ':nr_processo' => $nr_processo,
                                        ':dt_emissao'  => ($dt_emissao[2]  . "-" . $dt_emissao[1]  . "-" . $dt_emissao[0]),
                                        ':dt_validade' => ($dt_validade[2] . "-" . $dt_validade[1] . "-" . $dt_validade[0]),
                                        ':id_estabelecimento' => $id_estabelecimento,
                                        ':cd_atividade'    => $cd_atividade,
                                        ':cd_atividade_secundaria' => $cd_atividade_secundaria,
                                        ':cd_categoria'    => $cd_categoria,
                                        ':tp_situacao'     => $tp_situacao,
                                        ':sn_licenca_publica' => $sn_licenca_publica,
                                        ':cd_responsavel'     => $cd_responsavel,
                                        ':nm_responsavel_estabelecimento'  => $nm_responsavel_estabelecimento,
                                        ':nr_responsavel_estabelecimento'  => $nr_responsavel_estabelecimento,
                                        ':cn_responsavel_estabelecimento'  => $cn_responsavel_estabelecimento,
                                        ':ds_autenticacao' => $ds_autenticacao,
                                        ':ds_observacao'   => $ds_observacao
                                    ));

                                    $pdo->commit();
                                // Gravando Dados (Insert)
                                } else {
                                    $dao = Dao::getInstancia();
                                    $id_licenca = $dao->getGuidIDFormat();
                                    $hash = encript($id_licenca . $dh . $us);
                                    $ds_autenticacao = encript($id_licenca . $id_estabelecimento, $dt_validade[0] . "/" . $dt_validade[1] . "/" . $dt_validade[2]);

                                    $stm = $pdo->prepare(
                                          'Insert Into TBLICENCA_FUNCIONAMENTO ( '
                                        . '    id_licenca   '
                                        . '  , nr_exercicio '
                                        . '  , nr_licenca   '
                                        . '  , nr_processo  '
                                        . '  , dt_emissao   '
                                        . '  , dt_validade  '
                                        . '  , dt_aprovacao '
                                        . '  , id_estabelecimento '
                                        . '  , cd_atividade    '
                                        . '  , cd_atividade_secundaria '
                                        . '  , cd_categoria    '
                                        . '  , tp_situacao     '
                                        . '  , sn_licenca_publica '
                                        . '  , cd_responsavel     '
                                        . '  , nm_responsavel_estabelecimento '
                                        . '  , nr_responsavel_estabelecimento '
                                        . '  , cn_responsavel_estabelecimento '
                                        . '  , ds_observacao   '
                                        . '  , ds_autenticacao '
                                        . '  , us_cadastro   '
                                        . '  , dh_cadastro   '
                                        . '  , hash_cadastro '
                                        . ') values ( '
                                        . '    :id_licenca   '
                                        . '  , :nr_exercicio '
                                        . '  , :nr_licenca   '
                                        . '  , :nr_processo  '
                                        . '  , :dt_emissao   '
                                        . '  , :dt_validade  '
                                        . '  , null '
                                        . '  , :id_estabelecimento '
                                        . '  , :cd_atividade    '
                                        . '  , :cd_atividade_secundaria '
                                        . '  , :cd_categoria    '
                                        . '  , :tp_situacao     '
                                        . '  , :sn_licenca_publica '
                                        . '  , :cd_responsavel     '
                                        . '  , :nm_responsavel_estabelecimento '
                                        . '  , :nr_responsavel_estabelecimento '
                                        . '  , :cn_responsavel_estabelecimento '
                                        . '  , :ds_observacao   '
                                        . '  , :ds_autenticacao '
                                        . '  , :us_cadastro   '
                                        . '  , :dh_cadastro   '
                                        . '  , :hash_cadastro '
                                        . ')');
                                    $stm->execute(array(
                                        ':id_licenca'   => $id_licenca  ,
                                        ':nr_exercicio' => $nr_exercicio,
                                        ':nr_licenca'   => $nr_licenca  ,
                                        ':nr_processo'  => $nr_processo ,
                                        ':dt_emissao'   => ($dt_emissao[2]  . "-" . $dt_emissao[1]  . "-" . $dt_emissao[0]),
                                        ':dt_validade'  => ($dt_validade[2] . "-" . $dt_validade[1] . "-" . $dt_validade[0]),
                                        ':id_estabelecimento' => $id_estabelecimento,
                                        ':cd_atividade'    => $cd_atividade,
                                        ':cd_atividade_secundaria' => $cd_atividade_secundaria,
                                        ':cd_categoria'    => $cd_categoria,
                                        ':tp_situacao'     => $tp_situacao ,
                                        ':sn_licenca_publica' => $sn_licenca_publica,
                                        ':cd_responsavel'  => $cd_responsavel ,
                                        ':nm_responsavel_estabelecimento'  => $nm_responsavel_estabelecimento,
                                        ':nr_responsavel_estabelecimento'  => $nr_responsavel_estabelecimento,
                                        ':cn_responsavel_estabelecimento'  => $cn_responsavel_estabelecimento,
                                        ':ds_autenticacao' => $ds_autenticacao,
                                        ':ds_observacao'   => $ds_observacao  ,
                                        ':us_cadastro'     => $us  ,
                                        ':dh_cadastro'     => $dh  ,
                                        ':hash_cadastro'   => $hash              
                                    ));

                                    $pdo->commit();
                                    setEventoLicenca($usuario, "Pedido de Licença de Funcionamento referente ao processo No. {$nr_processo} cadastrado por '{$usuario->getLogin()}'", $id_estabelecimento, $id_licenca);
                                }

                                $registros = array('formulario' => array());

                                $registros['formulario'][0]['id_licenca']   = $id_licenca;
                                $registros['formulario'][0]['nr_processo']  = $nr_processo;
                                $registros['formulario'][0]['nr_exercicio'] = $nr_exercicio;
                                $registros['formulario'][0]['nr_licenca']   = $nr_licenca;
                                $registros['formulario'][0]['tp_situacao']  = $tp_situacao;
                                $registros['formulario'][0]['sn_licenca_publica']  = $sn_licenca_publica;

                                $json = json_encode($registros);
                                file_put_contents($file, $json);
                                echo "OK";
                            }
                        } else {
                            echo "Estabelecimento com o CNPJ <strong>'" . formatarTexto("##.###.###/####-##", $nr_cnpj) . "'</strong> não está cadastrado!";
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                    
                } break;
            
                case 'excluir_licenca_funcionamento' : {
                    try {
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                        $id_licenca  = trim(filter_input(INPUT_POST, 'id_licenca'));
                        $nr_processo = trim(filter_input(INPUT_POST, 'nr_processo'));
                        $tp_situacao = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tp_situacao')));
                        
                        if ( $tp_situacao > $_SITUACAO_LICENCA_AGUARDANDO ) {
                            echo "Este registro de Licença de Funcionamento está com a situação '{$_SITUACAO_LICENCA[$tp_situacao]}' e não poderá ser excluído!";
                        } else {
                            $cnf = Configuracao::getInstancia();
                            $pdo = $cnf->db('', '');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stm = $pdo->prepare('Delete from TBEVENTO Where id_licenca = :id_licenca');
                            $stm->execute(array(
                                ':id_licenca' => $id_licenca
                            ));
                            $pdo->commit();

                            $stm = $pdo->prepare('Delete from TBLICENCA_FUNCIONAMENTO Where id_licenca = :id_licenca');
                            $stm->execute(array(
                                ':id_licenca' => $id_licenca
                            ));
                            $pdo->commit();

                            // Atualizar Contadores
                            $nr_exercicio = explode("/", $nr_processo);
                            $stm = $pdo->prepare('Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(:nr_exercicio)');
                            $stm->execute(array(
                                ':nr_exercicio' => $nr_exercicio[1]
                            ));
                            $pdo->commit();
                            
                            $stm = $pdo->prepare('Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(:nr_processo)');
                            $stm->execute(array(
                                ':nr_processo' => $nr_processo
                            ));
                            $pdo->commit();
                            
                            echo "OK";
                            setEventoLicenca($usuario, "Exclusão de registro de Licença de Funcionamento referente ao processo No. {$nr_processo}", $id_estabelecimento, "");
                        }
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                  
                case 'situacao_licenca_funcionamento' : {
                    try {
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_estabelecimento'));
                        $id_licenca  = trim(filter_input(INPUT_POST, 'id_licenca'));
                        $nr_processo = trim(filter_input(INPUT_POST, 'nr_processo'));
                        $tp_situacao = (int)preg_replace("/[^0-9]/", "", "0".trim(filter_input(INPUT_POST, 'tp_situacao')));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   l.* ";
                        $sql .= "from TBLICENCA_FUNCIONAMENTO l ";
                        $sql .= "where (l.id_licenca = '{$id_licenca}') ";

                        $res = $pdo->query($sql);
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            // Licença cancelada não pode ser alterada
                            if (intval($obj->tp_situacao) === $_SITUACAO_LICENCA_CANCELADA) {
                                echo "A situação licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> não poderá ser alterada por está <strong>{$_SITUACAO_LICENCA[intval($obj->tp_situacao)]}</strong>!";
                            } else
                            // Licença vencida não pode ser alterada
                            if (intval($obj->tp_situacao) === $_SITUACAO_LICENCA_VENCIDA) {
                                echo "A situação licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> não poderá ser alterada por está <strong>{$_SITUACAO_LICENCA[intval($obj->tp_situacao)]}</strong>!";
                            } else
                            // Licença cancelada não pode ser alterada
                            if ((intval($obj->tp_situacao) > $tp_situacao) && ($tp_situacao !== $_SITUACAO_LICENCA_APROVADA)) {
                                echo "A situação licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> não poderá ser alterada por está <strong>{$_SITUACAO_LICENCA[intval($obj->tp_situacao)]}</strong>!";
                            } else
                            // Licença já na situação informada
                            if (intval($obj->tp_situacao) === $tp_situacao) {
                                echo "A situação licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> já está <strong>{$_SITUACAO_LICENCA[intval($obj->tp_situacao)]}</strong>!";
                            } else {
                                $cnf = Configuracao::getInstancia();
                                $pdo = $cnf->db('', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $stm = $pdo->prepare(
                                      'Update TBLICENCA_FUNCIONAMENTO l set '
                                    . '    l.dt_aprovacao = :dt_aprovacao '
                                    . '  , l.tp_situacao  = :tp_situacao  '
                                    . 'Where id_licenca   = :id_licenca   ');
                                $stm->execute(array(
                                    ':id_licenca'   => $id_licenca,
                                    ':tp_situacao'  => $tp_situacao,
                                    ':dt_aprovacao' => ($tp_situacao === $_SITUACAO_LICENCA_APROVADA?date("Y-m-d"):null)
                                ));
                                $pdo->commit();

                                echo "OK";
                                setEventoLicenca($usuario, "Situação do registro de Licença de "
                                    . "Funcionamento referente ao processo No. {$nr_processo} "
                                    . "alterada para '{$_SITUACAO_LICENCA[$tp_situacao]}' pelo usuário "
                                    . "'{$usuario->getLogin()}'.", $id_estabelecimento, $id_licenca);
                            }
                        } else {
                            echo "Licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> não localizada!";
                        }    
                    } catch (Exception $ex) {
                        echo $ex . "<br><br>" . $ex->getMessage();
                    } 
                } break;
                
                case 'salvar_evento_licenca' : {
                    try {
                        $id_estabelecimento = trim(filter_input(INPUT_POST, 'id_evento_estabelecimento'));
                        $id_licenca  = trim(filter_input(INPUT_POST, 'id_evento_licenca'));
                        $nr_processo = trim(filter_input(INPUT_POST, 'id_evento_processo'));
                        $dt_evento   = explode("/", trim(filter_input(INPUT_POST, 'dt_evento')));
                        $ds_evento   = trim(filter_input(INPUT_POST, 'ds_evento'));
                        $id_tecnico  = trim(filter_input(INPUT_POST, 'id_tecnico'));
                        
                        $cnf = Configuracao::getInstancia();
                        $pdo = $cnf->db('', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $sql  = "";
                        $sql .= "Select ";
                        $sql .= "   l.* ";
                        $sql .= "from TBLICENCA_FUNCIONAMENTO l ";
                        $sql .= "where (l.id_licenca = '{$id_licenca}') ";

                        $res = $pdo->query($sql);
                        
                        if (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $sql  = "";
                            $sql .= "Select ";
                            $sql .= "   t.* ";
                            $sql .= "from TBTECNICO t ";
                            $sql .= "where (t.id_tecnico = '{$id_tecnico}') ";
                            
                            $tec = $pdo->query($sql);
                            if (($resp = $tec->fetch(PDO::FETCH_OBJ)) !== false) {
                                if (intval($resp->sn_ativo) === 1) {
                                    $dao = Dao::getInstancia();
                                    $id  = $dao->getGuidIDFormat();
                                    $dt  = $dao->getDataServidor();
                                    $dh  = $dao->getDataHoraServidor();
                                    $us  = $usuario->getId();

                                    $hash = encript($id . $dh . $us);

                                    $stm = $pdo->prepare(
                                          'Insert Into TBEVENTO ('
                                        . '    id_evento '
                                        . '  , dt_evento '
                                        . '  , dh_evento '
                                        . '  , ds_evento '
                                        . '  , id_usuario '
                                        . '  , id_estabelecimento '
                                        . '  , id_licenca '
                                        . '  , hash_evento '
                                        . ') values ( '
                                        . '    :id_evento '
                                        . '  , :dt_evento '
                                        . '  , :dh_evento '
                                        . '  , :ds_evento '
                                        . '  , :id_usuario '
                                        . '  , :id_estabelecimento '
                                        . '  , :id_licenca '
                                        . '  , :hash_evento '
                                        . ')');
                                    $stm->execute(array(
                                        ':id_evento'  => $id,
                                        ':dt_evento'  => ($dt_evento[2]  . "-" . $dt_evento[1]  . "-" . $dt_evento[0]),
                                        ':dh_evento'  => $dh,
                                        ':ds_evento'  => $resp->nm_tecnico . " - " . $ds_evento,
                                        ':id_usuario' => $us,
                                        ':id_estabelecimento' => $id_estabelecimento,
                                        ':id_licenca'  => (trim($id_licenca) === ""?null:$id_licenca),
                                        ':hash_evento' => $hash,
                                    ));

                                    $pdo->commit();
                                    echo "OK";
                                } else {
                                    echo "O técnico <strong>{$resp->nm_tecnico}</strong> não está ativo no sistema.<br>Favor comunicar à Coordenação!";
                                }
                            } else {
                                echo "Registro técnico <strong>{$id_tecnico}</strong> não localizado!";
                            }
                        } else {
                            echo "Licença <strong>{$id_licenca}</strong> referente ao processo No. <strong>{$nr_processo}</strong> não localizada!";
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
    
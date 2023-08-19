<!DOCTYPE html>
<?php
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

    $desktop = "";
    $home = new Constantes();
    
    // Verificar o Token de segurança
    $token = filter_input(INPUT_POST, 'token');

    if ( $token !== $usuario->getToken_id() ) {
        $funcao = new Constantes();
        echo $funcao->message_alert("TokenID de segurança inválido!");
        exit;
    }
  
    $exercicio_padrao = date("Y");
    $estado_padrao    = "0";
    $cidade_padrao    = "0";
    $data_padrao_server = "";
        
    // Buscar Estado e Cidade padrões para Pesquisa

    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "Select "
         . "    coalesce(c.cd_estado, 0) as cd_estado "
         . "  , coalesce(c.cd_cidade, 0) as cd_cidade "
         . "from SYS_CONFIGURACAO c "
         . "where c.cd_configuracao = {$usuario->getSistema()} ";

    $res = $pdo->query($sql);
    if ((($obj = $res->fetch(PDO::FETCH_OBJ))) !== false) {
      $estado_padrao = $obj->cd_estado;
      $cidade_padrao = $obj->cd_cidade;
    }    
    
    $dao  = Dao::getInstancia();
    $data = explode("-", $dao->getDataServidor());
    $data_padrao_server = $data[2] . "/" . $data[1] . "/" . $data[0];
    
?>
<html>
    <head>
        <?php
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $erros = "";
        
        $dt_hoje = date("d/m/Y");
        
        // Emissão de Licença de Funcionamento
        $id_licenca          = getGuidEmpty();
        $nr_exercicio        = ""; // date("Y");
        $nr_licenca          = "";
        $nr_processo         = "";
        $dt_emissao          = date("d/m/Y");
        $dt_validade         = "";
        $id_estabelecimento_licenca = getGuidEmpty();
        $cd_atividade        = "0";
        $cd_atividade_secundaria = "0";
        $cd_categoria        = "0";
        $ds_autenticacao     = "";
        $tp_situacao         = "0";
        $sn_licenca_publica  = "0";
        $cd_responsavel      = getGuidEmpty();
        $nm_responsavel_estabelecimento = "";
        $nr_responsavel_estabelecimento = "";
        $cn_responsavel_estabelecimento = "";
        $ds_observacao       = "";
        $nr_cnpj_licenca     = "";
        $nm_razao_licenca    = "";
        $nm_fantasia_licenca = "";

        // Cadastro do Estabelecimento
        $id_estabelecimento = getGuidEmpty();
        $tp_pessoa          = "1"; // Pessoa Jurídica
        $nm_razao           = "";
        $nm_fantasia        = "";
        $nr_cnpj            = "";
        $nr_insc_est        = "";
        $nr_insc_mun        = "";
        $cd_cnae_principal  = "0";
        $cd_cnae_secundaria = "0";
        $sn_orgao_publico   = "0";
        $sn_ativo           = "0";
        $tp_endereco        = "0";
        $ds_endereco        = "";
        $nr_endereco        = "";
        $ds_complemento     = "";
        $cd_bairro          = "0";
        $nr_cep             = "";
        $cd_cidade          = "0";
        $cd_uf              = "";
        $cd_estado          = "0";
        $nr_comercial       = "";
        $ds_email           = "";
        $nm_contato         = "";
        $nr_telefone        = "";
        $nr_celular         = "";
        
        // Registro de Eventos
        $dt_evento  = date('d/m/Y');
        $id_tecnico = getGuidEmpty();
        $ds_evento  = "";
        
        $dominio = $_SERVER['HTTP_HOST'];
        $url = $dominio. $_SERVER['REQUEST_URI'];
        $GLOBALS['url'] = $url;
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Personal Management | Licenças de Funcionamento</title>
        <link rel="shortcut icon" href="icon.ico" >
         <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.4 -->
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="../plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- iCheck for checkboxes and radio inputs -->
        <link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Color Picker -->
        <link href="../plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap time Picker -->
        <link href="../plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Select2 -->
        <link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link href="../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="hold-transition skin-blue layout-top-nav">
        <style>
            #painel_cadastro{
                display:none;
                position:absolute;
                padding-left: 10%;
                padding-right: 10%;
                padding-top: 20px;
                padding-bottom: 40%;
            }
            #painel_cadastro_estabelecimento{
                display:none;
                position:absolute;
                padding-left: 10%;
                padding-right: 10%;
                padding-top: 20px;
                padding-bottom: 40%;
            }
            #painel_cadastro_evento{
                display:none;
                position:absolute;
                padding-left: 10%;
                padding-right: 10%;
                padding-top: 20px;
                padding-bottom: 40%;
            }
            #painel_wait_main{
                display:none;
                position:absolute;
                padding-left: 25%;
                padding-right: 25%;
                padding-top: 150px;
                padding-bottom: 40%;
            }
            #painel_informe{
                display:none;
                position:absolute;
                padding-left: 25%;
                padding-right: 25%;
                padding-top: 150px;
                padding-bottom: 40%;
            }
            #painel_alerta{
                display:none;
                position:absolute;
                padding-left: 25%;
                padding-right: 25%;
                padding-top: 150px;
                padding-bottom: 40%;
            }
            #painel_erro{
                display:none;
                position:absolute;
                padding-left: 25%;
                padding-right: 25%;
                padding-top: 150px;
                padding-bottom: 40%;
            }
            #painel_selecionar_estabelecimento{
                display:none;
                position:absolute;
                padding-left: 10%;
                padding-right: 10%;
                padding-top: 80px;
                padding-bottom: 40%;
            }
            /* Centralizar na verticação as células de tabelas renderizadas pela classe "dataTable()"*/
            table.dataTable tbody td {
                vertical-align: middle;
            }            
        </style>

        <?php
        // put your code here
        $acao = '';
        ?>

        <div id='painel_controle'>
            <section class='content-header'>
                    <h1>
                        Licenças de Funcionamento
                        <small>Painel de Controle</small>
                    </h1>
                <ol class='breadcrumb'>
                    <li onclick='HomeDesktop()'><a href='#'><i class='fa fa-home'></i>Home</a></li>
                    <li class='active'><a href='#'>Emissão de Documentos</a></li>
                    <li class='active'><a href='#'>Licenças de Funcionamento</a></li>
                </ol>
            </section>
            
            <!-- Main content -->
            <section class='content'>
 
                <!-- Pesquisar -->
                <div class="box box-info">

                    <!-- Definição da Ação e do Token de Segurança -->
                    <input type="hidden" id="ac"      name="ac"      value="pesquisar_licenca_funcionamento"/>                     
                    <input type="hidden" id="token"   name="token"   value="<?php echo $usuario->getToken_id(); ?>"/>
                    <input type="hidden" id="dt_hoje" name="dt_hoje" value="<?php echo $dt_hoje;?>">
                    <input type="hidden" id="nr_exercicio_padrao" name="nr_exercicio_padrao" value="<?php echo $exercicio_padrao; ?>"/>
                    <input type="hidden" id="cd_estado_padrao"    name="cd_estado_padrao" value="<?php echo $estado_padrao; ?>"/>
                    <input type="hidden" id="cd_cidade_padrao"    name="cd_cidade_padrao" value="<?php echo $cidade_padrao; ?>"/>

                    <div class="box-header with-border">
                        <h3 class="box-title">Dados para pesquisa</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" data-widget="remove" title="Fechar" id="btn_fechar_pesq" onclick="HomeDesktop()"><i class="fa fa-remove"></i></button>
                        </div>
                    </div><!-- /.box-header -->

                    <div class='box-body' id='form_pesquisa_campos'>
                      <div class='row'>

                        <div class='col-md-6'>
                            <table border="0" width="100%">
                                <tr>
                                    <td width="15%">
                                        <div class='form-group'>
                                            <label>Exercício</label>
                                            <select class='form-control select2' name='ano_exercicio' id='ano_exercicio'>
                                                <option value="0" <?php echo getTagSelected("0", $exercicio_padrao)?> >Todos</option>
                                                <?php
                                                    $cnf = Configuracao::getInstancia();
                                                    $pdo = $cnf->db('', '');
                                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                    $sql = "Select first 1 "
                                                         . "    extract(year from current_date) as nr_exercicio "
                                                         . "from SYS_SISTEMA "
                                                         . " "
                                                         . "Union "
                                                         . " "
                                                         . "Select "
                                                         . "    l.nr_exercicio "
                                                         . "from TBLICENCA_FUNCIONAMENTO l "
                                                         . "where l.nr_exercicio <> extract(year from current_date) "
                                                         . "group by "
                                                         . "    l.nr_exercicio "
                                                         . " "
                                                         . "order by 1 desc ";

                                                    $res = $pdo->query($sql);

                                                    while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                        $selecionar = getTagSelected($obj->nr_exercicio, $exercicio_padrao);
                                                        echo "<option value='" . $obj->nr_exercicio . "' " . $selecionar . ">" . $obj->nr_exercicio . "</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td width="84%">
                                        <div class='form-group'>
                                            <label>Tipo da pesquisa</label>
                                            <select class='form-control select2' name='tipo_pesquisa' id='tipo_pesquisa'>
                                                <option value='0' selected='selected'>Todas as Licenças de Funcionamento</option>
                                                <option value='1'>Apenas as Licenças de Funcionamento Pendentes</option>
                                                <option value='2'>Apenas as Licenças de Funcionamento Ativas</option>
                                                <option value='3'>Apenas as Licenças de Funcionamento Vendidas</option>
                                                <option value='4'>Apenas as Licenças de Funcionamento Suspensas</option>
                                                <option value='5'>Número de Controle</option>
                                                <option value='6'>Número do Processo</option>
                                                <option value='7'>Nome do Estabelecimento</option>
                                                <option value='8'>CNPJ do Estabelecimento</option>
                                            </select>
                                        </div><!-- /.form-group -->
                                    </td>
                                </tr>
                            </table>
                        </div><!-- /.col -->

                        <div class='col-md-6'>
                            <div class='form-group' id='inputbox_pesquisa'>
                                <label>Pesquisa</label>
                                <input type='text' name='pesquisa' id='pesquisa' class='form-control' placeholder='Informe o dado para pesquisa ...'/>
                            </div><!-- /.form-group -->
                        </div><!-- /.col -->

                      </div><!-- /.row -->
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button class="btn btn-primary" id="btn_pesquisar" name="btn_pesquisar" onclick="PesquisarLicencaFuncionamento()" title="Executar Pesquisa"><i class="fa fa-search"></i></button>
                        <button class="btn btn-primary" id="btn_limpar_pq" name="btn_limpar_pq" onclick="PrepararPesquisaLicencaFuncionamento()" title="Preparar para nova Pesquisa"><i class="fa  fa-eraser" ></i></button>
                        <button class="btn btn-primary" id="btn_novo"      name="btn_novo"      onclick="NovaLicencaFuncionamento()" title="Nova Licença de Fincionamento"><i class="fa fa-file-o"></i></button>
<!--                        
                        <div class='btn-group'>
                            <button type='button' class='btn btn-primary'><i class='fa fa-edit' title='Mais Opções'></i></button>
                            <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>
                                <span class='caret'></span>
                                <span class='sr-only'></span>
                            </button>
                            <ul class='dropdown-menu' role='menu'>
                                <li><a href='#'><i class='fa fa-calendar'></i> Aguardar</a></li>
                                <li><a href='#'><i class='fa fa-check-square-o'></i> Aprovar</a></li>
                                <li><a href='#'><i class='fa fa-circle-thin'></i> Suspender</a></li>
                                <li class='divider'></li>
                                <li><a href='#'><i class='fa fa-bell-o'></i> Registrar Eventos</a></li>
                            </ul>
                        </div>
                        -->
                    </div>

                </div><!-- /.box -->

                <!-- Resultado da Pesquisa -->
                <div id="resultado_pesquisa">
                    <!-- Tabela -->
                </div>
                        
            </section>
        </div>
        
        <!-- Painel estilo "Modal Dialog" para "Cadastro" -->
        <div id="painel_cadastro" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_cadastro').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Licença de Funcionamento</h4>
                    </div>
                    <div class="modal-body">
                        <table border="0" width="100%">
                            <tr>
                                <td><label>Nro. Processo</label></td>
                                <td>&nbsp;</td>
                                <td><label>Exercício</label></td>
                                <td>&nbsp;</td>
                                <td><label>Controle</label></td>
                                <td>&nbsp;</td>
                                <td><label>Emissão</label></td>
                                <td>&nbsp;</td>
                                <td><label>Validade</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" id="id_licenca"  name="id_licenca" value="<?php echo $id_licenca;?>" >
                                    <input type="hidden" id="tp_situacao" name="tp_situacao" value="<?php echo $tp_situacao;?>" >
                                    <input type="hidden" id="sn_licenca_publica" name="sn_licenca_publica" value="<?php echo $sn_licenca_publica;?>" >
                                    <input type="text" class="form-control" id="nr_processo" value="<?php echo $nr_processo;?>" maxlength="11"  style="width:140px;" onkeypress="formatar('######/####', this)">
                                </td>
                                <td>&nbsp;</td>
                                <td><input type="text" class="form-control" id="nr_exercicio" value="<?php echo $nr_exercicio;?>" maxlength="4" style="width:75px" disabled></td>
                                <td>&nbsp;</td>
                                <td><input type="text" class="form-control" id="nr_licenca" value="<?php echo $nr_licenca;?>" maxlength="10" style="width:77px" disabled></td>
                                <td>&nbsp;</td>
                                <td>
                                    <div class='input-group'>
                                        <div class='input-group-addon'>
                                            <i class='fa fa-calendar'></i>
                                        </div>
                                        <input type="text" class="form-control" id="dt_emissao" value="<?php echo $dt_emissao;?>" maxlength="10" OnKeyPress="formatar('##/##/####', this)">
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>
                                    <div class='input-group'>
                                        <div class='input-group-addon'>
                                            <i class='fa fa-calendar'></i>
                                        </div>
                                        <input type="text" class="form-control" id="dt_validade" value="<?php echo $dt_validade;?>" maxlength="10" OnKeyPress="formatar('##/##/####', this)" disabled>
                                        <input type="hidden" id="sn_provisoria" value="0"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"><label>Estabelecimento</label></td>
                                <td>&nbsp;</td>
                                <td colspan="5"><label>Razão Social</label></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="input-group">
                                        <input type="hidden" id="id_estabelecimento_licenca" name="id_estabelecimento_licenca" value="<?php echo $id_estabelecimento_licenca;?>" >
                                        <input type="text" class="form-control" id="nr_cnpj_licenca" value="<?php echo $nr_cnpj_licenca;?>" maxlength="18" onkeypress="return SomenteNumero(event);" style="width:140px;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-flat" type="button" id="btn_buscar_estabelecimento" onclick="BuscarEstabelecimentoLicenca()" title="Buscar Estabelecimento"><i class='fa fa-search'></i></button>
                                            <button class="btn btn-info btn-flat" type="button" id="btn_editar_estabelecimento" onclick="EditarEstabelecimentoLicenca(false)" title="Cadastro do Estabelecimento"><i class='fa fa-edit'></i></button>
                                        </span>
                                    </div><!-- /input-group -->
                                </td>
                                <td>&nbsp;</td>
                                <td colspan="5"><input type="text" class="form-control" id="nm_razao_licenca" value="<?php echo $nm_razao_licenca;?>" maxlength="150" disabled></td>
                            </tr>
                            <tr>
                                <td colspan="3"><label>Categoria</label></td>
                                <td>&nbsp;</td>
                                <td colspan="5"><label>Técnico Responsável (Vigilância Sanitária)</label></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <select class='form-control select2' name='cd_categoria' id='cd_categoria' style="width:217px" onchange="javascript: getValidadeCategoriaLicenca($('#nr_exercicio').val(), $('#dt_emissao').val(), this.value);">
                                        <option value="0" <?php echo getTagSelected("0", $cd_categoria)?> >Selecionar Categoria</option>
                                        <?php
                                            $cnf = Configuracao::getInstancia();
                                            $pdo = $cnf->db('', '');
                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                            $sql = "Select "
                                                 . "    c.cd_categoria "
                                                 . "  , c.ds_categoria "
                                                 . "from TBCATEGORIA_LICENCA c "
                                                 . "order by "
                                                 . "    c.ds_categoria ";

                                            $res = $pdo->query($sql);

                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                $selecionar = getTagSelected($obj->cd_categoria, $cd_categoria);
                                                echo "<option value='" . $obj->cd_categoria . "' " . $selecionar . ">" . $obj->ds_categoria . "</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                                <td>&nbsp;</td>
                                <td colspan="5">
                                    <select class='form-control select2' name='cd_responsavel' id='cd_responsavel'>
                                        <option value="<?php echo getGuidEmpty();?>" <?php echo getTagSelected(getGuidEmpty(), $cd_responsavel)?> >Selecionar Técnico</option>
                                        <?php
                                            $cnf = Configuracao::getInstancia();
                                            $pdo = $cnf->db('', '');
                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                            $sql = "Select "
                                                 . "    t.id_tecnico "
                                                 . "  , t.nm_tecnico "
                                                 . "from TBTECNICO t "
                                                 . "order by "
                                                 . "    t.nm_tecnico ";

                                            $res = $pdo->query($sql);

                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                $selecionar = getTagSelected($obj->id_tecnico, $cd_responsavel);
                                                echo "<option value='" . $obj->id_tecnico . "' " . $selecionar . ">" . $obj->nm_tecnico . "</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9" style="font-size:3px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="9" style="font-size:3px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    <div>
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#tab_atividade" data-toggle="tab" id="guia_tab_atividade"><i class="fa fa-archive"></i> Atividade(s)</a></li>
                                                <li><a href="#tab_responsavel" data-toggle="tab" id="guia_tab_responsavel"><i class="fa fa-user-secret"></i> Responsável</a></li>
                                                <li><a href="#tab_observacao"  data-toggle="tab" id="guia_tab_observacao"><i class="fa fa-file-text-o"></i> Observação</a></li>
                                                <li><a href="#tab_ocorrencia"  data-toggle="tab"><i class="fa fa-bell-o" id="guia_tab_ocorrencia"></i> Eventos (Ocorrências)</a></li>
                                            </ul>
                                            <div class="tab-content bg-light-blue">
                                                <div class="tab-pane active" id="tab_atividade">
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td><label>Atividade Principal</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select class='form-control select2' name='cd_atividade' id='cd_atividade'>
                                                                    <option value="0" <?php echo getTagSelected("0", $cd_atividade)?> >Selecionar Atividade Principal</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    c.cd_cnae "
                                                                             . "  , c.ds_cnae "
                                                                             . "from TBCNAE c "
                                                                             . "order by "
                                                                             . "    c.ds_cnae ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_cnae, $cd_atividade);
                                                                            echo "<option value='" . $obj->cd_cnae . "' " . $selecionar . ">" . $obj->ds_cnae . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Atividade Secundária</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select class='form-control select2' name='cd_atividade_secundaria' id='cd_atividade_secundaria'>
                                                                    <option value="0" <?php echo getTagSelected("0", $cd_atividade_secundaria)?> >Selecionar Atividade Secundária</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    c.cd_cnae "
                                                                             . "  , c.ds_cnae "
                                                                             . "from TBCNAE c "
                                                                             . "order by "
                                                                             . "    c.ds_cnae ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_cnae, $cd_atividade_secundaria);
                                                                            echo "<option value='" . $obj->cd_cnae . "' " . $selecionar . ">" . $obj->ds_cnae . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="tab_responsavel">
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td colspan="10"><label>Nome Completo</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="10"><input type="text" class="form-control" id="nm_responsavel_estabelecimento" value="<?php echo $nm_responsavel_estabelecimento;?>" maxlength="150" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Número / Conselho</label></td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class='input-group'>
                                                                    <input type="text" class="form-control" id="nr_responsavel_estabelecimento" value="<?php echo $nr_responsavel_estabelecimento;?>" maxlength="10" style="width:100px;" onkeypress="return SomenteNumero(event);">
                                                                    <div class='input-group-addon'>
                                                                        <i class='fa fa-angle-right'></i>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="cn_responsavel_estabelecimento" value="<?php echo $cn_responsavel_estabelecimento;?>" maxlength="7" style="width:100px;" onkeyup="javascript: this.value = TextoMaiusculo(this);">
                                                                </div>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                        
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="tab_observacao">
                                                    <font color="black">
                                                    <textarea rows="4" cols="86" maxlength="250" id="ds_observacao" style="width:550px; resize: none"><?php echo $ds_observacao?></textarea>
                                                    </font>
                                                </div>
                                                <div class="tab-pane active" id="tab_ocorrencia">
                                                    <div id="div_eventos">
<!--                                                        
                                                        <a id="ancora_eventos"></a>
                                                        <table id="tb_eventos" class="table table-bordered table-hover" width="100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Data</th>
                                                                    <th>Descrição</th>
                                                                    <th>Usuário</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                    <td>...</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarLicencaFuncionamento()" id="btn_salvar"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_cadastro').style.display='none';">Fechar</button>
                        <div id="painel_cadastro_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Cadastro Estabelecimento" -->
        <div id="painel_cadastro_estabelecimento" class="modal modal-info"> <!-- modal-primary-->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_cadastro_estabelecimento').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Cadastro Estabelecimento</h4>
                    </div>
                    <div class="modal-body">
                        <table border="0" width="100%">
                            <tr>
                                <td><label>CPF/CNPJ</label></td>
                                <td>&nbsp;</td>
                                <td><label>RG/Inscrição Estadual</label></td>
                                <td>&nbsp;</td>
                                <td><label>Inscrição Municipal</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" id="id_estabelecimento" name="id_estabelecimento" value="<?php echo $id_estabelecimento;?>" >
                                    <input type="hidden" id="tp_pessoa"          name="tp_pessoa" value="<?php echo $tp_pessoa;?>" >
                                    <input type="text" class="form-control" id="nr_cnpj" value="<?php echo $nr_cnpj;?>" style="width:220px" maxlength="18" onkeypress="return SomenteNumero(event);"> 
                                </td>
                                <td>&nbsp;</td>
                                <td><input type="text" class="form-control" id="nr_insc_est" value="<?php echo $nr_insc_est;?>" maxlength="15"></td>
                                <td>&nbsp;</td>
                                <td><input type="text" class="form-control" id="nr_insc_mun" value="<?php echo $nr_insc_mun;?>" maxlength="10"></td>
                            </tr>
                            <tr>
                                <td colspan="5"><label>Razão Social</label></td>
                            </tr>
                            <tr>
                                <td colspan="5"><input type="text" class="form-control" id="nm_razao" value="<?php echo $nm_razao;?>" maxlength="150" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                            </tr>
                            <tr>
                                <td colspan="5"><label>Nome Fantasia</label></td>
                            </tr>
                            <tr>
                                <td colspan="5"><input type="text" class="form-control" id="nm_fantasia" value="<?php echo $nm_fantasia;?>" maxlength="150" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                            </tr>
                            <tr>
                                <td colspan="5" style="font-size:3px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" style="font-size:3px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div>
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="active" ><a href="#tab_endereco_estab" data-toggle="tab" id="guia_tab_endereco_estab"><i class="fa fa-map-marker"></i> Endereço</a></li>
                                                <li><a href="#tab_atividade_estab" data-toggle="tab" id="guia_tab_atividade_estab"><i class="fa fa-archive"></i> Atividade(s)</a></li>
                                                <li><a href="#tab_contato" data-toggle="tab"><i class="fa fa-mobile" id="guia_tab_contato"></i> Contato</a></li>
                                            </ul>
                                            <div class="tab-content bg-aqua">  <!-- bg-light-blue-->
                                                <div class="tab-pane active" id="tab_endereco_estab">
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td><label>Estado</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Município</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Bairro</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" id="cd_uf" name="cd_uf" value="<?php echo $cd_uf;?>" >
                                                                <select class='form-control select2' name='cd_estado' id='cd_estado' style="width:160px" onchange="select_ListarCidades('cd_estado', 'div_cidade', 'cd_cidade')">
                                                                    <option value="0" <?php echo getTagSelected("0", $cd_estado)?> >Selecionar o Estado</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    e.cd_estado "
                                                                             . "  , e.nm_estado "
                                                                             . "from SYS_ESTADOS e "
                                                                             . "order by "
                                                                             . "    e.nm_estado ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_estado, $cd_estado);
                                                                            echo "<option value='" . $obj->cd_estado . "' " . $selecionar . ">" . $obj->nm_estado . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td>
                                                                <div id="div_cidade">
                                                                    <select class='form-control select2' name='cd_cidade' id='cd_cidade' onchange="select_ListarBairros('cd_cidade', 'div_bairro', 'cd_bairro')">
                                                                        <option value="0" <?php echo getTagSelected("0", $cd_cidade)?> >Selecionar o Município</option>
                                                                        <?php
                                                                            $cnf = Configuracao::getInstancia();
                                                                            $pdo = $cnf->db('', '');
                                                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                            $sql = "Select "
                                                                                 . "    c.cd_cidade "
                                                                                 . "  , c.nm_cidade "
                                                                                 . "  , e.uf_estado "
                                                                                 . "  , c.nm_cidade || ' (' || e.uf_estado || ')' as ds_cidade "
                                                                                 . "from SYS_CIDADES c "
                                                                                 . "  inner join SYS_ESTADOS e on (e.cd_estado = c.cd_estado) "
                                                                                 . ($estado_padrao === "0"?"where c.cd_estado = {$cd_estado} ":"where c.cd_estado = {$estado_padrao} ")
                                                                                 . "order by "
                                                                                 . "    4 ";

                                                                            $res = $pdo->query($sql);

                                                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                                $selecionar = getTagSelected($obj->cd_cidade, $cd_cidade);
                                                                                echo "<option value='" . $obj->cd_cidade . "' " . $selecionar . ">" . $obj->ds_cidade . "</option>";
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td>
                                                                <div id="div_bairro">
                                                                    <select class='form-control select2' name='cd_bairro' id='cd_bairro'>
                                                                        <option value="0" <?php echo getTagSelected("0", $cd_bairro)?> >Selecionar o Bairro</option>
                                                                        <?php
                                                                            $cnf = Configuracao::getInstancia();
                                                                            $pdo = $cnf->db('', '');
                                                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                            $sql = "Select "
                                                                                 . "    b.cd_bairro    "
                                                                                 . "  , b.nm_bairro    "
                                                                                 . "from SYS_BAIRROS b "
                                                                                 . "where b.cd_cidade = {$cd_cidade} "   
                                                                                 . "order by "
                                                                                 . "    b.nm_bairro ";

                                                                            $res = $pdo->query($sql);

                                                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                                $selecionar = getTagSelected($obj->cd_bairro, $cd_bairro);
                                                                                echo "<option value='" . $obj->cd_bairro . "' " . $selecionar . ">" . $obj->nm_bairro . "</option>";
                                                                            }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Tipo Endereço</label></td>
                                                            <td>&nbsp;</td>
                                                            <td colspan="3"><label>Logradouro</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select class='form-control select2' name='tp_endereco' id='tp_endereco' style="width:160px">
                                                                    <option value="0" <?php echo getTagSelected("0", $tp_endereco)?> >Selecionar Tipo</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    t.cd_tipo "
                                                                             . "  , t.cd_descricao "
                                                                             . "  , t.sg_tipo "
                                                                             . "from SYS_TIPO_LOGRADOURO t "
                                                                             . "order by "
                                                                             . "    t.cd_descricao ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_tipo, $tp_endereco);
                                                                            echo "<option value='" . $obj->cd_tipo . "' " . $selecionar . ">" . $obj->cd_descricao . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                            <td>&nbsp;</td>
                                                            <td colspan="3"><input type="text" class="form-control" id="ds_endereco" value="<?php echo $ds_endereco;?>" maxlength="100" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Número</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Complemento</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Cep</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" class="form-control" id="nr_endereco" style="width:160px" value="<?php echo $nr_endereco;?>" maxlength="10" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                                                            <td>&nbsp;</td>
                                                            <td><input type="text" class="form-control" id="ds_complemento" value="<?php echo $ds_complemento;?>" maxlength="50" onkeyup="javascript: this.value = TextoMaiusculo(this);"></td>
                                                            <td>&nbsp;</td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="nr_cep" value="<?php echo $nr_cep;?>" maxlength="10" onkeypress="formatar('##.###-###', this)">
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-info btn-flat" type="button" id="btn_buscar_endereco" onclick="BuscarEnderecoCepEstab()" title="Buscar Endereço"><i class='fa fa-search'></i></button>
                                                                    </span>
                                                                </div><!-- /input-group -->
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="tab_atividade_estab">
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td><label>Atividade Principal (Cnae Principal)</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select class='form-control select2' name='cd_cnae_principal' id='cd_cnae_principal' style="width:550px;">
                                                                    <option value="0" <?php echo getTagSelected("0", $cd_cnae_principal)?> >Selecionar Atividade Principal</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    c.cd_cnae "
                                                                             . "  , c.ds_cnae "
                                                                             . "from TBCNAE c "
                                                                             . "order by "
                                                                             . "    c.ds_cnae ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_cnae, $cd_cnae_principal);
                                                                            echo "<option value='" . $obj->cd_cnae . "' " . $selecionar . ">" . $obj->ds_cnae . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Atividade Secundária (Cnae Secundária)</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select class='form-control select2' name='cd_cnae_secundaria' id='cd_cnae_secundaria' style="width:550px;">
                                                                    <option value="0" <?php echo getTagSelected("0", $cd_cnae_secundaria)?> >Selecionar Atividade Secundária</option>
                                                                    <?php
                                                                        $cnf = Configuracao::getInstancia();
                                                                        $pdo = $cnf->db('', '');
                                                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                                        $sql = "Select "
                                                                             . "    c.cd_cnae "
                                                                             . "  , c.ds_cnae "
                                                                             . "from TBCNAE c "
                                                                             . "order by "
                                                                             . "    c.ds_cnae ";

                                                                        $res = $pdo->query($sql);

                                                                        while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                                            $selecionar = getTagSelected($obj->cd_cnae, $cd_cnae_secundaria);
                                                                            echo "<option value='" . $obj->cd_cnae . "' " . $selecionar . ">" . $obj->ds_cnae . "</option>";
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="tab_contato">
                                                    <table border="0" width="100%">
                                                        <tr>
                                                            <td><label>Telefone Comercial</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Telefone</label></td>
                                                            <td>&nbsp;</td>
                                                            <td><label>Celular</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="text" class="form-control" id="nr_comercial" value="<?php echo $nr_comercial;?>" maxlength="25" onkeypress="formatar('##.####-####/##.####-####', this)"></td>
                                                            <td>&nbsp;</td>
                                                            <td><input type="text" class="form-control" id="nr_telefone"  value="<?php echo $nr_telefone;?>" maxlength="25" onkeypress="formatar('##.####-####/##.####-####', this)"></td>
                                                            <td>&nbsp;</td>
                                                            <td><input type="text" class="form-control" id="nr_celular"   value="<?php echo $nr_celular;?>" maxlength="27" onkeypress="formatar('##.#####-####/##.#####-####', this)"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5"><label>Contato</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5"><input type="text" class="form-control" id="nm_contato" value="<?php echo $nm_contato;?>" maxlength="150"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5"><label>E-mail</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5"><input type="text" class="form-control" id="ds_email" value="<?php echo $ds_email;?>" maxlength="100" onkeyup="javascript: this.value = TextoMinusculo(this);"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><input type="checkbox" id="sn_orgao_publico" value="1" <?php if ($sn_orgao_publico  === '1') {echo "checked";}?> > Órgão Público</label>
                                </td>
                                <td>&nbsp;</td>
                                <td colspan="3">
                                    <label><input type="checkbox" id="sn_ativo" value="1" <?php if ($sn_ativo  === '1') {echo "checked";}?> > Ativo</label>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarEstabelecimentoLicenca()" id="btn_salvar_estabelecimento"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_cadastro_estabelecimento').style.display='none';">Fechar</button>
                        <div id="painel_cadastro_estabelecimento_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Eventos" -->
        <div id="painel_cadastro_evento" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_cadastro_evento').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Registrar Eventos</h4>
                    </div>
                    <div class="modal-body">
                        <table border="0" width="100%">
                            <tr>
                                <td><label>Controle</label></td>
                                <td>&nbsp;</td>
                                <td><label>Estabelecimento</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" id="id_evento_estabelecimento" name="id_evento_estabelecimento" value="" >
                                    <input type="hidden" id="id_evento_licenca"         name="id_evento_licenca"         value="" >
                                    <input type="hidden" id="id_evento_processo"        name="id_evento_processo"        value="" >
                                    <input type="text" class="form-control" id="nr_controle_licenca" value="<?php echo $nr_licenca . "/" . $nr_exercicio;?>" disabled>
                                </td>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="text" class="form-control" id="nm_estabelecimento_licenca" value="<?php echo $nm_razao_licenca;?>" style="width:420px;" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Data</label></td>
                                <td>&nbsp;</td>
                                <td><label>Técnico Responsável</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class='input-group'>
                                        <div class='input-group-addon'>
                                            <i class='fa fa-calendar'></i>
                                        </div>
                                        <input type="text" class="form-control" id="dt_evento" value="<?php echo $dt_evento;?>" maxlength="10" OnKeyPress="formatar('##/##/####', this)">
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>
                                    <select class='form-control select2' name='id_tecnico' id='id_tecnico' style="width:420px;">
                                        <option value="<?php echo getGuidEmpty();?>" <?php echo getTagSelected(getGuidEmpty(), $id_tecnico)?> >Selecionar o Técnico</option>
                                        <?php
                                            $cnf = Configuracao::getInstancia();
                                            $pdo = $cnf->db('', '');
                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                            $sql = "Select "
                                                 . "    t.id_tecnico "
                                                 . "  , t.nm_tecnico "
                                                 . "from TBTECNICO t "
                                                 . "order by "
                                                 . "    t.nm_tecnico ";

                                            $res = $pdo->query($sql);

                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                $selecionar = getTagSelected($obj->id_tecnico, $id_tecnico);
                                                echo "<option value='" . $obj->id_tecnico . "' " . $selecionar . ">" . $obj->nm_tecnico . "</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"><label>Descrição do Evento (Ocorrência)</label></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <font color="black">
                                    <textarea rows="4" cols="86" maxlength="200" id="ds_evento" style="width:570px; resize: none"><?php echo $ds_evento?></textarea>
                                    </font>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarEventoLicencaFuncionamento()"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_cadastro_evento').style.display='none';"><i class="fa fa-close "></i> Fechar</button>
                        <div id="painel_cadastro_evento_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Selecionar Estabelecimento" -->
        <div id="painel_selecionar_estabelecimento" class="modal modal-info">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_selecionar_estabelecimento').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Selecionar Estabelecimento Cadastrado</h4>
                    </div>
                    <div class="modal-body" id="tabela_estabelecimento">
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="EditarEstabelecimentoLicenca(true)">Novo</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_selecionar_estabelecimento').style.display='none';">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
            include('_scripts.php');
        ?>  
        <script type="text/javascript" src="../funcoes.js"></script>
        <script type="text/javascript" src="estabelecimento_controller.js"></script>
        <script type="text/javascript" src="licenca_funcionamento_controller.js"></script>
    </body>
</html>

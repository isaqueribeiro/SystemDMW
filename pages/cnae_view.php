<!DOCTYPE html>
<?php
    require_once '../lib/Constantes.php';
    require_once '../lib/funcoes.php';
    require_once '../lib/classes/usuario.php';
    require_once '../lib/classes/sessao.php';
    require_once '../lib/classes/autenticador.php';
    require_once '../lib/classes/configuracao.php';

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
    
?>
<html>
    <head>
        <?php
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $erros = "";
        
        $id_tecnico = getGuidEmpty();
        $nm_tecnico = "";
        $nr_cpf     = "";
        $sn_ativo   = "0";

        $cd_cnae  = "";
        $tp_secao = "XX";
        $ds_cnae  = "";
        $nm_cnae  = "";
        $sn_obriga_insc_est ="0";
        
        $dominio = $_SERVER['HTTP_HOST'];
        $url = $dominio. $_SERVER['REQUEST_URI'];
        $GLOBALS['url'] = $url;
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Personal Management | CNAEs</title>
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
                padding-top: 150px;
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
        </style>

        <?php
        // put your code here
        $acao = '';
        ?>

        <div id='painel_controle'>
            <section class='content-header'>
                    <h1>
                        CNAEs
                        <small>Painel de Controle</small>
                    </h1>
                <ol class='breadcrumb'>
                    <li onclick='HomeDesktop()'><a href='#'><i class='fa fa-home'></i>Home</a></li>
                    <li class='active'><a href='#'>Tabelas Auxiliares</a></li>
                    <li class='active'><a href='#'>CNAEs</a></li>
                </ol>
            </section>
            
            <!-- Main content -->
            <section class='content'>
 
                <!-- Pesquisar -->
                <div class="box box-info">

                    <!-- Definição da Ação e do Token de Segurança -->
                    <input type="hidden" id="ac"    name="ac"    value="pesquisar_cnae"/>                     
                    <input type="hidden" id="token" name="token" value="<?php echo $usuario->getToken_id(); ?>"/>

                    <div class="box-header with-border">
                        <h3 class="box-title">Dados para pesquisa</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" data-widget="remove" title="Fechar" id="btn_fechar_pesq" onclick="HomeDesktop()"><i class="fa fa-remove"></i></button>
                        </div>
                    </div><!-- /.box-header -->

                    <div class='box-body' id='form_pesquisa_campos'>
                      <div class='row'>

                        <div class='col-md-6'>
                          <div class='form-group'>
                            <label>Tipo da pesquisa</label>
                            <select class='form-control select2' name='tipo_pesquisa' id='tipo_pesquisa'>
                                <option value='0' selected='selected'>Todos os CNAE's</option>
                                <option value='1'>Apenas CNAE's com obrigatoriedade de IE</option>
                                <option value='2'>Descrição</option>
                                <option value='3'>Seção</option>
                            </select>
                          </div><!-- /.form-group -->
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
                        <button class="btn btn-primary" id="btn_pesquisar" name="btn_pesquisar" onclick="PesquisarCnae()" title="Executar Pesquisa"><i class="fa fa-search"></i></button>
                        <button class="btn btn-primary" id="btn_limpar_pq" name="btn_limpar_pq" onclick="PrepararPesquisaCnae()" title="Preparar para nova Pesquisa"><i class="fa  fa-eraser" ></i></button>
                        <button class="btn btn-primary" id="btn_novo"      name="btn_novo"      onclick="NovoCnae()" title="Novo Registro"><i class="fa fa-file-o"></i></button>
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
                        <h4 class="modal-title" id="titulo_informe">Cadastro Cnae</h4>
                    </div>
                    <div class="modal-body">
                        <table border="0" width="100%">
                            <tr>
                                <td><label>Código</label></td>
                                <td>&nbsp;</td>
                                <td><label>Descrição</label></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" id="operacao" name="operacao" value="" >
                                    <input type="text" class="form-control" id="cd_cnae"    value="<?php echo $cd_cnae;?>" style="width:100px" maxlength="10" onkeypress="return SomenteNumero(event);"> 
                                </td>
                                <td>&nbsp;</td>
                                <td>
                                    <input type="text" class="form-control" id="ds_cnae" value="<?php echo $ds_cnae;?>" style="width:472px" maxlength="250" >
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"><label>Seção</label></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <select class='form-control select2' name='tp_secao' id='tp_secao'>
                                        <option value="XX" <?php echo getTagSelected("XX", $tp_secao)?> >Selecionar Seção</option>
                                        <?php
                                            $cnf = Configuracao::getInstancia();
                                            $pdo = $cnf->db('', '');
                                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                            $sql = "Select "
                                                 . "    s.cd_secao "
                                                 . "  , s.cd_secao || ' - ' || s.ds_secao as ds_secao "
                                                 . "from SYS_CNAE_SECAO s "
                                                 . "order by "
                                                 . "    2 ";

                                            $res = $pdo->query($sql);

                                            while ( (($obj = $res->fetch(PDO::FETCH_OBJ))) !== false ) {
                                                $selecionar = getTagSelected($obj->cd_secao, $tp_secao);
                                                echo "<option value='" . $obj->cd_secao . "' " . $selecionar . ">" . $obj->ds_secao . "</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"><label>Especificidade</label></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <font color="black"><textarea rows="4" cols="92" maxlength="1024" id="nm_cnae"><?php echo $nm_cnae?></textarea></font>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size:3px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <label><input type="checkbox" id="sn_obriga_insc_est" value="1" <?php if ($sn_obriga_insc_est  === '1') {echo "checked";}?> > Obrigado a existência de Inscrição Estadual (IE)</label>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarCnae()"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_cadastro').style.display='none';">Fechar</button>
                        <div id="painel_cadastro_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <?php
            include('_scripts.php');
        ?>  
        <script type="text/javascript" src="../funcoes.js"></script>
        <script type="text/javascript" src="cnae_controller.js"></script>
        <script type="text/javascript" src="permissao_perfil_controller.js"></script>
    </body>
</html>

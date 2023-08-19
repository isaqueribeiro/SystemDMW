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
    $token = $_POST['token'];

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
        
        $cd_perfil = "";
        $ds_perfil = "";
        $sn_ativo  = "0";
        
        $dominio = $_SERVER['HTTP_HOST'];
        $url = $dominio. $_SERVER['REQUEST_URI'];
        $GLOBALS['url'] = $url;
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Perfis de Acesso</title>
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
            
            #listContainer{
              margin-top:0px;
            }
            #expList ul, li {
                list-style: none;
                margin:0;
                padding:0;
                cursor: pointer;
            }
            #expList p {
                margin:0;
                display:block;
            }
            #expList p:hover {
                background-color:#121212;
            }
            #expList li {
                line-height:140%;
                text-indent:0px;
                background-position: 1px 8px;
                padding-left: 20px;
                background-repeat: no-repeat;
            }

            /* Collapsed state for list element */
            #expList .collapsed {
                background-image: url(../dist/img/expandir.png);
            }
            /* Expanded state for list element
            /* NOTE: This class must be located UNDER the collapsed one */
            #expList .expanded {
                background-image: url(../dist/img/minimizar.png);
            }
            .listControl{
              margin-bottom: 15px;
            }
            .listControl a {
                border: 1px solid #555555;
                color: #555555;
                cursor: pointer;
                height: 1.5em;
                line-height: 1.5em;
                margin-right: 5px;
                padding: 4px 10px;
            }
            .listControl a:hover {
                background-color:#555555;
                color:#222222; 
                font-weight:normal;
            }            
            
        </style>

        <?php
        // put your code here
        $acao = '';
        $home = new Constantes();
        ?>

        <div id='painel_controle'>
            <section class='content-header'>
                    <h1>
                        Perfis de Acesso
                        <!--<small>Painel de Controle</small>-->
                    </h1>
                <ol class='breadcrumb'>
                    <li onclick='HomeDesktop()'><a href='#'><i class='fa fa-home'></i>Home</a></li>
                    <li class='active'><a href='#'>Permissões</a></li>
                    <li class='active'><a href='#'>Perfis</a></li>
                </ol>
            </section>
            
            <!-- Main content -->
            <section class='content'>
 
                <!-- Pesquisar -->
                <div class="box box-info">

                    <!-- Definição da Ação e do Token de Segurança -->
                    <input type="hidden" id="ac"    name="ac"    value="pesquisar_perfil"/>                   
                    <input type="hidden" id="token" name="token" value="<?php echo $usuario->getToken_id(); ?>"/>

                    <div class="box-header with-border">
                        <h3 class="box-title">Painel de Controle</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" data-widget="remove" title="Fechar" id="btn_fechar_pesq" onclick="HomeDesktop()"><i class="fa fa-remove"></i></button>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-info" id="btn_pesquisar" name="btn_pesquisar" onclick="PesquisarPerfil()" title="Atualizar Lista"><i class="fa fa-refresh"></i></button>
                        <button class="btn btn-primary" id="btn_novo" name="btn_novo" onclick="NovoPerfil()" title="Novo Registro"><i class="fa fa-file-o"></i></button>
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
                        <h4 class="modal-title" id="titulo_informe">Cadastro Perfil</h4>
                    </div>
                    <div class="modal-body">
                        <table border="0" width="100%">
                            <tr>
                                <td><label >Código</label></td>
                                <td>&nbsp;</td>
                                <td><label >Descrição</label></td>
                            </tr>
                            <tr>
                                <td width="100"><input type="text" class="form-control" id="cd_perfil" value="<?php echo $cd_perfil;?>" disabled></td>
                                <td>&nbsp;</td>
                                <td width="500"><input type="text" class="form-control" id="ds_perfil" value="<?php echo $ds_perfil;?>" maxlength="50"></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="font-size:3px">
                                    <input type="hidden" id="tx_permissao" value="">
                                    <input type="hidden" id="qt_usuarios" value="0">&nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <label><input type="checkbox" id="sn_ativo" value="1" <?php if ($sn_ativo === "1") {echo "checked";}?> > Ativo</label>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarPerfil()"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_cadastro').style.display='none';"><i class="fa fa-close "></i> Fechar</button>
                        <div id="painel_cadastro_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Configurar Permissões de Acesso" -->
        <div id="painel_configurar" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_configurar').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Permissões de Acesso</h4>
                    </div>
                    <div class="modal-body" id="painel_permissao">
                        
                    </div>
                    <div class="modal-footer">
                        <div class='box-tools pull-left'>
                            <button type='button' class='btn btn-info btn-sm' data-widget='save' title='Salvar configurações de Permissão' id='btn_salvar_permissao' onclick='SalvarPermissaoPerfil()'><i class='fa fa-save'></i> Salvar</button>
                        </div>
                        <div class='box-tools pull-right'>
                            <button type='button' class='btn btn-info btn-sm' data-widget='remove' title='Remover Todas as Permissões' id='btn_remover_permissao' onclick='LimparPermissaoPerfil()'><i class='fa fa-eraser'></i></button>
                            <button type='button' class='btn btn-info btn-sm' data-widget='check'  title='Adicionar Todas as Permissões' id='btn_adicionar_permissao' onclick='AdicionarPermissaoPerfil()'><i class='fa fa-check-square-o'></i></button>
                        </div>
                        <div id="painel_cadastro_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <?php
            include('_scripts.php');
        ?>  
        <script type="text/javascript" src="perfil_controller.js"></script>
        <script type="text/javascript" src="permissao_perfil_controller.js"></script>
    </body>
</html>

<!DOCTYPE html>
<?php
    require_once './lib/funcoes.php';
    require_once './lib/classes/perfil.php';
    require_once './lib/classes/usuario.php';
    require_once './lib/classes/sessao.php';
    require_once './lib/classes/autenticador.php';
    
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
?>
<html>
    <head>
        <?php
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $dominio = $_SERVER['HTTP_HOST'];
        $url = $dominio. $_SERVER['REQUEST_URI'];
        $GLOBALS['url'] = $url;
        
        // put your code here
        include './lib/Constantes.php';
        $acao = '';
        $home = new Constantes();
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Sistema Gestor de Documentos</title>
        <link rel="shortcut icon" href="icon.ico" >
         <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.4 -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- iCheck for checkboxes and radio inputs -->
        <link href="plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Color Picker -->
        <link href="plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap time Picker -->
        <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Select2 -->
        <link href="plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue sidebar-mini" onload="CarregarPermissaoAcesso('<?php echo $usuario->getPerfilCodigo();?>')">
        <style>
            #painel_loading{
                display:none;
                position:absolute;
                padding-left: 45%;
                padding-right: 45%;
                padding-top: 380px;
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

        <div class="wrapper">
            
            <header class="main-header">
                <!-- Logo -->
                <a href="principal.php" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><img src="pmw.png" title="Document Manager Web"></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg" title="Document Manager Web">dm<strong>Web</strong></span>
                </a>
                
                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" id="botao_navegacao">
                        <span class="sr-only">Alternância de navegação</span>
                    </a>
                    
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="dist/img/<?php echo $usuario->getIcon160x160()?>.jpg" class="user-image" alt="User Image" />
                                    <span class="hidden-xs"><?php echo $usuario->getLogin();?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="dist/img/<?php echo $usuario->getIcon160x160()?>.jpg" class="img-circle" alt="User Image" />
                                        <p>
                                            <?php echo $usuario->getNome_completo() . " - " . $usuario->getTipo();?>
                                            <small>Usuário ativo desde <?php echo $usuario->getData_cadastro();?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <input type="hidden" id="id_usuario" value="<?php echo $usuario->getId();?>"/>
                                            <a href="controle.php?ac=bloquear" class="btn btn-default btn-flat" title="Bloquear Sessão"><i class="fa fa-unlock-alt"></i></a>
                                            <a href="#" class="btn btn-default btn-flat" title="Alterar Senha Pessoal" onclick="AlterarSenha()"><i class="fa fa-edit"></i></a>
                                        </div>
                                        <div class="pull-right">
                                            <a id="link_encerrar_sessao" href="controle.php?ac=sair" class="btn btn-default btn-flat" title="Encerrar Sessão"><i class="fa fa-close "></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav> 
            </header>
            
            <aside class="main-sidebar">
                <section class="sidebar">
                    <div class="user-panel">
                        <div class="pull-left image">
                          <img src="dist/img/<?php echo $usuario->getIcon160x160()?>.jpg" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                          <p><?php echo $usuario->getNome_completo();?></p>
                          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    
                    <ul class="sidebar-menu">
                        <li class="header">TABELAS AUXILIARES</li>
                        <li class="treeview deactive">
                            <a href="#">
                                <i class="fa fa-th-list"></i><span>Endereços</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li id="op_endereco_estado" onclick="EstadoDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                                    <a href="#">
                                        <i class="fa fa-map-marker"></i>Estados
                                    </a>
                                </li>
                                <li id="op_endereco_cidade" onclick="MunicipioDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                                    <a href="#">
                                        <i class="fa fa-map-marker"></i>Municípios
                                    </a>
                                </li>
                                <li id="op_endereco_tipo" onclick="TipoLogradouroDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                                    <a href="#">
                                        <i class="fa fa-map-marker"></i>Tipos de Logradouros
                                    </a>
                                </li>
                                <li id="op_endereco_bairro" onclick="BairroDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                                    <a href="#">
                                        <i class="fa fa-map-marker"></i>Bairros
                                    </a>
                                </li>
                                <li id="op_endereco_cep" onclick="CepDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                                    <a href="#">
                                        <i class="fa fa-map-marker"></i>CEPs
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li id="op_tabela_cnae" onclick="CnaeDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                            <a href="#">
                                <i class="fa fa-th"></i><span>CNAEs</span>
                            </a>
                        </li>
                        
                        <li class="header">CADASTROS</li>
                        <li id="op_cadastro_tecnico" onclick="TecnicoDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                            <a href="#">
                                <i class="fa fa-th"></i><span>Técnicos</span>
                            </a>
                        </li>
                        <li id="op_cadastro_estabelecimento" onclick="EstabelecimentoDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                            <a href="#">
                                <i class="fa fa-building"></i><span>Estabelecimentos</span>
                            </a>
                        </li>
                        
                        
                        <li class="header">EMISSÃO DE DOCUMENTOS</li>
                        <li id="op_emitir_licenca" onclick="LicencaFuncionamentoDesktop('<?php echo $usuario->getLogin();?>', '<?php echo $usuario->getPerfilCodigo();?>')">
                            <a href="#">
                                <i class="fa fa-file"></i><span>Licença de Funcionamento</span>
                            </a>
                        </li>
                        
                        <?php
                        // Apenas Usuários com perfil "1. Administrador do Sistema" e "2. DTI"
                        if ( (int)$usuario->getPerfilCodigo() <= 2 ) {
                            echo "<li class='header'>CONTROLE DE ACESSOS</li> ";
                            echo "<li class='treeview deactive'> ";
                            echo "    <a href='#'> ";
                            echo "        <i class='fa fa-unlock-alt'></i><span>Permissões</span> ";
                            echo "        <i class='fa fa-angle-left pull-right'></i> ";
                            echo "    </a> ";
                            echo "    <ul class='treeview-menu'> ";
                            echo "        <li id='op_acesso_perfil' onclick='PerfilDesktop()'> ";
                            echo "            <a href='#'> ";
                            echo "                <i class='fa fa-users'></i>Perfis ";
                            echo "            </a> ";
                            echo "        </li> ";
                            echo "        <li id='op_acesso_usuario' onclick='UsuarioDesktop()'> ";
                            echo "            <a href='#'> ";
                            echo "                <i class='fa fa-user'></i>Usuários ";
                            echo "            </a> ";
                            echo "        </li> ";
                            echo "    </ul> ";
                            echo " ";
                            echo "</li> ";
                        }
                        ?>
                        
                    </ul>
                </section>
            </aside>

            <div class="content-wrapper" id="desktop">
                
            </div>
            
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                  <b>Versão</b> 1.0.0
                </div>
                <strong>Copyright &copy; <?php echo $home->get_copy_ano();?> <a href='<?php echo $home->get_home_ham();?>' target="_blank"><?php echo $home->get_company_ham();?></a>.</strong> 
                Todos os direitos reservados | Desenvolvido pela <a href='<?php echo $home->get_home_agil();?>' target="_blank">Ágil Soluções em Softwares</a>.
            </footer>
        </div> 
        
        <!-- Painel estilo "Modal Dialog" para "Alterar Senha" -->
        <div id="painel_alterar_senha" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_alterar_senha').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Alterar Senha</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group has-feedback">
                            <label>Login:</label> 
                            <input type="text" class="form-control" id="lg_usuario" value="<?php echo $usuario->getLogin();?>" disabled>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label>Nome Completo:</label> 
                            <input type="text" class="form-control" id="nm_usuario" value="<?php echo $usuario->getNome_completo();?>" disabled>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Senha atual..." id="pw_senha_atual" value="" required/>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <label>Nova Senha:</label> 
                            <input type="password" class="form-control" placeholder="Informar nova senha..." id="pw_senha_nova" value="" required/>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" class="form-control" placeholder="Confirmar nova senha..." id="pw_senha_confirmar" value="" required/>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btn_alterar_senha_salvar" type="button" class="btn btn-outline pull-left" data-dismiss="modal"  onclick="SalvarSenha()"><i class="fa fa-save"></i> Salvar</button>
                        <button id="btn_alterar_senha_fechar" type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_alterar_senha').style.display='none';"><i class="fa fa-close "></i> Fechar</button>
                        <div id="painel_alterar_senha_retorno"></div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Loading..." -->
        <div id="painel_loading" class="modal modal-primary">
            <div>
                <img src="dist/img/ajax-loader.gif"/>
            </div>
        </div>
        
        <!-- Painel estilo "Modal Dialog" para "Aguarde" -->
        <div id="painel_wait_main" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_wait_main').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_wait_main">Alerta!</h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_wait_main">One fine body&hellip;</p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Informe" -->
        <div id="painel_informe" class="modal modal-info">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_informe').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_informe">Informe!</h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_informe">One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_informe').style.display='none';">Fechar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Aleta" -->
        <div id="painel_alerta" class="modal modal-warning">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_alerta').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_alerta">Alerta!</h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_alerta">One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_alerta').style.display='none';">Fechar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Confirma" -->
        <div id="painel_confirma_main" class="modal modal-primary">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_confirma_main').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_confirma_main">Confirmar</h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_confirma_main">One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" id="botao_confirma_main_nao" onclick="document.getElementById('painel_confirma_main').style.display='none';">&nbsp;&nbsp; Não &nbsp;&nbsp;</button>
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" id="botao_confirma_main_sim" onclick="document.getElementById('painel_confirma_main').style.display='none';">&nbsp;&nbsp; Sim &nbsp;&nbsp;</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <!-- Painel estilo "Modal Dialog" para "Erro" -->
        <div id="painel_erro" class="modal modal-danger">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById('painel_erro').style.display='none';"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo_erro">Erro!</h4>
                    </div>
                    <div class="modal-body">
                        <p id="msg_erro">One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-right" data-dismiss="modal" onclick="document.getElementById('painel_erro').style.display='none';">Fechar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        
        <?php
          include('scripts_diversos.php');
        ?>  
        
        <!-- Controle Ajax para a chamada de páginas -->
        <script type="text/javascript" src="ajax.js"></script>
        <script type="text/javascript" src="funcoes.js"></script>
        <script type="text/javascript" src="principal.js"></script>
        <script type="text/javascript" src="pages/perfil_controller.js"></script>
        <script type="text/javascript" src="pages/permissao_perfil_controller.js"></script>
        <script type="text/javascript" src="pages/usuario_controller.js"></script>
        <script type="text/javascript" src="pages/estado_controller.js"></script>
        <script type="text/javascript" src="pages/cidade_controller.js"></script>
        <script type="text/javascript" src="pages/bairro_controller.js"></script>
        <script type="text/javascript" src="pages/cep_controller.js"></script>
        <script type="text/javascript" src="pages/cnae_controller.js"></script>
        <script type="text/javascript" src="pages/tecnico_controller.js"></script>
        <script type="text/javascript" src="pages/estabelecimento_controller.js"></script>
        <script type="text/javascript" src="pages/licenca_funcionamento_controller.js"></script>

        <!-- Script específico da página -->
        <script type="text/javascript">
            setTokenId("<?php echo $usuario->getToken_id(); ?>");
            setIdUsuario("<?php echo $usuario->getId(); ?>");
            setLgUsuario("<?php echo $usuario->getLogin(); ?>");
            setPwUsuario("<?php echo encript($usuario->getSenha()); ?>");
            setPfUsuario("<?php echo $usuario->getPerfilCodigo(); ?>");
            setSistema("<?php echo $usuario->getSistema(); ?>");
            setSistemaNome("<?php echo $usuario->getSistemaNome(); ?>");
    
            $(window).ready(function() {
                $('#loading').hide();
            });
            
            // Ao pressionar tecla e soltá-la
            document.onkeyup = function(e){ 
                // 1. Fechar formulários de cadastros ao pressionar ESC
                var showCadastro = false; //(document.getElementById("painel_cadastro").style.display === 'block'); /* Removida o ESC do Cadastro */
                var showAlerta   = (document.getElementById("painel_alerta").style.display === 'block');
                var showErro     = (document.getElementById("painel_erro").style.display === 'block');
                
                if ( (e.which === 27) && (showCadastro === true) && (showAlerta === false) && (showErro === false) ) {
                    document.getElementById('painel_cadastro').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showCadastro === true) && (showAlerta === true)  && (showErro === false) ) {
                    document.getElementById('painel_alerta').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showCadastro === true) && (showAlerta === false) && (showErro === false) ) {
                    document.getElementById('painel_cadastro').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showCadastro === true) && (showAlerta === false) && (showErro === true) ) {
                    document.getElementById('painel_erro').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showCadastro === true) ) {
                    document.getElementById('painel_cadastro').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showAlerta === true) ) {
                    document.getElementById('painel_alerta').style.display = 'none';
                } else 
                if ( (e.which === 27) && (showErro === true) ) {
                    document.getElementById('painel_erro').style.display = 'none';
                }
            }
        </script>
    </body>
</html>

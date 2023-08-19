<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    require_once './lib/classes/usuario.php';
    require_once './lib/classes/sessao.php';
    require_once './lib/classes/autenticador.php';

    $aut = Autenticador::getInstancia();

    $usuario = null;
    if ($aut->esta_logado()) {
        $usuario = $aut->pegar_usuario();
    } else {
        $aut->expulsar();
    }
?>
<html>
    <head>
        <?php
        ini_set('default_charset','UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $msg  = "";
        
        if (isset($_GET['ac'])) {
            $ac = $_GET["ac"];
            if ($ac == 'error_pwd') {
                $msg = "Senha inválida!";
            }
        }
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Bloqueio</title>
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
        <link href="plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Color Picker -->
        <link href="plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap time Picker -->
        <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <!-- Select2 -->
        <link href="plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Theme style -->
        <link href="dist/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link href="dist/css/skins/_all-skins.css" rel="stylesheet" type="text/css" />
        <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="lockscreen skin-green">
        <?php
        // put your code here
        include './lib/Constantes.php';
        $acao = '';
        $home = new Constantes();
        ?>
        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <a href="bloquear_pagina.php">Document Manager <b>WEB</b></a>
            </div>
            
            <!-- User name -->
            <div class="lockscreen-name"><?php echo $usuario->getNome_completo()?></div>

            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <img src="dist/img/<?php echo $usuario->getIcon128x128()?>.jpg" alt="User Image" />
                </div>
                <!-- /.lockscreen-image -->

                <!-- lockscreen credentials (contains the form) -->
                <form class="lockscreen-credentials" action="controle.php" method="post" target="_self">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Identificação do usuário..." required="true" disabled="true" id="login" name="login" value=<?php echo $usuario->getLogin()?> />
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Senha..." required="true" id="senha" name="senha" value=""/>
                        <div class="input-group-btn">
                            <button class="btn"  id="ac" name="ac" value="desbloquear"><i class="fa fa-arrow-right text-muted"></i></button>
                        </div>
                    </div>
                </form><!-- /.lockscreen credentials -->
            </div><!-- /.lockscreen-item -->
            
            <div class="help-block text-center">
                Digite sua senha para recuperar sua sessão
            </div>

            <?php
            if ($msg != "") {
                echo '<div class="help-block text-center">';
                echo '<p class="label-warning">'.$msg.'</p>';
                echo '</div>';
            }
            ?>

            <div class="text-center">
                <a href="index.php">Ou entrar com usuário diferente</a>
            </div>
            <div class="lockscreen-footer text-center">
                <strong>Copyright &copy; <?php echo $home->get_copy_ano();?> <a href='<?php echo $home->get_home_ham();?>' target="_blank"><?php echo $home->get_company_ham();?></a>.</strong> 
                Todos os direitos reservados | Desenvolvido pela <a href='<?php echo $home->get_home_agil();?>' target="_blank">Ágil Soluções em Softwares</a>.
            </div>
        </div><!-- /.center -->

        <!-- jQuery 2.1.4 -->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>

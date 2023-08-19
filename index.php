<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    require_once './lib/funcoes.php';

    session_start();
    session_destroy();
?>
<html>
    <head>
        <?php
        ini_set('default_charset', 'utf-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $msg  = "";
        
        if (isset($_GET['ac'])) {
            $ac = $_GET["ac"];
            if ($ac == 'error_login') {
                $msg = "Usuário e/ou senha inválido!";
            }
        }
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Entrar</title>
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
    <body class="login-page skin-yellow">
        <?php
        
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__FILE__) . '/logs/error_log_DocumentManagerWeb.txt');
        error_reporting(E_ALL);
        
        // Teste de criptografia de senha
//        echo encript("admin") . "<br><br>";
//        echo encript("adminadmin") . "<br><br>";
//        echo decript("WVdSdGFXNWQwMzNlMjJhZTM0OGFlYjU2NjBmYzIxNDBhZWMzNTg1MGM0ZGE5OTdoWkcxcGJnPT0=") . "<br><br>";
//        echo decript("WVdkMDMzZTIyYWUzNDhhZWI1NjYwZmMyMTQwYWVjMzU4NTBjNGRhOTk3UnRhVzVoWkcxcGJnPT0=") . "<br><br>";
        
        // Blocos de código abaixa para testes (FUNCIONANDO PERFEITAMENTE)
        /*
        include_once './lib/classes/configuracao.php';
        
        foreach(PDO::getAvailableDrivers() as $driver) {
            echo '<p>' . $driver.'</p>';
        }

        $t = Configuracao::getInstancia();
        echo '<p>' . $t->getDsn() . '</p>';
        echo '<p>' . $t->getUsuario() . '</p>';
        echo '<p>' . $t->getSenha() . '</p>';
        echo '<p>' . $t->getCharset() . '</p>';
 
        $dsn      = $t->getDsn();
        $user     = $t->getUsuario();
        $password = $t->getSenha();
        $charset  = $t->getCharset();
        
        try {
            
            $pdo = $t->db("sysdba", "masterkey");

            $sql  = "Select ";
            $sql .= "    u.* ";
            $sql .= "  , p.ds_perfil ";
            $sql .= "  , t.nm_tecnico ";
            $sql .= "  , t.nr_cpf ";
            $sql .= "from SYS_USUARIO u ";
            $sql .= "  inner join SYS_PERFIL p on (p.cd_perfil = u.cd_perfil) ";
            $sql .= "  left join TBTECNICO t on (t.id_tecnico = u.cd_tecnico) ";

            echo '<p>' . $sql . '</p>';
            
            $res = $pdo->query($sql);
            
            while ($obj = $res->fetch(PDO::FETCH_OBJ)) {
                echo "<p>Login : " . $obj->lg_usuario . "</p>";
                echo "<p>Nome  : " . $obj->nm_usuario . "</p>";
                echo "<p>Senha : " . $obj->pw_usuario . "</p>";
                echo "<p>MD5   : " . md5($obj->pw_usuario) . "</p>";
                echo "<p>SHA1  : " . sha1($obj->pw_usuario) . "</p>";
                echo "<p>BASE64: " . base64_encode($obj->pw_usuario) . "</p>";
                echo "<p>teste : " . encript($obj->pw_usuario) . "</p>";
                echo "<p>teste : " . decript(encript($obj->pw_usuario)) . "</p>";
                echo "<line>";
            }
            
        } catch (Exception $e) {
            echo 'Falha : ' . $e->getMessage();
        }
        */
        ?>
        <div class="login-box">
            <div class="login-logo">
                <a href="index.php">Document Manager <b>WEB</b></a>
            </div><!-- /.login-logo -->
            
            <div class="login-box-body">
                
                <p class="login-box-msg">Entre para iniciar sua sessão</p>
                
                <form action="controle.php" method="post" target="_self">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" placeholder="Identificação do usuário..." id="login" name="login" value="" required/>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    
                    <div class="form-group has-feedback">
                        <input type="password" class="form-control" placeholder="Senha..." id="senha" name="senha" value="" required/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    
                    <div class="row">
<!--                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input type="checkbox"> Lembre-me
                                </label>
                            </div>
                        </div> /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat" id="ac" name="ac" value="logar">Entrar</button>
                        </div><!-- /.col -->
                    </div>
                </form>

                <?php
                if ($msg != "") {
//                    echo '<br><p class="label-warning">'.$msg.'</p>';
                    echo '<div class="help-block text-center">';
                    echo '  <p class="label-warning">'.$msg.'</p>';
                    echo '</div>';
                }
                ?>
                
                <!--<a href="#">Esqueci minha senha</a><br>-->
                <!--<a href="#" class="text-center">Cadastrar novo usuário</a>-->

            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->

        <!-- jQuery 2.1.4 -->
        <script src="plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="plugins/iCheck/icheck.min.js" type="text/javascript"></script>

        <!-- Controle Ajax para a chamada de páginas -->
        <script type="text/javascript" src="ajax.js"></script>

        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
                
                // Foco do cursor no campo de login
                document.getElementById('login').focus();
            });
        </script>
    </body>
</html>

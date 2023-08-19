<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    require_once './lib/Constantes.php';
    require_once './lib/funcoes.php';
    require_once './lib/classes/usuario.php';
    require_once './lib/classes/sessao.php';
    require_once './lib/classes/autenticador.php';
    require_once './lib/classes/configuracao.php';
    require_once './lib/classes/dao.php';

    try {
        $_SITUACAO_LICENCA_APROVADA   = 2;
        $_SITUACAO_LICENCA_VENCIDA    = 3;
        $_SITUACAO_LICENCA_CANCELADA  = 5;

        $_SITUACAO_LICENCA = array("Pendente", "Aguardando", "Aprovada - Em vigor", "Vencida", "Suspensa", "Cancelada");

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
            . "where l.dt_validade  < current_date "
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

    } catch (Exception $ex) {
    }
    
    // Variáveis para armazenar os dados de impressão
    
    $id_licenca   = decript($_REQUEST['id']);
    $nr_exercicio = "";
    $nr_licenca   = "";
    $nr_controle  = "";
    $nr_processo  = "";
    $dt_emissao   = "";
    $dt_validade  = "";
    $dt_aprovacao = "";
    $id_estabelecimento = "";
    $nm_razao    = "";
    $nm_fantasia = "";
    $nr_cnpj     = "";
    $nr_insc_est    = "";
    $nr_insc_mun    = "";
    $tp_endereco    = "";
    $ds_endereco    = "";
    $nr_endereco    = "";
    $ds_complemento = "";
    $nr_cep    = "";
    $nm_bairro = "";
    $nm_cidade = "";
    $cd_atividade = "";
    $ds_atividade = "";
    $cd_responsavel = "";
    $nm_responsavel = "";
    $ds_observacao  = "";
    $tp_situacao    = 0;
    
    $_SITUACAO = array("Pendente", "Aguardando", "Aprovada", "Vencida", "Suspensa", "Cancelada");
    
    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Carregar Dados da Licença de Funcionamento para Impressão
    $sql = "Select "
         . "    l.id_licenca "
         . "  , l.nr_exercicio "
         . "  , l.nr_licenca "
         . "  , lpad(l.nr_licenca, 7, '0') || '/' || l.nr_exercicio as nr_controle "
         . "  , l.nr_processo "
         . "  , l.dt_emissao "
         . "  , l.dt_validade "
         . "  , coalesce(l.dt_aprovacao, current_date) as dt_aprovacao "
         . "  , l.id_estabelecimento "
         . "  , e.nm_razao "
         . "  , e.nm_fantasia "
         . "  , e.nr_cnpj "
         . "  , e.nr_insc_est "
         . "  , e.nr_insc_mun "
         . "  , tl.cd_descricao as tp_endereco "
         . "  , e.ds_endereco "
         . "  , e.nr_endereco "
         . "  , e.ds_complemento "
         . "  , e.nr_cep "
         . "  , br.nm_bairro "
         . "  , cd.nm_cidade "
         . "  , l.cd_atividade "
         . "  , c.ds_cnae as ds_atividade "
         . "  , l.cd_responsavel "
         . "  , t.nm_tecnico as nm_responsavel "
         . "  , l.ds_observacao "
         . "  , l.tp_situacao "
         . "from TBLICENCA_FUNCIONAMENTO l "
         . "  left join TBESTABELECIMENTO e on (e.id_estabelecimento = l.id_estabelecimento) "
         . "  left join TBCNAE c on (c.cd_cnae = l.cd_atividade) "
         . "  left join TBTECNICO t on (t.id_tecnico = l.cd_responsavel) "
         . "  left join SYS_TIPO_LOGRADOURO tl on (tl.cd_tipo = e.tp_endereco) "
         . "  left join SYS_BAIRROS br on (br.cd_bairro = e.cd_bairro) "
         . "  left join SYS_CIDADES cd on (cd.cd_cidade = e.cd_cidade) "
         . "where l.id_licenca  = '{$id_licenca}' "; 
    
    $lin = $pdo->query($sql);
    if ((($obj = $lin->fetch(PDO::FETCH_OBJ))) !== false) {
        $data_emissao   = explode("-", $obj->dt_emissao);
        $data_validade  = explode("-", $obj->dt_validade);
        $data_aprovacao = explode("-", $obj->dt_aprovacao);
        
        $id_licenca   = $obj->id_licenca;
        $nr_exercicio = $obj->nr_exercicio;
        $nr_licenca   = $obj->nr_licenca;
        $nr_controle  = $obj->nr_controle;
        $nr_processo  = $obj->nr_processo;
        $dt_emissao   = $data_emissao[2]   . "/" . $data_emissao[1]   . "/" . $data_emissao[0];
        $dt_validade  = $data_validade[2]  . "/" . $data_validade[1]  . "/" . $data_validade[0];
        $dt_aprovacao = $data_aprovacao[2] . "/" . $data_aprovacao[1] . "/" . $data_aprovacao[0];
        $id_estabelecimento = $obj->id_estabelecimento;
        $nm_razao    = $obj->nm_razao;
        $nm_fantasia = $obj->nm_fantasia;
        $nr_cnpj     = formatarTexto("##.###.###/####-##", $obj->nr_cnpj);
        $nr_insc_est    = $obj->nr_insc_est;
        $nr_insc_mun    = $obj->nr_insc_mun;
        $tp_endereco    = $obj->tp_endereco;
        $ds_endereco    = $obj->ds_endereco;
        $nr_endereco    = $obj->nr_endereco;
        $ds_complemento = $obj->ds_complemento;
        $nr_cep    = $obj->nr_cep;
        $nm_bairro = $obj->nm_bairro;
        $nm_cidade = $obj->nm_cidade;
        $cd_atividade = $obj->cd_atividade;
        $ds_atividade = $obj->ds_atividade;
        $cd_responsavel = $obj->cd_responsavel;
        $nm_responsavel = $obj->nm_responsavel;
        $ds_observacao  = $obj->ds_observacao;
        $tp_situacao    = (int)$obj->tp_situacao;
    }    
    
?>
<html>
    <head>
        <?php
        ini_set('default_charset','UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);

        $msg  = "";
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Validar Licença de Funcionamento</title>
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
        $acao = '';
        $home = new Constantes();
        ?>
        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <a href="#">Document Manager <b>WEB</b></a><br>
                <a href='<?php echo $home->get_home_ham();?>' target="_blank"><?php echo $home->get_company_ham();?></a>
            </div>
            
            <div class="lockscreen-name bg-blue-gradient"><label>Validar Licença de Funcionamento</label></div> 
            <br>
            <div class="lockscreen">
                <table class="table-responsive table-hover" border="0" width="100%" >
                    <tr>
                        <td class="bg-blue-gradient"><label>&nbsp;Processo</label></td>
                        <td>&nbsp;</td>
                        <td class="bg-blue-gradient"><label>&nbsp;Controle</td>
                        <td>&nbsp;</td>
                        <td class="bg-blue-gradient"><label>&nbsp;Emissão</td>
                        <td>&nbsp;</td>
                        <td class="bg-blue-gradient"><label>&nbsp;Validade</td>
                    </tr>
                    <tr>
                        <td><label>&nbsp;<?php echo $nr_processo;?></label></td>
                        <td>&nbsp;</td>
                        <td><label>&nbsp;<?php echo $nr_controle;?></label></td>
                        <td>&nbsp;</td>
                        <td><label>&nbsp;<?php echo $dt_emissao;?></label></td>
                        <td>&nbsp;</td>
                        <td><label>&nbsp;<?php echo $dt_validade;?></label></td>
                    </tr>
                    <tr>
                        <td class="bg-blue-gradient" colspan="7" align="center"><label>Situação da Licença</label></td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center"><font size="7"><label><?php echo ($tp_situacao === 0?"Inválida":$_SITUACAO[$tp_situacao]);?></label></font></td>
                    </tr>
                    <tr>
                        <td class="bg-blue-gradient" colspan="7"><label>&nbsp;Estabelecimento</label></td>
                    </tr>
                    <tr>
                        <td colspan="7"><label>&nbsp;<?php echo $nm_fantasia;?></label></td>
                    </tr>
                    <tr>
                        <td class="bg-blue-gradient"><label>&nbsp;CNPJ</label></td>
                        <td>&nbsp;</td>
                        <td class="bg-blue-gradient" colspan="5"><label>&nbsp;Razão Social</td>
                    </tr>
                    <tr>
                        <td><label>&nbsp;<?php echo $nr_cnpj;?></label></td>
                        <td>&nbsp;</td>
                        <td colspan="5"><label>&nbsp;<?php echo $nm_razao;?></label></td>
                    </tr>
                    
                </table>
                <br>
                <table class="table-responsive table-hover table-condensed table-striped" border="0" width="100%">
                    <tr>
                        <td class="bg-blue-gradient" colspan="7" align="center"><label>Eventos</label></td>
                    </tr>
                    <?php
                        $sql = 
                              "Select "
                            . "  e.*"
                            . "from TBEVENTO e "
                            . "where e.id_licenca = '{$id_licenca}' "
                            . "order by e.dh_evento DESC";
                        
                        $res = $pdo->query($sql);
                        while (($obj = $res->fetch(PDO::FETCH_OBJ)) !== false) {
                            $dt_evento  = explode("-", $obj->dt_evento);
                            $tr = "<tr>"
                                . "   <td align='center'>&nbsp;" . $dt_evento[2]  . "/" . $dt_evento[1]  . "/" . $dt_evento[0]  . "&nbsp;</td>"
                                . "   <td>&nbsp;</td>"
                                . "   <td align='justify'>" . $obj->ds_evento  . "</td>"
                                . "</tr>";
                            echo $tr;
                        }
                    ?>
                </table>
            </div>
            <br>
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

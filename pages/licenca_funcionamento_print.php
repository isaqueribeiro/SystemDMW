<!DOCTYPE html>
<?php
    /**
     * Armazena saída do HTML em buffer
     * Referências
     * http://php.net/manual/pt_BR/function.ob-start.php
     */ 
//    ob_start();
//    
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

    $desktop = "";
    $home = new Constantes();
    
    // Verificar o Token de segurança
    $token = $_REQUEST['token'];

    if ( $token !== $usuario->getToken_id() ) {
        $funcao = new Constantes();
        echo $funcao->message_alert("TokenID de segurança inválido!");
        exit;
    }
  
    $exercicio_padrao = date("Y");
    $cd_estado_padrao = "0";
    $nm_estado_padrao = "";
    $uf_estado_padrao = "";
    $cd_cidade_padrao = "0";
    $nm_cidade_padrao = "0";
    $data_padrao_server = "";
    $ds_governo     = "";
    $ds_sistema     = "";
    $ds_secretaria  = "";
    $ds_coordenacao = "";
        
    $ds_funcional_ass1 = "Coordenação de Vigilância Sanitária";
    $ds_funcional_ass2 = "Técnico(a):";
    $ds_funcional_ass3 = "Coordenação de Vigilância em Saúde";
    
    // Variáveis para armazenar os dados de impressão
    
    $id_licenca   = $_REQUEST['id_licenca']; //getGuidEmpty();
    $nr_exercicio = "";
    $nr_licenca   = "";
    $nr_controle  = "";
    $nr_processo  = "";
    $dt_emissao   = "";
    $dt_validade  = "";
    $dt_aprovacao = "";
    $id_estabelecimento = "";
    $tp_pessoa   = "";
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
    $ds_atividade_secuntaria = "";
    $cd_responsavel = "";
    $nm_responsavel = "";
    $nm_responsavel_estabelecimento = "";
    $nr_responsavel_estabelecimento = "";
    $cn_responsavel_estabelecimento = "";
    $ds_observacao  = "";
    $tp_situacao    = "";

    // Buscar Estado e Cidade padrões para Pesquisa

    $cnf = Configuracao::getInstancia();
    $pdo = $cnf->db('', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "Select "
         . "    coalesce(c.cd_estado, 0) as cd_estado "
         . "  , coalesce(e.nm_estado, '...') as nm_estado "
         . "  , coalesce(e.uf_estado, '...') as uf_estado "
         . "  , coalesce(c.cd_cidade, 0)     as cd_cidade "
         . "  , coalesce(m.nm_cidade, '...') as nm_cidade "
         . "  , coalesce(c.ds_governo, '...')         as ds_governo "
         . "  , coalesce(c.ds_sistema_governo, '...') as ds_sistema "
         . "  , coalesce(c.ds_secretaria, '...')  as ds_secretaria "
         . "  , coalesce(c.ds_coordenacao, '...') as ds_coordenacao "
         . "  , coalesce(c.ds_assinatura_licenca_1, '{$ds_funcional_ass1}') as ds_funcional_ass1 "
         . "  , coalesce(c.ds_assinatura_licenca_2, '{$ds_funcional_ass2}') as ds_funcional_ass2 "
         . "  , coalesce(c.ds_assinatura_licenca_3, '{$ds_funcional_ass3}') as ds_funcional_ass3 "
         . "from SYS_CONFIGURACAO c "
         . "  left join SYS_ESTADOS e on (e.cd_estado = c.cd_estado) "
         . "  left join SYS_CIDADES m on (m.cd_cidade = c.cd_cidade) "
         . "where c.cd_configuracao = {$usuario->getSistema()} ";

    $res = $pdo->query($sql);
    if ((($obj = $res->fetch(PDO::FETCH_OBJ))) !== false) {
        $cd_estado_padrao  = $obj->cd_estado;
        $nm_estado_padrao  = $obj->nm_estado;
        $uf_estado_padrao  = $obj->uf_estado;
        $cd_cidade_padrao  = $obj->cd_cidade;
        $nm_cidade_padrao  = $obj->nm_cidade;
        $ds_governo     = $obj->ds_governo;
        $ds_sistema     = $obj->ds_sistema;
        $ds_secretaria  = $obj->ds_secretaria;
        $ds_coordenacao = $obj->ds_coordenacao;
        $ds_funcional_ass1 = trim($obj->ds_funcional_ass1); 
        $ds_funcional_ass2 = trim($obj->ds_funcional_ass2); 
        $ds_funcional_ass3 = trim($obj->ds_funcional_ass3); 
    }    
    
    $dao  = Dao::getInstancia();
    $data = explode("-", $dao->getDataServidor());
    $data_padrao_server = $data[2] . "/" . $data[1] . "/" . $data[0];
    
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
         . "  , e.tp_pessoa "
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
         . "  , c1.ds_cnae as ds_atividade "
         . "  , l.cd_atividade_secundaria "
         . "  , c2.ds_cnae as ds_atividade_secundaria "
         . "  , l.cd_responsavel "
         . "  , t.nm_tecnico as nm_responsavel "
         . "  , coalesce(l.nm_responsavel_estabelecimento, '...') as nm_responsavel_estabelecimento "
         . "  , coalesce(l.nr_responsavel_estabelecimento, '   ') as nr_responsavel_estabelecimento "
         . "  , coalesce(l.cn_responsavel_estabelecimento, '   ') as cn_responsavel_estabelecimento "
         . "  , l.ds_observacao "
         . "  , l.tp_situacao "
         . "from TBLICENCA_FUNCIONAMENTO l "
         . "  left join TBESTABELECIMENTO e on (e.id_estabelecimento = l.id_estabelecimento) "
         . "  left join TBCNAE c1 on (c1.cd_cnae = l.cd_atividade) "
         . "  left join TBCNAE c2 on (c2.cd_cnae = l.cd_atividade_secundaria) "
         . "  left join TBTECNICO t on (t.id_tecnico = l.cd_responsavel) "
         . "  left join SYS_TIPO_LOGRADOURO tl on (tl.cd_tipo = e.tp_endereco) "
         . "  left join SYS_BAIRROS br on (br.cd_bairro = e.cd_bairro) "
         . "  left join SYS_CIDADES cd on (cd.cd_cidade = e.cd_cidade) "
         . "where l.tp_situacao = {$_SITUACAO_LICENCA_APROVADA} "
         . "  and l.id_licenca  = '{$id_licenca}' "; // {558D7010-5407-4187-BE9D-19ED8B1FBA1F}
    
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
        $tp_pessoa   = intval("0".$obj->tp_pessoa);
        $nr_cnpj     = (intval("0".$obj->tp_pessoa) === 0?formatarTexto("###.###.###-##", $obj->nr_cnpj):formatarTexto("##.###.###/####-##", $obj->nr_cnpj));
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
        $ds_atividade_secuntaria = $obj->ds_atividade_secundaria;
        $cd_responsavel = $obj->cd_responsavel;
        $nm_responsavel = $obj->nm_responsavel;
        $nm_responsavel_estabelecimento = trim($obj->nm_responsavel_estabelecimento);
        $nr_responsavel_estabelecimento = trim($obj->nr_responsavel_estabelecimento);
        $cn_responsavel_estabelecimento = trim($obj->cn_responsavel_estabelecimento);
        $ds_observacao  = $obj->ds_observacao;
        $tp_situacao    = $obj->tp_situacao;
    }    
    
?>
<html>
    <head>
        <?php
        
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', true);
        error_reporting(E_ALL);
        
        $erros = "";
        
        $dominio = $_SERVER['HTTP_HOST'];
        $url = $dominio. $_SERVER['REQUEST_URI'];
        $GLOBALS['url'] = $url;
        
        $url_qrcode = "https://portal.castanhal.gov.br/dmWeb/licenca_funcionamento_valida.php?id=" . encript($id_licenca);
        
        /* Inicio - Processo de geração do QR Code
         */
        // Configurar para local de gravação temporária de arquivos PNG
        $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
        $PNG_WEB_DIR  = "temp/";

        include "../lib/phpqrcode/qrlib.php";

        if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
        
        // 171 x 171
        $errorCorrectionLevel = 'L';
        $matrixPointSize      = 3;
        $filename     = $PNG_TEMP_DIR . str_replace("/", "", $nr_controle) . ".png";
        $filename_png = "./temp/" . str_replace("/", "", $nr_controle) . ".png";
        QRcode::png($url_qrcode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        /* Final - Processo de geração do QR Code
         */
        
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Document Manager Web | Licença de Funcionamento</title>
        <link rel="shortcut icon" href="../icon.ico" >
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
    <body onload="window.print();">
    <!--<body>-->
        <style>
            #tb_licenca{
                font-size: 16px;
            }            
        </style>
        <table border="0" width="100%">
            <tr>
                <td rowspan="3" align="center"><img src="../dist/img/castanhal_pa_brasao.png"></td>
                <td colspan="2"><font size="4"><?php echo $ds_governo;?></font></td>
                <!--<td rowspan="4" align="right"><img src="http://chart.apis.google.com/chart?chs=185x185&cht=qr&chl=<?php // echo $url_qrcode;?>&UTF-8"></td>-->
                <td rowspan="4" align="right"><img src="<?php echo $filename_png;?>"></td>
            </tr>
            <tr>
                <td colspan="2"><font size="6"><strong><?php echo $ds_sistema;?></strong></font></td>
            </tr>
            <tr>
                <td colspan="2"><font size="4"><?php echo $ds_secretaria;?></font></td>
            </tr>
            <tr>
                <td colspan="9" align="center"><font size="9"><strong>LICENÇA DE FUNCIONAMENTO</strong></font></td>
            </tr>
        </table>
        <br>
        <table border="0" width="100%" id="tb_licenca">
            <tr>
                <td height="40" width="120">Processo No.: </td>
                <td colspan="7">&nbsp;<strong><?php echo $nr_processo;?></strong></td>
                <td width="120">Contole No.: </td>
                <td>&nbsp;<strong><?php echo $nr_controle;?></strong></td>
            </tr>
            <tr>
                <td height="40">Estabelecimento: </td>
                <td colspan="9">&nbsp;<strong><?php echo $nm_fantasia;?></strong></td>
            </tr>
            <tr>
                <td height="40">Razão Social: </td>
                <td colspan="7">&nbsp;<strong><?php echo $nm_razao;?></strong></td>
                <td>CPF/CNPJ: </td>
                <td>&nbsp;<strong><?php echo $nr_cnpj;?></strong></td>
            </tr>
            <tr>
                <td height="40">Endereço: </td>
                <td colspan="7">&nbsp;<strong><?php echo $tp_endereco . " " . $ds_endereco;?></strong></td>
                <td>No.: </td>
                <td>&nbsp;<strong><?php echo $nr_endereco;?></strong></td>
            </tr>
            <tr>
                <td height="40">Complemento: </td>
                <td colspan="7">&nbsp;<strong><?php echo $ds_complemento;?></strong></td>
                <td>Bairro: </td>
                <td>&nbsp;<strong><?php echo $nm_bairro;?></strong></td>
            </tr>
            <tr>
                <td height="40">Atividade: </td>
                <td colspan="7">&nbsp;<strong><?php echo (trim($ds_atividade_secuntaria) === ""?$ds_atividade:$ds_atividade . " (" . $ds_atividade_secuntaria . ")");?></strong></td>
                <td>Resp. Técnico: </td>
                <td>&nbsp;<strong><?php echo ($cn_responsavel_estabelecimento === ""?$nm_responsavel_estabelecimento:$nm_responsavel_estabelecimento . " (" . $nr_responsavel_estabelecimento . " " . $cn_responsavel_estabelecimento . ")") ;?></strong></td>
            </tr>
            <tr>
                <td height="40">Observação: </td>
                <td colspan="7">&nbsp;<strong><?php echo $ds_observacao?></strong></td>
                <td colspan="2">&nbsp;<strong><?php echo strtoupper($nm_cidade_padrao . "/" . $uf_estado_padrao . ", " . getDataExtenso($dt_emissao));?>.</strong>&nbsp;</td>
            </tr>
            <tr>
                <td height="40" colspan="10">&nbsp;</td>
            </tr>
        </table>
        
        <table border="0" width="100%">
<!--            <tr>
                <td>
                    <i class="fa fa-pencil"><font face="Courier New"><?php echo str_pad("", 30, "-", STR_PAD_LEFT);?></font></i>
                </td>
                <td>&nbsp;</td>
                <td>
                    <i class="fa fa-pencil"></i>
                </td>
                <td>&nbsp;</td>
                <td>
                    <i class="fa fa-pencil"></i>
                </td>
            </tr>-->
            <tr>
                <td align="center" width="32%">
                    <!--<hr size="10" style="height:2px; color:#000; background-color:#000; margin-top: 2px; margin-bottom: 2px;">-->
                    <?php echo str_pad("", 70, "-", STR_PAD_LEFT);?> <i class="fa fa-pencil"></i><br>
                    <?php echo $ds_funcional_ass1;?>
                    <!--Coordenação de Vigilância Sanitária-->
                </td>
                <td>&nbsp;</td>
                <td align="center" width="32%">
                    <!--<hr size="10" style="height:2px; color:#000; background-color:#000; margin-top: 2px; margin-bottom: 2px;">-->
                    <?php echo str_pad("", 70, "-", STR_PAD_LEFT);?> <i class="fa fa-pencil"></i><br>
                    <?php echo (trim($nm_responsavel) === ""?"Técnico(a) da Visita":"Técnico(a): " . $nm_responsavel);?>
                </td>
                <td>&nbsp;</td>
                <td align="center" width="32%">
                    <!--<hr size="10" style="height:2px; color:#000; background-color:#000; margin-top: 2px; margin-bottom: 2px;">-->
                    <?php echo str_pad("", 70, "-", STR_PAD_LEFT);?> <i class="fa fa-pencil"></i><br>
                    <?php echo $ds_funcional_ass3;?>
                    <!--Coordenação de Vigilância em Saúde-->
                </td>
            </tr>
        </table>
        <br>
        <table border="0" width="100%">
            <tr>
                <td>
                    <font size="2">E X E R C Í C I O :</font><br>
                    <font size="5"><strong><?php echo $nr_exercicio;?></strong></font><br>
                    <font size="2"><strong>AFIXAR EM LUGAR VISÍVEL</strong></font>
                </td>
                <td>
                    <font size="2">V A L I D A D E :</font><br>
                    <font size="5"><strong><?php echo $dt_validade;?></strong></font><br>
                    &nbsp;
                </td>
                <td>&nbsp;</td>
                <td align="right"><img src="../dist/img/castanhal_pa_lema.png"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><?php echo $ds_coordenacao;?></td>
            </tr>
        </table>
        
        <script>
            function ClosePrint() {
                  setTimeout(function(){
                      window.print(); 
                  }, 500);
                  window.onfocus = function(){
                      setTimeout(function () { window.close(); }, 500);
                  }
            }
        </script>
    </body>
</html>
<?php
//    // Importa arquivo de config da classe DOMPDF
//    require_once '../lib/dompdf/dompdf_config.inc.php';
//
//    /**
//     *  Função ob_get_clean obtém conteúdo que está no buffer
//     *  e exclui o buffer de saída atual.
//     *  http://br1.php.net/manual/pt_BR/function.ob-get-clean.php 
//     */
//    $html = ob_get_clean(); 
//    $pdf  = new DOMPDF();
//    
//    $pdf->set_paper("legal", "landscape"); // Altera o papel para modo paisagem
//    $pdf->load_html($html);
//    $pdf->render();
//    $pdf->stream("exemplo.pdf");
?>
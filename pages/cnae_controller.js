/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function formatarTabelaCnae() {
    // Configurando Tabela
    $('#tb_cnaes').DataTable({
        "paging": true,
        "pageLength": 10, // Apenas 10 registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "55px" }, // Código
            null,                 // Descrição
            null,                 // Seção
            { "width": "5px" },   // Ativo
            { "width": "5px" }    // Excluir
        ],
        "order": [[1, 'asc']], // "order": [] <-- Ordenação indefinida
        "language": {
                "paginate": {
                    "first"  : "<<", // Primeira página
                    "last"   : ">>", // Última página
                    "next"    : ">", // Próxima página
                    "previous": "<"  // Página anterior
                },
                "aria": {
                    "sortAscending" : ": ativar para classificação ascendente na coluna",
                    "sortDescending": ": ativar para classificação descendente na coluna"
                },
                "info": "Exibindo _PAGE_ / _PAGES_",
                "infoEmpty": "Sem dados para exibição",
                "infoFiltered":   "(Filtrada a partir de _MAX_ registros no total)",
                "zeroRecords": "Sem registro(s) para exibição",
                "lengthMenu": "Exibindo _MENU_ registro(s)",
                "loadingRecords": "Por favor, aguarde - carregando...",
                "processing": "Processando...",
                "search": "Localizar:"
        }
    });  
}

function PrepararPesquisaCnae() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarCnae() {
    var params = {
        'ac'    : $('#ac').val(),
        'token' : $('#token').val(),
        'tipo_pesquisa' : $('#tipo_pesquisa').val(),
        'pesquisa'      : $('#pesquisa').val()
    };
    
    document.body.style.cursor = "wait";
    
    var aguarde = "<div class='col-md-offset-0'>" +
                  "  <div class='box box-danger'>" +
                  "    <div class='box-header'>" +
                  "      <h3 class='box-title'>Carregando dados...</h3>" +
                  "    </div>" +
                  "    <div class='box-body'>" +
                  "      Favor aguarde o resultado da pesquisa de Cnaes cadastrados!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/cnae_dao.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            $('#resultado_pesquisa').html(aguarde);
            document.body.style.cursor = "auto";
        },
        // Colocamos o retorno na tela
        success : function(data){
            loadingGif(false);
            document.body.style.cursor = "auto";
            $('#resultado_pesquisa').html(data);
            $(".select2").select2();
            
            // Configurando Tabela
            formatarTabelaCnae();
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#resultado_pesquisa').html("Erro (" + status + "):<br><br>" + request.responseText + "<br>Error : " + error);
        }
    });  
    // Finalizamos o Ajax
}

function NovoCnae() {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaTabelaCnaeID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        $('#operacao').val("inserir");
        $('#cd_cnae').val("");
        $('#ds_cnae').val("");
        $('#tp_secao').val("XX");
        $('#nm_cnae').val("");
        $("#sn_obriga_insc_est").prop("checked", false);

        document.getElementById('cd_cnae').disabled  = false;

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#cd_cnae').focus();
        $(".select2").select2();
    });
}

function EditarCnae(cnae) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaTabelaCnaeID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var referencia = cnae.replace("cnae_", "");

        var cd_cnae = referencia;

        var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
        var colunas = i_linha.getElementsByTagName('td');

        $('#operacao').val("editar");
        $('#cd_cnae').val(cd_cnae);
        $('#ds_cnae').val(colunas[1].firstChild.nodeValue);
        $('#tp_secao').val( $("#cell_tp_secao_"  + referencia).val() );
        $('#nm_cnae').val( $("#cell_nm_cnae_"  + referencia).val() );

        $("#sn_obriga_insc_est").prop("checked", ($("#cell_sn_obriga_insc_est_" + referencia).val() === "1"));

        document.getElementById('cd_cnae').disabled  = true;

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#ds_cnae').focus();
        $(".select2").select2();
    });
} 

function SalvarCnae() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    //if ( $('#cd_cnae').val().trim()  === "" )   str_mensagem += str_marcador + "Código<br>";
    if ( $('#ds_cnae').val().trim()  === "" )   str_mensagem += str_marcador + "Descrição<br>";
    if ( $('#tp_secao').val().trim() === "XX" ) str_mensagem += str_marcador + "Seção<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var inserir = ($('#operacao').val() === "inserir");
        var params  = {
            'ac'      : 'salvar_cnae',
            'token'   : $('#token').val(),
            'operacao': $('#operacao').val(),
            'cd_cnae' : $('#cd_cnae').val().trim(),
            'ds_cnae' : $('#ds_cnae').val(),
            'tp_secao': $('#tp_secao').val(),
            'nm_cnae' : $('#nm_cnae').val().toUpperCase(),
            'sn_obriga_insc_est' : '0'
        };

        if ( $('#sn_obriga_insc_est').is(":checked") ) params.sn_obriga_insc_est = $('#sn_obriga_insc_est').val();
        if (params.cd_cnae === "") params.ds_cnae = params.ds_cnae.toUpperCase();

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/cnae_dao.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                document.body.style.cursor = "wait";
            },
            // Colocamos o retorno na tela
            success : function(data){
                document.body.style.cursor = "auto";
                var retorno = data;
                if ( retorno === "OK" ) {
                    var file_json = "logs/cnae_" + getTokenId() + ".json"; 

                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                    //var painel = document.getElementById("painel_cadastro_retorno"); 
                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                    $.getJSON(file_json, function(data){
                        this.qtd = data.formulario.length;

                        if ( inserir === true ) {
                            $('#cd_cnae').val(data.formulario[0].cd_cnae);
                            $('#ds_cnae').val(data.formulario[0].ds_cnae);
                            $('#nm_cnae').val(data.formulario[0].nm_cnae);
                            AddTableRowCnae();
                        } else {
                            // Recuperar linha da tabela para alterar valor de linha
                            var referencia = $('#cd_cnae').val();
                            var i_linha = document.getElementById("linha_" + referencia);
                            var colunas = i_linha.getElementsByTagName('td');

                            // Devolver valores para a linha/coluna
                            colunas[1].firstChild.nodeValue = params.ds_cnae; 
                            colunas[2].firstChild.nodeValue = $('#tp_secao option:selected').text(); 

                            // Armazenar valor retornado no campo oculto
                            document.getElementById('cell_tp_secao_' + referencia).value = params.tp_secao;
                            document.getElementById('cell_nm_cnae_'  + referencia).value = $('#nm_cnae').val();
                            document.getElementById('cell_sn_obriga_insc_est_' + referencia).value = params.sn_obriga_insc_est;

                            // Atualizar imagem da célula de acordo com o status do registro
                            $('#img_obriga_ie_' + referencia).removeClass("fa-check-square-o");
                            $('#img_obriga_ie_' + referencia).removeClass("fa-circle-thin");
                            if (params.sn_obriga_insc_est === '1') {
                                $('#img_obriga_ie_' + referencia).addClass("fa-check-square-o");
                            } else {
                                $('#img_obriga_ie_' + referencia).addClass("fa-circle-thin");
                            }
                        }

                        document.getElementById('painel_cadastro').style.display = 'none';
                    });
                } else {
                    MensagemErro("Erro", retorno);
                }
            },
            error: function (request, status, error) {
                document.body.style.cursor = "auto";
                MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    }
}

function ExcluirCnae(referencia, linha) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaTabelaCnaeID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia  = referencia.replace("excluir_cnae_", "");
        var cd_cnae = referencia;
        var qt_esta = $("#cell_qt_estabelecimento_"  + referencia).val();

        if ( strToInt(qt_esta) > 0 ) {
            MensagemAlerta("Restrição", "Registro não poderá ser excluído.<br><br><strong>Motivo:</strong><br>Registro associado ao Cadastro de Estabelecimentos.");
        } else {
            MensagemConfirmar(
                    "Excluir Registro", 
                    "Confirma a <strong>exclusão</strong> do Registro <strong>" + cd_cnae + "</strong> do Cnae selecionado?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'     : 'excluir_cnae',
                    'token'  : $('#token').val(),
                    'cd_cnae': cd_cnae
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/cnae_dao.php',
                    // Definimos o tipo de requisição
                    type: 'post',
                    // Definimos o tipo de retorno
                    dataType : 'html',
                    // Dolocamos os valores a serem enviados
                    data: params,
                    // Antes de enviar ele alerta para esperar
                    beforeSend : function(){
                        document.body.style.cursor = "wait";
                    },
                    // Colocamos o retorno na tela
                    success : function(data){
                        var retorno = data;
                        if ( retorno === "OK" ) {
                            document.body.style.cursor = "auto";
                            RemoveTableRow(linha);
                        } else {
                            document.body.style.cursor = "auto";
                            MensagemErro("Erro", retorno);
                        }
                    },
                    error: function (request, status, error) {
                        document.body.style.cursor = "auto";
                        MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
                    }
                });  
                // Finalizamos o Ajax
            }
        }
    });
}

(function($) {
    AddTableRowCnae = function() {

    var referencia = $('#cd_cnae').val();
    var tabela = "";
    var input  = "";
    var ativo  = "";

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("resultado_pesquisa"); 
    
    if ( pagina.innerHTML.indexOf("tb_cnaes") === -1 ) {
        tabela += "<div class='box box-info' id='box_resultado_pesquisa'>";
        tabela += "    <div class='box-header with-border'>";
        tabela += "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
        tabela += "    </div>";
        tabela += "    ";
        tabela += "    <div class='box-body'>";

        tabela += "<a id='ancora_cnaes'></a><table id='tb_cnaes' class='table table-bordered table-hover'  width='100%'>";

        tabela += "<thead>";
        tabela += "    <tr>";
        tabela += "        <th>Código</th>";
        tabela += "        <th>Descrição</th>";
        tabela += "        <th>Seção</th>";
        tabela += "        <th data-orderable='false'><center>IE</center></th>";    // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "        <th data-orderable='false'></th>";                          // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "    </tr>";
        tabela += "</thead>";
        tabela += "<tbody>";
        tabela += "</tbody>";
        tabela += "</table>";
        
        $('#resultado_pesquisa').html(tabela);
    }
    
    var editar  = "<a id='cnae_" + referencia + "' href='javascript:preventDefault();' onclick='EditarCnae( this.id )'>  " + $('#cd_cnae').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_cnae_" + referencia + "' href='javascript:preventDefault();' onclick='ExcluirCnae( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_tp_secao_" + referencia + "' value='" + $('#tp_secao').val() + "'/>"; 
    input += "<input type='hidden' id='cell_nm_cnae_"  + referencia + "' value='" + $('#nm_cnae').val() + "'/>"; 
    input += "<input type='hidden' id='cell_qt_estabelecimento_"  + referencia + "' value='0'/>"; 
    
    if ( $('#sn_obriga_insc_est').is(":checked") ) {
        input += "<input type='hidden' id='cell_sn_obriga_insc_est_" + referencia + "' value='1'/>"; 
        ativo  = "<i id='img_obriga_ie_" + referencia + "' class='fa fa-check-square-o'>&nbsp;" + input + "</i>";
    } else {
        input += "<input type='hidden' id='cell_sn_obriga_insc_est_" + referencia + "' value='0'/>"; 
        ativo  = "<i id='img_obriga_ie_" + referencia + "' class='fa fa-circle-thin'>&nbsp;"    + input + "</i>";
    }
    
    var newRow = $("<tr id='linha_" + referencia + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#ds_cnae').val().trim() + "</td>";
    cols += "<td>" + $('#tp_secao option:selected').text() + "</td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_cnaes").append(newRow);
    if ( tabela !== "" ) formatarTabelaCnae();

    return false;
  };
})(jQuery);
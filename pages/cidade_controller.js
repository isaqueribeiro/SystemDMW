/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*  Documentação para configurar DataTable
 * 
//                    {
//                        "decimal":        "",
//                        "emptyTable":     "No data available in table",
//                        "info":           "Showing _START_ to _END_ of _TOTAL_ entries",
//                        "infoEmpty":      "Showing 0 to 0 of 0 entries",
//                        "infoFiltered":   "(filtered from _MAX_ total entries)",
//                        "infoPostFix":    "",
//                        "thousands":      ",",
//                        "lengthMenu":     "Show _MENU_ entries",
//                        "loadingRecords": "Loading...",
//                        "processing":     "Processing...",
//                        "search":         "Search:",
//                        "zeroRecords":    "No matching records found",
//                        "paginate": {
//                            "first":      "First",
//                            "last":       "Last",
//                            "next":       "Next",
//                            "previous":   "Previous"
//                        },
//                        "aria": {
//                            "sortAscending":  ": activate to sort column ascending",
//                            "sortDescending": ": activate to sort column descending"
//                        }
//                    }                    
 * 
 */


function PrepararPesquisaCidade() {
//    document.body.style.cursor = "auto";
//    
//    $('#resultado_pesquisa').html(""); 
//    $("#tipo_pesquisa").val("0");
//    $(".select2").select2();
//    
//    $("#pesquisa").val("");
//    $("#pesquisa").focus();
}

function PesquisarCidade() {
//    var params = {
//        'ac'    : $('#ac').val(),
//        'token' : $('#token').val(),
//        'tipo_pesquisa' : '',
//        'pesquisa'      : '', 
//        'cd_sistema'    : getSistema()
//    };
//    
//    document.body.style.cursor = "wait";
//    
//    var aguarde = "<div class='col-md-offset-0'>" +
//                  "  <div class='box box-danger'>" +
//                  "    <div class='box-header'>" +
//                  "      <h3 class='box-title'>Carregando dados...</h3>" +
//                  "    </div>" +
//                  "    <div class='box-body'>" +
//                  "      Favor aguarde o resultado da pesquisa de perfis de acesso!" +
//                  "    </div>" +
//                  "    <div class=overlay>" +
//                  "      <i class='fa fa-refresh fa-spin'></i>" +
//                  "    </div>" +
//                  "  </div>" +
//                  "</div>";
//          
//    // Iniciamos o Ajax 
//    $.ajax({
//        // Definimos a url
//        url : 'pages/estado_dao.php',
//        // Definimos o tipo de requisição
//        type: 'post',
//        // Definimos o tipo de retorno
//        dataType : 'html',
//        // Dolocamos os valores a serem enviados
//        data: params,
//        // Antes de enviar ele alerta para esperar
//        beforeSend : function(){
//            $('#resultado_pesquisa').html(aguarde);
//            document.body.style.cursor = "auto";
//        },
//        // Colocamos o retorno na tela
//        success : function(data){
//            loadingGif(false);
//            document.body.style.cursor = "auto";
//            $('#resultado_pesquisa').html(data);
//            $(".select2").select2();
//            
//            // Configurando Tabela
//            $('#tb_estados').DataTable({
//                "paging"      : true,
//                "pageLength"  : 10, // Apenas 10 registros por paginação
//                "lengthChange": false,
//                "searching" : true,
//                "ordering"  : true,
//                "info"      : true,
//                "autoWidth" : true,
//                "processing": true,
//                "columns": [
//                    { "width": "15px" }, // Codigo
//                    null,                // Nome
//                    { "width": "5px" },  // UF
//                    { "width": "5px" },  // Siafi
//                    { "width": "15px"},  // Municípios
//                    { "width": "15px"}   // Alíquota Icms
//                ],
//                "order": [[0, 'asc']], // "order": [] <-- Ordenação indefinida
//                "language": {
//                        "paginate": {
//                            "first"  : "<<", // Primeira página
//                            "last"   : ">>", // Última página
//                            "next"    : ">", // Próxima página
//                            "previous": "<"  // Página anterior
//                        },
//                        "aria": {
//                            "sortAscending" : ": ativar para classificação ascendente na coluna",
//                            "sortDescending": ": ativar para classificação descendente na coluna"
//                        },
//                        "info": "Exibindo _PAGE_ / _PAGES_",
//                        "infoEmpty": "Sem dados para exibição",
//                        "infoFiltered":   "(Filtrada a partir de _MAX_ registros no total)",
//                        "zeroRecords": "Sem registro(s) para exibição",
//                        "lengthMenu": "Exibindo _MENU_ registro(s)",
//                        "loadingRecords": "Por favor, aguarde - carregando...",
//                        "processing": "Processando...",
//                        "search": "Localizar:"
//                }
//            });  
//        },
//        error: function (request, status, error) {
//            document.body.style.cursor = "auto";
//            $('#resultado_pesquisa').html("Erro (" + status + "):<br><br>" + request.responseText + "<br>Error : " + error);
//        }
//    });  
//    // Finalizamos o Ajax
}

//function NovaCidade() {
//    $('#cd_perfil').val("");
//    $('#ds_perfil').val("");
//    $('#qt_usuarios').val("0");
//    $('#tx_permissao').val("");
//    $("#sn_ativo").prop("checked", true);
//    
//    document.getElementById('painel_cadastro').style.display = 'block';
//    $('#ds_perfil').focus();
//}
//
function EditarCidade(estado) {
//    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoEstadoID][0].substr(0, 7); 
//    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
//        var cd_estado = estado.replace("estado_", "");
//        var i_linha   = document.getElementById("linha_" + cd_estado); // Capturar a linha da tabela correspondente ao ID
//        var colunas   = i_linha.getElementsByTagName('td');
//
//        $('#cd_estado').val(zeroEsquerda(cd_estado, 3));
//        $('#nm_estado').val(colunas[1].firstChild.nodeValue);
//        $('#uf_estado').val(colunas[2].firstChild.nodeValue);
//        $('#cd_siafi').val(colunas[3].firstChild.nodeValue);
//        $('#pc_aliquota_icms').val(colunas[5].firstChild.nodeValue);
//
//        document.getElementById('painel_cadastro').style.display = 'block';
//        $('#cd_siafi').focus();
//    });
} 

function SalvarCidade() {
//    var str_mensagem = "";
//    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
//    
//    if ( $('#cd_estado').val().trim() === "" ) str_mensagem += str_marcador + "Código<br>";
//    if ( $('#nm_estado').val().trim() === "" ) str_mensagem += str_marcador + "Nome<br>";
//    if ( $('#uf_estado').val().trim() === "" ) str_mensagem += str_marcador + "UF<br>";
//    
//    if ( str_mensagem.trim() !== "" ) {
//        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
//    } else {
//        var inserir = ($('#operacao').val() === "inserir");
//        var params  = {
//            'ac'      : 'salvar_estado',
//            'token'   : $('#token').val(),
//            'cd_estado' : $('#cd_estado').val(),
//            'nm_estado' : $('#nm_estado').val(),
//            'uf_estado' : $('#uf_estado').val(),
//            'cd_siafi'  : $('#cd_siafi').val(),
//            'pc_aliquota_icms'  : $('#pc_aliquota_icms').val().replace(",", ".")
//        };
//
//        if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();
//        
//        // Iniciamos o Ajax 
//        $.ajax({
//            // Definimos a url
//            url : 'pages/estado_dao.php',
//            // Definimos o tipo de requisição
//            type: 'post',
//            // Definimos o tipo de retorno
//            dataType : 'html',
//            // Dolocamos os valores a serem enviados
//            data: params,
//            // Antes de enviar ele alerta para esperar
//            beforeSend : function(){
//                document.body.style.cursor = "wait";
//            },
//            // Colocamos o retorno na tela
//            success : function(data){
//                document.body.style.cursor = "auto";
//                var retorno = data;
//                if ( retorno === "OK" ) {
//                    var file_json = "logs/estado_" + getTokenId() + ".json"; 
//
//                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
//                    //var painel = document.getElementById("painel_cadastro_retorno"); 
//                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;
//
//                    $.getJSON(file_json, function(data){
//                        this.qtd = data.formulario.length;
//                        if ( this.qtd > 0 ) {
//                            $('#cd_estado').val ( zeroEsquerda(parseInt(data.formulario[this.qtd - 1].cd_estado), 3) );
//                            $('#uf_estado').val ( data.formulario[this.qtd - 1].uf_estado );
//                        }
//
//                        if ( inserir === true ) {
//                            AddTableRowEstado();
//                        } else {
//                            // Recuperar linha da tabela para alterar valor de linha
//                            var i_linha = document.getElementById("linha_" + strToInt($('#cd_estado').val()));
//                            var colunas = i_linha.getElementsByTagName('td');
//
//                            // Devolver valor da descrição para a linha/coluna
//                            colunas[1].firstChild.nodeValue = $('#nm_estado').val(); 
//                            colunas[2].firstChild.nodeValue = $('#uf_estado').val(); 
//                            colunas[3].firstChild.nodeValue = $('#cd_siafi').val(); 
//                            colunas[5].firstChild.nodeValue = $('#pc_aliquota_icms').val(); 
//                        }
//
//                        document.getElementById('painel_cadastro').style.display = 'none';
//                    });
//                } else {
//                    MensagemErro("Erro", data);
//                }
//            },
//            error: function (request, status, error) {
//                document.body.style.cursor = "auto";
//                MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
//            }
//        });  
//        // Finalizamos o Ajax
//    }
}

function ExcluirCidade(estado, linha) {
//    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoEstadoID][0].substr(0, 7);
//    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
//        var cd_estado  = estado.replace("excluir_estado_", "");
//        var i_linha    = document.getElementById("linha_" + parseInt(cd_estado));
//        var colunas    = i_linha.getElementsByTagName('td');
//        var municipios = colunas[4].firstChild.nodeValue;
//
//        if (parseInt(municipios) > 0) {
//            MensagemAlerta("Restrição", "O Estado não poderá ser excluído!<br><br><strong>Motivo : </strong><br>Este registros possue Municípios associadas.");
//        } else {
//            MensagemConfirmar(
//                    "Excluir Registro", 
//                    "Confirma a <strong>exclusão</strong> do Registro <strong>" + zeroEsquerda(cd_estado, 3) + "</strong> do estado selecionado?", 
//                    "300px");
//            var link = document.getElementById("botao_confirma_main_sim");
//            link.onclick = function() {
//                document.getElementById("painel_confirma_main").style.display = 'none';
//                var params  = {
//                    'ac'        : 'excluir_estado',
//                    'token'     : $('#token').val(),
//                    'cd_estado' : cd_estado
//                };
//
//                // Iniciamos o Ajax 
//                $.ajax({
//                    // Definimos a url
//                    url : 'pages/estado_dao.php',
//                    // Definimos o tipo de requisição
//                    type: 'post',
//                    // Definimos o tipo de retorno
//                    dataType : 'html',
//                    // Dolocamos os valores a serem enviados
//                    data: params,
//                    // Antes de enviar ele alerta para esperar
//                    beforeSend : function(){
//                        document.body.style.cursor = "wait";
//                    },
//                    // Colocamos o retorno na tela
//                    success : function(data){
//                        var retorno = data;
//                        if ( retorno === "OK" ) {
//                            document.body.style.cursor = "auto";
//                            RemoveTableRow(linha);
//                        } else {
//                            document.body.style.cursor = "auto";
//                            MensagemErro("Erro", data);
//                        }
//                    },
//                    error: function (request, status, error) {
//                        document.body.style.cursor = "auto";
//                        MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
//                    }
//                });  
//                // Finalizamos o Ajax
//            }
//        }
//    });
}

function select_ListarCidades(obj, div, seletor) {
    var params  = {
        'ac'       : 'listar_cidade',
        'token'    : $('#token').val(),
        'cd_estado': $('#' + obj).val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/cidade_dao.php',
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
                
                var _select = "";
                
                // Garantir o preenchimento da lista mesmo que não haja dados
                _select  = "<select class='form-control select2' name='" + seletor + "' id='" + seletor + "'>";
                _select += "<option value='0' selected='selected'>Selecionar o Município</option>";
                _select += "</select>";
                $('#' + div).html(_select);
                $(".select2").select2();
                
                var file_json = "logs/cidades_" + getTokenId() + ".json"; 
                $.getJSON(file_json, function(data){
                    this.qtd = data.cidades.length;
                    for (var i = 0; i < this.qtd; ++i ) {
                        if (i === 0) {
                            _select  = "<select class='form-control select2' name='" + seletor + "' id='" + seletor + "'>";
                            _select += "<option value='0' selected='selected'>Selecionar o Município</option>";
                        }
                        _select += "<option value='" + data.cidades[i].cd_cidade + "'>" + data.cidades[i].nm_cidade + "</option>";
                        if (i === (this.qtd - 1)) {
                            _select += "</select>";
                            $('#' + div).html(_select);
                            $(".select2").select2();
                            
                            // Redefinindo as chamadas de funções para os objetos que fazem uso desta função
                            var link = document.getElementById("cidade_pesquisa");
                            link.onchange = function() {
                                select_ListarBairros('cidade_pesquisa', 'div_bairro_pesquisa', 'bairro_pesquisa');
                            }
                            
                            var link = document.getElementById("cd_cidade");
                            link.onchange = function() {
                                select_ListarBairros('cd_cidade', 'div_bairro', 'cd_bairro');
                            }
                        }
                    }
                });
                
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

(function($) {
    AddTableRowCidade = function() {

//    var codigo = strToInt( $('#cd_perfil').val() );
//    var input  = "<input type='hidden' id='cell_usuarios_"  + codigo + "'  value='0'/>";
//        input += "<input type='hidden' id='cell_permissao_" + codigo + "' value=''/>";
//    var ativo  = "";
//    var acesso = "";
//    
//    var editar  = "<a id='perfil_" + codigo + "' href='javascript:preventDefault();' onclick='EditarPerfil( this.id )'>  " + $('#cd_perfil').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
//    var excluir = "<a id='excluir_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ExcluirPerfil( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";
//
//    if ( $('#sn_ativo').is(":checked") ) {
//        input += "<input type='hidden' id='cell_ativo_" + codigo + "' value='1'/>";
//        ativo  = "<i id='img_ativo_" + codigo + "' class='fa fa-check-square-o'>&nbsp;" + input + "</i>";
//    } else {
//        input += "<input type='hidden' id='cell_ativo_" + codigo + "' value='0'/>";
//        ativo  = "<i id='img_ativo_" + codigo + "' class='fa fa-circle-thin'>&nbsp;"    + input + "</i>";
//    }
//    
//    if ($('#tx_permissao').val().trim() === "") {
//        acesso = "<a id='configurar_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_" + codigo + "' class='fa fa-cog' title='Configurar acessos do Perfil'>&nbsp;</i>";
//    } else {
//        acesso = "<a id='configurar_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_" + codigo + "' class='fa fa-cogs' title='Configurar acessos do Perfil'>&nbsp;</i>";
//    }
//    
//    var newRow = $("<tr id='linha_" + codigo + "'>");
//    var cols = "";
//
//    cols += "<td>" + editar + "</td>";
//    cols += "<td>" + $('#ds_perfil').val().trim() + "</td>";
//    cols += "<td align=right>"  + $('#qt_usuarios').val().trim() + "</td>";
//    cols += "<td align=center>" + acesso  + "</td>";
//    cols += "<td align=center>" + ativo   + "</td>";
//    cols += "<td align=center>" + excluir + "</td>";
//    
//    newRow.append(cols);
//    
//    $("#tb_perfis").append(newRow);

    return false;
  };
})(jQuery);


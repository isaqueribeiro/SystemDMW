/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function PrepararPesquisaBairro() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#estado_pesquisa").val( $("#cd_estado_padrao").val() );
    $("#cidade_pesquisa").val( $("#cd_cidade_padrao").val() );
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarBairro() {
    var params = {
        'ac'    : $('#ac').val(),
        'token' : $('#token').val(),
        'estado_pesquisa' : $('#estado_pesquisa').val(),
        'cidade_pesquisa' : $('#cidade_pesquisa').val(),
        'tipo_pesquisa'   : $('#tipo_pesquisa').val(),
        'pesquisa'        : $('#pesquisa').val()
    };
    
    document.body.style.cursor = "wait";
    
    var aguarde = "<div class='col-md-offset-0'>" +
                  "  <div class='box box-danger'>" +
                  "    <div class='box-header'>" +
                  "      <h3 class='box-title'>Carregando dados...</h3>" +
                  "    </div>" +
                  "    <div class='box-body'>" +
                  "      Favor aguarde o resultado da pesquisa de bairros cadastrados!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/bairro_dao.php',
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
            $('#tb_bairros').DataTable({
                "paging": true,
                "pageLength": 10, // Apenas 10 registros por paginação
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "processing": true,
                "columns": [
                    { "width": "15px" }, // Código
                    null,                // Descrião
                    { "width": "5px" },  // Estabeleciomentos
                    { "width": "5px" }   // Excluir
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
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#resultado_pesquisa').html("Erro (" + status + "):<br><br>" + request.responseText + "<br>Error : " + error);
        }
    });  
    // Finalizamos o Ajax
}

function NovoBairro() {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoBairroID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        $('#cd_bairro').val("");
        $('#nm_bairro').val("");
        $('#qt_estabelecimento').val("0");

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nm_bairro').focus();
        $(".select2").select2();
    });
}

function EditarBairro(bairro) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoBairroID][0].substr(0, 7); 
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var codigo = bairro.replace("bairro_", "");

        var cd_estado = $("#cell_cd_estado_" + codigo).val();
        var cd_cidade = $("#cell_cd_cidade_" + codigo).val();
        var qt_estabe = $("#cell_qt_estabelecimento_" + codigo).val();

        var i_linha = document.getElementById("linha_" + codigo); // Capturar a linha da tabela correspondente ao ID
        var colunas = i_linha.getElementsByTagName('td');

        $('#cd_bairro').val(zeroEsquerda(codigo, 5));
        $('#nm_bairro').val(colunas[1].firstChild.nodeValue);

        $('#cd_estado').val(cd_estado);
        $('#cd_cidade').val(cd_cidade);
        $('#qt_estabelecimento').val(qt_estabe);

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nm_bairro').focus();
        $(".select2").select2();
    });
} 

function SalvarBairro() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#nm_bairro').val().trim() === "" ) str_mensagem += str_marcador + "Descrição<br>";
    if ( $('#cd_estado').val() === "0" ) str_mensagem += str_marcador + "Estado<br>";
    if ( $('#cd_cidade').val() === "0" ) str_mensagem += str_marcador + "Município<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var inserir = ($('#cd_bairro').val() === "");
        var params  = {
            'ac'      : 'salvar_bairro',
            'token'   : $('#token').val(),
            'cd_bairro' : $('#cd_bairro').val(),
            'nm_bairro' : $('#nm_bairro').val(),
            'cd_estado' : $('#cd_estado').val(),
            'cd_cidade' : $('#cd_cidade').val()
        };

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/bairro_dao.php',
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
                    var file_json = "logs/bairro_" + getTokenId() + ".json"; 

                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                    //var painel = document.getElementById("painel_cadastro_retorno"); 
                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                    $.getJSON(file_json, function(data){
                        this.qtd = data.formulario.length;

                        if ( inserir === true ) {
                            $('#cd_bairro').val ( data.formulario[this.qtd - 1].cd_bairro );
                            AddTableRowBairro();
                        } else {
                            // Recuperar linha da tabela para alterar valor de linha
                            var codigo = strToInt($('#cd_bairro').val());
                            var i_linha = document.getElementById("linha_" + codigo);
                            var colunas = i_linha.getElementsByTagName('td');

                            // Devolver valores para a linha/coluna
                            colunas[1].firstChild.nodeValue = params.nm_bairro; 

                            // Armazenar valor retornado no campo oculto
                            document.getElementById('cell_cd_bairro_' + codigo).value = codigo;
                            document.getElementById('cell_cd_estado_' + codigo).value = $('#cd_estado').val();
                            document.getElementById('cell_cd_cidade_' + codigo).value = $('#cd_cidade').val();
                            document.getElementById('cell_qt_estabelecimento_' + codigo).value = $('#qt_estabelecimento').val();
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

function ExcluirBairro(bairro, linha) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoBairroID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        var codigo = bairro.replace("excluir_bairro_", "");
        var cd_bairro = $('#cell_cd_bairro_' + codigo).val();
        var qt_estabe = $('#cell_qt_estabelecimento_' + codigo).val();

        if ( strToInt(qt_estabe) > 0 ) {
            MensagemAlerta("Restrição", "Registro não poderá ser excluído.<br><br><strong>Motivo:</strong><br>Registro associado ao Cadastro de Estabelecimentos.");
        } else {
            MensagemConfirmar(
                    "Excluir Registro", 
                    "Confirma a <strong>exclusão</strong> do Registro <strong>" + cd_bairro + "</strong> do bairro selecionado?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'       : 'excluir_bairro',
                    'token'    : $('#token').val(),
                    'cd_bairro': cd_bairro
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/bairro_dao.php',
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

function select_ListarBairros(obj, div, seletor) {
    var params  = {
        'ac'       : 'listar_bairro',
        'token'    : $('#token').val(),
        'cd_cidade': $('#' + obj).val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/bairro_dao.php',
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
                _select += "<option value='0' selected='selected'>Selecionar o Bairro</option>";
                _select += "</select>";
                $('#' + div).html(_select);
                $(".select2").select2();
                
                var file_json = "logs/bairros_" + getTokenId() + ".json"; 
                $.getJSON(file_json, function(data){
                    this.qtd = data.bairros.length;
                    for (var i = 0; i < this.qtd; ++i ) {
                        if (i === 0) {
                            _select  = "<select class='form-control select2' name='" + seletor + "' id='" + seletor + "'>";
                            _select += "<option value='0' selected='selected'>Selecionar o Bairro</option>";
                        }
                        _select += "<option value='" + data.bairros[i].cd_bairro + "'>" + data.bairros[i].nm_bairro + "</option>";
                        if (i === (this.qtd - 1)) {
                            _select += "</select>";
                            $('#' + div).html(_select);
                            $(".select2").select2();
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
    AddTableRowBairro = function() {

    var codigo = strToInt($('#cd_bairro').val());
    var input  = "";

    var editar  = "<a id='bairro_" + codigo + "' href='javascript:preventDefault();' onclick='EditarBairro( this.id )'>  " + zeroEsquerda(codigo, 5) + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_bairro_" + codigo + "' href='javascript:preventDefault();' onclick='ExcluirBairro( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_cd_bairro_"  + codigo + "' value='" + codigo + "'/>"; 
    input += "<input type='hidden' id='cell_cd_estado_"  + codigo + "' value='" + $('#cd_estado').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_cidade_"  + codigo + "' value='" + $('#cd_cidade').val() + "'/>"; 
    input += "<input type='hidden' id='cell_qt_estabelecimento_" + codigo + "' value='0'/>"; 
    
    var newRow = $("<tr id='linha_" + codigo + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#nm_bairro').val().trim() + "</td>";
    cols += "<td align=right>0</td>";
    cols += "<td align=center>" + input + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_bairros").append(newRow);

    return false;
  };
})(jQuery);
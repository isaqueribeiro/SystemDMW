/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function PrepararPesquisaCep() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#estado_pesquisa").val( $("#cd_estado_padrao").val() );
    $("#cidade_pesquisa").val( $("#cd_cidade_padrao").val() );
    $("#bairro_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
    
    select_ListarBairros('cidade_pesquisa', 'div_bairro_pesquisa', 'bairro_pesquisa');
}

function PesquisarCep() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#estado_pesquisa').val() === "0" ) str_mensagem += str_marcador + "Estado<br>";
    if ( $('#cidade_pesquisa').val() === "0" ) str_mensagem += str_marcador + "Cidade/Município<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var params = {
            'ac'    : $('#ac').val(),
            'token' : $('#token').val(),
            'estado_pesquisa' : $('#estado_pesquisa').val(),
            'cidade_pesquisa' : $('#cidade_pesquisa').val(),
            'bairro_pesquisa' : $('#bairro_pesquisa').val(),
            'pesquisa'        : $('#pesquisa').val()
        };

        document.body.style.cursor = "wait";

        var aguarde = "<div class='col-md-offset-0'>" +
                      "  <div class='box box-danger'>" +
                      "    <div class='box-header'>" +
                      "      <h3 class='box-title'>Carregando dados...</h3>" +
                      "    </div>" +
                      "    <div class='box-body'>" +
                      "      Favor aguarde o resultado da pesquisa de ceps cadastrados!" +
                      "    </div>" +
                      "    <div class=overlay>" +
                      "      <i class='fa fa-refresh fa-spin'></i>" +
                      "    </div>" +
                      "  </div>" +
                      "</div>";

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/cep_dao.php',
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
                $('#tb_ceps').DataTable({
                    "paging": true,
                    "pageLength": 10, // Apenas 10 registros por paginação
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "processing": true,
                    "columns": [
                        { "width": "80px" }, // Cep
                        null,                // Endereço
                        null,                // Bairro
                        null,                // Cidade
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
}

function NovoCep() {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoCepID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        $('#operacao').val("inserir");
        $('#nr_cep').val("");
        $('#cd_estado').val( $('#cd_estado_padrao').val() );
        $('#cd_cidade').val("0");
        $('#cd_bairro').val("0");
        $('#cd_tipo').val("0");
        $('#ds_logradouro').val("");

        $('#cd_estado').trigger('onchange');
        document.getElementById('nr_cep').disabled  = false;
        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nr_cep').focus();
        $(".select2").select2();
    });
}

function EditarCep(cep) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoCepID][0].substr(0, 7); 
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var codigo = cep.replace("cep_", "");

        var nr_cep    = $("#cell_nr_cep_"    + codigo).val();
        var cd_estado = $("#cell_cd_estado_" + codigo).val();
        var cd_cidade = $("#cell_cd_cidade_" + codigo).val();
        var cd_bairro = $("#cell_cd_bairro_" + codigo).val();
        var cd_tipo   = $("#cell_cd_tipo_"   + codigo).val();
        var ds_logradouro = $("#cell_ds_logradouro_" + codigo).val();

        $('#operacao').val("editar");
        $('#nr_cep').val(nr_cep);
        $('#cd_estado').val(cd_estado);
        
        // Iniciamos o Ajax para carregar a Lista de Cidades, 
        // selecionando a cidade correspondente ao registros
        var paramsCidades  = {
            'ac'       : 'listar_cidade',
            'token'    : $('#token').val(),
            'cd_estado': cd_estado
        };

        $.ajax({
            // Definimos a url
            url : 'pages/cidade_dao.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: paramsCidades,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                document.body.style.cursor = "wait";
            },
            // Colocamos o retorno na tela
            success : function(data){
                var retorno = data;
                if ( retorno === "OK" ) {
                    document.body.style.cursor = "auto";

                    var _selectCidade = "";

                    // Garantir o preenchimento da lista mesmo que não haja dados
                    _selectCidade  = "<select class='form-control select2' name='cd_cidade' id='cd_cidade'>";
                    _selectCidade += "<option value='0' selected='selected'>Selecionar o Município</option>";
                    _selectCidade += "</select>";
                    $('#div_cidade').html(_selectCidade);
                    $(".select2").select2();

                    var file_jsonCidade = "logs/cidades_" + getTokenId() + ".json"; 
                    $.getJSON(file_jsonCidade, function(data){
                        this.qtd = data.cidades.length;
                        for (var i = 0; i < this.qtd; ++i ) {
                            if (i === 0) {
                                _selectCidade  = "<select class='form-control select2' name='cd_cidade' id='cd_cidade'>";
                                _selectCidade += "<option value='0' selected='selected'>Selecionar o Município</option>";
                            }
                            _selectCidade += "<option value='" + data.cidades[i].cd_cidade + "'>" + data.cidades[i].nm_cidade + "</option>";
                            if (i === (this.qtd - 1)) {
                                _selectCidade += "</select>";
                                $('#div_cidade').html(_selectCidade);
                                $('#cd_cidade').val(cd_cidade);
                                $(".select2").select2();

                                var link = document.getElementById("cd_cidade");
                                link.onchange = function() {
                                    select_ListarBairros('cd_cidade', 'div_bairro', 'cd_bairro');
                                }
                                
                                
                                // Iniciamos o Ajax para carregar a Lista de Bairros, 
                                // selecionando o bairro correspondente ao registros
                                var paramsBairros  = {
                                    'ac'       : 'listar_bairro',
                                    'token'    : $('#token').val(),
                                    'cd_cidade': cd_cidade
                                };
                                
                                $.ajax({
                                    // Definimos a url
                                    url : 'pages/bairro_dao.php',
                                    // Definimos o tipo de requisição
                                    type: 'post',
                                    // Definimos o tipo de retorno
                                    dataType : 'html',
                                    // Dolocamos os valores a serem enviados
                                    data: paramsBairros,
                                    // Antes de enviar ele alerta para esperar
                                    beforeSend : function(){
                                        document.body.style.cursor = "wait";
                                    },
                                    // Colocamos o retorno na tela
                                    success : function(data){
                                        var retorno = data;
                                        if ( retorno === "OK" ) {
                                            document.body.style.cursor = "auto";

                                            var _selectBairro = "";

                                            // Garantir o preenchimento da lista mesmo que não haja dados
                                            _selectBairro  = "<select class='form-control select2' name='cd_bairro' id='cd_bairro'>";
                                            _selectBairro += "<option value='0' selected='selected'>Selecionar o Bairro</option>";
                                            _selectBairro += "</select>";
                                            $('#div_bairro').html(_selectBairro);
                                            $(".select2").select2();

                                            var file_jsonBairro = "logs/bairros_" + getTokenId() + ".json"; 
                                            $.getJSON(file_jsonBairro, function(data){
                                                this.qtd = data.bairros.length;
                                                for (var i = 0; i < this.qtd; ++i ) {
                                                    if (i === 0) {
                                                        _selectBairro  = "<select class='form-control select2' name='cd_bairro' id='cd_bairro'>";
                                                        _selectBairro += "<option value='0' selected='selected'>Selecionar o Bairro</option>";
                                                    }
                                                    _selectBairro += "<option value='" + data.bairros[i].cd_bairro + "'>" + data.bairros[i].nm_bairro + "</option>";
                                                    if (i === (this.qtd - 1)) {
                                                        _selectBairro += "</select>";
                                                        $('#div_bairro').html(_selectBairro);
                                                        $('#cd_bairro').val(cd_bairro);    
                                                        $('#cd_tipo').val(cd_tipo);
                                                        $('#ds_logradouro').val(ds_logradouro);

                                                        document.getElementById('nr_cep').disabled  = true;
                                                        document.getElementById('painel_cadastro').style.display = 'block';
                                                        $('#ds_logradouro').focus();
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
    });
} 

function SalvarCep() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#nr_cep').val().trim() === "" ) str_mensagem += str_marcador + "Número do Cep<br>";
    if ( $('#cd_estado').val() === "0" ) str_mensagem += str_marcador + "Estado<br>";
    if ( $('#cd_cidade').val() === "0" ) str_mensagem += str_marcador + "Município<br>";
    if ( $('#cd_bairro').val() === "0" ) str_mensagem += str_marcador + "Bairro<br>";
    if ( $('#cd_tipo').val()   === "0" ) str_mensagem += str_marcador + "Tipo<br>";
    if ( $('#ds_logradouro').val().trim() === "" ) str_mensagem += str_marcador + "Logradouro<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var inserir = ($('#operacao').val() === "inserir");
        var params  = {
            'ac'      : 'salvar_cep',
            'token'   : $('#token').val(),
            'operacao': $('#operacao').val(),
            'nr_cep'  : $('#nr_cep').val().replace('.', '').replace(/-/g, ''),
            'cd_estado' : $('#cd_estado').val(),
            'cd_cidade' : $('#cd_cidade').val(),
            'cd_bairro' : $('#cd_bairro').val(),
            'cd_tipo'   : $('#cd_tipo').val(),
            'ds_logradouro': $('#ds_logradouro').val(),
            'nm_bairro'  : '',
            'nm_cidade'  : '',
            'ds_endereco': '',
            'uf': ''
        };

        params.nm_bairro   = $('#cd_bairro option:selected').text();
        params.nm_cidade   = $('#cd_cidade option:selected').text();
        params.ds_endereco = $('#cd_tipo option:selected').text() + " " + params.ds_logradouro;
        params.ds_endereco = params.ds_endereco.trim();
        params.uf = params.nm_cidade.substr(-3).replace(")", "");

        params.nm_cidade = params.nm_cidade.replace("(" + params.uf + ")", "").trim();
        
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/cep_dao.php',
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
                    var file_json = "logs/cep_" + getTokenId() + ".json"; 

                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                    //var painel = document.getElementById("painel_cadastro_retorno"); 
                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                    $.getJSON(file_json, function(data){
                        this.qtd = data.formulario.length;

                        if ( inserir === true ) {
                            AddTableRowCep();
                        } else {
                            // Recuperar linha da tabela para alterar valor de linha
                            var codigo = params.nr_cep;
                            var i_linha = document.getElementById("linha_" + codigo);
                            var colunas = i_linha.getElementsByTagName('td');

                            // Devolver valores para a linha/coluna
                            colunas[1].firstChild.nodeValue = params.ds_endereco; 
                            colunas[2].firstChild.nodeValue = $('#cd_bairro option:selected').text();
                            colunas[3].firstChild.nodeValue = $('#cd_cidade option:selected').text();

                            // Armazenar valor retornado no campo oculto
                            document.getElementById('cell_nr_cep_'    + codigo).value = $('#nr_cep').val();
                            document.getElementById('cell_cd_estado_' + codigo).value = $('#cd_estado').val();
                            document.getElementById('cell_cd_cidade_' + codigo).value = $('#cd_cidade').val();
                            document.getElementById('cell_cd_bairro_' + codigo).value = $('#cd_bairro').val();
                            document.getElementById('cell_cd_tipo_'   + codigo).value = $('#cd_tipo').val();
                            document.getElementById('cell_ds_logradouro_' + codigo).value = params.ds_logradouro;
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

function ExcluirCep(cep, linha) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoCepID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        var codigo = cep.replace("excluir_cep_", "");
        var nr_cep = $('#cell_nr_cep_' + codigo).val();

        MensagemConfirmar(
                "Excluir Registro", 
                "Confirma a <strong>exclusão</strong> do CEP <strong>" + nr_cep + "</strong> no registro selecionado?", 
                "300px");
                
        var link = document.getElementById("botao_confirma_main_sim");
        link.onclick = function() {
            document.getElementById("painel_confirma_main").style.display = 'none';
            var params  = {
                'ac'    : 'excluir_cep',
                'token' : $('#token').val(),
                'nr_cep': codigo
            };

            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/cep_dao.php',
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
    });
}

(function($) {
    AddTableRowCep = function() {

    var codigo = $('#nr_cep').val().replace('.', '').replace(/-/g, '');
    var input  = "";

    var editar  = "<a id='cep_" + codigo + "' href='javascript:preventDefault();' onclick='EditarCep( this.id )'>  " + $('#nr_cep').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_cep_" + codigo + "' href='javascript:preventDefault();' onclick='ExcluirCep( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_nr_cep_"         + codigo + "' value='" + $('#nr_cep').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_estado_"      + codigo + "' value='" + $('#cd_estado').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_cidade_"      + codigo + "' value='" + $('#cd_cidade').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_bairro_"      + codigo + "' value='" + $('#cd_bairro').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_tipo_"        + codigo + "' value='" + $('#cd_tipo').val() + "'/>"; 
    input += "<input type='hidden' id='cell_ds_logradouro_"  + codigo + "' value='" + $('#ds_logradouro').val() + "'/>"; 
    
    var newRow = $("<tr id='linha_" + codigo + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#cd_tipo option:selected').text() + " " + $('#ds_logradouro').val() + "</td>";
    cols += "<td>" + $('#cd_bairro option:selected').text() + "</td>";
    cols += "<td>" + $('#cd_cidade option:selected').text() + "</td>";
    cols += "<td align=center>" + input + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_ceps").append(newRow);

    return false;
  };
})(jQuery);
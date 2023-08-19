/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function PrepararPesquisaTecnico() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarTecnico() {
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
                  "      Favor aguarde o resultado da pesquisa de técnicos cadastrados!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/tecnico_dao.php',
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
            $('#tb_tecnicos').DataTable({
                "paging": true,
                "pageLength": 10, // Apenas 10 registros por paginação
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "processing": true,
                "columns": [
                    { "width": "100px" },  // CPF
                    null,                // Nome do Técnico
                    { "width": "5px" },  // Ativo
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

function NovoTecnico() {
    var rotina = menus[menuCadastroID][2][rotinaCadastroTecnicoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var now = new Date();

        $('#id_tecnico').val(getGuidEmpty());
        $('#nr_cpf').val("");
        $('#nm_tecnico').val("");
        $("#sn_ativo").prop("checked", true);

        document.getElementById('nr_cpf').disabled  = false;

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nr_cpf').focus();
        $(".select2").select2();
    });
}

function EditarTecnico(tecnico) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroTecnicoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var referencia = tecnico.replace("tecnico_", "");

        var id_tecnico = $("#cell_id_tecnico_" + referencia).val();

        var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
        var colunas = i_linha.getElementsByTagName('td');

        $('#id_tecnico').val(id_tecnico);
        $('#nr_cpf').val( $("#cell_nr_cpf_"  + referencia).val() );
        $('#nm_tecnico').val(colunas[1].firstChild.nodeValue);

        $("#sn_ativo").prop("checked", ($("#cell_ativo_" + referencia).val() === "1"));

        document.getElementById('nr_cpf').disabled  = true;

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nm_tecnico').focus();
        $(".select2").select2();
    });
} 

function SalvarTecnico() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#nr_cpf').val().trim()     === "" ) str_mensagem += str_marcador + "CPF<br>";
    if ( $('#nm_tecnico').val().trim() === "" ) str_mensagem += str_marcador + "Nome Completo<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var cpf = $('#nr_cpf').val().replace(".", "").replace("-", "");
        if ( !validarCPF(cpf) ) {
            MensagemErro("Validação", "Número de CPF inválido!");
        } else {
            var inserir = ($('#id_tecnico').val() === "") || ($('#id_tecnico').val() === getGuidEmpty());
            var params  = {
                'ac'      : 'salvar_tecnico',
                'token'   : $('#token').val(),
                'id_tecnico' : $('#id_tecnico').val(),
                'nm_tecnico' : $('#nm_tecnico').val(),
                'nr_cpf'   : $('#nr_cpf').val().replace(".", "").replace("-", ""),
                'sn_ativo' : '0'
            };

            if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();

            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/tecnico_dao.php',
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
                        var file_json = "logs/tecnico_" + getTokenId() + ".json"; 

                        // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                        //var painel = document.getElementById("painel_cadastro_retorno"); 
                        //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                        $.getJSON(file_json, function(data){
                            this.qtd = data.formulario.length;

                            if ( inserir === true ) {
                                $('#id_tecnico').val ( data.formulario[this.qtd - 1].id_tecnico );
                                AddTableRowTecnico();
                            } else {
                                // Recuperar linha da tabela para alterar valor de linha
                                var referencia = $('#id_tecnico').val().replace(/-/g,'').replace("{", "").replace("}", "");
                                var i_linha = document.getElementById("linha_" + referencia);
                                var colunas = i_linha.getElementsByTagName('td');

                                // Devolver valores para a linha/coluna
                                colunas[1].firstChild.nodeValue = params.nm_tecnico; 

                                // Armazenar valor retornado no campo oculto
                                document.getElementById('cell_id_tecnico_' + referencia).value = $('#id_tecnico').val();
                                document.getElementById('cell_ativo_'      + referencia).value = params.sn_ativo;

                                // Atualizar imagem da célula de acordo com o status do registro
                                $('#img_ativo_' + referencia).removeClass("fa-check-square-o");
                                $('#img_ativo_' + referencia).removeClass("fa-circle-thin");
                                if (params.sn_ativo === '1') {
                                    $('#img_ativo_' + referencia).addClass("fa-check-square-o");
                                } else {
                                    $('#img_ativo_' + referencia).addClass("fa-circle-thin");
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
}

function ExcluirTecnico(referencia, linha) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroTecnicoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia = referencia.replace("excluir_tecnico_", "");
        var id_tecnico  = $('#cell_id_tecnico_' + referencia).val();
        var nr_usuarios = $('#cell_nr_usuarios_' + referencia).val();

        if ( $("#cell_cd_perfil_"  + referencia).val() < getPfUsuario() ) {
            MensagemAlerta("Restrição", "Usuário sem permissão para excluir regitros com <strong>perfis de acesso superior</strong>!");
        } else 
        if ( strToInt(nr_usuarios) > 0 ) {
            MensagemAlerta("Restrição", "Registro não poderá ser excluído.<br><br><strong>Motivo:</strong><br>Registro associado ao Cadastro de Usuários.");
        } else {
            MensagemConfirmar(
                    "Excluir Registro", 
                    "Confirma a <strong>exclusão</strong> do Registro <strong>" + id_tecnico + "</strong> do técnico selecionado?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'        : 'excluir_tecnico',
                    'token'     : $('#token').val(),
                    'id_tecnico': id_tecnico
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/tecnico_dao.php',
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
    AddTableRowTecnico = function() {

    var referencia = $('#id_tecnico').val().replace(/-/g,'').replace("{", "").replace("}", "");
    var input  = "";
    var ativo  = "";

    var editar  = "<a id='tecnico_" + referencia + "' href='javascript:preventDefault();' onclick='EditarTecnico( this.id )'>  " + $('#nr_cpf').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_tecnico_" + referencia + "' href='javascript:preventDefault();' onclick='ExcluirTecnico( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_id_tecnico_"  + referencia + "' value='" + $('#id_tecnico').val() + "'/>"; 
    input += "<input type='hidden' id='cell_nr_cpf_"      + referencia + "' value='" + $('#nr_cpf').val() + "'/>"; 
    input += "<input type='hidden' id='cell_nr_usuarios_" + referencia + "' value='0'/>"; 
    
    if ( $('#sn_ativo').is(":checked") ) {
        input += "<input type='hidden' id='cell_ativo_" + referencia + "' value='1'/>"; 
        ativo  = "<i id='img_ativo_" + referencia + "' class='fa fa-check-square-o'>&nbsp;" + input + "</i>";
    } else {
        input += "<input type='hidden' id='cell_ativo_" + referencia + "' value='0'/>"; 
        ativo  = "<i id='img_ativo_" + referencia + "' class='fa fa-circle-thin'>&nbsp;" + input + "</i>";
    }
    
    var newRow = $("<tr id='linha_" + referencia + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#nm_tecnico').val().trim() + "</td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_tecnicos").append(newRow);

    return false;
  };
})(jQuery);
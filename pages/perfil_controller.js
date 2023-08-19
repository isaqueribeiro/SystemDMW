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


function PrepararPesquisaPerfil() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarPerfil() {
    var params = {
        'ac'    : $('#ac').val(),
        'token' : $('#token').val(),
        'tipo_pesquisa' : '',
        'pesquisa'      : '', 
        'cd_sistema'    : getSistema()
    };
    
    document.body.style.cursor = "wait";
    
    var aguarde = "<div class='col-md-offset-0'>" +
                  "  <div class='box box-danger'>" +
                  "    <div class='box-header'>" +
                  "      <h3 class='box-title'>Carregando dados...</h3>" +
                  "    </div>" +
                  "    <div class='box-body'>" +
                  "      Favor aguarde o resultado da pesquisa de perfis de acesso!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/perfil_dao.php',
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
            $('#tb_perfis').DataTable({
                "paging"      : true,
                "pageLength"  : 10, // Apenas 10 registros por paginação
                "lengthChange": false,
                "searching" : true,
                "ordering"  : true,
                "info"      : true,
                "autoWidth" : true,
                "processing": true,
                "columns": [
                    { "width": "15px" }, // Codigo
                    null,                // Descrição
                    { "width": "5px" },  // Usuários
                    { "width": "5px" },  // Acesso
                    { "width": "5px" },  // Ativo
                    { "width": "5px" }   // Excluir
                ],
                "order": [[0, 'asc']], // "order": [] <-- Ordenação indefinida
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

function NovoPerfil() {
    $('#cd_perfil').val("");
    $('#ds_perfil').val("");
    $('#qt_usuarios').val("0");
    $('#tx_permissao').val("");
    $("#sn_ativo").prop("checked", true);
    
    document.getElementById('painel_cadastro').style.display = 'block';
    $('#ds_perfil').focus();
}

function EditarPerfil(perfil) {
    var cd_perfil = perfil.replace("perfil_", "");
    var i_linha   = document.getElementById("linha_" + cd_perfil); // Capturar a linha da tabela correspondente ao ID
    var colunas   = i_linha.getElementsByTagName('td');

    $('#cd_perfil').val(zeroEsquerda(cd_perfil, 3));
    $('#ds_perfil').val(colunas[1].firstChild.nodeValue);
    $('#qt_usuarios').val(colunas[2].firstChild.nodeValue);
    $('#tx_permissao').val($("#cell_permissao_" + cd_perfil).val());
    $("#sn_ativo").prop("checked", ($("#cell_ativo_" + cd_perfil).val() === "1"));
    
    document.getElementById('painel_cadastro').style.display = 'block';
    $('#ds_perfil').focus();
} 

function SalvarPerfil() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#ds_perfil').val().trim() === "" ) str_mensagem += str_marcador + "Descrição<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var inserir = ($('#cd_perfil').val() === "");
        var params  = {
            'ac'      : 'salvar_perfil',
            'token'   : $('#token').val(),
            'cd_perfil'   : $('#cd_perfil').val(),
            'ds_perfil'   : $('#ds_perfil').val(),
            'qt_usuarios' : $('#qt_usuarios').val(),
            'sn_ativo'    : '0'
        };

        if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();
        
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/perfil_dao.php',
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
                    var file_json = "logs/perfil_" + getTokenId() + ".json"; 

                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                    //var painel = document.getElementById("painel_cadastro_retorno"); 
                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                    $.getJSON(file_json, function(data){
                        this.qtd = data.formulario.length;
                        if ( this.qtd > 0 ) {
                            $('#cd_perfil').val ( zeroEsquerda(data.formulario[this.qtd - 1].cd_perfil, 3) );
                        }

                        if ( inserir === true ) {
                            AddTableRowPerfil();
                        } else {
                            // Recuperar linha da tabela para alterar valor de linha
                            var i_linha = document.getElementById("linha_" + strToInt($('#cd_perfil').val()));
                            var colunas = i_linha.getElementsByTagName('td');

                            // Devolver valor da descrição para a linha/coluna
                            colunas[1].firstChild.nodeValue = $('#ds_perfil').val(); 
                            colunas[2].firstChild.nodeValue = $('#qt_usuarios').val(); 

                            // Armazenar valor retornado no campo oculto
                            document.getElementById('cell_ativo_' + strToInt($('#cd_perfil').val())).value = params.sn_ativo;

                            // Atualizar imagem da célula de acordo com o status do registro
                            $('#img_ativo_' + strToInt($('#cd_perfil').val())).removeClass("fa-check-square-o");
                            $('#img_ativo_' + strToInt($('#cd_perfil').val())).removeClass("fa-circle-thin");
                            if (params.sn_ativo === '1') {
                                $('#img_ativo_' + strToInt($('#cd_perfil').val())).addClass("fa-check-square-o");
                            } else {
                                $('#img_ativo_' + strToInt($('#cd_perfil').val())).addClass("fa-circle-thin");
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

function ExcluirPerfil(perfil, linha) {
    var cd_perfil = perfil.replace("excluir_perfil_", "");
    var usuarios  = $("#cell_usuarios_" + cd_perfil).val();
    
    if (strToInt(usuarios) > 0) {
        MensagemAlerta("Restrição", "O perfil não poderá ser excluído!<br><br><strong>Motivo : </strong><br>Este registros possue Usuários associadas.");
    } else {
        MensagemConfirmar(
                "Excluir Registro", 
                "Confirma a <strong>exclusão</strong> do Registro <strong>" + zeroEsquerda(cd_perfil, 3) + "</strong> do perfil selecionado?", 
                "300px");
        var link = document.getElementById("botao_confirma_main_sim");
        link.onclick = function() {
            document.getElementById("painel_confirma_main").style.display = 'none';
            var params  = {
                'ac'        : 'excluir_perfil',
                'token'     : $('#token').val(),
                'cd_perfil' : cd_perfil
            };

            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/perfil_dao.php',
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
}

function ConfigurarPerfil(perfil) {
    var cd_perfil = perfil.replace("configurar_perfil_", "");
    var permissao = $("#cell_permissao_" + cd_perfil).val();
    
    // Recuperar linha da tabela para alterar valor de linha
    var i_linha = document.getElementById("linha_" + strToInt(cd_perfil));
    var colunas = i_linha.getElementsByTagName('td');
    
    MontarArvorePermissao(cd_perfil, colunas[1].firstChild.nodeValue, permissao);
    document.getElementById('painel_configurar').style.display = 'block';
    
}

(function($) {
    AddTableRowPerfil = function() {

    var codigo = strToInt( $('#cd_perfil').val() );
    var input  = "<input type='hidden' id='cell_usuarios_"  + codigo + "'  value='0'/>";
        input += "<input type='hidden' id='cell_permissao_" + codigo + "' value=''/>";
    var ativo  = "";
    var acesso = "";
    
    var editar  = "<a id='perfil_" + codigo + "' href='javascript:preventDefault();' onclick='EditarPerfil( this.id )'>  " + $('#cd_perfil').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ExcluirPerfil( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    if ( $('#sn_ativo').is(":checked") ) {
        input += "<input type='hidden' id='cell_ativo_" + codigo + "' value='1'/>";
        ativo  = "<i id='img_ativo_" + codigo + "' class='fa fa-check-square-o'>&nbsp;" + input + "</i>";
    } else {
        input += "<input type='hidden' id='cell_ativo_" + codigo + "' value='0'/>";
        ativo  = "<i id='img_ativo_" + codigo + "' class='fa fa-circle-thin'>&nbsp;"    + input + "</i>";
    }
    
    if ($('#tx_permissao').val().trim() === "") {
        acesso = "<a id='configurar_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_" + codigo + "' class='fa fa-cog' title='Configurar acessos do Perfil'>&nbsp;</i>";
    } else {
        acesso = "<a id='configurar_perfil_" + codigo + "' href='javascript:preventDefault();' onclick='ConfigurarPerfil(this.id)'><i id='img_acesso_" + codigo + "' class='fa fa-cogs' title='Configurar acessos do Perfil'>&nbsp;</i>";
    }
    
    var newRow = $("<tr id='linha_" + codigo + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#ds_perfil').val().trim() + "</td>";
    cols += "<td align=right>"  + $('#qt_usuarios').val().trim() + "</td>";
    cols += "<td align=center>" + acesso  + "</td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";
    
    newRow.append(cols);
    
    $("#tb_perfis").append(newRow);

    return false;
  };
})(jQuery);


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function PrepararPesquisaUsuario() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarUsuario() {
    var params = {
        'ac'    : $('#ac').val(),
        'token' : $('#token').val(),
        'tipo_pesquisa' : $('#tipo_pesquisa').val(),
        'pesquisa'      : $('#pesquisa').val(),
        'cd_sistema'    : getSistema()
    };
    
    document.body.style.cursor = "wait";
    
    var aguarde = "<div class='col-md-offset-0'>" +
                  "  <div class='box box-danger'>" +
                  "    <div class='box-header'>" +
                  "      <h3 class='box-title'>Carregando dados...</h3>" +
                  "    </div>" +
                  "    <div class='box-body'>" +
                  "      Favor aguarde o resultado da pesquisa de usuários do sistema!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/usuario_dao.php',
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
            $('#tb_usuarios').DataTable({
                "paging": true,
                "pageLength": 10, // Apenas 10 registros por paginação
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "processing": true,
                "columns": [
                    null,                // Login
                    null,                // Nome do Usuário
                    null,                // E-mail
                    null,                // Perfil
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

function NovoUsuario() {
    var now = new Date();
    
    $('#id_usuario').val(getGuidEmpty());
    $('#lg_usuario').val("");
    $('#nm_usuario').val("");
    $('#pw_usuario').val("");
    $('#cd_perfil').val("0");
    $('#ds_email').val("");
    $("#sn_alterar_senha").prop("checked", true);
    $("#sn_ativo").prop("checked", true);
    $('#cd_tecnico').val(getGuidEmpty());
    $('#ds_hash').val("");
    
    document.getElementById('lg_usuario').disabled  = false;
    
    document.getElementById('painel_cadastro').style.display = 'block';
    $('#lg_usuario').focus();
    $(".select2").select2();
}

function EditarUsuario(usuario) {
    var referencia = usuario.replace("usuario_", "");
    if ( $("#cell_cd_perfil_"  + referencia).val() < getPfUsuario() ) {
        MensagemAlerta("Restrição", "Usuário sem permissão para editar regitros com <strong>perfis de acesso superior</strong>!");
    } else {
        var id_usuario = $("#cell_id_usuario_" + referencia).val();
        var lg_usuario = $("#cell_lg_usuario_" + referencia).val();
        var pw_usuario = $("#cell_pw_usuario_" + referencia).val();

        var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
        var colunas = i_linha.getElementsByTagName('td');

        $('#id_usuario').val(id_usuario);
        $('#lg_usuario').val(lg_usuario);
        $('#pw_usuario').val(pw_usuario);
        $('#nm_usuario').val(colunas[1].firstChild.nodeValue);
        $('#ds_email').val  (colunas[2].firstChild.nodeValue);

        $('#cd_perfil').val ($("#cell_cd_perfil_"  + referencia).val());
        $('#cd_tecnico').val($("#cell_cd_tecnico_" + referencia).val());

        $("#sn_alterar_senha").prop("checked", ($("#cell_alterar_senha_" + referencia).val() === "1"));
        $("#sn_ativo").prop("checked",         ($("#cell_ativo_"         + referencia).val() === "1"));

        document.getElementById('lg_usuario').disabled  = true;

        document.getElementById('painel_cadastro').style.display = 'block';
        $(".select2").select2();
    }
} 

function SalvarUsuario() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#lg_usuario').val().trim() === "" ) str_mensagem += str_marcador + "Login<br>";
    if ( $('#nm_usuario').val().trim() === "" ) str_mensagem += str_marcador + "Nome Completo<br>";
    if ( $('#pw_usuario').val().trim() === "" ) str_mensagem += str_marcador + "Senha<br>";
    if ( $('#cd_perfil').val().trim() === "0" ) str_mensagem += str_marcador + "Perfil<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var inserir = ($('#id_usuario').val() === "") || ($('#id_usuario').val() === getGuidEmpty());
        var params  = {
            'ac'      : 'salvar_usuario',
            'token'   : $('#token').val(),
            'id_usuario' : $('#id_usuario').val(),
            'nm_usuario' : $('#nm_usuario').val(),
            'ds_email'   : $('#ds_email').val().replace("...", ""),
            'cd_perfil'  : $('#cd_perfil').val(),
            'lg_usuario' : $('#lg_usuario').val(),
            'pw_usuario' : $('#pw_usuario').val(),
            'cd_tecnico' : $('#cd_tecnico').val(),
            'sn_alterar_senha': '0',
            'sn_ativo'        : '0'
        };

        if ( $('#sn_alterar_senha').is(":checked") ) params.sn_alterar_senha = $('#sn_alterar_senha').val();
        if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();
        
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/usuario_dao.php',
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
                    var file_json = "logs/usuario_" + getTokenId() + ".json"; 

                    // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                    //var painel = document.getElementById("painel_cadastro_retorno"); 
                    //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                    $.getJSON(file_json, function(data){
                        this.qtd = data.formulario.length;

                        if ( inserir === true ) {
                            $('#id_usuario').val ( data.formulario[this.qtd - 1].id_usuario );
                            $('#pw_usuario').val ( data.formulario[this.qtd - 1].pw_usuario );
                            AddTableRowUsuario();
                        } else {
                            // Recuperar linha da tabela para alterar valor de linha
                            var referencia = $('#id_usuario').val().replace(/-/g,'').replace("{", "").replace("}", "");
                            var i_linha = document.getElementById("linha_" + referencia);
                            var colunas = i_linha.getElementsByTagName('td');

                            // Devolver valores para a linha/coluna
                            colunas[1].firstChild.nodeValue = params.nm_usuario; 
                            colunas[2].firstChild.nodeValue = params.ds_email; 
                            colunas[3].firstChild.nodeValue = document.getElementById("cd_perfil").options[document.getElementById("cd_perfil").selectedIndex].text;

                            // Armazenar valor retornado no campo oculto
                            document.getElementById('cell_id_usuario_'    + referencia).value = $('#id_usuario').val();
                            document.getElementById('cell_lg_usuario_'    + referencia).value = params.lg_usuario;
                            document.getElementById('cell_pw_usuario_'    + referencia).value = data.formulario[this.qtd - 1].pw_usuario;
                            document.getElementById('cell_cd_perfil_'     + referencia).value = params.cd_perfil;
                            document.getElementById('cell_cd_tecnico_'    + referencia).value = params.cd_tecnico;
                            document.getElementById('cell_alterar_senha_' + referencia).value = params.sn_alterar_senha;
                            document.getElementById('cell_ativo_'         + referencia).value = params.sn_ativo;

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

function ExcluirUsuario(referencia, linha) {
    referencia = referencia.replace("excluir_usuario_", "");
    var id_usuario = $('#cell_id_usuario_' + referencia).val();
    
    if ( $("#cell_cd_perfil_"  + referencia).val() < getPfUsuario() ) {
        MensagemAlerta("Restrição", "Usuário sem permissão para excluir regitros com <strong>perfis de acesso superior</strong>!");
    } else 
    if ( id_usuario === getIdUsuario() ) {
        MensagemAlerta("Restrição", "Usuário corrente não poderá ser excluído!");
    } else {
        MensagemConfirmar(
                "Excluir Registro", 
                "Confirma a <strong>exclusão</strong> do Registro <strong>" + id_usuario + "</strong> do usuário selecionado?", 
                "300px");
        var link = document.getElementById("botao_confirma_main_sim");
        link.onclick = function() {
            document.getElementById("painel_confirma_main").style.display = 'none';
            var params  = {
                'ac'        : 'excluir_usuario',
                'token'     : $('#token').val(),
                'id_usuario': id_usuario
            };

            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/usuario_dao.php',
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

(function($) {
    AddTableRowUsuario = function() {

    var referencia = $('#id_usuario').val().replace(/-/g,'').replace("{", "").replace("}", "");
    var input  = "";
    var ativo  = "";

    var editar  = "<a id='usuario_" + referencia + "' href='javascript:preventDefault();' onclick='EditarUsuario( this.id )'>  " + $('#lg_usuario').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_usuario_" + referencia + "' href='javascript:preventDefault();' onclick='ExcluirUsuario( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_id_usuario_" + referencia + "' value='" + $('#id_usuario').val() + "'/>"; 
    input += "<input type='hidden' id='cell_lg_usuario_" + referencia + "' value='" + $('#lg_usuario').val() + "'/>"; 
    input += "<input type='hidden' id='cell_pw_usuario_" + referencia + "' value='" + $('#pw_usuario').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_perfil_"  + referencia + "' value='" + $('#cd_perfil').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_tecnico_" + referencia + "' value='" + $('#cd_tecnico').val() + "'/>"; 
    
    if ( $('#sn_alterar_senha').is(":checked") ) {
        input += "<input type='hidden' id='cell_alterar_senha_" + referencia + "' value='1'/>"; 
    } else {
        input += "<input type='hidden' id='cell_alterar_senha_" + referencia + "' value='0'/>"; 
    }
    
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
    cols += "<td>" + $('#nm_usuario').val().trim() + "</td>";
    cols += "<td>" + $('#ds_email').val().trim() + "</td>";
    cols += "<td>" + $('#cd_perfil option:selected').text() + "</td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_usuarios").append(newRow);

    return false;
  };
})(jQuery);
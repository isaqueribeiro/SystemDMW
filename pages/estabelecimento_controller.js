/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function BuscarEnderecoCepEstab() {
    var nr_cep = $("#nr_cep").val().replace(".", "").replace("-", "");
    if ( nr_cep.trim() === "" ) {
        MensagemAlerta("Alerta", "Favor informar o <strong>Número do Cep</strong>.");
    } else {
        var params = {
            'ac'    : 'buscar_endereco_cep',
            'token' : $('#token').val(),
            'nr_cep': nr_cep
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
                loadingGif(true);
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                var retorno = data; 
                if ( retorno === "OK" ) {
                    var file_json = "logs/endereco_cep_" + getTokenId() + ".json"; 

                    $.getJSON(file_json, function(data){
                        var cd_estado = data.cep[0].cd_estado;
                        var cd_cidade = data.cep[0].cd_cidade;
                        var cd_bairro = data.cep[0].cd_bairro;
                        var cd_tipo   = data.cep[0].cd_tipo;
                        var ds_logradouro = data.cep[0].ds_logradouro;
                        
                        setEnderecoEstabelecimento(cd_estado, cd_cidade, cd_bairro, cd_tipo, ds_logradouro, function() {
                            $("#nr_endereco").focus();
                        });
                    });
                } else {
                    MensagemAlerta("Alerta", retorno);
                }
            },
            error: function (request, status, error) {
                loadingGif(false);
                MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    }
//    
//                    // verifica se o parâmetro callback é realmente uma função antes de executá-lo
//                    if(callback && typeof(callback) === "function") {
//                        callback();
//                    }
//                    return true;
//    
}

function setEnderecoEstabelecimento(cd_estado, cd_cidade, cd_bairro, tb_endereco, ds_endereco, callback) {
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
            loadingGif(true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            var retorno = data;
            if ( retorno === "OK" ) {
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
                                    loadingGif(true);
                                },
                                // Colocamos o retorno na tela
                                success : function(data){
                                    var retorno = data;
                                    if ( retorno === "OK" ) {
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
                                                    
                                                    $('#cd_estado').val(cd_estado);
                                                    $('#cd_cidade').val(cd_cidade);
                                                    $('#cd_bairro').val(cd_bairro);    
                                                    $('#tp_endereco').val(tb_endereco);
                                                    
                                                    $('#ds_endereco').val(ds_endereco);
                                                    $(".select2").select2();
                                                    loadingGif(false);
                                                    
                                                    // verifica se o parâmetro callback é realmente uma função antes de executá-lo
                                                    if(callback && typeof(callback) === "function") {
                                                        callback();
                                                    }
                                                }
                                            }
                                        });

                                    } else {
                                        loadingGif(false);
                                        MensagemErro("Erro", retorno);
                                    }
                                },
                                error: function (request, status, error) {
                                    loadingGif(false);
                                    MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
                                }
                            });  
                            // Finalizamos o Ajax

                        }
                    }
                });

            } else {
                loadingGif(false);
                MensagemErro("Erro", retorno);
            }
        },
        error: function (request, status, error) {
            loadingGif(false);
            MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
}

function formatarTabelaEstabelecimento() {
    // Configurando Tabela
    $('#tb_estabelecimentos').DataTable({
        "paging": true,
        "pageLength": 10, // Apenas 10 registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "140px" }, // CNPJ
            null,                 // Razão Social
            null,                 // Fantasia
            null,                 // IE
            null,                 // IM
            { "width": "10px" },  // Licenças
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

function PrepararPesquisaEstabelecimento() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html(""); 
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarEstabelecimento() {
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
                  "      Favor aguarde o resultado da pesquisa de Estabelecimentos cadastrados!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/estabelecimento_dao.php',
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
            formatarTabelaEstabelecimento();
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#resultado_pesquisa').html("Erro (" + status + "):<br><br>" + request.responseText + "<br>Error : " + error);
        }
    });  
    // Finalizamos o Ajax
}

function NovoEstabelecimento() {
    var rotina = menus[menuCadastroID][2][rotinaCadastroEstabelecimentoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        $('#guia_tab_atividade').trigger('click');
        $('#guia_tab_endereco').trigger('click');
        
        $('#id_estabelecimento').val(getGuidEmpty());
        $('#nr_cnpj').val("");
        $('#nr_insc_est').val("");
        $('#nr_insc_mun').val("");
        $('#nm_razao').val("");
        $('#nm_fantasia').val("");
        $('#cd_cnae_principal').val("0");
        $('#cd_cnae_secundaria').val("0");
        $("#sn_orgao_publico").prop("checked", false);
        $("#sn_ativo").prop("checked", true);

        $('#cd_estado').val( $('#cd_estado_padrao').val() );
        $('#cd_cidade').val( $('#cd_cidade_padrao').val() );
        $('#cd_bairro').val("0");
        $('#tp_endereco').val("0");
        $('#ds_endereco').val("");
        $('#nr_endereco').val("");
        $('#ds_complemento').val("");
        $('#nr_cep').val("");
        
        $('#nr_comercial').val("");
        $('#nr_telefone').val("");
        $('#nr_celular').val("");
        $('#nm_contato').val("");
        $('#ds_email').val("");
        
        $('#cd_cidade').trigger('onchange');
        document.getElementById('nr_cnpj').disabled = false;

        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nr_cnpj').focus();
        $(".select2").select2();
    });
}

function EditarEstabelecimento(estabelecimento) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroEstabelecimentoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        $('#guia_tab_atividade').trigger('click');
        $('#guia_tab_endereco').trigger('click');
        
        var referencia = estabelecimento.replace("estabelecimento_", "");
        
        var id_estabelecimento = $("#cell_id_estabelecimento_" + referencia).val();
        var cd_estado = $("#cell_cd_estado_" + referencia).val();
        var cd_cidade = $("#cell_cd_cidade_" + referencia).val();
        var cd_bairro = $("#cell_cd_bairro_" + referencia).val();
        var tp_endereco = $("#cell_tp_endereco_" + referencia).val();
        var ds_endereco = $("#cell_ds_endereco_" + referencia).val();

        setEnderecoEstabelecimento(cd_estado, cd_cidade, cd_bairro, tp_endereco, ds_endereco, function(){
            var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
            var colunas = i_linha.getElementsByTagName('td');

            $('#id_estabelecimento').val(id_estabelecimento);
            $('#tp_pessoa').val( $("#cell_tp_pessoa_"  + referencia).val() );
            $('#nr_cnpj').val( $("#cell_nr_cnpj_"  + referencia).val() );
            $('#cd_cnae_principal').val( $("#cell_cd_cnae_principal_" + referencia).val() );
            $('#cd_cnae_secundaria').val( $("#cell_cd_cnae_secundaria_" + referencia).val() );
            $('#nr_endereco').val( $("#cell_nr_endereco_" + referencia).val() );
            $('#ds_complemento').val( $("#cell_ds_complemento_" + referencia).val() );
            $('#cd_uf').val(     $("#cell_cd_uf_"     + referencia).val() );
            $('#nr_cep').val(    $("#cell_nr_cep_"    + referencia).val() );
            $('#nr_comercial').val( $("#cell_nr_comercial_" + referencia).val() );
            $('#nr_telefone').val( $("#cell_nr_telefone_"   + referencia).val() );
            $('#nr_celular').val( $("#cell_nr_celular_" 	+ referencia).val() );
            $('#nm_contato').val( $("#cell_nm_contato_" 	+ referencia).val() );
            $('#ds_email').val( $("#cell_ds_email_" 	+ referencia).val() );

            $('#nm_razao').val(colunas[1].firstChild.nodeValue);
            $('#nm_fantasia').val(colunas[2].firstChild.nodeValue);
            $('#nr_insc_est').val(colunas[3].firstChild.nodeValue);
            $('#nr_insc_mun').val(colunas[4].firstChild.nodeValue);

            $('#nr_insc_est').val( $('#nr_insc_est').val().replace("...", "") );
            $('#nr_insc_mun').val( $('#nr_insc_mun').val().replace("...", "").replace(/[^\d]+/g, "") );

            $("#sn_orgao_publico").prop("checked", ($("#cell_sn_orgao_publico_" + referencia).val() === "1"));
            $("#sn_ativo").prop("checked", ($("#cell_sn_ativo_" + referencia).val() === "1"));

            document.getElementById('nr_cnpj').disabled  = true;

            document.getElementById('painel_cadastro').style.display = 'block';
            $('#nm_razao').focus();
            $(".select2").select2();
        });
    });
} 

function SalvarEstabelecimento() {
//        var cnpj = $('#nr_cnpj').val().replace(/[^\d]+/g, ""); // Deixar apenas os números
//        if ( !validarCNPJ(cnpj) ) {
//            MensagemErro("Validação", "Número de CNPJ inválido!");
//            exit;
//        }    
//
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#nr_cnpj').val().trim()     === "" ) str_mensagem += str_marcador + "CPF/CNPJ<br>";
    if ( $('#nr_insc_est').val().trim() === "" ) str_mensagem += str_marcador + "RG/Inscrição Estadual<br>";
    if ( $('#nr_insc_mun').val().trim() === "" ) str_mensagem += str_marcador + "Inscrição Municipal<br>";
    if ( $('#nm_razao').val().trim()    === "" ) str_mensagem += str_marcador + "Razão Social<br>";
    if ( $('#nm_fantasia').val().trim() === "" ) str_mensagem += str_marcador + "Nome Fantasia<br>";
    if ( ($('#cd_estado').val().trim() === "0") || 
         ($('#cd_cidade').val().trim() === "0") ||
         ($('#cd_bairro').val().trim() === "0") ||
         ($('#tp_endereco').val().trim() === "0") ||
         ($('#ds_endereco').val().trim() === "")  ||
         ($('#nr_endereco').val().trim() === "")  ||
         ($('#nr_cep').val().trim() === "") ) str_mensagem += str_marcador + "Endereço Completo<br>";
    if ( $('#cd_cnae_principal').val().trim() === "0" ) str_mensagem += str_marcador + "Atividade Principal<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var cnpj = $('#nr_cnpj').val().replace(/[^\d]+/g, ""); // Deixar apenas os números
        
        var validado = false;
        if (cnpj.length < 14) {
            validado = validarCPF(cnpj);
        } else {
            validado = validarCNPJ(cnpj);
        }
        
        if ( !validado ) {
            MensagemErro("Validação", "Número de CPF/CNPJ inválido!");
        } else {
            var inserir = ($('#id_estabelecimento').val() === "") || ($('#id_estabelecimento').val() === getGuidEmpty());
            var params  = {
                'ac'      : 'salvar_estabelecimento',
                'token'   : $('#token').val(),
                'id_estabelecimento' : $('#id_estabelecimento').val(),
                'tp_pessoa'  : $('#tp_pessoa').val(), 
                'nm_razao'   : $('#nm_razao').val().trim(),
                'nm_fantasia': $('#nm_fantasia').val().trim(),
                'nr_cnpj'    : cnpj,
                'nr_insc_est': $('#nr_insc_est').val().trim().toUpperCase().replace(".", ""),
                'nr_insc_mun': $('#nr_insc_mun').val().trim().replace(/[^\d]+/g, ""), // Deixar apenas os números
                'cd_cnae_principal' : $('#cd_cnae_principal').val(),
                'cd_cnae_secundaria': $('#cd_cnae_secundaria').val(),
                'tp_endereco'	   : $('#tp_endereco').val(),
                'ds_endereco'      : $('#ds_endereco').val().trim(),
                'nr_endereco'      : $('#nr_endereco').val().trim(),
                'ds_complemento'   : $('#ds_complemento').val().trim(),
                'cd_bairro'        : $('#cd_bairro').val(),
                'nr_cep'           : $('#nr_cep').val().trim().replace(/[^\d]+/g, ""), // Deixar apenas os números
                'cd_cidade'        : $('#cd_cidade').val(),
                'cd_uf'            : $('#cd_uf').val().trim(),
                'cd_estado'        : $('#cd_estado').val(),
                'nr_comercial'     : $('#nr_comercial').val().trim(),
                'nr_telefone'      : $('#nr_telefone').val().trim(),
                'nr_celular'       : $('#nr_celular').val().trim(),
                'nm_contato'       : $('#nm_contato').val().trim(),
                'ds_email'         : $('#ds_email').val().trim(),
                'sn_orgao_publico' : '0',
                'sn_ativo'         : '0'
            };

            if ( $('#sn_orgao_publico').is(":checked") ) params.sn_orgao_publico = $('#sn_orgao_publico').val();
            if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();

            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/estabelecimento_dao.php',
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
                        var file_json = "logs/estabelecimento_" + getTokenId() + ".json"; 

                        // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                        //var painel = document.getElementById("painel_cadastro_retorno"); 
                        //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                        $.getJSON(file_json, function(data){
                            this.qtd = data.formulario.length;

                            $('#cd_uf').val ( data.formulario[this.qtd - 1].cd_uf );
                            
                            if ( inserir === true ) {
                                $('#id_estabelecimento').val ( data.formulario[this.qtd - 1].id_estabelecimento );
                                AddTableRowEstabelecimento();
                            } else {
                                // Recuperar linha da tabela para alterar valor de linha
                                var referencia = $('#id_estabelecimento').val().replace(/-/g,'').replace("{", "").replace("}", "");
                                var i_linha = document.getElementById("linha_" + referencia);
                                var colunas = i_linha.getElementsByTagName('td');

                                if (params.nr_insc_est.trim() === "") params.nr_insc_est = "...";
                                if (params.nr_insc_mun.trim() === "") params.nr_insc_mun = "...";
                                
                                // Devolver valores para a linha/coluna
                                colunas[1].firstChild.nodeValue = params.nm_razao; 
                                colunas[2].firstChild.nodeValue = params.nm_fantasia; 
                                colunas[3].firstChild.nodeValue = params.nr_insc_est; 
                                colunas[4].firstChild.nodeValue = params.nr_insc_mun; 

                                // Armazenar valor retornado no campo oculto
                                document.getElementById('cell_id_estabelecimento_' + referencia).value = $('#id_estabelecimento').val();
                                document.getElementById('cell_tp_pessoa_' + referencia).value          = $('#tp_pessoa').val();
                                document.getElementById('cell_nr_cnpj_' + referencia).value            = $('#nr_cnpj').val();
                                document.getElementById('cell_cd_cnae_principal_'  + referencia).value  = $('#cd_cnae_principal').val();
                                document.getElementById('cell_cd_cnae_secundaria_' + referencia).value  = $('#cd_cnae_secundaria').val();
                                document.getElementById('cell_tp_endereco_' + referencia).value        = $('#tp_endereco').val();
                                document.getElementById('cell_ds_endereco_' + referencia).value        = params.ds_endereco;
                                document.getElementById('cell_nr_endereco_' + referencia).value        = params.nr_endereco;
                                document.getElementById('cell_ds_complemento_' + referencia).value     = params.ds_complemento;
                                document.getElementById('cell_cd_bairro_' + referencia).value          = $('#cd_bairro').val();
                                document.getElementById('cell_nr_cep_' + referencia).value             = $('#nr_cep').val();
                                document.getElementById('cell_cd_cidade_' + referencia).value          = $('#cd_cidade').val();
                                document.getElementById('cell_cd_uf_' + referencia).value              = $('#cd_uf').val();
                                document.getElementById('cell_cd_estado_' + referencia).value          = $('#cd_estado').val();
                                document.getElementById('cell_nr_comercial_' + referencia).value       = params.nr_comercial;
                                document.getElementById('cell_nr_telefone_' + referencia).value        = params.nr_telefone;
                                document.getElementById('cell_nr_celular_' + referencia).value         = params.nr_celular;
                                document.getElementById('cell_ds_email_' + referencia).value           = params.ds_email;
                                document.getElementById('cell_nm_contato_' + referencia).value         = params.nm_contato;
                                document.getElementById('cell_sn_orgao_publico_' + referencia).value   = params.sn_orgao_publico;
                                document.getElementById('cell_sn_ativo_'         + referencia).value   = params.sn_ativo;

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

function ExcluirEstabelecimento(referencia, linha) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroEstabelecimentoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia = referencia.replace("excluir_estabelecimento_", "");
        var id_estabelecimento  = $('#cell_id_estabelecimento_' + referencia).val();
        var nr_licencas = $('#cell_nr_licencas_' + referencia).val();

        if ( strToInt(nr_licencas) > 0 ) {
            MensagemAlerta("Restrição", "Registro não poderá ser excluído.<br><br><strong>Motivo:</strong><br>Registro associado ao Cadastro de Licenças.");
        } else {
            MensagemConfirmar(
                    "Excluir Registro", 
                    "Confirma a <strong>exclusão</strong> do Registro <strong>" + id_estabelecimento + "</strong> referente ao Estabelecimento selecionado?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'    : 'excluir_estabelecimento',
                    'token' : $('#token').val(),
                    'id_estabelecimento': id_estabelecimento
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/estabelecimento_dao.php',
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
    AddTableRowEstabelecimento = function() {

    var referencia = $('#id_estabelecimento').val().replace(/-/g,'').replace("{", "").replace("}", "");
    var tabela = "";
    var input  = "";
    var ativo  = "";

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("resultado_pesquisa"); 
    
    if ( pagina.innerHTML.indexOf("tb_estabelecimentos") === -1 ) {
        tabela += "<div class='box box-info' id='box_resultado_pesquisa'>";
        tabela += "    <div class='box-header with-border'>";
        tabela += "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
        tabela += "    </div>";
        tabela += "    ";
        tabela += "    <div class='box-body'>";

        tabela += "<a id='ancora_estabelecimentos'></a><table id='tb_estabelecimentos' class='table table-bordered table-hover'  width='100%'>";

        tabela += "<thead>";
        tabela += "    <tr>";
        tabela += "        <th>CPF/CNPJ</th>";
        tabela += "        <th>Razão Social</th>";
        tabela += "        <th>Fantasia</th>";
        tabela += "        <th>RG/IE</th>";
        tabela += "        <th>IM</th>";
        tabela += "        <th data-orderable='false'><center>Licenças</center></th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "        <th data-orderable='false'><center>Ativo</center></th>";    // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "        <th data-orderable='false'></th>";                          // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "    </tr>";
        tabela += "</thead>";
        tabela += "<tbody>";
        tabela += "</tbody>";
        tabela += "</table>";
        
        $('#resultado_pesquisa').html(tabela);
    }
        
    var editar  = "<a id='estabelecimento_"         + referencia + "' href='javascript:preventDefault();' onclick='EditarEstabelecimento( this.id )'>  " + $('#nr_cnpj').val() + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_estabelecimento_" + referencia + "' href='javascript:preventDefault();' onclick='ExcluirEstabelecimento( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    input  = "<input type='hidden' id='cell_id_estabelecimento_" + referencia + "' value='" + $('#id_estabelecimento').val() + "'/>"; 
    input += "<input type='hidden' id='cell_tp_pessoa_"          + referencia + "' value='" + $('#tp_pessoa').val() + "'/>"; 
    input += "<input type='hidden' id='cell_nr_cnpj_"            + referencia + "' value='" + $('#nr_cnpj').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_cnae_principal_"  + referencia + "' value='" + $('#cd_cnae_principal').val() + "'/>"; 
    input += "<input type='hidden' id='cell_cd_cnae_secundaria_" + referencia + "' value='" + $('#cd_cnae_secundaria').val() + "'/>"; 
    input += "<input type='hidden' id='cell_tp_endereco_"        + referencia + "' value='" + $('#tp_endereco').val() + "'/>"; 
    input += "<input type='hidden' id='cell_ds_endereco_"        + referencia + "' value='" + $('#ds_endereco').val() + "'/>"; 
    input += "<input type='hidden' id='cell_nr_endereco_"        + referencia + "' value='" + $('#nr_endereco').val() + "'/>";
    input += "<input type='hidden' id='cell_ds_complemento_"     + referencia + "' value='" + $('#ds_complemento').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_bairro_"          + referencia + "' value='" + $('#cd_bairro').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_cep_"             + referencia + "' value='" + $('#nr_cep').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_cidade_"          + referencia + "' value='" + $('#cd_cidade').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_uf_"              + referencia + "' value='" + $('#cd_uf').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_estado_"          + referencia + "' value='" + $('#cd_estado').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_comercial_"       + referencia + "' value='" + $('#nr_comercial').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_telefone_"        + referencia + "' value='" + $('#nr_telefone').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_celular_"         + referencia + "' value='" + $('#nr_celular').val() + "'/>";
    input += "<input type='hidden' id='cell_nm_contato_"         + referencia + "' value='" + $('#nm_contato').val() + "'/>";
    input += "<input type='hidden' id='cell_ds_email_"           + referencia + "' value='" + $('#ds_email').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_licencas_"        + referencia + "' value='0'/>"; 
    
    if ( $('#sn_orgao_publico').is(":checked") ) {
        input += "<input type='hidden' id='cell_sn_orgao_publico_" + referencia + "' value='1'/>"; 
    } else {
        input += "<input type='hidden' id='cell_sn_orgao_publico_" + referencia + "' value='0'/>"; 
    }
    
    if ( $('#sn_ativo').is(":checked") ) {
        input += "<input type='hidden' id='cell_sn_ativo_" + referencia + "' value='1'/>"; 
        ativo  = "<i id='img_ativo_" + referencia + "' class='fa fa-check-square-o'>&nbsp;" + input + "</i>";
    } else {
        input += "<input type='hidden' id='cell_sn_ativo_" + referencia + "' value='0'/>"; 
        ativo  = "<i id='img_ativo_" + referencia + "' class='fa fa-circle-thin'>&nbsp;" + input + "</i>";
    }
    
    var newRow = $("<tr id='linha_" + referencia + "'>");
    var cols = "";

    if ($('#nr_insc_est').val().trim() === "") $('#nr_insc_est').val("...");
    if ($('#nr_insc_mun').val().trim() === "") $('#nr_insc_mun').val("...");
    
    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#nm_razao').val().trim()    + "</td>";
    cols += "<td>" + $('#nm_fantasia').val().trim() + "</td>";
    cols += "<td>" + $('#nr_insc_est').val().trim() + "</td>";
    cols += "<td>" + $('#nr_insc_mun').val().trim() + "</td>";
    cols += "<td align=right>0</td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";

    newRow.append(cols);
    
    $("#tb_estabelecimentos").append(newRow);
    if ( tabela !== "" ) formatarTabelaEstabelecimento();
    
    return false;
  };
})(jQuery);
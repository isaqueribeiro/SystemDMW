/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var _SITUACAO_LICENCA_PENDENTE   = 0;
var _SITUACAO_LICENCA_AGUARDANDO = 1;
var _SITUACAO_LICENCA_APROVADA   = 2;
var _SITUACAO_LICENCA_VENCIDA    = 3;
var _SITUACAO_LICENCA_SUSPENSA   = 4;
var _SITUACAO_LICENCA_CANCELADA  = 5;

var _SITUACAO_LICENCA = ["Pendente", "Aguardando", "Aprovada", "Vencida", "Suspensa", "Cancelada"];

function setSituacaoLicencaFuncionamento(referencia, situacao) {
    var tipoAcesso = tipoAcesso03;
    if (situacao === _SITUACAO_LICENCA_SUSPENSA) {
        tipoAcesso = tipoAcesso04;
    } else
    if (situacao === _SITUACAO_LICENCA_APROVADA) {
        tipoAcesso = tipoAcesso05;
    }
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso, function(){
        referencia = referencia.replace("set_licenca_", "");
        
        var id_estabelecimento = $('#cell_id_estabelecimento_'  + referencia).val();
        var id_licenca   = $('#cell_id_licenca_'  + referencia).val();
        var nr_processo  = $('#cell_nr_processo_' + referencia).val();
        var tp_situacao_ = $('#cell_tp_situacao_' + referencia).val();
        var tp_situacao  = situacao;
        var nr_exercicio = $('#cell_nr_exercicio_' + referencia).val();
        var nr_licenca   = $('#cell_nr_licenca_'   + referencia).val();
        var id_tecnico   = $('#cell_cd_responsavel_' + referencia).val().trim();
        
        // Licença cancelada não pode ser alterada
        if (strToInt(tp_situacao_) === _SITUACAO_LICENCA_CANCELADA) {
            MensagemAlerta("Restrição", "A situação licença <strong>" + id_licenca + "</strong> referente ao processo No. <strong>" + nr_processo + "</strong> não poderá ser alterada por está <strong>" + _SITUACAO_LICENCA[strToInt(tp_situacao_)] + "</strong>!");
        } else
        // Licença vencida não pode ser alterada
        if (strToInt(tp_situacao_) === _SITUACAO_LICENCA_VENCIDA) {
            MensagemAlerta("Restrição", "A situação licença <strong>" + id_licenca + "</strong> referente ao processo No. <strong>" + nr_processo + "</strong> não poderá ser alterada por está <strong>" + _SITUACAO_LICENCA[strToInt(tp_situacao_)] + "</strong>!");
        } else
        // Licença cancelada não pode ser alterada
        if ((strToInt(tp_situacao_) > tp_situacao) && (tp_situacao !== _SITUACAO_LICENCA_APROVADA)) {
            MensagemAlerta("Restrição", "A situação licença <strong>" + id_licenca + "</strong> referente ao processo No. <strong>" + nr_processo + "</strong> não poderá ser alterada por está <strong>" + _SITUACAO_LICENCA[strToInt(tp_situacao_)] + "</strong>!");
        } else
        // Licença já na situação informada
        if (strToInt(tp_situacao_) === tp_situacao) {
            MensagemAlerta("Restrição", "A situação licença <strong>" + id_licenca + "</strong> referente ao processo No. <strong>" + nr_processo + "</strong> já está <strong>" + _SITUACAO_LICENCA[strToInt(tp_situacao_)] + "</strong>!");
        } else
        // Verificar Técnico
        if ( ((tp_situacao === _SITUACAO_LICENCA_APROVADA)) && ((id_tecnico === "") || (id_tecnico === getGuidEmpty())) ) {
            MensagemAlerta("Restrição", "Favor registrar na Licença de Funcionamento o <strong>técnico responsável pela visita</strong> ao Estabelecimento.");
        } else {
            MensagemConfirmar(
                    "Situação da Licença de Funcionamento", 
                    "<strong>" + nr_exercicio + "/" + zeroEsquerda(nr_licenca, 5) + "</strong> - Deseja alterar a situação do registro da Licença de Funcionamento selecionada para <strong>" +  _SITUACAO_LICENCA[situacao] + "</strong>?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'         : 'situacao_licenca_funcionamento',
                    'token'      : $('#token').val(),
                    'id_estabelecimento' : id_estabelecimento,
                    'id_licenca' : id_licenca,
                    'nr_processo': nr_processo,
                    'tp_situacao': tp_situacao
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/licenca_funcionamento_dao.php',
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
                        var retorno = data;
                        if ( retorno === "OK" ) {
                            loadingGif(false);
                            document.getElementById('cell_tp_situacao_' + referencia).value = params.tp_situacao;

                            var opcoes = "";

                            opcoes  = "<div class='btn-group'> ";
                            opcoes += "    <button type='button' class='btn btn-primary'><i class='fa fa-edit' title='Mais Opções'></i></button> ";
                            opcoes += "    <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'> ";
                            opcoes += "        <span class='caret'></span> ";
                            opcoes += "        <span class='sr-only'></span> ";
                            opcoes += "    </button> ";
                            opcoes += "    <ul class='dropdown-menu' role='menu'> ";
                            opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_AGUARDANDO + ")'><i class='fa fa-calendar'></i> Aguardar</a></li> ";
                            opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_APROVADA   + ")'><i class='fa fa-check-square-o'></i> Aprovar</a></li> ";
                            opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_SUSPENSA   + ")'><i class='fa fa-circle-o'></i> Suspender</a></li> ";
                            opcoes += "        <li class='divider'></li> ";
                            opcoes += "        <li><a href='javascript:preventDefault();' id='emt_licenca_" + referencia + "' onclick='EmitirLicencaFuncionamento(this.id)'><i class='fa fa-print'></i> Emitir Licença</a></li> ";
                            opcoes += "        <li><a href='javascript:preventDefault();' id='evt_licenca_" + referencia + "' onclick='EventoLicencaFuncionamento(this.id)'><i class='fa fa-bell-o'></i> Registrar Eventos</a></li> ";
                            opcoes += "    </ul> ";
                            opcoes += "</div> ";

                            $('#situacao_' + referencia).html(opcoes + "&nbsp;&nbsp;<strong>" + _SITUACAO_LICENCA[tp_situacao] + "</strong>");

                            // Atualizar imagem da célula de acordo com o status do registro
                            $('#img_ativo_' + referencia).removeClass("fa-check-square-o");
                            $('#img_ativo_' + referencia).removeClass("fa-circle-thin");
                            if (strToInt(params.tp_situacao) === _SITUACAO_LICENCA_APROVADA) {
                                $('#img_ativo_' + referencia).addClass("fa-check-square-o");
                            } else {
                                $('#img_ativo_' + referencia).addClass("fa-circle-thin");
                            }
                        } else {
                            loadingGif(false);
                            MensagemAlerta("Alerta", retorno);
                        }
                    },
                    error: function (request, status, error) {
                        loadingGif(false);
                        MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
                    }
                });  
                // Finalizamos o Ajax
            };
        }
    });
}

function EmitirLicencaFuncionamento(referencia) {
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia = referencia.replace("emt_licenca_", "");
        
        var id_estabelecimento = $('#cell_id_estabelecimento_'  + referencia).val();
        var id_licenca   = $('#cell_id_licenca_'   + referencia).val();
        var nr_processo  = $('#cell_nr_processo_'  + referencia).val();
        var tp_situacao  = $('#cell_tp_situacao_'  + referencia).val();
        var nr_exercicio = $('#cell_nr_exercicio_' + referencia).val();
        var nr_licenca   = $('#cell_nr_licenca_'   + referencia).val();
        var id_tecnico   = $('#cell_cd_responsavel_' + referencia).val().trim();
        
        if (strToInt(tp_situacao) !== _SITUACAO_LICENCA_APROVADA) {
            MensagemAlerta("Restrição", "Apenas registros de Licenças de Funcionamento aprovados podem ser emitidos!");
        } else 
        if ((id_tecnico === "") || (id_tecnico === getGuidEmpty())) {
            MensagemAlerta("Restrição", "Favor registrar na Licença de Funcionamento o <strong>técnico responsável pela visita</strong> ao Estabelecimento.");
        } else {
            var param   = 
                    "?token=" + $('#token').val() + 
                    "&id_licenca="  + id_licenca;

            window.open("/dmWeb/pages/licenca_funcionamento_print.php" + param, '_blank');
        }
    });
}

function EventoLicencaFuncionamento(referencia) {
    // tipoAcesso03 - Modificar
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia = referencia.replace("evt_licenca_", "");
        
        var id_estabelecimento = $('#cell_id_estabelecimento_'  + referencia).val();
        var id_licenca   = $('#cell_id_licenca_'   + referencia).val();
        var nr_processo  = $('#cell_nr_processo_'  + referencia).val();
        var tp_situacao  = $('#cell_tp_situacao_'  + referencia).val();
        var nr_exercicio = $('#cell_nr_exercicio_' + referencia).val();
        var nr_licenca   = $('#cell_nr_licenca_'   + referencia).val();
        var nm_razao     = $('#cell_nm_razao_'     + referencia).val();

        if (strToInt(tp_situacao) === _SITUACAO_LICENCA_CANCELADA) {
            MensagemAlerta("Restrição", "Registros de Licenças de Funcionamento canceladas não podem ser receber registros de eventos!");
        } else {
            $('#id_evento_estabelecimento').val(id_estabelecimento);
            $('#id_evento_licenca').val(id_licenca);
            $('#id_evento_processo').val(nr_processo);
            $('#nr_controle_licenca').val(zeroEsquerda(nr_licenca, 5) + "/" + nr_exercicio);
            $('#nm_estabelecimento_licenca').val(nm_razao);
            $('#id_tecnico').val(getGuidEmpty());
            $('#ds_evento').val("");

            document.getElementById('painel_cadastro_evento').style.display = 'block';
            $('#dt_evento').focus();
            $(".select2").select2();
        }
    });
}

function EditarEventoLicencaFuncionamento(referencia) {
    // tipoAcesso05 - Controle Total para Emissão de Licença
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso05, function(){
        referencia = referencia.replace("evento_licenca_", "");
    
    });
}

function getValidadeCategoriaLicenca(ano, emissao, categoria) {
    var params  = {
        'ac'    : 'validade_categoria_licenca',
        'token' : $('#token').val(),
        'nr_exercicio' : ano,
        'dt_emissao'   : emissao,
        'cd_categoria' : categoria
    };
            
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/categoria_licenca_dao.php',
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
                var file_json = "logs/categoria_licenca_" + getTokenId() + ".json"; 
                $.getJSON(file_json, function(data){
                    this.qtd = data.categoria.length;
                    $('#dt_validade').val(data.categoria[this.qtd - 1].dt_vencimento);
                    $('#dt_validade').attr('disabled', (data.categoria[this.qtd - 1].sn_provisoria === "0"));
                    $('#sn_provisoria').val(data.categoria[this.qtd - 1].sn_provisoria);
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

function configurarTabelaLicencaFuncionamento() {
    // Configurando Tabela
    $('#tb_licencas_funcionamento').DataTable({
        "paging": true,
        "pageLength": 7, // Apenas 7 registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "85px" },  // Controle
            { "width": "100px" }, // Processo
            null,                 // Estabelecimento
            { "width": "140px" }, // CNPJ
            { "width": "80px" } , // Validade
            null,                 // Situação
            //{ "width": "50px" } , // Opções
            { "width": "5px" }  , // Ativo
            { "width": "5px" }    // Excluir
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
                "infoEmpty": "Não existem dados com os parâmetros de pesquisa informado!", // "Sem dados para exibição",
                "infoFiltered":   "(Filtrada a partir de _MAX_ registros no total)",
                "zeroRecords": "Sem registro(s) para exibição",
                "lengthMenu": "Exibindo _MENU_ registro(s)",
                "loadingRecords": "Por favor, aguarde - carregando...",
                "processing": "Processando...",
                "search": "Localizar:"
        }
    });  
}

function configurarTabelaSelecaoEstabelecimentos() {
    // Configurando Tabela
    $('#tb_selecao_estabelecimentos').DataTable({
        "paging": true,
        "pageLength": 4, // Quantidade de registros por paginação
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "processing": true,
        "columns": [
            { "width": "140px" }, // CNPJ/CPF
            null,                 // Razão
            null,                 // Fantasia
            { "width": "5px" }    // Ativo
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
                "infoEmpty": "Não existem dados com os parâmetros de pesquisa informado!", // "Sem dados para exibição",
                "infoFiltered":   "(Filtrada a partir de _MAX_ registros no total)",
                "zeroRecords": "Sem registro(s) para exibição",
                "lengthMenu": "Exibindo _MENU_ registro(s)",
                "loadingRecords": "Por favor, aguarde - carregando...",
                "processing": "Processando...",
                "search": "Localizar:"
        }
    });  
}

function configurarControles(editar_cadastro) {
    // Cadastro da Licença de Funcionamento
    document.getElementById('nr_processo').disabled     = !editar_cadastro;
    document.getElementById('dt_emissao').disabled      = !editar_cadastro;
    document.getElementById('nr_cnpj_licenca').disabled = !editar_cadastro;
    document.getElementById('btn_buscar_estabelecimento').disabled = !editar_cadastro;
    document.getElementById('cd_atividade').disabled    = !editar_cadastro;
    document.getElementById('cd_atividade_secundaria').disabled = !editar_cadastro;
    document.getElementById('cd_categoria').disabled    = !editar_cadastro;
    document.getElementById('cd_responsavel').disabled  = !editar_cadastro;
    document.getElementById('nm_responsavel_estabelecimento').disabled = !editar_cadastro;
    document.getElementById('nr_responsavel_estabelecimento').disabled = !editar_cadastro;
    document.getElementById('cn_responsavel_estabelecimento').disabled = !editar_cadastro;
    document.getElementById('ds_observacao').disabled   = !editar_cadastro;

    document.getElementById('btn_salvar').disabled  = !editar_cadastro;

    // Cadastro do Estabelecimentos
    document.getElementById('nr_cnpj').disabled  = ($('#nr_cnpj').val() !== "");

    document.getElementById('nr_insc_est').disabled = !editar_cadastro;
    document.getElementById('nr_insc_est').disabled = !editar_cadastro;
    document.getElementById('nr_insc_mun').disabled = !editar_cadastro;
    document.getElementById('nm_razao').disabled    = !editar_cadastro;
    document.getElementById('nm_fantasia').disabled = !editar_cadastro;
    document.getElementById('cd_cnae_principal').disabled  = !editar_cadastro;
    document.getElementById('cd_cnae_secundaria').disabled = !editar_cadastro;

    document.getElementById('cd_estado').disabled      = !editar_cadastro;
    document.getElementById('cd_cidade').disabled      = !editar_cadastro;
    document.getElementById('cd_bairro').disabled      = !editar_cadastro;
    document.getElementById('tp_endereco').disabled    = !editar_cadastro;
    document.getElementById('ds_endereco').disabled    = !editar_cadastro;
    document.getElementById('nr_endereco').disabled    = !editar_cadastro;
    document.getElementById('ds_complemento').disabled = !editar_cadastro;
    document.getElementById('btn_buscar_endereco').disabled = !editar_cadastro;
    document.getElementById('nr_cep').disabled         = !editar_cadastro;
    document.getElementById('sn_ativo').disabled       = !editar_cadastro;

    document.getElementById('nr_comercial').disabled = !editar_cadastro;
    document.getElementById('nr_telefone').disabled  = !editar_cadastro;
    document.getElementById('nr_celular').disabled   = !editar_cadastro;
    document.getElementById('nm_contato').disabled   = !editar_cadastro;
    document.getElementById('ds_email').disabled     = !editar_cadastro;

    document.getElementById('btn_salvar_estabelecimento').disabled = !editar_cadastro;
}

function SelecionarEstabelecimentoLicenca() {
    var params = {
        'ac'   : 'selecionar_estabelecimento',
        'token': $('#token').val()
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
            loadingGif(true);
        },
        // Colocamos o retorno na tela
        success : function(data){
            loadingGif(false);
            var retorno = data;
            $('#tabela_estabelecimento').html(retorno);
            configurarTabelaSelecaoEstabelecimentos();
            document.getElementById('painel_selecionar_estabelecimento').style.display = 'block';
        },
        error: function (request, status, error) {
            loadingGif(false);
            MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function SelecionarEstabelecimentoParaLicenca(referencia) {
    referencia = referencia.replace("estabelecimento_", "");
    
    var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
    var colunas = i_linha.getElementsByTagName('td');
    
    $('#id_estabelecimento_licenca').val( $('#cell_id_estabelecimento_' + referencia).val() );
    $('#nr_cnpj_licenca').val           ( $('#cell_nr_cnpj_' + referencia).val() );
    $('#nm_razao_licenca').val          ( colunas[1].firstChild.nodeValue );
    $('#cd_atividade').val              ( $('#cell_cd_cnae_principal_' + referencia).val() );
    $('#cd_atividade_secundaria').val   ( $('#cell_cd_cnae_secundaria_' + referencia).val() );
    $('#sn_licenca_publica').val        ( $('#cell_sn_orgao_publico_' + referencia).val() );
    
    document.getElementById('painel_selecionar_estabelecimento').style.display = 'none';
    
    $(".select2").select2();
}

function BuscarEstabelecimentoLicenca() {
    var params = {
        'ac'   : 'buscar_estabelecimento',
        'token': $('#token').val(),
        'id_estabelecimento' : $('#id_estabelecimento_licenca').val(),
        'nr_cnpj' : $('#nr_cnpj_licenca').val().trim().replace(/[^\d]+/g, "") // Deixar apenas os números
    };
    
    if ( (params.nr_cnpj === getGuidEmpty()) || (params.nr_cnpj === "") ) {
        //MensagemAlerta("Alerta", "Favor informar o CPF/CNPJ do Estabelecimento");
        SelecionarEstabelecimentoLicenca();
    } else {
        var validado = false;
        if (params.nr_cnpj.length < 14) {
            validado = validarCPF(params.nr_cnpj);
        } else {
            validado = validarCNPJ(params.nr_cnpj);
        }
        
        if ( !validado ) {
            MensagemAlerta("Validação", "Número de CPF/CNPJ inválido!");
        } else {
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
                    loadingGif(true);
                },
                // Colocamos o retorno na tela
                success : function(data){
                    loadingGif(false);
                    var retorno = data;
                    if ( retorno === "OK" ) {
                        var file_json = "logs/estabelecimento_" + getTokenId() + ".json"; 

                        $.getJSON(file_json, function(data){
                            this.qtd = data.estabelecimento.length;
                            $('#id_estabelecimento_licenca').val( data.estabelecimento[this.qtd - 1].id_estabelecimento );
                            $('#nm_razao_licenca').val          ( data.estabelecimento[this.qtd - 1].nm_razao );
                            $('#cd_atividade').val              ( data.estabelecimento[this.qtd - 1].cd_cnae_principal );
                            $('#cd_atividade_secundaria').val   ( data.estabelecimento[this.qtd - 1].cd_cnae_secundaria );
                            $('#sn_licenca_publica').val        ( data.estabelecimento[this.qtd - 1].sn_orgao_publico );
                            $(".select2").select2();
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
    }
}

function EditarEstabelecimentoLicenca(cnpj_vazio) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroEstabelecimentoID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        if (document.getElementById('painel_selecionar_estabelecimento').style.display !== 'none') {
            // Limpar campos de resultados de pesquisas antigas
            $('#id_estabelecimento_licenca').val(getGuidEmpty());
            $('#nr_cnpj_licenca').val("");
            $('#nm_razao_licenca').val("");
            $('#cd_atividade').val("0");
            $('#cd_atividade_secundaria').val("0");
            $('#sn_licenca_publica').val("0");
            
            document.getElementById('painel_selecionar_estabelecimento').style.display = 'none';
        }
        
        var params = {
            'ac'   : 'buscar_estabelecimento',
            'token': $('#token').val(),
            'id_estabelecimento' : $('#id_estabelecimento_licenca').val(),
            'nr_cnpj' : $('#nr_cnpj_licenca').val().trim().replace(/[^\d]+/g, "") // Deixar apenas os números
        };

        if ( (!cnpj_vazio)  && ((params.nr_cnpj === getGuidEmpty()) || (params.nr_cnpj === "")) ) {
            MensagemAlerta("Alerta", "Favor informar o CPF/CNPJ do Estabelecimento");
        } else {
            var validado = false;
            if (params.nr_cnpj.length < 14) {
                validado = validarCPF(params.nr_cnpj);
            } else {
                validado = validarCNPJ(params.nr_cnpj);
            }

            if ( !cnpj_vazio && !validado ) {
                MensagemAlerta("Validação", "Número de CPF/CNPJ inválido!");
            } else {
                var editar_cadastro = (strToInt($('#tp_situacao').val()) < _SITUACAO_LICENCA_APROVADA) || (strToInt($('#tp_situacao').val()) === _SITUACAO_LICENCA_SUSPENSA);
                
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
                        loadingGif(true);
                    },
                    // Colocamos o retorno na tela
                    success : function(data){
                        $('#guia_tab_endereco').trigger('click');

                        var cd_estado   = $("#cd_estado_padrao").val();
                        var cd_cidade   = $("#cd_cidade_padrao").val();
                        var cd_bairro   = "0";
                        var tp_endereco = "0";
                        var ds_endereco = "";

                        loadingGif(false);
                        var retorno = data;
                        if ( retorno === "OK" ) {
                            var file_json = "logs/estabelecimento_" + getTokenId() + ".json"; 

                            $.getJSON(file_json, function(data){
                                this.qtd = data.estabelecimento.length;
                                
                                cd_estado   = data.estabelecimento[this.qtd - 1].cd_estado;
                                cd_cidade   = data.estabelecimento[this.qtd - 1].cd_cidade;
                                cd_bairro   = data.estabelecimento[this.qtd - 1].cd_bairro;
                                tp_endereco = data.estabelecimento[this.qtd - 1].tp_endereco;
                                ds_endereco = data.estabelecimento[this.qtd - 1].ds_endereco;

                                $('#id_estabelecimento_licenca').val( data.estabelecimento[this.qtd - 1].id_estabelecimento );
                                $('#nm_razao_licenca').val          ( data.estabelecimento[this.qtd - 1].nm_razao );
                                $('#cd_atividade').val              ( data.estabelecimento[this.qtd - 1].cd_cnae_principal );
                                $('#cd_atividade_secundaria').val   ( data.estabelecimento[this.qtd - 1].cd_cnae_secundaria );

                                var id_estabelecimento = data.estabelecimento[this.qtd - 1].id_estabelecimento;
                                var tp_pessoa          = data.estabelecimento[this.qtd - 1].tp_pessoa;
                                var nr_cnpj            = data.estabelecimento[this.qtd - 1].nr_cnpj;
                                var nr_insc_mun        = data.estabelecimento[this.qtd - 1].nr_insc_mun;
                                var nr_insc_est        = data.estabelecimento[this.qtd - 1].nr_insc_est;
                                var nm_razao           = data.estabelecimento[this.qtd - 1].nm_razao;
                                var nm_fantasia        = data.estabelecimento[this.qtd - 1].nm_fantasia;
                                var nr_insc_est        = data.estabelecimento[this.qtd - 1].nr_insc_est;
                                var nr_insc_mun        = data.estabelecimento[this.qtd - 1].nr_insc_mun;
                                var cd_cnae_principal  = data.estabelecimento[this.qtd - 1].cd_cnae_principal;
                                var cd_cnae_secundaria = data.estabelecimento[this.qtd - 1].cd_cnae_secundaria;
                                var nr_endereco        = data.estabelecimento[this.qtd - 1].nr_endereco;
                                var ds_complemento     = data.estabelecimento[this.qtd - 1].ds_complemento;
                                var cd_uf              = data.estabelecimento[this.qtd - 1].cd_uf;
                                var nr_cep             = data.estabelecimento[this.qtd - 1].nr_cep;
                                var nr_comercial       = data.estabelecimento[this.qtd - 1].nr_comercial;
                                var nr_telefone        = data.estabelecimento[this.qtd - 1].nr_telefone;
                                var nr_celular         = data.estabelecimento[this.qtd - 1].nr_celular;
                                var nm_contato         = data.estabelecimento[this.qtd - 1].nm_contato;
                                var ds_email           = data.estabelecimento[this.qtd - 1].ds_email;
                                var sn_orgao_publico   = (data.estabelecimento[this.qtd - 1].sn_orgao_publico === "1");
                                var sn_ativo           = (data.estabelecimento[this.qtd - 1].sn_ativo === "1");

                                setEnderecoEstabelecimento(cd_estado, cd_cidade, cd_bairro, tp_endereco, ds_endereco, function(){
                                    $('#id_estabelecimento').val(id_estabelecimento);
                                    $('#tp_pessoa').val         (tp_pessoa);
                                    $('#nr_cnpj').val           (nr_cnpj);
                                    $('#nr_insc_est').val       (nr_insc_est);
                                    $('#nr_insc_mun').val       (nr_insc_mun);
                                    $('#nm_razao').val          (nm_razao);
                                    $('#nm_fantasia').val       (nm_fantasia);
                                    $('#cd_cnae_principal').val (cd_cnae_principal);
                                    $('#cd_cnae_secundaria').val(cd_cnae_secundaria);
                                    $('#nr_endereco').val       (nr_endereco);
                                    $('#ds_complemento').val    (ds_complemento);
                                    $('#cd_uf').val             (cd_uf);
                                    $('#nr_cep').val            (nr_cep);
                                    $('#nr_comercial').val      (nr_comercial);
                                    $('#nr_telefone').val       (nr_telefone);
                                    $('#nr_celular').val        (nr_celular);
                                    $('#nm_contato').val        (nm_contato);
                                    $('#ds_email').val          (ds_email);
                                    
                                    $("#sn_orgao_publico").prop("checked", sn_orgao_publico);
                                    $("#sn_ativo").prop("checked", sn_ativo);
                                    configurarControles(editar_cadastro);
                                                                        
                                    $('#guia_tab_atividade_estab').trigger('click');
                                    $('#guia_tab_endereco_estab').trigger('click');

                                    document.getElementById('painel_cadastro_estabelecimento').style.display = 'block';
                                    if (cnpj_vazio) {
                                        $('#nr_cnpj').focus();
                                    } else {
                                        $('#nm_razao').focus();
                                    }
                                    $(".select2").select2();
                                });
                            });
                        } else {
                            $('#id_estabelecimento_licenca').val(getGuidEmpty());
                            $('#nr_cnpj').val("");
                            $('#nm_razao_licenca').val("");
                            $('#cd_atividade').val("0");

                            setEnderecoEstabelecimento(cd_estado, cd_cidade, cd_bairro, tp_endereco, ds_endereco, function(){
                                $('#id_estabelecimento').val(getGuidEmpty());
                                $('#tp_pessoa').val         ("1");
                                $('#nr_cnpj').val           (params.nr_cnpj);
                                $('#nr_insc_mun').val       ("");
                                $('#nr_insc_est').val       ("");
                                $('#nm_razao').val          ("");
                                $('#nm_fantasia').val       ("");
                                $('#nr_insc_est').val       ("");
                                $('#nr_insc_mun').val       ("");
                                $('#cd_cnae_principal').val ("0");
                                $('#cd_cnae_secundaria').val("0");
                                $('#nr_endereco').val       ("");
                                $('#ds_complemento').val    ("");
                                $('#cd_uf').val             ("");
                                $('#nr_cep').val            ("");
                                $('#nr_comercial').val      ("");
                                $('#nr_telefone').val       ("");
                                $('#nr_celular').val        ("");
                                $('#nm_contato').val        ("");
                                $('#ds_email').val          ("");

                                $("#sn_orgao_publico").prop("checked", false);
                                $("#sn_ativo").prop("checked", true);
                                configurarControles(true);
                                
                                $('#guia_tab_atividade_estab').trigger('click');
                                $('#guia_tab_endereco_estab').trigger('click');

                                document.getElementById('painel_cadastro_estabelecimento').style.display = 'block';
                                if (cnpj_vazio) {
                                    $('#nr_cnpj').focus();
                                } else {
                                    $('#nm_razao').focus();
                                }
                                $(".select2").select2();
                            });
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
}

function SalvarEstabelecimentoLicenca() {
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

                        $.getJSON(file_json, function(data){
                            this.qtd = data.formulario.length;

                            $('#id_estabelecimento').val(data.formulario[this.qtd - 1].id_estabelecimento);
                            $('#nr_cnpj').val           (data.formulario[this.qtd - 1].nr_cnpj);
                            $('#nm_razao').val          (data.formulario[this.qtd - 1].nm_razao);
                            $('#cd_cnae_principal').val (data.formulario[this.qtd - 1].cd_cnae_principal);
                            $('#cd_cnae_secundaria').val(data.formulario[this.qtd - 1].cd_cnae_secundaria);
                            
                            $('#id_estabelecimento_licenca').val( $('#id_estabelecimento').val() );
                            $('#nr_cnpj_licenca').val           ( $('#nr_cnpj').val() );
                            $('#nm_razao_licenca').val          ( $('#nm_razao').val() );
                            $('#cd_atividade').val              ( $('#cd_cnae_principal').val() );
                            $('#cd_atividade_secundaria').val   ( $('#cd_cnae_secundaria').val() );
                            
                            
                            document.getElementById('painel_cadastro_estabelecimento').style.display = 'none';
                            $(".select2").select2();
                        });
                    } else {
                        MensagemAlerta("Alerta", retorno);
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

function PrepararPesquisaLicencaFuncionamento() {
    document.body.style.cursor = "auto";
    
    $('#resultado_pesquisa').html("");
    $("#ano_exercicio").val( $("#nr_exercicio_padrao").val() );
    $("#tipo_pesquisa").val("0");
    $(".select2").select2();
    
    $("#pesquisa").val("");
    $("#pesquisa").focus();
}

function PesquisarLicencaFuncionamento() {
    var params = {
        'ac'    : $('#ac').val(),
        'token' : $('#token').val(),
        'ano_exercicio' : $('#ano_exercicio').val(),
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
                  "      Favor aguarde o resultado da pesquisa de Licenças de Funcionamento cadastradas!" +
                  "    </div>" +
                  "    <div class=overlay>" +
                  "      <i class='fa fa-refresh fa-spin'></i>" +
                  "    </div>" +
                  "  </div>" +
                  "</div>";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/licenca_funcionamento_dao.php',
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
            configurarTabelaLicencaFuncionamento();
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#resultado_pesquisa').html("Erro (" + status + "):<br><br>" + request.responseText + "<br>Error : " + error);
        }
    });  
    // Finalizamos o Ajax
}

function NovaLicencaFuncionamento() {
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var data = new Date();
        
        CarregarEventoLicencaFuncionamento(getGuidEmpty());
        $('#guia_tab_observacao').trigger('click');
        $('#guia_tab_atividade').trigger('click');
        
        $('#id_licenca').val(getGuidEmpty());
        $('#nr_processo').val("");
        $('#nr_exercicio').val(data.getFullYear());
        $('#nr_licenca').val("");
        $('#dt_emissao').val( $('#dt_hoje').val() );
        $('#dt_validade').val("");
        $('#dt_validade').attr('disabled', true);
        $('#sn_provisoria').val("0");
        $('#id_estabelecimento_licenca').val(getGuidEmpty());
        $('#cd_categoria').val("0");
        $('#cd_atividade').val("0");
        $('#cd_atividade_secundaria').val("0");
        $('#tp_situacao').val(_SITUACAO_LICENCA_PENDENTE);
        $('#sn_licenca_publica').val("0");
        $('#cd_responsavel').val(getGuidEmpty());
        $('#nm_responsavel_estabelecimento').val("");
        $('#nr_responsavel_estabelecimento').val("");
        $('#cn_responsavel_estabelecimento').val("");
        $('#ds_observacao').val("");
        $('#nr_cnpj_licenca').val("");
        $('#nm_razao_licenca').val("");

        configurarControles(true);

        $("#div_eventos").html("");
        document.getElementById('painel_cadastro').style.display = 'block';
        $('#nr_processo').focus();
        $(".select2").select2();
    });
}

function EditarLicencaFuncionamento(estabelecimento) {
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso02, function(){
        var referencia      = estabelecimento.replace("licenca_funcionamento_", "");

        var id_licenca  = $("#cell_id_licenca_"  + referencia).val();
        var tp_situacao = $("#cell_tp_situacao_" + referencia).val();

        var i_linha = document.getElementById("linha_" + referencia); // Capturar a linha da tabela correspondente ao ID
        var colunas = i_linha.getElementsByTagName('td');

        $('#id_licenca').val(id_licenca);
        $('#tp_situacao').val(tp_situacao);
        $('#sn_licenca_publica').val( $("#cell_sn_licenca_publica_" + referencia).val() );
        $('#nr_processo').val (colunas[1].firstChild.nodeValue);
        $('#nr_exercicio').val( $("#cell_nr_exercicio_" + referencia).val() );
        $('#nr_licenca').val  ( zeroEsquerda($("#cell_nr_licenca_" + referencia).val(), 5) );
        $('#dt_emissao').val  ( $("#cell_dt_emissao_" + referencia).val() );
        $('#dt_validade').val ( $("#cell_dt_validade_" + referencia).val() );
        $('#dt_validade').attr('disabled', ($("#cell_sn_provisoria_" + referencia).val() === "0"));
        $('#sn_provisoria').val( $("#cell_sn_provisoria_" + referencia).val() );
        $('#id_estabelecimento_licenca').val ( $("#cell_id_estabelecimento_" + referencia).val() );
        $('#nm_razao_licenca').val(colunas[2].firstChild.nodeValue);
        $('#nr_cnpj_licenca').val (colunas[3].firstChild.nodeValue);
        $('#cd_atividade').val  ( $("#cell_cd_atividade_" + referencia).val() );
        $('#cd_atividade_secundaria').val  ( $("#cell_cd_atividade_secundaria_" + referencia).val() );
        $('#cd_categoria').val  ( $("#cell_cd_categoria_" + referencia).val() );
        $('#cd_responsavel').val( $("#cell_cd_responsavel_" + referencia).val() );
        $('#nm_responsavel_estabelecimento').val( $("#cell_nm_responsavel_estabelecimento_" + referencia).val() );
        $('#nr_responsavel_estabelecimento').val( $("#cell_nr_responsavel_estabelecimento_" + referencia).val() );
        $('#cn_responsavel_estabelecimento').val( $("#cell_cn_responsavel_estabelecimento_" + referencia).val() );
        $('#ds_observacao').val ( $("#cell_ds_observacao_" + referencia).val() );

        var editar_cadastro = (strToInt($('#tp_situacao').val()) < _SITUACAO_LICENCA_APROVADA) || ((strToInt($('#tp_situacao').val()) === _SITUACAO_LICENCA_SUSPENSA));
        configurarControles(editar_cadastro);

        document.getElementById('painel_cadastro').style.display = 'block';
        
        CarregarEventoLicencaFuncionamento(id_licenca);
        $('#guia_tab_observacao').trigger('click');
        $('#guia_tab_atividade').trigger('click');
        
        if (editar_cadastro === true) $('#nr_processo').focus();
        $(".select2").select2();
    });
} 

function SalvarLicencaFuncionamento() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
//    if ( $('#nr_processo').val().trim() === "" )      str_mensagem += str_marcador + "Número do Processo<br>";
    if ( $('#dt_emissao').val().trim()  === "" )      str_mensagem += str_marcador + "Data de Emissão<br>";
    if ( $('#dt_validade').val().trim()  === "" )     str_mensagem += str_marcador + "Data de Validade<br>";
    if ( $('#nr_cnpj_licenca').val().trim()  === "" ) str_mensagem += str_marcador + "Estabelecimento - CNPJ<br>";
    if ( $('#nm_razao_licenca').val().trim() === "" ) str_mensagem += str_marcador + "Estabelecimento - Razão Social<br>";
    if ( $('#cd_atividade').val().trim()   === "0" )  str_mensagem += str_marcador + "Atividade Principal<br>";
    if ( $('#cd_categoria').val().trim()   === "0" )  str_mensagem += str_marcador + "Categoria<br>";
    if ( $('#cd_responsavel').val().trim() === "0" )  str_mensagem += str_marcador + "Responsável Técnico<br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        str_mensagem = "";
        
        var nr_cnpj = $('#nr_cnpj_licenca').val().trim().replace(/[^\d]+/g, "") // Deixar apenas os números
        
        var validado = false;
        if (nr_cnpj.length < 14) {
            validado = validarCPF(nr_cnpj);
        } else {
            validado = validarCNPJ(nr_cnpj);
        }
        
        if (!validarData($('#dt_emissao').val()))  str_mensagem += str_marcador + "Data de Emissão<br>";
        if (!validarData($('#dt_validade').val())) str_mensagem += str_marcador + "Data de Validade<br>";
        if (!validado) str_mensagem += str_marcador + "Número de CPF/CNPJ<br>";
        
        if ( str_mensagem.trim() !== "" ) {
            MensagemAlerta("Validação", "<strong>Informação inválida no(s) campo(s) abaixo:</strong><br>" + str_mensagem);
        } else {
            var validado = false;
            if (nr_cnpj.length < 14) {
                validado = validarCPF(nr_cnpj);
            } else {
                validado = validarCNPJ(nr_cnpj);
            }

            if ( !validado ) {
                MensagemAlerta("Validação", "Número de CPF/CNPJ inválido!");
            } else {
                var inserir = ($('#id_licenca').val() === "") || ($('#id_licenca').val() === getGuidEmpty());
                var params  = {
                    'ac'    : 'salvar_licenca_funcionamento',
                    'token' : $('#token').val(),
                    'id_licenca'    : $('#id_licenca').val(),
                    'nr_exercicio'  : $('#nr_exercicio').val(),
                    'nr_licenca'    : $('#nr_licenca').val(),
                    'nr_processo'   : $('#nr_processo').val().trim(),
                    'dt_emissao'    : $('#dt_emissao').val(),
                    'dt_validade'   : $('#dt_validade').val(),
                    'sn_provisoria' : $('#sn_provisoria').val(),
                    'id_estabelecimento' : $('#id_estabelecimento_licenca').val(),
                    'nr_cnpj'  : $('#nr_cnpj_licenca').val().trim().replace(/[^\d]+/g, ""), // Deixar apenas os números
                    'nm_razao' : $('#nm_razao_licenca').val().trim(),
                    'cd_atividade'   : $('#cd_atividade').val(),
                    'cd_atividade_secundaria' : $('#cd_atividade_secundaria').val(),
                    'cd_categoria'   : $('#cd_categoria').val(),
                    'tp_situacao'    : $('#tp_situacao').val(),
                    'sn_licenca_publica' : $('#sn_licenca_publica').val(),
                    'cd_responsavel'     : $('#cd_responsavel').val(),
                    'nm_responsavel_estabelecimento' : $('#nm_responsavel_estabelecimento').val(),
                    'nr_responsavel_estabelecimento' : $('#nr_responsavel_estabelecimento').val(),
                    'cn_responsavel_estabelecimento' : $('#cn_responsavel_estabelecimento').val(),
                    'ds_observacao'  : $('#ds_observacao').val().trim()
                };

                if ( $('#sn_ativo').is(":checked") ) params.sn_ativo = $('#sn_ativo').val();

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/licenca_funcionamento_dao.php',
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
                            var file_json = "logs/licenca_funcionamento_" + getTokenId() + ".json"; 

                            // Bloco apenas para visualizar esses de PHP no momento da gravação dos dados
                            //var painel = document.getElementById("painel_cadastro_retorno"); 
                            //painel.innerHTML = xmlhttp.responseText + "\n" + file_json;

                            $.getJSON(file_json, function(data){
                                this.qtd = data.formulario.length;

                                $('#nr_processo').val( data.formulario[this.qtd - 1].nr_processo ); // Devolver Número do Processo independente da operação de gravação
                                
                                if ( inserir === true ) {
                                    $('#id_licenca').val  ( data.formulario[this.qtd - 1].id_licenca );
                                    $('#nr_exercicio').val( data.formulario[this.qtd - 1].nr_exercicio );
                                    $('#nr_licenca').val  ( data.formulario[this.qtd - 1].nr_licenca );
                                    AddTableRowLicencaFuncionamento();
                                } else {
                                    // Recuperar linha da tabela para alterar valor de linha
                                    var referencia = $('#id_licenca').val().replace(/-/g,'').replace("{", "").replace("}", "");
                                    var i_linha = document.getElementById("linha_" + referencia);
                                    var colunas = i_linha.getElementsByTagName('td');

                                    // Devolver valores para a linha/coluna
                                    colunas[1].firstChild.nodeValue = $('#nr_processo').val();
                                    colunas[2].firstChild.nodeValue = $('#nm_razao_licenca').val();
                                    colunas[3].firstChild.nodeValue = $('#nr_cnpj_licenca').val();

                                    // Armazenar valor retornado no campo oculto
                                    document.getElementById('cell_id_licenca_'    + referencia).value = $('#id_licenca').val();
                                    document.getElementById('cell_nr_exercicio_'  + referencia).value = $('#nr_exercicio').val();
                                    document.getElementById('cell_nr_licenca_'    + referencia).value = $('#nr_licenca').val();
                                    document.getElementById('cell_nr_processo_'   + referencia).value = $('#nr_processo').val();
                                    document.getElementById('cell_dt_emissao_'    + referencia).value = params.dt_emissao;
                                    document.getElementById('cell_dt_validade_'   + referencia).value = params.dt_validade;
                                    document.getElementById('cell_sn_provisoria_' + referencia).value = params.sn_provisoria;
                                    document.getElementById('cell_id_estabelecimento_' + referencia).value = params.id_estabelecimento;
                                    document.getElementById('cell_nr_cnpj_'  + referencia).value = $('#nr_cnpj_licenca').val();
                                    document.getElementById('cell_nm_razao_' + referencia).value = $('#nm_razao_licenca').val();
                                    document.getElementById('cell_cd_atividade_'   + referencia).value = params.cd_atividade;
                                    document.getElementById('cell_cd_atividade_secundaria_' + referencia).value = params.cd_atividade_secundaria;
                                    document.getElementById('cell_cd_categoria_'   + referencia).value = params.cd_categoria;
                                    document.getElementById('cell_tp_situacao_'    + referencia).value = params.tp_situacao;
                                    document.getElementById('cell_sn_licenca_publica_' + referencia).value = params.sn_licenca_publica;
                                    document.getElementById('cell_cd_responsavel_'     + referencia).value = params.cd_responsavel;
                                    document.getElementById('cell_nm_responsavel_estabelecimento_' + referencia).value = params.nm_responsavel_estabelecimento;
                                    document.getElementById('cell_nr_responsavel_estabelecimento_' + referencia).value = params.nr_responsavel_estabelecimento;
                                    document.getElementById('cell_cn_responsavel_estabelecimento_' + referencia).value = params.cn_responsavel_estabelecimento;
                                    document.getElementById('cell_ds_observacao_'  + referencia).value = params.ds_observacao;

                                    // Atualizar imagem da célula de acordo com o status do registro
                                    $('#img_ativo_' + referencia).removeClass("fa-check-square-o");
                                    $('#img_ativo_' + referencia).removeClass("fa-circle-thin");
                                    if (strToInt(params.tp_situacao) === _SITUACAO_LICENCA_APROVADA) {
                                        $('#img_ativo_' + referencia).addClass("fa-check-square-o");
                                    } else {
                                        $('#img_ativo_' + referencia).addClass("fa-circle-thin");
                                    }
                                }

                                document.getElementById('painel_cadastro').style.display = 'none';
                            });
                        } else {
                            MensagemAlerta("Alerta", retorno);
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
}

function SalvarEventoLicencaFuncionamento() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    
    if ( $('#dt_evento').val().trim()  === "" ) str_mensagem += str_marcador + "Data <br>";
    if ( ($('#id_tecnico').val().trim() === "") || ($('#id_tecnico').val().trim() === getGuidEmpty()) )  str_mensagem += str_marcador + "Técnico Responsável <br>";
    if ( $('#ds_evento').val().trim()  === "" ) str_mensagem += str_marcador + "Descrição do Evento <br>";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        if (!validarData($('#dt_evento').val()))  str_mensagem += str_marcador + "Data do Evento<br>";
        
        if ( str_mensagem.trim() !== "" ) {
            MensagemAlerta("Validação", "<strong>Informação inválida no(s) campo(s) abaixo:</strong><br>" + str_mensagem);
        } else {
            var params  = {
                'ac'    : 'salvar_evento_licenca',
                'token' : $('#token').val(),
                'id_evento_estabelecimento'   : $('#id_evento_estabelecimento').val(),
                'id_evento_licenca'  : $('#id_evento_licenca').val(),
                'id_evento_processo' : $('#id_evento_processo').val(),
                'dt_evento'  : $('#dt_evento').val().trim(),
                'ds_evento'  : $('#ds_evento').val().trim(),
                'id_tecnico' : $('#id_tecnico').val()
            };
            
            // Iniciamos o Ajax 
            $.ajax({
                // Definimos a url
                url : 'pages/licenca_funcionamento_dao.php',
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
                    var retorno = data;
                    if ( retorno === "OK" ) {
                        loadingGif(false);
                        document.getElementById('painel_cadastro_evento').style.display = 'none';
                    } else {
                        loadingGif(false);
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
    }
}

function ExcluirLicencaFuncionamento(referencia, linha) {
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(getLgUsuario(), getPfUsuario(), rotina, tipoAcesso03, function(){
        referencia = referencia.replace("excluir_licenca_funcionamento_", "");
        var id_estabelecimento = $('#cell_id_estabelecimento_'  + referencia).val();
        var id_licenca   = $('#cell_id_licenca_'   + referencia).val();
        var nr_processo  = $('#cell_nr_processo_'  + referencia).val();
        var tp_situacao  = $('#cell_tp_situacao_'  + referencia).val();
        var nr_exercicio = $('#cell_nr_exercicio_' + referencia).val();
        var nr_licenca   = $('#cell_nr_licenca_'   + referencia).val();

        if ( tp_situacao > _SITUACAO_LICENCA_AGUARDANDO ) {
            MensagemAlerta("Restrição", "<strong>" + nr_exercicio + "/" + zeroEsquerda(nr_licenca, 5) + "</strong> - Registros de Licenças de Funcionamento em situação <strong>'" + _SITUACAO_LICENCA[tp_situacao] + "'</strong> não poderá ser excluída!");
        } else {
            MensagemConfirmar(
                    "Excluir Registro", 
                    "<strong>" + nr_exercicio + "/" + zeroEsquerda(nr_licenca, 5) + "</strong> - Confirma a <strong>exclusão</strong> do registro de Licença de Funcionamento selecionado?", 
                    "300px");
            var link = document.getElementById("botao_confirma_main_sim");
            link.onclick = function() {
                document.getElementById("painel_confirma_main").style.display = 'none';
                var params  = {
                    'ac'         : 'excluir_licenca_funcionamento',
                    'token'      : $('#token').val(),
                    'id_estabelecimento' : id_estabelecimento,
                    'id_licenca' : id_licenca,
                    'nr_processo': nr_processo,
                    'tp_situacao': tp_situacao
                };

                // Iniciamos o Ajax 
                $.ajax({
                    // Definimos a url
                    url : 'pages/licenca_funcionamento_dao.php',
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
                        var retorno = data;
                        if ( retorno === "OK" ) {
                            loadingGif(false);
                            RemoveTableRow(linha);
                        } else {
                            loadingGif(false);
                            MensagemAlerta("Alerta", retorno);
                        }
                    },
                    error: function (request, status, error) {
                        loadingGif(false);
                        MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
                    }
                });  
                // Finalizamos o Ajax
            };
        };
    });
}

function CarregarEventoLicencaFuncionamento(id_licenca) {
    $('#guia_tab_ocorrencia').trigger('click');
    $('#div_eventos').html("");
    
    if (id_licenca !== getGuidEmpty()) {
        var params  = {
            'ac'    : 'carregar_eventos_licenca_funcionamento',
            'token' : $('#token').val(),
            'id_licenca' : id_licenca
        };
        
        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/licenca_funcionamento_dao.php',
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
                $('#div_eventos').html(data);
                
                // Configurar Tabela de Eventos
                $('#tb_eventos_licenca').DataTable({
                    "paging": true,
                    "pageLength": 3, // Apenas 3 registros por paginação
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "processing": true,
                    "columns": [
                        { "width": "80px" } , // Data
                        null,                 // Descrição
                        { "width": "5px" }    // Excluir
                    ],
                    "order": [[0, 'desc']], // "order": [] <-- Ordenação indefinida
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
                            "infoEmpty": "Não existem dados com os parâmetros de pesquisa informado!", // "Sem dados para exibição",
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
                MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    }
}

(function($) {
    AddTableRowLicencaFuncionamento = function() {

    var referencia = $('#id_licenca').val().replace(/-/g,'').replace("{", "").replace("}", "");
    var tabela = "";
    var input  = "";
    var ativo  = "";

    // Verifica se exite tabela para exibição nos dados na página atual,
    // e caso não exista, construí-la.
    var pagina = document.getElementById("resultado_pesquisa"); 

    if ( pagina.innerHTML.indexOf("tb_licencas_funcionamento") === -1 ) {
        tabela += "<div class='box box-info' id='box_resultado_pesquisa'>";
        tabela += "    <div class='box-header with-border'>";
        tabela += "        <h3 class='box-title'><b>Resultado da Pesquisa</b></h3>";
        tabela += "    </div>";
        tabela += "    ";
        tabela += "    <div class='box-body'>";

        tabela += "<a id='ancora_licencas_funcionamento'></a><table id='tb_licencas_funcionamento' class='table table-bordered table-hover'  width='100%'>";

        tabela += "<thead>";
        tabela += "    <tr>";
        tabela += "        <th>Controle</th>";
        tabela += "        <th>Processo</th>";
        tabela += "        <th>Estabelecimento</th>";
        tabela += "        <th>CPF/CNPJ</th>";
        tabela += "        <th>Validade</th>";
        tabela += "        <th data-orderable='false'>Situação</th>"; // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "        <th data-orderable='false'></th>";         // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "        <th data-orderable='false'></th>";         // Desabilitar a ordenação nesta columa pelo jQuery. 
        tabela += "    </tr>";
        tabela += "</thead>";
        tabela += "<tbody>";
        tabela += "</tbody>";
        tabela += "</table>";
        
        $('#resultado_pesquisa').html(tabela);
    }

    var opcoes  = "";
    var editar  = "<a id='licenca_funcionamento_"         + referencia + "' href='javascript:preventDefault();' onclick='EditarLicencaFuncionamento( this.id )'>  " + $('#nr_exercicio').val() + "/" + zeroEsquerda($('#nr_licenca').val(), 5) + "&nbsp;&nbsp;&nbsp;<i class='fa fa-edit' title='Editar Registro'>";
    var excluir = "<a id='excluir_licenca_funcionamento_" + referencia + "' href='javascript:preventDefault();' onclick='ExcluirLicencaFuncionamento( this.id, this )'><i class='fa fa-trash' title='Excluir Registro'></i>";

    opcoes  = "<div class='btn-group'> ";
    opcoes += "    <button type='button' class='btn btn-primary'><i class='fa fa-edit' title='Mais Opções'></i></button> ";
    opcoes += "    <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'> ";
    opcoes += "        <span class='caret'></span> ";
    opcoes += "        <span class='sr-only'></span> ";
    opcoes += "    </button> ";
    opcoes += "    <ul class='dropdown-menu' role='menu'> ";
    opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_AGUARDANDO + ")'><i class='fa fa-calendar'></i> Aguardar</a></li> ";
    opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_APROVADA   + ")'><i class='fa fa-check-square-o'></i> Aprovar</a></li> ";
    opcoes += "        <li><a href='javascript:preventDefault();' id='set_licenca_" + referencia + "' onclick='setSituacaoLicencaFuncionamento(this.id, " + _SITUACAO_LICENCA_SUSPENSA   + ")'><i class='fa fa-circle-o'></i> Suspender</a></li> ";
    opcoes += "        <li class='divider'></li> ";
    opcoes += "        <li><a href='javascript:preventDefault();' id='emt_licenca_" + referencia + "' onclick='EmitirLicencaFuncionamento(this.id)'><i class='fa fa-print'></i> Emitir Licença</a></li> ";
    opcoes += "        <li><a href='javascript:preventDefault();' id='evt_licenca_" + referencia + "' onclick='EventoLicencaFuncionamento(this.id)'><i class='fa fa-bell-o'></i> Registrar Eventos</a></li> ";
    opcoes += "    </ul> ";
    opcoes += "</div> ";

    input  = "<input type='hidden' id='cell_id_licenca_"    + referencia + "' value='" + $('#id_licenca').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_exercicio_"  + referencia + "' value='" + $('#nr_exercicio').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_licenca_"    + referencia + "' value='" + $('#nr_licenca').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_processo_"   + referencia + "' value='" + $('#nr_processo').val().trim() + "'/>";
    input += "<input type='hidden' id='cell_dt_emissao_"    + referencia + "' value='" + $('#dt_emissao').val() + "'/>";
    input += "<input type='hidden' id='cell_dt_validade_"   + referencia + "' value='" + $('#dt_validade').val() + "'/>";
    input += "<input type='hidden' id='cell_sn_provisoria_" + referencia + "' value='" + $('#sn_provisoria').val() + "'/>";
    input += "<input type='hidden' id='cell_id_estabelecimento_" + referencia + "' value='" + $('#id_estabelecimento_licenca').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_cnpj_"        + referencia + "' value='" + $('#nr_cnpj_licenca').val() + "'/>";
    input += "<input type='hidden' id='cell_nm_razao_"       + referencia + "' value='" + $('#nm_razao_licenca').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_atividade_"   + referencia + "' value='" + $('#cd_atividade').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_atividade_secundaria_" + referencia + "' value='" + $('#cd_atividade_secundaria').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_categoria_"   + referencia + "' value='" + $('#cd_categoria').val() + "'/>";
    input += "<input type='hidden' id='cell_tp_situacao_"    + referencia + "' value='" + $('#tp_situacao').val() + "'/>";
    input += "<input type='hidden' id='cell_sn_licenca_publica_" + referencia + "' value='" + $('#sn_licenca_publica').val() + "'/>";
    input += "<input type='hidden' id='cell_cd_responsavel_"     + referencia + "' value='" + $('#cd_responsavel').val() + "'/>";
    input += "<input type='hidden' id='cell_nm_responsavel_estabelecimento_" + referencia + "' value='" + $('#nm_responsavel_estabelecimento').val() + "'/>";
    input += "<input type='hidden' id='cell_nr_responsavel_estabelecimento_" + referencia + "' value='" + $('#nr_responsavel_estabelecimento').val() + "'/>";
    input += "<input type='hidden' id='cell_cn_responsavel_estabelecimento_" + referencia + "' value='" + $('#cn_responsavel_estabelecimento').val() + "'/>";
    input += "<input type='hidden' id='cell_ds_observacao_"  + referencia + "' value='" + $('#ds_observacao').val().trim() + "'/>";
    
    if ( strToInt($('#tp_situacao').val()) === _SITUACAO_LICENCA_APROVADA ) {
        ativo  = "<a href='#'><i id='img_ativo_" + referencia + "' class='fa fa-check-square-o' title='" + _SITUACAO_LICENCA[strToInt($('#tp_situacao').val())] + "'>&nbsp;" + input + "</i></a>";
    } else {
        ativo  = "<a href='#'><i id='img_ativo_" + referencia + "' class='fa fa-circle-thin' title='" + _SITUACAO_LICENCA[strToInt($('#tp_situacao').val())] + "'>&nbsp;" + input + "</i></a>";
    }
    
    var newRow = $("<tr id='linha_" + referencia + "'>");
    var cols = "";

    cols += "<td>" + editar + "</td>";
    cols += "<td>" + $('#nr_processo').val().trim() + "</td>";
    cols += "<td>" + $('#nm_razao_licenca').val()   + "</td>";
    cols += "<td>" + $('#nr_cnpj_licenca').val()    + "</td>";
    cols += "<td>" + $('#dt_validade').val()        + "</td>";
    cols += "<td><div id='situacao_" + referencia + "'>" + opcoes + "&nbsp;&nbsp;<strong>" + _SITUACAO_LICENCA[strToInt($('#tp_situacao').val())] + "</strong></div></td>";
    cols += "<td align=center>" + ativo   + "</td>";
    cols += "<td align=center>" + excluir + "</td>";

    newRow.append(cols);

    $("#tb_licencas_funcionamento").append(newRow);
    if ( tabela !== "" ) configurarTabelaLicencaFuncionamento();
    
    return false;
  };
})(jQuery);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var menuTabelaAuxiliarID   = 0;
var menuCadastroID         = 1;
var menuEmissaoDocumentoID = 2;

var rotinaEnderecoEstadoID    = 0;
var rotinaEnderecoMunicipioID = 1;
var rotinaEnderecoTipoID      = 2;
var rotinaEnderecoBairroID    = 3;
var rotinaEnderecoCepID       = 4;
var rotinaTabelaCnaeID        = 5;

var rotinaCadastroTecnicoID         = 0;
var rotinaCadastroEstabelecimentoID = 1;

var rotinaEmissaoLicencaID = 0;
var rotinaEmissaoOficioID  = 1;

var tipoAcesso01 = "0100"; // Visualizar
var tipoAcesso02 = "0200"; // Modificar (Inserir e Editar)
var tipoAcesso03 = "0300"; // Controle Total (Visualizar, Inserir, Editar e Excluir) OU . . . Permissões Especiais []
var tipoAcesso04 = "0400"; // . . . Permissões Especiais []
var tipoAcesso05 = "0500"; // . . . Permissões Especiais []
var tipoAcesso06 = "0600"; // . . . Permissões Especiais []
var tipoAcesso07 = "0700"; // . . . Permissões Especiais []
var tipoAcesso08 = "0800"; // . . . Permissões Especiais []
var tipoAcesso09 = "0900"; //  . . .Permissões Especiais []

var menus = new Array(
	["01001000000", "Tabelas Auxiliares", Array(
		["01001010000", "Endereço > Estados", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar'
		)],
		["01001020000", "Endereço > Municípios", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar'
		)],
		["01001030000", "Endereço > Tipos de Logradouros", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar'
		)],
		["01001040000", "Endereço > Bairros", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar',
                        ' Controle Total'
		)],
		["01001050000", "Endereço > CEPs", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar',
                        ' Controle Total'
		)],
		["01001060000", "CNAEs", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar',
                        ' Controle Total'
		)]
	)],
	["01002000000", "Cadastros", Array(
		["01002010000", "Técnicos", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar',
                        ' Controle Total'
		)],
		["01002020000", "Estabelecimentos", Array(
			' Sem Permissão',
			' Visualizar',
			' Modificar',
                        ' Controle Total'
		)]
	)],
	["01003000000", "Emissão de Documentos", Array(
		["01003010000", "Licença de Funcionamento", Array(
			' Sem Permissão',
			' Visualizar',
			' Imprimir Licença',
			' Modificar',
			' Suspender Licença',
                        ' Controle Total'
		)],
		["01003020000", "Ofícios", Array(
			' Sem Permissão',
			' Visualizar',
			' Imprimir Ofício',
			' Modificar',
                        ' Controle Total'
		)]
	)]
);

function prepareList() {
    $('#expList').find('li:has(ul)').unbind('click').click(function(event) {
        if(this == event.target) {
            $(this).toggleClass('expanded');
            $(this).children('ul').toggle('medium');
        }
        return false;
    }).addClass('collapsed').removeClass('expanded').children('ul').hide();

    //Hack to add links inside the cv
    $('#expList a').unbind('click').click(function() {
        window.open($(this).attr('href'));
        return false;
    });

    //Create the button functionality
    $('#expandList').unbind('click').click(function() {
        $('.collapsed').addClass('expanded');
        $('.collapsed').children().show('medium');
    })
    $('#collapseList').unbind('click').click(function() {
        $('.collapsed').removeClass('expanded');
        $('.collapsed').children().hide('medium');
    })
};

function MontarArvorePermissao(cd_perfil, ds_perfil, tx_permissao) {
    var result = document.getElementById("painel_permissao");
    var permis = "<input type='hidden' id='cd_perfil_permissao' value='" + cd_perfil + "'/>\n\
        <div class='callout callout-info' id='ds_perfil_permissao'><strong>Perfil:</strong><br><p>" + ds_perfil + "</p></div>";
    
    var rotina = "";
    var matriz = "";
    var raiz   = "";
    var acesso = "";
    var arvore = "";
    
    arvore += "<div id='listContainer'>";
    arvore += "  <ul id='expList'>";
    arvore += "    <li><img src='pmw.png'> " + getSistemaNome();
    arvore += "      <ul>";
    
    // Índices da matriz
    // 0  - ID 
    // 1  - Descrição
    // 2  - Array

    // Montando Menus
    for (var mnu = 0; mnu < menus.length; mnu++) {
        arvore += "        <li><img src='form-edit.png'> " + menus[mnu][1] + "<!-- " + menus[mnu][0] + " -->"; 
        arvore += "          <ul>";
        
        // Montando Rotinas
        for (var rot = 0; rot < menus[mnu][2].length; rot++) {
            rotina = menus[mnu][2][rot][0];
            raiz   = menus[mnu][2][rot][0].substring(0, 7);

            arvore += "            <li><img src='form-user.png'>" + menus[mnu][2][rot][1] + " <!-- " + rotina + " -->";
            arvore += "              <ul>";
            arvore += "                <table id='tabela_" + rotina + "'>";
            
            // Montando Tipos de Acessos
            for (var acs = 0; acs < menus[mnu][2][rot][2].length; acs++) {
                var fa_checkbox = "fa-genderless";
                var parametros  = "";
                var valor_check = "0";
                if (acs === 0) {
                    fa_checkbox = "fa-check-square-o";
                    valor_check = "1";
                }
                
                acesso      = "0" + acs + "00";
                parametros  = mnu + "|" + rot + "|" + raiz + acesso;
                matriz     += parametros + ";";
                arvore += "                  <tr id='linha_" + 
                        raiz + acesso + "' onclick=MarcarRotinaPermissao('" + 
                        parametros    + "')><td><input type='hidden' id='rotina_" + 
                        raiz + acesso + "' value='" + valor_check + "'/><i id='img_" + 
                        raiz + acesso + "' class='fa " + fa_checkbox + "'/> " + menus[mnu][2][rot][2][acs] + "</td></tr>";
            }
            
            arvore += "                </table>";
            arvore += "              </ul>";
            arvore += "            </li>";
        }
        
        arvore += "          </ul>";
        arvore += "        </li>"; 
    } 
    
    arvore += "      </ul>";
    arvore += "    </li>";
    arvore += "  </ul>";
    arvore += "</div><br><br><div id='testar'></div>";
    arvore += "<input type='hidden' id='matriz_rotinas' value='" + matriz + "'/>";
    arvore += "<input type='hidden' id='gravar_rotinas' value=''/>";
    
//    arvore += "<div class='box-tools pull-left'>";
//    arvore += "    <button type='button' class='btn btn-info btn-sm' data-widget='remove' title='Remover Todas as Permissões' id='btn_remover_permissao' onclick='LimparPermissaoUsuario()'><i class='fa fa-eraser'></i></button>";
//    arvore += "    <button type='button' class='btn btn-info btn-sm' data-widget='check'  title='Adicionar Todas as Permissões' id='btn_adicionar_permissao' onclick='AdicionarPermissaoUsuario()'><i class='fa fa-check-square-o'></i></button>";
//    arvore += "</div>";
//    arvore += "<div class='box-tools pull-right'>";
//    arvore += "    <button type='button' class='btn btn-info btn-sm' data-widget='save' title='Salvar configurações de Permissão' id='btn_salvar_permissao' onclick='ConfirmacaoGravarPermissaoUsuario()'><i class='fa fa-save'></i></button>";
//    arvore += "</div>";

    result.innerHTML = permis + "\n" + arvore;
    $(".select2").select2();
    prepareList();

    // Marcar as permissões do perfil
    var acessos = tx_permissao;
    acessos = acessos.split(";");

    for (var i = 0; i < acessos.length; i++) {
        MarcarRotinaPermissao(acessos[i]);
    }
}

function MarcarRotinaPermissao(value) {
    if ( value.trim() !== "" ) {
        var configuracao = value.split("|");
        var idxMenu   = configuracao[0];
        var idxRotina = configuracao[1];
        var acesso    = configuracao[2];

        var qtde_tipo = menus[idxMenu][2][idxRotina][2].length;
        var rotina    = acesso.substring(0, 7);

        for (var x = 0; x < qtde_tipo; x++) {
            var obj = rotina + "0" +  x + "00";

            $('#img_' + obj).removeClass("fa");
            $('#img_' + obj).removeClass("fa-check-square-o");
            $('#img_' + obj).removeClass("fa-genderless");

            $('#img_' + obj).addClass("fa");
            $('#img_' + obj).addClass("fa-genderless");

            document.getElementById('rotina_' + obj).value = "0";
        }

        $('#img_' + acesso).removeClass("fa");
        $('#img_' + acesso).removeClass("fa-check-square-o");
        $('#img_' + acesso).removeClass("fa-genderless");

        $('#img_' + acesso).addClass("fa");
        $('#img_' + acesso).addClass("fa-check-square-o");

        document.getElementById('rotina_' + acesso).value = "1";
    }
}

function LimparPermissaoPerfil() {
    if ($('#cd_perfil_permissao').val() === "") exit;
    MensagemConfirmar(
        "Limpar Permissões", 
        "Deseja <strong>remover todas as permissões</strong> do perfil selecionado?", 
        "200px");
    var link = document.getElementById("botao_confirma_main_sim");
    link.onclick = function() {
        document.getElementById("painel_confirma_main").style.display = 'none';
        
        $('#gravar_rotinas').val("");

        var raiz   = "";
        var acesso = "";

        for (var mnu = 0; mnu < menus.length; mnu++) {
            for (var rot = 0; rot < menus[mnu][2].length; rot++) {
                raiz = menus[mnu][2][rot][0].substring(0, 7);

                for (var acs = 0; acs < menus[mnu][2][rot][2].length; acs++) {
                    var parametros  = "";
                    acesso      = "0" + acs + "00";
                    parametros  = mnu + "|" + rot + "|" + raiz + acesso;
                    if (acs === 0) MarcarRotinaPermissao(parametros);
                }
            }
        } 
    }    
}

function AdicionarPermissaoPerfil() {
    if ($('#cd_perfil_permissao').val() === "") exit;
    MensagemConfirmar(
        "Adicionar Permissões", 
        "Deseja <strong>adicionar todas as permissões</strong> para o perfil selecionado?", 
        "200px");
    var link = document.getElementById("botao_confirma_main_sim");
    link.onclick = function() {
        document.getElementById("painel_confirma_main").style.display = 'none';
        
        var raiz   = "";
        var acesso = "";

        for (var mnu = 0; mnu < menus.length; mnu++) {
            for (var rot = 0; rot < menus[mnu][2].length; rot++) {
                raiz = menus[mnu][2][rot][0].substring(0, 7);

                for (var acs = 0; acs < menus[mnu][2][rot][2].length; acs++) {
                    var parametros  = "";
                    acesso      = "0" + acs + "00";
                    parametros  = mnu + "|" + rot + "|" + raiz + acesso;
                    if (acs === (menus[mnu][2][rot][2].length - 1)) MarcarRotinaPermissao(parametros);
                }
            }
        } 
    } 
}

function SalvarPermissaoPerfil() {
    if ( $('#cd_perfil_permissao').val().trim() === "" ) {
        MensagemAlerta("Permissão de Acesso", "Favor selecionar o perfil desejado e configurar suas permissões de acesso!");
        
    } else {
        MensagemConfirmar(
            "Salvar Permissões", 
            "Confirma a configuração de permissões informada para o perfil selecionado?", 
            "200px");
        var link = document.getElementById("botao_confirma_main_sim");
        link.onclick = function() {
            document.getElementById("painel_confirma_main").style.display = 'none';
            GravarPermissaoPerfil();
        }

        var matriz_rotinas = $('#matriz_rotinas').val(); 
        var acessos  = matriz_rotinas.split(";"); 
        var direitos = "";
        var i = 0;
        for (i = 0; i < acessos.length; i++) {
            var rotina = acessos[i].split("|");
            var acesso = rotina[2].substring(7, 11); 
            if ( parseInt(acesso) !== 0 ) {
                if (parseInt($('#rotina_' + rotina[2]).val()) === 1) {
                    direitos += acessos[i] + ";";  
                    $('#gravar_rotinas').val(direitos);
                }
            }
        }
    }
}

function GravarPermissaoPerfil(){
    var params  = {
        'ac'          : 'salvar_permissao_perfil',
        'token'       : $('#token').val(),
        'cd_sistema'  : getSistema(),
        'cd_perfil'   : parseInt($('#cd_perfil_permissao').val().trim()),
        'tx_permissao': $('#gravar_rotinas').val()
    };
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/permissao_perfil_dao.php',
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
            if ( data === "OK" ) {
                document.getElementById("painel_configurar").style.display = 'none';
                $('#cell_permissao_' + params.cd_perfil).val(params.tx_permissao);
                
                // Atualizar imagem da célula de acordo com a presença das permissões
                $('#img_acesso_' + params.cd_perfil).removeClass("fa-cog");
                $('#img_acesso_' + params.cd_perfil).removeClass("fa-cogs");
                if (params.tx_permissao.trim() === "") {
                    $('#img_acesso_' + params.cd_perfil).addClass("fa-cog");
                } else {
                    $('#img_acesso_' + params.cd_perfil).addClass("fa-cogs");
                }
            } else {
                MensagemErro("Erro", data);
            }
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function CarregarPermissaoAcesso(perfil) {
    var params  = {
        'ac'          : 'carregar_permissao_perfil',
        'token'       : getTokenId(),
        'cd_sistema'  : getSistema(),
        'cd_perfil'   : parseInt(perfil)
    };

    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/permissao_perfil_dao.php',
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
            if ( retorno !== "OK" ) {
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

function getPermissaoPerfil(usuario, perfil, rotina, tipo_acesso, callback) {
    var file_acesso = "logs/perfil_permissao_" + getTokenId() + ".json"; 
    $.getJSON(file_acesso, function(data){
        if (perfil === data.permissao[0].cd_perfil) {
            var permissoes = data.permissao[0].tx_permissao;
            var localizar  = permissoes.indexOf(rotina); 
            if ( localizar !== -1 ) {
                var acesso = permissoes.substr(localizar, 11);
                acesso = acesso.replace(rotina, "");
                if (acesso < strToInt(tipo_acesso)) {
                    MensagemAlerta("Restrição", "Usuário <strong>'" + usuario +"'</strong> sem premissão para esta rotina³");
                    return false;
                } else {
                    // verifica se o parâmetro callback é realmente uma função antes de executá-lo
                    if(callback && typeof(callback) === "function") {
                        callback();
                    }
                    return true;
                }
            } else {
                MensagemAlerta("Restrição", "Usuário <strong>'" + usuario +"'</strong> sem premissão para esta rotina²");
                return false;
            }
        } else {
            MensagemAlerta("Restrição", "Usuário <strong>'" + usuario +"'</strong> sem premissão para esta rotina¹");
            return false;
        }
    });
}

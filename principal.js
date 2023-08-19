/* 
 * Funções Ajax para a chamada de Páginas
 */

function removerMarcadores() {
    $('#op_endereco_estado').removeClass('active');
    $('#op_endereco_cidade').removeClass('active');
    $('#op_endereco_tipo').removeClass('active');
    $('#op_endereco_bairro').removeClass('active');
    $('#op_endereco_cep').removeClass('active');
    
    $('#op_tabela_cnae').removeClass('active');
    
    $('#op_cadastro_tecnico').removeClass('active');
    $('#op_cadastro_estabelecimento').removeClass('active');
    
    $('#op_emitir_licenca').removeClass('active');
    
    $('#op_acesso_perfil').removeClass('active');
    $('#op_acesso_usuario').removeClass('active');
}

function MensagemInforme(titulo, mensagem) {
    document.getElementById("titulo_informe").innerHTML        = titulo;
    document.getElementById("painel_informe").style.paddingTop = "200px";
    document.getElementById("painel_informe").style.display    = 'block';
    document.getElementById("msg_informe").innerHTML           = mensagem; 
}

function MensagemAlerta(titulo, mensagem) {
    document.getElementById("titulo_alerta").innerHTML        = titulo;
    document.getElementById("painel_alerta").style.paddingTop = "200px";
    document.getElementById("painel_alerta").style.display    = 'block';
    document.getElementById("msg_alerta").innerHTML           = mensagem; 
}

function MensagemErro(titulo, mensagem) {
    document.getElementById("titulo_erro").innerHTML        = titulo;
    document.getElementById("painel_erro").style.paddingTop = "200px";
    document.getElementById("painel_erro").style.display    = 'block';
    document.getElementById("msg_erro").innerHTML           = mensagem; 
}

function MensagemConfirmar(titulo, mensagem, posicao) {
    document.getElementById("titulo_confirma_main").innerHTML        = titulo;
    document.getElementById("painel_confirma_main").style.paddingTop = posicao;
    document.getElementById("painel_confirma_main").style.display    = 'block';
    document.getElementById("msg_confirma_main").innerHTML           = mensagem; 
}

function HomeDesktop() {
    var params = {
        'ac' : 'homeDesktop'
    };
    
    var aguarde = "";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'controle.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            removerMarcadores();
            document.body.style.cursor = "wait";
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#desktop').html(data);
            document.body.style.cursor = "auto";
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function ExibirCalendario() {
    var params = {
        'ac' : 'calendario'
    };
    
    var aguarde = "";
          
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'calendario.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            removerMarcadores();
            $('#op_utilitario_calendaio').addClass('active');
            document.body.style.cursor = "wait";
        },
        // Colocamos o retorno na tela
        success : function(data){
            $('#desktop').html(data);
            document.body.style.cursor = "auto";
        },
        error: function (request, status, error) {
            document.body.style.cursor = "auto";
            $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function EstadoDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoEstadoID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'estado',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Estados") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/estado_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();

                PesquisarEstado();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function MunicipioDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoMunicipioID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'municipio',
            'token' : getTokenId()
        };

        MensagemInforme("Informe", "Rotina não disponível nesta versão!");
        exit;
        
        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Municípios") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/municipio_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();

                PesquisarEstado();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function TipoLogradouroDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoTipoID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'tipo_logradouro',
            'token' : getTokenId()
        };

        MensagemInforme("Informe", "Rotina não disponível nesta versão!");
        exit;
        
        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Tipos de Logradouros") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/tipo_logradouro_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();

                PesquisarEstado();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function BairroDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoBairroID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'bairro',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Bairros") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/bairro_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();

                PesquisarBairro();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function CepDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaEnderecoCepID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'cep',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Ceps") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/cep_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function CnaeDesktop(usuario, perfil) {
    var rotina = menus[menuTabelaAuxiliarID][2][rotinaTabelaCnaeID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'cnae',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("CNAEs") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/cnae_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#op_tabela_cnae').addClass('active');
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function TecnicoDesktop(usuario, perfil) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroTecnicoID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'tecnico',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Técnicos") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/tecnico_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#op_cadastro_tecnico').addClass('active');
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();

                PesquisarTecnico();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function EstabelecimentoDesktop(usuario, perfil) {
    var rotina = menus[menuCadastroID][2][rotinaCadastroEstabelecimentoID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'estabelecimento',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Estabelecimentos") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/estabelecimento_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#op_cadastro_estabelecimento').addClass('active');
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function LicencaFuncionamentoDesktop(usuario, perfil) {
    var rotina = menus[menuEmissaoDocumentoID][2][rotinaEmissaoLicencaID][0].substr(0, 7);
    getPermissaoPerfil(usuario, perfil, rotina, tipoAcesso01, function(){
        var params = {
            'ac'    : 'licenca_funcionamento',
            'token' : getTokenId()
        };

        var desktop = document.getElementById("desktop"); 
        if ( desktop.innerHTML.indexOf("Licenças de Funcionamento") !== -1 ) {
            exit;
        }
        else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
            MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
            exit;
        }

        // Iniciamos o Ajax 
        $.ajax({
            // Definimos a url
            url : 'pages/licenca_funcionamento_view.php',
            // Definimos o tipo de requisição
            type: 'post',
            // Definimos o tipo de retorno
            dataType : 'html',
            // Dolocamos os valores a serem enviados
            data: params,
            // Antes de enviar ele alerta para esperar
            beforeSend : function(){
                loadingGif(true);
                document.body.style.cursor = "auto";
            },
            // Colocamos o retorno na tela
            success : function(data){
                $('#op_emitir_licenca').addClass('active');
                loadingGif(false);
                $('#desktop').html(data);
                document.body.style.cursor = "auto";
                $(".select2").select2();
            },
            error: function (request, status, error) {
                loadingGif(false);
                document.body.style.cursor = "auto";
                $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
            }
        });  
        // Finalizamos o Ajax
    });
}

function PerfilDesktop() {
    var params = {
        'ac'    : 'perfil',
        'token' : getTokenId()
    };
  
    var desktop = document.getElementById("desktop"); 
    if ( desktop.innerHTML.indexOf("Perfis") !== -1 ) {
        exit;
    }
    else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
        MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
        exit;
    }
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/perfil_view.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            loadingGif(true);
            document.body.style.cursor = "auto";
        },
        // Colocamos o retorno na tela
        success : function(data){
            loadingGif(false);
            $('#desktop').html(data);
            document.body.style.cursor = "auto";
            $(".select2").select2();
            
            PesquisarPerfil();
        },
        error: function (request, status, error) {
            loadingGif(false);
            document.body.style.cursor = "auto";
            $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function UsuarioDesktop() {
    var params = {
        'ac'    : 'usuario',
        'token' : getTokenId()
    };
  
    var desktop = document.getElementById("desktop"); 
    if ( desktop.innerHTML.indexOf("Usuários") !== -1 ) {
        exit;
    }
    else if ( desktop.innerHTML.indexOf("Home") !== -1 ) {
        MensagemAlerta("Informe", "Existe controle aberto!<br>Favor fechá-lo para que outra rotina seja executada.");
        exit;
    }
    
    // Iniciamos o Ajax 
    $.ajax({
        // Definimos a url
        url : 'pages/usuario_view.php',
        // Definimos o tipo de requisição
        type: 'post',
        // Definimos o tipo de retorno
        dataType : 'html',
        // Dolocamos os valores a serem enviados
        data: params,
        // Antes de enviar ele alerta para esperar
        beforeSend : function(){
            loadingGif(true);
            document.body.style.cursor = "auto";
        },
        // Colocamos o retorno na tela
        success : function(data){
            loadingGif(false);
            $('#desktop').html(data);
            document.body.style.cursor = "auto";
            $(".select2").select2();
            
            PesquisarUsuario();
        },
        error: function (request, status, error) {
            loadingGif(false);
            document.body.style.cursor = "auto";
            $('#desktop').html("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
        }
    });  
    // Finalizamos o Ajax
}

function loadingGif(exibir) {
    if (exibir === true) {
        document.getElementById('painel_loading').style.display='block';
    } else {
        document.getElementById('painel_loading').style.display='none';
    }
}

function AlterarSenha() {
    $('#pw_senha_atual').val("");
    $('#pw_senha_nova').val("");
    $('#pw_senha_confirmar').val("");
    $('#painel_alterar_senha_retorno').html("");

    document.getElementById("painel_alterar_senha").style.paddingTop = "100px";
    document.getElementById('painel_alterar_senha').style.display    = 'block';
    $('#pw_senha_atual').focus();
}

function SalvarSenha() {
    var str_mensagem = "";
    var str_marcador = "<i class='fa fa-angle-double-right'></i>&nbsp;&nbsp;";
    if ( $('#pw_senha_atual').val().trim() === "" )     str_mensagem += str_marcador + "Senha atual<br>";
    if ( $('#pw_senha_nova').val().trim() === "" )      str_mensagem += str_marcador + "Nova Senha<br>";
    if ( $('#pw_senha_confirmar').val().trim() === "" ) str_mensagem += str_marcador + "Confirmação da Nova Senha";
    
    if ( str_mensagem.trim() !== "" ) {
        MensagemAlerta("Campos Requeridos", "<strong>É obrigatório o preenchimento(s) do(s) campo(s) abaixo:</strong><br>" + str_mensagem);
    } else {
        var str_senha_atual = $('#pw_senha_atual').val().trim();
        var str_senha_nova  = $('#pw_senha_nova').val().trim();
        var str_senha_conf  = $('#pw_senha_confirmar').val().trim();
        var tam_senha_nova  = str_senha_nova.length;
        
        if (str_senha_atual === str_senha_nova) {
            MensagemAlerta("Alerta !", "A <strong>Nova Senha</strong> não poderá ser idêntica à <strong>senha atual</strong>.");
        } 
        else if (tam_senha_nova < 6) {
            MensagemAlerta("Alerta !", "A <strong>Nova Senha</strong> não pode possuir quantidade menor que 6 caracteres.");
        } 
        else  if (str_senha_nova !== str_senha_conf) {
            MensagemAlerta("Alerta !", "A <strong>Nova Senha</strong> difere de sua <strong>confirmação</strong>.");
        } else {
            var params = {
                'ac'    : 'decript',
                'token' : getTokenId(),
                'values': getPwUsuario()
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
                    $('#painel_alterar_senha_retorno').html("<strong>Validando senha... favor aguardar!</strong>");
                    document.body.style.cursor = "auto";
                },
                // Colocamos o retorno na tela
                success : function(data){
                    if ( str_senha_atual !== data ) {
                        $('#painel_alterar_senha_retorno').html("");
                        MensagemAlerta("Alerta !", "Senha atual inválida.");
                    } else {
                        $('#painel_alterar_senha_retorno').html("");
                        var fields = {
                            'ac'    : 'alterar_senha',
                            'token' : getTokenId(),
                            'id_usuario' : getIdUsuario(),
                            'pw_usuario' : str_senha_nova
                        };

                        $.ajax({
                            // Definimos a url
                            url : 'pages/usuario_dao.php',
                            // Definimos o tipo de requisição
                            type: 'post',
                            // Definimos o tipo de retorno
                            dataType : 'html',
                            // Dolocamos os valores a serem enviados
                            data: fields,
                            // Antes de enviar ele alerta para esperar
                            beforeSend : function(){
                                $('#painel_alterar_senha_retorno').html("<strong>Salvando nova senha... favor aguardar!</strong>");
                                document.body.style.cursor = "auto";
                            },
                            // Colocamos o retorno na tela
                            success : function(data){
                                if ( data === "OK" ) {
                                    $('#btn_alterar_senha_fechar').trigger('click');
                                    MensagemInforme("Informe", "Nova Senha gravada com sucesso.<br><strong>Favor efetuar login em uma nova sessão com a nova senha!</strong>");
                                    //window.open("controle.php?ac=sair");
                                } else {
                                    MensagemErro("Erro", data);
                                }
                            },
                            error: function (request, status, error) {
                                $('#painel_alterar_senha_retorno').html("<font color='red'>Erro ao tentar gravar a nova senha!</font>");
                            }
                        });  
                    }
                },
                error: function (request, status, error) {
                    MensagemErro("Erro (" + status + ")", request.responseText + "<br><strong>Error : </strong>" + error.toString());
                }
            });  
            // Finalizamos o Ajax
        }
    }
}
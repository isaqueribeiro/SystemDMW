/* 
 * Função para criar um objeto XMLHTTPRequest
 */

var home_host    = "http://localhost:8081/dmWeb/";
var sistema      = "0";
var sistema_nome = "";

var token_id        = "";
var lg_usuario_tmp  = "";
var id_usuario_tmp  = "{00000000-0000-0000-0000-000000000000}";
var pw_usuario_tmp  = "";
var pf_usuario_tmp  = 0;
var qt_procedimento = 0;
var ar_procedimento = new Array();

function getHomeHost() {
    return home_host; 
}

function getSistema() {
    return sistema; 
}

function getSistemaNome() {
    return sistema_nome; 
}

function getTokenId() {
    return token_id; 
}

function getUrlPermissaoAcesso() {
    return "logs/perfil_permissao_" + token_id + ".json";      
}

function getPwUsuario() {
    return pw_usuario_tmp; 
}

function getLgUsuario() {
    return lg_usuario_tmp; 
}

function getIdUsuario() {
    return id_usuario_tmp; 
}

function getPfUsuario() {
    return pf_usuario_tmp; 
}

function getGuidEmpty(){
    return "{00000000-0000-0000-0000-000000000000}";
}

function setSistema(value) {
    sistema = value; 
}

function setSistemaNome(value) {
    sistema_nome = value; 
}

function setTokenId(value) {
    token_id = value;
}

function setPwUsuario(value) {
    pw_usuario_tmp = value;
}

function setLgUsuario(value) {
    lg_usuario_tmp = value;
}

function setPfUsuario(value) {
    pf_usuario_tmp = value;
}

function setIdUsuario(value) {
    id_usuario_tmp = value;
}

function getQtdeProcedimento() {
    return qt_procedimento;
}

function setQtdeProcedimento(value) {
    qt_procedimento = value;
}

function getArrayProcedimento(indice) {
    return ar_procedimento[indice];
}

function setArrayProcedimento(indice, value) {
    if (indice < ar_procedimento.length) ar_procedimento[indice] = value;
}

function CriaRequest() {
    try{ 
        request = new XMLHttpRequest();        
    }catch (IEAtual){
        try{
            request = new ActiveXObject("Msxml2.XMLHTTP");       
        }catch(IEAntigo){
            try{
                request = new ActiveXObject("Microsoft.XMLHTTP");          
            }catch(falha){
                request = false;
            }
        }
    }

    if (!request) 
        alert("Seu navegador não suporta Ajax!");
    else
        return request;
}
 
function strToInt(value) {
    var str = "0" + value;
    return parseInt(str);
} 

function formatar(mascara, documento){
    var i = documento.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(i);

    if ( texto.substring(0, 1) !== saida ){
        documento.value += texto.substring(0, 1);
    }
//    <td height="24">Hora:</td>
//    <td><input type="text" name="hora" maxlength="5" OnKeyPress="formatar('##:##', this)" ></td>
}

function zeroEsquerda(valor, zeros) {
    var foo = "";
    var tam = zeros - valor.length;
    
    while (foo.length < tam) {
        foo = "0" + foo;
    }
    
    var str = foo.concat(valor.toString());
    
    return str;
} 

function validarData(data) { 
    // DD/MM/AAAA
    // 0123456789
    // 1234567890
    var dia = data.substring(0,2);
    var mes = data.substring(3,5);
    var ano = data.substring(6,10);
 
    //Criando um objeto Date usando os valores ano, mes e dia.
    var novaData = new Date(ano, (mes-1), dia); 
    
    var mesmoDia = parseInt(dia, 10) === parseInt(novaData.getDate());
    var mesmoMes = parseInt(mes, 10) === parseInt(novaData.getMonth()) + 1;
    var mesmoAno = parseInt(ano) === parseInt(novaData.getFullYear());
 
    if (!((mesmoDia) && (mesmoMes) && (mesmoAno))) {
        return false;
    } else {  
        return true;
    }
}

function validarHora(hora) { 
    // HH:MM
    // 01234
    // 12345
    var hr = hora.substring(0,2);
    var mm = hora.substring(3,5);
 
    if ( (parseInt(hr, 10) < 0) || (parseInt(hr, 10) > 23) || (parseInt(mm, 10) < 0) || (parseInt(mm, 10) > 59) )  {
        return false;
    } else {  
        return true;
    }
}

function validarDataCampo (campo, valor) {
    var date   = valor;
    var ardt   = new Array;
    var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    ardt = date.split("/");
    erro = false;

    if ( date.search(ExpReg) === -1){
        erro = true;
    }
    else if (((ardt[1] === 4)||(ardt[1] === 6)||(ardt[1] === 9)||(ardt[1] === 11)) && (ardt[0] > 30))
        erro = true;
    else if ( ardt[1] === 2) {
        if ((ardt[0] > 28) && ((ardt[2]%4) !== 0))
            erro = true;
        if ((ardt[0] > 29) && ((ardt[2]%4) === 0))
            erro = true;
    }
    if (erro) {
            alert("O valor " + valor + " não é uma data válida!!!");
            campo.focus();
            campo.value = "";
            return false;
    }
    
    return true;
}

function getMousePos(canvas, evt){
    // get canvas position
    var obj  = canvas;
    var top  = 0;
    var left = 0;
    while (obj.tagName != 'BODY') {
        top  += obj.offsetTop;
        left += obj.offsetLeft;
        obj   = obj.offsetParent;
    }
  
    // return relative mouse position
    var mouseX = evt.clientX - left + window.pageXOffset;
    var mouseY = evt.clientY - top + window.pageYOffset;
    return {
        x: mouseX,
        y: mouseY
    };
}

function SomenteNumero(e){
    var tecla = (window.event)?event.keyCode:e.which;
    if( (tecla > 47 && tecla < 58) ) return true;
    else {
        if ( tecla === 8 || tecla === 0) return true;
        else  return false;
    }
}

function TextoMaiusculo(o) {
    return o.value.toUpperCase();
}

function TextoMinusculo(o) {
    return o.value.toLowerCase();
}

function decript(values) {
    var params = {
        'ac'    : 'decript',
        'token' : getTokenId(),
        'values': values
    };
    
    var str = "";
    
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
            document.body.style.cursor = "auto";
        },
        // Colocamos o retorno na tela
        success : function(data){
            str = data;
        },
        error: function (request, status, error) {
            str = "ERROR";
        }
    });  
    // Finalizamos o Ajax
    
    return str;
}

// Escopo da Declaração de Funções jQuery
(function($) {
  RemoveTableRow = function(handler) {
    var tr = $(handler).closest('tr');

    tr.fadeOut(400, function(){ 
      tr.remove(); 
    }); 

    return false;
  };
})(jQuery);

/*
function getMeioTela(){
    var heightDocument = $(document).height();
    var heightWindows  = $(window).height();
    var retorno = 0;
    
    if (heightDocument > heightWindows) {
        retorno = (int)(heightDocument / 2);
    } else {
        retorno = (int)(heightWindows / 2);
    }
    return retorno;
}
*/
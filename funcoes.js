/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function calc_digitos_posicoes( digitos, posicoes, soma_digitos) {
    // Garante que o valor é uma string
    digitos = digitos.toString();
    // Faz a soma dos dígitos com a posição
    // Ex. para 10 posições:
    //   0    2    5    4    6    2    8    8   4
    // x10   x9   x8   x7   x6   x5   x4   x3  x2
    //   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
    for ( var i = 0; i < digitos.length; i++  ) {
        // Preenche a soma com o dígito vezes a posição
        soma_digitos = soma_digitos + ( digitos[i] * posicoes );
        // Subtrai 1 da posição
        posicoes--;
        // Parte específica para CNPJ
        // Ex.: 5-4-3-2-9-8-7-6-5-4-3-2
        if ( posicoes < 2 ) {
            // Retorno a posição para 9
            posicoes = 9;
        }
    }
    // Captura o resto da divisão entre soma_digitos dividido por 11
    // Ex.: 196 % 11 = 9
    soma_digitos = soma_digitos % 11;
    // Verifica se soma_digitos é menor que 2
    if ( soma_digitos < 2 ) {
        // soma_digitos agora será zero
        soma_digitos = 0;
    } else {
        // Se for maior que 2, o resultado é 11 menos soma_digitos
        // Ex.: 11 - 9 = 2
        // Nosso dígito procurado é 2
        soma_digitos = 11 - soma_digitos;
    }
    // Concatena mais um dígito aos primeiro nove dígitos
    // Ex.: 025462884 + 2 = 0254628842
    var cpf = digitos + soma_digitos;
    // Retorna
    return cpf;
} 

function valida_cpf( valor ) {
    // Garante que o valor é uma string
    valor = valor.toString();
    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');
    // Captura os 9 primeiros dígitos do CPF
    // Ex.: 02546288423 = 025462884
    var digitos = valor.substr(0, 9);
    // Faz o cálculo dos 9 primeiros dígitos do CPF para obter o primeiro dígito
    var novo_cpf = calc_digitos_posicoes( digitos, 10, 0 );
    // Faz o cálculo dos 10 dígitos do CPF para obter o último dígito
    var novo_cpf = calc_digitos_posicoes( novo_cpf, 11, 0 );
    // Verifica se o novo CPF gerado é idêntico ao CPF enviado
    if ( novo_cpf === valor ) {
        // CPF válido
        return true;
    } else {
        // CPF inválido
        return false;
    }
}

function valida_cnpj ( valor ) {
    // Garante que o valor é uma string
    valor = valor.toString();
    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');
    // O valor original
    var cnpj_original = valor;
    // Captura os primeiros 12 números do CNPJ
    var primeiros_numeros_cnpj = valor.substr( 0, 12 );
    // Faz o primeiro cálculo
    var primeiro_calculo = calc_digitos_posicoes( primeiros_numeros_cnpj, 5, 0 );
    // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
    var segundo_calculo = calc_digitos_posicoes( primeiro_calculo, 6, 0 );
    // Concatena o segundo dígito ao CNPJ
    var cnpj = segundo_calculo;
    // Verifica se o CNPJ gerado é idêntico ao enviado
    if ( cnpj === cnpj_original ) {
        return true;
    }
    // Retorna falso por padrão
    return false;
}

function valida_cpf_cnpj ( valor ) {
    // Verifica se é CPF ou CNPJ
    var valida = verifica_cpf_cnpj( valor );
    // Garante que o valor é uma string
    valor = valor.toString();
    // Remove caracteres inválidos do valor
    valor = valor.replace(/[^0-9]/g, '');
    // Valida CPF
    if ( valida === 'CPF' ) {
        // Retorna true para cpf válido
        return valida_cpf( valor );
    } 
    // Valida CNPJ
    else if ( valida === 'CNPJ' ) {
        // Retorna true para CNPJ válido
        return valida_cnpj( valor );
    } 
    // Não retorna nada
    else {
        return false;
    }
}

function validarCPF(cpf) {  
    cpf = cpf.replace(/[^\d]+/g, '');    

    var add = 0;
    var rev = 0;
    
    if(cpf === '') return false; 

    // Elimina CPFs invalidos conhecidos    
    if (cpf.length !== 11 || 
        cpf === "00000000000" || 
        cpf === "11111111111" || 
        cpf === "22222222222" || 
        cpf === "33333333333" || 
        cpf === "44444444444" || 
        cpf === "55555555555" || 
        cpf === "66666666666" || 
        cpf === "77777777777" || 
        cpf === "88888888888" || 
        cpf === "99999999999")
            return false;      
 
    // Valida 1o digito 
    add = 0;    
    for (i = 0; i < 9; i ++)       
        add += parseInt(cpf.charAt(i)) * (10 - i);  
        rev = 11 - (add % 11);  
        if (rev === 10 || rev === 11)     
            rev = 0;    
        if (rev !== parseInt(cpf.charAt(9)))     
            return false; 
      
    // Valida 2o digito 
    add = 0;    
    for (i = 0; i < 10; i ++)        
        add += parseInt(cpf.charAt(i)) * (11 - i);  
    rev = 11 - (add % 11);  
    if (rev === 10 || rev === 11) 
        rev = 0;    
    if (rev !== parseInt(cpf.charAt(10)))
        return false;       
    return true;   
}

function validarCNPJ(cnpj) {
    return valida_cnpj(cnpj);
//    cnpj = cnpj.replace(/[^\d]+/g, '');
//    
//    var tamanho   = 0;
//    var numeros   = 0;
//    var digitos   = 0;
//    var soma      = 0;
//    var resultado = 0;
//    var pos = 0;
//    
//    if(cnpj === '') return false;
//     
//    if (cnpj.length !== 14)
//        return false;
// 
//    // Elimina CNPJs invalidos conhecidos
//    if (cnpj === "00000000000000" || 
//        cnpj === "11111111111111" || 
//        cnpj === "22222222222222" || 
//        cnpj === "33333333333333" || 
//        cnpj === "44444444444444" || 
//        cnpj === "55555555555555" || 
//        cnpj === "66666666666666" || 
//        cnpj === "77777777777777" || 
//        cnpj === "88888888888888" || 
//        cnpj === "99999999999999")
//        return false;
//         
//    // Valida DVs
//    tamanho = cnpj.length - 2;
//    numeros = cnpj.substring(0,tamanho);
//    digitos = cnpj.substring(tamanho);
//    soma = 0;
//    pos = tamanho - 7;
//    for (i = tamanho; i >= 1; i--) {
//      soma += numeros.charAt(tamanho - i) * pos--;
//      if (pos < 2)
//            pos = 9;
//    }
//    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
//    if (resultado !== digitos.charAt(0))
//        return false;
//         
//    tamanho = tamanho + 1;
//    numeros = cnpj.substring(0,tamanho);
//    soma = 0;
//    pos = tamanho - 7;
//    for (i = tamanho; i >= 1; i--) {
//      soma += numeros.charAt(tamanho - i) * pos--;
//      if (pos < 2)
//            pos = 9;
//    }
//    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
//    if (resultado !== digitos.charAt(1))
//          return false;
//           
//    return true;    
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

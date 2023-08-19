


/*------ SYSDBA 06/01/2017 09:54:20 --------*/

ALTER TABLE SYS_CONFIGURACAO
    ADD DS_ASSINATURA_LICENCA_1 DMN_VCHAR_50,
    ADD DS_ASSINATURA_LICENCA_2 DMN_VCHAR_50,
    ADD DS_ASSINATURA_LICENCA_3 DMN_VCHAR_50;

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_ASSINATURA_LICENCA_1 IS
'Descricao para documentos - Identificacao funcional da 1a. assinatuta';

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_ASSINATURA_LICENCA_2 IS
'Descricao para documentos - Identificacao funcional da 2a. assinatuta';

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_ASSINATURA_LICENCA_3 IS
'Descricao para documentos - Identificacao funcional da 3a. assinatuta';


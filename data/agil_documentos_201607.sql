


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column ID_LICENCA position 1;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column NR_EXERCICIO position 2;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column NR_LICENCA position 3;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column NR_PROCESSO position 4;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column DT_EMISSAO position 5;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column DT_VALIDADE position 6;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column ID_ESTABELECIMENTO position 7;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column CD_CATEGORIA position 8;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column CD_ATIVIDADE position 9;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column DS_AUTENTICACAO position 10;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column TP_SITUACAO position 11;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column CD_RESPONSAVEL position 12;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column DS_OBSERVACAO position 13;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column US_CADASTRO position 14;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column DH_CADASTRO position 15;


/*------ SYSDBA 01/07/2016 17:59:53 --------*/

alter table TBLICENCA_FUNCIONAMENTO
alter column HASH_CADASTRO position 16;




/*------ SYSDBA 01/07/2016 18:43:55 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Token unknown - line 1, column 56.
Union.

*/

/*------ SYSDBA 01/07/2016 18:44:23 --------*/

/*!!! Error occured !!!
Invalid token.
Dynamic SQL Error.
SQL error code = -104.
Token unknown - line 2, column 53.
Union.

*/

/*------ SYSDBA 05/07/2016 17:43:30 --------*/

/*!!! Error occured !!!
Column does not belong to referenced table.
Dynamic SQL Error.
SQL error code = -206.
Column unknown.
L.
At line 1, column 170.

*/


/*------ SYSDBA 06/07/2016 13:21:18 --------*/

ALTER TABLE TBLICENCA_FUNCIONAMENTO
    ADD DT_APROVACAO DMN_DATE;

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.DT_APROVACAO IS
'Data de Aprovacao da Licenca';

alter table TBLICENCA_FUNCIONAMENTO
alter ID_LICENCA position 1;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_EXERCICIO position 2;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_LICENCA position 3;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_PROCESSO position 4;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_EMISSAO position 5;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_VALIDADE position 6;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_APROVACAO position 7;

alter table TBLICENCA_FUNCIONAMENTO
alter ID_ESTABELECIMENTO position 8;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_CATEGORIA position 9;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_ATIVIDADE position 10;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_AUTENTICACAO position 11;

alter table TBLICENCA_FUNCIONAMENTO
alter TP_SITUACAO position 12;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_RESPONSAVEL position 13;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_OBSERVACAO position 14;

alter table TBLICENCA_FUNCIONAMENTO
alter US_CADASTRO position 15;

alter table TBLICENCA_FUNCIONAMENTO
alter DH_CADASTRO position 16;

alter table TBLICENCA_FUNCIONAMENTO
alter HASH_CADASTRO position 17;




/*------ SYSDBA 06/07/2016 17:07:10 --------*/

CREATE DOMAIN DMN_VCHAR_15 AS
VARCHAR(15);CREATE DOMAIN DMN_VCHAR_15_NN AS
VARCHAR(15)
NOT NULL;


/*------ SYSDBA 06/07/2016 17:07:22 --------*/

update RDB$RELATION_FIELDS set
RDB$FIELD_SOURCE = 'DMN_VCHAR_15'
where (RDB$FIELD_NAME = 'NR_PROCESSO') and
(RDB$RELATION_NAME = 'TBLICENCA_FUNCIONAMENTO')
;




/*------ SYSDBA 08/07/2016 20:19:27 --------*/

ALTER TABLE SYS_CONFIGURACAO
    ADD DS_GOVERNO DMN_VCHAR_100,
    ADD DS_SISTEMA_GOVERNO DMN_VCHAR_100,
    ADD DS_SECRETARIA DMN_VCHAR_100;

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_GOVERNO IS
'Descricao para documentos - Nome da Entidade governamental';

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_SISTEMA_GOVERNO IS
'Descricao para documentos - Nome do sistema de atendimento';

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_SECRETARIA IS
'Descricao para documentos - Nome da Secretaria do Governo';




/*------ SYSDBA 08/07/2016 20:20:09 --------*/

COMMENT ON TABLE SYS_CONFIGURACAO IS 'Tabela de Configuracoes.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   15/06/2016

Tabela responsavel por armazenar os dados padroes que definem o comportamento
de determinadas areas do sistema.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    08/07/2016 - IMR :
        + Novos campo DS_GOVERNO,

    15/06/2016 - IMR :
        * Documentacao da tabela.';




/*------ SYSDBA 08/07/2016 20:20:27 --------*/

COMMENT ON TABLE SYS_CONFIGURACAO IS 'Tabela de Configuracoes.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   15/06/2016

Tabela responsavel por armazenar os dados padroes que definem o comportamento
de determinadas areas do sistema.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    08/07/2016 - IMR :
        + Novos campo DS_GOVERNO, DS_SISTEMA_GOVERNO e

    15/06/2016 - IMR :
        * Documentacao da tabela.';




/*------ SYSDBA 08/07/2016 20:21:10 --------*/

COMMENT ON TABLE SYS_CONFIGURACAO IS 'Tabela de Configuracoes.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   15/06/2016

Tabela responsavel por armazenar os dados padroes que definem o comportamento
de determinadas areas do sistema.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    08/07/2016 - IMR :
        + Novos campo DS_GOVERNO, DS_SISTEMA_GOVERNO e DS_SECRETARIA para armazenar
          descricoes informativas que serao utilizadas na impressao de determinados
          documentos.

    15/06/2016 - IMR :
        * Documentacao da tabela.';




/*------ SYSDBA 08/07/2016 20:21:32 --------*/

ALTER TABLE SYS_CONFIGURACAO
    ADD DS_COORDENACAO DMN_VCHAR_100;




/*------ SYSDBA 08/07/2016 20:21:53 --------*/

COMMENT ON TABLE SYS_CONFIGURACAO IS 'Tabela de Configuracoes.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   15/06/2016

Tabela responsavel por armazenar os dados padroes que definem o comportamento
de determinadas areas do sistema.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    08/07/2016 - IMR :
        + Novos campo DS_GOVERNO, DS_SISTEMA_GOVERNO, DS_SECRETARIA e DS_COORDENACAO
          para armazenar descricoes informativas que serao utilizadas na impressao
          de determinados documentos.

    15/06/2016 - IMR :
        * Documentacao da tabela.';




/*------ SYSDBA 08/07/2016 20:22:08 --------*/

COMMENT ON COLUMN SYS_CONFIGURACAO.DS_COORDENACAO IS
'Descricao para documentos - Nome da Coordenacao';



/*------ SYSDBA 11/07/2016 11:27:41 --------*/

Update SYS_CONFIGURACAO c Set
    c.ds_governo  = 'GOVERNO MUNICIPAL DE CASTANHAL'
  , c.ds_sistema_governo = 'SISTEMA ÚNICO DE SAÚDE'
  , c.ds_secretaria  = 'Secretaria Municipal de Saúde'
  , c.ds_coordenacao = 'COORDENADORIA DE VIGILÂNCIA SANITÁRIA'
where c.cd_configuracao = 1;

/*------ SYSDBA 11/07/2016 11:27:44 --------*/

COMMIT WORK;

/*------ SYSDBA 11/07/2016 11:31:08 --------*/

Update SYS_CONFIGURACAO c Set
    c.ds_governo         = 'GOVERNO MUNICIPAL DE CASTANHAL'
  , c.ds_sistema_governo = 'SISTEMA ÚNICO DE SAÚDE'
  , c.ds_secretaria  = 'Secretaria Municipal de Saúde'
  , c.ds_coordenacao = 'COORDENADORIA DE VIGILÂNCIA SANITÁRIA'
where c.cd_configuracao = 1;

/*------ SYSDBA 11/07/2016 11:31:10 --------*/

COMMIT WORK;


/*------ SYSDBA 11/07/2016 16:03:38 --------*/

CREATE INDEX IDX_TBLICENCA_FUNCIONAMENTO_VAL
ON TBLICENCA_FUNCIONAMENTO (DT_VALIDADE);



/*------ SYSDBA 11/07/2016 19:30:32 --------*/

/*!!! Error occured !!!
Unsuccessful execution caused by a system error that precludes successful execution of subsequent statements.
Dynamic SQL Error.
expression evaluation not supported.
Strings cannot be added or subtracted in dialect 3.

*/

/*------ SYSDBA 11/07/2016 19:30:33 --------*/

/*!!! Error occured !!!
Unsuccessful execution caused by a system error that precludes successful execution of subsequent statements.
Dynamic SQL Error.
expression evaluation not supported.
Strings cannot be added or subtracted in dialect 3.

*/


/*------ SYSDBA 20/07/2016 12:39:51 --------*/

ALTER TABLE TBLICENCA_FUNCIONAMENTO
    ADD CD_ATIVIDADE_SECUNDARIA DMN_VCHAR_10;

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.CD_ATIVIDADE_SECUNDARIA IS
'Atividade Secundaria';

alter table TBLICENCA_FUNCIONAMENTO
alter ID_LICENCA position 1;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_EXERCICIO position 2;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_LICENCA position 3;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_PROCESSO position 4;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_EMISSAO position 5;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_VALIDADE position 6;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_APROVACAO position 7;

alter table TBLICENCA_FUNCIONAMENTO
alter ID_ESTABELECIMENTO position 8;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_CATEGORIA position 9;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_ATIVIDADE position 10;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_ATIVIDADE_SECUNDARIA position 11;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_AUTENTICACAO position 12;

alter table TBLICENCA_FUNCIONAMENTO
alter TP_SITUACAO position 13;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_RESPONSAVEL position 14;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_OBSERVACAO position 15;

alter table TBLICENCA_FUNCIONAMENTO
alter US_CADASTRO position 16;

alter table TBLICENCA_FUNCIONAMENTO
alter DH_CADASTRO position 17;

alter table TBLICENCA_FUNCIONAMENTO
alter HASH_CADASTRO position 18;




/*------ SYSDBA 20/07/2016 12:40:14 --------*/

ALTER TABLE TBLICENCA_FUNCIONAMENTO
ADD CONSTRAINT FK_TBLICENCA_FUNCIONAMENTO_ATV2
FOREIGN KEY (CD_ATIVIDADE_SECUNDARIA)
REFERENCES TBCNAE(CD_CNAE);




/*------ SYSDBA 20/07/2016 13:41:30 --------*/

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.CD_RESPONSAVEL IS
'Responsavel Tecnico (Vigilancia Sanitaria)';




/*------ SYSDBA 20/07/2016 14:02:47 --------*/

CREATE DOMAIN DMN_VCHAR_07 AS
VARCHAR(7);CREATE DOMAIN DMN_VCHAR_07_NN AS
VARCHAR(7)
NOT NULL;


/*------ SYSDBA 20/07/2016 14:33:57 --------*/

ALTER TABLE TBLICENCA_FUNCIONAMENTO
    ADD NM_RESPONSAVEL_ESTABELECIMENTO DMN_NOME,
    ADD NR_RESPONSAVEL_ESTABELECIMENTO DMN_VCHAR_10,
    ADD CN_RESPONSAVEL_ESTABELECIMENTO DMN_VCHAR_07;

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.NM_RESPONSAVEL_ESTABELECIMENTO IS
'Resposanvel do Estabelecimento - Nome Completo';

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.NR_RESPONSAVEL_ESTABELECIMENTO IS
'Resposanvel do Estabelecimento - Numero no Conselho';

COMMENT ON COLUMN TBLICENCA_FUNCIONAMENTO.CN_RESPONSAVEL_ESTABELECIMENTO IS
'Resposanvel do Estabelecimento - Conselho';

alter table TBLICENCA_FUNCIONAMENTO
alter ID_LICENCA position 1;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_EXERCICIO position 2;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_LICENCA position 3;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_PROCESSO position 4;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_EMISSAO position 5;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_VALIDADE position 6;

alter table TBLICENCA_FUNCIONAMENTO
alter DT_APROVACAO position 7;

alter table TBLICENCA_FUNCIONAMENTO
alter ID_ESTABELECIMENTO position 8;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_CATEGORIA position 9;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_ATIVIDADE position 10;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_ATIVIDADE_SECUNDARIA position 11;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_AUTENTICACAO position 12;

alter table TBLICENCA_FUNCIONAMENTO
alter TP_SITUACAO position 13;

alter table TBLICENCA_FUNCIONAMENTO
alter CD_RESPONSAVEL position 14;

alter table TBLICENCA_FUNCIONAMENTO
alter NM_RESPONSAVEL_ESTABELECIMENTO position 15;

alter table TBLICENCA_FUNCIONAMENTO
alter NR_RESPONSAVEL_ESTABELECIMENTO position 16;

alter table TBLICENCA_FUNCIONAMENTO
alter CN_RESPONSAVEL_ESTABELECIMENTO position 17;

alter table TBLICENCA_FUNCIONAMENTO
alter DS_OBSERVACAO position 18;

alter table TBLICENCA_FUNCIONAMENTO
alter US_CADASTRO position 19;

alter table TBLICENCA_FUNCIONAMENTO
alter DH_CADASTRO position 20;

alter table TBLICENCA_FUNCIONAMENTO
alter HASH_CADASTRO position 21;




/*------ SYSDBA 20/07/2016 15:07:53 --------*/

ALTER TABLE TBESTABELECIMENTO
    ADD CD_CNAE_SECUNDARIA DMN_VCHAR_10;

COMMENT ON COLUMN TBESTABELECIMENTO.CD_CNAE_SECUNDARIA IS
'Atividade Secundaria
CNAE - Codigo Nacional de Atividade Empresarial';

alter table TBESTABELECIMENTO
alter ID_ESTABELECIMENTO position 1;

alter table TBESTABELECIMENTO
alter TP_PESSOA position 2;

alter table TBESTABELECIMENTO
alter NM_RAZAO position 3;

alter table TBESTABELECIMENTO
alter NM_FANTASIA position 4;

alter table TBESTABELECIMENTO
alter NR_CNPJ position 5;

alter table TBESTABELECIMENTO
alter NR_INSC_EST position 6;

alter table TBESTABELECIMENTO
alter NR_INSC_MUN position 7;

alter table TBESTABELECIMENTO
alter CD_CNAE_PRINCIPAL position 8;

alter table TBESTABELECIMENTO
alter CD_CNAE_SECUNDARIA position 9;

alter table TBESTABELECIMENTO
alter SN_ATIVO position 10;

alter table TBESTABELECIMENTO
alter TP_ENDERECO position 11;

alter table TBESTABELECIMENTO
alter DS_ENDERECO position 12;

alter table TBESTABELECIMENTO
alter NR_ENDERECO position 13;

alter table TBESTABELECIMENTO
alter DS_COMPLEMENTO position 14;

alter table TBESTABELECIMENTO
alter CD_BAIRRO position 15;

alter table TBESTABELECIMENTO
alter NR_CEP position 16;

alter table TBESTABELECIMENTO
alter CD_CIDADE position 17;

alter table TBESTABELECIMENTO
alter CD_UF position 18;

alter table TBESTABELECIMENTO
alter CD_ESTADO position 19;

alter table TBESTABELECIMENTO
alter DS_EMAIL position 20;

alter table TBESTABELECIMENTO
alter NM_CONTATO position 21;

alter table TBESTABELECIMENTO
alter NR_TELEFONE position 22;

alter table TBESTABELECIMENTO
alter NR_COMERCIAL position 23;

alter table TBESTABELECIMENTO
alter NR_CELULAR position 24;

alter table TBESTABELECIMENTO
alter US_CADASTRO position 25;

alter table TBESTABELECIMENTO
alter DH_CADASTRO position 26;

alter table TBESTABELECIMENTO
alter HASH_CADASTRO position 27;

alter table TBESTABELECIMENTO
alter US_ALTERACAO position 28;

alter table TBESTABELECIMENTO
alter DH_ALTERACAO position 29;

alter table TBESTABELECIMENTO
alter HASH_ALTERACAO position 30;




/*------ SYSDBA 20/07/2016 15:08:12 --------*/

ALTER TABLE TBESTABELECIMENTO
ADD CONSTRAINT FK_TBESTABELECIMENTO_CNAE2
FOREIGN KEY (CD_CNAE_SECUNDARIA)
REFERENCES TBCNAE(CD_CNAE);




/*------ SYSDBA 20/07/2016 16:36:38 --------*/

CREATE SEQUENCE GEN_PROCESSO_2016;




/*------ SYSDBA 20/07/2016 16:36:55 --------*/

COMMENT ON SEQUENCE GEN_PROCESSO_2016 IS 'Sequenciador de processos para 2016';




/*------ SYSDBA 20/07/2016 16:37:41 --------*/

CREATE SEQUENCE GEN_PROCESSO_2017;

COMMENT ON SEQUENCE GEN_PROCESSO_2017 IS 'Sequenciador de processos para 2017';

CREATE SEQUENCE GEN_PROCESSO_2018;

COMMENT ON SEQUENCE GEN_PROCESSO_2018 IS 'Sequenciador de processos para 2018';

CREATE SEQUENCE GEN_PROCESSO_2019;

COMMENT ON SEQUENCE GEN_PROCESSO_2019 IS 'Sequenciador de processos para 2019';

CREATE SEQUENCE GEN_PROCESSO_2020;

COMMENT ON SEQUENCE GEN_PROCESSO_2020 IS 'Sequenciador de processos para 2020';




/*------ SYSDBA 20/07/2016 16:38:31 --------*/

ALTER SEQUENCE GEN_PROCESSO_2016 RESTART WITH 3926;




/*------ SYSDBA 26/07/2016 11:00:46 --------*/

ALTER TABLE TBEVENTO
    ADD ID_TECNICO DMN_GUID;

COMMENT ON COLUMN TBEVENTO.ID_TECNICO IS
'Origem do Evento : Tecnico responsavel';

alter table TBEVENTO
alter ID_EVENTO position 1;

alter table TBEVENTO
alter DT_EVENTO position 2;

alter table TBEVENTO
alter DH_EVENTO position 3;

alter table TBEVENTO
alter DS_EVENTO position 4;

alter table TBEVENTO
alter ID_USUARIO position 5;

alter table TBEVENTO
alter ID_ESTABELECIMENTO position 6;

alter table TBEVENTO
alter ID_LICENCA position 7;

alter table TBEVENTO
alter ID_TECNICO position 8;

alter table TBEVENTO
alter HASH_EVENTO position 9;




/*------ SYSDBA 26/07/2016 11:01:14 --------*/

ALTER TABLE TBEVENTO
ADD CONSTRAINT FK_TBEVENTO_TECNICO
FOREIGN KEY (ID_TECNICO)
REFERENCES TBTECNICO(ID_TECNICO);




/*------ SYSDBA 26/07/2016 11:01:28 --------*/

ALTER TABLE TBEVENTO DROP CONSTRAINT FK_TBEVENTO_TECNICO;




/*------ SYSDBA 26/07/2016 11:02:22 --------*/

ALTER TABLE TBEVENTO
ADD CONSTRAINT FK_TBEVENTO_TECNICO
FOREIGN KEY (ID_TECNICO)
REFERENCES TBTECNICO(ID_TECNICO);




/*------ SYSDBA 12/08/2016 15:07:03 --------*/

SET TERM ^ ;

create or alter procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO integer)
as
declare variable NR_CONTROLE integer;
declare variable UP_GENERADOR varchar(1024);
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /*
    Atualizar Generator
  */
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;
  execute statement :up_generador;
end^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA TO "PUBLIC";



/*------ SYSDBA 12/08/2016 15:09:54 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO integer)
as
declare variable NR_CONTROLE integer;
declare variable UP_GENERADOR varchar(1024);
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /*
    Atualizar Generator
  */
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;
  execute statement :up_generador;
end^

SET TERM ; ^

COMMENT ON PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA IS 'Procedure Atualizar Sequenciador Controle de Licencas de Funcionamento.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   17/06/2016

Procedimento responsavel por identificar octet_length ultimo numero de licenca
(controle) gerado para as Licencas de Funcionamento e atualizar o generator com
o valor encontrado.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    12/08/2016 - IMR :
        * Documentacao da procedure.';




/*------ SYSDBA 12/08/2016 15:13:10 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO integer)
as
declare variable NR_CONTROLE DMN_INTEGER;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /*
    Atualizar Generator
  */
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;
  execute statement :up_generador;
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:13:24 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO DMN_SMALLINT)
as
declare variable NR_CONTROLE DMN_INTEGER;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /*
    Atualizar Generator
  */
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;
  execute statement :up_generador;
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:27:18 --------*/

SET TERM ^ ;

create or alter procedure SP_ATUALIZAR_PROCESSO_LICENCA (
    NR_PROCESSO DMN_VCHAR_15)
as
declare variable POSICAO DMN_INTEGER;
declare variable nr_exercicio dmn_smallint;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  posicao = position('/', :nr_processo);

  if (coalesce(:posicao, 0) > 0) then
  begin
    /* Identificar o exercicio do processo */
    nr_exercicio = substring(:nr_processo from (:posicao + 1) for 4);

    Select
      max(l.nr_processo)
    from TBLICENCA_FUNCIONAMENTO l
    where l.nr_processo like '%/' || :nr_exercicio
    Into
      nr_processo;

    if (coalesce(trim(:nr_processo), '') <> '') then
    begin
      nr_processo = replace(:nr_processo, '/' || :nr_exercicio, '');
      /*
        Atualizar Generator
      */
      up_generador = 'ALTER SEQUENCE GEN_PROCESSO_' || :nr_exercicio || ' RESTART WITH ' || :nr_processo;
      execute statement :up_generador;
    end
  end
end^

SET TERM ; ^

GRANT EXECUTE ON PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA TO "PUBLIC";



/*------ SYSDBA 12/08/2016 15:33:56 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_PROCESSO_LICENCA (
    NR_PROCESSO DMN_VCHAR_15)
as
declare variable POSICAO DMN_INTEGER;
declare variable NR_EXERCICIO DMN_SMALLINT;
declare variable ex_GENERADOR dmn_vchar_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  posicao = position('/', :nr_processo);

  if (coalesce(:posicao, 0) > 0) then
  begin
    /* Identificar o exercicio do processo */
    nr_exercicio = substring(:nr_processo from (:posicao + 1) for 4);

    Select
      max(l.nr_processo)
    from TBLICENCA_FUNCIONAMENTO l
    where l.nr_processo like '%/' || :nr_exercicio
    Into
      nr_processo;

    if (coalesce(trim(:nr_processo), '') <> '') then
    begin
      nr_processo = replace(:nr_processo, '/' || :nr_exercicio, '');

      /* Atualizar Generator */
      se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_PROCESSO_' || :nr_processo || '''';
      up_generador = 'ALTER SEQUENCE GEN_PROCESSO_' || :nr_exercicio || ' RESTART WITH ' || :nr_processo;

      execute statement se_generador
      Into
        ex_generador;

      if ( nullif(:ex_generador, '') is not null ) then
        execute statement :up_generador;
    end
  end
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:35:29 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO DMN_SMALLINT)
as
declare variable NR_CONTROLE DMN_INTEGER;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /* Atualizar Generator */
  se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_PROCESSO_' || :nr_exercicio || '''';
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;

  execute statement se_generador
  Into
    ex_generador;

  if ( nullif(:ex_generador, '') is not null ) then
    execute statement :up_generador;
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:35:37 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_PROCESSO_LICENCA (
    NR_PROCESSO DMN_VCHAR_15)
as
declare variable POSICAO DMN_INTEGER;
declare variable NR_EXERCICIO DMN_SMALLINT;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  posicao = position('/', :nr_processo);

  if (coalesce(:posicao, 0) > 0) then
  begin
    /* Identificar o exercicio do processo */
    nr_exercicio = substring(:nr_processo from (:posicao + 1) for 4);

    Select
      max(l.nr_processo)
    from TBLICENCA_FUNCIONAMENTO l
    where l.nr_processo like '%/' || :nr_exercicio
    Into
      nr_processo;

    if (coalesce(trim(:nr_processo), '') <> '') then
    begin
      nr_processo = replace(:nr_processo, '/' || :nr_exercicio, '');

      /* Atualizar Generator */
      se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_PROCESSO_' || :nr_exercicio || '''';
      up_generador = 'ALTER SEQUENCE GEN_PROCESSO_' || :nr_exercicio || ' RESTART WITH ' || :nr_processo;

      execute statement se_generador
      Into
        ex_generador;

      if ( nullif(:ex_generador, '') is not null ) then
        execute statement :up_generador;
    end
  end
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:35:50 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO DMN_SMALLINT)
as
declare variable NR_CONTROLE DMN_INTEGER;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /* Atualizar Generator */
  se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || '''';
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;

  execute statement se_generador
  Into
    ex_generador;

  if ( nullif(:ex_generador, '') is not null ) then
    execute statement :up_generador;
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:36:50 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_PROCESSO_LICENCA (
    NR_PROCESSO DMN_VCHAR_15)
as
declare variable POSICAO DMN_INTEGER;
declare variable NR_EXERCICIO DMN_SMALLINT;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  posicao = position('/', :nr_processo);

  if (coalesce(:posicao, 0) > 0) then
  begin
    /* Identificar o exercicio do processo */
    nr_exercicio = substring(:nr_processo from (:posicao + 1) for 4);

    Select
      max(l.nr_processo)
    from TBLICENCA_FUNCIONAMENTO l
    where l.nr_processo like '%/' || :nr_exercicio
    Into
      nr_processo;

    if (coalesce(trim(:nr_processo), '') <> '') then
    begin
      nr_processo = replace(:nr_processo, '/' || :nr_exercicio, '');

      /* Atualizar Generator */
      se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_PROCESSO_' || :nr_exercicio || '''';
      up_generador = 'ALTER SEQUENCE GEN_PROCESSO_' || :nr_exercicio || ' RESTART WITH ' || :nr_processo;

      execute statement se_generador
      Into
        ex_generador;

      if ( nullif(:ex_generador, '') is not null ) then
        execute statement :up_generador;
    end
  end
end^

SET TERM ; ^

COMMENT ON PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA IS 'Procedure Atualizar Sequenciador Processo de Licencas de Funcionamento.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   17/06/2016

Procedimento responsavel por identificar o ultimo numero de processo da licenca
gerado para as Licencas de Funcionamento e atualizar o generator com o valor
encontrado.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    12/08/2016 - IMR :
        * Documentacao da procedure.';




/*------ SYSDBA 12/08/2016 15:37:04 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_CONTROLE_LICENCA (
    NR_EXERCICIO DMN_SMALLINT)
as
declare variable NR_CONTROLE DMN_INTEGER;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  /*
    Buscar a ultima Licenca de Funcionamento inserida na base
    de acordo com o exercicio informado.
   */
  Select
    max(l.nr_licenca)
  from TBLICENCA_FUNCIONAMENTO l
  where l.nr_exercicio = coalesce(:nr_exercicio, 0)
  Into
    nr_controle;

  nr_controle = coalesce(:nr_controle, 0);

  /* Atualizar Generator */
  se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || '''';
  up_generador = 'ALTER SEQUENCE GEN_LICENCA_FUNCIONAMENTO_' || :nr_exercicio || ' RESTART WITH ' || :nr_controle;

  execute statement se_generador
  Into
    ex_generador;

  if ( nullif(:ex_generador, '') is not null ) then
    execute statement :up_generador;
end^

SET TERM ; ^

COMMENT ON PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA IS 'Procedure Atualizar Sequenciador Controle de Licencas de Funcionamento.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   17/06/2016

Procedimento responsavel por identificar o ultimo numero de licenca (controle)
gerado para as Licencas de Funcionamento e atualizar o generator com o valor
encontrado.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    12/08/2016 - IMR :
        * Documentacao da procedure.';




/*------ SYSDBA 12/08/2016 15:38:53 --------*/

SET TERM ^ ;

CREATE trigger tg_licenca_funciona_atualiza for tblicenca_funcionamento
active after delete position 0
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_exercicio);
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:39:09 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_licenca_funciona_atualiza for tblicenca_funcionamento
active after delete position 10
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_exercicio);
end^

SET TERM ; ^




/*------ SYSDBA 12/08/2016 15:40:51 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_licenca_funciona_atualiza for tblicenca_funcionamento
active after delete position 10
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_exercicio);
end^

SET TERM ; ^

COMMENT ON TRIGGER TG_LICENCA_FUNCIONA_ATUALIZA IS 'Trigger Atualizar Contadores Licenda de Funcionamento.

    Autor   :   Isaque Marinho Ribeiro
    Data    :   12/08/2016

Trigger responsavel por atualizar os contadores para a licenca de funcionamento
todas as vezes que um registro for excluido.


Historico:

    Legendas:
        + Novo objeto de banco (Campos, Triggers)
        - Remocao de objeto de banco
        * Modificacao no objeto de banco

    12/08/2016 - IMR :
        * Documentacao da trigger.';



/*------ 12/08/2016 15:50:40 --------*/

SET TERM ^ ;

CREATE OR ALTER procedure SP_ATUALIZAR_PROCESSO_LICENCA (
    NR_PROCESSO DMN_VCHAR_15)
as
declare variable POSICAO DMN_INTEGER;
declare variable NR_PROCESSO_NOVO DMN_VCHAR_15;
declare variable NR_EXERCICIO DMN_SMALLINT;
declare variable EX_GENERADOR DMN_VCHAR_30;
declare variable SE_GENERADOR DMN_VCHAR_1024;
declare variable UP_GENERADOR DMN_VCHAR_1024;
begin
  posicao = position('/', :nr_processo);

  if (coalesce(:posicao, 0) > 0) then
  begin
    /* Identificar o exercicio do processo */
    nr_exercicio = substring(:nr_processo from (:posicao + 1) for 4);

    Select
      max(l.nr_processo)
    from TBLICENCA_FUNCIONAMENTO l
    where l.nr_processo like '%/' || :nr_exercicio
    Into
      nr_processo_novo;

    if (coalesce(trim(:nr_processo_novo), '') <> '') then
    begin
      nr_processo_novo = replace(:nr_processo_novo, '/' || :nr_exercicio, '');

      /* Atualizar Generator */
      se_generador = 'SELECT rdb$generator_name from rdb$generators  where rdb$generator_name = ''GEN_PROCESSO_' || :nr_exercicio || '''';
      up_generador = 'ALTER SEQUENCE GEN_PROCESSO_' || :nr_exercicio || ' RESTART WITH ' || :nr_processo_novo;

      execute statement se_generador
      Into
        ex_generador;

      if ( nullif(:ex_generador, '') is not null ) then
        execute statement :up_generador;
    end
  end
end^

/*------ 12/08/2016 15:50:40 --------*/

SET TERM ; ^


/*------ SYSDBA 12/08/2016 15:57:54 --------*/

SET TERM ^ ;

CREATE OR ALTER trigger tg_licenca_funciona_atualiza for tblicenca_funcionamento
active after delete position 10
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_processo);
end^

SET TERM ; ^




/*------ SYSDBA 12/10/2016 09:41:07 --------*/

ALTER TABLE TBESTABELECIMENTO
    ADD SN_ORCAO_PUBLICO DMN_BOOLEAN;

COMMENT ON COLUMN TBESTABELECIMENTO.SN_ORCAO_PUBLICO IS
'Orgao Publico:
0 - Nao
1 - Sim';

alter table TBESTABELECIMENTO
alter ID_ESTABELECIMENTO position 1;

alter table TBESTABELECIMENTO
alter TP_PESSOA position 2;

alter table TBESTABELECIMENTO
alter NM_RAZAO position 3;

alter table TBESTABELECIMENTO
alter NM_FANTASIA position 4;

alter table TBESTABELECIMENTO
alter NR_CNPJ position 5;

alter table TBESTABELECIMENTO
alter NR_INSC_EST position 6;

alter table TBESTABELECIMENTO
alter NR_INSC_MUN position 7;

alter table TBESTABELECIMENTO
alter CD_CNAE_PRINCIPAL position 8;

alter table TBESTABELECIMENTO
alter CD_CNAE_SECUNDARIA position 9;

alter table TBESTABELECIMENTO
alter SN_ORCAO_PUBLICO position 10;

alter table TBESTABELECIMENTO
alter SN_ATIVO position 11;

alter table TBESTABELECIMENTO
alter TP_ENDERECO position 12;

alter table TBESTABELECIMENTO
alter DS_ENDERECO position 13;

alter table TBESTABELECIMENTO
alter NR_ENDERECO position 14;

alter table TBESTABELECIMENTO
alter DS_COMPLEMENTO position 15;

alter table TBESTABELECIMENTO
alter CD_BAIRRO position 16;

alter table TBESTABELECIMENTO
alter NR_CEP position 17;

alter table TBESTABELECIMENTO
alter CD_CIDADE position 18;

alter table TBESTABELECIMENTO
alter CD_UF position 19;

alter table TBESTABELECIMENTO
alter CD_ESTADO position 20;

alter table TBESTABELECIMENTO
alter DS_EMAIL position 21;

alter table TBESTABELECIMENTO
alter NM_CONTATO position 22;

alter table TBESTABELECIMENTO
alter NR_TELEFONE position 23;

alter table TBESTABELECIMENTO
alter NR_COMERCIAL position 24;

alter table TBESTABELECIMENTO
alter NR_CELULAR position 25;

alter table TBESTABELECIMENTO
alter US_CADASTRO position 26;

alter table TBESTABELECIMENTO
alter DH_CADASTRO position 27;

alter table TBESTABELECIMENTO
alter HASH_CADASTRO position 28;

alter table TBESTABELECIMENTO
alter US_ALTERACAO position 29;

alter table TBESTABELECIMENTO
alter DH_ALTERACAO position 30;

alter table TBESTABELECIMENTO
alter HASH_ALTERACAO position 31;




/*------ SYSDBA 12/10/2016 09:41:15 --------*/

UPDATE TBESTABELECIMENTO
SET SN_ORCAO_PUBLICO = 0;


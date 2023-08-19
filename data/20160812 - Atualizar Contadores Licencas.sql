/* Server version: WI-V6.3.3.26780 Firebird 2.5 
   SQLDialect: 3. ODS: 11.2. Forced writes: On. Sweep interval: 20000.
   Page size: 16384. Cache pages: 2048 (32768 Kb). Read-only: False. */
SET CLIENTLIB 'C:\Program Files (x86)\Firebird\Firebird_2_5\bin\fbclient.dll';
SET NAMES UNICODE_FSS;

SET SQL DIALECT 3;

CONNECT 'localhost:DMWEB_SMS_CASTANHAL' USER 'SYSDBA' PASSWORD 'masterkey';

SET AUTODDL ON;

/* Create Procedure... */
SET TERM ^ ;

CREATE PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA(NR_EXERCICIO DMN_SMALLINT)
 AS
 BEGIN EXIT; END
^

SET TERM ; ^

DESCRIBE PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA
'Procedure Atualizar Sequenciador Controle de Licencas de Funcionamento.

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

SET TERM ^ ;

CREATE PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA(NR_PROCESSO DMN_VCHAR_15)
 AS
 BEGIN EXIT; END
^

SET TERM ; ^

DESCRIBE PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA
'Procedure Atualizar Sequenciador Processo de Licencas de Funcionamento.

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


/* Alter Procedure... */
/* Restore proc. body: SP_ATUALIZAR_CONTROLE_LICENCA */
SET TERM ^ ;

ALTER PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA(NR_EXERCICIO DMN_SMALLINT)
 AS
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
end
^

/* Restore proc. body: SP_ATUALIZAR_PROCESSO_LICENCA */
ALTER PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA(NR_PROCESSO DMN_VCHAR_15)
 AS
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
end
^

/* Creating trigger... */
CREATE TRIGGER TG_LICENCA_FUNCIONA_ATUALIZA FOR TBLICENCA_FUNCIONAMENTO
ACTIVE AFTER DELETE POSITION 10 
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_exercicio);
end
^

SET TERM ; ^

DESCRIBE TRIGGER TG_LICENCA_FUNCIONA_ATUALIZA
'Trigger Atualizar Contadores Licenda de Funcionamento.

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


/* Alter Procedure... */
/* Create(Add) privilege */
GRANT EXECUTE ON PROCEDURE SP_ATUALIZAR_CONTROLE_LICENCA TO PUBLIC;

GRANT EXECUTE ON PROCEDURE SP_ATUALIZAR_PROCESSO_LICENCA TO PUBLIC;


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
end
^

SET TERM ; ^

SET TERM ^ ;

CREATE OR ALTER trigger tg_licenca_funciona_atualiza for tblicenca_funcionamento
active after delete position 10
AS
begin
  Execute Procedure SP_ATUALIZAR_CONTROLE_LICENCA(old.nr_exercicio);
  Execute Procedure SP_ATUALIZAR_PROCESSO_LICENCA(old.nr_processo);
end^

SET TERM ; ^


CREATE OR REPLACE FUNCTION tes.ft_conciliacion_bancaria_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Tesoreria
 FUNCION: 		tes.ft_conciliacion_bancaria_sel
 DESCRIPCION:   Funcion conciliacion bancaria
 AUTOR: 		Breydi vasquez pacheco
 FECHA:	        19-02-2019
 COMENTARIOS:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
    v_con				varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_filtro_saldo		varchar;
    v_fecha_anterior	date;
    v_cnx 				varchar;
    v_fecha_ini			date;
    v_fecha_fin 		date;
    v_periodo			integer;
    v_pe			    integer;
    v_fecha_repo		date;
    i 					integer;
    v_count				integer;
    v_cargo             varchar;
BEGIN

	v_nombre_funcion = 'tes.ft_conciliacion_bancaria_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_CONCBAN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Breydi vasquez pacheco
 	#FECHA:		19-02-2019
	***********************************/

	if(p_transaccion='TES_CONCBAN_SEL')then

    	begin

          select fc.descripcion_cargo into v_cargo
          from segu.vusuario u
          inner join orga.vfuncionario f on f.id_persona = u.id_persona
          inner join orga.vfuncionario_cargo_lugar fc on fc.id_funcionario = f.id_funcionario
          where u.id_usuario = p_id_usuario
          and nombre_unidad = 'Tesorería'
          and nombre_cargo = 'Tesorería';

    		--Sentencia de la consulta
			v_consulta:=' select conci.id_conciliacion_bancaria,
                                 conci.id_cuenta_bancaria,
                                 conci.id_funcionario_elabo,
                                 conci.id_funcionario_vb,
                                 ges.id_gestion,
                                 per.id_periodo,
                                 conci.saldo_banco,
                                 conci.observaciones,
                                 conci.fecha,
                                 conci.fecha_reg,
                                 ges.gestion,
								 param.f_literal_periodo(per.id_periodo)  as literal,
                                 usu1.cuenta as usr_reg,
                                 usu2.cuenta as usr_mod,
                                 fun.desc_funcionario2 as fun_elabo,
                                 fu.desc_funcionario2  as fun_vb,
                                 conci.estado,
                                 conci.saldo_real_1,
            					 conci.saldo_real_2,
                                 conci.saldo_libros,
                                 (conci.saldo_real_1 - conci.saldo_real_2)::numeric as diferencia,
                                 '''||coalesce(v_cargo,'no')||'''::varchar as jefe_tesoreria
                          from tes.tconciliacion_bancaria conci
                          inner join segu.tusuario usu1 on usu1.id_usuario = conci.id_usuario_reg
                          left join segu.tusuario usu2 on usu2.id_usuario = conci.id_usuario_mod
                          inner join param.tgestion ges on ges.id_gestion = conci.id_gestion
                          inner join param.tperiodo per on per.id_periodo = conci.id_periodo
                          left join orga.vfuncionario fun on fun.id_funcionario = conci.id_funcionario_elabo
                          left join orga.vfuncionario fu on fu.id_funcionario = conci.id_funcionario_vb
                          where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_CONCBAN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Breydi vasquez pacheco
 	#FECHA:		19-02-2019
	***********************************/

	elsif(p_transaccion='TES_CONCBAN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(conci.id_conciliacion_bancaria)
                          from tes.tconciliacion_bancaria conci
                          inner join segu.tusuario usu1 on usu1.id_usuario = conci.id_usuario_reg
                          left join segu.tusuario usu2 on usu2.id_usuario = conci.id_usuario_mod
                          inner join param.tgestion ges on ges.id_gestion = conci.id_gestion
                          inner join param.tperiodo per on per.id_periodo = conci.id_periodo
                          inner join orga.vfuncionario fun on fun.id_funcionario = conci.id_funcionario_elabo
                          inner join orga.vfuncionario fu on fu.id_funcionario = conci.id_funcionario_vb
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_RECONCBAN_SEL'
 	#DESCRIPCION:	Reporte Conciliaicion Bancaria
 	#AUTOR:		BVP
 	#FECHA:		19-02-2019
	***********************************/

	elsif(p_transaccion='TES_RECONCBAN_SEL')then

		begin
       --Sentencia de la consulta de conteo de registros
        create temp table tt_conciliacion_t3 (
            nombre_institucion 	varchar,
            nro_cuenta 			varchar,
            denominacion		varchar,
            moneda				varchar,
            nro_cheque 			varchar,
            concepto			text,
            total_haber 		numeric(18,2),
            fecha				date,
            mes                 integer
        ) on commit drop;

    	v_periodo = v_parametros.id_periodo;

        select per.fecha_fin
	           into v_fecha_repo
        from param.tperiodo per
        where per.id_periodo = v_periodo;


          with recursive det ( inst, nr_c, me, co, cou, saldo, mone, deno, nc) as (
              select
                 ins.nombre as nombre_institucion,
                 cuba.nro_cuenta,
                 extract(month from cbre.fecha)::integer,
                 ('CHEQUES EN CIRCULACION '||upper(tes.f_month(cbre.fecha))||'/'||substr((''||extract(year from cbre.fecha)||'')::text,3,4)),
                 (select count(id_periodo)
                          from tes.tconciliacion_bancaria_rep
                          where id_conciliacion_bancaria = cbre.id_conciliacion_bancaria
                          and id_periodo = cbre.id_periodo) as contador,
                 case when cbre.periodo_1 is not null then
                  cbre.periodo_1
                  when cbre.periodo_2 is not null and v_fecha_repo < '01/03/2021'::date then
                  cbre.periodo_2
                  when cbre.periodo_3 is not null and v_fecha_repo < '01/01/2020'::date then
                  cbre.periodo_3
                  end as saldo,
                  mon.moneda,
                  cuba.denominacion,
                  cbre.nro_cheque
              from tes.tconciliacion_bancaria_rep cbre
              inner join tes.tconciliacion_bancaria cb on cb.id_conciliacion_bancaria = cbre.id_conciliacion_bancaria
              inner join tes.tcuenta_bancaria cuba on cuba.id_cuenta_bancaria = cb.id_cuenta_bancaria
              inner join param.tinstitucion ins on ins.id_institucion = cuba.id_institucion
              inner join param.tmoneda mon on mon.id_moneda = cuba.id_moneda
              where  cbre.id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria)

	  	insert into  tt_conciliacion_t3 (nombre_institucion, nro_cuenta, concepto, moneda, nro_cheque, denominacion, total_haber, fecha, mes)

          select
              inst,
              nr_c,
              co,
              mone,
              case when cou > 1 then
              'VARIOS'::varchar
              when cou = 1  then
              ''||nc||''::varchar end,
              deno,
          	  saldo,
              v_fecha_repo,
              me::integer
          from det;


      v_consulta:='select
      			   t3.nombre_institucion,
                   t3.nro_cuenta,
                   t3.concepto,
                   to_char(t3.fecha,''dd/mm/yyyy'')::text as fecha,
                   sum(t3.total_haber) as saldo,
                   t3.moneda,
                   t3.denominacion,
                   t3.nro_cheque
                 from tt_conciliacion_t3 t3
                 where  t3.total_haber > 0 and';
			v_consulta:= v_consulta||v_parametros.filtro;
			v_consulta:= v_consulta||'group by t3.nro_cheque,
            						  t3.nombre_institucion,
                                      t3.nro_cuenta,
                                      t3.concepto,
                                      t3.fecha,
                                      t3.moneda,
					                  t3.denominacion,
                                      t3.mes
            			order by t3.mes desc ';
			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'TES_RECONCBANDET_SEL'
 	#DESCRIPCION:	Reporte Conciliaicion Bancaria
 	#AUTOR:		BVP
 	#FECHA:		19-02-2019
	***********************************/

	elsif(p_transaccion='TES_RECONCBANDET_SEL')then

		begin
			v_consulta:='
                        select
                        ins.nombre as nombre_institucion,
                        conba.fecha,
                        conba.saldo_banco as saldo,
                        conba.observaciones as observaciones,
                        fun.desc_funcionario2 as fun_elab,
                        fu.desc_funcionario2 as fun_vb,
                        mon.moneda,
                        to_char(deta.fecha, ''dd/mm/yyyy'')::text  as fecha_reg,
                        deta.concepto as concepto,
                        deta.importe,
                        deta.nro_comprobante,
                        deta.tipo,
                        --tes.f_saldo_cuenta_bancaria(conba.id_cuenta_bancaria,conba.id_periodo) as sal_ext_ban,
                        conba.saldo_libros as sal_ext_ban,
                        param.f_literal_periodo(conba.id_periodo)as periodo,
                        ges.gestion,
                        cban.nro_cuenta,
                        cban.denominacion
                        from  tes.tconciliacion_bancaria conba
                        left join tes.tdetalle_conciliacion_bancaria deta on deta.id_conciliacion_bancaria =conba.id_conciliacion_bancaria
                        inner join tes.tcuenta_bancaria cban on cban.id_cuenta_bancaria = conba.id_cuenta_bancaria
                        inner join param.tinstitucion ins on ins.id_institucion  = cban.id_institucion
                        left join orga.vfuncionario fun on fun.id_funcionario = conba.id_funcionario_elabo
                        left join orga.vfuncionario fu on fu.id_funcionario = conba.id_funcionario_vb
                        inner join tes.tcuenta_bancaria cuba on cuba.id_cuenta_bancaria = conba.id_cuenta_bancaria
                        inner join param.tmoneda mon on mon.id_moneda =  cuba.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = conba.id_gestion
                        where conba.id_cuenta_bancaria = '||v_parametros.id_cuenta_bancaria||'
                        and conba.id_periodo = '||v_parametros.id_periodo ||' ';

			--Definicion de la respuesta
            raise notice '%',v_consulta;
--			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
      /*********************************
      #TRANSACCION:  'TES_DETREPCONBA_SEL'
      #DESCRIPCION:	Consulta de datos
      #AUTOR:		Breydi vasquez pacheco
      #FECHA:		18-09-2019
      ***********************************/

      elsif(p_transaccion='TES_DETREPCONBA_SEL')then

          begin
             v_consulta:='select cbre.id_conciliacion_bancaria_rep,
                                 cbre.id_conciliacion_bancaria,
                                 cbre.nro_cheque,
                                 cbre.periodo_1,
                                 cbre.periodo_2,
                                 cbre.periodo_3,
                                 cbre.total_haber,
                                 cbre.estado,
                                 cbre.detalle,
                                 cbre.observacion,
                                 cbre.fecha,
                                 cbre.fecha_reg,
                                 usu1.cuenta as usr_reg,
                                 usu2.cuenta as usr_mod
                        from tes.tconciliacion_bancaria_rep cbre
                        inner join segu.tusuario usu1 on usu1.id_usuario = cbre.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = cbre.id_usuario_mod
                        where cbre.total_haber > 0 and ';
                  v_consulta:=v_consulta||v_parametros.filtro;
				  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
                  raise notice '%',v_consulta;
                  --Devuelve la respuesta
                  return v_consulta;
		end;
      /*********************************
      #TRANSACCION:  'TES_DETREPCONBA_CONT'
      #DESCRIPCION:	Consulta de datos
      #AUTOR:		Breydi vasquez pacheco
      #FECHA:		18-09-2019
      ***********************************/

      elsif(p_transaccion='TES_DETREPCONBA_CONT')then

          begin
             v_consulta:='select count(cbre.id_conciliacion_bancaria_rep),
              					 sum(cbre.periodo_1) as total_1,
                                 sum(cbre.periodo_2) as total_2,
                                 sum(cbre.periodo_3) as total_3
                        from tes.tconciliacion_bancaria_rep cbre
                        inner join segu.tusuario usu1 on usu1.id_usuario = cbre.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = cbre.id_usuario_mod
                        where cbre.total_haber > 0 and ';
                  v_consulta:=v_consulta||v_parametros.filtro;
                  --Devuelve la respuesta
                  return v_consulta;
		end;
	else

		raise exception 'Transaccion inexistente';

	end if;

EXCEPTION

	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

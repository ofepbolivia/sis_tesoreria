CREATE OR REPLACE FUNCTION tes.ft_cuotas_devengadas_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_cuotas_devengadas_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tobligacion_det'
 AUTOR: 		Ismael Valdivia
 FECHA:	        25-11-2021 12:53:35
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'tes.ft_obligacion_det_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_DEVEN_SEL'
 	#DESCRIPCION:	Listado de las cuotas devengadas
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		25-11-2021 12:53:35
	***********************************/

	if(p_transaccion='TES_DEVEN_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						plapa.id_plan_pago,
						plapa.estado_reg,
						plapa.nro_cuota,
						plapa.monto_ejecutar_total_mb,
						plapa.nro_sol_pago,
						plapa.tipo_cambio,
						plapa.fecha_pag,
						plapa.id_proceso_wf,
						plapa.fecha_dev,
						plapa.estado,
						plapa.tipo_pago,
						plapa.monto_ejecutar_total_mo,
						plapa.descuento_anticipo_mb,
						plapa.obs_descuentos_anticipo,
						plapa.id_plan_pago_fk,
						plapa.id_obligacion_pago,
						plapa.id_plantilla,
						plapa.descuento_anticipo,
						plapa.otros_descuentos,
						plapa.tipo,
						plapa.obs_monto_no_pagado,
						plapa.obs_otros_descuentos,
						plapa.monto,
						plapa.id_int_comprobante,
						plapa.nombre_pago,
						plapa.monto_no_pagado_mb,
						plapa.monto_mb,
						plapa.id_estado_wf,
						plapa.id_cuenta_bancaria,
						plapa.otros_descuentos_mb,
						plapa.forma_pago,
						plapa.monto_no_pagado,
						plapa.fecha_reg,
						plapa.id_usuario_reg,
						plapa.fecha_mod,
						plapa.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        plapa.fecha_tentativa,
                        pla.desc_plantilla,
                        plapa.liquido_pagable,
                        plapa.total_prorrateado,
                        plapa.total_pagado ,
						coalesce(cb.nombre_institucion,''S/N'') ||'' (''||coalesce(cb.nro_cuenta,''S/C'')||'')'' as desc_cuenta_bancaria ,
                        plapa.sinc_presupuesto ,
                        plapa.monto_retgar_mb,
                        plapa.monto_retgar_mo,
                        descuento_ley,
                        obs_descuentos_ley ,
                        descuento_ley_mb,
                        porc_descuento_ley,
                        plapa.nro_cheque,
                        plapa.nro_cuenta_bancaria,
                        plapa.id_cuenta_bancaria_mov,
                        cbanmo.descripcion as desc_deposito,
                        op.numero  as numero_op ,
                        op.id_depto_conta,
                        op.id_moneda ,
                        mon.tipo_moneda ,
                        mon.codigo as desc_moneda,
                        op.num_tramite,
                        plapa.porc_monto_excento_var,
                        plapa.monto_excento,
                        ew.obs as obs_wf ,
                        plapa.obs_descuento_inter_serv,
                        plapa.descuento_inter_serv,
                        ROUND(plapa.porc_monto_retgar,3)::numeric as porc_monto_retgar,
                        fun.desc_funcionario1::text,
                        plapa.revisado_asistente,
                        plapa.conformidad,
                        plapa.fecha_conformidad,
                        op.tipo_obligacion,
                        plapa.monto_ajuste_ag,
                        plapa.monto_ajuste_siguiente_pago,
                        op.pago_variable,
                        plapa.monto_anticipo,
                        plapa.fecha_costo_ini,
                        plapa.fecha_costo_fin,
                        plapa.fecha_conclusion_pago,
                        funwf.desc_funcionario1::text as funcionario_wf,
                        plapa.tiene_form500,
                        plapa.id_depto_lb,
                        depto.nombre as desc_depto_lb,
                        op.ultima_cuota_dev,
                        plapa.id_depto_conta as id_depto_conta_pp,
                        depc.nombre_corto as desc_depto_conta_pp,
                        (select count(*)
                             from unnest(pwf.id_tipo_estado_wfs) elemento
                             where elemento = ew.id_tipo_estado) as contador_estados,
                        depto.prioridad as prioridad_lp,
						--coalesce(plapa.es_ultima_cuota,true) as es_ultima_cuota,
                        plapa.es_ultima_cuota as es_ultima_cuota,
                        tcon.nro_cbte,
                        tcon.c31,
                        op.id_gestion,
                        tcon.fecha_costo_ini as fecha_cbte_ini,
                        tcon.fecha_costo_fin as fecha_cbte_fin,
                        plapa.monto_establecido,
                        pro.id_proveedor,
                        pro.nit,
                        plapa.id_proveedor_cta_bancaria,
                        mul.id_multa,
                        mul.desc_multa,
                        op.id_obligacion_pago_extendida

                        from tes.tplan_pago plapa
                        inner join wf.tproceso_wf pwf on pwf.id_proceso_wf = plapa.id_proceso_wf
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago = plapa.id_obligacion_pago
                        inner join param.tmoneda mon on mon.id_moneda = op.id_moneda
                        inner join wf.testado_wf ew on ew.id_estado_wf = plapa.id_estado_wf
                        left join param.tplantilla pla on pla.id_plantilla = plapa.id_plantilla
                        inner join segu.tusuario usu1 on usu1.id_usuario = plapa.id_usuario_reg
                        left join tes.vcuenta_bancaria cb on cb.id_cuenta_bancaria = plapa.id_cuenta_bancaria
                        left join segu.tusuario usu2 on usu2.id_usuario = plapa.id_usuario_mod
                        left join tes.tcuenta_bancaria_mov cbanmo on cbanmo.id_cuenta_bancaria_mov = plapa.id_cuenta_bancaria_mov
                        left join param.vproveedor pro on pro.id_proveedor = op.id_proveedor
                        left join orga.vfuncionario fun on fun.id_funcionario = op.id_funcionario
                        left join orga.vfuncionario funwf on funwf.id_funcionario = ew.id_funcionario
                        left join param.tdepto depto on depto.id_depto = plapa.id_depto_lb
                        left join param.tdepto depc on depc.id_depto = plapa.id_depto_conta
                        left join conta.tint_comprobante tcon on tcon.id_int_comprobante = plapa.id_int_comprobante
                        left join sigep.tmulta mul on mul.id_multa = plapa.id_multa
                       where  plapa.estado_reg=''activo''  and plapa.tipo = ''pagado'' and plapa.estado not in (''pagado'') and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--v_consulta:=v_consulta||' limit 10 ';
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_DEVEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		25-11-2021 12:53:35
	***********************************/

	elsif(p_transaccion='TES_DEVEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(plapa.id_plan_pago)
						from tes.tplan_pago plapa
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago = plapa.id_obligacion_pago
                        inner join param.tmoneda mon on mon.id_moneda = op.id_moneda
                        inner join wf.testado_wf ew on ew.id_estado_wf = plapa.id_estado_wf
                        left join param.tplantilla pla on pla.id_plantilla = plapa.id_plantilla
                        inner join segu.tusuario usu1 on usu1.id_usuario = plapa.id_usuario_reg
                        left join tes.vcuenta_bancaria cb on cb.id_cuenta_bancaria = plapa.id_cuenta_bancaria
                        left join segu.tusuario usu2 on usu2.id_usuario = plapa.id_usuario_mod
                        left join orga.vfuncionario fun on fun.id_funcionario = op.id_funcionario
                        left join tes.tcuenta_bancaria_mov cbanmo on cbanmo.id_cuenta_bancaria_mov = plapa.id_cuenta_bancaria_mov
                        left join param.vproveedor pro on pro.id_proveedor = op.id_proveedor
                        left join orga.vfuncionario funwf on funwf.id_funcionario = ew.id_funcionario
                        left join param.tdepto depto on depto.id_depto = plapa.id_depto_lb
                        left join tes.tts_libro_bancos lb on plapa.id_int_comprobante = lb.id_int_comprobante
                        left join conta.tint_comprobante tcon on tcon.id_int_comprobante = plapa.id_int_comprobante
                      where  plapa.estado_reg=''activo''   and plapa.tipo = ''pagado'' and plapa.estado not in (''pagado'') and ';

			--Definicion de la respuesta
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

ALTER FUNCTION tes.ft_cuotas_devengadas_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;

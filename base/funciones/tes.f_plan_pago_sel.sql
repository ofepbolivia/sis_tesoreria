CREATE OR REPLACE FUNCTION tes.f_plan_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.f_plan_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tplan_pago'
 AUTOR: 		 (admin)
 FECHA:	        10-04-2013 15:43:23
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
    v_filtro			varchar;

    v_historico        varchar;
    v_inner            varchar;
    v_strg_pp         varchar;
    v_strg_obs         varchar;
    va_id_depto        integer[];

    v_strg_sol			varchar;
    v_filadd 			varchar;
    v_id_funcionario			integer;
    v_consulta_sup		varchar= ' ';
    v_consulta_sup_1	varchar= ' ';

BEGIN

	v_nombre_funcion = 'tes.f_plan_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_PLAPA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	if(p_transaccion='TES_PLAPA_SEL')then

    	begin


            -- obtiene los departamentos del usuario
            select
                pxp.aggarray(depu.id_depto)
            into
                va_id_depto
            from param.tdepto_usuario depu
            where depu.id_usuario =  p_id_usuario;




            v_filtro='';

            IF (v_parametros.id_funcionario_usu is null) then

                v_parametros.id_funcionario_usu = -1;

            END IF;


            IF  lower(v_parametros.tipo_interfaz) = 'planpagovbcostos' THEN

                IF p_administrador !=1 THEN
                   v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and ((lower(plapa.estado)=''supcostos'')  or  (lower(plapa.estado)=''vbcostos''))  and ';
                 ELSE
                     v_filtro = ' ((lower(plapa.estado)=''supcostos'')  or  (lower(plapa.estado)=''vbcostos'')) and ';
                END IF;


            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagovb' THEN

                IF p_administrador !=1 THEN
                   v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or (ew.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||')) or (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER)))  )
                   and  (lower(plapa.estado)!=''borrador'') and lower(plapa.estado)!=''pagado'' and lower(plapa.estado)!=''devengado'' and lower(plapa.estado)!=''anticipado'' and lower(plapa.estado)!=''aplicado'' and lower(plapa.estado)!=''anulado'' and lower(plapa.estado)!=''devuelto'' and ';
                 ELSE
                     v_filtro = ' (lower(plapa.estado)!=''borrador''  and lower(plapa.estado)!=''pendiente''  and lower(plapa.estado)!=''pagado'' and lower(plapa.estado)!=''devengado'' and lower(plapa.estado)!=''anticipado'' and lower(plapa.estado)!=''aplicado'' and lower(plapa.estado)!=''anulado'' and lower(plapa.estado)!=''devuelto'') and ';
                END IF;


            END IF;

            IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
             v_historico =  v_parametros.historico;
            ELSE
               v_historico = 'no';
            END IF;


            IF  lower(v_parametros.tipo_interfaz) = 'planpagovbasistente' and v_historico != 'si'  THEN
              	v_filtro = ' (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER))) and ';
            	  --v_filtro = v_filtro || ' (lower(plapa.estado)=''vbgerente'') and ';
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadpendiente'  THEN
            	IF p_administrador !=1 THEN
              		v_filtro = ' (op.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or op.id_usuario_reg = ' || p_id_usuario||' ) and ';
                END IF;
            	v_filtro = v_filtro || ' lower(plapa.tipo) in (''devengado'',''devengado_pagado'',''devengado_pagado_1c'', ''devengado_pagado_1c_sp'') and plapa.fecha_conformidad is null and  ';
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadrealizada'  THEN
              	IF p_administrador !=1 THEN
              		v_filtro = ' (op.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or op.id_usuario_reg = ' || p_id_usuario||' ) and ';
                END IF;
            	v_filtro = v_filtro || ' lower(plapa.tipo) in (''devengado'',''devengado_pagado'',''devengado_pagado_1c'', ''devengado_pagado_1c_sp'') and plapa.fecha_conformidad is not null and  ';
            END IF;







            IF v_historico =  'si' THEN


                v_inner =  'inner join wf.testado_wf ew on ew.id_proceso_wf = plapa.id_proceso_wf ';
                v_strg_pp = 'DISTINCT(plapa.id_plan_pago)';
                v_strg_obs = '''---''::text  as obs_wf';

               IF  lower(v_parametros.tipo_interfaz) != 'planpagovbasistente'  THEN
                      IF p_administrador !=1 THEN
                            --v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  ';
                       v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or (ew.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||'))    ) and  ';
                     ELSE
                        v_filtro = '';
                     END IF;
               ELSE
                  --historico para interface de asistentes
                  v_filtro = ' (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER))) and ';

               END IF;


            ELSE

               v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = plapa.id_estado_wf';
               v_strg_pp = 'plapa.id_plan_pago';
               v_strg_obs = 'ew.obs as obs_wf';


             END IF;




    		--Sentencia de la consulta
			v_consulta:='select
						'||v_strg_pp||',
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
                        '||v_strg_obs||' ,
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
                        '||v_inner||'
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
                       where  plapa.estado_reg=''activo''  and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ', nro_cuota ASC limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

             raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

  /*********************************
 	#TRANSACCION:  'TES_PLAPA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPA_CONT')then

		begin


        v_filtro='';

            IF (v_parametros.id_funcionario_usu is null) then
              	v_parametros.id_funcionario_usu = -1;
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagovbcostos' THEN

                IF p_administrador !=1 THEN
                   v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and ((lower(plapa.estado)=''supcostos'')  or  (lower(plapa.estado)=''vbcostos''))  and ';
                 ELSE
                     v_filtro = ' ((lower(plapa.estado)=''supcostos'')  or  (lower(plapa.estado)=''vbcostos'')) and ';
                END IF;


            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagovb' THEN
                 IF p_administrador !=1 THEN
                    v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(plapa.estado)!=''borrador'') and lower(plapa.estado)!=''pagado'' and lower(plapa.estado)!=''devengado'' and lower(plapa.estado)!=''anticipado'' and lower(plapa.estado)!=''aplicado''  and ';
                 ELSE
                      v_filtro = ' (lower(plapa.estado)!=''borrador''  and lower(plapa.estado)!=''pendiente''  and lower(plapa.estado)!=''pagado'' and lower(plapa.estado)!=''devengado'' and lower(plapa.estado)!=''anticipado'' and lower(plapa.estado)!=''aplicado'' and lower(plapa.estado)!=''anulado'') and ';
                END IF;
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagovbasistente' THEN
              v_filtro = ' (ew.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER))) and ';
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
                v_historico =  v_parametros.historico;
            ELSE
               v_historico = 'no';
            END IF;

            IF v_historico =  'si' THEN

               v_inner =  'inner join wf.testado_wf ew on ew.id_proceso_wf = plapa.id_proceso_wf';
               v_strg_pp = 'DISTINCT(plapa.id_plan_pago)';

                IF p_administrador =1 THEN

                       --v_filtro = ' (lower(plapa.estado)!=''borrador'' ) and ';

               END IF;

            ELSE

               v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = plapa.id_estado_wf';
               v_strg_pp = 'plapa.id_plan_pago';


             END IF;



			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count('||v_strg_pp||')
						from tes.tplan_pago plapa
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago = plapa.id_obligacion_pago
                        inner join param.tmoneda mon on mon.id_moneda = op.id_moneda
                        '||v_inner||'
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
                      where  plapa.estado_reg=''activo''   and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;




	/*********************************
 	#TRANSACCION:  'TES_PLAPAOB_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		18-07-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPAOB_SEL')then

    	begin


    		--Sentencia de la consulta
			v_consulta:='select
						plapa.id_plan_pago,
						plapa.nro_cuota,
						plapa.monto_ejecutar_total_mb,
						plapa.nro_sol_pago,
						plapa.tipo_cambio,
						plapa.fecha_pag,
						plapa.fecha_dev,
						plapa.estado,
						plapa.tipo_pago,
						plapa.monto_ejecutar_total_mo,
						plapa.descuento_anticipo_mb,
						plapa.obs_descuentos_anticipo,
						plapa.id_plan_pago_fk,
						plapa.descuento_anticipo,
						plapa.otros_descuentos,
						plapa.tipo,
						plapa.obs_monto_no_pagado,
						plapa.obs_otros_descuentos,
						plapa.monto,
						plapa.nombre_pago,
						plapa.monto_no_pagado_mb,
						plapa.monto_mb,
						plapa.otros_descuentos_mb,
						plapa.forma_pago,
						plapa.monto_no_pagado,
                        plapa.fecha_tentativa,
                        pla.desc_plantilla,
                        plapa.liquido_pagable,
                        plapa.total_prorrateado,
                        plapa.total_pagado ,
						cb.nombre_institucion ||'' (''||cb.nro_cuenta||'')'' as desc_cuenta_bancaria ,
                        plapa.sinc_presupuesto ,
                        plapa.monto_retgar_mb,
                        plapa.monto_retgar_mo,
                        dep.nombre

						from tes.tplan_pago plapa
                        left join param.tplantilla pla on pla.id_plantilla = plapa.id_plantilla
						inner join segu.tusuario usu1 on usu1.id_usuario = plapa.id_usuario_reg
                        left join tes.vcuenta_bancaria cb on cb.id_cuenta_bancaria = plapa.id_cuenta_bancaria
                        left join segu.tusuario usu2 on usu2.id_usuario = plapa.id_usuario_mod

                        left join param.tdepto dep on dep.id_depto = plapa.id_depto_lb

                       where  plapa.estado_reg=''activo''  and plapa.id_obligacion_pago='||v_parametros.id_obligacion_pago||' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

             raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

   	/*********************************
 	#TRANSACCION:  'TES_PLAPAREP_SEL'
 	#DESCRIPCION:	Consulta para reporte plan de pagos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		19-07-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPAREP_SEL')then

    	begin

    		  --  Sentencia de la consulta
              v_consulta:='select
                               	  pg.estado,
                                  op.numero as numero_oc,
                                  pv.desc_proveedor as proveedor,
                                  pg.nro_cuota as nro_cuota,
                                  pg.fecha_dev as fecha_devengado	,
                                  pg.fecha_pag as fecha_pago,
                                  pg.forma_pago as forma_pago,
                                  pg.tipo_pago as tipo_pago,
                                  mon.moneda as moneda,
                                  mon.codigo as codigo_moneda,
                                  op.tipo_cambio_conv as tipo_cambio,
                                  pg.monto as importe,
                                  pg.monto_no_pagado as monto_no_pagado,
                                  pg.otros_descuentos as otros_descuentos,
                                  pg.obs_otros_descuentos,
                                  pg.descuento_ley,
                                  pg.obs_descuentos_ley,
                                  pg.monto_ejecutar_total_mo as monto_ejecutado_total,
                                  pg.liquido_pagable as liquido_pagable,
                                  pg.total_pagado as total_pagado,
                                  pg.fecha_reg,
                                  op.total_pago,
                                  pg.tipo,
                                  pg.monto_excento
                        from tes.tplan_pago pg
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago=pg.id_obligacion_pago
                        inner join param.vproveedor pv on pv.id_proveedor=op.id_proveedor
                        left join tes.tcuenta_bancaria cta on cta.id_cuenta_bancaria=pg.id_cuenta_bancaria
                        inner join param.tmoneda mon on mon.id_moneda=op.id_moneda
                        where pg.id_plan_pago='||v_parametros.id_plan_pago||' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;



	/*********************************
 	#TRANSACCION:  'TES_VERDIS_SEL'
 	#DESCRIPCION:	Consulta para verificar la disponibilidad presupuestaria de toda la cuota
 	#AUTOR:			RCM
 	#FECHA:			15/12/2013
	***********************************/

	elsif(p_transaccion='TES_VERDIS_SEL')then

    	begin

    		--Sentencia de la consulta
              v_consulta:='select
              				id_partida,  id_presupuesto, id_moneda, importe,
							presupuesto, desc_partida , desc_presupuesto
              				from
							tes.f_verificar_disponibilidad_presup_oblig_pago('||v_parametros.id_plan_pago ||')
							as (id_partida integer, id_presupuesto integer, id_moneda INTEGER, importe numeric,
							presupuesto varchar,
							desc_partida text, desc_presupuesto text)
							where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_VERDIS_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:			RCM
 	#FECHA:			15/12/2013
	***********************************/

	elsif(p_transaccion='TES_VERDIS_CONT')then

		begin

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_partida)
						from
						tes.f_verificar_disponibilidad_presup_oblig_pago('||v_parametros.id_plan_pago ||')
						as (id_partida integer, id_presupuesto integer, id_moneda INTEGER, importe numeric,
						presupuesto varchar,
						desc_partida text, desc_presupuesto text)
                        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_ACTCONFPP_SEL'
 	#DESCRIPCION:	Acta de Conformidad Maestro Plan de Pago
 	#AUTOR:			JRR
 	#FECHA:			30/09/2014
	***********************************/

	elsif(p_transaccion='TES_ACTCONFPP_SEL')then

		begin

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
            			fun.desc_funcionario1,
                        prov.desc_proveedor,
            			to_char( pp.fecha_conformidad,''DD/MM/YYYY''),
                        pp.conformidad,
                        COALESCE(cot.numero_oc,cont.numero)::varchar as numero_oc,
                        op.numero,
                        pp.nro_cuota,op.num_tramite::varchar,
                         tes.f_get_detalle_html(op.id_obligacion_pago)::text,
                         (case when (pxp.list_unique(ci.tipo)) is null THEN
                            ''Servicio''
                         ELSE
                            pxp.list_unique(ci.tipo)
                         END)::varchar as tipo,

                         pp.fecha_costo_ini,
                         pp.fecha_costo_fin,
                         pp.observaciones_pago,
                         op.total_nro_cuota,
                         op.obs
						from tes.tplan_pago pp
						inner join tes.tobligacion_pago op on pp.id_obligacion_pago = op.id_obligacion_pago
						inner join param.vproveedor prov on prov.id_proveedor = op.id_proveedor
						inner join orga.vfuncionario fun on fun.id_funcionario = op.id_funcionario
						left join adq.tcotizacion cot on cot.id_obligacion_pago = op.id_obligacion_pago
                        inner join tes.tobligacion_det od on od.id_obligacion_pago = op.id_obligacion_pago
                        inner join param.tconcepto_ingas ci on ci.id_concepto_ingas = od.id_concepto_ingas

                        left join leg.tcontrato cont on cont.id_contrato = op.id_contrato
						where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' group by fun.desc_funcionario1, prov.desc_proveedor, pp.fecha_conformidad,pp.conformidad, cot.numero_oc,op.numero,cont.numero, pp.nro_cuota,op.num_tramite,
                        pp.fecha_costo_ini, pp.fecha_costo_fin, pp.obs_monto_no_pagado, pp.observaciones_pago, op.total_nro_cuota, op.obs,op.id_obligacion_pago';
            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'TES_PAXCIG_SEL'
 	#DESCRIPCION:	Listado de pagos por concepto
 	#AUTOR:			JRR
 	#FECHA:			15/12/2013
	***********************************/

	elsif(p_transaccion='TES_PAXCIG_SEL')then

		begin

			--Sentencia de la consulta de conteo de registros
			v_consulta:='WITH obligacion_pago_concepto AS(
						    SELECT
						        pxp.aggarray(od.id_obligacion_det) as id_obligacion_det,
						        od.id_obligacion_pago,
						        pxp.list(ot.desc_orden) as desc_orden
						    FROM tes.tobligacion_det od
						    left join conta.torden_trabajo ot on ot.id_orden_trabajo = od.id_orden_trabajo
						    where od.id_concepto_ingas = ' || v_parametros.id_concepto || ' and od.estado_reg = ''activo''
						    group by od.id_obligacion_pago
						)

						SELECT pp.id_plan_pago,
                        		(case when ot.id_orden_trabajo is not null then
								ot.desc_orden
						        ELSE
						        opc.desc_orden
						        end) as orden_trabajo, op.num_tramite,pp.nro_cuota,prov.rotulo_comercial as proveedor,pp.estado,
						        (case when com.fecha is null THEN
						        pp.fecha_tentativa else
						        com.fecha
						        end) as fecha
						        ,mon.moneda,pp.monto,pro.monto_ejecutar_mo,
                                od.id_centro_costo, pp.fecha_costo_ini, pp.fecha_costo_fin,

                                 /*Aumentando la descripcion del Centro de Costo 28/11/2019(Ismael Valdivia)*/
                               	cc.codigo_cc,
                                od.descripcion,
                                op.obs as justificacion
                                /*****************************************************************/

						FROM tes.tobligacion_pago op
						inner join obligacion_pago_concepto opc on op.id_obligacion_pago = opc.id_obligacion_pago
						inner join tes.tplan_pago pp on op.id_obligacion_pago = pp.id_obligacion_pago and pp.estado_reg = ''activo''
						left join tes.tprorrateo pro on pro.id_plan_pago = pp.id_plan_pago and  pro.id_obligacion_det = ANY(opc.id_obligacion_det) and pro.estado_reg = ''activo''
						left join tes.tobligacion_det od on od.id_obligacion_det = pro.id_obligacion_det

                        /*Aumentando la condicion para recuperar la descripcion del Centro de costo 28/11/2019(Ismael Valdivia)*/
                        left join param.vcentro_costo cc on cc.id_centro_costo = od.id_centro_costo
                        /***************************************************************************/


						left join conta.torden_trabajo ot on od.id_orden_trabajo = ot.id_orden_trabajo
						inner join param.vproveedor prov on prov.id_proveedor = op.id_proveedor
						inner join param.tmoneda mon on op.id_moneda = mon.id_moneda
						left join conta.tint_comprobante com on com.id_int_comprobante = pp.id_int_comprobante
						where pp.tipo = ''devengado_pagado''  and  op.estado_reg = ''activo'' and '||v_parametros.filtro ||
						'order by pp.fecha_costo_ini,op.num_tramite,pp.nro_cuota, opc.desc_orden limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;


			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_PAXCIG_CONT'
 	#DESCRIPCION:	Conteo de pagos por concepto
 	#AUTOR:			JRR
 	#FECHA:			15/12/2013
	***********************************/

	elsif(p_transaccion='TES_PAXCIG_CONT')then

		begin

			--Sentencia de la consulta de conteo de registros
			v_consulta:='WITH obligacion_pago_concepto AS(
						    SELECT
						        pxp.aggarray(od.id_obligacion_det) as id_obligacion_det,
						        od.id_obligacion_pago,
						        pxp.list(ot.desc_orden) as desc_orden
						    FROM tes.tobligacion_det od
						    left join conta.torden_trabajo ot on ot.id_orden_trabajo = od.id_orden_trabajo
						    where od.id_concepto_ingas = ' || v_parametros.id_concepto || ' and od.estado_reg = ''activo''
						    group by od.id_obligacion_pago
						)

						SELECT count(pp.id_plan_pago),

						/*Aumentando para obtener la suma total 28/11/2019 (Ismael Valdivia)*/
                        sum (pro.monto_ejecutar_mo)
                        /**************************************/

						FROM tes.tobligacion_pago op
						inner join obligacion_pago_concepto opc on op.id_obligacion_pago = opc.id_obligacion_pago
						inner join tes.tplan_pago pp on op.id_obligacion_pago = pp.id_obligacion_pago and pp.estado_reg = ''activo''
						left join tes.tprorrateo pro on pro.id_plan_pago = pp.id_plan_pago and  pro.id_obligacion_det = ANY(opc.id_obligacion_det) and pro.estado_reg = ''activo''
						left join tes.tobligacion_det od on od.id_obligacion_det = pro.id_obligacion_det

                        /*Aumentando la condicion para recuperar la descripcion del Centro de costo 28/11/2019(Ismael Valdivia)*/
                        left join param.vcentro_costo cc on cc.id_centro_costo = od.id_centro_costo
                        /***************************************************************************/

						left join conta.torden_trabajo ot on od.id_orden_trabajo = ot.id_orden_trabajo
						inner join param.vproveedor prov on prov.id_proveedor = op.id_proveedor
						inner join param.tmoneda mon on op.id_moneda = mon.id_moneda
						left join conta.tint_comprobante com on com.id_int_comprobante = pp.id_int_comprobante
						where pp.tipo = ''devengado_pagado''  and  op.estado_reg = ''activo'' and '||v_parametros.filtro;


			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'TES_PAGOS_SEL'
 	#DESCRIPCION:	Consulta para reporte de pagos
 	#AUTOR:		rac
 	#FECHA:		22-12-2014 15:43:23
	***********************************/

	ELSIF(p_transaccion='TES_PAGOS_SEL')then

    	begin

            --Sentencia de la consulta
			v_consulta:='SELECT
                            id_plan_pago,
                            id_gestion,
                            gestion,
                            id_obligacion_pago,
                            num_tramite,
                            orden_compra,
                            tipo_obligacion,
                            pago_variable,
                            desc_proveedor,
                            estado,
                            usuario_reg,
                            fecha,
                            fecha_reg,
                            ob_obligacion_pago,
                            fecha_tentativa_de_pago,
                            nro_cuota,
                            tipo_plan_pago,
                            estado_plan_pago,
                            obs_descuento_inter_serv,
                            obs_descuentos_anticipo,
                            obs_descuentos_ley,
                            obs_monto_no_pagado,
                            obs_otros_descuentos,
                            codigo,
                            monto_cuota,
                            monto_anticipo,
                            monto_excento,
                            monto_retgar_mo,
                            monto_ajuste_ag,
                            monto_ajuste_siguiente_pago,
                            liquido_pagable,
                            monto_presupuestado,
                            desc_contrato,
                            desc_funcionario1
                          FROM
                            tes.vpago_x_proveedor
							WHERE  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ', nro_cuota ASC limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

             raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

   /*********************************
 	#TRANSACCION:  'TES_PAGOS_CONT'
 	#DESCRIPCION:	Conteo de registros para el reporte de  pagos
 	#AUTOR:		rac
 	#FECHA:		22-12-2014 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PAGOS_CONT')then

		begin


        v_filtro='';
            /*

			--Sentencia de la consulta de conteo de registros
			v_consulta:='SELECT count(id_plan_pago),
                                sum(monto_cuota) as monto_cuota,
                                sum(monto_anticipo) as monto_anticipo,
                                sum(monto_excento) as monto_excento,
                                sum(monto_retgar_mo) as monto_retgar_mo,
                                sum(monto_ajuste_ag) as monto_ajuste_ag,
                                sum(monto_ajuste_siguiente_pago) as monto_ajuste_siguiente_pago,
                                sum(liquido_pagable) as liquido_pagable,
                                sum(monto_presupuestado) as monto_presupuestado
						 FROM  tes.vpago_x_proveedor
                         WHERE ';*/


            v_consulta:='SELECT count(id_plan_pago)
						 FROM  tes.vpago_x_proveedor
                         WHERE ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;



   /*********************************
 	#TRANSACCION:  'TES_PAGOSB_SEL'
 	#DESCRIPCION:	Consulta para reporte de pagos
 	#AUTOR:		rac
 	#FECHA:		22-12-2014 15:43:23
	***********************************/

	ELSIF(p_transaccion='TES_PAGOSB_SEL')then

    	begin

            --Sentencia de la consulta
			v_consulta:='SELECT
                                id_plan_pago,
                                id_gestion,
                                gestion,
                                id_obligacion_pago,
                                num_tramite,
                                tipo_obligacion,
                                pago_variable,
                                desc_proveedor,
                                estado,
                                usuario_reg,
                                fecha,
                                fecha_reg,
                                ob_obligacion_pago,
                                fecha_tentativa_de_pago,
                                nro_cuota,
                                tipo_plan_pago,
                                estado_plan_pago,
                                obs_descuento_inter_serv,
                                obs_descuentos_anticipo,
                                obs_descuentos_ley,
                                obs_monto_no_pagado,
                                obs_otros_descuentos,
                                codigo,
                                monto_cuota,
                                monto_anticipo,
                                monto_excento,
                                monto_retgar_mo,
                                monto_ajuste_ag,
                                monto_ajuste_siguiente_pago,
                                liquido_pagable,
                                id_contrato,
                                desc_contrato,
                                desc_funcionario1,
                                bancarizacion,
                                id_proceso_wf,
                                id_plantilla,
                                desc_plantilla,
                                tipo_informe
                            FROM
                                tes.vpagos
							WHERE  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ', nro_cuota ASC limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

             raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

   /*********************************
 	#TRANSACCION:  'TES_PAGOSB_CONT'
 	#DESCRIPCION:	Conteo de registros para el reporte de  pagos
 	#AUTOR:		rac
 	#FECHA:		22-12-2014 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PAGOSB_CONT')then

		begin


        v_filtro='';

			v_consulta:='SELECT count(id_plan_pago)
						 FROM  tes.vpagos
                         WHERE ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'TES_PROCRE_SEL'
 	#DESCRIPCION:	Proceso con retencion 7%
 	#AUTOR:		MAM
 	#FECHA:		22-12-2014 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PROCRE_SEL')then

		begin

			v_consulta:='SELECT
                                obli.id_proveedor,
                                obli.id_moneda,
                                obli.id_funcionario,
                                obli.num_tramite,
                                pro.desc_proveedor as proveedor,
                                pla.tipo,
                                pla.estado,
                                pla.monto,
                                pla.fecha_dev,
                                pla.nro_cuota,
                                pla.monto_retgar_mo,
                                mo.moneda,
                                pla.liquido_pagable,
                                com.c31,
                                cc.numero,
                                ROUND(pla.porc_monto_retgar,3)::numeric as porc_monto_retgar

                                FROM tes.tobligacion_pago obli
                                inner join tes.tplan_pago pla on pla.id_obligacion_pago = obli.id_obligacion_pago
                                inner join param.vproveedor pro on pro.id_proveedor = obli.id_proveedor
                                inner join param.tmoneda mo on mo.id_moneda = obli.id_moneda
                                inner join conta.tint_comprobante com on com.id_int_comprobante = pla.id_int_comprobante
                                left join   leg.tcontrato cc on cc.id_contrato = obli.id_contrato
                                 WHERE pla.fecha_dev >= '''||v_parametros.fecha_ini||''' and pla.fecha_dev <= '''||v_parametros.fecha_fin||''' and (pla.estado in (''devengado'') and monto_retgar_mo != 0 or pla.estado in (''devuelto'' , ''anticipado'')) ';

            if (v_parametros.id_proveedor >0) then
                v_consulta:= v_consulta || 'and cc.id_proveedor = '||v_parametros.id_proveedor;
            end if;
            if (v_parametros.id_contrato >0) then
            	v_consulta:= v_consulta || 'and cc.id_contrato = '||v_parametros.id_contrato;
            end if;
			--Definicion de la respuesta
            --v_consulta:=v_consulta||v_parametros.filtro;
			 v_consulta:=v_consulta||'ORDER BY proveedor, obli.num_tramite, pla.nro_cuota ASC';

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
 	#TRANSACCION:  'TES_ACTCONTOTAL_SEL'
 	#DESCRIPCION:	Acta de Conformidad Maestro Plan de Pago Total
 	#AUTOR:			admin
 	#FECHA:			28/08/2018
	***********************************/

	elsif(p_transaccion='TES_ACTCONTOTAL_SEL')then

		begin


			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
            			to_char(conf.fecha_inicio,''DD/MM/YYYY'')::varchar,
                        to_char(conf.fecha_fin,''DD/MM/YYYY'')::varchar,
                        to_char(conf.fecha_conformidad_final,''DD/MM/YYYY'')::varchar,
            	        conf.conformidad_final::text,
                        conf.observaciones::varchar,
                        op.num_tramite::varchar,
                        prov.desc_proveedor::varchar as proveedor,
                        fun.desc_funcionario1::text as nombre_solicitante,
                        COALESCE(sol.nro_po, ''S/N'')::varchar,
                        to_char(sol.fecha_po, ''DD/MM/YYYY'')::varchar,
                        op.nro_cuota_vigente::numeric,
                        ci.desc_ingas::varchar,
                        ctd.cantidad_adju::numeric,
                        sold.descripcion::varchar as descripcion_sol,
                        (case when conf.fecha_conformidad_final is not null then ''si'' else ''no'' end)::varchar as firma


                     from tes.tobligacion_pago op
                     left join tes.tconformidad conf on conf.id_obligacion_pago = op.id_obligacion_pago
                     left join param.vproveedor prov on prov.id_proveedor = op.id_proveedor
                     left join orga.vfuncionario fun on fun.id_funcionario = op.id_funcionario
                     left join adq.tcotizacion cot on cot.id_obligacion_pago = op.id_obligacion_pago
                     left join adq.tproceso_compra pc on pc.id_proceso_compra = cot.id_proceso_compra
                     left join adq.tsolicitud sol on sol.id_solicitud = pc.id_solicitud

                     left join adq.tcotizacion_det ctd on ctd.id_cotizacion = cot.id_cotizacion
                     left join adq.tsolicitud_det sold on sold.id_solicitud_det=  ctd.id_solicitud_det
                     left join param.tconcepto_ingas ci on ci.id_concepto_ingas = sold.id_concepto_ingas

                     where op.id_proceso_wf = '||v_parametros.id_proceso_wf ;

			--Definicion de la respuesta
			--v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta;

             raise notice 'consulta %',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

   /*********************************
 	#TRANSACCION:  'TES_CONFPAGFIN_SEL'
 	#DESCRIPCION:	Consulta
 	#AUTOR:	admin
 	#FECHA:		27-09-2018 16:01:32
	***********************************/

	elsif(p_transaccion='TES_CONFPAGFIN_SEL')then

    	begin
            -- ini
             -- obtiene los departamentos del usuario
            select
                pxp.aggarray(depu.id_depto)
            into
                va_id_depto
            from param.tdepto_usuario depu
            where depu.id_usuario =  p_id_usuario;

            IF (v_parametros.id_funcionario_usu is null) then

                v_parametros.id_funcionario_usu = -1;

            END IF;
            --fin


          --v_filadd='';
          --v_inner='';
          --
          IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadpendiente'  THEN
            	IF p_administrador !=1 THEN
              		v_filtro = 'conf.fecha_conformidad_final is null and (obpg.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or obpg.id_usuario_reg = ' || p_id_usuario||' ) and ';
                ELSE
                	v_filtro = 'conf.fecha_conformidad_final is null and';
                END IF;
          END IF;

          IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadrealizada'  THEN
              	IF p_administrador !=1 THEN
              		v_filtro = '(obpg.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or obpg.id_usuario_reg = ' || p_id_usuario||' ) and ';
                ELSE
                	v_filtro = 'conf.fecha_conformidad_final is not null and';
                END IF;
          END IF;
          --

         -- raise exception '(%),... %', v_parametros.tipo_interfaz, v_filadd;

                  --Sentencia de la consulta
                  v_consulta:='select
                              obpg.id_obligacion_pago,
                              obpg.id_proveedor,
                              pv.desc_proveedor,
                              obpg.estado,
                              obpg.tipo_obligacion,
                              obpg.id_moneda,
                              mn.moneda,
                              obpg.obs,
                              obpg.porc_retgar,
                              obpg.id_subsistema,
                              ss.nombre as nombre_subsistema,
                              obpg.id_funcionario,
                              fun.desc_funcionario1,
                              obpg.estado_reg,
                              obpg.porc_anticipo,
                              obpg.id_estado_wf,
                              obpg.id_depto,
                              dep.nombre as nombre_depto,
                              obpg.num_tramite,
                              obpg.id_proceso_wf,
                              obpg.fecha_reg,
                              obpg.id_usuario_reg,
                              obpg.fecha_mod,
                              obpg.id_usuario_mod,
                              usu1.cuenta as usr_reg,
                              usu2.cuenta as usr_mod,
                              obpg.fecha,
                              obpg.numero,
                              obpg.tipo_cambio_conv,
                              obpg.id_gestion,
                              obpg.comprometido,
                              obpg.nro_cuota_vigente,
                              mn.tipo_moneda,
                              obpg.total_pago,
                              obpg.pago_variable,
                              obpg.id_depto_conta,
                              obpg.total_nro_cuota,
                              obpg.fecha_pp_ini,
                              obpg.rotacion,
                              obpg.id_plantilla,
                              pla.desc_plantilla,
                              fun.desc_funcionario1 as desc_funcionario,
                              obpg.ultima_cuota_pp,
                              obpg.ultimo_estado_pp,
                              obpg.tipo_anticipo,
                              obpg.ajuste_anticipo,
                              obpg.ajuste_aplicado,
                              obpg.monto_estimado_sg,
                              obpg.id_obligacion_pago_extendida,
                              con.tipo||'' - ''||con.numero::varchar as desc_contrato,
                              con.id_contrato,
                              obpg.obs_presupuestos,
                              obpg.uo_ex,
                              conf.id_conformidad,
                              conf.conformidad_final,
                              conf.fecha_conformidad_final::date,
                              conf.fecha_inicio::date,
                              conf.fecha_fin::date,
                              conf.observaciones

                              from tes.tobligacion_pago obpg
                              inner join segu.tusuario usu1 on usu1.id_usuario = obpg.id_usuario_reg
                              left join segu.tusuario usu2 on usu2.id_usuario = obpg.id_usuario_mod
                              left join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                              inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                              inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
                              inner join param.tdepto dep on dep.id_depto=obpg.id_depto
                              left join param.tplantilla pla on pla.id_plantilla = obpg.id_plantilla
                              inner join orga.vfuncionario fun on fun.id_funcionario=obpg.id_funcionario
                              left join leg.tcontrato con on con.id_contrato = obpg.id_contrato

                              left join tes.tconformidad conf on conf.id_obligacion_pago = obpg.id_obligacion_pago

                              where  obpg.estado != ''anulado''
                              and obpg.tipo_obligacion = ''adquisiciones'' and '||v_filtro;

                  --Definicion de la respuesta
                  v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;


              raise notice '%',v_consulta;
			--Devuelve la respuesta
       -- raise exception ' error %', v_consulta;
			return v_consulta;

		end;

         /*********************************
 	#TRANSACCION:  'TES_CONFPAGFIN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:	 admin
 	#FECHA:		27-09-2018 16:01:32
	***********************************/

	elsif(p_transaccion='TES_CONFPAGFIN_CONT')then

		begin
          -- obtiene los departamentos del usuario
            select
                pxp.aggarray(depu.id_depto)
            into
                va_id_depto
            from param.tdepto_usuario depu
            where depu.id_usuario =  p_id_usuario;

            IF (v_parametros.id_funcionario_usu is null) then

                v_parametros.id_funcionario_usu = -1;

            END IF;
            --fin


          --v_filadd='';
          --v_inner='';
          --
          IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadpendiente'  THEN
            	IF p_administrador !=1 THEN
              		v_filtro = 'conf.fecha_conformidad_final is null and (obpg.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or obpg.id_usuario_reg = ' || p_id_usuario||' ) and ';
                ELSE
                	v_filtro = 'conf.fecha_conformidad_final is null and';
                END IF;
          END IF;

          IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadrealizada'  THEN
              	IF p_administrador !=1 THEN
              		v_filtro = '(obpg.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or obpg.id_usuario_reg = ' || p_id_usuario||' ) and ';
                ELSE
                	v_filtro = 'conf.fecha_conformidad_final is not null and';
                END IF;
          END IF;
          --

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(obpg.id_obligacion_pago)
					    from tes.tobligacion_pago obpg
						inner join segu.tusuario usu1 on usu1.id_usuario = obpg.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = obpg.id_usuario_mod
                        left join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                        inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                        inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
						inner join param.tdepto dep on dep.id_depto=obpg.id_depto
                        inner join orga.vfuncionario fun on fun.id_funcionario=obpg.id_funcionario
                        left join leg.tcontrato con on con.id_contrato = obpg.id_contrato

                        left join tes.tconformidad conf on conf.id_obligacion_pago = obpg.id_obligacion_pago
                        where  obpg.estado != ''anulado'' and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta

            raise notice '%',v_consulta;
			return v_consulta;

		end;

        /*********************************
        #TRANSACCION:  'TES_PROCREPRO_SEL'
        #DESCRIPCION:	Proceso con retencion 7% a Prorrateo
        #AUTOR:		YMR
        #FECHA:		18-10-2019 15:43:23
        ***********************************/

    elsif(p_transaccion='TES_PROCREPRO_SEL')then

            begin

            v_consulta:='SELECT  pro.desc_proveedor AS proveedor,
                                     c.numero AS nro_contrato,
                                     op.num_tramite,
                                     pp.nro_cuota,
                                     pp.tipo,
                                     pp.fecha_dev,
                                     mo.moneda,
                                     cc.codigo_cc::varchar,
                                     (par.codigo::varchar||''-''||par.nombre_partida::varchar)::varchar AS partida,
                                     vcp.codigo_categoria::varchar,
                                     com.c31,
                                     pror.monto_ejecutar_mo AS monto,
                                     pror.monto_ejecutar_mo * 0.07 AS monto_retgar_mo,
                                     pror.monto_ejecutar_mo * 0.93 AS liquido_pagable
                              FROM   tes.tobligacion_pago op
                                     left join leg.tcontrato c  ON c.id_contrato = op.id_contrato
                                     inner join tes.tplan_pago pp ON pp.id_obligacion_pago = op.id_obligacion_pago
                                     left join tes.tobligacion_det od ON od.id_obligacion_pago = op.id_obligacion_pago
                                     join param.vcentro_costo cc ON cc.id_centro_costo = od.id_centro_costo
                                     join pre.tpartida par ON par.id_partida = od.id_partida
                                     join param.vproveedor pro ON pro.id_proveedor = op.id_proveedor
                                     join pre.tpresupuesto pr ON pr.id_centro_costo = cc.id_centro_costo
                                     join pre.vcategoria_programatica vcp ON vcp.id_categoria_programatica = pr.id_categoria_prog
                                     join param.tmoneda mo ON mo.id_moneda = op.id_moneda
                                     join conta.tint_comprobante com ON com.id_int_comprobante = pp.id_int_comprobante
                                     join tes.tprorrateo pror ON pror.id_plan_pago = pp.id_plan_pago AND
         							 	  pror.id_obligacion_det = od.id_obligacion_det
                              WHERE  pp.estado IN ( ''devengado'', ''devuelto'' )
                                     AND pp.monto_retgar_mo != 0
                                     AND pp.fecha_dev >= '''||v_parametros.fecha_ini||'''
                                     AND pp.fecha_dev <= '''||v_parametros.fecha_fin||'''';

                              if (v_parametros.id_proveedor >0) then
                                  v_consulta:= v_consulta || 'and c.id_proveedor = '||v_parametros.id_proveedor;
                              end if;
                              if (v_parametros.id_contrato >0) then
                                  v_consulta:= v_consulta || 'and c.id_contrato = '||v_parametros.id_contrato;
                              end if;
                v_consulta:=v_consulta||' ORDER  BY op.id_proveedor, op.id_contrato, op.num_tramite, pp.nro_cuota ASC';

                raise notice '% .',v_consulta;
                --Devuelve la respuesta
                return v_consulta;


            end;

            /*********************************
            #TRANSACCION:  'TES_REXCONT_SEL'
            #DESCRIPCION:	consulta reporte Resumen por contrato
            #AUTOR:		breydi vasquez
            #FECHA:		10-12-2020
            ***********************************/

        elsif(p_transaccion='TES_REXCONT_SEL')then

                begin

                if (v_parametros.id_proveedor >0) then
                    v_consulta_sup = ' and cc.id_proveedor = '||v_parametros.id_proveedor;
                end if;
                if (v_parametros.id_contrato >0) then
                	v_consulta_sup_1 = ' and cc.id_contrato = '||v_parametros.id_contrato;
                end if;

                v_consulta:= '

                SELECT	TO_JSON(ROW_TO_JSON(jsonD) :: TEXT) #>> ''{}'' as jsonData
                FROM (
                SELECT ARRAY_TO_JSON(ARRAY_AGG(tvalue_data)) as data
                FROM(

                with  dev (id,prov, n, t, m, mr, li ) AS
                  (select
                          cc.id_contrato,
                          pro.desc_proveedor,
                          cc.numero,
                          pla.tipo ,
                          sum(pla.monto) as monto,
                          sum(pla.monto_retgar_mo),
                          sum(pla.liquido_pagable)
                      FROM tes.tobligacion_pago obli
                      inner join tes.tplan_pago pla on pla.id_obligacion_pago = obli.id_obligacion_pago
                      inner join param.vproveedor pro on pro.id_proveedor = obli.id_proveedor
                      left join   leg.tcontrato cc on cc.id_contrato = obli.id_contrato
                      WHERE pla.fecha_dev >= '''||v_parametros.fecha_ini||''' and pla.fecha_dev <= '''||v_parametros.fecha_fin||'''
                      and (pla.estado in (''devengado'') and monto_retgar_mo != 0 or pla.estado in (''devuelto''))
                      and pla.tipo = ''dev_garantia''
                      '|| v_consulta_sup ||'
                      '|| v_consulta_sup_1 ||'

                      GROUP BY
                      cc.id_contrato,
                      pro.desc_proveedor,
                      cc.numero,
                      pla.tipo
                      )
                  select
                          pro.desc_proveedor as proveedor,
                          cc.numero,
                          pla.tipo,
                          sum(pla.monto) as monto,
                          sum(pla.monto_retgar_mo) as monto_retgar_mo,
                          sum(pla.liquido_pagable) as liquido_pagable,
                          coalesce(dd.m, 0) as dev_garantia,
                          (sum(pla.monto_retgar_mo) - coalesce(dd.m, 0)) as total_devol
                      FROM tes.tobligacion_pago obli
                      inner join tes.tplan_pago pla on pla.id_obligacion_pago = obli.id_obligacion_pago
                      inner join param.vproveedor pro on pro.id_proveedor = obli.id_proveedor
                      left join   leg.tcontrato cc on cc.id_contrato = obli.id_contrato
                      left join dev dd on dd.id = cc.id_contrato
                      WHERE pla.fecha_dev >= '''||v_parametros.fecha_ini||''' and pla.fecha_dev <= '''||v_parametros.fecha_fin||'''
                      and (pla.estado in (''devengado'') and monto_retgar_mo != 0 or pla.estado in (''devuelto''))
                      and pla.tipo = ''devengado_pagado''
                      '|| v_consulta_sup ||'
                      '|| v_consulta_sup_1 ||'

                      GROUP BY
                      pro.desc_proveedor,
                      cc.numero,
                      dd.m,
                      pla.tipo
                      ORDER BY proveedor
                  ) tvalue_data
                ) jsonD ';

                raise notice '% .',v_consulta;
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

ALTER FUNCTION tes.f_plan_pago_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
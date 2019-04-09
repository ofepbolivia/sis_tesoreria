CREATE OR REPLACE FUNCTION tes.ft_solicitud_plan_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_solicitud_plan_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tplan_pago'
 AUTOR: 		 (admin)
 FECHA:	        12-12-2018 15:43:23
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


BEGIN

	v_nombre_funcion = 'tes.ft_solicitud_plan_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_SOPLAPA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		12-12-2018 15:43:23
	***********************************/

	if(p_transaccion='TES_SOPLAPA_SEL')then

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
                   v_filtro = '(ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or (ew.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||'))    ) and  (lower(plapa.estado)!=''borrador'') and lower(plapa.estado)!=''pagado'' and lower(plapa.estado)!=''devengado'' and lower(plapa.estado)!=''anticipado'' and lower(plapa.estado)!=''aplicado'' and lower(plapa.estado)!=''anulado'' and lower(plapa.estado)!=''devuelto'' and ';
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
            	v_filtro = v_filtro || ' (lower(plapa.estado)=''vbgerente'') and ';
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadpendiente'  THEN
            	IF p_administrador !=1 THEN
              		v_filtro = ' (op.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or op.id_usuario_reg = ' || p_id_usuario||' ) and ';
                END IF;
            	v_filtro = v_filtro || ' lower(plapa.tipo) in (''devengado'',''devengado_pagado'',''devengado_pagado_1c'') and plapa.fecha_conformidad is null and  ';
            END IF;

            IF  lower(v_parametros.tipo_interfaz) = 'planpagoconformidadrealizada'  THEN
              	IF p_administrador !=1 THEN
              		v_filtro = ' (op.id_funcionario  = ' || v_parametros.id_funcionario_usu::varchar || ' or op.id_usuario_reg = ' || p_id_usuario||' ) and ';
                END IF;
            	v_filtro = v_filtro || ' lower(plapa.tipo) in (''devengado'',''devengado_pagado'',''devengado_pagado_1c'') and plapa.fecha_conformidad is not null and  ';
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
                        plapa.porc_monto_retgar,
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
                        plapa.id_proveedor_cta_bancaria,
                        provcue.nro_cuenta as nro_cuenta_prov

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
                        left join tes.tts_libro_bancos lb on plapa.id_int_comprobante = lb.id_int_comprobante
                        left join param.tdepto depc on depc.id_depto = plapa.id_depto_conta
                        left join conta.tint_comprobante tcon on tcon.id_int_comprobante = plapa.id_int_comprobante
                        left join param.tproveedor_cta_bancaria provcue on provcue.id_proveedor_cta_bancaria = plapa.id_proveedor_cta_bancaria
                       where  plapa.estado_reg=''activo''  and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ', nro_cuota ASC limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
--raise exception '%',v_consulta;
             raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

   /*********************************
 	#TRANSACCION:  'TES_SOPLAPA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-12-2018 15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOPLAPA_CONT')then

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
                        left join param.tproveedor_cta_bancaria provcue on provcue.id_proveedor_cta_bancaria = plapa.id_proveedor_cta_bancaria
                      where  plapa.estado_reg=''activo''   and '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

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
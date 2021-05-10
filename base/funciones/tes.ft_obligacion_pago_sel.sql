CREATE OR REPLACE FUNCTION tes.ft_obligacion_pago_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_obligacion_pago_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tobligacion_pago'
 AUTOR: 		Gonzalo Sarmiento Sejas
 FECHA:	        02-04-2013 16:01:32
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
    v_filadd 			varchar;
    va_id_depto 		integer[];
    v_obligaciones      record;
    v_obligaciones_partida	record;
    v_respuesta_verificar	record;
    v_inner 			varchar;
    v_historico         varchar;
    v_strg_sol			varchar;
    v_id_clase_comprobante	integer;

    --variables reporte certificacion presupuestaria
    v_record_op					record;
    v_index						integer;
    v_record					record;
    v_record_funcionario		record;
    v_firmas					varchar[];
    v_firma_fun					varchar;
    v_nombre_entidad			varchar;
    v_direccion_admin			varchar;
    v_unidad_ejecutora			varchar;
    v_cod_proceso				varchar;
    v_cont						integer;
    v_gerencia					varchar;
    v_id_funcionario			integer;

    v_id_gestion				integer;
    v_add_filtro				varchar;
    v_gestion 					integer;
    v_fecha_ini					date;
    v_fecha_fin					date;
    id_partida_ejecucion_raiz   int4;
    v_moneda					varchar;

    v_id_uo						integer;
	v_filtro					varchar;

    -- bvp
    v_proces_wf					integer;
    v_nro_tramite				varchar;
	v_id_estado_wf				integer;
    v_fecha_sol					date;
BEGIN

	v_nombre_funcion = 'tes.ft_obligacion_pago_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_OBPG_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	if(p_transaccion='TES_OBPG_SEL')then

    	begin

          v_filadd='';
          v_inner='';
          v_strg_sol = 'obpg.id_obligacion_pago';

          IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
             v_historico =  v_parametros.historico;
          ELSE
            v_historico = 'no';
          END IF;


         -- 25-02-2021 (may) se aumenta PGAE para pagos de gestion anterior del exterior
         IF   v_parametros.tipo_interfaz in ('obligacionPagoTes','obligacionPagoUnico', 'PGA', 'PPM', 'PCE', 'PBR', 'PGAE') THEN

                 IF   p_administrador != 1 THEN

                   select
                       pxp.aggarray(depu.id_depto)
                    into
                       va_id_depto
                   from param.tdepto_usuario depu
                   where depu.id_usuario =  p_id_usuario;

					if(v_parametros.tipo_interfaz  = 'PGA')then

                    	SELECT tf.id_funcionario
                        into v_id_funcionario
                        FROM segu.tusuario tu
                        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                        WHERE tu.id_usuario = p_id_usuario ;

                       --15-01-2020 (MAY) modificacion para que pueda ver tambien sus asistentes de un funcionario
                       --v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';

                       v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||' or
                          (obpg.id_funcionario  IN (select * FROM orga.f_get_funcionarios_x_usuario_asistente(now()::date,'||p_id_usuario||') AS (id_funcionario INTEGER)))) and ';


                    elsif(v_parametros.tipo_interfaz  = 'PPM')then

                    	SELECT tf.id_funcionario
                        into v_id_funcionario
                        FROM segu.tusuario tu
                        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                        WHERE tu.id_usuario = p_id_usuario ;
                        v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                    elsif(v_parametros.tipo_interfaz  = 'PCE')then

                    	SELECT tf.id_funcionario
                        into v_id_funcionario
                        FROM segu.tusuario tu
                        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                        WHERE tu.id_usuario = p_id_usuario ;
                        v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                   elsif(v_parametros.tipo_interfaz  = 'PBR')then

                      SELECT tf.id_funcionario
                      into v_id_funcionario
                      FROM segu.tusuario tu
                      INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                      WHERE tu.id_usuario = p_id_usuario ;
                      v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                    else
                 		v_filadd='(obpg.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||')) and';
                    end if;

                 END IF;


                IF   v_parametros.tipo_interfaz  = 'obligacionPagoUnico' THEN
                   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_unico'' and';
                ELSIF v_parametros.tipo_interfaz  = 'PGA' THEN
                    v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pga'' and';
                ELSIF v_parametros.tipo_interfaz  = 'PPM' THEN
                	v_filadd=v_filadd ||' obpg.tipo_obligacion = ''ppm'' and';
                ELSIF v_parametros.tipo_interfaz  = 'PCE' THEN
                	v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pce'' and';
         		ELSIF v_parametros.tipo_interfaz  = 'PBR' THEN
                	v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pbr'' and';
                -- 25-02-2021 (may) se aumenta PGAE para pagos de gestion anterior del exterior
                ELSIF v_parametros.tipo_interfaz  = 'PGAE' THEN
                	v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pgaext'' and';
                ELSE
                   v_filadd=v_filadd ||' obpg.tipo_obligacion in (''pago_directo'',''rrhh'') and';
                END IF;



         ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoVb' THEN


              IF v_historico =  'si' THEN
                    v_inner =  '  inner join wf.testado_wf ew on ew.id_proceso_wf = obpg.id_proceso_wf  ';
                    v_strg_sol = 'DISTINCT(obpg.id_obligacion_pago)';
               ELSE
                     v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = obpg.id_estado_wf';


                  IF p_administrador !=1 THEN

                      v_filadd = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(obpg.estado) not in (''borrador'',''en_pago'',''registrado'',''finalizado'',''anulado'')) and ';
                  ELSE
                      v_filadd = ' (lower(obpg.estado) not in (''borrador'',''en_pago'',''registrado'',''finalizado'',''anulado'')) and ';
                  END IF;
              END IF;


         ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoVbPoa' THEN

              IF v_historico = 'no' THEN
                 v_filadd=' (obpg.estado = ''vbpoa'') and';
              ELSE
                 v_filadd=' (obpg.estado not in  (''borrador'')) and';
              END IF;

         ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoConsulta' THEN
            --no hay limitaciones ...
         ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoApropiacion' THEN
            --no hay limitaciones ...
         ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoConta' THEN
            --no hay limitaciones ...

         --(28-01-2020) MAY nuevo filtro consulta obligaciones de pago para cada gerencia
         ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoConsultaGerencia' THEN

          		--busca id_uo del usuario
                  SELECT uo.id_uo
                  INTO v_id_uo
                  FROM orga.tfuncionario f
                  inner join segu.tusuario usu on usu.id_persona = f.id_persona
                  inner join orga.tuo_funcionario func on func.id_funcionario = f.id_funcionario and func.estado_reg = 'activo'
                  inner join orga.tuo uo on uo.estado_reg='activo' and uo.id_uo = tes.f_get_uo_gerencia_proceso(func.id_uo,null::integer,null::date)
                  WHERE usu.id_usuario= p_id_usuario;


             v_inner = '   inner JOIN orga.tuo_funcionario uof on uof.id_funcionario = obpg.id_funcionario and uof.tipo = ''oficial'' and uof.estado_reg = ''activo'' and (current_date <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)
			               inner JOIN orga.tuo tuo on tuo.id_uo = tes.f_get_uo_gerencia_proceso(uof.id_uo,null::integer,null::date)  ';

             v_filadd = ' tuo.id_uo = '||v_id_uo||'  and  ';

         --
         --05-02-2020 (MAY) filtro para interfaz para procesos de gestion materiales en obligaciones de pago
         ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoGestionMat' THEN

           		v_filadd= ' obpg.tipo_obligacion = ''adquisiciones'' and  obpg.tipo_solicitud = ''Boa'' and ';
         --

         ELSE

              -- SI LA INTERFACE VIENE DE ADQUISIONES

              IF   p_administrador != 1 THEN
                     select
                         pxp.aggarray(depu.id_depto)
                      into
                         va_id_depto
                     from param.tdepto_usuario depu
                     where depu.id_usuario =  p_id_usuario and depu.cargo in  ('responsable', 'auxiliar');

               --(may)

                        SELECT tf.id_funcionario
                        INTO v_id_funcionario
                        FROM segu.tusuario tu
                        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                        WHERE tu.id_usuario = p_id_usuario ;


                        v_filadd = ' (pc.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||')  or obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||' or sol.id_usuario_reg = '||p_id_usuario||') and ';

				--
              END IF;


              v_inner = '
                            inner join adq.tcotizacion cot on cot.id_obligacion_pago = obpg.id_obligacion_pago
                            inner join adq.tproceso_compra pc on pc.id_proceso_compra = cot.id_proceso_compra
                            inner join adq.tsolicitud sol on sol.id_solicitud = pc.id_solicitud ';
        END IF;


--raise exception 'ññeja %',v_filadd;
                  --Sentencia de la consulta
                  v_consulta:='select
                              '||v_strg_sol||',
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
                              obpg.codigo_poa,
                              obpg.obs_poa,
                              obpg.uo_ex,
                              obpg.id_funcionario_responsable,
							  fresp.desc_funcionario1 AS desc_fun_responsable,
                              conf.id_conformidad,
                              conf.conformidad_final,
                              conf.fecha_conformidad_final::date,
                              conf.fecha_inicio::date,
                              conf.fecha_fin::date,
                              conf.observaciones,
                              obpg.fecha_certificacion_pres,
                              obpg.presupuesto_aprobado,
                              obpg.nro_preventivo

                              from tes.tobligacion_pago obpg
                              inner join segu.tusuario usu1 on usu1.id_usuario = obpg.id_usuario_reg
                              left join segu.tusuario usu2 on usu2.id_usuario = obpg.id_usuario_mod
                              inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                              inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
                              inner join param.tdepto dep on dep.id_depto=obpg.id_depto

                              left join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                              left join leg.tcontrato con on con.id_contrato = obpg.id_contrato
                              left join param.tplantilla pla on pla.id_plantilla = obpg.id_plantilla
                              '||v_inner ||'
                              left join orga.vfuncionario fun on fun.id_funcionario=obpg.id_funcionario
                              left join orga.vfuncionario fresp ON fresp.id_funcionario = obpg.id_funcionario_responsable

                              left join tes.tconformidad conf on conf.id_obligacion_pago = obpg.id_obligacion_pago

                              where  '||v_filadd;

                  --Definicion de la respuesta
                  v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;


            --raise exception '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'TES_OBPG_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPG_CONT')then

		begin

              v_filadd='';
              v_inner='';
              v_strg_sol = 'obpg.id_obligacion_pago';

              IF  pxp.f_existe_parametro(p_tabla,'historico') THEN
                 v_historico =  v_parametros.historico;
              ELSE
                v_historico = 'no';
              END IF;

             -- 25-02-2021 (may) se aumenta PGAE para pagos de gestion anterior del exterior
             IF   v_parametros.tipo_interfaz in ('obligacionPagoTes','obligacionPagoUnico', 'PGA', 'PPM', 'PCE', 'PBR', 'PGAE') THEN

                     IF   p_administrador != 1 THEN

                       select
                           pxp.aggarray(depu.id_depto)
                        into
                           va_id_depto
                       from param.tdepto_usuario depu
                       where depu.id_usuario =  p_id_usuario;

                        if(v_parametros.tipo_interfaz  = 'PGA')then
                          SELECT tf.id_funcionario
                          into v_id_funcionario
                          FROM segu.tusuario tu
                          INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                          WHERE tu.id_usuario = p_id_usuario ;

                         v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                        elsif(v_parametros.tipo_interfaz  = 'PPM')then
                          SELECT tf.id_funcionario
                          into v_id_funcionario
                          FROM segu.tusuario tu
                          INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                          WHERE tu.id_usuario = p_id_usuario ;
                          v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                        elsif(v_parametros.tipo_interfaz  = 'PCE')then

                          SELECT tf.id_funcionario
                          into v_id_funcionario
                          FROM segu.tusuario tu
                          INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                          WHERE tu.id_usuario = p_id_usuario ;
                          v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                        elsif(v_parametros.tipo_interfaz  = 'PBR')then

                          SELECT tf.id_funcionario
                          into v_id_funcionario
                          FROM segu.tusuario tu
                          INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                          WHERE tu.id_usuario = p_id_usuario ;
                          v_filadd = v_filadd ||'(obpg.id_funcionario = '||v_id_funcionario||' or obpg.id_usuario_reg = '||p_id_usuario||') and ';
                        else
                            v_filadd='(obpg.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||')) and';
                        end if;


                    END IF;


                    IF   v_parametros.tipo_interfaz  = 'obligacionPagoUnico' THEN
                       v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_unico'' and';
                    ELSIF v_parametros.tipo_interfaz  = 'PGA' THEN
                   	   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pga'' and';
                    ELSIF v_parametros.tipo_interfaz  = 'PPM' THEN
                   	   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''ppm'' and';
                    ELSIF v_parametros.tipo_interfaz  = 'PCE' THEN
                	   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pce'' and';
                    ELSIF v_parametros.tipo_interfaz  = 'PBR' THEN
                	   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pbr'' and';
                    -- 25-02-2021 (may) se aumenta PGAE para pagos de gestion anterior del exterior
                    ELSIF v_parametros.tipo_interfaz  = 'PGAE' THEN
                        v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pgaext'' and';
                    ELSE
                       v_filadd=v_filadd ||' obpg.tipo_obligacion in (''pago_directo'',''rrhh'') and';
                    END IF;



             ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoVb' THEN


                  IF v_historico =  'si' THEN
                        v_inner =  '  inner join wf.testado_wf ew on ew.id_proceso_wf = obpg.id_proceso_wf  ';
                        v_strg_sol = 'DISTINCT(obpg.id_obligacion_pago)';
                   ELSE
                         v_inner =  'inner join wf.testado_wf ew on ew.id_estado_wf = obpg.id_estado_wf';


                    IF p_administrador !=1 THEN
                        v_filadd = ' (ew.id_funcionario='||v_parametros.id_funcionario_usu::varchar||' ) and  (lower(obpg.estado) not in (''borrador'',''en_pago'',''registrado'',''finalizado'',''anulado'')) and ';
                    ELSE
                        v_filadd = ' (lower(obpg.estado) not in (''borrador'',''en_pago'',''registrado'',''finalizado'',''anulado'')) and ';
                    END IF;


                  END IF;

             ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoVbPoa' THEN

                  IF v_historico = 'no' THEN
                     v_filadd=' (obpg.estado = ''vbpoa'') and';
                  ELSE
                     v_filadd=' (obpg.estado not in  (''borrador'')) and';
                  END IF;

             ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoConsulta' THEN
                --no hay limitaciones ...
             ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoApropiacion' THEN
                --no hay limitaciones ...
             ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoConta' THEN
                --no hay limitaciones ...

            --(28-01-2020) MAY nuevo filtro consulta obligaciones de pago para cada gerencia
         ELSIF  v_parametros.tipo_interfaz =  'ObligacionPagoConsultaGerencia' THEN

          		--busca id_uo del usuario
                  SELECT uo.id_uo
                  INTO v_id_uo
                  FROM orga.tfuncionario f
                  inner join segu.tusuario usu on usu.id_persona = f.id_persona
                  inner join orga.tuo_funcionario func on func.id_funcionario = f.id_funcionario and func.estado_reg = 'activo'
                  inner join orga.tuo uo on uo.estado_reg='activo' and uo.id_uo = tes.f_get_uo_gerencia_proceso(func.id_uo,null::integer,null::date)
                  WHERE usu.id_usuario= p_id_usuario;


             v_inner = '   inner JOIN orga.tuo_funcionario uof on uof.id_funcionario = obpg.id_funcionario and uof.tipo = ''oficial'' and uof.estado_reg = ''activo'' and (current_date <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)
			               inner JOIN orga.tuo tuo on tuo.id_uo = tes.f_get_uo_gerencia_proceso(uof.id_uo,null::integer,null::date)  ';

             v_filadd = ' tuo.id_uo = '||v_id_uo||'  and  ';

             --

             --05-02-2020 (MAY) filtro para interfaz para procesos de gestion materiales en obligaciones de pago
             ELSIF v_parametros.tipo_interfaz =  'ObligacionPagoGestionMat' THEN

                    v_filadd= ' obpg.tipo_obligacion = ''adquisiciones'' and  obpg.tipo_solicitud = ''Boa'' and ';
             --

             ELSE

                  -- SI LA NTERFACE VIENE DE ADQUISIONES

                  IF   p_administrador != 1 THEN
                       select
                             pxp.aggarray(depu.id_depto)
                          into
                             va_id_depto
                         from param.tdepto_usuario depu
                         where depu.id_usuario =  p_id_usuario and depu.cargo = 'responsable';


                         v_filadd='( (pc.id_depto  in ('|| COALESCE(array_to_string(va_id_depto,','),'0')||'))   or   pc.id_usuario_auxiliar = '||p_id_usuario::varchar ||' ) and ';
                  END IF;


                  v_inner = '
                                inner join adq.tcotizacion cot on cot.id_obligacion_pago = obpg.id_obligacion_pago
                                inner join adq.tproceso_compra pc on pc.id_proceso_compra = cot.id_proceso_compra  ';
            END IF;


			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count('||v_strg_sol||')
					    from tes.tobligacion_pago obpg
						inner join segu.tusuario usu1 on usu1.id_usuario = obpg.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = obpg.id_usuario_mod
                        inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                        inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
                        inner join param.tdepto dep on dep.id_depto=obpg.id_depto

                        left join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                        left join leg.tcontrato con on con.id_contrato = obpg.id_contrato
                        left join param.tplantilla pla on pla.id_plantilla = obpg.id_plantilla
                        '|| v_inner ||'
                        left join orga.vfuncionario fun on fun.id_funcionario=obpg.id_funcionario
                        left join orga.vfuncionario fresp ON fresp.id_funcionario = obpg.id_funcionario_responsable
                        where  '||v_filadd;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta

            raise notice '%',v_consulta;
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'TES_OBPGSOL_SEL'
 	#DESCRIPCION:	Consulta de obligaciones de pagos por solicitante
 	#AUTOR:	Rensi Arteaga Copari
 	#FECHA:		08-05-2014 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPGSOL_SEL')then

    	begin

          v_filadd='';
          v_inner='';


          IF  v_parametros.tipo_interfaz !=  'ObligacionPagoConta' THEN
            --no hay limitaciones ...
           IF   p_administrador != 1 THEN
            	--(maylee.perez) para internacionales la vista de los procesos que van desde la estacion central
                IF   v_parametros.tipo_interfaz  = 'obligacionPagoInterS' THEN
                       v_filadd='';
                ELSE

                   v_filadd = '(obpg.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or obpg.id_usuario_reg='||p_id_usuario||' ) and ';
           		END IF;
           END IF;
          END IF;

         IF  v_parametros.tipo_interfaz in ('obligacionPagoSol', 'obligacionPagoUnico','obligacionPagoEspecial', 'solicitudSinImputacionPresupuestaria') THEN

                IF   v_parametros.tipo_interfaz  = 'obligacionPagoUnico' THEN
                   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_unico'' and ';

                ELSIF   v_parametros.tipo_interfaz  = 'obligacionPagoEspecial' THEN
                   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_especial'' and ';

                ELSIF   v_parametros.tipo_interfaz  = 'solicitudSinImputacionPresupuestaria' THEN
                   v_filadd= ' obpg.tipo_obligacion = ''pago_especial_spi'' and ';
                ELSE
                   v_filadd=v_filadd ||' obpg.tipo_obligacion in (''pago_directo'',''rrhh'') and';

                END IF;



         END IF;
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
                              obpg.presupuesto_aprobado

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
                              where  '||v_filadd;

                  --Definicion de la respuesta
                  v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;







              raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

     /*********************************
 	#TRANSACCION:  'TES_OBPGSOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:	 RAC (KPLIAN)
 	#FECHA:		04-05-2014 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPGSOL_CONT')then

		begin

            v_filadd='';
            v_inner='';

             IF  v_parametros.tipo_interfaz !=  'ObligacionPagoConta' THEN
                --no hay limitaciones ...
                IF   p_administrador != 1 THEN
                       v_filadd = '(obpg.id_funcionario='||v_parametros.id_funcionario_usu::varchar||'  or obpg.id_usuario_reg='||p_id_usuario||' ) and ';
                END IF;
             END IF;

             IF  v_parametros.tipo_interfaz in ('obligacionPagoSol', 'obligacionPagoUnico','obligacionPagoEspecial') THEN

                  IF   v_parametros.tipo_interfaz  = 'obligacionPagoUnico' THEN
                     v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_unico'' and ';
                  ELSIF   v_parametros.tipo_interfaz  = 'obligacionPagoEspecial' THEN
                   v_filadd=v_filadd ||' obpg.tipo_obligacion = ''pago_especial'' and ';
                  ELSE
                     v_filadd=v_filadd ||' obpg.tipo_obligacion in (''pago_directo'',''rrhh'') and';
                  END IF;
             END IF ;

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
                        where  '||v_filadd;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta

            raise notice '%',v_consulta;
			return v_consulta;

		end;

   /*********************************
 	#TRANSACCION:  'TES_ESTOBPG_SEL'
 	#DESCRIPCION:	Consulta de registros para los reportes
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		31-05-2013
	***********************************/
	elsif (p_transaccion='TES_ESTOBPG_SEL')then
    	begin
         create temporary table flujo_obligaciones(
        	funcionario text,
            nombre text,
            nombre_estado varchar,
            fecha_reg date,
            id_tipo_estado int4,
            id_estado_wf int4,
            id_estado_anterior int4
        ) on commit drop;

    	--recupera el flujo de control de las obligaciones

    	FOR v_obligaciones IN(
            select op.id_estado_wf
            from tes.tobligacion_pago op
            where op.id_obligacion_pago=v_parametros.id_obligacion_pago
        )LOOP
        		raise  notice 'estasd %', v_obligaciones.id_estado_wf;
        	   INSERT INTO flujo_obligaciones(
               WITH RECURSIVE estados_obligaciones(id_depto, id_proceso_wf, id_tipo_estado,id_estado_wf, id_estado_anterior, fecha_reg)AS(
                   SELECT et.id_depto, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
                   FROM wf.testado_wf et
                   WHERE et.id_estado_wf=v_obligaciones.id_estado_wf
                UNION ALL
                   SELECT et.id_depto, et.id_proceso_wf, et.id_tipo_estado, et.id_estado_wf, et.id_estado_anterior, et.fecha_reg
                   FROM wf.testado_wf et, estados_obligaciones
                   WHERE et.id_estado_wf=estados_obligaciones.id_estado_anterior
                )SELECT dep.nombre::text, tp.nombre||'-'||prv.desc_proveedor, te.nombre_estado, eo.fecha_reg, eo.id_tipo_estado, eo.id_estado_wf, COALESCE(eo.id_estado_anterior,NULL) as id_estado_anterior
                 FROM estados_obligaciones eo
                 INNER JOIN wf.ttipo_estado te on te.id_tipo_estado= eo.id_tipo_estado
                 INNER JOIN wf.tproceso_wf pwf on pwf.id_proceso_wf=eo.id_proceso_wf
                 INNER JOIN wf.ttipo_proceso tp on tp.id_tipo_proceso=pwf.id_tipo_proceso
                 INNER JOIN tes.tobligacion_pago op on op.id_proceso_wf=pwf.id_proceso_wf
                 INNER JOIN param.vproveedor prv on prv.id_proveedor=op.id_proveedor
                 LEFT JOIN param.tdepto dep on dep.id_depto=eo.id_depto
                 ORDER BY eo.id_estado_wf ASC
                 );
        END LOOP;

              v_consulta:='select * from flujo_obligaciones';
              --Devuelve la respuesta
              return v_consulta;


        end;



     /*********************************
 	#TRANSACCION:  'TES_OBPGSEL_SEL'
 	#DESCRIPCION:	Reporte de Obligacion Seleccionado
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		17-07-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPGSEL_SEL')then
      begin
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
						obpg.porc_anticipo,
						obpg.id_depto,
                        dep.nombre as nombre_depto,
						obpg.num_tramite,
                        obpg.fecha,
                        obpg.numero,
                        obpg.tipo_cambio_conv,
                        obpg.comprometido,
                        obpg.nro_cuota_vigente,
                        mn.tipo_moneda,
                        obpg.pago_variable
						from tes.tobligacion_pago obpg
                        inner join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                        inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                        inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
						inner join param.tdepto dep on dep.id_depto=obpg.id_depto
                        where obpg.id_obligacion_pago='||v_parametros.id_obligacion_pago||'';

            --Definicion de la respuesta
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
      end;


    /*********************************
 	#TRANSACCION:  'TES_PROCP_SEL'
 	#DESCRIPCION:	Reporte de procesos pendientes
 	#AUTOR:		MAM
 	#FECHA:		05-12-20116 16:01:32
	***********************************/

    elsif(p_transaccion='TES_PROCP_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='with estadoPlanPago as (select op.num_tramite,p.nro_cuota,p.liquido_pagable, p.estado, op.id_obligacion_pago, p.fecha_tentativa, p.tipo
						from tes.tobligacion_pago op
						left join tes.tplan_pago p on p.id_obligacion_pago = op.id_obligacion_pago and p.estado_reg = ''activo''
						and p.estado != ''anulado'')
					SELECT
                    obl.num_tramite,
                    obl.id_obligacion_pago,
                    obl.estado,
                    to_char(obl.fecha,''DD/MM/YYYY''),
                    fu.desc_funcionario1,
                    pr.desc_proveedor,
                    obl.total_pago,
                    m.moneda,
                    es.estado as estado_pago,
                    es.nro_cuota,
                    es.liquido_pagable,
                    obl.obs,
                    to_char(es.fecha_tentativa,''DD/MM/YYYY'') as fecha_tentativa ,
                    per.nombre_completo1 as nombre,
                    de.nombre as nombre_depto,
                    obl.pago_variable,
                    es.tipo
                    from tes.tobligacion_pago obl
                    inner join segu.tusuario usu1 on usu1.id_usuario = obl.id_usuario_reg
                    inner join segu.vpersona per on per.id_persona = usu1.id_persona
                    inner join orga.vfuncionario fu on fu.id_funcionario = obl.id_funcionario
                    inner join param.vproveedor pr on pr.id_proveedor = obl.id_proveedor
                    inner join param.tmoneda m on m.id_moneda = obl.id_moneda
                    left join estadoPlanPago es on es.id_obligacion_pago = obl.id_obligacion_pago
                    inner join param.tdepto de on de.id_depto = obl.id_depto
                    inner join param.tproveedor pro on pro.id_proveedor = obl.id_proveedor
                    where  obl.fecha >= '''||v_parametros.fecha_ini||''' and obl.fecha <= '''||v_parametros.fecha_fin||'''
                    and obl.estado IN(''borrador'',''registrado'',''en_pago'',''anulado'')';
                    v_consulta:=v_consulta||'ORDER BY num_tramite, obl.num_tramite, es.nro_cuota ASC';

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'TES_PAGSINDOC_SEL'
 	#DESCRIPCION:	Reporte de pagos sin documentos relacionados
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		03-03-2017 16:01:32
	***********************************/

    elsif(p_transaccion='TES_PAGSINDOC_SEL')then

    	begin
        	select cla.id_clase_comprobante into v_id_clase_comprobante
			from conta.tclase_comprobante cla
			where cla.codigo='DIARIO';

    		--Sentencia de la consulta
			v_consulta:='select dev.id_int_comprobante, dev.nro_tramite, dev.c31, dev.beneficiario, dev.glosa1
						from conta.tint_comprobante pago
						inner join conta.tint_comprobante dev on dev.id_int_comprobante = ANY(pago.id_int_comprobante_fks)
        				and dev.id_clase_comprobante = 3
					    and conta.f_recuperar_nro_documento_facturas_comprobante(dev.id_int_comprobante) is  null
						where dev.id_clase_comprobante ='|| v_id_clase_comprobante ||' and
		      			pago.fecha between '''||v_parametros.fecha_ini||''' and
      					'''||v_parametros.fecha_fin||''' and
      					pago.estado_reg = ''validado'' and
      					pago.id_moneda = (select id_moneda from param.tmoneda where tipo_moneda=''base'')';

            raise notice '% ',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

     /*********************************
 	#TRANSACCION:  'TES_COMEJEPAG_SEL'
 	#DESCRIPCION:	Reporte de Comprometido Ejecutado Pagado
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		17-07-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_COMEJEPAG_SEL')then
        begin
    		--Sentencia de la consulta
            create temp table obligaciones(
                    id_obligacion_det 	integer,
                    id_partida			integer,
                    nombre_partida		text,
                    id_concepto_ingas	integer,
                    nombre_ingas			text,
                    id_obligacion_pago	integer,
                    id_centro_costo		integer,
                    codigo_cc			text,
                    id_partida_ejecucion_com	integer,
                    descripcion			text,
                    comprometido		numeric DEFAULT 0.00,
                    ejecutado			numeric DEFAULT 0.00,
                    pagado				numeric DEFAULT 0.00,
                    revertible			numeric DEFAULT 0.00,
                    revertir			numeric,
                    moneda 				varchar,
                    desc_orden			varchar
            ) on commit drop;

            insert into obligaciones (id_obligacion_det,
                                      id_partida,
                                      nombre_partida,
            						  id_concepto_ingas,
                                      nombre_ingas,
                                      id_obligacion_pago,
                                      id_centro_costo,
                                      codigo_cc,
                                      id_partida_ejecucion_com,
                                      descripcion,
                                      desc_orden)
            select
                obdet.id_obligacion_det,
                obdet.id_partida,
                par.nombre_partida||'-('||par.codigo||')' as nombre_partida,
                obdet.id_concepto_ingas,
                cig.desc_ingas||'-('||cig.movimiento||')' as nombre_ingas,
                obdet.id_obligacion_pago,
                obdet.id_centro_costo,
                cc.codigo_cc,
                obdet.id_partida_ejecucion_com,
                obdet.descripcion,
                ort.desc_orden
           from tes.tobligacion_det obdet
                inner join param.vcentro_costo cc on cc.id_centro_costo=obdet.id_centro_costo
                inner join segu.tusuario usu1 on usu1.id_usuario = obdet.id_usuario_reg
                inner join pre.tpartida par on par.id_partida=obdet.id_partida
                inner join param.tconcepto_ingas cig on cig.id_concepto_ingas=obdet.id_concepto_ingas
                inner join conta.torden_trabajo ort on ort.id_orden_trabajo = obdet.id_orden_trabajo
            where obdet.id_obligacion_pago=v_parametros.id_obligacion_pago;

            --raise exception 'Moneda %', v_parametros.id_moneda ;
            select moneda into v_moneda
            from param.tmoneda
            where id_moneda = v_parametros.id_moneda;

			FOR v_obligaciones_partida in (select * from obligaciones)
       	    LOOP
            	v_respuesta_verificar = pre.f_verificar_com_eje_pag(v_obligaciones_partida.id_partida_ejecucion_com,v_parametros.id_moneda);



            	update obligaciones set
                comprometido = COALESCE(v_respuesta_verificar.ps_comprometido,0.00::numeric),
                ejecutado = COALESCE(v_respuesta_verificar.ps_ejecutado,0.00::numeric),
                pagado = COALESCE(v_respuesta_verificar.ps_pagado,0.00::numeric),
                revertible =  COALESCE(v_respuesta_verificar.ps_comprometido,0.00::numeric) - COALESCE(v_respuesta_verificar.ps_ejecutado,0.00::numeric),
                moneda = v_moneda
                where obligaciones.id_obligacion_det=v_obligaciones_partida.id_obligacion_det;

        	END LOOP;

            --raise exception 'Moneda %', v_parametros.id_moneda ;

              v_consulta:='select * from obligaciones where  ';

              --Definicion de la respuesta
              v_consulta:=v_consulta||v_parametros.filtro;
              v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion;


			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'TES_REPCERPRE_SEL'
 	#DESCRIPCION:	Reporte Certificación Presupuestaria
 	#AUTOR:		FEA
 	#FECHA:		02-08-2017 15:00
	***********************************/

	elsif(p_transaccion='TES_REPCERPRE_SEL')then

		begin

        	SELECT top.id_funcionario
            INTO v_id_funcionario
            FROM tes.tobligacion_pago top
            WHERE top.id_proceso_wf =  v_parametros.id_proceso_wf;

        	--Gerencia del funcionario solicitante
        	/*WITH RECURSIVE gerencia(id_uo, id_nivel_organizacional, nombre_unidad, nombre_cargo, codigo) AS (
              SELECT tu.id_uo, tu.id_nivel_organizacional, tu.nombre_unidad, tu.nombre_cargo, tu.codigo
              FROM orga.tuo  tu
              INNER JOIN orga.tuo_funcionario tf ON tf.id_uo = tu.id_uo
              WHERE tf.id_funcionario = v_id_funcionario and tu.estado_reg = 'activo'

              UNION ALL

              SELECT teu.id_uo_padre, tu1.id_nivel_organizacional, tu1.nombre_unidad, tu1.nombre_cargo, tu1.codigo
              FROM orga.testructura_uo teu
              INNER JOIN gerencia g ON g.id_uo = teu.id_uo_hijo
              INNER JOIN orga.tuo tu1 ON tu1.id_uo = teu.id_uo_padre
              WHERE substring(g.nombre_cargo,1,7) <> 'Gerente'
          	)

            SELECT (codigo||'-'||nombre_unidad)::varchar
            INTO v_gerencia
            FROM gerencia
            ORDER BY id_nivel_organizacional asc limit 1;*/
            --end gerencia

            SELECT top.estado, top.id_estado_wf, top.obs, top.id_gestion
            INTO v_record_op
            FROM tes.tobligacion_pago top
            WHERE top.id_proceso_wf = v_parametros.id_proceso_wf;


            SELECT tpo.nombre
            INTO v_cod_proceso
            FROM wf.tproceso_wf tpw
            INNER JOIN wf.ttipo_proceso ttp ON ttp.id_tipo_proceso = tpw.id_tipo_proceso
            INNER JOIN wf.tproceso_macro tpo ON tpo.id_proceso_macro = ttp.id_proceso_macro
            WHERE tpw.id_proceso_wf = v_parametros.id_proceso_wf;

            IF(v_record_op.estado IN ('vbpresupuestos', 'suppresu', 'registrado', 'en_pago', 'finalizado'))THEN
              v_index = 1;
              FOR v_record IN (WITH RECURSIVE firmas(id_estado_fw, id_estado_anterior,fecha_reg, codigo, id_funcionario) AS (
                                SELECT tew.id_estado_wf, tew.id_estado_anterior , tew.fecha_reg, te.codigo, tew.id_funcionario
                                FROM wf.testado_wf tew
                                INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = tew.id_tipo_estado
                                WHERE tew.id_estado_wf = v_record_op.id_estado_wf

                                UNION ALL

                                SELECT ter.id_estado_wf, ter.id_estado_anterior, ter.fecha_reg, te.codigo, ter.id_funcionario
                                FROM wf.testado_wf ter
                                INNER JOIN firmas f ON f.id_estado_anterior = ter.id_estado_wf
                                INNER JOIN wf.ttipo_estado te ON te.id_tipo_estado = ter.id_tipo_estado
                                WHERE f.id_estado_anterior IS NOT NULL
                            )SELECT distinct on (codigo) codigo, fecha_reg , id_estado_fw, id_estado_anterior, id_funcionario FROM firmas ORDER BY codigo, fecha_reg DESC) LOOP

                  IF(v_record.codigo = 'vbpoa' OR v_record.codigo = 'suppresu' OR v_record.codigo = 'vbpresupuestos' OR v_record.codigo = 'registrado')THEN
                    	SELECT vf.desc_funcionario1, vf.nombre_cargo, vf.oficina_nombre
                        INTO v_record_funcionario
                        FROM orga.vfuncionario_cargo_lugar_todos vf
                        WHERE vf.id_funcionario = v_record.id_funcionario;
                        v_firmas[v_index] = v_record.codigo::VARCHAR||','||v_record.fecha_reg::VARCHAR||','||v_record_funcionario.desc_funcionario1::VARCHAR||','||v_record_funcionario.nombre_cargo::VARCHAR||','||v_record_funcionario.oficina_nombre;
                        v_index = v_index + 1;
                  END IF;
              END LOOP;
            	v_firma_fun = array_to_string(v_firmas,';');
            ELSE
            	v_firma_fun = '';
        	END IF;
        	------
            SELECT (''||te.codigo||' '||te.nombre)::varchar
            INTO v_nombre_entidad
            FROM param.tempresa te;
            ------
            SELECT (''||tda.codigo||' '||tda.nombre)::varchar
            INTO v_direccion_admin
            FROM pre.tdireccion_administrativa tda;
			------
            SELECT (''||tue.codigo||' '||tue.nombre)::varchar
            INTO v_unidad_ejecutora
            FROM pre.tunidad_ejecutora tue;
            ---

			--Sentencia de la consulta de conteo de registros
			--26-04-2021(may) modificacion parte FROM current_date
            --inner JOIN orga.tuo_funcionario uof ON uof.id_funcionario = ts.id_funcionario and uof.tipo = ''oficial'' and uof.estado_reg = ''activo'' and (current_date <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)
            --30-04-2021(may) se aumento parte FROM uof.fecha_asignacion <= ts.fecha and
            --inner JOIN orga.tuo_funcionario uof ON uof.id_funcionario = ts.id_funcionario and uof.tipo = 'oficial' and uof.estado_reg = 'activo' and (ts.fecha <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null)

			v_consulta:='
            SELECT
            	vcp.id_categoria_programatica AS id_cp, ttc.codigo AS centro_costo,
            	vcp.codigo_programa , vcp.codigo_proyecto, vcp.codigo_actividad,
            	vcp.codigo_fuente_fin, vcp.codigo_origen_fin, tpar.codigo AS codigo_partida,
            	tpar.nombre_partida AS nombre_partida, tcg.codigo AS codigo_cg, tcg.nombre AS nombre_cg,
            	sum(tsd.monto_pago_mo) AS monto_pago, tmo.codigo AS codigo_moneda, ts.num_tramite,

            '''||v_nombre_entidad||'''::varchar AS nombre_entidad,
            COALESCE('''||v_direccion_admin||'''::varchar, '''') AS direccion_admin,

            coalesce(vcp.desc_unidad_ejecutora::varchar,''Boliviana de Aviación - BoA''::varchar) as unidad_ejecutora,
            coalesce(vcp.codigo_unidad_ejecutora::varchar,''0''::varchar) as codigo_ue,
            COALESCE('''||v_firma_fun||'''::varchar, '''') AS firmas,
            COALESCE('''||v_record_op.obs||'''::varchar,'''') AS justificacion,
            COALESCE(tet.codigo::varchar,''00''::varchar) AS codigo_transf,
            (''(''||tuo.codigo||'')''||tuo.nombre_unidad)::varchar as unidad_solicitante,
            fun.desc_funcionario1::varchar as funcionario_solicitante,
            '''||v_cod_proceso||'''::varchar AS codigo_proceso,

            COALESCE(ts.fecha,null::date) AS fecha_soli,
            COALESCE(tg.gestion, (extract(year from now()::date))::integer) AS gestion,
            ts.codigo_poa,
            (select  pxp.list(distinct ob.codigo|| '' ''||ob.descripcion||'' '')
            from pre.tobjetivo ob
            where ob.codigo = ANY (string_to_array(ts.codigo_poa,'','')) and ob.id_gestion = '||v_record_op.id_gestion||'
            )::varchar as codigo_descripcion,
            ts.tipo_obligacion,
			ts.fecha_certificacion_pres
            FROM tes.tobligacion_pago ts
            INNER JOIN tes.tobligacion_det tsd ON tsd.id_obligacion_pago = ts.id_obligacion_pago
            INNER JOIN pre.tpartida tpar ON tpar.id_partida = tsd.id_partida

            inner join param.tgestion tg on tg.id_gestion = ts.id_gestion

            --INNER JOIN pre.tpresup_partida tpp ON tpp.id_partida = tpar.id_partida AND tpp.id_centro_costo = tsd.id_centro_costo

            INNER JOIN param.tcentro_costo tcc ON tcc.id_centro_costo = tsd.id_centro_costo
            INNER JOIN param.ttipo_cc ttc ON ttc.id_tipo_cc = tcc.id_tipo_cc

            INNER JOIN pre.tpresupuesto	tp ON tp.id_presupuesto = tsd.id_centro_costo --tpp.id_presupuesto
            INNER JOIN pre.vcategoria_programatica vcp ON vcp.id_categoria_programatica = tp.id_categoria_prog

            INNER JOIN pre.tclase_gasto_partida tcgp ON tcgp.id_partida = tpar.id_partida --tpp.id_partida
            INNER JOIN pre.tclase_gasto tcg ON tcg.id_clase_gasto = tcgp.id_clase_gasto

            INNER JOIN param.tmoneda tmo ON tmo.id_moneda = ts.id_moneda

            inner join orga.vfuncionario fun on fun.id_funcionario = ts.id_funcionario

            inner JOIN orga.tuo_funcionario uof ON uof.id_funcionario = ts.id_funcionario and uof.tipo = ''oficial''  and (uof.fecha_asignacion <= ts.fecha and (ts.fecha <= uof.fecha_finalizacion or  uof.fecha_finalizacion is null))
			inner JOIN orga.tuo tuo on tuo.id_uo = orga.f_get_uo_gerencia(uof.id_uo,uof.id_funcionario,current_date)

            left JOIN pre.tpresupuesto_partida_entidad tppe ON tppe.id_partida = tpar.id_partida AND tppe.id_presupuesto = tp.id_presupuesto
            left JOIN pre.tentidad_transferencia tet ON tet.id_entidad_transferencia = tppe.id_entidad_transferencia

            WHERE tsd.estado_reg = ''activo''
            AND uof.estado_reg = ''activo''
            AND ts.id_proceso_wf = '||v_parametros.id_proceso_wf;

			v_consulta =  v_consulta ||
            ' GROUP BY vcp.id_categoria_programatica, tpar.codigo, ttc.codigo, vcp.codigo_programa,
            vcp.codigo_proyecto, vcp.codigo_actividad, vcp.codigo_fuente_fin, vcp.codigo_origen_fin,
    		tpar.nombre_partida, tcg.codigo, tcg.nombre, tmo.codigo, ts.num_tramite, tet.codigo,
    		funcionario_solicitante, ts.fecha, tg.gestion, ts.codigo_poa, tuo.codigo, tuo.nombre_unidad, ts.tipo_obligacion, ts.fecha_certificacion_pres,
    		vcp.desc_unidad_ejecutora, vcp.codigo_unidad_ejecutora ';
			v_consulta =  v_consulta || 'ORDER BY tpar.codigo, tcg.nombre, vcp.id_categoria_programatica, ttc.codigo asc  ';
			--Devuelve la respuesta
            RAISE NOTICE 'v_consulta %',v_consulta;
			return v_consulta;

        end;
   /*********************************
 	#TRANSACCION:  'TES_PAGSINDOC_SEL'
 	#DESCRIPCION:	Reporte Solicitud de Costos
 	#AUTOR:		Franklin Espinoza Alvarez
 	#FECHA:		31-01-2018 16:01:32
	***********************************/

    elsif(p_transaccion='TES_REPSC_SEL')then

    	begin
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
						obpg.porc_anticipo,
						obpg.id_depto,
                        dep.nombre as nombre_depto,
						obpg.num_tramite,
                        obpg.fecha,
                        obpg.numero,
                        obpg.tipo_cambio_conv,
                        obpg.comprometido,
                        obpg.nro_cuota_vigente,
                        mn.tipo_moneda,
                        obpg.pago_variable
						from tes.tobligacion_pago obpg
                        inner join param.vproveedor pv on pv.id_proveedor=obpg.id_proveedor
                        inner join param.tmoneda mn on mn.id_moneda=obpg.id_moneda
                        inner join segu.tsubsistema ss on ss.id_subsistema=obpg.id_subsistema
						inner join param.tdepto dep on dep.id_depto=obpg.id_depto
                        where obpg.id_proceso_wf = '||v_parametros.id_proceso_wf;

            --Definicion de la respuesta
			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--Devuelve la respuesta
			return v_consulta;

		end;

         /*********************************
 	#TRANSACCION:  'TES_REPROCPAG_SEL'
 	#DESCRIPCION:	Reporte de procesos de pagos
 	#AUTOR:		admin
 	#FECHA:		07-08-2018 16:01:32
	***********************************/

    elsif(p_transaccion='TES_REPROCPAG_SEL')then

    	begin
         	    		--Sentencia de la consulta
			v_consulta:='with estadoPlanPago as (select op.num_tramite, p.monto, p.nro_cuota,p.liquido_pagable, p.estado, op.id_obligacion_pago, p.fecha_tentativa, p.tipo
						from tes.tobligacion_pago op
						left join tes.tplan_pago p on p.id_obligacion_pago = op.id_obligacion_pago and p.estado_reg = ''activo''
						and p.estado != ''anulado'')

                 	SELECT
                    obl.num_tramite,
                    obl.id_obligacion_pago,
                    pr.desc_proveedor,
                    obl.obs,
                    es.monto,
                    m.moneda,
                    to_char(obl.fecha,''DD/MM/YYYY''),
                    obl.estado,
                    de.nombre as nombre_depto,
                    to_char(es.fecha_tentativa,''DD/MM/YYYY'')::date as fecha_tentativa,
                    es.nro_cuota

                    from tes.tobligacion_pago obl
                    inner join segu.tusuario usu1 on usu1.id_usuario = obl.id_usuario_reg
                    inner join param.vproveedor pr on pr.id_proveedor = obl.id_proveedor
                    inner join param.tmoneda m on m.id_moneda = obl.id_moneda
                    left join estadoPlanPago es on es.id_obligacion_pago = obl.id_obligacion_pago
                    inner join param.tdepto de on de.id_depto = obl.id_depto

                    where
                    es.fecha_tentativa between '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin||'''
                    and obl.estado IN(''borrador'',''registrado'',''en_pago'',''anulado'')
                    AND es.monto>= '''||v_parametros.monto||''' ';

                    v_consulta:=v_consulta||'ORDER BY obl.num_tramite, es.nro_cuota ASC';

            raise notice '% .',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

           /*********************************
 	#TRANSACCION:  'TES_LISOBLPA_SEL'
 	#DESCRIPCION:	lista obligaciones de pago
 	#AUTOR:	admin
 	#FECHA:		06-09-2018 16:01:32
	***********************************/

	elsif(p_transaccion='TES_LISOBLPA_SEL')then

    	begin

         -- raise exception '(%),... %', v_parametros.tipo_interfaz, v_filadd;

                  --Sentencia de la consulta
                  v_consulta:='select
                              obpg.id_obligacion_pago,
                              obpg.num_tramite,
                              obpg.id_gestion,
                              obpg.estado

                              from tes.tobligacion_pago obpg

                              where ' ;
                  --Definicion de la respuesta
                  v_consulta:=v_consulta||v_parametros.filtro;
                  v_consulta:=v_consulta;
                  --||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
--raise notice 'error1  %',v_consulta;
--raise exception 'error  %',v_consulta;
              raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
          /*********************************
 	#TRANSACCION:  'TES_LISOBLPA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:	 RAC (KPLIAN)
 	#FECHA:		04-05-2014 16:01:32
	***********************************/

	elsif(p_transaccion='TES_LISOBLPA_CONT')then

		begin


			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(obpg.id_obligacion_pago)
					    from tes.tobligacion_pago obpg

                              where';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta

            raise notice '%',v_consulta;
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'TES_LIBAN_EXT_SEL'
 	#DESCRIPCION:	Listado libro de bancos exterior y observacion
 	#AUTOR:	 BVP
 	#FECHA:
	***********************************/

	elsif(p_transaccion='TES_LIBAN_EXT_SEL')then

		begin

        select ges.gestion into v_gestion
        from param.tgestion ges
        where ges.id_gestion = v_parametros.id_gestion;

        v_fecha_ini = ('01/01/'||v_gestion::varchar)::date;
        v_fecha_fin = ('31/12/'||v_gestion::varchar)::date;


			--Sentencia pagos con libro de bancos exterior y observacion
			v_consulta:='select
                        plbex.id_obligacion_pago,
                        plbex.num_tramite,
                        plbex.fecha,
                        plbex.nro_cuenta,
                        plbex.nombre,
                        plbex.codigo,
                        plbex.nombre_estado,
                        plbex.obs,
                        plbex.desc_persona,
                        plbex.usuario_ai,
                        plbex.monto,
                        mone.moneda,
                        mone.codigo as cod_moneda,
                        plbex.estado_pp,
                        plbex.nombre_proveedor,
                        plbex.id_plan_pago

                    from tes.v_pagos_libro_banco_exterior_2 plbex
                    inner join param.tmoneda mone on mone.id_moneda = plbex.id_moneda
					where plbex.fecha between '''||v_fecha_ini||''' and '''||v_fecha_fin||'''
                    and ' ;

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'TES_LIBAN_EXT_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:	 BVP
 	#FECHA:
	***********************************/

	elsif(p_transaccion='TES_LIBAN_EXT_CONT')then

		begin

        select ges.gestion into v_gestion
        from param.tgestion ges
        where ges.id_gestion = v_parametros.id_gestion;

        v_fecha_ini = ('01/01/'||v_gestion::varchar)::date;
        v_fecha_fin = ('31/12/'||v_gestion::varchar)::date;


			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(plbex.id_obligacion_pago)
						from tes.v_pagos_libro_banco_exterior_2 plbex
                        inner join param.tmoneda mone on mone.id_moneda = plbex.id_moneda
                        where plbex.fecha between '''||v_fecha_ini||''' and '''||v_fecha_fin||'''
                        and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

  /*********************************
  #TRANSACCION:  'TES_DETEVPRE_SEL'
  #DESCRIPCION:	Listado detalle evolucion presupuestaria
  #AUTOR:	 BVP
  #FECHA:
  ***********************************/

	elsif(p_transaccion='TES_DETEVPRE_SEL')then

      begin
          --Sentencia pagos con libro de bancos exterior y observacion

            CREATE TEMPORARY TABLE ttemp_eval_det (
              id_partida_ejecucion	integer,
              id_partida_ejecucion_fk   integer,
              moneda					varchar,
              comprometido				numeric,
              ejecutado					numeric,
              pagado					numeric,
              nro_tramite				varchar,
              tipo_movimiento			varchar,
              nombre_partida			varchar,
              codigo					varchar,
              codigo_categoria			varchar,
              fecha						date,
              codigo_cc              	varchar,
              usr_reg					varchar,
              usr_mod 					varchar,
              fecha_reg					timestamp,
              fecha_mod                 timestamp,
              estado_reg				varchar
            )ON COMMIT DROP ;

            WITH RECURSIVE path_rec(id_partida_ejecucion, id_partida_ejecucion_fk ) AS (
            SELECT
              pe.id_partida_ejecucion,
              pe.id_partida_ejecucion_fk
            FROM pre.tpartida_ejecucion pe
            WHERE pe.id_partida_ejecucion = v_parametros.id_partida_ejecucion_com

            UNION

            SELECT
              pe2.id_partida_ejecucion,
              pe2.id_partida_ejecucion_fk
            FROM pre.tpartida_ejecucion pe2
            inner join path_rec  pr on pe2.id_partida_ejecucion = pr.id_partida_ejecucion_fk


            )
            SELECT
            id_partida_ejecucion
            into id_partida_ejecucion_raiz
            FROM path_rec order by id_partida_ejecucion limit 1 offset 0;

            WITH RECURSIVE path_rec( id_partida_ejecucion,  id_partida_ejecucion_fk, monto, monto_mb, tipo_movimiento,
                    id_moneda ) AS (
                SELECT
                  pe.id_partida_ejecucion,
                  pe.id_partida_ejecucion_fk,
                  pe.monto,
                  pe.monto_mb,
                  pe.tipo_movimiento,
                  pe.id_moneda
                FROM pre.tpartida_ejecucion pe
                WHERE pe.id_partida_ejecucion = id_partida_ejecucion_raiz

                UNION
                SELECT
                  pe2.id_partida_ejecucion,
                  pe2.id_partida_ejecucion_fk,
                  pe2.monto,
                  pe2.monto_mb,
                  pe2.tipo_movimiento,
                  pe2.id_moneda
                FROM pre.tpartida_ejecucion pe2
                inner join path_rec  pr on pe2.id_partida_ejecucion_fk = pr.id_partida_ejecucion
            )
            insert into ttemp_eval_det (id_partida_ejecucion, id_partida_ejecucion_fk,
                          comprometido, ejecutado, pagado, moneda, nro_tramite, tipo_movimiento,
                          nombre_partida, codigo, codigo_categoria, fecha, codigo_cc,
                          usr_reg, usr_mod, fecha_reg, fecha_mod, estado_reg)
             SELECT
             p.id_partida_ejecucion,
             p.id_partida_ejecucion_fk,
             case when p.tipo_movimiento = 'comprometido'then
                p.monto
             else
             0.00 end,
             case when p.tipo_movimiento = 'ejecutado'then
                p.monto
             else
             0.00 end,
             case when p.tipo_movimiento = 'pagado'then
                p.monto
             else
             0.00 end,
              mo.moneda,
              pej.nro_tramite,
              p.tipo_movimiento,
              par.nombre_partida,
              par.codigo,
              cat.codigo_categoria,
              pej.fecha,
              vpre.codigo_cc,
              usu1.cuenta as usr_reg,
              usu2.cuenta as usr_mod,
              pej.fecha_reg,
              pej.fecha_mod,
              pej.estado_reg
            FROM path_rec p
            inner join pre.tpartida_ejecucion pej on pej.id_partida_ejecucion = p.id_partida_ejecucion
            inner join segu.tusuario usu1 on usu1.id_usuario = pej.id_usuario_reg
            inner join pre.tpresupuesto pre on pre.id_presupuesto = pej.id_presupuesto
            left join segu.tusuario usu2 on usu2.id_usuario = pej.id_usuario_mod
            INNER JOIN pre.vpresupuesto vpre ON vpre.id_presupuesto = pre.id_presupuesto
            inner join pre.vcategoria_programatica cat on cat.id_categoria_programatica = pre.id_categoria_prog
            inner join pre.tpartida par on par.id_partida = pej.id_partida
            inner join param.tmoneda mo on mo.id_moneda = p.id_moneda;

            insert into ttemp_eval_det (id_partida_ejecucion,
                          comprometido, ejecutado, pagado, nro_tramite)
			select 0,
                   sum(comprometido),
                   sum(ejecutado),
                   sum(pagado),
                   'TOTAL'::varchar
            FROM ttemp_eval_det;

          v_consulta:=' select * from ttemp_eval_det
          			 where (tipo_movimiento = '''||v_parametros.tipo_interfaz||'''
                      or tipo_movimiento is null)
                      and ';

          --Definicion de la respuesta
          v_consulta:=v_consulta||v_parametros.filtro;
          v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad;
          --Devuelve la respuesta
          raise notice '%',v_consulta;
          return v_consulta;
      end;

 /*********************************
 	#TRANSACCION:  'TES_SOLREPOBP_SEL'
 	#DESCRIPCION:	Consulta de datos para reporte solicitud
 	#AUTOR:		Breydi vasquez
 	#FECHA:		22/02/2020
	***********************************/

	elsif(p_transaccion='TES_SOLREPOBP_SEL')then

    begin

            IF  pxp.f_existe_parametro(p_tabla,'id_obligacion_pago') THEN

                  v_filtro = 'obpg.id_obligacion_pago = '||v_parametros.id_obligacion_pago||' and ';

                    select
                    obp.id_proceso_wf
                    into v_proces_wf
                    from tes.tobligacion_pago obp
                    where obp.id_obligacion_pago = v_parametros.id_obligacion_pago;

               		select obp.num_tramite
                     into v_nro_tramite
                    from tes.tobligacion_pago obp
                    where obp.id_obligacion_pago = v_parametros.id_obligacion_pago;

            ELSE
                  v_filtro = 'obpg.id_proceso_wf = '||v_parametros.id_proceso_wf||' and ';

                  v_proces_wf = v_parametros.id_proceso_wf;

               		select obp.num_tramite
                     into v_nro_tramite
                    from tes.tobligacion_pago obp
                    where obp.id_proceso_wf = v_parametros.id_proceso_wf;

            END IF;


              select es.id_estado_wf
                  into v_id_estado_wf
              from wf.testado_wf es
              where es.fecha_reg = (
                      select
                           max(ewf.fecha_reg)
                         FROM  wf.testado_wf ewf
                         INNER JOIN  wf.ttipo_estado te on ewf.id_tipo_estado = te.id_tipo_estado
                         LEFT JOIN   segu.tusuario usu on usu.id_usuario = ewf.id_usuario_reg
                         LEFT JOIN  orga.vfuncionario fun on fun.id_funcionario = ewf.id_funcionario
                         LEFT JOIN  param.tdepto depto on depto.id_depto = ewf.id_depto
                         WHERE
                          ewf.id_proceso_wf = v_proces_wf
                          and te.codigo = 'borrador'
                          and te.etapa = 'Solicitante');

              select
                     ew.fecha_reg::date
                     into v_fecha_sol
                   FROM  wf.testado_wf ew
                   where ew.id_estado_anterior = v_id_estado_wf;


            --Sentencia de la consulta
			v_consulta:='select
                                obpg.id_obligacion_pago,
                                obpg.estado,
                                obpg.numero,
                                obpg.num_tramite,
                                obpg.fecha_conclusion_pago as fecha_apro,
                                mon.codigo as desc_moneda,
                                obpg.tipo_solicitud as tipo,
                                ges.gestion as desc_gestion,
                                obpg.fecha as fecha_soli,
                                '''||coalesce(v_fecha_sol, now())||'''::date as fecha_soli_gant,
                                fun.desc_funcionario1 as desc_funcionario,
                                uo.codigo||''-''||uo.nombre_unidad as desc_uo,
                                dep.codigo as desc_depto,
                                funa.desc_funcionario1 as desc_funcionario_apro,
                                fca.descripcion_cargo::varchar as cargo_desc_funcionario,
                                fcag.descripcion_cargo::varchar as cargo_desc_funcionario_apro,
                                obpg.fecha_reg,
                                obpg.codigo_poa,
                                COALESCE(obpg.usuario_ai,'''')::varchar as nombre_usuario_ai,
                                uo.codigo as codigo_uo,
                                dep.prioridad as dep_prioridad,
                                obpg.id_moneda,
                                obpg.obs
                        from tes.tobligacion_pago obpg
                        inner join segu.tusuario usu1 on usu1.id_usuario = obpg.id_usuario_reg
                        inner join orga.vfuncionario fun on fun.id_funcionario = obpg.id_funcionario
                        inner join orga.tuo uo on uo.id_uo = orga.f_get_uo_gerencia_ope(NULL, obpg.id_funcionario,obpg.fecha)
                        inner join param.tmoneda mon on mon.id_moneda = obpg.id_moneda
                        inner join param.tgestion ges on ges.id_gestion = obpg.id_gestion
                        inner join param.tdepto dep on dep.id_depto = obpg.id_depto
                        inner join orga.vfuncionario funa on funa.id_funcionario = obpg.id_funcionario_gerente
                        left join segu.tusuario usu2 on usu2.id_usuario = obpg.id_usuario_mod
                        inner join wf.testado_wf ew on ew.id_estado_wf = obpg.id_estado_wf
                        inner join orga.vfuncionario_ultimo_cargo fca on fca.id_funcionario = fun.id_funcionario
                        left join orga.vfuncionario_ultimo_cargo fcag on fcag.id_funcionario = obpg.id_funcionario_gerente
                        where '||v_filtro;

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice '%', v_consulta;

			--Devuelve la respuesta
			return v_consulta;
    end;

    /*********************************
 	#TRANSACCION:  'TES_RELACOB_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
    #FECHA:		02/11/2020 10:28:30
	***********************************/

	elsif(p_transaccion='TES_RELACOB_SEL')then

    	begin

            --Sentencia de la consulta
			v_consulta:='select
                            rp.id_relacion_proceso_pago,
                            rp.observaciones,
                            rp.id_obligacion_pago,
                            op.num_tramite::varchar,

                            rp.estado_reg,
                            rp.fecha_reg,
                            rp.id_usuario_reg,
                            rp.id_usuario_mod,
                            rp.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod

                      from tes.trelacion_proceso_pago rp
                         inner join segu.tusuario usu1 on usu1.id_usuario = rp.id_usuario_reg
                         left join segu.tusuario usu2 on usu2.id_usuario = rp.id_usuario_mod

                         join tes.tobligacion_pago op on op.id_obligacion_pago = rp.id_obligacion_pago

                      where  rp.estado_reg = ''activo'' and ';


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice '... %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_RELACOB_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
    #FECHA:		02/11/2020 10:28:30
	***********************************/

	elsif(p_transaccion='TES_RELACOB_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(rp.id_relacion_proceso_pago)
					     from tes.trelacion_proceso_pago rp
                         inner join segu.tusuario usu1 on usu1.id_usuario = rp.id_usuario_reg
                         left join segu.tusuario usu2 on usu2.id_usuario = rp.id_usuario_mod

                         join tes.tobligacion_pago op on op.id_obligacion_pago = rp.id_obligacion_pago

                      where  rp.estado_reg = ''activo'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
        #TRANSACCION:  'TES_COMBOP_SEL'
        #DESCRIPCION: Consulta de datos de combo obligaciones de pago
        #AUTOR:		maylee.perez
    	#FECHA:		02/11/2020 10:28:30
        ***********************************/


        elseif(p_transaccion='TES_COMBOP_SEL')then

            begin


              --Sentencia de la consulta
               v_consulta:=' Select opa.id_obligacion_pago,
               						opa.num_tramite

				  from tes.tobligacion_pago opa
                  where opa.id_gestion = 19
     			  and opa.tipo_obligacion in (''pago_especial'', ''pago_directo'')
                  and  ';

      --raise exception 'llega %',v_parametros.filtro;
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

          end;

        /*********************************
        #TRANSACCION:  'TES_COMBOP_CONT'
        #DESCRIPCION: Conteo de registros de combo obligaciones de pago
        #AUTOR:		maylee.perez
    	#FECHA:		02/11/2020 10:28:30
        ***********************************/

        elsif(p_transaccion='TES_COMBOP_CONT')then

          begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(opa.id_obligacion_pago)
                    from tes.tobligacion_pago opa
                    where opa.id_gestion = 19
                    and opa.tipo_obligacion in (''pago_especial'', ''pago_directo'')
                    and  ';



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

ALTER FUNCTION tes.ft_obligacion_pago_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;

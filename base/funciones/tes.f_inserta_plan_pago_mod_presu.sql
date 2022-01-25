CREATE OR REPLACE FUNCTION tes.f_inserta_plan_pago_mod_presu (
  p_administrador integer,
  p_id_usuario integer,
  p_id_plan_pago integer
)
RETURNS varchar AS
$body$
    /**************************************************************************
     SISTEMA:		Adquisiciones
     FUNCION: 		tes.f_inserta_plan_pago_mod_presu
     DESCRIPCION:   Inserta registro de modificacion presupuestaria
     AUTOR: 		Maylee Perez Pastor
     FECHA:	        02-06-2020
     COMENTARIOS:
    ***************************************************************************
     HISTORIAL DE MODIFICACIONES:
     DESCRIPCION:
     AUTOR:
     FECHA:
    ***************************************************************************/

    DECLARE


        v_resp		            	varchar;
        v_nombre_funcion        	text;
        v_mensaje_error         	text;
        v_parametros record;
        v_resp_doc     				boolean;
        va_id_funcionario_gerente   INTEGER[];
        v_id_proceso_macro 			integer;
        v_codigo_tipo_proceso 		varchar;
        v_anho 						integer;
        v_num_tramite  				varchar;
        v_id_proceso_wf 			integer;
        v_id_estado_wf 				integer;
        v_codigo_estado 			varchar;
        v_codigo_estado_ant 		varchar;
        v_id_obligacion_pago 		integer;

        v_id_ajuste					integer;
        v_plan_pago					record;
        v_obligacion_pago			record;
        v_correlativo				integer;
        v_codigo_tipo_pro			varchar;
        v_id_estado_actual 			integer;
        v_id_tipo_estado			integer;
        v_estado_pago				record;
        va_codigo_estado 			varchar[];
        va_id_tipo_estado 			integer[];
        va_codigo_estado_fin        varchar;
        v_id_funcionario			integer;
        v_id_funcionario_aprobador	integer;



    BEGIN

        v_nombre_funcion = 'f_inserta_plan_pago_mod_presu';

		    select pp.*
            into v_plan_pago
            from tes.tplan_pago pp
            inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
            where pp.id_plan_pago =  p_id_plan_pago ;

            select op.*
            into v_obligacion_pago
            from tes.tplan_pago pp
            inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
            where pp.id_plan_pago =  p_id_plan_pago ;



           --obtener el codigo del tipo de proceso
            select
                 tp.codigo,
                 pm.id_proceso_macro
              into
                 v_codigo_tipo_proceso,
                 v_id_proceso_macro
            from  wf.tproceso_macro pm
            inner join wf.ttipo_proceso tp on tp.id_proceso_macro = pm.id_proceso_macro
            inner join segu.tsubsistema s on s.id_subsistema = pm.id_subsistema
            where   tp.estado_reg = 'activo'
                    and tp.inicio = 'si'
                    and pm.codigo = 'AJT'
                    and s.codigo = 'PRE';

            --insertar correlativo y detalle
             select max(taj.correlativo)
             into v_correlativo
             from pre.tajuste taj
             where taj.nro_tramite = v_obligacion_pago.num_tramite;
  --p_id_usuario = 17; HUGO TAPIA
             SELECT tf.id_funcionario
             INTO v_id_funcionario
             FROM segu.tusuario tu
             INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
             WHERE tu.id_usuario = p_id_usuario ;


            -- INICIO el tramite en el sistema de WF
             SELECT
                   ps_num_tramite ,
                   ps_id_proceso_wf ,
                   ps_id_estado_wf ,
                   ps_codigo_estado
                into
                   v_num_tramite,
                   v_id_proceso_wf,
                   v_id_estado_wf,
                   v_codigo_estado

              FROM wf.f_inicia_tramite(
                   p_id_usuario,
                   null,
                   null,
                   v_obligacion_pago.id_gestion,
                   v_codigo_tipo_proceso,
                   v_id_funcionario::integer,
                   NULL,
                   'Inicio Disminucion Comprometido',
                   '',
                   v_obligacion_pago.num_tramite);


                   -- inserta documentos en estado borrador si estan configurados
                   v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);
                   -- verificar documentos
                   v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_wf);



              --estado SIGUIENTE REVISION
                       SELECT tf.id_funcionario
                       INTO v_id_funcionario_aprobador
                       FROM segu.tusuario tu
                       INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
                       WHERE tu.id_usuario = 17 ; -- =HUGO TAPIA
                       --WHERE tu.id_usuario = 727 ; -- =CASIANA ALVAREZ DE CAMACHO


                   select
                      ew.id_tipo_estado ,
                      te.codigo
                   into
                      v_id_tipo_estado,
                      v_codigo_estado
                    from wf.testado_wf ew
                    inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                    where ew.id_estado_wf = v_id_estado_wf;

               v_id_estado_actual =  wf.f_registra_estado_wf( v_id_tipo_estado,
                                                       v_id_funcionario_aprobador,
                                                       v_id_estado_wf,
                                                       v_id_proceso_wf,
                                                       p_id_usuario,
                                                       null,
                                                       null,
                                                       v_obligacion_pago.id_depto,
                                                       '');


                   --   para un estado siguiente
                     SELECT  ps_id_tipo_estado,
                             ps_codigo_estado

                       into
                          va_id_tipo_estado,
                          va_codigo_estado

                      FROM wf.f_obtener_estado_wf(
                       v_id_proceso_wf,
                        NULL,
                       v_id_tipo_estado,
                       'siguiente',
                       p_id_usuario);

                       v_id_estado_actual =  wf.f_registra_estado_wf( va_id_tipo_estado[1],
                                                       v_id_funcionario_aprobador,
                                                       v_id_estado_actual,
                                                       v_id_proceso_wf,
                                                       p_id_usuario,
                                                       null,
                                                       null,
                                                       v_obligacion_pago.id_depto,
                                                       '');


                     -- inserta documentos en estado borrador si estan configurados
                     v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_actual);
                     -- verificar documentos
                     v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_actual);


                       --estado SIGUIENTE APROBADO

                       select
                          ew.id_tipo_estado ,
                          te.codigo
                       into
                          v_id_tipo_estado,
                          v_codigo_estado
                        from wf.testado_wf ew
                        inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                        where ew.id_estado_wf = v_id_estado_actual;

                        --   para un estado siguiente
                       SELECT  ps_id_tipo_estado,
                               ps_codigo_estado

                         into
                            va_id_tipo_estado,
                            va_codigo_estado

                        FROM wf.f_obtener_estado_wf(
                         v_id_proceso_wf,
                          NULL,
                         v_id_tipo_estado,
                         'siguiente',
                         p_id_usuario);

                         v_id_estado_actual =  wf.f_registra_estado_wf( va_id_tipo_estado[1],
                                                         v_id_funcionario_aprobador,
                                                         v_id_estado_actual,
                                                         v_id_proceso_wf,
                                                         p_id_usuario,
                                                         null,
                                                         null,
                                                         v_obligacion_pago.id_depto,
                                                         '');

                         -- inserta documentos en estado borrador si estan configurados
                         v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_actual);
                         -- verificar documentos
                         v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_actual);

                       va_codigo_estado_fin = va_codigo_estado[1];



                 -- Sentencia de la insercion CABECERA
                  insert into pre.tajuste(
                    estado_reg,
                    estado,
                    justificacion,
                    tipo_ajuste,
                    id_usuario_reg,
                    fecha_reg,
                    fecha,
                    id_gestion,
                    importe_ajuste,
                    movimiento,
                    id_moneda,

                    nro_tramite,
                    id_proceso_wf,
                    id_estado_wf,

                    correlativo,
                    tipo_proceso
                  ) values(
                    'activo',
                    va_codigo_estado_fin,
                    case  when v_plan_pago.id_depto_lb = 27 then 'REVERSIÓN AUTOMÁTICA DEL PRESUPUESTO CUOTA '||v_plan_pago.nro_cuota||', PAGADO VIA BUENOS AIRES'
                          when v_plan_pago.id_depto_lb = 26 then 'REVERSIÓN AUTOMÁTICA DEL PRESUPUESTO CUOTA '||v_plan_pago.nro_cuota||', PAGADO VIA MIAMI'
                          when v_plan_pago.id_depto_lb = 28 then 'REVERSIÓN AUTOMÁTICA DEL PRESUPUESTO CUOTA '||v_plan_pago.nro_cuota||', PAGADO VIA MADRID'
                          when v_plan_pago.id_depto_lb = 39 then 'REVERSIÓN AUTOMÁTICA DEL PRESUPUESTO CUOTA '||v_plan_pago.nro_cuota||', PAGADO VIA SAO PAULO'
                          when v_plan_pago.id_depto_lb = 82 then 'REVERSIÓN AUTOMÁTICA DEL PRESUPUESTO CUOTA '||v_plan_pago.nro_cuota||', PAGADO VIA LIMA'
                    end,
                    'rev_comprometido', --tipo ajuste
                    p_id_usuario,
                    now(),
                    now(), --fecha
                    v_obligacion_pago.id_gestion,
                    v_plan_pago.liquido_pagable,--importe_ajuste
                    'gasto', --movimiento
                    v_obligacion_pago.id_moneda,

                    v_obligacion_pago.num_tramite,
                    v_id_proceso_wf,
                    v_id_estado_actual, --v_id_estado_wf,

                    coalesce(v_correlativo+1, 1),
                    'exterior'

                )RETURNING id_ajuste into v_id_ajuste;


                -- Sentencia de la insercion DETALLE
                insert into pre.tajuste_det(
                    id_presupuesto,
                    importe,
                    id_partida,
                    estado_reg,
                    tipo_ajuste,
                    id_usuario_ai,
                    fecha_reg,
                    usuario_ai,
                    id_usuario_reg,
                    fecha_mod,
                    id_usuario_mod,
                    id_ajuste,
                    descripcion,
                    id_orden_trabajo,
                    id_sol_origen
                ) select
                    tsd.id_centro_costo,
                    pro.monto_ejecutar_mo,
                    tsd.id_partida,
                    'activo',
                    'decremento',
                    null,
                    now(),
                    null,
                    p_id_usuario,
                    null,
                    null,
                    v_id_ajuste,
                    tsd.descripcion,
                    tsd.id_orden_trabajo,
                    tsd.id_obligacion_det
                from  tes.tprorrateo pro
                inner join tes.tobligacion_det tsd on tsd.id_obligacion_det = pro.id_obligacion_det
                inner join tes.tobligacion_pago ts on ts.id_obligacion_pago = tsd.id_obligacion_pago
                --left join tes.tprorrateo pro on pro.id_plan_pago = p_id_plan_pago
                where ts.num_tramite = v_obligacion_pago.num_tramite and tsd.estado_reg = 'activo'
                and pro.id_plan_pago = p_id_plan_pago;


			  ----------------------------------------------------
              --  ACTUALIZA EL NUEVO ESTADO DE AJUSTES
              ----------------------------------------------------

              IF  pre.f_fun_inicio_ajuste_wf(p_id_usuario,
                                                null,
                                                null,
                                                v_id_estado_actual,
                                                v_id_proceso_wf,
                                                va_codigo_estado[1]) THEN


              END IF;

              --


                 --Definicion de la respuesta
                 v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Ajustes almacenado(a) con exito (id_ajuste'||v_id_ajuste||')');
                 v_resp = pxp.f_agrega_clave(v_resp,'id_ajuste',v_id_ajuste::varchar);


                --Devuelve la respuesta
                return v_resp;






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
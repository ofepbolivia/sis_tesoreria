--------------- SQL ---------------

CREATE OR REPLACE FUNCTION tes.f_gestionar_cuota_plan_pago_eliminacion (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_int_comprobante integer,
  p_conexion varchar = NULL::character varying
)
RETURNS boolean AS
$body$
/*

Autor: RAC KPLIANF
Fecha:   6 junio de 2013
Descripcion  Esta funcion retrocede el estado de los planes de pago cuando los comprobantes son eliminados

  

*/


DECLARE
  
	v_nombre_funcion   	text;
	v_resp				varchar;
    
    
    v_registros 		record;
    
    v_id_estado_actual  integer;
    
    
    va_id_tipo_estado integer[];
    va_codigo_estado varchar[];
    va_disparador    varchar[];
    va_regla         varchar[]; 
    va_prioridad     integer[];
    
    v_tipo_sol   varchar;
    
    v_nro_cuota numeric;
    
     v_id_proceso_wf integer;
     v_id_estado_wf integer;
  
     v_id_plan_pago integer;
     v_verficacion  boolean;
     v_verficacion2  varchar[];
     
     
      v_id_tipo_estado integer;
     v_id_funcionario  integer;
     v_id_usuario_reg integer;
     v_id_depto integer;
     v_codigo_estado varchar;
     v_id_estado_wf_ant  integer;
     v_rec_cbte_trans   record;


       ----------------------
     --variables para el paso directo de estado pendiente a borrador 09/12/2019
     --------------------
     v_registros_ant record;
     v_registros_sig	record;
     v_obs 		varchar;
     p_id_funcionario integer =NULL::integer;
     v_alarmas_con integer[];
     va_verifica_documento varchar;
     v_resp_doc boolean;
     v_res_validacion	text;

BEGIN

	v_nombre_funcion = 'tes.f_gestionar_cuota_plan_pago_eliminacion';
    
    
    
    -- 1) con el id_comprobante identificar el plan de pago
   
      select 
          pp.id_plan_pago,
          pp.id_estado_wf,
          pp.id_proceso_wf,
          pp.tipo,
          pp.estado,
          pp.id_plan_pago_fk,
          pp.id_obligacion_pago,
          pp.nro_cuota,
          pp.id_plantilla,
          pp.monto_ejecutar_total_mo,
          pp.monto_no_pagado,
          pp.liquido_pagable,
          pwf.id_tipo_proceso,
          op.id_depto ,
          op.pago_variable,
          pp.id_cuenta_bancaria ,
          pp.nombre_pago,
          pp.forma_pago,
          pp.tipo_cambio,
          pp.tipo_pago,
          pp.fecha_tentativa,
          pp.otros_descuentos,
          pp.monto_retgar_mo,
          op.numero,
          c.temporal,
          c.estado_reg as estadato_cbte,
          plt.desc_plantilla
      into
          v_registros
      from  tes.tplan_pago pp
      inner join tes.tobligacion_pago  op on op.id_obligacion_pago = pp.id_obligacion_pago
      inner  join wf.tproceso_wf pwf  on  pwf.id_proceso_wf = pp.id_proceso_wf
      inner join param.tplantilla plt on plt.id_plantilla = pp.id_plantilla
      inner join conta.tint_comprobante  c on c.id_int_comprobante = pp.id_int_comprobante
      where  pp.id_int_comprobante = p_id_int_comprobante;
    
    --2) Validar que tenga un plan de pago
    
    
     IF  v_registros.id_plan_pago is NULL  THEN
     
        raise exception 'El comprobante no esta relacionado con nigun plan de pagos';
     
     END IF;
     
     
         -- si el registro del plan de pago proviene de un tipo de documento distinto de Extracto bancario se retrocede normalmente
            if (v_registros.desc_plantilla != 'Extracto Bancario') THEN
     
                   --  recuperaq estado anterior segun Log del WF
                      SELECT
                         ps_id_tipo_estado,
                         ps_id_funcionario,
                         ps_id_usuario_reg,
                         ps_id_depto,
                         ps_codigo_estado,
                         ps_id_estado_wf_ant
                      into
                         v_id_tipo_estado,
                         v_id_funcionario,
                         v_id_usuario_reg,
                         v_id_depto,
                         v_codigo_estado,
                         v_id_estado_wf_ant
                      FROM wf.f_obtener_estado_ant_log_wf(v_registros.id_estado_wf);



                       --

                        select
                             ew.id_proceso_wf
                          into
                             v_id_proceso_wf
                        from wf.testado_wf ew
                        where ew.id_estado_wf= v_id_estado_wf_ant;

                        -- registra nuevo estado

                        v_id_estado_actual = wf.f_registra_estado_wf(
                            v_id_tipo_estado,
                            v_id_funcionario,
                            v_registros.id_estado_wf,
                            v_id_proceso_wf,
                            p_id_usuario,
                            p_id_usuario_ai,
                            p_usuario_ai,
                            v_id_depto,
                            'Eliminación de comprobante de la OP:'|| COALESCE(v_registros.numero,'NaN')||', cuota nro: '|| COALESCE(v_registros.nro_cuota,'NAN'));
             else
             -- si proviene de un tipo de documento extracto bancario retrocedemos a borrador de otra manera ya que no existen estados intermedios
                           SELECT
                           ps_id_tipo_estado,
                           ps_codigo_estado
                           into
                             v_id_tipo_estado,
                             v_codigo_estado
                           FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_registros.id_tipo_proceso);



                              v_obs = 'La solicitud de '||v_registros.tipo ||'pasa a borrador';
                              select
                              ew.estado_reg,
                              ew.id_funcionario,
                              ew.id_depto,
                              tew.alerta,
                              ew.id_depto,
                              tew.id_tipo_estado,
                              tew.nombre_estado,
                              tew.disparador,
                              ew.id_estado_wf
                              into
                              v_registros_ant
                              from wf.testado_wf ew
                              inner join wf.ttipo_estado tew on tew.id_tipo_estado = ew.id_tipo_estado
                              where ew.id_estado_wf = v_registros.id_estado_wf;
                               --
                              --revisar que el estado se encuentre activo, en caso contrario puede
                              --se una orden desde una pantalla desactualizada

                              IF (v_registros_ant.estado_reg !='activo') THEN
                                 raise exception 'El estado se encuentra inactivo, actualice sus datos' ;
                              END IF;

                              if(v_id_tipo_estado is null
                                  OR v_registros.id_estado_wf is null
                                  OR v_registros.id_proceso_wf is null
                                  )then
                                  raise exception 'Faltan parametros, existen parametros nulos o en blanco, para registrar el estado en el WF.';

                              end if;

                              --recupera datos del tipo estado anterior

                              SELECT
                               te.alerta,
                               s.nombre,
                               s.codigo,
                               s.id_subsistema,
                               te.nombre_estado,
                               pm.nombre as nombre_proceso_macro,
                               te.plantilla_mensaje,
                               te.plantilla_mensaje_asunto,
                               te.disparador,
                               te.cargo_depto,
                               te.titulo_alerta,
                               te.acceso_directo_alerta,
                               te.nombre_clase_alerta,
                               te.parametros_ad,
                               te.tipo_noti
                              INTO
                               v_registros_sig
                              FROM wf.ttipo_estado te
                              inner join wf.ttipo_proceso tp on tp.id_tipo_proceso  = te.id_tipo_proceso
                              left join wf.ttipo_documento td on td.id_tipo_proceso = tp.id_tipo_proceso
                              inner join wf.tproceso_macro pm on tp.id_proceso_macro = pm.id_proceso_macro
                              inner join segu.tsubsistema s on pm.id_subsistema = s.id_subsistema
                              WHERE te.id_tipo_estado = v_id_tipo_estado;


                              IF(v_registros_sig.id_subsistema is NULL ) THEN

                                 raise exception  'El proceso macro no esta relacionado con ningun sistema';

                              END IF;

                               --verificamos si requiere manejo de alerta

                              INSERT INTO wf.testado_wf(
                               id_estado_anterior,
                               id_tipo_estado,
                               id_proceso_wf,
                               id_funcionario,
                               fecha_reg,
                               estado_reg,
                               id_usuario_reg,
                               id_depto,
                               obs,
                               id_alarma,
                               id_usuario_ai,
                               usuario_ai)
                              values(
                                 v_registros.id_estado_wf,
                                 v_id_tipo_estado,
                                 v_registros.id_proceso_wf,
                                 p_id_funcionario,
                                 now(),
                                 'activo',
                                 p_id_usuario,
                                 v_registros.id_depto,
                                 v_obs,
                                 v_alarmas_con,
                                 p_id_usuario_ai,
                                 p_usuario_ai)
                              RETURNING id_estado_wf INTO v_id_estado_actual;


                              --inserta log de estado en el proceso_wf
                              update wf.tproceso_wf SET
                              id_tipo_estado_wfs =  array_append(id_tipo_estado_wfs, v_id_tipo_estado)
                              where id_proceso_wf = v_registros.id_proceso_wf;


                              UPDATE wf.testado_wf
                              SET estado_reg = 'inactivo'
                              WHERE id_estado_wf = v_registros.id_estado_wf;

                              select
                               ew.verifica_documento
                              into
                               va_verifica_documento
                              from wf.testado_wf ew
                              where ew.id_estado_wf = v_registros.id_estado_wf;


                              -- inserta documentos en estado borrador si estan configurados
                               v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_registros.id_proceso_wf, v_id_estado_actual);

                              -- verificar documentos
                              IF(va_verifica_documento='si')THEN
                                  v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_actual);
                              END IF;

                               -- verificar observaciones abiertas
                              v_resp_doc = wf.f_verifica_observaciones(p_id_usuario, v_registros.id_estado_wf);

                              --valida datos de los formularios
                              v_res_validacion = wf.f_valida_cambio_estado(v_registros.id_estado_wf,'preregistro',v_id_tipo_estado);
                              IF  (v_res_validacion IS NOT NULL AND v_res_validacion != '') THEN

                                  raise exception 'Es necesario registrar los siguientes campos en el formulario: % . Antes de pasar al estado : %',v_res_validacion,v_registros.nombre_estado;

                              END IF;

            end if;
          
          IF v_codigo_estado != 'pendiente' THEN          
                      
            -- actualiza estado en la solicitud
              update tes.tplan_pago pp set 
                 id_estado_wf =  v_id_estado_actual,
                 estado = v_codigo_estado,
                 id_usuario_mod=p_id_usuario,
                 fecha_mod=now(),
                 id_int_comprobante = NULL,
                 id_usuario_ai = p_id_usuario_ai,
                 usuario_ai = p_usuario_ai
               where pp.id_plan_pago = v_registros.id_plan_pago;
          ELSE
          -- si el estado es pendiente conservamos el ID del cbte ...
            
            -- actualiza estado en la solicitud
            update tes.tplan_pago pp set 
               id_estado_wf =  v_id_estado_actual,
               estado = v_codigo_estado,
               id_usuario_mod=p_id_usuario,
               fecha_mod=now(),
               id_usuario_ai = p_id_usuario_ai,
               usuario_ai = p_usuario_ai
             where pp.id_plan_pago = v_registros.id_plan_pago;
          
          
             
          END IF;   
             
     
           -- solo si el estado del cbte es borrador y no es un comprobante temporal
           -- desasociamos las transacciones del comprobante
         
           
            IF v_registros.estadato_cbte in ('borrador','eliminado') and v_registros.temporal = 'no' then
               --cheque si tiene prorrateo en tesoria (modulo de obligacion de pagos)
              for v_rec_cbte_trans in (select * 
                                       from conta.tint_transaccion
                                        where id_int_comprobante = p_id_int_comprobante) LOOP
              
                    update tes.tprorrateo p set
                         id_int_transaccion = NULL
                    where p.id_int_transaccion = v_rec_cbte_trans.id_int_transaccion;
                    
                     
             
              END LOOP;
            END IF;
            
         
     
    
     -- 3.1)  si es tipo es devengado_pago
      IF   v_registros.tipo = 'devengado_pagado' THEN
           
             
           
     END IF;
    
  
RETURN  TRUE;



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
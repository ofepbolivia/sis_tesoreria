CREATE OR REPLACE FUNCTION tes.f_inserta_plan_pago_replicado (
  p_administrador integer,
  p_id_usuario integer,
  p_hstore public.hstore,
  p_salta boolean = false
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		tes.f_inserta_plan_pago_replicado
 DESCRIPCION:   Inserta registro de cotizacion
 AUTOR: 		Maylee Perez Pastor
 FECHA:	        15-8-2019
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

    v_resp		           			varchar;
	v_nombre_funcion       			text;
	v_mensaje_error         		text;
    v_registros   					record;
    v_nro_cuota						numeric;
    v_fecha_tentativa				date;
    va_id_tipo_estado_pro 			integer[];
    va_codigo_estado_pro 			varchar[];
    va_disparador_pro 				varchar[];
    va_regla_pro 					varchar[];
    va_prioridad_pro 				integer[];
    v_id_estado_actual 				integer;
    v_id_proceso_wf 				integer;
    v_id_estado_wf 					integer;
    v_codigo_estado					varchar;
    v_id_plan_pago					integer;
    v_registros_tpp                 record;
    v_resp_doc   					boolean;
    v_obligacion					record;
    v_fecha_ini_pp					date;
    v_fecha_fin_pp 					date;
    v_fecha_conclusion				date;

BEGIN

          v_nombre_funcion = 'tes.f_inserta_plan_pago_dev';

          select *
          into v_obligacion
          from tes.tobligacion_pago op
          where id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer;


           -- segun el tipo  recuperamos el tipo_plan_pago, determinamos el flujos para el WF
           select
            tpp.id_tipo_plan_pago,
            tpp.codigo_proceso_llave_wf
           into
           v_registros_tpp
           from  tes.ttipo_plan_pago  tpp
           where tpp.codigo =  (p_hstore->'tipo')::varchar;


          --  obtiene datos de la obligacion
          select
            op.porc_anticipo,
            op.porc_retgar,
            op.num_tramite,
            op.id_proceso_wf,
            op.id_estado_wf,
            op.estado,
            op.id_depto,
            op.pago_variable,
            op.numero
          into v_registros
           from tes.tobligacion_pago op
           where op.id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer;


           select
             max(pp.nro_cuota),
             max(pp.fecha_tentativa)
           into
             v_nro_cuota,
             v_fecha_tentativa
           from tes.tplan_pago pp
           where
               pp.id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer
           and pp.estado_reg='activo';


         -- define numero de cuota
         v_nro_cuota = floor(COALESCE(v_nro_cuota,0))+1;



          -------------------------------------
          --  Manejo de estados con el WF
          -------------------------------------

          --cambia de estado al obligacion
          IF  v_registros.estado = 'registrado' THEN

               SELECT
                     ps_id_tipo_estado,
                     ps_codigo_estado,
                     ps_disparador,
                     ps_regla,
                     ps_prioridad
                  into
                    va_id_tipo_estado_pro,
                    va_codigo_estado_pro,
                    va_disparador_pro,
                    va_regla_pro,
                    va_prioridad_pro

                FROM wf.f_obtener_estado_wf( v_registros.id_proceso_wf,  v_registros.id_estado_wf,NULL,'siguiente');


                  v_id_estado_actual =  wf.f_registra_estado_wf(va_id_tipo_estado_pro[1],
                                                                   NULL, --id_funcionario
                                                                    v_registros.id_estado_wf,
                                                                    v_registros.id_proceso_wf,
                                                                    p_id_usuario,
                                                                    (p_hstore->'_id_usuario_ai')::integer,
                                                                    (p_hstore->'_nombre_usuario_ai')::varchar,
                                                                    v_registros.id_depto);



                     SELECT
                               ps_id_proceso_wf,
                               ps_id_estado_wf,
                               ps_codigo_estado
                         into
                               v_id_proceso_wf,
                               v_id_estado_wf,
                               v_codigo_estado
                      FROM wf.f_registra_proceso_disparado_wf(
                               p_id_usuario,
                               (p_hstore->'_id_usuario_ai')::integer,
                               (p_hstore->'_nombre_usuario_ai')::varchar,
                               v_id_estado_actual,
                               NULL,
                               v_registros.id_depto,
                              ('Solicitud de devengado para la OP:'|| COALESCE(v_registros.numero,'s/n')||' cuota nro'||v_nro_cuota::varchar),
                               v_registros_tpp.codigo_proceso_llave_wf,
                               COALESCE(v_registros.numero,'s/n')||'-N# '||v_nro_cuota::varchar
                           );

                 select op.fecha_costo_ini_pp, op.fecha_costo_fin_pp, op.fecha_conclusion_pago
                 into v_fecha_ini_pp, v_fecha_fin_pp, v_fecha_conclusion
                 from tes.tobligacion_pago op
                 where op.id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer;




          ELSEIF   v_registros.estado = 'en_pago' THEN



                 --registra estado de cotizacion

                  SELECT
                           ps_id_proceso_wf,
                           ps_id_estado_wf,
                           ps_codigo_estado
                     into
                           v_id_proceso_wf,
                           v_id_estado_wf,
                           v_codigo_estado
                  FROM wf.f_registra_proceso_disparado_wf(
                           p_id_usuario,
                           (p_hstore->'_id_usuario_ai')::integer,
                           (p_hstore->'_nombre_usuario_ai')::varchar,
                           v_registros.id_estado_wf,
                           NULL,
                           v_registros.id_depto,
                           ('Solicitud de devengado para la OP:'|| v_registros.numero||' cuota nro'||v_nro_cuota::varchar),
                           v_registros_tpp.codigo_proceso_llave_wf,
                           v_registros.numero||'-N# '||v_nro_cuota::varchar
                         );



          ELSE


           		 raise exception 'Estado no reconocido % ',  v_registros.estado;

          END IF;



          --actualiza la cuota vigente en la obligacion
           update tes.tobligacion_pago  p set
                  nro_cuota_vigente =  v_nro_cuota
           where id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer;



            --Sentencia de la insercion
        	insert into tes.tplan_pago(
			estado_reg,
			nro_cuota,
		    nro_sol_pago,
            id_proceso_wf,
		    estado,
			tipo_pago,
			monto_ejecutar_total_mo,
			obs_descuentos_anticipo,
			id_plan_pago_fk,
			id_obligacion_pago,
			id_plantilla,
			descuento_anticipo,
			otros_descuentos,
			tipo,
			obs_monto_no_pagado,
			obs_otros_descuentos,
			monto,
		    nombre_pago,
            id_estado_wf,
		    id_cuenta_bancaria,
			forma_pago,
			monto_no_pagado,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod,
            liquido_pagable,
            fecha_tentativa,
            --tipo_cambio,
            monto_retgar_mo,
            descuento_ley,
            obs_descuentos_ley,
            porc_descuento_ley,
            nro_cheque,
            nro_cuenta_bancaria,
            id_cuenta_bancaria_mov,
            porc_monto_excento_var,
            monto_excento,
            id_usuario_ai,
            usuario_ai,
            descuento_inter_serv,
            obs_descuento_inter_serv,
            porc_monto_retgar,
            monto_anticipo,
            fecha_costo_ini,
            fecha_costo_fin,
            fecha_conclusion_pago,
            es_ultima_cuota,
            monto_establecido,
            id_proveedor_cta_bancaria
          	) values(
			'activo',
			v_nro_cuota,
			'---',--'nro_sol_pago',
			v_id_proceso_wf,
			v_codigo_estado,
			(p_hstore->'tipo_pago')::varchar,
			(p_hstore->'monto_ejecutar_total_mo')::numeric,
            (p_hstore->'obs_descuentos_anticipo'),
			(p_hstore->'id_plan_pago_fk')::integer,
			(p_hstore->'id_obligacion_pago')::integer,
			(p_hstore->'id_plantilla')::integer,
			COALESCE((p_hstore->'descuento_anticipo')::numeric,0),
			(p_hstore->'otros_descuentos')::numeric,
			(p_hstore->'tipo')::varchar,
			(p_hstore->'obs_monto_no_pagado')::text,
			(p_hstore->'obs_otros_descuentos')::text,
			(p_hstore->'monto')::numeric,
			(p_hstore->'nombre_pago')::varchar,
		    v_id_estado_wf,
			(p_hstore->'id_cuenta_bancaria')::integer,
			(p_hstore->'forma_pago')::varchar,
			(p_hstore->'monto_no_pagado')::numeric,
			now(),
			p_id_usuario,
			null,
			null,
            (p_hstore->'liquido_pagable')::numeric,
            (p_hstore->'fecha_tentativa')::date,
            --(p_hstore->'tipo_cambio')::numeric,
            (p_hstore->'monto_retgar_mo')::numeric,
            (p_hstore->'descuento_ley')::numeric,
            (p_hstore->'obs_descuentos_ley'),
            (p_hstore->'porc_descuento_ley')::numeric,
			COALESCE((p_hstore->'nro_cheque')::integer,0),
			(p_hstore->'nro_cuenta_bancaria')::varchar,
            (p_hstore->'id_cuenta_bancaria_mov')::integer,
            (p_hstore->'porc_monto_excento_var')::numeric,
            COALESCE((p_hstore->'monto_excento')::numeric,0),
            (p_hstore->'id_usuario_ai')::integer,
            (p_hstore->'usuario_ai')::varchar,
             COALESCE((p_hstore->'descuento_inter_serv')::numeric,0),
            (p_hstore->'obs_descuento_inter_serv')::varchar,
            (p_hstore->'porc_monto_retgar')::numeric,
            (p_hstore->'monto_anticipo')::numeric,
            (p_hstore->'fecha_costo_ini')::date,
            (p_hstore->'fecha_costo_fin')::date,
            (p_hstore->'fecha_conclusion_pago')::date,
            true,
            (p_hstore->'monto_establecido')::numeric,
            (p_hstore->'id_proveedor_cta_bancaria')::integer

           )RETURNING id_plan_pago into v_id_plan_pago;

           IF (v_fecha_ini_pp is not Null or v_fecha_fin_pp is not Null or v_fecha_conclusion is not Null) THEN
           update tes.tplan_pago set
           fecha_costo_ini = v_fecha_ini_pp,
           fecha_costo_fin = v_fecha_fin_pp,
           fecha_conclusion_pago = v_fecha_conclusion
           where id_obligacion_pago = (p_hstore->'id_obligacion_pago')::integer;
           END IF;



            -- inserta documentos en estado borrador si estan configurados
            v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);

            -- verificar documentos
            v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_wf);

            --------------------------------------------------
            -- Inserta prorrateo automatico
            ------------------------------------------------
           IF not ( SELECT * FROM tes.f_prorrateo_plan_pago( v_id_plan_pago,
               										 (p_hstore->'id_obligacion_pago')::integer,
                                                     'no',
                                                     (p_hstore->'monto_ejecutar_total_mo')::numeric,
                                                     p_id_usuario)) THEN


              raise exception 'Error al prorratear';

			END IF;



            --si el salto esta habilitado cambiamos la cuota al siguiente estado
            IF p_salta  and v_registros.pago_variable = 'no' THEN
                  IF not tes.f_cambio_estado_plan_pago(p_id_usuario, v_id_plan_pago) THEN
                    raise exception 'error al cambiar de estado';
                  END IF;
            END IF;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Replicacion Plan Pago almacenado(a) con exito (id_plan_pago'||v_id_plan_pago::varchar||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_id_plan_pago::varchar);

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

ALTER FUNCTION tes.f_inserta_plan_pago_replicado (p_administrador integer, p_id_usuario integer, p_hstore public.hstore, p_salta boolean)
  OWNER TO mperez;
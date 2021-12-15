CREATE OR REPLACE FUNCTION tes.ft_cuotas_devengadas_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Obligaciones de Pago
 FUNCION: 		tes.ft_cuotas_devengadas_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tdepto_cuenta_bancaria'
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        26-11-2021 11:41:38
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
    v_registros_tpp 	    record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;

    v_registros 				record;
    v_pre_integrar_presupuestos	varchar;
    v_nombre_conexion			varchar;
    v_centro 					varchar;
    v_sw_retenciones 			varchar;
    v_verficacion 				varchar[];
    v_res						boolean;
    v_result					varchar;


BEGIN

    v_nombre_funcion = 'tes.ft_cuotas_devengadas_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_SOL_DEVENG_IME'
 	#DESCRIPCION:	Registro de solicitud del devengado
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		26-11-2021 11:45:38
	***********************************/

	if(p_transaccion='TES_SOL_DEVENG_IME')then

        begin

        	select
             pp.*,op.total_pago,op.comprometido, op.id_contrato, op.tipo_obligacion
           into
             v_registros
           from tes.tplan_pago pp
           inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
           where pp.id_plan_pago = v_parametros.id_plan_pago;

           --raise exception 'Aqui llega el dato %',v_registros.id_int_comprobante;

		   v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');

          IF v_pre_integrar_presupuestos = 'true' THEN
                 /*jrr:29/10/2014
                 1) si el presupuesto no esta comprometido*/

                 if (v_registros.comprometido = 'no') then

                      /*1.1)Validar que la suma de los detalles igualen al total de la obligacion*/
                     if ((select sum(od.monto_pago_mo)
                          from tes.tobligacion_det od
                          where id_obligacion_pago = v_registros.id_obligacion_pago and estado_reg = 'activo') != v_registros.total_pago) THEN
                          raise exception 'La suma de todos los detalles no iguala con el total de la obligacion. La diferencia se genero al modificar la apropiacion';
                      end if;

                      /*1.2 Comprometer*/
                      select * into v_nombre_conexion from migra.f_crear_conexion();

                      select tes.f_gestionar_presupuesto_tesoreria(v_registros.id_obligacion_pago, p_id_usuario, 'comprometer',NULL,v_nombre_conexion) into v_res;

                      if v_res = false then
                          raise exception 'Error al comprometer el presupuesto';
                      end if;

                      update tes.tobligacion_pago
                      set comprometido = 'si'
                      where id_obligacion_pago = v_registros.id_obligacion_pago;

                end if;

            END IF;

          --(may) 20-07-2019 los tipo plan de pago devengado_pagado_1c_sp son para las internacionales -tramites sp con contato
          IF  v_registros.tipo  in ('pagado' ,'devengado_pagado','devengado_pagado_1c','anticipo','ant_parcial','devengado_pagado_1c_sp') and v_registros.tipo_obligacion != 'pago_pvr' THEN

                  IF v_registros.forma_pago = 'cheque' THEN

                      IF  v_registros.nro_cheque is NULL THEN

                         raise exception  'Tiene que especificar el  nro de cheque';

                      END IF;
                   ELSE

                 	  --IF  v_registros.nro_cuenta_bancaria  = '' or  v_registros.nro_cuenta_bancaria is NULL THEN
                      IF v_registros.id_proveedor_cta_bancaria is NULL THEN

                         raise exception  'Tiene que especificar el nro de cuenta destino, para la transferencia bancaria';

                      END IF;


                  END IF;


                  IF v_registros.id_cuenta_bancaria is NULL THEN
                     raise exception  'Tiene que especificar la cuenta bancaria origen de los fondos';
                  END IF ;



                   --validacion de deposito, (solo BOA, puede retirarse)
                   IF v_registros.id_cuenta_bancaria_mov is NULL THEN

                        --TODO verificar si la cuenta es de centro

                       select
                           cb.centro
                       into
                           v_centro
                       from tes.tcuenta_bancaria cb
                       where cb.id_cuenta_bancaria = v_registros.id_cuenta_bancaria;



                        IF  v_registros.nro_cuenta_bancaria  = '' or  v_registros.nro_cuenta_bancaria is NULL THEN

                             IF  v_centro = 'no' THEN

                              raise exception  'Tiene que especificar el deposito  origen de los fondos';

                             END IF;


                        END IF;

                   END IF ;

           END IF;


           --si es un pago de vengado , revisar si tiene contrato
           --si tiene contrato con renteciones de garantia validar que la rentecion de garantia sea mayor a cero
           --(may) 20-07-2019 los tipo plan de pago devengado_pagado_1c_sp son para las internacionales -tramites sp con contato
           IF  v_registros.tipo in ('devengado','devengado_pagado','devengado_pagado_1c', 'devengado_pagado_1c_sp') THEN

               IF v_registros.id_contrato is not null THEN

                   v_sw_retenciones = 'no';
                   select
                     c.tiene_retencion
                   into
                     v_sw_retenciones
                   from leg.tcontrato c
                   where c.id_contrato = v_registros.id_contrato;

                   IF v_sw_retenciones = 'si' and  v_registros.monto_retgar_mo = 0 THEN

                      IF v_registros.monto != v_registros.descuento_inter_serv THEN
                        raise exception 'Según contrato este pago debe tener retenciones de garantia';
                      END IF;
                   END IF;

               END IF;

           END IF;

           /*if v_registros.tipo_obligacion = 'pago_pvr' then
            v_verficacion = tes.f_generar_comprobante_pvr(
                                                      p_id_usuario,
                                                      v_parametros._id_usuario_ai,
                                                      v_parametros._nombre_usuario_ai,
                                                      v_parametros.id_plan_pago,
                                                      v_parametros.id_depto_conta,
                                                      v_nombre_conexion);
           else*/

           /*Aqui eliminamos el comprobante relacionado al plan de pago (Ismael Valdivia 3DIC2021)*/
            if (v_registros.id_int_comprobante is not null) then
            v_result = conta.f_eliminar_int_comprobante(p_id_usuario,
                                                    v_parametros._id_usuario_ai,
                                                    v_parametros._nombre_usuario_ai,
                                                    v_registros.id_int_comprobante,
                                                    'si');
            end if;
            /***************************************************************************************/


            v_verficacion = tes.f_generar_comprobante_devengado(
                                                      p_id_usuario,
                                                      v_parametros._id_usuario_ai,
                                                      v_parametros._nombre_usuario_ai,
                                                      v_parametros.id_plan_pago,
                                                      v_parametros.id_depto_conta,
                                                      v_nombre_conexion);
           --end if;
		  --raise exception 'Aqui llega fin';
          --select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');
          --raise exception 'Aqui acaba';
          v_resp = '';

          IF  v_verficacion[1]= 'TRUE'   THEN

             --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Solitud de generacion de comprobante desde interface de plan de pagos');
                v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

                --Devuelve la respuesta
                return v_resp;

            ELSE

                --Definicion de la respuesta


                v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'resultado','falla');
                v_resp = pxp.f_agrega_clave(v_resp,'qwe','123');
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_verficacion[2]);

                --Devuelve la respuesta
                return v_resp;


            END IF;


		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

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

ALTER FUNCTION tes.ft_cuotas_devengadas_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;

CREATE OR REPLACE FUNCTION tes.f_sig_stado_ob (
  p_administrador integer,
  p_id_usuario integer,
  v_parametros public.hstore
)
RETURNS boolean AS
$body$
/*
Autor: Franklin Espinoza A.
Fecha: 26/07/2017
Descripción: Funcion que se llama una vez que se habilita el pago en el sistem Adquisiciones
despues de crear la obligacion de pago para pasar el proceso al estado en_pago es el contenido
de la transaccion TES_SIGESTOB_IME.
*/
DECLARE

	v_id_funcionario	integer;
    v_record			record;
    v_id_usuario		integer;
    v_acceso_directo	varchar;
    v_clase 			varchar;
    v_parametros_ad 	varchar;
    v_tipo_noti			varchar;
    v_titulo			varchar;
    v_id_estado_actual	integer;

    v_fecha				date;

    v_codigo_estado varchar;

    ----------------------------------
    v_id_proceso_wf 		integer;
    v_id_estado_wf 			integer;
    v_id_depto 				integer;
    v_tipo_obligacion 		varchar;
    v_total_nro_cuota  integer;
    v_fecha_pp_ini		date;
    v_rotacion			integer;
    v_id_plantilla 	integer;
    v_tipo_cambio_conv numeric;
    v_desc_proveedor   text;
    v_pago_variable	varchar;
    v_comprometido 			varchar;
    v_id_usuario_reg_op        integer;
    v_fecha_op					date;

    v_total_detalle 		numeric;
    v_id_uo					integer;
    va_id_funcionarios			integer[];
    v_id_tipo_estado 		integer;
    v_codigo_estado_siguiente	varchar;
    v_registros_proc 			record;
    v_codigo_tipo_pro   		varchar;
    v_registros_op_ori record;
    v_saldo_x_pagar numeric;
    v_registros_pp_origen record;
    v_hstore_registros hstore;
    v_id_estado_wf_pp  VARCHAR[];
     v_id_proceso_wf_pp varchar[];
     v_id_plan_pago_pp varchar[];
     v_id_estado_actual_pp integer;
     v_id_tipo_estado_pp integer;
     v_monto_ajuste_ret_garantia_ga  numeric;
     v_monto_ajuste_ret_anticipo_par_ga  numeric;

     v_registros_plan   record;
     v_monto_cuota 		numeric;
     v_i                integer;
     v_descuentos_ley   numeric;
     v_tipo_plan_pago		varchar;
     va_tipo_pago			varchar[];
     v_hstore_pp		hstore;
     v_sw_saltar 				boolean;
     v_resp		            varchar;
     v_pre_integrar_presupuestos	varchar;
      v_obs varchar;
     v_ultima_cuota		boolean;
     v_adq_comprometer_presupuesto			varchar;

BEGIN
	--raise exception 'positivo %',v_parametros;
    --raise exception 'positivo %',(v_parametros->'id_obligacion_pago')::integer;
	 --Procesamos sig. Estado de Obligacion de Pago
 	--obtenermos datos basicos
         select
              op.id_proceso_wf,
              op.id_estado_wf,
              op.estado,
              op.id_depto,
              op.tipo_obligacion,
              op.total_nro_cuota,
              op.fecha_pp_ini,
              op.rotacion,
              op.id_plantilla,
              op.tipo_cambio_conv,
              pr.desc_proveedor,
              op.pago_variable,
              op.comprometido,
              op.id_usuario_reg,
              op.fecha

             into
              v_id_proceso_wf,
              v_id_estado_wf,
              v_codigo_estado,
              v_id_depto,
              v_tipo_obligacion,
              v_total_nro_cuota,
              v_fecha_pp_ini,
              v_rotacion,
              v_id_plantilla,
              v_tipo_cambio_conv,
              v_desc_proveedor,
              v_pago_variable,
              v_comprometido,
              v_id_usuario_reg_op,
              v_fecha_op
           from tes.tobligacion_pago op
           left join param.vproveedor pr  on pr.id_proveedor = op.id_proveedor
           where op.id_obligacion_pago = (v_parametros->'id_obligacion_pago')::integer;

          -------------------------------------------------
          --  Validamos que la solicitud tengan contenido
          --------------------------------------------------

           IF  v_codigo_estado in ('borrador','vbpoa','vbpresupuestos','liberacion') THEN
                  --validamos que el detalle tenga por lo menos un item con valor
                   select
                    sum(od.monto_pago_mo)
                   into
                    v_total_detalle
                   from tes.tobligacion_det od
                   where od.id_obligacion_pago = (v_parametros->'id_obligacion_pago')::integer and od.estado_reg ='activo';

                   IF v_total_detalle = 0 or v_total_detalle is null THEN
                      raise exception 'No existe el detalle de obligacion...';
                   END IF;
                  ------------------------------------------------------------
                  --calcula el factor de prorrateo de la obligacion  detalle
                  -----------------------------------------------------------
                  IF (tes.f_calcular_factor_obligacion_det((v_parametros->'id_obligacion_pago')::integer) != 'exito')  THEN
                      raise exception 'error al calcular factores';
                  END IF;
           END IF;

           ----------------------------------------------------------------------------------------------------------
           --  valida si tiene algun concepto en la tabla de excepciones, si es asi cambi la gerencia de aprobación
           ----------------------------------------------------------------------------------------------------------

           IF  v_codigo_estado = 'borrador'  THEN

                    SELECT
                      ce.id_uo
                    into
                      v_id_uo
                    FROM tes.tconcepto_excepcion ce
                    where   ce.estado_reg = 'activo'  and
                             ce.id_concepto_ingas in (select
                                                    id_concepto_ingas
                                                   from tes.tobligacion_det od
                                                   where od.id_obligacion_pago = (v_parametros->'id_obligacion_pago')::integer
                                                   and od.estado_reg = 'activo' )
                    limit 1 OFFSET 0;

                    --si existe una excepcion cambiar el funcionar aprobador

                    IF v_id_uo is NOT NULL THEN
                         --recuperamos el aprobador

                         va_id_funcionarios =  orga.f_get_funcionarios_x_uo(v_id_uo, v_fecha_op);

                        IF va_id_funcionarios[1] is NULL THEN
                           raise exception 'La UO configurada por excpeción no tiene un funcionario asignado para le fecha de la OP';
                        END IF;

                        update tes.tobligacion_pago o set
                          id_funcionario_gerente = va_id_funcionarios[1],
                          uo_ex = 'si'
                        where o.id_obligacion_pago = (v_parametros->'id_obligacion_pago')::integer;

                     END IF ;
            END IF;

          -- recupera datos del estado

           select
            ew.id_tipo_estado ,
            te.codigo
           into
            v_id_tipo_estado,
            v_codigo_estado
          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf = (v_parametros->'id_estado_wf_act')::integer;



           -- obtener datos tipo estado
           select
                 te.codigo
            into
                 v_codigo_estado_siguiente
           from wf.ttipo_estado te
           where te.id_tipo_estado = (v_parametros->'id_tipo_estado')::integer;

           --IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN
              v_id_depto = (v_parametros->'id_depto_wf')::integer;
           --END IF;

           --IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                  v_obs=(v_parametros->'obs')::varchar;
          /* ELSE
                  v_obs='---';
           END IF;*/

           ---------------------------------------
           -- REGISTA EL SIGUIENTE ESTADO DEL WF.
           ---------------------------------------
           v_id_estado_actual =  wf.f_registra_estado_wf(  (v_parametros->'id_tipo_estado')::integer,
                                                           (v_parametros->'id_funcionario_wf')::integer,
                                                           (v_parametros->'id_estado_wf_act')::integer,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           (v_parametros->'_id_usuario_ai')::integer,
                                                           (v_parametros->'_nombre_usuario_ai')::varchar,
                                                           v_id_depto,
                                                           v_obs);

          --------------------------------------
          -- registra los procesos disparados
          --------------------------------------

          FOR v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf,(v_parametros->'json_procesos')::json)) LOOP

               -- get cdigo tipo proceso
               select
                  tp.codigo
               into
                  v_codigo_tipo_pro
               from wf.ttipo_proceso tp
               where  tp.id_tipo_proceso =  v_registros_proc.id_tipo_proceso_pro;


              -- disparar creacion de procesos seleccionados
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
                       (v_parametros->'_id_usuario_ai')::integer,
                       (v_parametros->'_nombre_usuario_ai')::varchar,
                       v_id_estado_actual,
                       v_registros_proc.id_funcionario_wf_pro,
                       v_registros_proc.id_depto_wf_pro,
                       v_registros_proc.obs_pro,
                       v_codigo_tipo_pro,
                       v_codigo_tipo_pro);


           END LOOP;

          -- si es estado actual es vbpresupeustos registras las observaciones de presupeustos
           IF  v_codigo_estado  in  ('vbpresupuestos') THEN
                 update tes.tobligacion_pago  set
                  obs_presupuestos = (v_parametros->'obs')::varchar
                 where id_obligacion_pago  = (v_parametros->'id_obligacion_pago')::integer;
           END IF;

          --------------------------------------------------
          --  ACTUALIZA EL NUEVO ESTADO DE LA OBLIGACION
          ----------------------------------------------------

           update tes.tobligacion_pago  set
             id_estado_wf =  v_id_estado_actual,
             estado = v_codigo_estado_siguiente,
             id_usuario_mod = p_id_usuario,
             total_pago = v_total_detalle,
             fecha_mod = now(),
             id_usuario_ai = (v_parametros->'_id_usuario_ai')::integer,
             usuario_ai = (v_parametros->'_nombre_usuario_ai')::varchar
           where id_obligacion_pago  = (v_parametros->'id_obligacion_pago')::integer;



          ---------------------------------------
          --  CHEQUEAR OBLIGACIONES EXTENDIDAS
          ---------------------------------------

          IF  v_codigo_estado_siguiente = 'registrado'  THEN  -- solo si esta pasando al estado registrado

                  --  chequear si es una obligacion de pago extendida,
                  select
                       op.id_obligacion_pago,
                       op.num_tramite,
                       op.id_depto,
                       op.id_depto_conta
                  into
                      v_registros_op_ori
                  from tes.tobligacion_pago op
                  where op.id_obligacion_pago_extendida = (v_parametros->'id_obligacion_pago')::integer;

                 --  si es una obligacion de pago extendida


                 IF v_registros_op_ori is not NULL THEN

                        -- Chequear si la obligacion original tiene un saldo anticipado
                        v_saldo_x_pagar = 0;
                        v_saldo_x_pagar = tes.f_determinar_total_faltante(v_registros_op_ori.id_obligacion_pago,'anticipo_sin_aplicar');



                        IF  v_saldo_x_pagar > 0 THEN
                           -- Si tiene saldo anticipado validar el monto presupuesto es suficiente para  cubrir este anticipo
                           IF  v_saldo_x_pagar > v_total_detalle   THEN
                              raise exception 'El total presupuestado no es suficiente para  cubrir el saldo anticipado en la gestión anterior. Saldo anticipado (%)', v_saldo_x_pagar;
                           END IF;
                           -- Recupera la ultima plantilla de  documento con saldo anticipado
                            select
                              *
                           INTO
                              v_registros_pp_origen
                           from tes.tplan_pago pp
                           where
                                 (pp.monto_anticipo > 0 or  pp.tipo = 'anticipo' ) and
                                 pp.estado_reg = 'activo'  and
                                 pp.id_obligacion_pago = v_registros_op_ori.id_obligacion_pago

                           order by pp.nro_cuota desc  LIMIT 1 OFFSET 0;

                           -- insertar un plan de pagos de anticipo en estado anticipado
                           -- con el monto del saldo listo para colgar aplicaciones

                           v_hstore_registros =   hstore(ARRAY[
                                                  'id_cuenta_bancaria', v_registros_pp_origen.id_cuenta_bancaria::varchar,
                                                  'id_cuenta_bancaria_mov', v_registros_pp_origen.id_cuenta_bancaria_mov::varchar,
                                                  'forma_pago', v_registros_pp_origen.forma_pago::varchar,
                                                  'nro_cheque', v_registros_pp_origen.nro_cheque::varchar,
                                                  'nro_cuenta_bancaria',v_registros_pp_origen.nro_cuenta_bancaria::varchar,
                                                  'monto', v_saldo_x_pagar::varchar,
                                                  'id_obligacion_pago', (v_parametros->'id_obligacion_pago')::integer,
                                                  'monto_retgar_mo', '0',
                                                  'descuento_ley', v_registros_pp_origen.descuento_ley::varchar,
                                                  'descuento_anticipo', '0',
                                                  'otros_descuentos', '0',
                                                  'monto_no_pagado', '0',
                                                  'fecha_tentativa', now()::varchar,
                                                  'id_plantilla', v_registros_pp_origen.id_plantilla::varchar,
                                                  'tipo', 'anticipo',
                                                  'porc_monto_excento_var', v_registros_pp_origen.porc_monto_excento_var::varchar,
                                                  'monto_excento', v_registros_pp_origen.monto_excento::varchar,
                                                  'tipo_cambio', v_registros_pp_origen.tipo_cambio::varchar,
                                                  'porc_descuento_ley', v_registros_pp_origen.porc_descuento_ley::varchar,
                                                  'descuento_inter_serv', 0::varchar,
                                                  '_id_usuario_ai', (v_parametros->'_id_usuario_ai')::varchar,
                                                  '_nombre_usuario_ai', (v_parametros->'_nombre_usuario_ai')::varchar,
                                                  'nombre_pago', v_registros_pp_origen.nombre_pago::varchar,
                                                  'id_plan_pago_fk',NULL::varchar,
                                                  'porc_monto_retgar', '0',
                                                  'monto_ajuste_ag', '0'
                                                ]);


                               -- llamada para insertar plan de pagos
                               v_resp = tes.f_inserta_plan_pago_anticipo(p_administrador, p_id_usuario, v_hstore_registros);
                               v_id_estado_wf_pp =  pxp.f_recupera_clave(v_resp, 'id_estado_wf');
                               v_id_proceso_wf_pp =  pxp.f_recupera_clave(v_resp, 'id_proceso_wf');
                               v_id_plan_pago_pp =  pxp.f_recupera_clave(v_resp, 'id_plan_pago');

                               --  cambia de estado el plan de pago,lo lleva a anticipado
                               select
                                te.id_tipo_estado
                               into
                                v_id_tipo_estado_pp
                               from wf.ttipo_estado te
                               inner join wf.tproceso_wf  pw on pw.id_tipo_proceso = te.id_tipo_proceso
                                    and pw.id_proceso_wf = v_id_proceso_wf_pp[1]::integer
                               where te.codigo = 'anticipado';

                               IF v_id_tipo_estado_pp is  null THEN
                                raise exception 'El proceso de WF esta mal parametrizado, no tiene el estado Visto bueno contabilidad (vbconta) ';
                               END IF;


                             -- registrar el siguiente estado detectado  (vbconta)
                             v_id_estado_actual_pp =  wf.f_registra_estado_wf(v_id_tipo_estado_pp,
                                                                           NULL,
                                                                           v_id_estado_wf_pp[1]::integer,
                                                                           v_id_proceso_wf_pp[1]::integer,
                                                                           p_id_usuario,
                                                                           (v_parametros->'_id_usuario_ai')::integer,
                                                           				   (v_parametros->'_nombre_usuario_ai')::varchar,
                                                                           v_id_depto,
                                                                           'Se lleva el anticipo a finalizado por saldo de la anterior gestion ('||v_registros_op_ori.num_tramite ||')');


                              -- actualiza el nuevo estado para el anticipo
                              update tes.tplan_pago pp  set
                                     id_estado_wf =  v_id_estado_actual_pp,
                                     estado = 'anticipado'
                              where id_plan_pago  = v_id_plan_pago_pp[1]::integer;

                        END IF;

                        -- Chequear si tiene dev de garantia pendientes,
                        v_monto_ajuste_ret_garantia_ga = 0;
                        v_monto_ajuste_ret_garantia_ga = tes.f_determinar_total_faltante(v_registros_op_ori.id_obligacion_pago,'dev_garantia');

                        -- chequear si tiene retenciones pendientes de anticipos parciales
                        v_monto_ajuste_ret_anticipo_par_ga = 0;
                        v_monto_ajuste_ret_anticipo_par_ga = tes.f_determinar_total_faltante(v_registros_op_ori.id_obligacion_pago,'ant_parcial_descontado');

                        update tes.tobligacion_pago  set
                         monto_ajuste_ret_garantia_ga = COALESCE(v_monto_ajuste_ret_garantia_ga,0),
                         monto_ajuste_ret_anticipo_par_ga = COALESCE(v_monto_ajuste_ret_anticipo_par_ga,0)
                       where id_obligacion_pago  = (v_parametros->'id_obligacion_pago')::integer;

                 END IF;

            END IF;

          -------------------------------------------
          --  VERIFICA SI ES NECESARIO UN CONTRATO
          -----------------------------------------
           IF  v_codigo_estado = 'borrador'  THEN

                 IF not tes.f_validar_contrato((v_parametros->'id_obligacion_pago')::integer) THEN
                   raise exception 'contrato no validao';
                 END IF;

           END IF;

          --------------------------------------------------
          --  INSERCION AUTOMATICA DE CUOTAS
          --------------------------------------------------

          --  TODO considerar el saldo de anticipo, menos  el total a pagar para determinar el monto, considerar numero de cuota
          --  si llegando al estado registrado,  verifica el total de cuotas y las inserta con la plantilla por defecto

          IF  v_codigo_estado_siguiente = 'registrado'  and v_total_nro_cuota > 0 THEN

                      select
                       ps_descuento_porc,
                       ps_descuento,
                       ps_observaciones
                     into
                      v_registros_plan
                     FROM  conta.f_get_descuento_plantilla_calculo(v_id_plantilla);

                     /*jrr(10/10/2014): En caso de que sea pago variable el valor de la cuota sera 0*/

                     if (v_pago_variable = 'si') then
                      	v_monto_cuota = 0;
                     else
                     	v_monto_cuota =  (v_total_detalle::numeric/v_total_nro_cuota::numeric)::numeric(19,1);
                     end if;

                     FOR v_i  IN 1..v_total_nro_cuota LOOP
                         IF v_i = v_total_nro_cuota THEN
                            v_monto_cuota = v_total_detalle - (v_monto_cuota*v_total_nro_cuota) + v_monto_cuota;
                         	/*jrr(10/10/2014): En caso de que sea pago variable el valor de la cuota sera 0*/
                            if (v_pago_variable = 'si') then
                              v_monto_cuota = 0;
                           	end if;
                            v_ultima_cuota = TRUE;
                         ELSE
                         	v_ultima_cuota = FALSE;
                         END IF;

                         v_descuentos_ley = v_monto_cuota * v_registros_plan.ps_descuento_porc;




                         IF v_tipo_obligacion in  ('pago_especial') THEN
                            v_tipo_plan_pago = 'especial';
                         ELSE
                           --verifica que tipo de apgo estan deshabilitados

                           va_tipo_pago = regexp_split_to_array(pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'), E'\\s+');

                           v_tipo_plan_pago = 'devengado_pagado';

                           IF v_tipo_plan_pago =ANY(va_tipo_pago) THEN
                                  v_tipo_plan_pago = 'devengado_pagado_1c';
                           END IF;

                         END IF;



                         --armar hstore
                         v_hstore_pp =   hstore(ARRAY[
                                                        'tipo_pago',
                                                        'normal',
                                                        'tipo',
                                                        v_tipo_plan_pago,
                                                        'tipo_cambio',v_tipo_cambio_conv::varchar,
                                                        'id_plantilla',v_id_plantilla::varchar,
                                                        'id_obligacion_pago',(v_parametros->'id_obligacion_pago')::varchar,
                                                        'monto_no_pagado','0',
                                                        'monto_retgar_mo','0',
                                                        'otros_descuentos','0',
                                                        'monto_excento','0',
                                                        'id_plan_pago_fk',NULL::varchar,
                                                        'porc_descuento_ley',v_registros_plan.ps_descuento_porc::varchar,
                                                        'obs_descuentos_ley',v_registros_plan.ps_observaciones::varchar,
                                                        'obs_otros_descuentos','',
                                                        'obs_monto_no_pagado','',
                                                        'nombre_pago',v_desc_proveedor::varchar,
                                                        'monto', v_monto_cuota::varchar,
                                                        'descuento_ley',v_descuentos_ley::varchar,
                                                        'fecha_tentativa',v_fecha_pp_ini::varchar,
                                                        '_id_usuario_ai',(v_parametros->'_id_usuario_ai')::varchar,
                                                        '_nombre_usuario_ai', (v_parametros->'_nombre_usuario_ai')::varchar,
                                                        'ultima_cuota',v_ultima_cuota::varchar
                                                       ]);


                            --TODO,  bloquear en formulario de OP  facturas con monto excento


                            -- si es un proceso de pago unico,  la primera cuota pasa de borrador al siguiente estado de manera automatica
                            IF  v_tipo_obligacion = 'pago_unico' and   v_i = 1   THEN
                               v_sw_saltar = TRUE;
                            else
                               v_sw_saltar = FALSE;
                            END IF;

                            -- llamada para insertar plan de pagos
                            v_resp = tes.f_inserta_plan_pago_dev(p_administrador, v_id_usuario_reg_op,v_hstore_pp, v_sw_saltar);

                            -- calcula la fecha para la siguiente insercion
                            v_fecha_pp_ini =  v_fecha_pp_ini + interval  '1 month'*v_rotacion;
                     END LOOP;
          END IF;


           IF  v_codigo_estado = 'borrador'   and v_tipo_obligacion != 'adquisiciones' and v_tipo_obligacion != 'pago_especial' and   v_pre_integrar_presupuestos = 'true'  THEN

               --si es borrador verificamos que el presupeusto sea suficiente para proseguir con la ordenç
               IF not tes.f_gestionar_presupuesto_tesoreria((v_parametros->'id_obligacion_pago')::integer, p_id_usuario, 'verificar')  THEN
                   raise exception 'Error al verificar  presupeusto';
               END IF;

           END IF;


          -----------------------------------------------------------------------------
          -- COMPROMISO PRESUPUESTARIO
          -- cuando pasa al estado registrado y el presupeusto no esta comprometido
          ------------------------------------------------------------------------------
          IF  v_codigo_estado_siguiente = 'registrado'  and  v_comprometido = 'no' and v_tipo_obligacion != 'adquisiciones' and v_tipo_obligacion != 'pago_especial' and   v_pre_integrar_presupuestos = 'true'  THEN

          		--jrr: llamamos a la funcion que revierte de planillas en caso de que sea de recursos humanos
                if (v_tipo_obligacion = 'rrhh') then
                    IF NOT plani.f_generar_pago_tesoreria(p_administrador,p_id_usuario,(v_parametros->'_id_usuario_ai')::integer,
                    (v_parametros->'_nombre_usuario_ai')::varchar,(v_parametros->'id_obligacion_pago')::integer,v_obs) THEN
                         raise exception 'Error al generar el pago de devengado';
                      END IF;
                end if;
               --TODO aumentar capacidad de rollback
               -- verficar presupuesto y comprometer
               IF not tes.f_gestionar_presupuesto_tesoreria((v_parametros->'id_obligacion_pago')::integer, p_id_usuario, 'comprometer')  THEN
                   raise exception 'Error al comprometer el presupeusto';
               END IF;

               v_comprometido = 'si';
               --cambia la bandera del comprometido
               update tes.tobligacion_pago  set
                 comprometido = v_comprometido
               where id_obligacion_pago  = (v_parametros->'id_obligacion_pago')::integer;



           END IF;

           --RAC 02/08/2017
           --verifica si el presupeusto fue comprometido en adquisicioens o no

              v_adq_comprometer_presupuesto = pxp.f_get_variable_global('adq_comprometer_presupuesto');

           IF          v_codigo_estado_siguiente = 'registrado'
                  and  v_comprometido = 'no'
                  and  v_tipo_obligacion = 'adquisiciones'
                  and  v_adq_comprometer_presupuesto = 'no'
                  and  v_pre_integrar_presupuestos = 'true'  THEN

                       -- verficar presupuesto y comprometer
                       IF not tes.f_gestionar_presupuesto_tesoreria(v_parametros.id_obligacion_pago, p_id_usuario, 'comprometer')  THEN
                           raise exception 'Error al comprometer el presupeusto';
                       END IF;

                       v_comprometido = 'si';
                       --cambia la bandera del comprometido
                       update tes.tobligacion_pago  set
                         comprometido = v_comprometido
                       where id_obligacion_pago  = v_parametros.id_obligacion_pago;

           END IF;

           -- cuando viene de adquisiciones no es necesario comprometer pero dejamos la bancera de compromiso barcada
           --  ya que los montos se comprometiron en la solicitud de compra

           IF v_codigo_estado_siguiente = 'registrado'  and  v_comprometido = 'no' and v_tipo_obligacion in  ('adquisiciones','pago_especial') THEN
               v_comprometido = 'si';
               --cambia la bandera del comprometido
               update tes.tobligacion_pago  set
                 comprometido = v_comprometido
               where id_obligacion_pago  = (v_parametros->'id_obligacion_pago')::integer;
           END IF;

    RETURN TRUE;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
CREATE OR REPLACE FUNCTION tes.f_plan_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.f_plan_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tplan_pago'
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

	v_nro_requerimiento    	integer;
	v_parametros           	record;
    v_registros_tpp 	    record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_plan_pago			integer;
    v_estado_aux			varchar;
    v_sw_retenciones 		varchar;
    v_otros_descuentos_mb 	numeric;
    v_monto_no_pagado_mb 	numeric;
    v_descuento_anticipo_mb numeric;
    v_monto_anticipo 		numeric;
    v_monto_total 			numeric;
    v_resp_doc   			boolean;

    v_nro_cuota 			numeric;

    v_registros record;
     va_id_tipo_estado_pro 	integer[];
    va_codigo_estado_pro 	varchar[];
    va_disparador_pro 		varchar[];
    va_regla_pro 			varchar[];
    va_prioridad_pro 		integer[];

    v_id_estado_actual 		integer;


    v_id_proceso_wf 		integer;
    v_id_estado_wf 			integer;
    v_codigo_estado 		varchar;

    v_monto_mb 				numeric;
    v_liquido_pagable 		numeric;

    v_liquido_pagable_mb 	numeric;

    v_monto_ejecutar_total_mo numeric;
    v_monto_ejecutar_total_mb numeric;

    v_tipo 					varchar;
    v_id_tipo_estado 		integer;
    v_fecha_tentativa 		date;
    v_monto 				numeric;
    v_cont 					integer;
    v_id_prorrateo 			integer;

    v_tipo_sol 				varchar;

    va_id_tipo_estado 		integer[];
    va_codigo_estado 		varchar[];
    va_disparador    		varchar[];
    va_regla         		varchar[];
    va_prioridad     		integer[];

    v_monto_ejecutar_mo 	numeric;

    v_count 				integer;
    v_registros_pp 			record;

    v_verficacion 			varchar[];

    v_perdir_obs 			varchar;
    v_num_estados 			integer;
    v_num_funcionarios  	integer;
    v_num_deptos  			integer;

    v_id_funcionario_estado integer;
    v_id_depto_estado 		integer;

    v_num_obliacion_pago 	varchar;
    v_codigo_estado_siguiente  varchar;
    v_id_depto  			INTEGER;
    v_obs 					varchar;

    v_id_funcionario     	integer;
    v_id_usuario_reg        integer;
    v_id_estado_wf_ant 		integer;

    v_id_cuenta_bancaria 		integer;
    v_id_cuenta_bancaria_mov integer;


    v_forma_pago 			varchar;

    v_nro_cheque 			integer;

    v_nro_cuenta_bancaria  	varchar;

    v_centro 				varchar;

    v_sw_me_plantilla   	varchar;

    v_porc_monto_excento_var numeric;
    v_monto_excento 		 numeric;

    v_total_prorrateo        numeric;
    v_registros_proc        record;
    v_codigo_tipo_pro       varchar;


    v_acceso_directo  		varchar;
    v_clase   				varchar;
    v_parametros_ad 	  	varchar;
    v_tipo_noti  			varchar;
    v_titulo   				varchar;
    v_codigo_proceso_llave_wf  varchar;
    v_porc_monto_retgar        numeric;
    v_porc_ant				   numeric;
    v_monto_ant_parcial_descontado  numeric;
    v_saldo_x_descontar			 numeric;
    v_saldo_x_pagar 			numeric;
    v_revisado					 varchar;
    v_check_ant_mixto			 numeric;
    v_nombre_conexion			 varchar;
    v_res						boolean;
    v_tipo_obligacion			varchar;
    v_operacion 				varchar;
    v_id_depto_lb				integer;
    v_id_usuario_firma			integer;
    v_id_persona				integer;
    v_exception_detail			varchar;
    v_exception_context			varchar;
    v_id_uo						integer;
    v_especial					numeric;
    v_correo_conformidad_pago   boolean;
    v_record					record;
    v_anio_gestion			 	    integer;

    --(F.E.A) VARIABLES PARA DEFINIR LA ULTIMA CUOTA Y ENVIAR CORREO CUANDO ES ULTIMA CUOTA PARA INDICAR QUE ADJUNTE FORM. 500.
	v_reg_plan_pago				record;
    v_bandera					boolean=false;
    v_cont_plan_pago		    integer;
    v_registros_aux				record;
    v_descripcion 				varchar;
    v_desc_persona				varchar;
    v_id_alarma					integer;

    v_fecha_op					varchar;
    v_anio_op					integer;
    v_num_tramite				varchar;
    v_anio_ges					integer;

    v_sum_monto_pp				numeric;
    v_sum_monto_pe				numeric;
    v_sum_total_pp				numeric;
    v_sum_monto_solo_pp			numeric;

    v_forma_pago_cb				varchar;
    v_nro_cuenta				varchar;

    v_monto_establecido			numeric;
    v_porcentaje13_monto		numeric;
    v_codigo_tipo_relacion		varchar;

    --variables para internacionales
    v_res_cone  				varchar;
    v_cadena_cnx 				varchar;
    v_cadena_inter				varchar;
    v_obligacion_pago_json  	json;
    v_obligacion_det_json		json;
    v_obligacion_pp_json		json;
    v_obligacion_pago			integer;

    v_documentos 				json;
    v_pre_integrar_presupuestos	varchar;

    v_id_depto_lb_pp			integer;
    v_prorrateo_json			json;
    v_cuenta_bancaria_benef		varchar;
    v_id_proveedor_cta_bancaria	integer;

    v_estado					varchar;

    v_parametros_pp				record;
    v_monto_pp					numeric;
    v_id_plantilla				integer;
    v_id_obligacion_pago_pp		integer;
    v_numero_tramite			varchar;

    vtipo_pp					varchar;


       --04/12/2019 (Alan) actualizar el funcionario gerente si es necesario
    va_id_funcionario_gerente		INTEGER[];
    v_fun_gerente					integer;
    v_funcionario						integer;

    v_prioridad					integer;

    -- 14-06-2021 (may)
    v_registros_pro				record;
    v_comprometido				numeric;
    v_ejecutado					numeric;
    v_centro_costo				varchar;
    v_desc_ingas				varchar;
    v_partida					varchar;
    v_sw_verificacion  			boolean;
    v_mensaje_verificacion 		varchar;
    v_respuesta 				varchar[];
    v_id_moneda					integer;

    --23-02-2022 (may)
	v_sw_movimiento_partida		varchar;


BEGIN

    v_nombre_funcion = 'tes.f_plan_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);


	/*********************************
 	#TRANSACCION:  'TES_PLAPA_INS'
 	#DESCRIPCION:	Insercion de cuotas de devengado en el plan de pago
 	#AUTOR:		RAC KPLIAN
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	if(p_transaccion='TES_PLAPA_INS')then

        begin

        	--valida fechas de costos
			 IF v_parametros.fecha_costo_fin <  v_parametros.fecha_costo_ini THEN
               raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
            END IF;

            --17-06-2020 (may) para el usuario MAMANI CALLISAYA VERONICA=615 solo manejara con un solo comprobante devengado_pagado_1c que es de combustible
            IF (p_id_usuario = 615 and v_parametros.tipo != 'devengado_pagado_1c') THEN
            	RAISE EXCEPTION 'Usted únicamente puede realizar el pago con el tipo de cuota de Aplicación de Anticipo(Combustible).' ;
            END IF;
            --

            /*--control de fechas inicio y fin
            select date_part('year',op.fecha), to_char(op.fecha,'DD/MM/YYYY')::varchar as fecha, op.num_tramite
            into v_anio_op, v_fecha_op, v_num_tramite
            from tes.tobligacion_pago op
            join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
            where pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_op) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_op)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % TIENE COMO FECHA %', v_num_tramite,v_fecha_op;
            END IF;
            */
            --control de fechas inicio y fin que esten en el rango del la gestion del tramite
            select date_part('year',op.fecha), to_char(op.fecha,'DD/MM/YYYY')::varchar as fecha, op.num_tramite, ges.gestion
            into v_anio_op, v_fecha_op, v_num_tramite, v_anio_ges
            from tes.tobligacion_pago op
            join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
            join param.tgestion ges on ges.id_gestion = op.id_gestion
            where pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_ges) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_ges)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % gestión %', v_num_tramite, v_anio_ges;
            END IF;


            IF v_parametros.fecha_costo_ini is Null THEN
            	raise EXCEPTION 'Debe completar la fecha inicio';
            END IF;

            IF v_parametros.fecha_costo_fin is Null THEN
            	raise EXCEPTION 'Debe completar la fecha fin';
            END IF;

			/*--validador de gestion
			v_anio_gestion = ( select date_part('year',now()))::INTEGER;

			IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_gestion) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_gestion)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTION ACTUAL';
            END IF;
			*/


             --si es un pago variable, controla que el total del plan de pago no sea mayor a lo comprometido
                       select

                            op.num_tramite,
                            op.id_proceso_wf,
                            op.id_estado_wf,
                            op.estado,
                            op.id_depto,
                            op.pago_variable,
                            op.id_funcionario_gerente, --04/12/2019 Alan
                            op.id_funcionario,
                            op.tipo_obligacion

                          into v_registros
                           from tes.tobligacion_pago op
                           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

                              --recuperamos el id_funcionario_gerente actual de la obligacion de pago
                              ---25-08-2021 (may) modificacion asolicitud de Marcelo Vidaurre para funcionario GERENTE,
            				  /* sera segun una matriz y conceptos de gastos para un funcionario aprobador, funcion despues de insertar*/
                              --15-10-2021 (may) pra procesos de adquisiciones tambien se incluye que tenga aprobador,hasta distinto aprobador que del flujo adquisiciones
			 				--SOLO PARA tramites recurrentes registro uno por uno el detalle
                          IF (v_registros.tipo_obligacion in ('pago_directo', 'pago_unico','pago_especial', 'pga', 'adquisiciones' ) ) THEN

                            	 v_resp = tes.ft_solicitud_obligacion_pago(v_parametros.id_obligacion_pago, p_id_usuario);

                          ELSE

                                 SELECT
                                 pxp.aggarray(id_funcionario)
                                 into va_id_funcionario_gerente
                                 FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_registros.id_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

                                 if v_registros.id_funcionario_gerente != va_id_funcionario_gerente[1] then
                                      update tes.tobligacion_pago
                                      set id_funcionario_gerente =  va_id_funcionario_gerente[1]
                                      where id_obligacion_pago = v_parametros.id_obligacion_pago;
                                 end if;

                          END IF;

                        select
                            pp.monto,
                            pp.estado,
                            pp.tipo,
                            pp.id_plan_pago_fk,
                            pp.porc_monto_retgar,
                            pp.descuento_anticipo,
                            pp.monto_ejecutar_total_mo,
                            pp.monto_anticipo
                           into
                             v_registros_pp
                           from tes.tplan_pago pp
                           where pp.estado_reg='activo'
                           and pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

                           SELECT pc.codigo_tipo_relacion
                           INTO v_codigo_tipo_relacion
                           FROM param.tplantilla p
                           inner join conta.tplantilla_calculo pc on pc.id_plantilla = p.id_plantilla
                           WHERE p.id_plantilla = v_parametros.id_plantilla;

                            --SELECT sum(pp.monto)
                            --05-03-2021 (may)modificacion ya no del monto establecido debe ser del monto a ejecutar...por el 13% ya no hay
                            --SELECT sum(pp.monto_establecido)
                            SELECT sum(pp.monto_ejecutar_total_mo)
                            INTO v_sum_monto_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.estado != 'anulado' and pp.estado != 'pago_exterior' and pp.estado != 'pagado' and pp.estado != 'pendiente'
                            and pp.tipo not in ('especial', 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant', 'anticipo', 'ant_parcial', 'ant_rendicion', 'ant_aplicado', 'rendicion','ret_rendicion' )
                            and pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            SELECT sum(pe.monto)
                            INTO v_sum_monto_pe
                            FROM pre.tpartida_ejecucion pe
                            join tes.tobligacion_pago opa on opa.num_tramite = pe.nro_tramite
                            WHERE pe.tipo_movimiento = 'comprometido'
                            and opa.id_obligacion_pago = v_parametros.id_obligacion_pago;

                      IF v_registros.pago_variable='si' or v_registros.pago_variable='no' THEN

                       /*--
                        05-03-2021 (may)  se modifica desde la fecha que se debe controlar que el monto comprometido no debe sobre pasar al MONTO A EJECUTAR
                        				antes se realizaba con campo Monto sin iva
                        --*/
                      	--para los que se descuentan el IVA 13%
						/*IF (v_codigo_tipo_relacion in ('IVA-CF','IVA-CF-PA','RC-IVA')) THEN

                            v_porcentaje13_monto = COALESCE(v_parametros.monto * 0.13, 0);
          					v_monto_establecido  = COALESCE(v_parametros.monto, 0) - COALESCE(v_porcentaje13_monto,0);

                            v_sum_total_pp = COALESCE(v_sum_monto_pp,0) + COALESCE(v_monto_establecido, 0);

                           --raise exception 'llegaaaa %>= %', v_sum_total_pp,v_sum_monto_pe ;
                            IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;

						ELSE*/

                            v_sum_total_pp = COALESCE(v_sum_monto_pp, 0) + COALESCE(v_parametros.monto, 0);

                            /*--raise exception 'llegaaaa %>= %', v_sum_total_pp,v_sum_monto_pe ;
                            IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;*/

                            --(may)10-12-2021 modificacion control no controle para los de tipo adq que son los que vienen de GM
                           IF (v_registros.tipo_obligacion != 'gestion_mat') THEN
                              IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                                raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                              END IF;
                           END IF;

                      --END IF;
                          /*  v_sum_total_pp = COALESCE(v_parametros.monto,0);

                            IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;
							*/


                      END IF;
                 ----

        	select tipo_obligacion into v_tipo_obligacion
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos de devengado a una obligacion de RRHH';
            end if;

            v_resp = tes.f_inserta_plan_pago_dev(p_administrador, p_id_usuario,hstore(v_parametros));

            --Devuelve la respuesta
            return v_resp;

            update tes.tplan_pago set
                fecha_costo_ini = v_parametros.fecha_costo_ini,
                fecha_costo_fin = v_parametros.fecha_costo_fin

			where id_int_comprobante = v_parametros.id_int_comprobante;


		end;

   	/*********************************
 	#TRANSACCION:  'TES_PLAPAPA_INS'
 	#DESCRIPCION:	Insercion de cuotas de CUOTAS DE SEGUNDO NIVEL,  PAGO o APLICAION DE ANTICIPO , en el plan de pago
 	#AUTOR:		RAC KPLIAN
 	#FECHA:	7062013   15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPAPA_INS')then

        begin
        	--select tipo_obligacion into v_tipo_obligacion
        	  select tipo_obligacion, id_funcionario_gerente, id_funcionario into v_tipo_obligacion, v_fun_gerente, v_funcionario
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos a una obligacion de RRHH';
            end if;
            --recuperamos el id_funcionario_gerente actual de la obligacion de pago
             SELECT
             pxp.aggarray(id_funcionario)
             into
                 va_id_funcionario_gerente
             FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

             if v_fun_gerente != va_id_funcionario_gerente[1] then
                  update tes.tobligacion_pago
                  set id_funcionario_gerente =  va_id_funcionario_gerente[1]
                  where id_obligacion_pago = v_parametros.id_obligacion_pago;
             end if;


            v_resp = tes.f_inserta_plan_pago_pago(p_administrador, p_id_usuario,hstore(v_parametros));
            --Devuelve la respuesta
            return v_resp;

		end;

   /*********************************
 	#TRANSACCION:  'TES_PPANTPAR_INS'
 	#DESCRIPCION:	Inserta cuotas del tipo anticipo parcial , o
                    anticipo total  o dev_garantia (todas no  tienen prorrateo por que no ejecutan presupuesto)
 	#AUTOR:		RAC KPLIAN
 	#FECHA: 17/07/2014   15:43:23
	***********************************/

	elsif(p_transaccion='TES_PPANTPAR_INS')then

        begin
        	--select tipo_obligacion into v_tipo_obligacion
        	  select tipo_obligacion, id_funcionario_gerente, id_funcionario into v_tipo_obligacion, v_fun_gerente, v_funcionario
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos a una obligacion de RRHH';
            end if;

              --recuperamos el id_funcionario_gerente actual de la obligacion de pago
             SELECT
             pxp.aggarray(id_funcionario)
             into
                 va_id_funcionario_gerente
             FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

             if v_fun_gerente != va_id_funcionario_gerente[1] then
                  update tes.tobligacion_pago
                  set id_funcionario_gerente =  va_id_funcionario_gerente[1]
                  where id_obligacion_pago = v_parametros.id_obligacion_pago;
             end if;

            v_resp = tes.f_inserta_plan_pago_anticipo(p_administrador, p_id_usuario,hstore(v_parametros));
            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'TES_PLAPA_MOD'
 	#DESCRIPCION:	Modificacion de cuotas de devegando y pago
 	#AUTOR:		admin
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPA_MOD')then

		begin
--raise exception 'llega ini';
           --determinar exixtencia de parametros dinamicos para registro
           -- (Interface de obligacions de adquisocines o interface de obligaciones tesoeria)
           -- la adquisiciones tiene menos parametros presentes

           -- raise exception '%, %, %,%,%', COALESCE(v_parametros.id_cuenta_bancaria,0), COALESCE(v_parametros.id_cuenta_bancaria_mov,0),  COALESCE(v_parametros.forma_pago,''),  COALESCE(v_parametros.nro_cheque,0),  COALESCE(v_parametros.nro_cuenta_bancaria,'');

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta_bancaria') THEN
               v_id_cuenta_bancaria =  v_parametros.id_cuenta_bancaria;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'id_depto_lb') THEN
               v_id_depto_lb =  v_parametros.id_depto_lb;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta_bancaria_mov') THEN
               v_id_cuenta_bancaria_mov =  v_parametros.id_cuenta_bancaria_mov;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'forma_pago') THEN
               v_forma_pago =  v_parametros.forma_pago;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'nro_cheque') THEN
               v_nro_cheque =  v_parametros.nro_cheque;
             END IF;

  			--modificacion para v_nro_cuenta_bancaria
            /* IF  pxp.f_existe_parametro(p_tabla,'nro_cuenta_bancaria') THEN
                v_nro_cuenta_bancaria =  v_parametros.nro_cuenta_bancaria;
             END IF;
			*/
            	/*	select pcb.id_proveedor_cta_bancaria
                    into v_id_proveedor_cta_bancaria
                    from param.tproveedor_cta_bancaria pcb
                    where pcb.nro_cuenta = v_parametros.id_proveedor_cta_bancaria::varchar;
            */

            		  select ins.nombre||'-'|| pcb.nro_cuenta
                      into v_cuenta_bancaria_benef
                      from param.tproveedor_cta_bancaria pcb
                      left join param.tinstitucion ins on ins.id_institucion=pcb.id_banco_beneficiario
                      where pcb.id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria;

             		v_nro_cuenta_bancaria = v_cuenta_bancaria_benef::varchar;

             --

             IF  pxp.f_existe_parametro(p_tabla,'porc_monto_excento_var') THEN
                v_porc_monto_excento_var =  v_parametros.porc_monto_excento_var;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'monto_excento') THEN
                v_monto_excento =  v_parametros.monto_excento;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'monto_anticipo') THEN
                v_monto_anticipo =  v_parametros.monto_anticipo;
             ELSE
               v_monto_anticipo = 0;
             END IF;



            --validamos que el monto a pagar sea mayor que cero
           IF  v_parametros.monto = 0 THEN
              raise exception 'El monto a pagar no puede ser 0';
           END IF;


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
            op.id_funcionario_gerente,
            op.id_funcionario,
            op.tipo_obligacion,
            op.fecha
          into v_registros
           from tes.tobligacion_pago op
           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

			--25-08-2021 (may) modificacion a solicitud de Marcelo Vidaurre para funcionario GERENTE segun la Matriz
             /*--recuperamos el id_funcionario_gerente actual de la obligacion de pago
           SELECT
           pxp.aggarray(id_funcionario)
           into
               va_id_funcionario_gerente
           FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_registros.id_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

           if v_registros.id_funcionario_gerente != va_id_funcionario_gerente[1] then
                update tes.tobligacion_pago
                set id_funcionario_gerente =  va_id_funcionario_gerente[1]
                where id_obligacion_pago = v_parametros.id_obligacion_pago;
           end if;
           */


          select
            pp.monto,
            pp.estado,
            pp.tipo,
            pp.id_plan_pago_fk,
            pp.porc_monto_retgar,
            pp.descuento_anticipo,
            pp.monto_ejecutar_total_mo,
            pp.monto_anticipo
           into
             v_registros_pp
           from tes.tplan_pago pp
           where pp.estado_reg='activo'
           and  pp.id_plan_pago= v_parametros.id_plan_pago ;


           IF v_codigo_estado = 'borrador' or v_codigo_estado = 'pagado'  or v_codigo_estado = 'pendiente' or v_codigo_estado = 'devengado' or v_codigo_estado = 'anulado' THEN
             raise exception 'Solo puede modificar pagos en estado borrador';
           END IF;

           ---25-08-2021 (may) modificacion asolicitud de Marcelo Vidaurre para funcionario GERENTE,
            /* sera segun una matriz y conceptos de gastos para un funcionario aprobador, funcion despues de insertar*/
            --SOLO PARA procesos recurrentes, pagos unicos, PGA, sin imputacion
           IF (v_registros_pp.estado = 'borrador') THEN

                --SOLO PARA tramites recurrentes registro uno por uno el detalle
                --15-10-2021 (may) pra procesos de adquisiciones tambien se incluye que tenga aprobador,hasta distinto aprobador que del flujo adquisiciones

                IF (v_registros.tipo_obligacion in ('pago_directo', 'pago_unico','pago_especial', 'pga', 'adquisiciones' ) ) THEN

                   v_resp = tes.ft_solicitud_obligacion_pago(v_parametros.id_obligacion_pago, p_id_usuario);

                ELSE

                	--recuperamos el id_funcionario_gerente actual de la obligacion de pago
                   SELECT
                   pxp.aggarray(id_funcionario)
                   into
                       va_id_funcionario_gerente
                   FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_registros.id_funcionario , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

                   if v_registros.id_funcionario_gerente != va_id_funcionario_gerente[1] then
                        update tes.tobligacion_pago
                        set id_funcionario_gerente =  va_id_funcionario_gerente[1]
                        where id_obligacion_pago = v_parametros.id_obligacion_pago;
                   end if;

                END IF;

            END IF;


           --valida que los valores no sean negativos
           IF  v_parametros.monto <0 or v_parametros.monto_no_pagado <0 or v_parametros.otros_descuentos  <0 THEN
               raise exception 'No se admiten cifras negativas';
           END IF;


           -------------------------------------
           -- Si es una cuota de devengado
           -- Segun el tipo de cuota
           ------------------------------------

           -----------------------------------------------------------------------------------------------
           -- EDICION DE CUOTAS QUE TIENEN DEVENGADO   ('devengado_pagado','devengado','devengado_pagado_1c')
           --------------------------------------------------------------------------------------------------

           IF v_registros_pp.tipo in ('devengado_pagado','devengado','devengado_pagado_1c','especial') THEN


                 IF v_registros_pp.tipo in  ('especial') THEN

                       IF v_registros.pago_variable = 'no' THEN
                           v_monto_total = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'especial_total');
                           v_especial = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'especial');
                           IF v_especial + v_registros_pp.monto <  v_parametros.monto  THEN
                              raise exception 'No puede exceder el total determinado: %', v_monto_total;
                           END IF;
                       END IF;

                        v_monto_ejecutar_total_mo   = COALESCE(v_parametros.monto,0);
                        v_liquido_pagable  = COALESCE(v_parametros.monto,0);

                         --raise exception '% , %',v_monto_total,v_parametros.monto
                 ELSE

                     -- si no es un proceso variable, verifica que el registro no sobrepase el total a pagar
                     IF v_registros.pago_variable='no' THEN
                        v_monto_total = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'registrado');
                        IF (v_monto_total + v_registros_pp.monto)  <  v_parametros.monto  THEN
                          raise exception 'No puede exceder el total a pagar en obligaciones no variables.';
                        END IF;

                        --   si es  un pago no variable  (si es una cuota de devengao_pagado, devegando_pagado_1c, pagado)
                        --  validar que no se haga el ultimo pago sin  terminar de descontar el anticipo,

                        IF   v_registros_pp.tipo in('devengado_pagado','devengado_pagado_1c')  THEN
                            -- saldo_x_pagar = determinar cuanto falta por pagar (sin considerar el devengado)
                            v_saldo_x_pagar = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago,'total_registrado_pagado');

                            -- saldo_x_descontar = determinar cuanto falta por descontar del anticipo
                            v_saldo_x_descontar = v_monto_ant_parcial_descontado;

                             -- saldo_x_descontar - descuento_anticipo >  sando_x_pagar
                            IF (v_saldo_x_descontar + v_registros_pp.descuento_anticipo -  COALESCE(v_parametros.descuento_anticipo,0))  > (v_saldo_x_pagar + v_registros_pp.monto - COALESCE(v_parametros.monto,0)) THEN
                               raise exception 'El saldo a pagar no es sufuciente para recuperar el anticipo (%)',v_saldo_x_descontar;
                            END IF;

                        END IF;

                     END IF;

                     -- si el descuento anticipo es mayor a cero verificar que nose sobrepase el total anticipado
                     v_monto_ant_parcial_descontado = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'ant_parcial_descontado' );
                     IF v_monto_ant_parcial_descontado + v_registros_pp.descuento_anticipo <  v_parametros.descuento_anticipo  THEN

                          raise exception 'El descuento por anticipo no puede exceder el faltante por descontar que es  %',v_monto_ant_parcial_descontado;

                     END IF;

                     -- calcula el liquido pagable y el monto a ejecutar presupeustaria mente
                     v_liquido_pagable = COALESCE(v_parametros.monto,0) - COALESCE(v_parametros.monto_no_pagado,0) - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0) - COALESCE(v_parametros.descuento_ley,0) - COALESCE(v_parametros.descuento_anticipo,0)- COALESCE(v_parametros.descuento_inter_serv,0);
                     v_monto_ejecutar_total_mo  = COALESCE(v_parametros.monto,0) -  COALESCE(v_parametros.monto_no_pagado,0) -  COALESCE(v_parametros.monto_anticipo,0);
                     v_porc_monto_retgar = COALESCE(v_parametros.monto_retgar_mo,0)/COALESCE(v_parametros.monto,0);

					-- 23-10-2019(may) modificacion aviso de los montos liquido pagable y a ejecutar
                     IF v_liquido_pagable  < 0 THEN
                          raise exception 'El Líquido Pagable es de % y no puede ser menor a cero', v_liquido_pagable;
                     END IF;
                     IF v_monto_ejecutar_total_mo < 0 THEN
                          raise exception 'El Monto a Ejecutar es de % y no puede ser menor a cero', v_monto_ejecutar_total_mo;
                     END IF;

                     --revision de anticipo
                     --si es un proceso variable, verifica que el registro no sobrepase el total a pagar
                     IF v_registros.pago_variable = 'no' THEN
                                -- Validamos anticipos mistos
                                -- total a ejecutar + total_anticipo (mixto) <= total a pagar (presupuestado) +(total a pagar siguiente gestion)
                                v_check_ant_mixto = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'registrado_monto_ejecutar');

                                --suma los montos a ejecutar y anticipar antes de la edicion
                                IF v_check_ant_mixto +  v_registros_pp.monto_ejecutar_total_mo + v_registros_pp.monto_anticipo - v_monto_ejecutar_total_mo - v_monto_anticipo  < 0 THEN
                                     raise exception 'El monto del anticipo sobre pasa lo previsto para la siguiente gestion, ajuste el monto a ejecutarpara la siguiente gestión';
                                END IF;
                     END IF;


                      --(may)si es un pago variable, controla que el total del plan de pago no sea mayor a lo comprometido
                      		SELECT sum(pp.monto_ejecutar_total_mo)
                            --SELECT sum(pp.monto_establecido)
                            INTO v_sum_monto_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.estado != 'anulado' and pp.estado != 'pago_exterior' and pp.estado != 'pagado' and pp.estado != 'pendiente'
                            --and pp.tipo not in ( 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant')
                            and pp.tipo not in ('especial', 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant', 'anticipo', 'ant_parcial', 'ant_rendicion', 'ant_aplicado', 'rendicion','ret_rendicion' )
                            and pp.id_plantilla not in (23) -- id23 = proforma recibo de alquiler
                            and pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            --SELECT pp.monto_ejecutar_total_mo
                            --05-03-2021 (may)modificacion ya no del monto establecido debe ser del monto a ejecutar...por el 13% ya no hay
                            --SELECT pp.monto_establecido
                            SELECT pp.monto_ejecutar_total_mo
                            INTO v_sum_monto_solo_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.id_plan_pago= v_parametros.id_plan_pago;

                            SELECT sum(pe.monto)
                            INTO v_sum_monto_pe
                            FROM pre.tpartida_ejecucion pe
                            join tes.tobligacion_pago opa on opa.num_tramite = pe.nro_tramite
                            WHERE pe.tipo_movimiento = 'comprometido'
                            and opa.id_obligacion_pago = v_parametros.id_obligacion_pago;




                      IF v_registros.pago_variable='si' or v_registros.pago_variable='no' THEN

                        SELECT pc.codigo_tipo_relacion
                        INTO v_codigo_tipo_relacion
                        FROM param.tplantilla p
                        inner join conta.tplantilla_calculo pc on pc.id_plantilla = p.id_plantilla
                        WHERE  pc.codigo_tipo_relacion in ('IVA-CF','IVA-CF-PA', 'RC-IVA')
                        and p.id_plantilla = v_parametros.id_plantilla;


                      	/*--
                        05-03-2021 (may)  se modifica desde la fecha que se debe controlar que el monto comprometido no debe sobre pasar al MONTO A EJECUTAR
                        				antes se realizaba con campo Monto sin iva
                        --*/
                      	--para los que se descuentan el IVA 13%
						/*IF (v_codigo_tipo_relacion in ('IVA-CF','IVA-CF-PA','RC-IVA')) THEN

                            v_porcentaje13_monto = COALESCE(v_parametros.monto * 0.13, 0);
          					v_monto_establecido  = COALESCE(v_parametros.monto, 0) - v_porcentaje13_monto;

                            --v_sum_total_pp = (v_sum_monto_pp - v_sum_monto_solo_pp) + v_parametros.monto;
                            v_sum_total_pp = (v_sum_monto_pp - v_sum_monto_solo_pp) + v_monto_establecido;
                         --raise exception '% = % - % + %',v_sum_total_pp,  v_sum_monto_pp, v_sum_monto_solo_pp, v_parametros.monto;

                           IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                           END IF;

                       ELSE*/

                      	 v_monto_establecido  = COALESCE(v_parametros.monto);
                         v_sum_total_pp = (COALESCE(v_sum_monto_pp,0) - COALESCE(v_sum_monto_solo_pp, 0)) + COALESCE(v_parametros.monto, 0);
                         --raise exception '% = % - % + %',v_sum_total_pp,  v_sum_monto_pp, v_sum_monto_solo_pp, v_parametros.monto;



						 --23-01-2022 (may) desde la fecha los PGA con conceptos de gastos de flujo no ingresan por la condicion porque ya no pasan por el estado de presupuestos
                         SELECT par.sw_movimiento
                         INTO v_sw_movimiento_partida
                         FROM  tes.tobligacion_det od
                         inner JOIN pre.tpartida par ON par.id_partida = od.id_partida
                         WHERE od.id_obligacion_pago =  v_parametros.id_obligacion_pago
                         LIMIT 1 ;

                         IF ((v_registros.tipo_obligacion != 'gestion_mat')) THEN

                         	IF (v_registros.tipo_obligacion = 'pga' and v_sw_movimiento_partida != 'flujo') THEN

                            		IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                                			raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            		END IF;

                            END IF;

                         END IF;


                       --END IF;


                     END IF;
                   --
                    select tipo_obligacion
                    into v_tipo_obligacion
                    from tes.tobligacion_pago
                    where id_obligacion_pago = v_parametros.id_obligacion_pago;

                 /*  IF (v_tipo_obligacion != 'sp' and v_tipo_obligacion != 'spd' and v_tipo_obligacion != 'spi' and v_tipo_obligacion != 'pago_especial_spi' )THEN

                    --valida si forma_pago es igual a forma_pago cuenta bancaria
                   	  IF v_id_cuenta_bancaria is Not NULL THEN
                        select cb.forma_pago, cb.nro_cuenta
                        into v_forma_pago_cb, v_nro_cuenta
                        from tes.tcuenta_bancaria cb
                        where cb.id_cuenta_bancaria = v_parametros.id_cuenta_bancaria;

                        IF (upper(v_forma_pago_cb) != upper(v_parametros.forma_pago)) then
                              raise exception 'Modificar la Forma de Pago, este pertenece como  %  para la Cuenta Bancaria %', UPPER(v_forma_pago_cb), v_nro_cuenta;
                        END IF;
                      END IF;
                   END IF; */
              --

               END IF;


           -------------------------------------------------------------
           -- EDICION DE CUOTAS DEL ANTICIPO   (ant_parcial,anticipo)
           --------------------------------------------------------------

           ELSIF v_registros_pp.tipo in  ('ant_parcial', 'anticipo', 'dev_garantia','dev_garantia_con','dev_garantia_con_ant') THEN



                   --  si es un proceso variable, verifica que el registro no sobrepase el total a pagar
                  IF v_registros_pp.tipo in  ('ant_parcial') THEN
                         v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'ant_parcial');
                         v_porc_ant = pxp.f_get_variable_global('politica_porcentaje_anticipo')::numeric;


                         IF (v_monto_total + v_registros_pp.monto) <  COALESCE(v_parametros.monto,0) AND v_registros.pago_variable = 'no'  THEN
                            raise exception 'No puede exceder el total a pagar segun politica de anticipos % porc', v_porc_ant*100;
                         END IF;

                  ELSIF v_registros_pp.tipo in  ('anticipo') THEN

                      -- validaciones segun el tipo de anticipo
                      IF v_registros.pago_variable='no' THEN
                            v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'registrado');

                            IF (v_monto_total + v_registros_pp.monto)  <  v_parametros.monto  THEN
                               raise exception 'No puede exceder el total a pagar en obligaciones no variables: %',v_monto_total;
                            END IF;

                          ----------------------------
                          --  si es  un pago no variable  (si es una cuota de devengao_pagado, devegando_pagado_1c, pagado)
                          --  validar que no se haga el ultimo pago sin  terminar de descontar el anticipo,
                          ------------------------------------------------

                          -- saldo_x_pagar = determinar cuanto falta por pagar (sin considerar el devengado)

                          v_saldo_x_pagar = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago,'total_registrado_pagado');

                          -- saldo_x_descontar = determinar cuanto falta por descontar del anticipo parcial
                          v_saldo_x_descontar = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago,'ant_parcial_descontado');

                          -- saldo_x_descontar - descuento_anticipo >  sando_x_pagar
                          IF (v_saldo_x_descontar + v_registros_pp.descuento_anticipo)  > (v_saldo_x_pagar + v_registros_pp.monto - COALESCE(v_parametros.monto,0)) THEN
                               raise exception 'El saldo a pagar no es sufuciente para recuperar el anticipo (%)',v_saldo_x_descontar;
                          END IF;

                      END IF;


                  ELSIF v_registros_pp.tipo in  ('dev_garantia') THEN

                        IF v_registros.pago_variable='no' THEN
                           v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'dev_garantia');
                           IF v_monto_total + v_registros_pp.monto <  v_parametros.monto  THEN
                              raise exception 'No puede exceder el total de retencion de garantia devuelto: %', v_monto_total;
                           END IF;
                        END IF;
                         --raise exception '% , %',v_monto_total,v_parametros.monto;




                  END IF;

                   v_liquido_pagable = COALESCE(v_parametros.monto,0) - COALESCE(v_parametros.monto_no_pagado,0) - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0) - COALESCE(v_parametros.descuento_ley,0) - COALESCE(v_parametros.descuento_inter_serv,0);

                  --v_liquido_pagable = COALESCE(v_parametros.monto,0) -  COALESCE(v_parametros.descuento_anticipo,0); --en anticipo el monto es el liquido pagable
                  v_monto_ejecutar_total_mo  = 0;  -- el monto a ejecutar es cero los anticipo parciales no ejecutan presupeusto

                  IF   v_liquido_pagable  < 0  or v_monto_ejecutar_total_mo < 0  THEN
                      raise exception ' Ni  el monto a ejecutar   ni el liquido pagable  puede ser menor a cero';
                  END IF;

            -------------------------------------------------------------
            -- EDICION DE CUOTAS DEL ANTICIPO APLCIADO  (ant_aplicado)
            --------------------------------------------------------------

            ELSIF v_registros_pp.tipo = 'ant_aplicado' THEN

                   IF  v_registros.pago_variable = 'no' THEN

                      v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'ant_aplicado_descontado', v_registros_pp.id_plan_pago_fk);
                      IF (v_monto_total + v_registros_pp.monto)  <  v_parametros.monto  THEN
                         raise exception 'No puede exceder el total anticipado';
                      END IF;

                   ELSE
                     v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'ant_aplicado_descontado_op_variable', v_parametros.id_plan_pago_fk);

                   END IF;



                   --  calcula el liquido pagable y el monto a ejecutar presupeustaria mente
                   --  en cuota de pago el monoto no pagado no se considera

                   v_liquido_pagable = COALESCE(v_parametros.monto,0)  - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0)  - COALESCE(v_parametros.descuento_ley,0)- COALESCE(v_parametros.descuento_anticipo,0)- COALESCE(v_parametros.descuento_inter_serv,0);
                   v_monto_ejecutar_total_mo  = COALESCE(v_parametros.monto,0);  -- TODO ver si es necesario el monto no pagado
                   v_porc_monto_retgar= COALESCE(v_registros_pp.porc_monto_retgar,0);

                   IF   v_liquido_pagable  < 0  or v_monto_ejecutar_total_mo < 0  THEN
                        raise exception ' Ni el  monto a ejecutar   ni el liquido pagable  puede ser menor a cero';
                   END IF;

                  --modificamos el total pagado en la cuota padre

                  update tes.tplan_pago pp set
                  total_pagado = total_pagado - v_registros_pp.monto + COALESCE(v_parametros.monto,0)
                  where id_plan_pago=v_registros_pp.id_plan_pago_fk;

            -------------------------------------
            -- EDICION DE CUOTAS DEL TIPO PAGADO
            -------------------------------------


            ELSIF v_registros_pp.tipo IN ('pagado','pagado_rrhh') THEN

                    --verifica el el registro que falta por pagar
                    v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'registrado_pagado', v_registros_pp.id_plan_pago_fk);
                    IF (v_monto_total + v_registros_pp.monto)  <  v_parametros.monto  THEN
                      raise exception 'No puede exceder el total a pagar en obligaciones no variables';
                     END IF;

                    --valida que la retencion de anticipo no sobre pase el total anticipado
                    v_monto_ant_parcial_descontado = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'ant_parcial_descontado' );
                    -- si el descuento anticipo es mayor a cero verificar que nose sobrepase el total anticipado
                    IF v_monto_ant_parcial_descontado + v_registros_pp.descuento_anticipo <  v_parametros.descuento_anticipo  THEN
                        raise exception 'El decuento por anticipo no puede exceder el falta por descontar que es  %',v_monto_ant_parcial_descontado+descuento_anticipo;
                     END IF;

                   ------------------------------------------------------------
                   --   si es  un pago no variable  (si es una cuota de devengao_pagado, devegando_pagado_1c, pagado)
                   --  validar que no se haga el ultimo pago sin  terminar de descontar el anticipo,
                   --------------------------------------------------------
                   IF v_registros.pago_variable='no' THEN
                         -- saldo_x_pagar = determinar cuanto falta por pagar (sin considerar el devengado)
                         v_saldo_x_pagar = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago,'total_registrado_pagado');

                         -- saldo_x_descontar = determinar cuanto falta por descontar del anticipo
                         v_saldo_x_descontar = v_monto_ant_parcial_descontado;

                         -- saldo_x_descontar - descuento_anticipo >  sando_x_pagar
                         IF (v_saldo_x_descontar + v_registros_pp.descuento_anticipo -  COALESCE(v_parametros.descuento_anticipo,0))  > (v_saldo_x_pagar + v_registros_pp.monto - COALESCE(v_parametros.monto,0)) THEN
                               raise exception 'El saldo a pagar no es sufuciente para recuperar el anticipo (%)',v_saldo_x_descontar;
                         END IF;
                    END IF;


                    -- calcula el liquido pagable y el monto a ejecutar presupeustaria mente
                    --  en cuota de pago el monoto no pagado no se considera

                   v_liquido_pagable = COALESCE(v_parametros.monto,0)  - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0)  - COALESCE(v_parametros.descuento_ley,0)- COALESCE(v_parametros.descuento_anticipo,0)- COALESCE(v_parametros.descuento_inter_serv,0);
                   v_monto_ejecutar_total_mo  = COALESCE(v_parametros.monto,0);  -- TODO ver si es necesario el monto no pagado
                   v_porc_monto_retgar= COALESCE(v_registros_pp.porc_monto_retgar,0);

                   IF   v_liquido_pagable  < 0  or v_monto_ejecutar_total_mo < 0  THEN
                        raise exception ' Ni el  monto a ejecutar   ni el liquido pagable  puede ser menor a cero';
                   END IF;

                  --modificamos el total pagado en la cuota padre

                  update tes.tplan_pago pp set
                  total_pagado = total_pagado - v_registros_pp.monto + COALESCE(v_parametros.monto,0)
                  where id_plan_pago=v_registros_pp.id_plan_pago_fk;

            ELSE

               raise exception 'Tipo no reconocido %',v_registros_pp.tipo;

            END IF;

           --RAC 11/02/2014
           --calculo porcentaje monto excento

           Select
           p.sw_monto_excento
           into
           v_sw_me_plantilla
           from param.tplantilla p
           where p.id_plantilla =  v_parametros.id_plantilla;

           IF v_sw_me_plantilla = 'si' and  v_monto_excento < 0 THEN
              raise exception  'Este documento necesita especificar un monto excento no negativo';
           END IF;

           IF COALESCE(v_monto_excento,0) > COALESCE(v_monto_ejecutar_total_mo,0) and v_registros_pp.tipo not in ('ant_parcial','anticipo','dev_garantia','especial', 'dev_garantia_con', 'dev_garantia_con_ant') THEN
             raise exception 'El monto excento (%) debe ser menor que el total a ejecutar(%)',v_monto_excento, v_monto_ejecutar_total_mo  ;
           END IF;

           --CALUCLO DEL PORCENTAJE DE MONTO EXCENTO
           IF  COALESCE(v_monto_excento,0) > 0 THEN
                v_porc_monto_excento_var  = v_monto_excento / v_parametros.monto;
           ELSE
                v_porc_monto_excento_var = 0;
           END IF;

--raise exception 'llegaaa %, %',v_parametros.id_proveedor_cta_bancaria,v_nro_cuenta_bancaria ;

--RAISE EXCEPTION 'v_parametros.es_ultima_cuota : %', v_parametros.es_ultima_cuota;

			--Sentencia de la modificacion
			update tes.tplan_pago set
			monto_ejecutar_total_mo = v_monto_ejecutar_total_mo,
			obs_descuentos_anticipo = v_parametros.obs_descuentos_anticipo,
			id_plantilla = v_parametros.id_plantilla,
			descuento_anticipo = COALESCE(v_parametros.descuento_anticipo,0),
            descuento_inter_serv = COALESCE(v_parametros.descuento_inter_serv,0),
            obs_descuento_inter_serv = v_parametros.obs_descuento_inter_serv,
			otros_descuentos = COALESCE( v_parametros.otros_descuentos,0),
            tipo= v_parametros.tipo,
			obs_monto_no_pagado = v_parametros.obs_monto_no_pagado,
			obs_otros_descuentos = v_parametros.obs_otros_descuentos,
			monto = v_parametros.monto,
			nombre_pago = v_parametros.nombre_pago,
			id_cuenta_bancaria = v_id_cuenta_bancaria,
            id_depto_lb = v_id_depto_lb,
			forma_pago = v_forma_pago,
			monto_no_pagado = v_parametros.monto_no_pagado,
            liquido_pagable=v_liquido_pagable,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
            --tipo_cambio= v_parametros.tipo_cambio,
            monto_retgar_mo= v_parametros.monto_retgar_mo,
            descuento_ley=v_parametros.descuento_ley,
            obs_descuentos_ley=v_parametros.obs_descuentos_ley,
            porc_descuento_ley=v_parametros.porc_descuento_ley,
            nro_cheque =  COALESCE(v_nro_cheque,0),
            fecha_tentativa = v_parametros.fecha_tentativa,
            nro_cuenta_bancaria = v_nro_cuenta_bancaria,
            id_cuenta_bancaria_mov = v_id_cuenta_bancaria_mov,
            porc_monto_excento_var =  v_porc_monto_excento_var,
            monto_excento = COALESCE(v_monto_excento,0),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            porc_monto_retgar = v_porc_monto_retgar,
            monto_ajuste_ag = v_parametros.monto_ajuste_ag,
            monto_anticipo = v_monto_anticipo,
            fecha_costo_ini = v_parametros.fecha_costo_ini,
            fecha_costo_fin = v_parametros.fecha_costo_fin,
            fecha_conclusion_pago = v_parametros.fecha_conclusion_pago,
            /*es_ultima_cuota = v_parametros.es_ultima_cuota*/
            monto_establecido =v_monto_establecido,
            id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria,
            id_multa = v_parametros.id_multa

            where id_plan_pago = v_parametros.id_plan_pago;

            /*--control de fechas inicio y fin
            select date_part('year',op.fecha), to_char(op.fecha,'DD/MM/YYYY')::varchar as fecha, op.num_tramite
            into v_anio_op, v_fecha_op, v_num_tramite
            from tes.tobligacion_pago op
            join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
            where pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_op) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_op)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % TIENE COMO FECHA %', v_num_tramite,v_fecha_op;
            END IF;
			*/


            --control de fechas inicio y fin que esten en el rango del la gestion del tramite
            select date_part('year',op.fecha), to_char(op.fecha,'DD/MM/YYYY')::varchar as fecha, op.num_tramite, ges.gestion
            into v_anio_op, v_fecha_op, v_num_tramite, v_anio_ges
            from tes.tobligacion_pago op
            join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
            join param.tgestion ges on ges.id_gestion = op.id_gestion
            where pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

            /*Aumentando condicion para los devengados de la siguiente Gestion (IRVA 03DIC2021)*/
            IF  pxp.f_existe_parametro(p_tabla,'tipo_interfaz') THEN
               if (v_parametros.tipo_interfaz != 'deven_SigGestion') then
               	IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_ges) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_ges)) THEN
                	raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % gestión %', v_num_tramite, v_anio_ges;
            	END IF;
               end if;
            ELSE
            	IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_ges) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_ges)) THEN
                	raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % gestión %', v_num_tramite, v_anio_ges;
            	END IF;
            END IF;



            --control fecha de conclusion del pago
            /*IF (v_parametros.fecha_conclusion_pago<now()) THEN
                raise exception 'LA FECHA CONCLUSION ES MENOR A LA FECHA %',to_char(now(), 'DD-MM-YYYY');
            END IF;*/

            -- chequea fechas de costos inicio y fin
            v_resp_doc =  tes.f_validar_periodo_costo(v_parametros.id_plan_pago);



             IF v_registros_pp.tipo not in ('ant_parcial','anticipo','dev_garantia','dev_garantia_con','dev_garantia_con_ant') THEN

                   ----------------------------------------------------------------------
                   -- Inserta prorrateo automatico  si no es algun tipo decuota sin prorrateo (sin presupeustos)
                   ----------------------------------------------------------------------

                   --si el monto del pago y el total de prorrateo no son iguales, reiniciamos el prorrateo

                    select
                      sum(pr.monto_ejecutar_mo)
                    into
                      v_total_prorrateo
                    from  tes.tprorrateo pr
                    inner join tes.tobligacion_det od on od.id_obligacion_det = pr.id_obligacion_det
                    where pr.id_plan_pago = v_parametros.id_plan_pago
                    and pr.estado_reg = 'activo' and od.estado_reg = 'activo';




                   IF v_total_prorrateo != v_monto_ejecutar_total_mo THEN

                        --elimina el prorrateo si es automatico
                         delete from tes.tprorrateo pro where pro.id_plan_pago = v_parametros.id_plan_pago;

                        IF not ( SELECT * FROM tes.f_prorrateo_plan_pago( v_parametros.id_plan_pago,
                                                                   v_parametros.id_obligacion_pago,
                                                                   v_registros.pago_variable,
                                                                   v_monto_ejecutar_total_mo,
                                                                   p_id_usuario,
                                                                   v_registros_pp.id_plan_pago_fk

                                                                   )) THEN


                            raise exception 'Error al prorratear';

                         END IF;
                    END IF;

            END IF;
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Plan Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_PLAPA_ELI'
 	#DESCRIPCION:	Eliminacion de registros de plan de pagos
 	#AUTOR:		admin
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PLAPA_ELI')then

		begin

          --obtiene datos de plan de pago
          select
            pp.estado,
            pp.nro_cuota,
            pp.tipo_pago ,
            pp.tipo,
            pp.id_proceso_wf,
            pp.id_obligacion_pago,
            op.id_depto,
            pp.id_estado_wf,
            pp.id_plan_pago_fk,
            pp.monto_ejecutar_total_mo,
            op.tipo_obligacion
          into v_registros
           from tes.tplan_pago pp
           inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
           where pp.id_plan_pago = v_parametros.id_plan_pago;

            if (v_registros.tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible eliminar pagos de una obligacion de RRHH';
            end if;

           IF  v_registros.estado != 'borrador' THEN

             raise exception 'No puede elimiar cuotas  que no esten en estado borrador';

           END IF;


           --si es una cuota de devengao_pago o devengado validamos que elimine
           --primero la ultima cuota

           -------------------------------------------------
           --  Eliminacion de cuentas de primer nivel
           ------------------------------------------------
           IF  v_registros.tipo in  ('devengado_pagado','devengado','devengado_pagado_1c','ant_parcial','anticipo','dev_garantia','especial','dev_garantia_con','dev_garantia_con_ant')   THEN
                     select
                      max(pp.nro_cuota)
                     into
                      v_nro_cuota
                     from tes.tplan_pago pp
                     where
                        pp.id_obligacion_pago = v_registros.id_obligacion_pago
                         and   pp.estado_reg = 'activo';

                     v_nro_cuota = floor(COALESCE(v_nro_cuota,0));

                     IF v_nro_cuota != v_registros.nro_cuota THEN

                       raise exception 'Elimine primero la ultima cuota';

                     END IF;

                     --recuperamos el id_tipo_proceso en el WF para el estado anulado
                     --ya que este es un estado especial que no tiene padres definidos


                     select
                      te.id_tipo_estado
                     into
                      v_id_tipo_estado
                     from wf.tproceso_wf pw
                     inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
                     inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'anulado'
                     where pw.id_proceso_wf = v_registros.id_proceso_wf;


                     IF  v_id_tipo_estado is NULL THEN

                         raise exception 'no existe el estado enulado en la cofiguacion de WF para este tipo de proceso';

                     END IF;
                     -- pasamos la cotizacion al siguiente estado

                     v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                                 NULL,
                                                                 v_registros.id_estado_wf,
                                                                 v_registros.id_proceso_wf,
                                                                 p_id_usuario,
                                                                 v_parametros._id_usuario_ai,
                                                                 v_parametros._nombre_usuario_ai,
                                                                 v_registros.id_depto,
                                                                 'Se elimina la cuota');


                     -- actualiza estado en la cotizacion

                     update tes.tplan_pago  pp set
                       id_estado_wf =  v_id_estado_actual,
                       estado = 'anulado',
                       id_usuario_mod=p_id_usuario,
                       fecha_mod=now(),
                       estado_reg='inactivo',
                       id_usuario_ai = v_parametros._id_usuario_ai,
                       usuario_ai = v_parametros._nombre_usuario_ai
                     where pp.id_plan_pago  = v_parametros.id_plan_pago;

                    --actulizamos el nro_cuota actual actual en obligacion_pago


                    update tes.tobligacion_pago op set
                       nro_cuota_vigente = v_nro_cuota - 1
                     where   op.id_obligacion_pago = v_registros.id_obligacion_pago;


                   --elimina los prorrateos
                    update  tes.tprorrateo pro  set
                     estado_reg='inactivo'
                    where pro.id_plan_pago =  v_parametros.id_plan_pago;
      ------------------------------------------------------------------------------------
      --  Eliminacion de cuotas de segundo nivel (que dependen de otro plan de pagos)
      -------------------------------------------------------------------------------------

      ELSIF  v_registros.tipo in ('pagado','ant_aplicado')   THEN
             -- eliminacion de cuotas de pago


                    select
                      max(pp.nro_cuota)
                    into
                      v_nro_cuota
                    from tes.tplan_pago pp
                    where
                      pp.id_plan_pago_fk = v_registros.id_plan_pago_fk
                      and   pp.estado_reg = 'activo';



                   IF v_nro_cuota != v_registros.nro_cuota THEN

                     raise exception 'Elimine primero la ultima cuota';

                   END IF;

                    select
                    te.id_tipo_estado
                   into
                    v_id_tipo_estado
                   from wf.tproceso_wf pw
                   inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso
                   inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'anulado'
                   where pw.id_proceso_wf = v_registros.id_proceso_wf;



               -- pasamos la cotizacion al siguiente estado

               v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                           NULL,
                                                           v_registros.id_estado_wf,
                                                           v_registros.id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
                                                           v_parametros._nombre_usuario_ai,
                                                           v_registros.id_depto,
                                                           'Elimina la cuota de pago');


                 -- actualiza estado en la cotizacion

                 update tes.tplan_pago  pp set
                   id_estado_wf =  v_id_estado_actual,
                   estado = 'anulado',
                   id_usuario_mod=p_id_usuario,
                   fecha_mod=now(),
                   estado_reg='inactivo',
                   id_usuario_ai = v_parametros._id_usuario_ai,
                   usuario_ai = v_parametros._nombre_usuario_ai
                 where pp.id_plan_pago  = v_parametros.id_plan_pago;


                  --elimina los prorrateos
                  update  tes.tprorrateo pro  set
                   estado_reg='inactivo'
                  where pro.id_plan_pago =  v_parametros.id_plan_pago;


                    update tes.tplan_pago  pp set
                     total_pagado = total_pagado - v_registros.monto_ejecutar_total_mo,
                     fecha_mod=now()
                   where pp.id_plan_pago  = v_registros.id_plan_pago_fk;



           ELSE

                raise exception 'Tipo no reconocido';

          END IF;




            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Plan Pago eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_SOLDEVPAG_IME'
 	#DESCRIPCION:	Solicitud de devengado o pago
 	#AUTOR:		RAC
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOLDEVPAG_IME')then

		begin

           --validacion de la cuota

           select
             pp.*,op.total_pago,op.comprometido, op.id_contrato, op.tipo_obligacion
           into
             v_registros
           from tes.tplan_pago pp
           inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
           where pp.id_plan_pago = v_parametros.id_plan_pago;

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

           if v_registros.tipo_obligacion = 'pago_pvr' then
            v_verficacion = tes.f_generar_comprobante_pvr(
                                                      p_id_usuario,
                                                      v_parametros._id_usuario_ai,
                                                      v_parametros._nombre_usuario_ai,
                                                      v_parametros.id_plan_pago,
                                                      v_parametros.id_depto_conta,
                                                      v_nombre_conexion);
           else
            v_verficacion = tes.f_generar_comprobante(
                                                      p_id_usuario,
                                                      v_parametros._id_usuario_ai,
                                                      v_parametros._nombre_usuario_ai,
                                                      v_parametros.id_plan_pago,
                                                      v_parametros.id_depto_conta,
                                                      v_nombre_conexion);
           end if;

          --select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');

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


    /*********************************
 	#TRANSACCION:  'TES_ANTEPP_IME'
 	#DESCRIPCION:	Trasaacion utilizada  pasar a  estados anterior en el plan de pagos
                    segun la operacion definida
 	#AUTOR:		RAC
 	#FECHA:		19-02-2013 12:12:51
	***********************************/

	elseif(p_transaccion='TES_ANTEPP_IME')then
        begin

        v_operacion = 'anterior';

        IF  pxp.f_existe_parametro(p_tabla , 'estado_destino')  THEN
           v_operacion = v_parametros.estado_destino;
        END IF;

        --recueprar datos del plan de pagos

        --obtenermos datos basicos
        select
            pp.id_plan_pago,
            pp.id_proceso_wf,
            pp.estado,
            pp.fecha_tentativa,
            pp.total_prorrateado ,
            pp.monto_ejecutar_total_mo,
            pp.estado,
            pwf.id_tipo_proceso
        into
            v_registros_pp

        from tes.tplan_pago  pp
        inner  join wf.tproceso_wf pwf  on  pwf.id_proceso_wf = pp.id_proceso_wf
        where pp.id_proceso_wf  = v_parametros.id_proceso_wf;

        v_id_proceso_wf = v_registros_pp.id_proceso_wf;

        IF  v_operacion = 'anterior' THEN
            --------------------------------------------------
            --Retrocede al estado inmediatamente anterior
            -------------------------------------------------
           --recuperaq estado anterior segun Log del WF
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
              FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);





        ELSE
           --recupera el estado inicial
           -- recuperamos el estado inicial segun tipo_proceso

             SELECT
               ps_id_tipo_estado,
               ps_codigo_estado
             into
               v_id_tipo_estado,
               v_codigo_estado
             FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_registros_pp.id_tipo_proceso);



             --busca en log e estado de wf que identificamos como el inicial
             SELECT
               ps_id_funcionario,
              ps_id_depto
             into
              v_id_funcionario,
             v_id_depto


             FROM wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);




        END IF;



         --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = 'notificacion';
             v_titulo  = 'Visto Bueno';


           IF   v_codigo_estado_siguiente not in('borrador','pendiente','pagado','devengado','anulado')   THEN
                  v_acceso_directo = '../../../sis_tesoreria/vista/plan_pago/PlanPagoVb.php';
                 v_clase = 'PlanPagoVb';
                 v_parametros_ad = '{filtro_directo:{campo:"plapa.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                 v_tipo_noti = 'notificacion';
                 v_titulo  = 'Visto Bueno';

           END IF;


          -- registra nuevo estado

          v_id_estado_actual = wf.f_registra_estado_wf(
              v_id_tipo_estado,                --  id_tipo_estado al que retrocede
              v_id_funcionario,                --  funcionario del estado anterior
              v_parametros.id_estado_wf,       --  estado actual ...
              v_id_proceso_wf,                 --  id del proceso actual
              p_id_usuario,                    -- usuario que registra
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_id_depto,                       --depto del estado anterior
              '[RETROCESO] '|| v_parametros.obs,
              v_acceso_directo,
              v_clase,
              v_parametros_ad,
              v_tipo_noti,
              v_titulo);




            IF  not tes.f_fun_regreso_plan_pago_wf(p_id_usuario,
                                                   v_parametros._id_usuario_ai,
                                                   v_parametros._nombre_usuario_ai,
                                                   v_id_estado_actual,
                                                   v_parametros.id_proceso_wf,
                                                   v_codigo_estado) THEN

               raise exception 'Error al retroceder estado';

            END IF;


         -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado)');
            v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          --Devuelve la respuesta
            return v_resp;





        end;

     /*********************************
 	#TRANSACCION:  'TES_SIGEPP_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de los planes de pago, integrado  con el EF
 	#AUTOR:		RAC
 	#FECHA:		17-12-2013 12:12:51
	***********************************/

	elseif(p_transaccion='TES_SIGEPP_IME')then
        begin

         /*   PARAMETROS

        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');
        */

        --obtenermos datos basicos
        select
            pp.id_plan_pago,
            pp.id_proceso_wf,
            pp.estado,
            pp.fecha_tentativa,
            op.numero,
            pp.total_prorrateado ,
            pp.monto_ejecutar_total_mo,
            pp.estado,
            pp.id_estado_wf,
            op.tipo_obligacion,
            pp.id_depto_lb,
            pp.monto,
            pp.id_plantilla,
            pp.id_obligacion_pago,
            op.num_tramite,
            pp.tipo,
            op.id_moneda
        into
            v_id_plan_pago,
            v_id_proceso_wf,
            v_codigo_estado,
            v_fecha_tentativa,
            v_num_obliacion_pago,
            v_total_prorrateo,
            v_monto_ejecutar_total_mo,
            v_estado_aux,
            v_id_estado_actual,
            v_tipo_obligacion,
            v_id_depto_lb_pp,
            v_monto_pp,
            v_id_plantilla,
            v_id_obligacion_pago_pp,
            v_numero_tramite,
            vtipo_pp,
            v_id_moneda

        from tes.tplan_pago  pp
        inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
        where pp.id_proceso_wf  = v_parametros.id_proceso_wf_act;

        --14-06-2021 (may)
        v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');

         -- 14-06-2021 (may)
          v_sw_verificacion = true;
          v_mensaje_verificacion ='';
          v_cont =1;

          IF vtipo_pp in ('devengado_pagado','devengado','devengado_pagado_1c','ant_aplicado','rendicion') and  v_pre_integrar_presupuestos = 'true'  THEN


           		--verifica si el presupuesto comprometido sobrante alcanza para pagar el monto de la cuota prorrateada correspondiente al pago

                  FOR  v_registros_pro in (
                                 select
                                   pro.id_prorrateo,
                                   pro.monto_ejecutar_mb,
                                   pro.monto_ejecutar_mo,
                                   od.id_partida_ejecucion_com,
                                   od.descripcion,
                                   od.id_concepto_ingas,
                                   par.sw_movimiento,
                                   tp.movimiento,
                                   od.id_centro_costo
                                 from  tes.tprorrateo pro
                                 inner join tes.tobligacion_det od on od.id_obligacion_det = pro.id_obligacion_det
                                 INNER JOIN pre.tpresupuesto   p  on p.id_centro_costo = od.id_centro_costo
   								 INNER JOIN pre.tpartida par on par.id_partida  = od.id_partida
                                 INNER JOIN pre.ttipo_presupuesto tp on tp.codigo = p.tipo_pres


                                 where  pro.id_plan_pago = v_id_plan_pago
                                   and pro.estado_reg = 'activo') LOOP

						--raise exception 'v_registros_pro: %', v_registros_pro;
                        v_comprometido=0;
                        v_ejecutado=0;

                        IF v_registros_pro.sw_movimiento != 'flujo'  THEN

				          SELECT
                                   ps_comprometido,
                                   COALESCE(ps_ejecutado,0)
                               into
                                   v_comprometido,
                                   v_ejecutado
                            FROM pre.f_verificar_com_eje_pag(v_registros_pro.id_partida_ejecucion_com, v_id_moneda,null);

                        END IF;

                      --verifica si el presupuesto comprometido sobrante alcanza para devengar
                      IF  (v_comprometido - v_ejecutado)  <  v_registros_pro.monto_ejecutar_mo  and v_registros_pro.sw_movimiento != 'flujo' THEN

                         -- raise EXCEPTION  '% - % = % < %',v_comprometido,v_ejecutado,v_comprometido - v_ejecutado, v_registros_pro.monto_ejecutar_mb;


                         select cig.desc_ingas
                         into v_desc_ingas
                         from  param.tconcepto_ingas cig
                         where cig.id_concepto_ingas  = v_registros_pro.id_concepto_ingas;

						  select ttc.codigo AS centro_costo
                          into v_centro_costo
                          from param.tcentro_costo tcc
        				  inner join param.ttipo_cc ttc on ttc.id_tipo_cc = tcc.id_tipo_cc
                          where  tcc.id_centro_costo = v_registros_pro.id_centro_costo;

                          select (tp.codigo||' - '||tp.nombre_partida)
                          into v_partida
                          from pre.tpartida_ejecucion pe
                          inner join  pre.tpartida tp on tp.id_partida = pe.id_partida
                          where pe.id_partida_ejecucion = v_registros_pro.id_partida_ejecucion_com;

                          --sinc_presupuesto
                          v_mensaje_verificacion ='Falta Presupuesto según el siguiente detalle :' ||v_cont::varchar||') '||v_desc_ingas||'('||  substr(v_registros_pro.descripcion, 0, 15)   ||'...)'||' Presup. '||v_centro_costo||' monto faltante ' || (v_registros_pro.monto_ejecutar_mo - (v_comprometido - v_ejecutado))::varchar||', en la partida: <br>'||coalesce(v_partida,'')||'<br> -------------------------------------------------------- <br>';
                          v_sw_verificacion=false;
                          v_cont = v_cont +1;

                      END IF;

						IF not v_sw_verificacion THEN
                             raise EXCEPTION  '% ',v_mensaje_verificacion;
                        END IF;


                   END LOOP;




          END IF;
          --


         --si esta saliendo de borrador vadamos el rango de gasto
         -- chequea fechas de costos inicio y fin
         IF(v_estado_aux in ('borrador','vbconta', 'vbsolicitante')) THEN
         	v_resp_doc =  tes.f_validar_periodo_costo(v_id_plan_pago);
         END IF;

         --validamos que el pago no sea menor a la fecha tentaiva
         if ( (v_estado_aux = 'borrador' and v_tipo_obligacion != 'adquisiciones'    and v_tipo_obligacion != 'rrhh' ) or v_estado_aux = 'vbsolicitante' ) then
            IF  v_fecha_tentativa::date > (now()::date + CAST('2 days' AS INTERVAL))::date THEN
               raise exception 'No puede adelantar el pago,  la fecha tentativa esta marcada para el %', to_char(v_fecha_tentativa,'DD/MM/YYYY/');
            END IF;
         end if;

          select
            ew.id_tipo_estado ,
            te.pedir_obs,
            ew.id_estado_wf
           into
            v_id_tipo_estado,
            v_perdir_obs,
            v_id_estado_wf

          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf =  v_parametros.id_estado_wf_act;


           -- obtener datos tipo estado

                select
                 te.codigo
                into
                 v_codigo_estado_siguiente
                from wf.ttipo_estado te
                where te.id_tipo_estado = v_parametros.id_tipo_estado;

             IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN

               v_id_depto = v_parametros.id_depto_wf;

             END IF;



             IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                  v_obs=v_parametros.obs;
             ELSE
                   v_obs='---';

             END IF;

             /*JRR (28) Se comenta la conformidad implicita
             if (v_estado_aux = 'borrador') then




                 update tes.tplan_pago
                 set conformidad = v_obs,
                 fecha_conformidad = now()
                 where id_proceso_wf  = v_parametros.id_proceso_wf_act;

                 select usu.id_usuario into v_id_usuario_firma
                  from tes.tplan_pago pp
                  inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                  inner join orga.tfuncionario fun on op.id_funcionario = fun.id_funcionario
                  inner join segu.tusuario usu on fun.id_persona = usu.id_persona
                  where pp.id_plan_pago = v_id_plan_pago;

                 v_resp_doc = wf.f_verifica_documento(v_id_usuario_firma, v_id_estado_actual);
             end if;*/

             --si viene del estado vobo finanzas actualizamos el depto de libro de bancos
             --22/07/2015 RAC --  se comenta el siguiente if ya que el estado vbfin en el wizard ya nose manda el libro de bancos
             /*
             if (v_estado_aux = 'vbfin') then
                 update tes.tplan_pago set
                 id_depto_lb = v_parametros.id_depto_lb
                 where id_proceso_wf  = v_parametros.id_proceso_wf_act;
             end if; */

             --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = 'notificacion';
             v_titulo  = 'Visto Bueno';


             IF   v_codigo_estado_siguiente not in('borrador','pendiente','pagado','devengado','anulado')   THEN
                  v_acceso_directo = '../../../sis_tesoreria/vista/plan_pago/PlanPagoVb.php';
                  v_clase = 'PlanPagoVb';
                  v_parametros_ad = '{filtro_directo:{campo:"plapa.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                  v_tipo_noti = 'notificacion';
                  v_titulo  = 'Visto Bueno';

             END IF;


             -- hay que recuperar el supervidor que seria el estado inmediato,...
             v_id_estado_actual =  wf.f_registra_estado_wf(v_parametros.id_tipo_estado,
                                                             v_parametros.id_funcionario_wf,
                                                             v_parametros.id_estado_wf_act,
                                                             v_id_proceso_wf,
                                                             p_id_usuario,
                                                             v_parametros._id_usuario_ai,
                                                             v_parametros._nombre_usuario_ai,
                                                             v_id_depto,
                                                             COALESCE(v_num_obliacion_pago,'--')||' Obs:'||v_obs,
                                                             v_acceso_directo ,
                                                             v_clase,
                                                             v_parametros_ad,
                                                             v_tipo_noti,
                                                             v_titulo);

          --------------------------------------
          -- registra los procesos disparados
          --------------------------------------

          FOR v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) LOOP

               --get cdigo tipo proceso
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
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_id_estado_actual,
                       v_registros_proc.id_funcionario_wf_pro,
                       v_registros_proc.id_depto_wf_pro,
                       v_registros_proc.obs_pro,
                       v_codigo_tipo_pro,
                       v_codigo_tipo_pro);


           END LOOP;

           -- actualiza estado en la solicitud
           -- funcion para cambio de estado

          IF  tes.f_fun_inicio_plan_pago_wf(p_id_usuario,
           									v_parametros._id_usuario_ai,
                                            v_parametros._nombre_usuario_ai,
                                            v_id_estado_actual,
                                            v_parametros.id_proceso_wf_act,
                                            v_codigo_estado_siguiente) THEN

          END IF;

          --(may) controla que el total del plan de pago no sea mayor a lo comprometido, controla en estado Borrador
          -- y tipo de devoluciones de garantia , ,
          --(may) 11-01-2020 para los de tipo de cuota pagado (pagar) no realice el control

          IF (v_estado_aux = 'borrador' and vtipo_pp not in ( 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant', 'pagado')) THEN


          		--SELECT sum(pp.monto_establecido)
                --05-03-2021 (may)modificacion ya no del monto establecido debe ser del monto a ejecutar...por el 13% ya no hay
                --SELECT sum(pp.monto)
                SELECT sum(pp.monto_ejecutar_total_mo)
                INTO v_sum_monto_pp
                FROM tes.tplan_pago pp
                WHERE pp.estado != 'anulado' and pp.estado != 'pago_exterior' and pp.estado != 'pagado' and pp.estado != 'pendiente'
                and pp.tipo not in ('especial', 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant', 'anticipo', 'ant_parcial', 'ant_rendicion', 'ant_aplicado', 'rendicion','ret_rendicion' )
                --and pp.tipo not in ( 'dev_garantia', 'dev_garantia_con', 'dev_garantia_con_ant', 'anticipo', 'ant_parcial', 'ant_rendicion')
                and pp.estado_reg != 'anulado'
                and pp.id_obligacion_pago = v_id_obligacion_pago_pp;

                SELECT pp.monto_establecido
                INTO v_sum_monto_solo_pp
                FROM tes.tplan_pago pp
                WHERE pp.id_plan_pago= v_id_plan_pago;

                SELECT sum(pe.monto)
                INTO v_sum_monto_pe
                FROM pre.tpartida_ejecucion pe
                join tes.tobligacion_pago opa on opa.num_tramite = pe.nro_tramite
                WHERE pe.tipo_movimiento = 'comprometido'
                and opa.id_obligacion_pago = v_id_obligacion_pago_pp;

                SELECT pc.codigo_tipo_relacion
                INTO v_codigo_tipo_relacion
                FROM param.tplantilla p
                inner join conta.tplantilla_calculo pc on pc.id_plantilla = p.id_plantilla
                WHERE  pc.codigo_tipo_relacion in ('IVA-CF','IVA-CF-PA', 'RC-IVA')
                and p.id_plantilla = v_id_plantilla;

                /*--
                05-03-2021 (may)  se modifica desde la fecha que se debe controlar que el monto comprometido no debe sobre pasar al MONTO A EJECUTAR
                                antes se realizaba con campo Monto sin iva
                --*/
                --para los que se descuentan el IVA 13%
                /*IF (v_codigo_tipo_relacion in ('IVA-CF','IVA-CF-PA', 'RC-IVA')) THEN

                    v_porcentaje13_monto = COALESCE(v_monto_pp * 0.13, 0);
                    v_monto_establecido  = COALESCE(v_monto_pp, 0) - COALESCE(v_porcentaje13_monto,0);

                    v_sum_total_pp = COALESCE(v_monto_establecido,0);

                   IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe,0)) THEN
                      raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_numero_tramite;
                   END IF;

               ELSE*/

                 v_monto_establecido  = COALESCE(v_monto_pp, 0);
                 v_sum_total_pp = (COALESCE(v_sum_monto_pp,0));
 				--raise exception 'v_sum_total_pp % = v_sum_monto_pp % - v_sum_monto_solo_pp % + v_monto_pp %',v_sum_total_pp, v_sum_monto_pp,v_sum_monto_solo_pp, v_monto_pp;

                 IF (v_tipo_obligacion != 'gestion_mat') THEN
                   IF (COALESCE(v_sum_total_pp,0) > COALESCE(v_sum_monto_pe, 0)) THEN
                      raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_numero_tramite;
                   END IF;
                 END IF;
               --END IF;

          END IF;


          ---


          /*------------------------------------------------------------------
            CONEXION A BUE
            si elige  Libro de bancos destino a BUE copia su obligacion,
            detalle y plan de pago al servidor BUE con un nuevo proceso

            --conexion para MIAMI falta desarrollar conexion
            --------------------------------------------------------------------*/

            v_obligacion_pago_json=(select json_agg(top.*)
            						from tes.tobligacion_pago top
            						inner join tes.tplan_pago tp on tp.id_obligacion_pago = top.id_obligacion_pago
                                    where tp.id_plan_pago = v_id_plan_pago );
            						--where tp.id_plan_pago = v_parametros.id_plan_pago );

            v_obligacion_pago=(select top.id_obligacion_pago
                                  from tes.tobligacion_pago top
                                  inner join tes.tplan_pago tp on tp.id_obligacion_pago = top.id_obligacion_pago
                                  where tp.id_plan_pago = v_id_plan_pago);
                                  --where tp.id_plan_pago = v_parametros.id_plan_pago);

            v_obligacion_det_json=(select json_agg(tod.*)
            						from tes.tobligacion_det tod
                                    inner join tes.tobligacion_pago top on top.id_obligacion_pago = tod.id_obligacion_pago
                                    where tod.id_obligacion_pago = v_obligacion_pago );

             v_obligacion_pp_json=(select json_agg(pp.*)
            				      from tes.tplan_pago pp
                                  inner join tes.tobligacion_pago top on top.id_obligacion_pago = pp.id_obligacion_pago
                                  where top.id_obligacion_pago = v_obligacion_pago );

             v_documentos=(select json_agg(dw.*)
            				      from wf.tdocumento_wf dw
                                  inner join tes.tplan_pago pp on pp.id_proceso_wf = dw.id_proceso_wf
                                  --inner join tes.tobligacion_pago op on op.id_proceso_wf = dw.id_proceso_wf
                                  --where op.id_obligacion_pago = v_obligacion_pago );
                                  where pp.id_plan_pago = v_id_plan_pago);
                                  --where pp.id_plan_pago = v_parametros.id_plan_pago);

             v_prorrateo_json=(select json_agg(pro.*)
            						from tes.tprorrateo pro
            						inner join tes.tplan_pago tp on tp.id_plan_pago = pro.id_plan_pago
                                      where tp.id_plan_pago = v_id_plan_pago );
         --raise exception 'llge %',v_prorrateo_json;
           --registra op para una internacional
           --IF (v_registros_pp.estado = 'vbfin') THEN
         IF pxp.f_get_variable_global('ESTACION_inicio') ='BOL' THEN
           IF (v_estado_aux = 'vbfin') THEN

             --IF (v_parametros.id_depto_lb = 27 )THEN
             IF (v_id_depto_lb_pp = 27 )THEN
                --conexion BUE
                v_cadena_cnx =  tes.f_obtener_cadena_conexion_argentina();

                --Se realizara la creacion de la funcion de insertando datos en buenos aires
                v_cadena_inter = 'select tes.ft_obligacion_pago_inter_bue_ime('''||p_administrador::integer||''','''||p_id_usuario::integer||''','''||v_obligacion_pago_json::json||''','''||v_obligacion_det_json::json||''','''||v_obligacion_pp_json::json||''','''||v_documentos::json||''','''||v_prorrateo_json::json||''')';


               v_resp =  (SELECT dblink_connect(v_cadena_cnx));

			             IF(v_resp!='OK') THEN

			             	--modificar bandera de fallo
			                 raise exception 'FALLA CONEXION A LA BASE DE DATOS CON DBLINK';

			             ELSE

                        --raise exception 'FALLA %',v_obligacion_pago_json;

                            PERFORM * FROM dblink(v_cadena_inter,true)AS ( xx varchar);


			   			 END IF;
              END IF;

              /*---------------------------------------------------------
              02-06-2020 (may)
              para la estacion central BOL, cuando sean pagos internacionales realice una modificacion presupuestaria automaticamente
              desde el estado de vbfin
              -----------------------------------------------------------*/

              select deplb.prioridad
              into v_prioridad
              from tes.tplan_pago pp
              left join param.tdepto deplb ON deplb.id_depto = pp.id_depto_lb
              where pp.id_plan_pago = v_id_plan_pago;


			  --23-01-2022 (may) desde la fecha los PGA con conceptos de gastos de flujo ya no pasan por el estado de presupuestos, y tampoco se realizara la modificacion presupuestaria
               SELECT par.sw_movimiento
               INTO v_sw_movimiento_partida
               FROM  tes.tobligacion_det od
               inner JOIN pre.tpartida par ON par.id_partida = od.id_partida
               WHERE od.id_obligacion_pago =  v_id_obligacion_pago_pp
               LIMIT 1 ;


				-- v_tipo_obligacion != 'pago_especial' estos tipo de obligacion no realizan su modificacion presupuestaria porq son sin imputacion
                IF (v_prioridad = 3 and v_tipo_obligacion != 'pago_especial') THEN

                	IF (v_tipo_obligacion= 'pga' and v_sw_movimiento_partida !='flujo') THEN

                  		v_resp = tes.f_inserta_plan_pago_mod_presu(p_administrador, p_id_usuario,v_id_plan_pago);

                	END IF;

                END IF;


              --

            END IF;

         END IF;
          ---


          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del plan de pagos)');
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

     end;


    /*********************************
 	#TRANSACCION:  'TES_SINPRE_IME'
 	#DESCRIPCION:	Incremeta el  presupuesto faltante para la cuota indicada del plan de pagos
 	#AUTOR:		rac
 	#FECHA:		08-07-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_SINPRE_IME')then

		begin

           -- verficar presupuesto y comprometer
           IF not tes.f_gestionar_presupuesto_tesoreria(NULL, p_id_usuario, 'sincronizar_presupuesto',v_parametros.id_plan_pago)  THEN

                 raise exception 'Error al comprometer el presupeusto';

           END IF;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','El presupuesto de la cuota del plan de pago fue incrementado exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************
 	#TRANSACCION:  'TES_GENCONF_IME'
 	#DESCRIPCION:	Actualiza los Datos del Acta de Conformidad
 	#AUTOR:		jrr
 	#FECHA:		26-09-2014
	***********************************/

	elsif(p_transaccion='TES_GENCONF_IME')then

		begin --raise exception 'v_parametros.id_plan_pago: %', v_parametros.id_plan_pago;
        	--verificar si el plan de pagos fue realizado por el empleado solicitante o el usuario de registro
            --de la obligacion de pago
            select usu.id_persona into v_id_persona
            from segu.vusuario usu
            where usu.id_usuario = p_id_usuario;



            select ce.id_uo into v_id_uo
            from tes.tconcepto_excepcion ce
            inner join tes.tobligacion_det od on ce.id_concepto_ingas = od.id_concepto_ingas
            inner join tes.tobligacion_pago op on od.id_obligacion_pago = op.id_obligacion_pago
            inner join tes.tplan_pago pp  on pp.id_obligacion_pago = op.id_obligacion_pago
            where pp.id_plan_pago = v_parametros.id_plan_pago
            limit 1 offset 0;

            if (v_id_uo is not null) then
            	raise exception 'Este pago tiene definida una excepción y solo el gerente aprobador de la obligacion firmara el acta de conformidad';
            end if;

            if(not exists (select 1
                from tes.tplan_pago pp
                inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                inner join orga.tfuncionario fun on op.id_funcionario = op.id_funcionario
                inner join segu.vusuario usu on usu.id_usuario = op.id_usuario_reg
                where pp.id_plan_pago = v_parametros.id_plan_pago and
                (v_id_persona = fun.id_persona or v_id_persona = usu.id_persona or p_administrador = 1))) then
            	raise exception 'Solo el solicitante y el usuario que registro la obligacion pueden generar la conformidad';
            end if;


        	select usu.id_usuario into v_id_usuario_firma
            from tes.tplan_pago pp
            inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
            inner join orga.tfuncionario fun on op.id_funcionario = fun.id_funcionario
            inner join segu.tusuario usu on fun.id_persona = usu.id_persona
            where pp.id_plan_pago = v_parametros.id_plan_pago;

            if v_id_usuario_firma is null then
            	raise exception 'El funcionario del proceso no tiene usuario en el sistema para firmar el acta de conformidad';
            end if;

           update tes.tplan_pago
           set conformidad = v_parametros.conformidad,
           fecha_conformidad = v_parametros.fecha_conformidad
           where id_plan_pago = v_parametros.id_plan_pago;

           select pp.id_estado_wf,pp.id_proceso_wf into
           v_id_estado_actual,v_id_proceso_wf
           from tes.tplan_pago pp
           where pp.id_plan_pago =  v_parametros.id_plan_pago;

           --para eliminar la firma si existiera
           update wf.tdocumento_wf
           set fecha_firma = NULL
           from wf.ttipo_documento td
           where td.id_tipo_documento = wf.tdocumento_wf.id_tipo_documento and td.codigo = 'ACTCONF' and
           wf.tdocumento_wf .estado_reg = 'activo' and td.estado_reg = 'activo' and
           wf.tdocumento_wf .id_proceso_wf = v_id_proceso_wf;

           select tpp.conformidad, tpp.es_ultima_cuota
           into v_record
           from tes.tplan_pago tpp
           where tpp.id_plan_pago = v_parametros.id_plan_pago;

           if(v_record.es_ultima_cuota = true and (v_record.conformidad != '' or v_record.conformidad is not null))then
             --Enviar correo a las auxiliares de adquisicones indicando que se dio conformidad al pago.
              v_correo_conformidad_pago = tes.f_correo_conformidad_pago(v_parametros.id_plan_pago, p_id_usuario);
           end if;

           v_resp_doc = wf.f_verifica_documento(v_id_usuario_firma, v_id_estado_actual);
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se modificaron los datos de la conformidad exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

    /*********************************
 	#TRANSACCION:  'TES_REVPP_IME'
 	#DESCRIPCION:	Sirve cpara marcar como revisado o no revisado, sirve como un indicador  de que la documentacion fue revisada por el asistente
 	#AUTOR:		rac
 	#FECHA:		23-09-2014 15:43:23
	***********************************/

	elsif(p_transaccion='TES_REVPP_IME')then

		begin

            select
              pp.revisado_asistente,
              pp.id_proceso_wf
            into
              v_registros_tpp
            from tes.tplan_pago pp
            where pp.id_plan_pago = v_parametros.id_plan_pago;

            IF v_registros_tpp.revisado_asistente = 'si' THEN
               v_revisado = 'no';
            ELSE
               v_revisado = 'si';
            END IF;

            update tes.tplan_pago  pp set
               revisado_asistente = v_revisado,
               id_usuario_mod=p_id_usuario,
               fecha_mod=now(),
               id_usuario_ai = v_parametros._id_usuario_ai,
               usuario_ai = v_parametros._nombre_usuario_ai
             where pp.id_plan_pago  = v_parametros.id_plan_pago;

            --modifica el proeso wf para actulizar el mismo campo
             update wf.tproceso_wf  set
               revisado_asistente = v_revisado
             where id_proceso_wf  = v_registros_tpp.id_proceso_wf;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','el plan de pago fue marcado como revisado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;


    /*********************************
 	#TRANSACCION:  'TES_CBFRM500_IME'
 	#DESCRIPCION:	CAmbio el estado de requiere fromr 500 para no generar mas alarmas de correo
 	#AUTOR:		RAC KPLIAN
 	#FECHA:		02-03-2015
	***********************************/

	elsif(p_transaccion='TES_CBFRM500_IME')then

		begin

           update tes.tplan_pago  set
            tiene_form500 = 'si'
           where id_plan_pago = v_parametros.id_plan_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se cambio el estado de tiene formulario 500 ');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_parametros.id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;
        end;

     /*********************************
 	#TRANSACCION:  'TES_GETCFGPAGO_IME'
 	#DESCRIPCION:	Recupera la configuracion de apgos habilitados en la estación
 	#AUTOR:		RAC KPLIAN
 	#FECHA:		22-11-2015
	***********************************/

	elsif(p_transaccion='TES_GETCFGPAGO_IME')then

		begin
         --raise exception 'llega'

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se cambio el estado de tiene formulario 500 ');
            v_resp = pxp.f_agrega_clave(v_resp,'tes_tipo_pago_deshabilitado', pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'));

            --Devuelve la respuesta
            return v_resp;
        end;
    /*********************************
 	#TRANSACCION:  'TES_ULTIMA_CUOTA_SET'
 	#DESCRIPCION:	Fijamos el el campo es_ultimo_pago a true, si fuese el ultimo cuota de pago
 	#AUTOR:		Franklin E.
 	#FECHA:		31-05-2016
	***********************************/

	elsif(p_transaccion='TES_ULTIMA_CUOTA_SET')then

		begin
        	--raise exception 'id_obligacion_pago: %, es_ultima_cuota: %, id_plan_pago: %', v_parametros.id_obligacion_pago, v_parametros.es_ultima_cuota, v_parametros.id_plan_pago;
        	IF(v_parametros.accion = 'grid')THEN
                SELECT count(tpp.id_plan_pago) AS cont, tpp.id_plan_pago
                INTO v_reg_plan_pago
                FROM tes.tobligacion_pago top
                INNER JOIN tes.tplan_pago tpp ON tpp.id_obligacion_pago = top.id_obligacion_pago
                WHERE  top.id_obligacion_pago = v_parametros.id_obligacion_pago AND tpp.es_ultima_cuota = true
                GROUP BY tpp.id_plan_pago;

                IF(v_reg_plan_pago.cont IS NULL)THEN
                    v_bandera = true;
                END IF;

                IF(v_reg_plan_pago.cont =1 AND v_reg_plan_pago.id_plan_pago = v_parametros.id_plan_pago)THEN
                    UPDATE tes.tplan_pago SET
                      es_ultima_cuota =  CASE WHEN (v_parametros.es_ultima_cuota=FALSE OR v_parametros.es_ultima_cuota IS NULL)  THEN TRUE ELSE FALSE END
                    WHERE id_plan_pago = v_parametros.id_plan_pago;
                ELSIF v_bandera THEN
                    UPDATE tes.tplan_pago SET
                      es_ultima_cuota =  CASE WHEN (v_parametros.es_ultima_cuota=FALSE OR v_parametros.es_ultima_cuota IS NULL)  THEN TRUE ELSE FALSE END
                    WHERE id_plan_pago = v_parametros.id_plan_pago;
                END IF;
            ELSE
            	SELECT count(tpp.id_plan_pago)
                INTO v_cont_plan_pago
				FROM tes.tplan_pago tpp
				WHERE tpp.id_obligacion_pago = v_parametros.id_obligacion_pago AND tpp.estado_reg = 'activo';

                UPDATE tes.tobligacion_pago SET
                	total_nro_cuota = v_cont_plan_pago
                WHERE id_obligacion_pago = v_parametros.id_obligacion_pago;

            	FOR v_reg_plan_pago IN (SELECT tpp.id_plan_pago, tpp.es_ultima_cuota
                						FROM tes.tplan_pago tpp
                                        WHERE tpp.id_obligacion_pago = v_parametros.id_obligacion_pago AND tpp.estado_reg = 'activo'
                                        ORDER BY tpp.fecha_reg ASC)LOOP

                	UPDATE tes.tplan_pago SET
                		es_ultima_cuota = CASE WHEN v_cont_plan_pago = 1 THEN TRUE ELSE FALSE END
                	WHERE id_plan_pago = v_reg_plan_pago.id_plan_pago;

                    v_cont_plan_pago = v_cont_plan_pago - 1;
                END LOOP;
            END IF;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se cambio el estado de la ultima cuota con Exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::VARCHAR);
            --Devuelve la respuesta
            return v_resp;
        end;


    /*********************************
 	#TRANSACCION:  'TES_REPPP_IME'
 	#DESCRIPCION:	Replicacion Plan de Pago
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		15-08-2019 22:09:52
	***********************************/

	elsif(p_transaccion='TES_REPPP_IME')then

		begin
                /*--------------------
                Replicar un Plan de Pago
                ----------------------*/

 				select pp.*
                into v_parametros_pp
                from tes.tplan_pago pp
                where pp.id_plan_pago = v_parametros.id_plan_pago;

            v_resp = tes.f_inserta_plan_pago_replicado(p_administrador, p_id_usuario,hstore(v_parametros_pp));



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Plan Pago Replicado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_pago',v_id_plan_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;



    else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		--v_res_cone=(select dblink_disconnect());
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

ALTER FUNCTION tes.f_plan_pago_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;

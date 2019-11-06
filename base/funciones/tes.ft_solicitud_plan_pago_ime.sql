CREATE OR REPLACE FUNCTION tes.ft_solicitud_plan_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_solicitud_plan_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tplan_pago'
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

    v_cuenta_bancaria_benef		varchar;
    v_plan_pago					integer;

    --- franklin.espinoza
    v_documentos				integer[];
    v_tam_array_doc				integer;
	v_id_documento				integer;
BEGIN

    v_nombre_funcion = 'tes.ft_solicitud_plan_pago_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_SOPLAPA_INS'
 	#DESCRIPCION:	Insercion de cuotas de devengado en el plan de pago
 	#AUTOR:		admin
 	#FECHA:		12-12-2018 15:43:23
	***********************************/

	if(p_transaccion='TES_SOPLAPA_INS')then

        begin

        	--valida fechas de costos
			 IF v_parametros.fecha_costo_fin <  v_parametros.fecha_costo_ini THEN
               raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
            END IF;

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
                            op.pago_variable
                          into v_registros
                           from tes.tobligacion_pago op
                           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;


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

                      IF v_registros.pago_variable='si' or v_registros.pago_variable='no' THEN

                            SELECT sum(pp.monto)
                            INTO v_sum_monto_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.estado != 'anulado'
                            and pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            SELECT sum(pe.monto)
                            INTO v_sum_monto_pe
                            FROM pre.tpartida_ejecucion pe
                            join tes.tobligacion_pago opa on opa.num_tramite = pe.nro_tramite
                            WHERE opa.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            v_sum_total_pp = v_sum_monto_pp + v_parametros.monto;

                          -- raise exception 'llegaaaa %>= %', v_sum_monto_pp,v_sum_monto_pe ;
                            IF (v_sum_total_pp > v_sum_monto_pe) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;

                            v_sum_total_pp = v_parametros.monto;

                            IF (v_sum_total_pp > v_sum_monto_pe) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;



                      END IF;
                 ----

        	select tipo_obligacion into v_tipo_obligacion
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos de devengado a una obligacion de RRHH';
            end if;

            v_resp = tes.ft_solicitud_inserta_plan_pago_dev(p_administrador, p_id_usuario,hstore(v_parametros));

            --Devuelve la respuesta
            return v_resp;

            update tes.tplan_pago set
                fecha_costo_ini = v_parametros.fecha_costo_ini,
                fecha_costo_fin = v_parametros.fecha_costo_fin

			where id_int_comprobante = v_parametros.id_int_comprobante;

             --actualiza el campo nro_cuenta_bancaria para los SP

                /*    IF v_tipo_obligacion = 'sp' then
                      select pcb.nro_cuenta ||'-'|| ins.nombre
                      into v_cuenta_bancaria_benef
                      from param.tproveedor_cta_bancaria pcb
                      left join param.tinstitucion ins on ins.id_institucion=pcb.id_banco_beneficiario
                      --left join tes.tplan_pago pp on pp.id_proveedor_cta_bancaria = pcb.id_proveedor_cta_bancaria
                      where pcb.id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria;

                        update tes.tplan_pago SET
                        nro_cuenta_bancaria = v_cuenta_bancaria_benef
                        where id_plan_pago= v_id_plan_pago;
                    end IF;*/
              ---


		end;

   	/*********************************
 	#TRANSACCION:  'TES_SOPLAPAPA_INS'
 	#DESCRIPCION:	Insercion de cuotas de CUOTAS DE SEGUNDO NIVEL,  PAGO o APLICAION DE ANTICIPO , en el plan de pago
 	#AUTOR:		admin
 	#FECHA:	12-12-2018   15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOPLAPAPA_INS')then

        begin

        	select tipo_obligacion into v_tipo_obligacion
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos a una obligacion de RRHH';
            end if;


            v_resp = tes.f_inserta_plan_pago_pago(p_administrador, p_id_usuario,hstore(v_parametros));
            --Devuelve la respuesta
            return v_resp;

		end;

   /*********************************
 	#TRANSACCION:  'TES_SOPPANTPAR_INS'
 	#DESCRIPCION:	Inserta cuotas del tipo anticipo parcial , o
                    anticipo total  o dev_garantia (todas no  tienen prorrateo por que no ejecutan presupuesto)
 	#AUTOR:		admin
 	#FECHA: 12-12-2018   15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOPPANTPAR_INS')then

        begin

        	select tipo_obligacion into v_tipo_obligacion
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            if (v_tipo_obligacion = 'rrhh') then
            	raise exception 'No es posible insertar pagos a una obligacion de RRHH';
            end if;
            v_resp = tes.f_inserta_plan_pago_anticipo(p_administrador, p_id_usuario,hstore(v_parametros));
            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'TES_SOPLAPA_MOD'
 	#DESCRIPCION:	Modificacion de cuotas de devegando y pago
 	#AUTOR:		admin
 	#FECHA:		12-12-2018 15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOPLAPA_MOD')then

		begin

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

             /*IF  pxp.f_existe_parametro(p_tabla,'id_cuenta_bancaria_mov') THEN
               v_id_cuenta_bancaria_mov =  v_parametros.id_cuenta_bancaria_mov;
             END IF;*/

             IF  pxp.f_existe_parametro(p_tabla,'forma_pago') THEN
               v_forma_pago =  v_parametros.forma_pago;
             END IF;

             IF  pxp.f_existe_parametro(p_tabla,'nro_cheque') THEN
               v_nro_cheque =  v_parametros.nro_cheque;
             END IF;

			--SI ES SP y SPD
            select tipo_obligacion
            into v_tipo_obligacion
            from tes.tobligacion_pago
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            --IF v_tipo_obligacion = 'sp' or v_tipo_obligacion = 'spd' or v_tipo_obligacion = 'spi'then


                     select ins.nombre||'-'|| pcb.nro_cuenta
                      into v_cuenta_bancaria_benef
                      from param.tproveedor_cta_bancaria pcb
                      left join param.tinstitucion ins on ins.id_institucion=pcb.id_banco_beneficiario
                      where pcb.id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria;

             		v_nro_cuenta_bancaria = v_cuenta_bancaria_benef::varchar;

             --ELSE
             --	   IF  pxp.f_existe_parametro(p_tabla,'nro_cuenta_bancaria') THEN
             --   	v_nro_cuenta_bancaria =  v_parametros.nro_cuenta_bancaria;
             --	   END IF;
             --end IF;
             --

             IF  pxp.f_existe_parametro(p_tabla,'nro_cuenta_bancaria') THEN
                v_nro_cuenta_bancaria =  v_parametros.nro_cuenta_bancaria;
             END IF;

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
            op.pago_variable
          into v_registros
           from tes.tobligacion_pago op
           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;


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


           --valida que los valores no sean negativos
           IF  v_parametros.monto <0 or v_parametros.monto_no_pagado <0 or v_parametros.otros_descuentos  <0 THEN
               raise exception 'No se admiten cifras negativas';
           END IF;


           -------------------------------------
           -- Si es una cuota de devengado
           -- Segun el tipo de cuota
           ------------------------------------
--raise exception 'tipo  %', v_registros_pp.tipo;
           -----------------------------------------------------------------------------------------------
           -- EDICION DE CUOTAS QUE TIENEN DEVENGADO   ('devengado_pagado','devengado','devengado_pagado_1c'), 'devengado_pagado_1c_sp'
           --------------------------------------------------------------------------------------------------

           --(may) 18-07-2019 los tipo pp especial_spi son para las internacionales -tramites SIP
           --(may) 20-07-2019 los tipo plan de pago devengado_pagado_1c_sp son para las internacionales -tramites sp con contato
           IF v_registros_pp.tipo in ('devengado_pagado','devengado','devengado_pagado_1c_sp','especial_spi', 'especial', 'v_registros_pp.tipo','devengado_pagado_1c') THEN


                 IF v_registros_pp.tipo in  ('especial_spi', 'especial') THEN
--raise exception 'tipo  %, %', v_registros_pp.tipo, v_registros.pago_variable;
                       IF v_registros.pago_variable = 'no' THEN
                           v_monto_total = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'especial_total');
                           v_especial = tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, 'especial_spi');
                           IF v_especial + v_registros_pp.monto <  v_parametros.monto  THEN
                              raise exception 'No puede exceder el total determinado: %', v_monto_total;
                           END IF;
                       END IF;

                        v_monto_ejecutar_total_mo   = COALESCE(v_parametros.monto,0);
                        --v_liquido_pagable  = COALESCE(v_parametros.monto,0);
                        --cambio de liquido pagable por tipo de estacion modificacion del 06/11/2019 por Alan
                          if (v_registros_pp.tipo in  ('especial_spi'))then
                         	  v_liquido_pagable = COALESCE(v_parametros.monto,0) - COALESCE(v_parametros.monto_no_pagado,0) - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0) - COALESCE(v_parametros.descuento_ley,0) - COALESCE(v_parametros.descuento_anticipo,0)- COALESCE(v_parametros.descuento_inter_serv,0);
						              else
                        	  v_liquido_pagable  = COALESCE(v_parametros.monto,0);
                      	  end if;

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

                        IF   v_registros_pp.tipo in('devengado_pagado','devengado_pagado_1c_sp', 'devengado_pagado_1c')  THEN
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

                          raise exception 'El decuento por anticipo no puede exceder el faltante por descontar que es  %',v_monto_ant_parcial_descontado;

                     END IF;

                     -- calcula el liquido pagable y el monto a ejecutar presupeustaria mente
                     v_liquido_pagable = COALESCE(v_parametros.monto,0) - COALESCE(v_parametros.monto_no_pagado,0) - COALESCE(v_parametros.otros_descuentos,0) - COALESCE( v_parametros.monto_retgar_mo,0) - COALESCE(v_parametros.descuento_ley,0) - COALESCE(v_parametros.descuento_anticipo,0)- COALESCE(v_parametros.descuento_inter_serv,0);
                     v_monto_ejecutar_total_mo  = COALESCE(v_parametros.monto,0) /*(f.e.a)3182019-  COALESCE(v_parametros.monto_no_pagado,0)*/ -  COALESCE(v_parametros.monto_anticipo,0);
                     v_porc_monto_retgar = COALESCE(v_parametros.monto_retgar_mo,0)/COALESCE(v_parametros.monto,0);

                     IF   v_liquido_pagable  < 0  or v_monto_ejecutar_total_mo < 0  THEN
                          raise exception ' Ni el  monto a ejecutar   ni el liquido pagable  puede ser menor a cero';
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


                      --si es un pago variable, controla que el total del plan de pago no sea mayor a lo comprometido
                      IF v_registros.pago_variable='si' or v_registros.pago_variable='no' THEN

                            SELECT sum(pp.monto)
                            INTO v_sum_monto_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.estado != 'anulado'
                            and pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            SELECT pp.monto
                            INTO v_sum_monto_solo_pp
                            FROM tes.tplan_pago pp
                            WHERE pp.id_plan_pago= v_parametros.id_plan_pago;

                            SELECT sum(pe.monto)
                            INTO v_sum_monto_pe
                            FROM pre.tpartida_ejecucion pe
                            join tes.tobligacion_pago opa on opa.num_tramite = pe.nro_tramite
                            WHERE opa.id_obligacion_pago = v_parametros.id_obligacion_pago;

                            v_sum_total_pp = (v_sum_monto_pp - v_sum_monto_solo_pp) + v_parametros.monto;
                             -- raise exception '% = % - % + %',v_sum_total_pp,  v_sum_monto_pp, v_sum_monto_solo_pp, v_parametros.monto;

                           IF ((v_sum_total_pp) > v_sum_monto_pe) THEN
                              raise exception ' El monto total de las cuotas es de % y excede al monto total certificado de % para el trámite %. Comunicarse con la Unidad de Presupuestos. ',v_sum_total_pp, v_sum_monto_pe, v_registros.num_tramite ;
                            END IF;

                        END IF;
                   --

               END IF;



           -------------------------------------------------------------
           -- EDICION DE CUOTAS DEL ANTICIPO   (ant_parcial,anticipo)
           --------------------------------------------------------------

           ELSIF v_registros_pp.tipo in  ('ant_parcial', 'anticipo', 'dev_garantia') THEN



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

           IF COALESCE(v_monto_excento,0) > COALESCE(v_monto_ejecutar_total_mo,0) and v_registros_pp.tipo not in ('ant_parcial','anticipo','dev_garantia','especial_spi') THEN
             raise exception 'El monto excento (%) debe ser menor que el total a ejecutar(%)',v_monto_excento, v_monto_ejecutar_total_mo  ;
           END IF;

           --CALUCLO DEL PORCENTAJE DE MONTO EXCENTO
           IF  COALESCE(v_monto_excento,0) > 0 THEN
                v_porc_monto_excento_var  = v_monto_excento / v_parametros.monto;
           ELSE
                v_porc_monto_excento_var = 0;
           END IF;



			--franklin.espinoza
            --verifacion si existe parametro documentos a relacionar a un plan de pago
            if pxp.f_existe_parametro(p_tabla,'documentos') then
                v_documentos = string_to_array(v_parametros.documentos,',');
                v_tam_array_doc = array_length(v_documentos,1);
                --raise exception 'documentosSSSSS %, %, %', v_parametros.documentos, v_documentos, v_tam_array_doc;
                if v_tam_array_doc > 0 then
                    for v_id_documento in SELECT documento FROM unnest(v_documentos) AS documento loop
                        update conta.tdoc_compra_venta set
                        	id_plan_pago = v_parametros.id_plan_pago
                        where id_doc_compra_venta = v_id_documento;
                    end loop;
                end if;
            else
				--if pxp.f_existe_parametro(p_tabla,'documentos') then
                  if v_parametros.id_doc_compra_venta is not null then
                    update conta.tdoc_compra_venta set
                        id_plan_pago = v_parametros.id_plan_pago
                    where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
                  end if;
                --end if;
            end if;
--raise exception 'v_monto_ejecutar_total_mo: %',v_monto_ejecutar_total_mo;
			--Sentencia de la modificacion
			update tes.tplan_pago set
			monto_ejecutar_total_mo = v_monto_ejecutar_total_mo,
			obs_descuentos_anticipo = v_parametros.obs_descuentos_anticipo,
			id_plantilla = v_parametros.id_plantilla,
			descuento_anticipo = COALESCE(v_parametros.descuento_anticipo,0),
            descuento_inter_serv = COALESCE(v_parametros.descuento_inter_serv,0),
            obs_descuento_inter_serv = v_parametros.obs_descuento_inter_serv,
			otros_descuentos = COALESCE( v_parametros.otros_descuentos,0),
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
            tipo_cambio= case when v_parametros.tipo_cambio is null then 1 else v_parametros.tipo_cambio end,
            monto_retgar_mo= v_parametros.monto_retgar_mo,
            descuento_ley=v_parametros.descuento_ley,
            obs_descuentos_ley=v_parametros.obs_descuentos_ley,
            porc_descuento_ley=v_parametros.porc_descuento_ley,
            nro_cheque =  COALESCE(v_nro_cheque,0),
            fecha_tentativa = v_parametros.fecha_tentativa,
            nro_cuenta_bancaria = v_nro_cuenta_bancaria,
            --id_cuenta_bancaria_mov = v_parametros.id_cuenta_bancaria_mov,
            --id_cuenta_bancaria_mov = v_id_cuenta_bancaria_mov,
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
            --es_ultima_cuota = v_parametros.es_ultima_cuota
            id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria

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

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_ges) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_ges)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, NÚMERO DE TRÁMITE % gestión %', v_num_tramite, v_anio_ges;
            END IF;

            -- chequea fechas de costos inicio y fin
            v_resp_doc =  tes.f_validar_periodo_costo(v_parametros.id_plan_pago);

            --modifica el campo nro_cuenta_bancaria para los SP
                      select tipo_obligacion
                      into v_tipo_obligacion
                      from tes.tobligacion_pago
                      where id_obligacion_pago = v_parametros.id_obligacion_pago;

                    IF v_tipo_obligacion = 'sp' or v_tipo_obligacion = 'spd' or v_tipo_obligacion = 'spi' then
                      select pcb.nro_cuenta ||'-'|| ins.nombre
                      into v_cuenta_bancaria_benef
                      from param.tproveedor_cta_bancaria pcb
                      left join param.tinstitucion ins on ins.id_institucion=pcb.id_banco_beneficiario
                      --left join tes.tplan_pago pp on pp.id_proveedor_cta_bancaria = pcb.id_proveedor_cta_bancaria
                      where pcb.id_proveedor_cta_bancaria = v_parametros.id_proveedor_cta_bancaria;


                        update tes.tplan_pago SET
                        nro_cuenta_bancaria = v_cuenta_bancaria_benef
                        where id_plan_pago= v_parametros.id_plan_pago;
                    end IF;
                    ---



            IF v_registros_pp.tipo not in ('ant_parcial','anticipo','dev_garantia') THEN

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
 	#TRANSACCION:  'TES_SOPLAPA_ELI'
 	#DESCRIPCION:	Eliminacion de registros de plan de pagos
 	#AUTOR:		admin
 	#FECHA:		12-12-2018 15:43:23
	***********************************/

	elsif(p_transaccion='TES_SOPLAPA_ELI')then

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
           IF  v_registros.tipo in  ('devengado_pagado','devengado','devengado_pagado_1c','ant_parcial','anticipo','dev_garantia','especial', 'devengado_pagado_1c_sp', 'especial_spi')   THEN
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
 	#TRANSACCION:  'TES_SOSIGEPP_IME'
 	#DESCRIPCION:	funcion que controla el cambio al Siguiente estado de los planes de pago, integrado  con el EF
 	#AUTOR:		ADMIN
 	#FECHA:		12-12-2018 12:12:51
	***********************************/

	elseif(p_transaccion='TES_SOSIGEPP_IME')then
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
            op.tipo_obligacion
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
            v_tipo_obligacion

        from tes.tplan_pago  pp
        inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
        where pp.id_proceso_wf  = v_parametros.id_proceso_wf_act;

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


          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del plan de pagos)');
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

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

CREATE OR REPLACE FUNCTION tes.ft_obligacion_pago_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_obligacion_pago_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tobligacion_pago'
 AUTOR: 		Gonzalo Sarmiento Sejas (KPLIAN)
 FECHA:	        02-04-2013 16:01:32
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
    v_registros_op          record;
    va_tipo_pago			varchar[];

	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_obligacion_pago	integer;

    v_tipo_documento   		varchar;
    v_num 					varchar;
    v_id_periodo 			integer;
    v_codigo_proceso_macro  varchar;
    v_id_proceso_macro 		integer;
    v_codigo_tipo_proceso	varchar;

     v_num_tramite 			varchar;
     v_id_proceso_wf 		integer;
     v_id_estado_wf 		integer;
     v_codigo_estado 		varchar;
     v_codigo_estado_ant 	varchar;
     v_anho 				integer;
     v_id_gestion 			integer;
     v_id_subsistema 		integer;

    va_id_tipo_estado 		integer[];
    va_codigo_estado 		varchar[];
    va_disparador 			varchar[];
    va_regla 				varchar[];
    va_prioridad  			integer[];

    v_id_proceso_compra 	integer;
    v_id_depto 				integer;
    v_total_detalle 		numeric;
    v_id_estado_actual  	integer;
    v_tipo_obligacion 		varchar;
    v_id_tipo_estado 		integer;
    v_id_funcionario 		integer;
    v_id_usuario_reg 		integer;
    v_id_estado_wf_ant  	integer;
    v_comprometido 			varchar;
    v_monto_total 			numeric;
    v_id_obligacion_det 	integer;
    v_factor 				numeric;

    v_registros    			record;
    v_cad_ep 				varchar;
    v_cad_uo 				varchar;
    v_tipo_plan_pago		varchar;


     v_total_nro_cuota  integer;
     v_fecha_pp_ini		date;
     v_rotacion			integer;
     v_id_plantilla 	integer;

     v_hstore_pp		hstore;
     v_tipo_cambio_conv numeric;
     v_registros_plan   record;
     v_desc_proveedor   text;
     v_descuentos_ley   numeric;
     v_i                integer;
     v_monto_cuota 		numeric;
     v_ope_filtro       varchar[];
     v_ind              varchar;
     v_sw               boolean;
     v_resp_doc     boolean;
     va_id_funcionario_gerente   INTEGER[];
     v_num_estados integer;
     v_num_funcionarios integer;
     v_id_funcionario_estado integer;
     v_obs varchar;
     v_pago_variable	varchar;
     v_check_ant_mixto numeric;
     v_hstore_registros hstore;
     v_date date;
     v_registros_det record;
     v_id_centro_costo_dos integer;
     v_id_obligacion_pago_sg varchar[];
     v_id_gestion_sg varchar[];
     v_id_partida  integer;
     v_id_obligacion_pago_extendida integer;
     v_registros_op_ori record;
     v_saldo_x_pagar numeric;
     v_registros_pp_origen record;

     v_id_estado_wf_pp  VARCHAR[];
     v_id_proceso_wf_pp varchar[];
     v_id_plan_pago_pp varchar[];
     v_id_estado_actual_pp integer;
     v_id_tipo_estado_pp integer;
     v_monto_ajuste_ret_garantia_ga  numeric;
     v_monto_ajuste_ret_anticipo_par_ga  numeric;
     v_resp_fin varchar[];
     v_preguntar varchar;
     v_id_funcionario_sol integer;

     va_id_presupuesto 			integer[];
     va_id_partida 	    		integer[];
     va_momento					INTEGER[];
     va_monto          			numeric[];
     va_id_moneda    			integer[];
     va_id_partida_ejecucion	integer[];
     va_columna_relacion   		varchar[];
     va_fk_llave             	integer[];
     va_id_obligacion_det	  	integer[];
     va_fecha 					date[];
     v_fecha     				date;
     va_id_obligacion_det_tmp   integer[];
     va_revertir  				numeric[];
     v_tam        				integer;
     v_indice 					integer;
     va_resp_ges              	numeric[];
     v_id_contrato				integer;
     v_registros_documento		record;
     v_registros_con			record;
     v_id_documento_wf_op		integer;
     v_id_usuario_reg_op        integer;
     v_habilitar_copia_contrato	boolean;
     v_ano_1 					integer;
     v_ano_2 					integer;
     v_sw_saltar 				boolean;
     v_fecha_op					date;
     va_id_funcionarios			integer[];
     v_id_uo					integer;
     v_codigo_estado_siguiente	varchar;
     v_registros_proc 			record;
     v_codigo_tipo_pro   		varchar;
     v_pre_integrar_presupuestos			varchar;
     va_num_tramite							VARCHAR[];
     v_adq_comprometer_presupuesto			varchar;

     v_id_administrador			integer;
     v_correo_habilitar_pago    boolean;
     v_ultima_cuota				boolean;
     v_id_cotizacion			integer;

     v_id_tipo_proceso			integer;
     v_tipo_anticipo			varchar;

     v_parametros_op      record;
     v_fecha_aux          integer;

     v_aprobado			  varchar;

     --(may) 02-12-2020
     v_id_relacion_proceso_pago			integer;
     v_id_obligacion_pago_secundario	integer;
     v_id_obligacion_pago_original		record;
     v_id_obligacion_det_rel 			integer;
     v_obligacion_pago					record;
	 v_documentos						record;
     v_facturas							record;
     v_registros_pp_json				record;
     v_estado_pago						record;
     v_estado							varchar;
     v_id_plan_pago						integer;
     v_hstore_registros_pp				hstore;
     v_id_obligacion_pago_sg_pp 		varchar[];
     v_id_gestion_sg_pp					varchar[];
     v_prorrateo						record;
     v_id_doc_compra_venta				integer;
     v_num_tramite_sg					varchar;
     v_id_prorrateo						integer;
     v_id_obligacion_det_pro			integer;
     v_id_plan_pago_pro					integer;
     v_montos_devengados_pro			record;
     v_monto_pago_mo_det				numeric;
     v_monto_pago_mb_det				numeric;

     /****************** Obligacion *******************/
     v_depto                            record;
     v_filadd                           varchar;
     v_id_usuario 		                integer;
     v_id_lugar                         integer;

     v_record_json                      jsonb;
     v_record_json_array                jsonb = '[]'::jsonb;
     v_id_concepto_ingas                integer;
     v_id_centro_costo                  integer;
     v_id_cargo                         integer;
     v_presupuestos                     record;
     v_funcionario                      record;
     v_total_pago                       numeric=0;
     v_saldo_presupuesto                record;
     v_partida                          varchar;
     v_monto_ope_adm                    numeric = 0;
     v_tipo_viatico                     varchar;
     v_json_presupuesto                 varchar;
     v_codigo_cc                        varchar='';

     v_status                           varchar;
     v_estados                          record;

     v_relacion_contable                record;

     v_id_planilla_pvr_con_pago         integer;
     v_id_planilla_pvr_sin_pago         integer;
     v_verificar_id                     boolean = false;
     v_verificar_internacional          boolean = true;
     v_verificar_nacional               boolean = true;

     v_id_partida_aux                   integer;
     v_partida_aux                      varchar;

     v_saldo_presupuesto_aux            record;
     /****************** Obligacion *******************/


	/********** plan pago **********/
     v_fecha_tentativa 		date;
     v_num_obliacion_pago 	    varchar;
     v_total_prorrateo           numeric;
     v_monto_ejecutar_total_mo   numeric;
     v_estado_aux			    varchar;
     v_id_depto_lb_pp			integer;
     v_monto_pp					numeric;
     v_id_obligacion_pago_pp		integer;
     v_numero_tramite			varchar;
     vtipo_pp					varchar;
     v_id_moneda					integer;
     v_gestion					integer;

    v_nombre_conexion			 varchar;
    v_res						 boolean;
    v_centro 					 varchar;
    v_sw_retenciones 			 varchar;
    v_verficacion 				 varchar[];

    /********** plan pago **********/
BEGIN

    v_nombre_funcion = 'tes.ft_obligacion_pago_ime';
    v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');
    v_parametros = pxp.f_get_record(p_tabla);
    v_preguntar = 'no';

	/*********************************
 	#TRANSACCION:  'TES_OBPG_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	if(p_transaccion='TES_OBPG_INS')then

        begin

          /*
          --(franklin.espinoza) 02/01/2020 reglas
          v_fecha_aux = date_part('year',v_parametros.fecha);
          if(v_fecha_aux = 2020)then
            raise exception 'ESTIMADO USUARIO, NO ES POSIBLE HACER REGISTROS PARA LA GESTION 2020';
          end if;*/

          --raise 'Estimado Usuario: <br> A partir del Lunes 6 de Enero del 2020 podran crear procesos correspondientes a la gestión';
             v_resp = tes.f_inserta_obligacion_pago(p_administrador, p_id_usuario,hstore(v_parametros));
            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_OBPG_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPG_MOD')then

		begin

           --raise exception 'sss';

            select
               op.id_funcionario,
               op.fecha,
               op.tipo_obligacion,
               op.id_proceso_wf,
               op.tipo_obligacion,
               op.num_tramite
            into
               v_registros
            from tes.tobligacion_pago op
            where  op.id_obligacion_pago = v_parametros.id_obligacion_pago;


            IF  v_parametros.id_funcionario is NULL THEN
               v_id_funcionario_sol = v_registros.id_funcionario;
            ELSE
               v_id_funcionario_sol = v_parametros.id_funcionario;
            END IF;

            --   TODO
            --25-08-2021 (may) modificacion a solicitud de Marcelo Vidaurre para funcionario GERENTE segun la Matriz
            /*--   revisa tabla de expcecion por concepto de gasto
            --  algunos concepto de gasto solo los pueden aprobar ciertas gerencias ....

          IF  v_id_funcionario_sol is not NULL  THEN

                 --OJO  si el funcionario que solicita es un gerente .... es el mimso encargado de aprobar
                 IF exists(select 1 from orga.tuo_funcionario uof
                           inner join orga.tuo uo on uo.id_uo = uof.id_uo and uo.estado_reg = 'activo'
                           inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional and no.numero_nivel in (1)
                           where  uof.estado_reg = 'activo' and  uof.id_funcionario = v_id_funcionario_sol ) THEN

                      va_id_funcionario_gerente[1] = v_id_funcionario_sol;

                 ELSE
                    --si tiene funcionario identificar el gerente correspondientes
                     SELECT
                           pxp.aggarray(id_funcionario)
                       into
                           va_id_funcionario_gerente
                    -- FROM orga.f_get_aprobadores_x_funcionario(v_registros.fecha,  v_id_funcionario_sol , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);
                    --recuperar el funcionario_gerente actual
                        FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_id_funcionario_sol , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

                        --NOTA el valor en la primera posicion del array es el genre de menor nivel

                END IF;

               -----------------------------
               -- verificar exepcione
               -------------------------------

                  SELECT
                    ce.id_uo
                  into
                    v_id_uo
                  FROM tes.tconcepto_excepcion ce
                  where   ce.estado_reg = 'activo'  and
                           ce.id_concepto_ingas in (select
                                                  id_concepto_ingas
                                                 from tes.tobligacion_det od
                                                 where od.id_obligacion_pago = v_parametros.id_obligacion_pago
                                                 and od.estado_reg = 'activo' )  limit 1 OFFSET 0;

                    --si existe una excepcion cambiar el funcionar aprobador

                    IF v_id_uo is NOT NULL THEN
                         --recuperamos el aprobador

                        va_id_funcionarios =  orga.f_get_funcionarios_x_uo(v_id_uo, coalesce(v_fecha_op,now()::date));

                        IF va_id_funcionarios[1] is NULL THEN
                           raise exception 'La UO configurada por excpeción no tiene un funcionario asignado para le fecha de la OP%,%',v_id_uo,v_fecha_op;
                        END IF;

                        va_id_funcionario_gerente[1] = va_id_funcionarios[1];

                    END IF ;



            END IF;
            */

            ---25-08-2021 (may) modificacion asolicitud de Marcelo Vidaurre para funcionario gerente 01-09-2021,
            /* sera segun una matriz y conceptos de gastos para un funcionario aprobador, funcion despues de insertar*/
            --SOLO PARA procesos recurrentes, pagos unicos, PGA, sin imputacion
            --SOLO PARA tramites recurrentes registro uno por uno el detalle

            IF (v_registros.tipo_obligacion in ('pago_directo', 'pago_unico','pago_especial', 'pga') ) THEN

               v_resp = tes.ft_solicitud_obligacion_pago(v_parametros.id_obligacion_pago, p_id_usuario);

            ELSE

                  IF  v_id_funcionario_sol is not NULL  THEN

                       --OJO  si el funcionario que solicita es un gerente .... es el mimso encargado de aprobar
                       IF exists(select 1 from orga.tuo_funcionario uof
                                 inner join orga.tuo uo on uo.id_uo = uof.id_uo and uo.estado_reg = 'activo'
                                 inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional and no.numero_nivel in (1)
                                 where  uof.estado_reg = 'activo' and  uof.id_funcionario = v_id_funcionario_sol ) THEN

                            va_id_funcionario_gerente[1] = v_id_funcionario_sol;

                       ELSE
                          --si tiene funcionario identificar el gerente correspondientes
                           SELECT
                                 pxp.aggarray(id_funcionario)
                             into
                                 va_id_funcionario_gerente
                          -- FROM orga.f_get_aprobadores_x_funcionario(v_registros.fecha,  v_id_funcionario_sol , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);
                          --recuperar el funcionario_gerente actual
                              FROM orga.f_get_aprobadores_x_funcionario(now()::date,  v_id_funcionario_sol , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);

                              --NOTA el valor en la primera posicion del array es el genre de menor nivel

                      END IF;

                     -----------------------------
                     -- verificar exepcione
                     -------------------------------

                        SELECT
                          ce.id_uo
                        into
                          v_id_uo
                        FROM tes.tconcepto_excepcion ce
                        where   ce.estado_reg = 'activo'  and
                                 ce.id_concepto_ingas in (select
                                                        id_concepto_ingas
                                                       from tes.tobligacion_det od
                                                       where od.id_obligacion_pago = v_parametros.id_obligacion_pago
                                                       and od.estado_reg = 'activo' )  limit 1 OFFSET 0;

                          --si existe una excepcion cambiar el funcionar aprobador

                          IF v_id_uo is NOT NULL THEN
                               --recuperamos el aprobador

                              va_id_funcionarios =  orga.f_get_funcionarios_x_uo(v_id_uo, coalesce(v_fecha_op,now()::date));

                              IF va_id_funcionarios[1] is NULL THEN
                                 raise exception 'La UO configurada por excpeción no tiene un funcionario asignado para le fecha de la OP%,%',v_id_uo,v_fecha_op;
                              END IF;

                              va_id_funcionario_gerente[1] = va_id_funcionarios[1];

                          END IF ;

                  END IF;


            END IF;




            IF   pxp.f_existe_parametro(p_tabla,'id_contrato')    THEN
              v_id_contrato = v_parametros.id_contrato;
            END IF;

			--(may)
            IF pxp.f_get_variable_global('ESTACION_inicio') != 'BOL' THEN
            	v_tipo_anticipo = '';
            ELSE
            	v_tipo_anticipo = v_parametros.tipo_anticipo;
            END IF;

            --raise exception 'sss %',va_id_funcionario_gerente[1];

			--Sentencia de la modificacion
			update tes.tobligacion_pago set
              id_proveedor = v_parametros.id_proveedor,
              id_moneda = v_parametros.id_moneda,
              tipo_cambio_conv=v_parametros.tipo_cambio_conv,
              obs = v_parametros.obs,
              --porc_retgar = v_parametros.porc_retgar,
              id_funcionario = v_id_funcionario_sol,
              --porc_anticipo = v_parametros.porc_anticipo,
              id_depto = v_parametros.id_depto,
              fecha_mod = now(),
              id_usuario_mod = p_id_usuario,
              pago_variable = v_parametros.pago_variable,
              total_nro_cuota = v_parametros.total_nro_cuota,
              fecha_pp_ini = v_parametros.fecha_pp_ini,
              rotacion = v_parametros.rotacion,
              id_plantilla = v_parametros.id_plantilla,
              id_usuario_ai = v_parametros._id_usuario_ai,
              usuario_ai = v_parametros._nombre_usuario_ai,
              --tipo_anticipo = v_parametros.tipo_anticipo,
              tipo_anticipo = v_tipo_anticipo,
              --id_funcionario_gerente = va_id_funcionario_gerente[1],
              id_contrato = v_id_contrato

            where id_obligacion_pago = v_parametros.id_obligacion_pago;


            -------------------------------------
            -- COPIA CONTRATOS,  si es un pago recurrente
            -- si viene de adquiscioens y elnumero de de tramite del troato es el mismo que la obligacion no es encesario copiar
            -------------------------------------

            --  Si  la referencia al contrato esta presente ..  copiar el documento de contrato
            IF v_id_contrato  is not  NULL    THEN

                 v_habilitar_copia_contrato = TRUE;

                  --con el ide de contrato obtenet el id_proceso_wf
                   SELECT
                     con.id_proceso_wf,
                     con.numero,
                     con.estado,
                     pwf.nro_tramite
                   INTO
                    v_registros_con
                   FROM leg.tcontrato con
                   INNER JOIN wf.tproceso_wf pwf on pwf.id_proceso_wf = con.id_proceso_wf
                   WHERE con.id_contrato = v_id_contrato;

                 IF  v_registros.tipo_obligacion = 'adquisiciones'  THEN

                       IF v_registros_con.nro_tramite = v_registros.num_tramite THEN
                         v_habilitar_copia_contrato = FALSE;
                       ELSE
                         v_habilitar_copia_contrato = TRUE;
                       END IF;

                 END IF;

                IF v_habilitar_copia_contrato THEN
                      -- con el proceso del contrato buscar el documento con codigo CONTRATO

                      SELECT
                        d.*
                      into
                       v_registros_documento
                      FROM wf.tdocumento_wf d
                      INNER JOIN wf.ttipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                      WHERE td.codigo = 'CONTRATO' and
                            d.id_proceso_wf = v_registros_con.id_proceso_wf;

                        -- copiamos el link de referencia del contrato
                        select
                         dwf.id_documento_wf
                        into
                         v_id_documento_wf_op
                        from wf.tdocumento_wf dwf
                        inner  join  wf.ttipo_documento td on td.id_tipo_documento = dwf.id_tipo_documento
                        where td.codigo = 'CONTRATO'  and dwf.id_proceso_wf = v_registros.id_proceso_wf;

                        UPDATE
                          wf.tdocumento_wf
                        SET
                           id_usuario_mod = p_id_usuario,
                           fecha_mod = now(),
                           chequeado = v_registros_documento.chequeado,
                           url = v_registros_documento.url,
                           extension = v_registros_documento.extension,
                           obs = v_registros_documento.obs,
                           chequeado_fisico = v_registros_documento.chequeado_fisico,
                           id_usuario_upload = v_registros_documento.id_usuario_upload,
                           fecha_upload = v_registros_documento.fecha_upload,
                           id_proceso_wf_ori = v_registros_documento.id_proceso_wf,
                           id_documento_wf_ori = v_registros_documento.id_documento_wf,
                           nro_tramite_ori = v_registros_con.nro_tramite
                        WHERE
                          id_documento_wf = v_id_documento_wf_op;
                  END IF;

            END IF;



			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_OBPG_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBPG_ELI')then

		begin

            -- obtiene datos de la obligacion

                select
                  op.estado,
                  op.id_proceso_wf,
                  op.id_obligacion_pago,
                  op.id_depto,
                  op.id_estado_wf,
                  op.num_tramite
                into v_registros
                 from  tes.tobligacion_pago op
                 where  op.id_obligacion_pago = v_parametros.id_obligacion_pago;


                IF v_registros.estado!='borrador'  THEN

                   raise exception 'Solo se pueden anular obligaciones en estado borrador';

                END IF;

               --recuperamos el id_tipo_proceso en el WF para el estado anulado
               --ya que este es un estado especial que no tiene padres definidos


               select
               	te.id_tipo_estado
               into
               	v_id_tipo_estado
               from wf.tproceso_wf pw
               inner join wf.ttipo_proceso tp on pw.id_tipo_proceso = tp.id_tipo_proceso and tp.estado_reg != 'inactivo'
               inner join wf.ttipo_estado te on te.id_tipo_proceso = tp.id_tipo_proceso and te.codigo = 'anulado'
               where pw.id_proceso_wf = v_registros.id_proceso_wf;


               IF v_id_tipo_estado is NULL THEN

                  raise exception 'El estado anulado para la obligacion de pago no esta parametrizado en el workflow';

               END IF;


               -- pasamos la obligacion al estado anulado



               v_id_estado_actual =  wf.f_registra_estado_wf(v_id_tipo_estado,
                                                           NULL,
                                                           v_registros.id_estado_wf,
                                                           v_registros.id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
            											   v_parametros._nombre_usuario_ai,
                                                           v_registros.id_depto,
                                                           'Obligacion de Pago Anulada');


               -- actualiza estado en la cotizacion

               update tes.tobligacion_pago  op set
                 id_estado_wf =  v_id_estado_actual,
                 estado = 'anulado',
                 id_usuario_mod=p_id_usuario,
                 fecha_mod=now(),
                 estado_reg='inactivo',
                 id_usuario_ai = v_parametros._id_usuario_ai,
                 usuario_ai = v_parametros._nombre_usuario_ai
               where op.id_obligacion_pago  = v_parametros.id_obligacion_pago;


               --inactiva el datalle de la solicitud
               update tes.tobligacion_det od set
                estado_reg= 'inactivo',
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai
               where  od.id_obligacion_pago = v_parametros.id_obligacion_pago;


               ----------------------------------------------------------------
               ---si esta integrado con adquisiciones libera la cotizacion ----
               -----------------------------------------------------------------

                IF  exists (select 1
                            from adq.tcotizacion cot
                            where cot.id_obligacion_pago = v_parametros.id_obligacion_pago)  THEN


                         -- retroceder el estado de la cotizacion

                          Select
                          c.id_cotizacion,
                          c.id_proceso_wf,
                          c.id_estado_wf,
                          c.estado
                          into
                          v_registros

                          from adq.tcotizacion c
                          where c.id_obligacion_pago = v_parametros.id_obligacion_pago;

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
                            FROM wf.f_obtener_estado_ant_log_wf(v_registros.id_estado_wf);




                          -- registra nuevo estado

                          v_id_estado_actual = wf.f_registra_estado_wf(
                              v_id_tipo_estado,
                              v_id_funcionario,
                              v_registros.id_estado_wf,
                              v_registros.id_proceso_wf,
                              p_id_usuario,
                              v_parametros._id_usuario_ai,
                              v_parametros._nombre_usuario_ai,
                              v_id_depto,
                              'El estado  retrocede por anulacion de la obligacion en tesoreria');



                            -- actualiza estado en la solicitud
                            update adq.tcotizacion  s set
                               id_estado_wf =  v_id_estado_actual,
                               estado = v_codigo_estado,
                               id_usuario_mod=p_id_usuario,
                               fecha_mod=now(),
                               id_obligacion_pago = NULL
                             where id_cotizacion = v_registros.id_cotizacion;

                           --romper relacion con obligacion det
                           update adq.tcotizacion_det  s set
                                   id_obligacion_det = NULL
                           where id_cotizacion = v_registros.id_cotizacion;

                  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago eliminado(a), y cotizacion retrocedida');

                --ELSE
                --  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago eliminado(a)');
                END IF;

               ----------------------------------------------------------------
               --28-12-2021 (may)
               ---si esta integrado con Gestion de Materiales libera COmpra
               -----------------------------------------------------------------

               IF  exists (select 1
                            from mat.tsolicitud sol
                            where sol.id_obligacion_pago = v_parametros.id_obligacion_pago)  THEN


               			 -- retroceder el estado de la cotizacion

                          Select sol.id_solicitud,
                                sol.id_proceso_wf,
                                sol.id_estado_wf,
                                sol.estado
                          into v_registros
                          from mat.tsolicitud sol
                          where sol.id_obligacion_pago = v_parametros.id_obligacion_pago;


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
                            FROM wf.f_obtener_estado_ant_log_wf(v_registros.id_estado_wf);


                          -- registra nuevo estado

                          v_id_estado_actual = wf.f_registra_estado_wf(
                                              v_id_tipo_estado,
                                              v_id_funcionario,
                                              v_registros.id_estado_wf,
                                              v_registros.id_proceso_wf,
                                              p_id_usuario,
                                              v_parametros._id_usuario_ai,
                                              v_parametros._nombre_usuario_ai,
                                              v_id_depto,
                                              'El estado  retrocede por anulacion de la obligacion en tesoreria');



                            -- actualiza estado en la solicitud
                            update mat.tsolicitud sol set
                               id_estado_wf =  v_id_estado_actual,
                               estado = v_codigo_estado,
                               id_usuario_mod=p_id_usuario,
                               fecha_mod=now(),
                               id_obligacion_pago = NULL
                             where sol.id_obligacion_pago = v_parametros.id_obligacion_pago;


                  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago eliminado(a), y GM retrocedida');
               ELSE
                  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago eliminado(a)');
               END IF;

               -----

            --Definicion de la respuesta

            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'TES_FINREG_IME'
 	#DESCRIPCION:	Finaliza el registro de obligacion de pago
 	#AUTOR:	    Rensi Arteaga Copari
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_FINREG_IME')then

		begin



        --raise exception '... % ...', v_parametros.id_obligacion_pago;

            v_resp = tes.f_finalizar_obligacion_total(v_parametros.id_obligacion_pago,p_id_usuario,v_parametros._id_usuario_ai,v_parametros._nombre_usuario_ai,v_parametros.forzar_fin);

            --Devuelve la respuesta
            return v_resp;

		end;

     /*********************************
 	#TRANSACCION:  'TES_SIGESTOB_IME'
 	#DESCRIPCION:	cambia al siguiente estado de la obligacion de pago con el wizard del WF (no se considera el estado finalizado)
 	#AUTOR:		RAC
 	#FECHA:		24-06-2015 12:12:51
	***********************************/

	elseif(p_transaccion='TES_SIGESTOB_IME')then
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
           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;
           --Verifica si el tipo de pago es pga para que exiga tipo documento y fecha de pago.
           if (v_tipo_obligacion = 'pga') then
             if(v_fecha_pp_ini is null and v_id_plantilla is null)then
                raise exception 'Estimado usuario, defina el tipo documento y la fecha de pago. ';
             end if;
           end if;
          -------------------------------------------------
          --  Validamos que la solicitud tengan contenido
          --------------------------------------------------

           IF  v_codigo_estado in ('borrador','vbpoa','vbpresupuestos','liberacion' ) THEN
                  --validamos que el detalle tenga por lo menos un item con valor
                   select
                    sum(od.monto_pago_mo)
                   into
                    v_total_detalle
                   from tes.tobligacion_det od
                   where od.id_obligacion_pago = v_parametros.id_obligacion_pago and od.estado_reg ='activo';

                   IF v_total_detalle = 0 or v_total_detalle is null THEN
                      raise exception 'No existe el detalle de obligacion...';
                   END IF;
                  ------------------------------------------------------------
                  --calcula el factor de prorrateo de la obligacion  detalle
                  -----------------------------------------------------------
                  IF (tes.f_calcular_factor_obligacion_det(v_parametros.id_obligacion_pago) != 'exito')  THEN
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
                                                   where od.id_obligacion_pago = v_parametros.id_obligacion_pago
                                                   and od.estado_reg = 'activo' )
                    limit 1 OFFSET 0;

                    --si existe una excepcion cambiar el funcionar aprobador

                    IF v_id_uo is NOT NULL THEN
                         --recuperamos el aprobador

                         va_id_funcionarios =  orga.f_get_funcionarios_x_uo(v_id_uo, v_fecha_op);

                        IF va_id_funcionarios[1] is NULL THEN
                           --raise exception 'La UO configurada por excpeción no tiene un funcionario asignado para le fecha de la OP';
                        END IF;

                        update tes.tobligacion_pago o set
                          id_funcionario_gerente = va_id_funcionarios[1],
                          uo_ex = 'si'
                        where o.id_obligacion_pago = v_parametros.id_obligacion_pago;

                     END IF ;

                    ---25-08-2021 (may) modificacion asolicitud de Marcelo Vidaurre para funcionario gerente desde la fecha 01-09-2021,
                    /* sera segun una matriz y conceptos de gastos para un funcionario aprobador, funcion despues de insertar*/
                    --SOLO PARA procesos recurrentes, pagos unicos, PGA, sin imputacion


                    --SOLO PARA tramites recurrentes registro uno por uno el detalle
                    IF (v_tipo_obligacion in ('pago_directo', 'pago_unico','pago_especial', 'pga') ) THEN

                       v_resp = tes.ft_solicitud_obligacion_pago(v_parametros.id_obligacion_pago, p_id_usuario);

                    END IF;


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
          where ew.id_estado_wf = v_parametros.id_estado_wf_act;



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

           ---------------------------------------
           -- REGISTA EL SIGUIENTE ESTADO DEL WF.
           ---------------------------------------

           v_id_estado_actual =  wf.f_registra_estado_wf(  v_parametros.id_tipo_estado,
                                                           v_parametros.id_funcionario_wf,
                                                           v_parametros.id_estado_wf_act,
                                                           v_id_proceso_wf,
                                                           p_id_usuario,
                                                           v_parametros._id_usuario_ai,
                                                           v_parametros._nombre_usuario_ai,
                                                           v_id_depto,
                                                           v_obs);

          --------------------------------------
          -- registra los procesos disparados
          --------------------------------------

          FOR v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) LOOP

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
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
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
                  obs_presupuestos = v_parametros.obs
                 where id_obligacion_pago  = v_parametros.id_obligacion_pago;
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
             id_usuario_ai = v_parametros._id_usuario_ai,
             usuario_ai = v_parametros._nombre_usuario_ai
           where id_obligacion_pago  = v_parametros.id_obligacion_pago;



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
                  where op.id_obligacion_pago_extendida = v_parametros.id_obligacion_pago;

                 --  si es una obligacion de pago extendida

                 IF v_registros_op_ori is not NULL THEN

                        -- Chequear si la obligacion original tiene un saldo anticipado
                        v_saldo_x_pagar = 0;
                        --v_saldo_x_pagar = tes.f_determinar_total_faltante(v_registros_op_ori.id_obligacion_pago,'anticipo_sin_aplicar');



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
                                                  'id_obligacion_pago', v_parametros.id_obligacion_pago::varchar,
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
                                                  '_id_usuario_ai', v_parametros._id_usuario_ai::varchar,
                                                  '_nombre_usuario_ai', v_parametros._nombre_usuario_ai::varchar,
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
                                                                           v_parametros._id_usuario_ai,
                                                                           v_parametros._nombre_usuario_ai,
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
                       where id_obligacion_pago  = v_parametros.id_obligacion_pago;

                 END IF;

            END IF;

          -------------------------------------------
          --  VERIFICA SI ES NECESARIO UN CONTRATO
          -----------------------------------------
           IF  v_codigo_estado = 'borrador'  THEN

                 IF not tes.f_validar_contrato(v_parametros.id_obligacion_pago) THEN
                   raise exception 'contrato no validao';
                 END IF;

           END IF;

          --------------------------------------------------
          --  INSERCION AUTOMATICA DE CUOTAS
          --------------------------------------------------

          --  TODO considerar el saldo de anticipo, menos  el total a pagar para determinar el monto, considerar numero de cuota
          --  si llegando al estado registrado,  verifica el total de cuotas y las inserta con la plantilla por defecto

          IF  v_codigo_estado_siguiente = 'registrado'  and v_total_nro_cuota > 0 THEN

           --enviamos correo al area solicintante para indicar que se habilito su pago
          if(v_tipo_obligacion = 'adquisiciones') then
            select tc.id_cotizacion
            into v_id_cotizacion
            from adq.tcotizacion tc
            where tc.id_obligacion_pago = v_parametros.id_obligacion_pago;
            --raise exception 'v_id_cotizacion :%, %', v_tipo_obligacion, v_parametros.id_obligacion_pago;
            v_correo_habilitar_pago = adq.f_correo_habilitar_pago(v_id_cotizacion, p_id_usuario);
          end if;
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
                            v_ultima_cuota = true;
                         END IF;

                         v_descuentos_ley = v_monto_cuota * v_registros_plan.ps_descuento_porc;



						--(may)tipo de obligacion SIP para  internacionales pago_especial_spi
                        --pago para bol pago_especial

                         IF v_tipo_obligacion in  ('pago_especial') THEN
                            v_tipo_plan_pago = 'especial';
                         ELSIF v_tipo_obligacion in  ('pago_especial_spi') THEN
                            v_tipo_plan_pago = 'especial_spi';
                         ELSE


                           --verifica que tipo de apgo estan deshabilitados

                           va_tipo_pago = regexp_split_to_array(pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'), E'\\s+');

                           v_tipo_plan_pago = 'devengado_pagado';

                           IF v_tipo_plan_pago =ANY(va_tipo_pago) THEN
                                  v_tipo_plan_pago = 'devengado_pagado_1c';
                           END IF;

                           -- para los pagos internacionales que solo es devengado_pagado_1c
                           --IF v_tipo_obligacion in  ('spd', 'pago_especial_spi') THEN
                           --	v_tipo_plan_pago = 'devengado_pagado_1c';
                           --END IF;
						   --
                           IF v_tipo_obligacion in  ('spd', 'pgaext') THEN
                           		v_tipo_plan_pago = 'devengado_pagado_1c_sp';
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
                                                        'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar,
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
                                                        '_id_usuario_ai',v_parametros._id_usuario_ai::varchar,
                                                        '_nombre_usuario_ai', v_parametros._nombre_usuario_ai::varchar/*,
                                                        'ultima_cuota',v_ultima_cuota::varchar*/
                                                       ]);

                            --TODO,  bloquear en formulario de OP  facturas con monto excento


                            -- si es un proceso de pago unico,  la primera cuota pasa de borrador al siguiente estado de manera automatica
                            IF  ((v_tipo_obligacion = 'pbr' or v_tipo_obligacion = 'ppm' or v_tipo_obligacion = 'pga' or v_tipo_obligacion = 'pce' or v_tipo_obligacion = 'pago_unico' or v_tipo_obligacion = 'spd' or v_tipo_obligacion ='pgaext') and   v_i = 1)   THEN
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

		--tipo de obligacion SIP para  internacionales pago_especial_spi
           IF  v_codigo_estado = 'borrador'   and v_tipo_obligacion != 'adquisiciones' and v_tipo_obligacion != 'pago_especial' and v_tipo_obligacion != 'pago_especial_spi' and   v_pre_integrar_presupuestos = 'true'  THEN

               --si es borrador verificamos que el presupeusto sea suficiente para proseguir con la ordenç
               IF not tes.f_gestionar_presupuesto_tesoreria(v_parametros.id_obligacion_pago, p_id_usuario, 'verificar')  THEN
                   raise exception 'Error al verificar  presupeusto';
               END IF;

           END IF;


          -----------------------------------------------------------------------------
          -- COMPROMISO PRESUPUESTARIO
          -- cuando pasa al estado registrado y el presupeusto no esta comprometido
          ------------------------------------------------------------------------------


          IF       v_codigo_estado_siguiente = 'registrado'
              and  v_comprometido = 'no'
              and v_tipo_obligacion != 'adquisiciones'
              and  v_tipo_obligacion != 'pago_especial'
              --tipo de obligacion SIP para  internacionales pago_especial_spi
              and  v_tipo_obligacion != 'pago_especial_spi'
              and  v_pre_integrar_presupuestos = 'true'  THEN

                      --jrr: llamamos a la funcion que revierte de planillas en caso de que sea de recursos humanos
                      if (v_tipo_obligacion = 'rrhh') then

                           IF NOT plani.f_generar_pago_tesoreria(p_administrador,p_id_usuario,v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai,v_parametros.id_obligacion_pago,v_obs) THEN
                               raise exception 'Error al generar el pago de devengado';
                            END IF;
                      end if;
                     --TODO aumentar capacidad de rollback
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
           --tipo de obligacion SIP para  internacionales pago_especial_spi
           IF v_codigo_estado_siguiente = 'registrado'  and  v_comprometido = 'no' and v_tipo_obligacion in  ('adquisiciones','pago_especial', 'pago_especial_spi') THEN
               v_comprometido = 'si';
               --cambia la bandera del comprometido
               update tes.tobligacion_pago  set
                 comprometido = v_comprometido
               where id_obligacion_pago  = v_parametros.id_obligacion_pago;
           END IF;

          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del plan de pagos)');
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

     end;



    /*********************************
 	#TRANSACCION:  'TES_ANTEOB_IME'
 	#DESCRIPCION:	Retrocede estado de la obligacion
 	#AUTOR:	        Rensi Arteaga Copari
 	#FECHA:		02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_ANTEOB_IME')then

		begin




        --recupera parametros
            select
            op.id_estado_wf,
            op.id_proceso_wf,
            op.estado,
            op.comprometido,
            op.tipo_obligacion,
            pwf.id_tipo_proceso

            into
            v_id_estado_wf,
            v_id_proceso_wf,
            v_codigo_estado_ant,
            v_comprometido,
            v_tipo_obligacion,
            v_id_tipo_proceso

            from tes.tobligacion_pago op
            inner join wf.tproceso_wf pwf  on  pwf.id_proceso_wf = op.id_proceso_wf
            where op.id_obligacion_pago = v_parametros.id_obligacion_pago;



        --------------------------------------------------
        --Retrocede al estado inmediatamente anterior
        -------------------------------------------------
         IF  v_parametros.operacion = 'cambiar' or v_parametros.operacion = 'inicio' or v_parametros.operacion = 'anterior' THEN

                       --validaciones

                       IF v_codigo_estado_ant = 'en_pago' THEN
                       --verificar que no tenga plnes de pago

                         IF  EXISTS(select 1
                         from tes.tplan_pago pp
                         where pp.id_obligacion_pago = v_parametros.id_obligacion_pago
                               and pp.estado_reg='activo') THEN

                            raise exception 'Para retroceder no debe tener planes de pago activos';

                         END IF;


                       END IF;



                      --recuperaq estado anterior segun Log del WF
                      if(v_parametros.operacion = 'cambiar' or v_parametros.operacion = 'anterior')then

                          select
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
                          from wf.f_obtener_estado_ant_log_wf(v_id_estado_wf);

                      elsif(v_parametros.operacion = 'inicio')then

                      --FEA 13/3/2017 Funcionalidad que devuelve al estado  borrador la obligacion de pago
                          select
                              ps_id_tipo_estado,
                              ps_codigo_estado
                          into
                              v_id_tipo_estado,
                              v_codigo_estado
                          from wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_id_tipo_proceso);


                          --busca en log e estado de wf que identificamos como el inicial
                          select
                              ps_id_funcionario,
                              ps_id_depto
                          into
                              v_id_funcionario,
                              v_id_depto
                          from wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);
                      end if;


                      -- recupera el proceso_wf

                      /*select
                           ew.id_proceso_wf
                        into
                           v_id_proceso_wf
                      from wf.testado_wf ew
                      where ew.id_estado_wf= v_id_estado_wf_ant;*/

                      v_obs = '';

                      IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                         v_obs = '-'||v_obs||COALESCE(v_parametros.obs,'---');
                      END IF;




                      -- registra nuevo estado

                      v_id_estado_actual = wf.f_registra_estado_wf(
                          v_id_tipo_estado,
                          v_id_funcionario,
                          v_id_estado_wf,
                          v_id_proceso_wf,
                          p_id_usuario,
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          v_id_depto,
                          v_obs);



                      -- actualiza estado en la obligacion
                        update tes.tobligacion_pago  op set
                           id_estado_wf =  v_id_estado_actual,
                           estado = v_codigo_estado,
                           id_usuario_mod=p_id_usuario,
                           fecha_mod=now(),
                           obs = obs --||v_obs
                         where id_obligacion_pago = v_parametros.id_obligacion_pago;


                        -- cuando el estado al que regresa es  borrador o presupeustos esta comprometido y no viene de adquisiciones se revierte el repsupuesto
                        --tipo de obligacion SIP para  internacionales pago_especial_spi
                        --30-12-2021 (may) se aumenta a proceso de GM gestion_mat para que no revierta el presupuesto (igual que adq)
         			     IF (v_codigo_estado = 'borrador' or v_codigo_estado = 'vbpresupuestos') and v_comprometido = 'si' and   v_tipo_obligacion !='adquisiciones' and   v_tipo_obligacion !='pago_especial' and v_tipo_obligacion !='pago_especial_spi' and v_tipo_obligacion != 'gestion_mat' and v_pre_integrar_presupuestos = 'true'  THEN

                             --se revierte el presupeusto
                             IF not tes.f_gestionar_presupuesto_tesoreria(v_parametros.id_obligacion_pago, p_id_usuario, 'revertir')  THEN
                                  raise exception 'Error al revertir el presupeusto';
                             END IF;

                            --se modifica la bandera del comprometido
                            update tes.tobligacion_pago op set
                              comprometido = 'no'
                            where id_obligacion_pago = v_parametros.id_obligacion_pago;

                         END IF;


                         --RAC 02/08/2017
                         --verifica si el presupeusto fue comprometido en adquisicioens o no
                         --30-12-2021 (may) se aumenta a proceso de GM gestion_mat para que no revierta el presupuesto (igual que adq)

                         v_adq_comprometer_presupuesto = pxp.f_get_variable_global('adq_comprometer_presupuesto');

                          IF ( v_codigo_estado = 'borrador' or v_codigo_estado = 'vbpresupuestos')
                             and v_comprometido = 'si'
                             and  v_tipo_obligacion in ('adquisiciones', 'gestion_mat')
                             and  v_adq_comprometer_presupuesto = 'no'
                             and   v_pre_integrar_presupuestos = 'true'  THEN

                             --se revierte el presupeusto
                             IF not tes.f_gestionar_presupuesto_tesoreria(v_parametros.id_obligacion_pago, p_id_usuario, 'revertir')  THEN
                                 raise exception 'Error al revertir el presupeusto';
                             END IF;

                            --se modifica la bandera del comprometido
                            update tes.tobligacion_pago op set
                              comprometido = 'no'
                            where id_obligacion_pago = v_parametros.id_obligacion_pago;

                         END IF;





                          IF v_codigo_estado = 'borrador' THEN

                              --se modifica la bandera del comprometido
                              update tes.tobligacion_pago op set
                                 total_pago=NULL
                              where id_obligacion_pago = v_parametros.id_obligacion_pago;

                              --jrr: llamamos a la funcion que revierte de planillas en caso de que sea de recursos humanos
                              if (v_tipo_obligacion = 'rrhh') then
                                  IF NOT plani.f_anular_obligacion_tesoreria(p_id_usuario,v_parametros._id_usuario_ai,
                                            v_parametros._nombre_usuario_ai,v_parametros.id_obligacion_pago,v_obs) THEN
                                       raise exception 'Error al anular la obligacion';
                                    END IF;
                              end if;

                          END IF;


                        -- si hay mas de un estado disponible  preguntamos al usuario
                        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado)');
                        v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


                      --Devuelve la respuesta
                        return v_resp;


           ELSE

                   raise exception 'Operacion no implementada';
           END IF;



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Retrocede estado de la obligacion');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'TES_PAFPP_IME'
 	#DESCRIPCION:	Calcula el restante por registrar, devengar o pagar  segun filtro
 	#AUTOR:		admin
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_PAFPP_IME')then

		begin

            v_ope_filtro = regexp_split_to_array(v_parametros.ope_filtro,',');

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','determina cuanto falta por pgar');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

            v_sw = TRUE;





            FOR v_ind IN array_lower(v_ope_filtro, 1) .. array_upper(v_ope_filtro, 1)
            LOOP

                v_monto_total= tes.f_determinar_total_faltante(v_parametros.id_obligacion_pago, v_ope_filtro[v_ind], v_parametros.id_plan_pago);

              IF v_sw THEN
                v_resp = pxp.f_agrega_clave(v_resp,'monto_total_faltante',v_monto_total::varchar);
                v_sw = FALSE;
              ELSE
                v_resp = pxp.f_agrega_clave(v_resp,v_ope_filtro[v_ind],v_monto_total::varchar);
              END IF;

            END LOOP;



            --Devuelve la respuesta
            return v_resp;

		end;
	 /*********************************
 	#TRANSACCION:  'TES_OBEPUO_IME'
 	#DESCRIPCION:	Obtener listado de up y ep correspondientes a los centros de costo
                    del detalle de la obligacion de pago

 	#AUTOR:	        Rensi Arteaga Copari
 	#FECHA:		    1-4-2013 14:48:35
	***********************************/

	elsif(p_transaccion='TES_OBEPUO_IME')then

		begin



            select
              pxp.list(cc.id_uo::text),
              pxp.list(cc.id_ep::text)
            into
              v_cad_uo,
              v_cad_ep
            from tes.tobligacion_det  od
            inner join param.tcentro_costo cc on od.id_centro_costo = cc.id_centro_costo
            where od.id_obligacion_pago = v_parametros.id_obligacion_pago
            and od.estado_reg = 'activo';


             --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','UOs, EPs retornados');
            v_resp = pxp.f_agrega_clave(v_resp,'eps',v_cad_ep::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'uos',v_cad_uo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_IDSEXT_GET'
 	#DESCRIPCION:	Devuelve los IDS de otros sistemas (adquisiciones, etc.) a partir de la obligacion de pago
 	#AUTOR:			RCM
 	#FECHA:			02-04-2013 16:01:32
	***********************************/

	elsif(p_transaccion='TES_IDSEXT_GET')then

		begin

			--1.Verificar existencia de la obligación de pago
			if not exists(select 1 from tes.tobligacion_pago
						where id_obligacion_pago = v_parametros.id_obligacion_pago) then
				raise exception 'Obligación de pago inexistente';
			end if;

			--2.Condicional por sistema
			if v_parametros.sistema = 'ADQ' then

				--2.1 Obtiene los IDS: id_cotizacion, id_proceso_compra, id_solicitud
				select
				cot.id_cotizacion, cot.id_proceso_compra, pro.id_solicitud
				into v_registros
				from adq.tcotizacion cot
				inner join adq.tproceso_compra pro on pro.id_proceso_compra = cot.id_proceso_compra
				where cot.id_obligacion_pago = v_parametros.id_obligacion_pago;


				--2.2 Respuesta por sistema
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','IDs obtenidos');
				v_resp = pxp.f_agrega_clave(v_resp,'id_cotizacion',v_registros.id_cotizacion::varchar);
				v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_compra',v_registros.id_proceso_compra::varchar);
				v_resp = pxp.f_agrega_clave(v_resp,'id_solicitud',v_registros.id_solicitud::varchar);


			elsif v_parametros.sistema = 'TES' then
				--(17/12/2013)TODO implementar cuando corresponda
				raise exception 'Funcionalidad no implementada para el sistema %',v_parametros.sistema;

				--2.1 Obtiene los IDS
				--2.2 Respuesta por sistema
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','IDs obtenidos');


			elsif v_parametros.sistema = 'CONTA' then
				--(17/12/2013)TODO implementar si corresponde
				raise exception 'Funcionalidad no implementada para el sistema %',v_parametros.sistema;

				--2.1 Obtiene los IDS
				--2.2 Respuesta por sistema
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','IDs obtenidos');


			else
				raise exception 'Sistema no reconocido';
			end if;

			--3.Respuesta
			return v_resp;

		end;

   /*********************************
 	#TRANSACCION:  'TES_OBLAJUS_IME'
 	#DESCRIPCION:	Inserta ajustes en la obligacion de pagos variables, segun el tipo ajustes para anticipos totales o resevar paga anticipos a aplicar la siguiente gestion
 	#AUTOR:	    Rensi Arteaga Copari (KPLIAN)
 	#FECHA:		23-10-2014 16:01:32
	***********************************/

	elsif(p_transaccion='TES_OBLAJUS_IME')then

		begin
			select
            op.estado,
            op.pago_variable,
            op.monto_estimado_sg,
            op.id_obligacion_pago_extendida
            into
            v_registros
            from tes.tobligacion_pago op
            where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

          IF v_parametros.tipo_ajuste = 'ajuste' THEN



                IF v_registros.pago_variable = 'no' THEN
                  raise exception 'Solo puede insertar ajustes en pagos variables';
                END IF;

                IF v_registros.estado != 'en_pago' THEN
                  raise exception 'Solo puede insertar ajustes cuando la obligacion este en estado: en_pago';
                END IF;


                --Sentencia de la modificacion
                update tes.tobligacion_pago  set
                ajuste_aplicado = v_parametros.ajuste_aplicado,
                ajuste_anticipo = v_parametros.ajuste_anticipo,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now()
                where id_obligacion_pago=v_parametros.id_obligacion_pago;

                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se insertaron ajustes a la obligacion de pago');
                v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


          ELSE

                --revision de anticipo
                IF v_registros.id_obligacion_pago_extendida is not NULL THEN
                  raise exception 'No puede modificar para obligaciones extendidas';
                END IF;

                IF v_registros.estado   in ('finalizado','anulado') THEN
                  raise exception 'No puede modificar en obligaciones finalizadas o anuladas';
                END IF;

                --suma los montos a ejecutar y anticipar antes de la edicion
               IF  v_parametros.monto_estimado_sg  < 0 THEN
                     raise exception 'El monto de ampliación no puede ser menor que cero';
               END IF;



                --  Sentencia de la modificacion
                update tes.tobligacion_pago  set
                monto_estimado_sg = v_parametros.monto_estimado_sg,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now()
                where id_obligacion_pago=v_parametros.id_obligacion_pago;

                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se cambio el monto  previsto para llevar al gasto la siguiente gestion (anticipos)');
                v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);



          END IF;

          --Devuelve la respuesta
          return v_resp;

		end;
   /*********************************
 	#TRANSACCION:  'TES_EXTOP_IME'
 	#DESCRIPCION:  Extiende la obligacion de pago para la gestion siguiente
 	#AUTOR:	    Rensi Arteaga Copari (KPLIAN)
 	#FECHA:		31-10-2014 16:01:32
	***********************************/

	elsif(p_transaccion='TES_EXTOP_IME')then

		begin

            --------------------------------------
            -- verificar que no este extendida
            --------------------------------------

            Select
            *
            into
            v_registros
            from tes.tobligacion_pago op
            where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

            --validar que el estado de la obligacion sea finaliza
            IF v_registros.estado not in ('registrado','en_pago','finalizado','finalizado') THEN
               raise exception 'No se permiten obligaciones de pago que no esten finalizadas';
            END IF;

            --validar que no tenga extenciones
            IF v_registros.id_obligacion_pago_extendida is not null THEN
              raise exception 'la obligacion de pago ya fue extendida';
            END IF;

           ------------------------------
           -- copiar obligacion de pago
           ------------------------------

            v_date = now()::Date;
            v_anho = (date_part('year', v_registros.fecha))::integer;
            v_anho = v_anho  + 1;

            /*IF (v_anho||'-1-1')::date > v_date THEN
               v_date = (v_anho::varchar||'-1-1')::Date;
            END  IF;*/
            if(v_anho>date_part('year', v_date)) then
            	v_date = (date_part('year', v_date)::varchar||'-1-1')::Date;
            else
            	v_date = (v_anho::varchar||'-1-1')::Date;
            end if;

            v_hstore_registros =   hstore(ARRAY[
                                             'fecha',v_date::varchar,
                                             'tipo_obligacion', 'pago_directo',
                                             'id_funcionario',v_registros.id_funcionario::varchar,
                                             '_id_usuario_ai',v_parametros._id_usuario_ai::varchar,
                                             '_nombre_usuario_ai',v_parametros._nombre_usuario_ai::varchar,
                                             'id_depto',v_registros.id_depto::varchar,
                                             'obs','Extiende el tramite: '||v_registros.num_tramite||',  Obs:  '||v_registros.obs,
                                             'id_proveedor',v_registros.id_proveedor::varchar,
                                             'tipo_obligacion',v_registros.tipo_obligacion::varchar,
                                             'id_moneda',v_registros.id_moneda::varchar,
                                             'tipo_cambio_conv',v_registros.tipo_cambio_conv::varchar,
                                             'pago_variable',v_registros.pago_variable::varchar,
                                             'total_nro_cuota',v_registros.total_nro_cuota::varchar,
                                             'fecha_pp_ini',v_registros.fecha_pp_ini::varchar,
                                             'rotacion',v_registros.rotacion::varchar,
                                             'id_plantilla',v_registros.id_plantilla::varchar,
                                             'tipo_anticipo',v_registros.tipo_anticipo::varchar,
                                             'id_contrato',v_registros.id_contrato::varchar
                                            ]);


            --bandera para ver si es un pago recurrente o unico=1, pga=2.
            if(v_parametros.id_administrador = 2)then
               v_id_administrador = 2;
            else
               v_id_administrador = p_administrador;
            end if;

             v_resp = tes.f_inserta_obligacion_pago(v_id_administrador, p_id_usuario, hstore(v_hstore_registros));
             v_id_obligacion_pago_sg =  pxp.f_recupera_clave(v_resp, 'id_obligacion_pago');
             v_id_gestion_sg =  pxp.f_recupera_clave(v_resp, 'id_gestion');



             --------------------------------------------------------------------------------------------
             -- copiar detalle de obligacion , verifican la tabla id_presupuestos_ids si existe se copia...
             ------------------------------------------------------------------------------------------------
              FOR  v_registros_det in (
                                      SELECT
                                          od.id_obligacion_det,
                                          od.id_concepto_ingas,
                                          od.id_centro_costo,
                                          od.id_partida,
                                          od.descripcion,
                                          od.monto_pago_mo ,
                                          od.id_orden_trabajo,
                                          od.monto_pago_mb
                                        FROM  tes.tobligacion_det od
                                        where  od.estado_reg = 'activo' and
                                               od.id_obligacion_pago = v_parametros.id_obligacion_pago) LOOP


                    --recueprar centro de cotos para la siguiente gestion  (los centro de cosots y presupeustos tiene los mismo IDS)


                      select
                        pi.id_presupuesto_dos
                      into
                        v_id_centro_costo_dos
                      from pre.tpresupuesto_ids pi
                      where pi.id_presupuesto_uno = v_registros_det.id_centro_costo;

                      IF v_id_centro_costo_dos is not NULL THEN

                              SELECT
                                  ps_id_partida
                                into
                                  v_id_partida
                              FROM conta.f_get_config_relacion_contable('CUECOMP', v_id_gestion_sg[1]::integer, v_registros_det.id_concepto_ingas, v_id_centro_costo_dos);


                              --Sentencia de la insercion
                              insert into tes.tobligacion_det(
                                estado_reg,
                                --id_cuenta,
                                id_partida,
                                --id_auxiliar,
                                id_concepto_ingas,
                                monto_pago_mo,
                                id_obligacion_pago,
                                id_centro_costo,
                                monto_pago_mb,
                                descripcion,
                                fecha_reg,
                                id_usuario_reg,
                                fecha_mod,
                                id_usuario_mod,
                                id_orden_trabajo
                              )
                              values
                              (
                                'activo',
                                --v_parametros.id_cuenta,
                                v_id_partida,
                                --v_parametros.id_auxiliar,
                                v_registros_det.id_concepto_ingas,
                                v_registros_det.monto_pago_mo,
                                v_id_obligacion_pago_sg[1]::integer,
                                v_id_centro_costo_dos,
                                v_registros_det.monto_pago_mb,
                                v_registros_det.descripcion,
                                now(),
                                p_id_usuario,
                                null,
                                null,
                                v_registros_det.id_orden_trabajo

                              )RETURNING id_obligacion_det into v_id_obligacion_det;

                      END IF;

              END LOOP;

            --actualiza obligacion extendida en la original

            update tes.tobligacion_pago set
                id_obligacion_pago_extendida = v_id_obligacion_pago_sg[1]::integer,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now()
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            -- Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se extendio la obligacion de pago a la siguiente gestion');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


          --Devuelve la respuesta
          return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'TES_REVPARPRE_IME'
 	#DESCRIPCION:	Revierte el presupeusto parcialmente
 	#AUTOR:		RAC - KPLIAN
 	#FECHA:		10-04-2013 15:43:23
	***********************************/

	elsif(p_transaccion='TES_REVPARPRE_IME')then

		begin

           v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');

            select
               op.id_obligacion_pago,
               op.id_moneda,
               op.estado,
               op.fecha,
               op.num_tramite,
               op.tipo_cambio_conv

            into
               v_registros_op
            from tes.tobligacion_pago op
            where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

            IF v_registros_op.estado = 'finalizado' THEN
               raise exception 'no puede modificar el presupuesto de obligaciones finalizadas';
            END IF;

            --17-12-2020(may) modificacion aumentando que los de tipo Pagar no ingrese a la condicion
            --validar que no tenga comprobantes  pendientes sin validar
            IF exists( select 1
                      from tes.tplan_pago pp
                      where pp.id_obligacion_pago  = v_parametros.id_obligacion_pago and pp.estado_reg ='activo' and pp.estado = 'pendiente'
                      and pp.tipo != 'pagado'
                      ) THEN

                 --raise exception 'Tiene algun comprobnate pendiente de valiación, eliminelo o validaelo antes de volver a intentar';
                 --17-12-2020 (may) modificacion raise
                 raise exception 'Tiene algun comprobante pendiente de validación, elimine o valide antes de volver a intentar.';

             END IF;



            -- la fecha de solictud es la fecha de compromiso
            IF  now()  < v_registros_op.fecha THEN
                v_fecha = v_registros_op.fecha::date;
            ELSE
                 -- la fecha de reversion como maximo puede ser el 31 de diciembre
                 v_fecha = now()::date;
                 v_ano_1 =  EXTRACT(YEAR FROM  now()::date);
                 v_ano_2 =  EXTRACT(YEAR FROM  v_registros_op.fecha::date);

                 IF  v_ano_1  >  v_ano_2 THEN
                   v_fecha = ('31-12-'|| v_ano_2::varchar)::date;
                 END IF;
            END IF;

            va_id_obligacion_det_tmp =  string_to_array(v_parametros.id_ob_dets::text,',')::integer[];
            va_revertir = string_to_array(v_parametros.revertir::text,',')::numeric[];
            v_tam = array_length(va_id_obligacion_det_tmp, 1);

            v_i = 1;
            FOR v_registros in (
                            SELECT  od.id_obligacion_det,
                                    od.id_centro_costo,
                                    od.id_partida_ejecucion_com,
                                    od.id_partida,
                                    p.id_presupuesto
                            FROM  tes.tobligacion_det od
                            INNER JOIN pre.tpresupuesto   p  on p.id_centro_costo = od.id_centro_costo
                            WHERE od.id_obligacion_det = ANY(va_id_obligacion_det_tmp)
                         ) LOOP


                va_id_presupuesto[v_i] = v_registros.id_presupuesto;
                va_id_partida[v_i] = v_registros.id_partida;
                va_momento[v_i]	= 2; --el momento 2 con signo negativo  es revertir
                va_id_moneda[v_i]  = v_registros_op.id_moneda;


                va_id_partida_ejecucion[v_i] = v_registros.id_partida_ejecucion_com;
                va_columna_relacion[v_i] = 'id_obligacion_pago';
                va_fk_llave[v_i] = v_registros_op.id_obligacion_pago;
                va_fecha[v_i] = v_fecha ;
                va_id_obligacion_det[v_i] = v_registros.id_obligacion_det;
                va_num_tramite[v_i] =  v_registros_op.num_tramite;
                v_indice = v_i;

                FOR v_j IN 1..v_tam LOOP
                   IF v_registros.id_obligacion_det = va_id_obligacion_det_tmp[v_j] THEN
                       v_indice = v_j;
                       v_j = v_tam + 1;
                   END IF;
                END LOOP;

                va_monto[v_i]  = va_revertir[v_indice]*-1;

                v_i = v_i + 1;

          END LOOP;



          --si se integra con presupuestos
          IF v_pre_integrar_presupuestos = 'true' THEN

            va_resp_ges =  pre.f_gestionar_presupuesto(  p_id_usuario ,
            											 v_registros_op.tipo_cambio_conv, -- tipo de cambio

               											 va_id_presupuesto,
                                                         va_id_partida,
                                                         va_id_moneda,
                                                         va_monto,
                                                         va_fecha, --p_fecha
                                                         va_momento,
                                                         va_id_partida_ejecucion,--  p_id_partida_ejecucion
                                                         va_columna_relacion,
                                                         va_fk_llave,
                                                         va_num_tramite
                                                         );





          END IF;
            -- Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se extendio la obligacion de pago a la siguiente gestion');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'TES_OBSPRE_MOD'
 	#DESCRIPCION:	Modificar las observaciones de presupeustos

 	#AUTOR:	        Rensi Arteaga Copari
 	#FECHA:		    1-4-2015 14:48:35
	***********************************/

	elsif(p_transaccion='TES_OBSPRE_MOD')then

		begin

             update tes.tobligacion_pago set
              obs_presupuestos = v_parametros.obs,
              fecha_certificacion_pres = v_parametros.fecha_cer_pres
             where id_obligacion_pago = v_parametros.id_obligacion_pago;

             --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','UOs, EPs retornados');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;
     /*********************************
 	#TRANSACCION:  'TES_OBSPOA_MOD'
 	#DESCRIPCION:	Modificar las observaciones del área de POA

 	#AUTOR:	        Rensi Arteaga Copari
 	#FECHA:		    1-4-2015 14:48:35
	***********************************/

	elsif(p_transaccion='TES_OBSPOA_MOD')then

		begin


             update tes.tobligacion_pago set
              obs_poa = v_parametros.obs_poa,
              codigo_poa = v_parametros.codigo_poa
             where id_obligacion_pago = v_parametros.id_obligacion_pago;

             --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obs poa en obligaciones de pago');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

     /*********************************
 	#TRANSACCION:  'TES_GETFILOP_IME'
 	#DESCRIPCION:	Recupera datos delproveedor, OT , tramite y conceptos de gasto  de la obligacion
    #AUTOR:	        Rensi Arteaga Copari
 	#FECHA:		    29-08-2015 14:48:35
	***********************************/

	elsif(p_transaccion='TES_GETFILOP_IME')then

		begin


           --recupera datos de la OP y proveedor
           select
             op.id_proveedor,
              pr.desc_proveedor,
              op.num_tramite
           into
             v_registros_op
           from tes.tobligacion_pago op
           inner join param.vproveedor pr on pr.id_proveedor = op.id_proveedor
           where op.id_obligacion_pago = v_parametros.id_obligacion_pago;


           --recupera datos del detalle ots y conceptos
           select
              pxp.list(od.id_orden_trabajo::varchar) as id_orden_trabajos,
              pxp.list(od.id_concepto_ingas::varchar) as id_concepto_ingas
           into
             v_registros
           from tes.tobligacion_det od
           where od.id_obligacion_pago = v_parametros.id_obligacion_pago and od.estado_reg = 'activo';



             --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','recupera datos de la obligacion');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_proveedor',v_registros_op.id_proveedor::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'num_tramite',v_registros_op.num_tramite::varchar);


            v_resp = pxp.f_agrega_clave(v_resp,'desc_proveedor',v_registros_op.desc_proveedor::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_orden_trabajos',v_registros.id_orden_trabajos::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_ingas',v_registros.id_concepto_ingas::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
    #TRANSACCION:  'TES_REPOP_IME'
    #DESCRIPCION:	Replicar una Obligacion de Pago
    #AUTOR:		Alan Kevin Felipez Gutierrez
    #FECHA:		15-11-2019 17:01:32
    ***********************************/

    elsif(p_transaccion='TES_REPOP_IME')then

        begin
                  /*--------------------
                  Replicar una Obligacion de Pago
                  ----------------------*/
            select op.*
                  into v_parametros_op
                  from tes.tobligacion_pago op
                  where op.id_obligacion_pago = v_parametros.id_obligacion_pago;
                  --raise exception 'llega obligacion pago: %',hstore(v_parametros_op);
              v_resp = tes.f_inserta_obligacion_pago_replicado(p_administrador, p_id_usuario,hstore(v_parametros_op));



              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligacion de Pago Replicado');
              v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

              --Devuelve la respuesta
              return v_resp;

        end;

    /*********************************
    #TRANSACCION:  'TES_VALPRE_IME'
    #DESCRIPCION:	validar presupuesto a nivel centro de costo
    #AUTOR:		breydi vasquez
    #FECHA:		08-01-2020
    ***********************************/

    elsif(p_transaccion='TES_VALPRE_IME')then

        begin
			select sol.presupuesto_aprobado
            	into v_aprobado
            from tes.tobligacion_pago sol
            where sol.id_obligacion_pago = v_parametros.id_obligacion_pago;

			if v_parametros.aprobar = 'si' and v_aprobado <> 'aprobado' then

                  update tes.tobligacion_pago  set
                  presupuesto_aprobado = 'aprobado'
                  where id_obligacion_pago = v_parametros.id_obligacion_pago;

            else
            	if v_aprobado <> 'aprobado' then
                  update tes.tobligacion_pago  set
                  presupuesto_aprobado = 'sin_presupuesto_cc'
                  where id_obligacion_pago = v_parametros.id_obligacion_pago;
                end if;
            end if;


          --Definicion de la respuesta
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se Actualizo con exito');
          v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

          --Devuelve la respuesta
          return v_resp;

        end;

    /*********************************
 	#TRANSACCION:  'TES_DOCSIGEP_IME'
 	#DESCRIPCION:	Registrar Nro. Preventivo,
 	#AUTOR:		franklin.espinoza
 	#FECHA:		15/10/2020 10:28:30
	***********************************/

	elsif(p_transaccion = 'TES_DOCSIGEP_IME') then

		begin

        	-----------------------------------
            --REGISTRO DE DOCUMENTO SIGEP
            -----------------------------------
            update tes.tobligacion_pago  set
              nro_preventivo = v_parametros.nro_preventivo
            where id_obligacion_pago = v_parametros.id_obligacion_pago;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se Actualizo con exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);

            --Devuelve la respuesta
            return v_resp;
		end;

		/*********************************
        #TRANSACCION:  'TES_RELACOB_IME'
        #DESCRIPCION:	Relacionar obligacion de pago
        #AUTOR:		maylee.perez
        #FECHA:		02/11/2020 10:28:30
        ***********************************/

        elsif(p_transaccion = 'TES_RELACOB_IME') then

            begin

            	SELECT op.id_obligacion_pago
                INTO v_id_obligacion_pago_secundario
                FROM tes.tobligacion_pago op
                WHERE op.id_proceso_wf = v_parametros.id_proceso_wf ;

                -- Sentencia de la insercion
        	--raise exception 'lega2 %',v_parametros.id_obligacion_pago;
                insert into tes.trelacion_proceso_pago(
                  observaciones,
                  id_obligacion_pago, -- obligacion que se relaciona
                  id_obligacion_pago_ini,

                  estado_reg,
                  fecha_reg,
                  id_usuario_reg,
                  id_usuario_mod,
                  fecha_mod
                )
                values(
                  v_parametros.observaciones,
                  v_parametros.id_obligacion_pago,
                  v_id_obligacion_pago_secundario,

                  'activo',
                  now(),
                  p_id_usuario,
                  null,
                  null

                )RETURNING id_relacion_proceso_pago into v_id_relacion_proceso_pago;



            --raise exception 'lega2 %',v_id_obligacion_pago_secundario;

                FOR v_id_obligacion_pago_original IN( SELECT odet.*
                                                      FROM tes.tobligacion_det odet
                                                      WHERE odet.id_obligacion_pago = v_parametros.id_obligacion_pago
                                                      )LOOP


                                  --Sentencia de la insercion
                                  insert into tes.tobligacion_det(
                                    estado_reg,
                                    --id_cuenta,
                                    id_partida,
                                    --id_auxiliar,
                                    id_concepto_ingas,
                                    monto_pago_mo,
                                    id_obligacion_pago,
                                    id_centro_costo,
                                    monto_pago_mb,
                                    descripcion,
                                    fecha_reg,
                                    id_usuario_reg,
                                    fecha_mod,
                                    id_usuario_mod,
                                    id_orden_trabajo,
                                    id_obligacion_pago_relacion
                                  )
                                  values(
                                    'activo',
                                    --v_parametros.id_cuenta,
                                    v_id_obligacion_pago_original.id_partida,
                                    --v_parametros.id_auxiliar,
                                    v_id_obligacion_pago_original.id_concepto_ingas,
                                    0, --v_id_obligacion_pago_original.monto_pago_mo,
                                    v_id_obligacion_pago_secundario,
                                    v_id_obligacion_pago_original.id_centro_costo,
                                    0, --v_id_obligacion_pago_original.monto_pago_mb,
                                    v_id_obligacion_pago_original.descripcion,
                                    now(),
                                    p_id_usuario,
                                    null,
                                    null,
                                    v_id_obligacion_pago_original.id_orden_trabajo,
                                    v_id_obligacion_pago_original.id_obligacion_pago

                                    )RETURNING id_obligacion_det into v_id_obligacion_det;


                END LOOP;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje',' Se registro con exito');
                v_resp = pxp.f_agrega_clave(v_resp,'id_relacion_proceso_pago',v_id_relacion_proceso_pago::varchar);

                --Devuelve la respuesta
                return v_resp;
            end;

        /*********************************
        #TRANSACCION:  'TES_RELACOB_MOD'
        #DESCRIPCION:	Modificacion obligacion de pago
        #AUTOR:		maylee.perez
        #FECHA:		02/11/2020 10:28:30
        ***********************************/

        elsif(p_transaccion='TES_RELACOB_MOD')then

            begin

                    --Sentencia de la modificacion
                    update tes.trelacion_proceso_pago set
                    observaciones = v_parametros.observaciones,
                    id_obligacion_pago = v_parametros.id_obligacion_pago,

                    id_usuario_mod = p_id_usuario,
                    fecha_mod = now()

                    where id_relacion_proceso_pago=v_parametros.id_relacion_proceso_pago;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Observaciones modificado(a)');
                v_resp = pxp.f_agrega_clave(v_resp,'id_relacion_proceso_pago',v_parametros.id_relacion_proceso_pago::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;
		/*********************************
        #TRANSACCION:  'TES_RELACOB_ELI'
        #DESCRIPCION:	Eliminacion de registros
        #AUTOR:		maylee.perez
        #FECHA:		02/11/2020 10:28:30
        ***********************************/

        elsif(p_transaccion='TES_RELACOB_ELI')then

            begin

           -- raise exception 'legaeli %', v_parametros.id_relacion_proceso_pago;
                --Sentencia de la modificacion
                update tes.trelacion_proceso_pago  set
                  estado_reg = 'inactivo',
                  id_usuario_mod = p_id_usuario,
                  fecha_mod = now()
                where id_relacion_proceso_pago=v_parametros.id_relacion_proceso_pago;

                --obligacion de pago del de donde se saco la relacion
                SELECT rpp.id_obligacion_pago
                INTO v_id_obligacion_pago
                FROM tes.trelacion_proceso_pago rpp
                WHERE rpp.id_relacion_proceso_pago = v_parametros.id_relacion_proceso_pago;


                FOR v_id_obligacion_det_rel IN( SELECT odet.id_obligacion_det
                                                      FROM tes.tobligacion_det odet
                                                      WHERE odet.id_obligacion_pago_relacion = v_id_obligacion_pago
                                                      )LOOP

                 --raise exception 'llega2 %',v_id_obligacion_det_rel;
                            delete from tes.tobligacion_det od
                            where od.id_obligacion_det = v_id_obligacion_det_rel;


                END LOOP;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Observaciones eliminado(a)');
                v_resp = pxp.f_agrega_clave(v_resp,'id_relacion_proceso_pago',v_parametros.id_relacion_proceso_pago::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;

        /*********************************
        #TRANSACCION:  'TES_EXTPGAE_IME'
        #DESCRIPCION:  Extiende la obligacion de pago Exterior para la gestion siguiente
        #AUTOR:	    maylee.perez
        #FECHA:		25-02-2021 16:01:32
        ***********************************/

        elsif(p_transaccion='TES_EXTPGAE_IME')then

            begin
                --------------------------------------
                -- verificar que no este extendida
                --------------------------------------

                Select *
                into v_registros
                from tes.tobligacion_pago op
                where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

                --validar que el estado de la obligacion sea finaliza
                IF v_registros.estado not in ('registrado','en_pago','finalizado') THEN
                   raise exception 'No se permiten obligaciones de pago que no esten finalizadas';
                END IF;

                --validar que no tenga extenciones
                IF v_registros.id_obligacion_pago_extendida is not null THEN
                  raise exception 'La obligacion de pago ya fue extendida';
                END IF;

            ---

			IF EXISTS (SELECT pp.*
                        FROM tes.tplan_pago pp
                        WHERE pp.id_obligacion_pago =  v_parametros.id_obligacion_pago
                        and pp.estado = 'devengado' ) THEN



                         ------------------------------
                         -- copiar obligacion de pago
                         ------------------------------

                          v_date = now()::Date;
                          v_anho = (date_part('year', v_registros.fecha))::integer;
                          v_anho = v_anho  + 1;


                          if(v_anho>date_part('year', v_date)) then
                              v_date = (date_part('year', v_date)::varchar||'-1-1')::Date;
                          else
                              v_date = (v_anho::varchar||'-1-1')::Date;
                          end if;

                          v_hstore_registros =   hstore(ARRAY[
                                                           'fecha',v_date::varchar,
                                                           'tipo_obligacion', 'pgaext',
                                                           'estado', 'borrador'::varchar,
                                                           'id_funcionario',v_registros.id_funcionario::varchar,
                                                           '_id_usuario_ai',v_parametros._id_usuario_ai::varchar,
                                                           '_nombre_usuario_ai',v_parametros._nombre_usuario_ai::varchar,
                                                           'id_depto',v_registros.id_depto::varchar,
                                                           'obs','Extiende el tramite: '||v_registros.num_tramite||',  Obs:  '||v_registros.obs,
                                                           'id_proveedor',v_registros.id_proveedor::varchar,
                                                           --'tipo_obligacion',v_registros.tipo_obligacion::varchar,
                                                           'id_moneda',v_registros.id_moneda::varchar,
                                                           'tipo_cambio_conv',v_registros.tipo_cambio_conv::varchar,
                                                           'pago_variable',v_registros.pago_variable::varchar,
                                                           'total_nro_cuota',v_registros.total_nro_cuota::varchar,
                                                           'fecha_pp_ini',v_registros.fecha_pp_ini::varchar,
                                                           'rotacion',v_registros.rotacion::varchar,
                                                           'id_plantilla',v_registros.id_plantilla::varchar,
                                                           'tipo_anticipo',v_registros.tipo_anticipo::varchar,
                                                           'id_contrato',v_registros.id_contrato::varchar
                                                          ]);


                          --bandera para ver si es un pago recurrente o unico=1, pga=2.
                          if(v_parametros.id_administrador = 2)then
                             v_id_administrador = 2;
                          elsif (v_parametros.id_administrador = 3) then
                             v_id_administrador = 3;
                          else
                             v_id_administrador = p_administrador;
                          end if;

                           v_resp = tes.f_inserta_obligacion_pago(v_id_administrador, p_id_usuario, hstore(v_hstore_registros));
                           v_id_obligacion_pago_sg =  pxp.f_recupera_clave(v_resp, 'id_obligacion_pago');
                           v_id_gestion_sg =  pxp.f_recupera_clave(v_resp, 'id_gestion');

                           -----

                           SELECT op.id_proceso_wf, op.id_estado_wf, op.estado, op.num_tramite
                           INTO v_id_proceso_wf, v_id_estado_wf, v_estado, v_num_tramite_sg
                           FROM tes.tobligacion_pago op
                           WHERE op.id_obligacion_pago = v_id_obligacion_pago_sg[1]::integer;


                           -----

                           --------------------------------------------------------------------------------------------
                           -- copiar detalle de obligacion , verifican la tabla id_presupuestos_ids si existe se copia...
                           ------------------------------------------------------------------------------------------------
                            FOR  v_registros_det in (
                                                    SELECT
                                                        od.id_obligacion_det,
                                                        od.id_concepto_ingas,
                                                        od.id_centro_costo,
                                                        od.id_partida,
                                                        od.descripcion,
                                                        od.monto_pago_mo ,
                                                        od.id_orden_trabajo,
                                                        od.monto_pago_mb
                                                      FROM  tes.tobligacion_det od
                                                      where  od.estado_reg = 'activo' and
                                                             od.id_obligacion_pago = v_parametros.id_obligacion_pago) LOOP





                                            SELECT pro.monto_ejecutar_mo, pro.monto_ejecutar_mb
                                            INTO v_montos_devengados_pro
                                            FROM tes.tprorrateo pro
                                            inner join tes.tobligacion_det od on od.id_obligacion_det = pro.id_obligacion_det
                                            inner join tes.tplan_pago pp on pp.id_plan_pago = pro.id_plan_pago
                                            WHERE od.id_obligacion_pago = v_parametros.id_obligacion_pago
                                            and od.id_obligacion_det = v_registros_det.id_obligacion_det
                                            and pp.estado = 'devengado';


                                            v_monto_pago_mo_det = (COALESCE(v_registros_det.monto_pago_mo, 0) - COALESCE(v_montos_devengados_pro.monto_ejecutar_mo, 0));
                                            v_monto_pago_mb_det = (COALESCE(v_registros_det.monto_pago_mb, 0) - COALESCE(v_montos_devengados_pro.monto_ejecutar_mb, 0));




                                            --Sentencia de la insercion
                                            insert into tes.tobligacion_det(
                                              estado_reg,
                                              --id_cuenta,
                                              id_partida,
                                              --id_auxiliar,
                                              id_concepto_ingas,
                                              monto_pago_mo,
                                              id_obligacion_pago,
                                              id_centro_costo,
                                              monto_pago_mb,
                                              descripcion,
                                              fecha_reg,
                                              id_usuario_reg,
                                              fecha_mod,
                                              id_usuario_mod,
                                              id_orden_trabajo
                                            )
                                            values
                                            (
                                              'activo',
                                              --v_parametros.id_cuenta,
                                              v_registros_det.id_partida,
                                              --v_parametros.id_auxiliar,
                                              v_registros_det.id_concepto_ingas,
                                              v_monto_pago_mo_det, --v_registros_det.monto_pago_mo,
                                              v_id_obligacion_pago_sg[1]::integer,
                                              v_registros_det.id_centro_costo,
                                              v_monto_pago_mb_det, --v_registros_det.monto_pago_mb,
                                              v_registros_det.descripcion,
                                              now(),
                                              p_id_usuario,
                                              null,
                                              null,
                                              v_registros_det.id_orden_trabajo

                                            )RETURNING id_obligacion_det into v_id_obligacion_det;



                            END LOOP;

                          --actualiza obligacion extendida en la original

                          update tes.tobligacion_pago set
                              id_obligacion_pago_extendida = v_id_obligacion_pago_sg[1]::integer,
                              id_usuario_mod = p_id_usuario,
                              fecha_mod = now()
                          where id_obligacion_pago = v_parametros.id_obligacion_pago;



           ELSE ---IF exist



			---
               ------------------------------
               -- copiar obligacion de pago
               ------------------------------

                v_date = now()::Date;
                v_anho = (date_part('year', v_registros.fecha))::integer;
                v_anho = v_anho  + 1;


                if(v_anho>date_part('year', v_date)) then
                    v_date = (date_part('year', v_date)::varchar||'-1-1')::Date;
                else
                    v_date = (v_anho::varchar||'-1-1')::Date;
                end if;

                v_hstore_registros =   hstore(ARRAY[
                                                 'fecha',v_date::varchar,
                                                 'tipo_obligacion', 'pgaext',
                                                 'estado', 'en_pago'::varchar,
                                                 'id_funcionario',v_registros.id_funcionario::varchar,
                                                 '_id_usuario_ai',v_parametros._id_usuario_ai::varchar,
                                                 '_nombre_usuario_ai',v_parametros._nombre_usuario_ai::varchar,
                                                 'id_depto',v_registros.id_depto::varchar,
                                                 'obs','Extiende el tramite: '||v_registros.num_tramite||',  Obs:  '||v_registros.obs,
                                                 'id_proveedor',v_registros.id_proveedor::varchar,
                                                 --'tipo_obligacion',v_registros.tipo_obligacion::varchar,
                                                 'id_moneda',v_registros.id_moneda::varchar,
                                                 'tipo_cambio_conv',v_registros.tipo_cambio_conv::varchar,
                                                 'pago_variable',v_registros.pago_variable::varchar,
                                                 'total_nro_cuota',v_registros.total_nro_cuota::varchar,
                                                 'fecha_pp_ini',v_registros.fecha_pp_ini::varchar,
                                                 'rotacion',v_registros.rotacion::varchar,
                                                 'id_plantilla',v_registros.id_plantilla::varchar,
                                                 'tipo_anticipo',v_registros.tipo_anticipo::varchar,
                                                 'id_contrato',v_registros.id_contrato::varchar
                                                ]);


                --bandera para ver si es un pago recurrente o unico=1, pga=2.
                if(v_parametros.id_administrador = 2)then
                   v_id_administrador = 2;
                elsif (v_parametros.id_administrador = 3) then
                   v_id_administrador = 3;
                else
                   v_id_administrador = p_administrador;
                end if;

                 v_resp = tes.f_inserta_obligacion_pago(v_id_administrador, p_id_usuario, hstore(v_hstore_registros));
                 v_id_obligacion_pago_sg =  pxp.f_recupera_clave(v_resp, 'id_obligacion_pago');
                 v_id_gestion_sg =  pxp.f_recupera_clave(v_resp, 'id_gestion');

                 -----

                 SELECT op.id_proceso_wf, op.id_estado_wf, op.estado, op.num_tramite
                 INTO v_id_proceso_wf, v_id_estado_wf, v_estado, v_num_tramite_sg
                 FROM tes.tobligacion_pago op
                 WHERE op.id_obligacion_pago = v_id_obligacion_pago_sg[1]::integer;

                 --UPDATE
                 update tes.tobligacion_pago set
                 estado = 'en_pago'::varchar,
                 total_pago = v_registros.total_pago::numeric
                 where id_obligacion_pago = v_id_obligacion_pago_sg[1]::integer;

                SELECT es.* , op.estado
                INTO v_estado_pago
                FROM tes.tobligacion_pago op
                inner join wf.testado_wf es on es.id_estado_wf = op.id_estado_wf
                inner join wf.ttipo_estado ts on ts.id_tipo_estado= es.id_tipo_estado
                WHERE op.id_obligacion_pago = v_id_obligacion_pago_sg[1]::integer;


				---------------------------------------
                -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                ---------------------------------------

				IF v_estado_pago.estado::varchar = 'en_pago' THEN
                	IF v_estado = 'borrador' THEN

                       --   para un estado siguiente
                             SELECT  ps_id_tipo_estado,
                                     ps_codigo_estado
                                     --ps_disparador,
                                     --ps_regla,
                                     --ps_prioridad

                                 into
                                    va_id_tipo_estado,
                                    va_codigo_estado
                                    --va_disparador,
                                    --va_regla,
                                    --va_prioridad

                                FROM wf.f_obtener_estado_wf(
                                 v_id_proceso_wf,
                                  NULL,
                                 v_estado_pago.id_tipo_estado,
                                 'siguiente',
                                 p_id_usuario);

                       v_id_estado_actual =  wf.f_registra_estado_wf(  va_id_tipo_estado[1],
                                                                       v_estado_pago.id_funcionario,
                                                                       v_estado_pago.id_estado_wf,
                                                                       v_id_proceso_wf,
                                                                       p_id_usuario,
                                                                       v_estado_pago.id_usuario_ai,
                                                                       v_estado_pago.usuario_ai,
                                                                       v_estado_pago.id_depto,
                                                                       v_estado_pago.obs);

                       -- actualiza estado en la solicitud
                          update tes.tobligacion_pago   set
                             id_estado_wf =  v_id_estado_actual,
                             estado = va_codigo_estado[1],
                             id_usuario_mod = p_id_usuario,
                             --id_usuario_ai = p_id_usuario_ai,
                             --usuario_ai = p_usuario_ai,
                             fecha_mod = now()

                          where id_proceso_wf = v_id_proceso_wf;

                    END IF;

                END IF;
                 -----



                 --------------------------------------------------------------------------------------------
                 -- copiar detalle de obligacion , verifican la tabla id_presupuestos_ids si existe se copia...
                 ------------------------------------------------------------------------------------------------
                  FOR  v_registros_det in (
                                          SELECT
                                              od.id_obligacion_det,
                                              od.id_concepto_ingas,
                                              od.id_centro_costo,
                                              od.id_partida,
                                              od.descripcion,
                                              od.monto_pago_mo ,
                                              od.id_orden_trabajo,
                                              od.monto_pago_mb,
                                              od.factor_porcentual
                                            FROM  tes.tobligacion_det od
                                            where  od.estado_reg = 'activo' and
                                                   od.id_obligacion_pago = v_parametros.id_obligacion_pago) LOOP


                        --recueprar centro de cotos para la siguiente gestion  (los centro de cosots y presupeustos tiene los mismo IDS)

                            select pi.id_presupuesto_dos
                            into v_id_centro_costo_dos
                            from pre.tpresupuesto_ids pi
                            where pi.id_presupuesto_uno = v_registros_det.id_centro_costo;

                            IF v_id_centro_costo_dos is not NULL THEN

                                    SELECT ps_id_partida
                                    into v_id_partida
                                    FROM conta.f_get_config_relacion_contable('CUECOMP', v_id_gestion_sg[1]::integer, v_registros_det.id_concepto_ingas, v_id_centro_costo_dos);


                                  --Sentencia de la insercion
                                  insert into tes.tobligacion_det(
                                    estado_reg,
                                    --id_cuenta,
                                    id_partida,
                                    --id_auxiliar,
                                    id_concepto_ingas,
                                    monto_pago_mo,
                                    id_obligacion_pago,
                                    id_centro_costo,
                                    monto_pago_mb,
                                    descripcion,
                                    fecha_reg,
                                    id_usuario_reg,
                                    fecha_mod,
                                    id_usuario_mod,
                                    id_orden_trabajo,
                                    factor_porcentual
                                  )
                                  values
                                  (
                                    'activo',
                                    --v_parametros.id_cuenta,
                                    v_registros_det.id_partida,
                                    --v_parametros.id_auxiliar,
                                    v_registros_det.id_concepto_ingas,
                                    v_registros_det.monto_pago_mo,
                                    v_id_obligacion_pago_sg[1]::integer,
                                    v_registros_det.id_centro_costo,
                                    v_registros_det.monto_pago_mb,
                                    v_registros_det.descripcion,
                                    now(),
                                    p_id_usuario,
                                    null,
                                    null,
                                    v_registros_det.id_orden_trabajo,
                                    v_registros_det.factor_porcentual

                                  )RETURNING id_obligacion_det into v_id_obligacion_det;

                            END IF;

                  END LOOP;

                --actualiza obligacion extendida en la original

                update tes.tobligacion_pago set
                    id_obligacion_pago_extendida = v_id_obligacion_pago_sg[1]::integer,
                    id_usuario_mod = p_id_usuario,
                    fecha_mod = now()
                where id_obligacion_pago = v_parametros.id_obligacion_pago;


                ------------------------------
                --01-04-2021
                --INSERTA DOCUMENTOS
                ------------------------------


                select op.*
                into v_obligacion_pago
                from tes.tobligacion_pago op
                where op.id_obligacion_pago = v_parametros.id_obligacion_pago ;



                for v_documentos in (select wf.*
                							from  wf.tdocumento_wf wf
                                            where wf.id_proceso_wf = v_obligacion_pago.id_proceso_wf) loop
                --v_id_documento_wf_op = 30;
               		INSERT INTO
                                wf.tdocumento_wf
                              (
                                id_usuario_reg,
                                fecha_reg,
                                estado_reg,
                                id_tipo_documento,
                                id_proceso_wf,
                                num_tramite,
                                chequeado,
                                url,
                                extension,
                                obs,
                                chequeado_fisico,
                                id_usuario_upload,
                                fecha_upload,
                                id_documento_wf_ori,
                                id_estado_ini
                              )
                              VALUES (
                                p_id_usuario,
                                now(),
                               'activo',
                                v_documentos.id_tipo_documento,
                                v_documentos.id_proceso_wf,
                                v_documentos.num_tramite,
                                v_documentos.chequeado,
                                v_documentos.url,
                                v_documentos.extension,
                                v_documentos.obs,
                                v_documentos.chequeado_fisico,
                                v_documentos.id_usuario_upload,
                                v_documentos.fecha_upload,
                                v_documentos.id_documento_wf,
                                v_id_estado_actual
                               );

          		 END LOOP;

                 -----------------------------------------------------------------------------------------------
                 --01-04-2021
                 -- copiar plan de pago
                 ------------------------------------------------------------------------------------------------


                  FOR  v_registros_pp_json in ( SELECT pp.*
                                              FROM tes.tplan_pago pp
                                              WHERE pp.id_obligacion_pago =  v_parametros.id_obligacion_pago) LOOP

                           v_hstore_registros_pp =   hstore(ARRAY[
               								 'nro_cuota',v_registros_pp_json.nro_cuota::varchar,
                                              'estado', 'borrador'::varchar,
                                             'tipo_pago',v_registros_pp_json.tipo_pago::varchar,
                                             'monto_ejecutar_total_mo',v_registros_pp_json.monto_ejecutar_total_mo::varchar,
                                             'id_plantilla',v_registros_pp_json.id_plantilla::varchar,
                                             'descuento_anticipo',v_registros_pp_json.descuento_anticipo::varchar,
                                             'otros_descuentos',v_registros_pp_json.otros_descuentos::varchar,
                                             'tipo',v_registros_pp_json.tipo::varchar,
                                              --'tipo',(v_registros_pp_json->>'tipo')::varchar,
                                             'monto',v_registros_pp_json.monto::varchar,
                                             'nombre_pago',v_registros_pp_json.nombre_pago::varchar,
                                             'forma_pago',v_registros_pp_json.forma_pago::varchar,
                                             'fecha_reg',v_registros_pp_json.fecha_reg::varchar,
                                             'id_usuario_reg',v_registros_pp_json.id_usuario_reg::varchar,
                                             --'fecha_tentativa',(v_registros_pp_json->>'fecha_tentativa')::varchar,
                                             --'fecha_costo_ini',(v_registros_pp_json->>'fecha_costo_ini')::varchar,
                                             --'fecha_costo_fin',(v_registros_pp_json->>'fecha_costo_fin')::varchar,
                                             'es_ultima_cuota',v_registros_pp_json.es_ultima_cuota::varchar,
                                             'id_usuario_ai',v_registros_pp_json.id_usuario_ai::varchar,
                                             'usuario_ai',v_registros_pp_json.usuario_ai::varchar,
                                             'monto_no_pagado',v_registros_pp_json.monto_no_pagado::varchar,
                                             'monto_retgar_mo',v_registros_pp_json.monto_retgar_mo::varchar,
                                             'descuento_ley',v_registros_pp_json.descuento_ley::varchar,
                                             'porc_descuento_ley',v_registros_pp_json.porc_descuento_ley::varchar,
                                             'id_obligacion_pago',v_id_obligacion_pago_sg[1]::varchar,
                                             'id_depto_lb',v_registros_pp_json.id_depto_lb::varchar,
                                             'obs_monto_no_pagado',v_registros_pp_json.obs_monto_no_pagado::varchar
                                            ]);

            	END LOOP;

                v_resp = tes.f_inserta_plan_pago_dev(p_administrador, p_id_usuario,hstore(v_hstore_registros_pp));
                v_id_obligacion_pago_sg_pp =  pxp.f_recupera_clave(v_resp, 'id_plan_pago');
                v_id_gestion_sg_pp =  pxp.f_recupera_clave(v_resp, 'id_gestion');


                 ------------------------------
                 --01-04-2021
                 --INSERTA FACTURAS
                 ------------------------------

                for v_facturas in (select dcv.*
                                  from  conta.tdoc_compra_venta dcv
                                  left join tes.tplan_pago pp on pp.id_plan_pago =   dcv.id_plan_pago
                                  where pp.id_obligacion_pago =  v_parametros.id_obligacion_pago) loop


                                   UPDATE conta.tdoc_compra_venta SET
                                   id_plan_pago = v_id_obligacion_pago_sg_pp[1]::integer,
                                   nro_tramite = v_num_tramite_sg
                                   WHERE id_doc_compra_venta = v_facturas.id_doc_compra_venta;

                                   UPDATE conta.tdoc_compra_venta_ext SET
                                   nro_tramite_relacion = v_obligacion_pago.num_tramite
                                   WHERE id_doc_compra_venta = v_facturas.id_doc_compra_venta;




                 END LOOP;


             END IF; --IF EXIST


                -- Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se extendio la obligacion de pago a la siguiente gestion');
                v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);


              --Devuelve la respuesta
              return v_resp;

            end;
	/*********************************
    #TRANSACCION:  'TES_GENERAR_PVR_IME'
    #DESCRIPCION:	Generar Obligación Pago Viaticos y Refrigerios
    #AUTOR:		   franklin.espinoza
    #FECHA:		   01/11/2021 10:28:30
    ***********************************/
    elsif(p_transaccion='TES_GENERAR_PVR_IME')then
    	begin

    	    select tvr.nombre_origen, tvr.fecha_pago, tvr.detalle_con_pago
    	    into v_registros
    	    from tes.tplanilla_pvr_con_pago tvr
    	    where tvr.fecha_pago = v_parametros.fecha_pago and  tvr.nombre_origen = v_parametros.nombre_origen;

    	    if v_parametros.nombre_origen != 'viatico_operativo' and v_parametros.nombre_origen != 'viatico_administrativo' then
                raise 'Estimado Usuario: El campo nombre_origen: % no esta correctamente apropiado tiene que definir como "viatico_operativo" o "viatico_administrativo"',v_parametros.nombre_origen;
            end if;

    	    if v_registros.nombre_origen = v_parametros.nombre_origen and v_registros.fecha_pago = v_parametros.fecha_pago  and v_registros.detalle_con_pago::jsonb = v_parametros.json_beneficiarios::jsonb then
                raise 'Estimado Usuario ya se genero una Obligacion de Pago para la fecha % para el tipo %', to_char(v_parametros.fecha_pago,'dd/mm/yyyy'), v_parametros.nombre_origen;
            else
    	        insert into tes.tplanilla_pvr_con_pago(
                    nombre_origen,
                    fecha_pago,
    	            ids_funcionario,
    	            detalle_con_pago,
    	            glosa_pago
                )values(
                    v_parametros.nombre_origen,
                    v_parametros.fecha_pago,
                    '[]'::jsonb,
                    '[]'::jsonb,
                    v_parametros.glosa_pago
                )returning id_planilla_pvr_con_pago into v_id_planilla_pvr_con_pago;

    	        insert into tes.tplanilla_pvr_sin_pago(
                    nombre_origen,
                    fecha_pago,
    	            ids_funcionario,
    	            detalle_sin_pago,
    	            glosa_pago
                )values(
                    v_parametros.nombre_origen,
                    v_parametros.fecha_pago,
                    '[]'::jsonb,
                    '[]'::jsonb,
                    v_parametros.glosa_pago
                )returning id_planilla_pvr_sin_pago into v_id_planilla_pvr_sin_pago;

                /******* creamos la tabla para consultar presupuesto *******/
                create temp table tt_saldo_presupuesto (
                    id_centro_costo INTEGER,
                    id_partida INTEGER,
                    vigente NUMERIC
                )on commit drop;
                /******* creamos la tabla para consultar presupuesto *******/

            end if;


            select s.id_subsistema
            into v_id_subsistema
            from segu.tsubsistema s where s.codigo = 'TES';

            select pm.id_proceso_macro
            into v_id_proceso_macro
            from wf.tproceso_macro pm
            where pm.codigo = 'PVR';

            select   tp.codigo
            into v_codigo_tipo_proceso
            from wf.ttipo_proceso tp
            where tp.id_proceso_macro = v_id_proceso_macro and tp.estado_reg = 'activo' and tp.inicio = 'si';

            select ges.id_gestion
            into v_id_gestion
            from param.tgestion ges
            where ges.gestion = date_part('year',current_date);

            select per.id_periodo
            into v_id_periodo
            from param.tperiodo per
            where per.periodo = date_part('month',current_date) and per.id_gestion = v_id_gestion;

            select usu.id_usuario
            into v_id_usuario
            from orga.vfuncionario vf
            inner join segu.tusuario usu on usu.id_persona = vf.id_persona
            where vf.id_funcionario = v_parametros.id_funcionario_responsable;

            select pxp.list(uge.id_grupo::text)
            into v_filadd
            from segu.tusuario_grupo_ep uge
            where  uge.id_usuario = v_id_usuario;

            select car.id_lugar, car.id_cargo
            into v_id_lugar, v_id_cargo
            from orga.tuo_funcionario tuo
            inner join orga.tcargo car on car.id_cargo = tuo.id_cargo
            where tuo.tipo = 'oficial' and coalesce(tuo.fecha_finalizacion,'31/12/9999'::date) >= current_date and tuo.id_funcionario = v_parametros.id_funcionario_responsable;

            execute('
                SELECT
                DISTINCT
                DEPPTO.id_depto,
                DEPPTO.codigo,
                DEPPTO.nombre,
                DEPPTO.nombre_corto,
                DEPPTO.id_subsistema,
                DEPPTO.estado_reg,
                DEPPTO.fecha_reg,
                DEPPTO.id_usuario_reg,
                DEPPTO.fecha_mod,
                DEPPTO.id_usuario_mod,
                PERREG.nombre_completo1 as usureg,
                PERMOD.nombre_completo1 as usumod,
                SUBSIS.codigo||'' - ''||SUBSIS.nombre as desc_subsistema
            FROM param.tdepto DEPPTO
            INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
            INNER JOIN segu.tusuario USUREG on USUREG.id_usuario=DEPPTO.id_usuario_reg
            INNER JOIN segu.vpersona PERREG on PERREG.id_persona=USUREG.id_persona
            LEFT JOIN segu.tusuario USUMOD on USUMOD.id_usuario=DEPPTO.id_usuario_mod
            LEFT JOIN segu.vpersona PERMOD on PERMOD.id_persona=USUMOD.id_persona
            inner join param.tdepto_uo_ep due on due.id_depto =DEPPTO.id_depto
            inner join param.tgrupo_ep gep on gep.estado_reg = ''activo'' and ((gep.id_uo = due.id_uo  and gep.id_ep = due.id_ep )
            or (gep.id_uo = due.id_uo  and gep.id_ep is NULL ) or (gep.id_uo is NULL and gep.id_ep = due.id_ep )) and gep.id_grupo in ('||v_filadd||')
            WHERE DEPPTO.estado_reg = ''activo''  and SUBSIS.codigo = ''TES''  AND '||v_id_lugar||' = ANY(DEPPTO.id_lugares) AND DEPPTO.modulo = ''OP'' ') into v_depto;


            --obtener el correlativo segun el tipo de documento
            v_num = param.f_obtener_correlativo(
               'PVR',
               v_id_periodo,
               NULL,
               v_depto.id_depto::integer,
               v_id_usuario,
               'TES',
               NULL
            );

            -- inciar el tramite en el sistema de WF
            SELECT ps_num_tramite, ps_id_proceso_wf, ps_id_estado_wf, ps_codigo_estado
            into v_num_tramite, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado
            FROM wf.f_inicia_tramite(
               v_id_usuario,
               null::integer,
               null::varchar,
               v_id_gestion,
               v_codigo_tipo_proceso,
               v_parametros.id_funcionario_responsable::integer,
               v_depto.id_depto::integer,
               'Obligacion de pago ('||v_parametros.nombre_origen||') '||(v_parametros.glosa_pago)::varchar,
                'PVR'
               --v_num
            );

            --Sentencia de la insercion
            insert into tes.tobligacion_pago(
                estado,
                tipo_obligacion,
                id_moneda,
                obs,
                id_subsistema,
                id_funcionario,
                estado_reg,
                id_estado_wf,
                id_depto,
                num_tramite,
                id_proceso_wf,
                fecha_reg,
                id_usuario_reg,
                fecha_mod,
                id_usuario_mod,
                numero,
                fecha,
                id_gestion,
                tipo_cambio_conv,    -->  TIPO cambio convenido ....
                pago_variable,
                total_nro_cuota,
                fecha_pp_ini,

                tipo_anticipo,
                id_funcionario_gerente,
                id_contrato,
                fecha_costo_ini_pp,
                fecha_costo_fin_pp,
                fecha_conclusion_pago,
                presupuesto_aprobado,
                total_pago,
                id_plantilla,
                id_proveedor
            ) values(
                v_codigo_estado,
                'pago_pvr',
                1,
                v_parametros.glosa_pago,
                v_id_subsistema,
                v_parametros.id_funcionario_responsable,
                'activo',
                v_id_estado_wf,
                v_depto.id_depto,
                v_num_tramite,
                v_id_proceso_wf,
                now(),
                v_id_usuario,
                null,
                null,
                v_num,
                v_parametros.fecha_pago::date,
                v_id_gestion,
                1,--(p_hstore->'tipo_cambio_conv')::numeric,
                'no',
                1,
                current_date,
                'no'::varchar,
                null::integer,
                null::integer,
                v_parametros.fecha_ini,
                v_parametros.fecha_fin,
                v_parametros.fecha_fin,
                'verificar',
                v_total_pago,
                41,
                1262
            )RETURNING id_obligacion_pago into v_id_obligacion_pago;


    	    for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.json_beneficiarios)  loop

                select pre.id_centro_costo, pre.id_ot, vf.desc_funcionario2 funcionario, ('( '||tcc.codigo||' )'||tcc.descripcion) centro_costo
    	        into v_presupuestos
    	        from orga.tuo_funcionario asig
    	        inner join orga.vfuncionario vf on vf.id_funcionario = asig.id_funcionario
    	        inner join orga.tcargo_presupuesto pre on pre.id_cargo = asig.id_cargo and pre.id_gestion = v_id_gestion
                and
                (
                    (v_parametros.fecha_ini::date between pre.fecha_ini and coalesce(pre.fecha_fin, '31/12/2021'::date))
                    or
                    (v_parametros.fecha_fin::date between pre.fecha_ini and coalesce(pre.fecha_fin, '31/12/2021'::date))
                )

    	        inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
                inner join param.ttipo_cc tcc on tcc.id_tipo_cc = cc.id_tipo_cc
    	        where asig.tipo = 'oficial' and asig.id_funcionario = (v_record_json->>'id_funcionario')::integer  and coalesce(asig.fecha_finalizacion,'31/12/9999'::date) >= v_parametros.fecha_ini::date ;

                if (v_record_json->>'monto_nacional_operativo')::numeric > 0 then
                    v_id_concepto_ingas = 4814;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then
                        v_id_concepto_ingas = 4872;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;
                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar

                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;
                    end if;

                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_operativo')::numeric > 0 then
                    v_id_concepto_ingas = 4815;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then
                        v_id_concepto_ingas = 4873;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;
                    end if;

                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;

                if (v_record_json->>'monto_nacional_operativo_vuelo')::numeric > 0 or (v_record_json->>'monto_nacional_administrativo')::numeric > 0 then
                    v_id_concepto_ingas = 4872;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;
                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_operativo_vuelo')::numeric > 0 or (v_record_json->>'monto_internacional_administrativo')::numeric > 0 then
                    v_id_concepto_ingas = 4873;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;
                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;

                if (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric > 0 then
                    v_id_concepto_ingas = 2912;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;
                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric > 0 then
                    v_id_concepto_ingas = 2914;
                    select par.id_partida into v_id_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;
                    select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto from tt_saldo_presupuesto tsp where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;
                    if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                        insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                        values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado','01/01/2021','31/12/2021') - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido','01/01/2021','31/12/2021'));
                    end if;
                end if;
            end loop;

    	    /*create temp table tt_sin_presupuesto(
    	        id_funcionario      integer,
    	        codigo_cc           varchar,
                centro_costo		varchar,
                partida 			varchar
            )on commit drop;*/

            for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.json_beneficiarios)  loop
                v_record_json_array = '[]'::jsonb;
                v_record_json_array = v_record_json_array||v_record_json;
                --raise 'v_record_json: %, json_beneficiarios: %, compara: %',v_record_json_array, v_parametros.json_beneficiarios, v_parametros.json_beneficiarios @> v_record_json_array;
                select pre.id_centro_costo, pre.id_ot, vf.id_funcionario, vf.desc_funcionario2 funcionario, ('( '||tcc.codigo||' )'||tcc.descripcion) centro_costo, tcc.codigo codigo_cc
    	        into v_presupuestos
    	        from orga.tuo_funcionario asig
    	        inner join orga.vfuncionario vf on vf.id_funcionario = asig.id_funcionario
    	        inner join orga.tcargo_presupuesto pre on pre.id_cargo = asig.id_cargo and pre.id_gestion = v_id_gestion
    	        and
                (
                    (v_parametros.fecha_ini::date between pre.fecha_ini and coalesce(pre.fecha_fin, '31/12/2021'::date))
                    or
                    (v_parametros.fecha_fin::date between pre.fecha_ini and coalesce(pre.fecha_fin, '31/12/2021'::date))
                )
    	        inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
                inner join param.ttipo_cc tcc on tcc.id_tipo_cc = cc.id_tipo_cc
    	        where asig.tipo = 'oficial' and asig.id_funcionario = (v_record_json->>'id_funcionario')::integer  and coalesce(asig.fecha_finalizacion,'31/12/9999'::date) >= v_parametros.fecha_ini ;

                if (v_record_json->>'monto_nacional_operativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_operativo' then
                    v_id_concepto_ingas = 4814;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then
                        v_id_concepto_ingas = 4872;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;
                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar

                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;
                    end if;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_operativo')::numeric then
                        /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                        v_verificar_internacional = true;
                        if (v_record_json->>'monto_internacional_operativo')::numeric > 0 then

                            --v_id_concepto_ingas = 4815;
                            select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                            from pre.tconcepto_partida par
                            inner join pre.tpartida tp on tp.id_partida = par.id_partida
                            where par.id_concepto_ingas = 4815 and tp.id_gestion = v_id_gestion;

                            select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                            from tt_saldo_presupuesto tsp
                            where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                            v_verificar_internacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_internacional_operativo')::numeric;
                        end if;
                        /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                        if v_verificar_internacional then
                            update tt_saldo_presupuesto set
                                vigente = vigente - (v_record_json->>'monto_nacional_operativo')::numeric
                            where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                            insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                             id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                             fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                            values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_nacional_operativo')::numeric, v_id_obligacion_pago,
                                    v_presupuestos.id_centro_costo, (v_record_json->>'monto_nacional_operativo')::numeric,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Operativo Nacional '||v_presupuestos.funcionario end, now(), v_id_usuario,
                                    null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar,  (v_record_json->>'descripcion')::text
                            )RETURNING id_obligacion_det into v_id_obligacion_det;

                            v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                            v_verificar_id  = false;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_con_pago tvr
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;

                            if not v_verificar_id then
                                update tes.tplanilla_pvr_con_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_con_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_con_pago tvr
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_con_pago set
                                 detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            end if;
                        else
                            v_verificar_id = false;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;
                        end if;
                    else

                        v_verificar_id = true;
                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_sin_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago)
                        values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago);
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_operativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_operativo' then
                    v_id_concepto_ingas = 4815;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then
                        v_id_concepto_ingas = 4873;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    end if;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_internacional_operativo')::numeric then

                        /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                        v_verificar_nacional = true;
                        if (v_record_json->>'monto_nacional_operativo')::numeric > 0 then

                            --v_id_concepto_ingas = 4814;
                            select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                            from pre.tconcepto_partida par
                            inner join pre.tpartida tp on tp.id_partida = par.id_partida
                            where par.id_concepto_ingas = 4814 and tp.id_gestion = v_id_gestion;

                            select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                            from tt_saldo_presupuesto tsp
                            where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                            v_verificar_nacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_nacional_operativo')::numeric;
                        end if;
                        /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                        if v_verificar_nacional then
                            update tt_saldo_presupuesto set
                                vigente = vigente - (v_record_json->>'monto_internacional_operativo')::numeric
                            where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;
                            insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                             id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                             fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                            values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_internacional_operativo')::numeric, v_id_obligacion_pago,
                                    v_presupuestos.id_centro_costo, (v_record_json->>'monto_internacional_operativo')::numeric,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Operativo Internacional '||v_presupuestos.funcionario end, now(), v_id_usuario,
                                    null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar, (v_record_json->>'descripcion')::text
                            )RETURNING id_obligacion_det into v_id_obligacion_det;

                            v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                            v_verificar_id = false;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_con_pago tvr
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_con_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_con_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_con_pago tvr
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_con_pago set
                                 detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                            end if;
                        else
                            v_verificar_id = false;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;
                        end if;
                    else

                        v_verificar_id = false;
                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_sin_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago)
                        values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago);
                    end if;
                end if;

                if (v_record_json->>'monto_nacional_operativo_vuelo')::numeric > 0 or (v_record_json->>'monto_nacional_administrativo')::numeric > 0 then
                    v_id_concepto_ingas = 4872;
                    if (v_record_json->>'monto_nacional_operativo_vuelo')::numeric > 0 and (v_record_json->>'monto_nacional_administrativo')::numeric = 0 then
                        v_monto_ope_adm = (v_record_json->>'monto_nacional_operativo_vuelo')::numeric;
                        v_tipo_viatico = 'Viatico Nacional Operativo ';
                    elsif (v_record_json->>'monto_nacional_operativo_vuelo')::numeric = 0 or (v_record_json->>'monto_nacional_administrativo')::numeric > 0 then
                        v_monto_ope_adm = (v_record_json->>'monto_nacional_administrativo')::numeric;
                        v_tipo_viatico = 'Viatico Nacional Administrativo ';
                    end if;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  (v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_operativo_vuelo')::numeric or v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_administrativo')::numeric ) then
                        update tt_saldo_presupuesto set
                            vigente = vigente - coalesce((v_record_json->>'monto_nacional_operativo_vuelo')::numeric,0) - coalesce((v_record_json->>'monto_nacional_administrativo')::numeric,0)
                        where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                        --Sentencia de la insercion
                        insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                         id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                         fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar)
                        values( 'activo', v_id_partida, v_id_concepto_ingas, v_monto_ope_adm, v_id_obligacion_pago,
                                v_presupuestos.id_centro_costo, v_monto_ope_adm,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else v_tipo_viatico||v_presupuestos.funcionario end, now(), v_id_usuario,
                                null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar
                        )RETURNING id_obligacion_det into v_id_obligacion_det;

                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_con_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;

                    else

                        v_verificar_id = false;
                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_sin_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_sin_pago tvr
                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_sin_pago set
                             detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                        end if;

                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida)
                        values(v_presupuestos.id_funcionario, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida);
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_operativo_vuelo')::numeric > 0 or (v_record_json->>'monto_internacional_administrativo')::numeric > 0 then
                    v_id_concepto_ingas = 4873;

                    if (v_record_json->>'monto_internacional_operativo_vuelo')::numeric > 0 or (v_record_json->>'monto_internacional_administrativo')::numeric = 0 then
                        v_monto_ope_adm = (v_record_json->>'monto_internacional_operativo_vuelo')::numeric;
                        v_tipo_viatico = 'Viatico Internacional Operativo ';
                    elsif (v_record_json->>'monto_internacional_operativo_vuelo')::numeric = 0 or (v_record_json->>'monto_internacional_administrativo')::numeric > 0 then
                        v_monto_ope_adm = (v_record_json->>'monto_internacional_administrativo')::numeric;
                        v_tipo_viatico = 'Viatico Internacional Administrativo ';
                    end if;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and (v_saldo_presupuesto.vigente >= (v_record_json->>'monto_internacional_operativo_vuelo')::numeric or v_saldo_presupuesto.vigente >=  (v_record_json->>'monto_internacional_administrativo')::numeric) then
                        update tt_saldo_presupuesto set
                            vigente = vigente - coalesce((v_record_json->>'monto_internacional_operativo_vuelo')::numeric,0) - coalesce((v_record_json->>'monto_internacional_administrativo')::numeric,0)
                        where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                        --Sentencia de la insercion
                        insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                         id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                         fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar)
                        values( 'activo', v_id_partida, v_id_concepto_ingas, v_monto_ope_adm, v_id_obligacion_pago,
                                v_presupuestos.id_centro_costo, v_monto_ope_adm,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else v_tipo_viatico||v_presupuestos.funcionario end, now(), v_id_usuario,
                                null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar
                        )RETURNING id_obligacion_det into v_id_obligacion_det;

                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_con_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;
                    else
                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida)
                        values(v_presupuestos.id_funcionario, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida);
                    end if;
                end if;

                if (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric > 0 then
                    v_id_concepto_ingas = 2912;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric then
                        update tt_saldo_presupuesto set
                            vigente = vigente - (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric
                        where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                        --Sentencia de la insercion
                        insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                         id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                         fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar)
                        values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric, v_id_obligacion_pago,
                                v_presupuestos.id_centro_costo, (v_record_json->>'monto_nacional_administrativo_entrenamiento')::numeric,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Nacional Administrativo '||v_presupuestos.funcionario end, now(), v_id_usuario,
                                null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar
                        )RETURNING id_obligacion_det into v_id_obligacion_det;

                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_con_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;
                    else
                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida)
                        values(v_presupuestos.id_funcionario, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida);
                    end if;
                end if;

                if (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric > 0 then
                    v_id_concepto_ingas = 2914;

                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                    from pre.tconcepto_partida par
                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                    where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                    from tt_saldo_presupuesto tsp
                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                    select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                    into v_relacion_contable
                    from conta.trelacion_contable rel
                    where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                    if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric then
                        update tt_saldo_presupuesto set
                            vigente = vigente - (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric
                        where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                        --Sentencia de la insercion
                        insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                         id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                         fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar)
                        values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric, v_id_obligacion_pago,
                                v_presupuestos.id_centro_costo, (v_record_json->>'monto_internacional_administrativo_entrenamiento')::numeric,  case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Internacional Administrativo '||v_presupuestos.funcionario end, now(), v_id_usuario,
                                null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar
                        )RETURNING id_obligacion_det into v_id_obligacion_det;

                        select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;

                        v_verificar_id = false;
                        select tvr.detalle_con_pago @> v_record_json_array
                        into v_verificar_id
                        from tes.tplanilla_pvr_con_pago tvr
                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        if not v_verificar_id then
                            update tes.tplanilla_pvr_con_pago set
                             detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                            where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                        end if;
                    else
                        insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida)
                        values(v_presupuestos.id_funcionario, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida);
                    end if;
                end if;

            end loop;

    	    update tes.tobligacion_pago set
    	        total_pago = v_total_pago
    	    where id_obligacion_pago = v_id_obligacion_pago;

            v_json_presupuesto = '[';
    	    for v_presupuestos in select sp.codigo_cc, sp.centro_costo, sp.id_funcionario,  pxp.list(sp.partida)partida
                                  from tes.tt_sin_presupuesto sp
    	                          group by sp.codigo_cc,sp.centro_costo,sp.id_funcionario
                                  order by sp.codigo_cc asc loop

                v_json_presupuesto = v_json_presupuesto||'{"id_funcionario":'||v_presupuestos.id_funcionario||',"codigo_cc":'||v_presupuestos.codigo_cc||','||'"centro":"'||v_presupuestos.centro_costo||'","partida":"'||v_presupuestos.partida||'"},';

            end loop;

    	    v_json_presupuesto = v_json_presupuesto||']';

    	    v_json_presupuesto = replace(v_json_presupuesto, ',]', ']');
    	    v_json_presupuesto = replace(v_json_presupuesto, ',}', '}');

            if v_json_presupuesto = '[]' then
                v_status = 'exito';
            else
                v_status = 'observado';
            end if;

            /******************************** PROCESO DE CAMBIO DE ESTADO Y GENERACIÓN CUOTA ********************************/
            if v_id_obligacion_pago is not null then
              v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');

              for v_estados in select tte.codigo
                               from wf.ttipo_proceso  ttp
                               inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                               where ttp.codigo = 'PVR' loop

                  select op.id_proceso_wf, op.id_estado_wf, op.estado, op.id_depto, op.tipo_obligacion,
                    op.total_nro_cuota, op.fecha_pp_ini, op.rotacion, op.id_plantilla, op.tipo_cambio_conv,
                    pr.desc_proveedor, op.pago_variable, op.comprometido, op.id_usuario_reg, op.fecha
                  into v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_id_depto, v_tipo_obligacion,
                    v_total_nro_cuota, v_fecha_pp_ini, v_rotacion, v_id_plantilla, v_tipo_cambio_conv,
                    v_desc_proveedor, v_pago_variable, v_comprometido, v_id_usuario_reg_op, v_fecha_op
                  from tes.tobligacion_pago op
                  left join param.vproveedor pr  on pr.id_proveedor = op.id_proveedor
                  where op.id_obligacion_pago = v_id_obligacion_pago;

                  select te.id_tipo_estado
                  into v_id_tipo_estado
                  from wf.testado_wf te
                  inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                  where te.id_estado_wf = v_id_estado_wf;


                  SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                  into v_id_tipo_estado, v_codigo_estado_siguiente
                  from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                  select tft.id_funcionario
                  into v_id_funcionario
                  from wf.ttipo_estado tip
                  inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                  where tip.id_tipo_estado = v_id_tipo_estado
                  limit 1;

                  ---------------------------------------
                  -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                  ---------------------------------------
                  v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                                 v_id_funcionario,
                                                                 v_id_estado_wf,
                                                                 v_id_proceso_wf,
                                                                 p_id_usuario,
                                                                 null::integer,
                                                                 null::varchar,
                                                                 null::integer,
                                                                 'Cambio en Automatico PVR');


                  IF  v_codigo_estado in ('borrador','vbpoa','vbpresupuestos','liberacion' ) THEN
                      --validamos que el detalle tenga por lo menos un item con valor
                      select sum(od.monto_pago_mo)
                      into v_total_detalle
                      from tes.tobligacion_det od
                      where od.id_obligacion_pago = v_id_obligacion_pago and od.estado_reg ='activo';

                      IF v_total_detalle = 0 or v_total_detalle is null THEN
                          raise exception 'No existe el detalle de obligacion...';
                      END IF;
                      ------------------------------------------------------------
                      --calcula el factor de prorrateo de la obligacion  detalle
                      -----------------------------------------------------------
                      IF (tes.f_calcular_factor_obligacion_det(v_id_obligacion_pago) != 'exito')  THEN
                          raise exception 'error al calcular factores';
                      END IF;
                  END IF;

                  update tes.tobligacion_pago  set
                   id_estado_wf =  v_id_estado_actual,
                   estado = v_codigo_estado_siguiente,
                   id_usuario_mod = p_id_usuario,
                   fecha_mod = now()
                  where id_obligacion_pago  = v_id_obligacion_pago;


                  IF  v_codigo_estado_siguiente = 'registrado'  and v_total_nro_cuota > 0 THEN

                      select ps_descuento_porc, ps_descuento, ps_observaciones into v_registros_plan
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
                              v_ultima_cuota = true;
                          END IF;

                          v_descuentos_ley = v_monto_cuota * v_registros_plan.ps_descuento_porc;



                          --(may)tipo de obligacion SIP para  internacionales pago_especial_spi
                          --pago para bol pago_especial

                          IF v_tipo_obligacion in  ('pago_especial') THEN
                              v_tipo_plan_pago = 'especial';
                          ELSIF v_tipo_obligacion in  ('pago_especial_spi') THEN
                              v_tipo_plan_pago = 'especial_spi';
                          ELSE
                              --verifica que tipo de apgo estan deshabilitados
                              va_tipo_pago = regexp_split_to_array(pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'), E'\\s+');
                              v_tipo_plan_pago = 'devengado_pagado';

                              IF v_tipo_plan_pago =ANY(va_tipo_pago) THEN
                                  v_tipo_plan_pago = 'devengado_pagado_1c';
                              END IF;

                              IF v_tipo_obligacion in  ('spd', 'pgaext') THEN
                                  v_tipo_plan_pago = 'devengado_pagado_1c_sp';
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
                                                          'id_obligacion_pago',v_id_obligacion_pago::varchar,
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
                                                          'fecha_tentativa',v_fecha_pp_ini::varchar
                          ]);

                          --TODO,  bloquear en formulario de OP  facturas con monto excento


                          -- si es un proceso de pago unico,  la primera cuota pasa de borrador al siguiente estado de manera automatica
                          IF  ((v_tipo_obligacion = 'pbr' or v_tipo_obligacion = 'ppm' or v_tipo_obligacion = 'pga' or v_tipo_obligacion = 'pce' or v_tipo_obligacion = 'pago_unico' or v_tipo_obligacion = 'spd' or v_tipo_obligacion ='pgaext') and   v_i = 1)   THEN
                             v_sw_saltar = TRUE;
                          else
                             v_sw_saltar = FALSE;
                          END IF;

                          -- llamada para insertar plan de pagos
                          v_resp = tes.f_inserta_plan_pago_dev(p_administrador, v_id_usuario_reg_op,v_hstore_pp, v_sw_saltar);
                          --raise 'v_resp: %', v_resp;
                          -- calcula la fecha para la siguiente insercion
                          v_fecha_pp_ini =  v_fecha_pp_ini + interval  '1 month'*v_rotacion;
                      END LOOP;

                      IF not tes.f_gestionar_presupuesto_tesoreria(v_id_obligacion_pago, p_id_usuario, 'comprometer')  THEN
                          raise exception 'Error al comprometer el presupeusto';
                      END IF;

                      v_comprometido = 'si';
                      --cambia la bandera del comprometido
                      update tes.tobligacion_pago  set
                          comprometido = v_comprometido
                      where id_obligacion_pago  = v_id_obligacion_pago;

                      EXIT;
                  END IF;
              end loop;
              /*********************************************** Plan Pago Next Estado ***********************************************/
              select pp.id_proceso_wf
              into v_id_proceso_wf
              from tes.tplan_pago pp
              where pp.id_obligacion_pago = v_id_obligacion_pago;

               for v_estados in select tte.codigo
                               from wf.ttipo_proceso  ttp
                               inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                               where ttp.codigo = 'PVR_DEV' loop

                  select pp.id_plan_pago, pp.id_proceso_wf, pp.id_estado_wf, pp.estado, pp.fecha_tentativa, op.numero, pp.total_prorrateado ,
                         pp.monto_ejecutar_total_mo, pp.estado, pp.id_estado_wf, op.tipo_obligacion, pp.id_depto_lb, pp.monto,
                         pp.id_plantilla, pp.id_obligacion_pago, op.num_tramite, pp.tipo, op.id_moneda
                  into   v_id_plan_pago, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_fecha_tentativa, v_num_obliacion_pago, v_total_prorrateo,
                         v_monto_ejecutar_total_mo, v_estado_aux, v_id_estado_actual, v_tipo_obligacion, v_id_depto_lb_pp, v_monto_pp,
                         v_id_plantilla, v_id_obligacion_pago_pp, v_numero_tramite, vtipo_pp, v_id_moneda
                  from tes.tplan_pago  pp
                  inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                  where pp.id_proceso_wf  = v_id_proceso_wf;

                  select te.id_tipo_estado
                  into v_id_tipo_estado
                  from wf.testado_wf te
                  inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                  where te.id_estado_wf = v_id_estado_wf;


                  SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                  into v_id_tipo_estado, v_codigo_estado_siguiente
                  from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                  select tft.id_funcionario, tft.id_depto
                  into v_id_funcionario, v_id_depto
                  from wf.ttipo_estado tip
                  inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                  where tip.id_tipo_estado = v_id_tipo_estado
                  limit 1;
                  --raise 'v_id_tipo_estado: %, v_codigo_estado_siguiente: %, v_id_funcionario: %, v_id_depto: %',v_id_tipo_estado, v_codigo_estado_siguiente, v_id_funcionario, v_id_depto;
                  /*if  v_codigo_estado_siguiente = 'supconta' then
                      raise 'v_id_funcionario: %, v_id_depto: %',v_id_funcionario, v_id_depto;
                  end if;*/
                  ---------------------------------------
                  -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                  ---------------------------------------
                  v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                                 v_id_funcionario,
                                                                 v_id_estado_wf,
                                                                 v_id_proceso_wf,
                                                                 p_id_usuario,
                                                                 null::integer,
                                                                 null::varchar,
                                                                 v_id_depto,
                                                                 'Cambio en Automatico Plan Pago PVR');


                  update tes.tplan_pago  set
                   id_estado_wf =  v_id_estado_actual,
                   estado = v_codigo_estado_siguiente,
                   id_usuario_mod = p_id_usuario,
                   fecha_mod = now(),

                   id_depto_lb = 15,
                   id_cuenta_bancaria = 61,
                   forma_pago = 'transferencia',
                   id_proveedor_cta_bancaria = 652,
                   id_depto_conta = 4

                  where id_obligacion_pago  = v_id_obligacion_pago;

                  if  v_codigo_estado_siguiente = 'vbconta' then
                      EXIT;
                  end if;

              end loop;
			end if;
    	    /*********************************************** Plan Pago Next Estado ***********************************************/
            /******************************** PROCESO DE CAMBIO DE ESTADO Y GENERACIÓN CUOTA ********************************/

            /******************************** PROCESO PARA GENERAR EL COMPROBANTE PRESUPUESTARIO ********************************/
            --validacion de la cuota


            select pp.*,op.total_pago,op.comprometido, op.id_contrato, op.tipo_obligacion
            into v_registros
            from tes.tplan_pago pp
            inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
            where pp.id_obligacion_pago = v_id_obligacion_pago;

			v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');
			IF v_pre_integrar_presupuestos = 'true' THEN
              /*jrr:29/10/2014
              1) si el presupuesto no esta comprometido*/

              if (v_registros.comprometido = 'no') then

                  /*1.1)Validar que la suma de los detalles igualen al total de la obligacion*/
                  if ((select sum(od.monto_pago_mo)
                        from tes.tobligacion_det od
                        where id_obligacion_pago = v_id_obligacion_pago and estado_reg = 'activo') != v_registros.total_pago) THEN
                        raise exception 'La suma de todos los detalles no iguala con el total de la obligacion. La diferencia se genero al modificar la apropiacion';
                  end if;

                  /*1.2 Comprometer*/
                  select * into v_nombre_conexion from migra.f_crear_conexion();

                  select tes.f_gestionar_presupuesto_tesoreria(v_id_obligacion_pago, p_id_usuario, 'comprometer',NULL,v_nombre_conexion) into v_res;

                  if v_res = false then
                      raise exception 'Error al comprometer el presupuesto';
                  end if;

                  update tes.tobligacion_pago
                  set comprometido = 'si'
                  where id_obligacion_pago = v_id_obligacion_pago;
              end if;
	  		END IF;

            --(may) 20-07-2019 los tipo plan de pago devengado_pagado_1c_sp son para las internacionales -tramites sp con contato
            IF  v_registros.tipo  in ('pagado' ,'devengado_pagado','devengado_pagado_1c','anticipo','ant_parcial','devengado_pagado_1c_sp') and v_registros.tipo_obligacion != 'pago_pvr' THEN

              IF v_registros.forma_pago = 'cheque' THEN
              	IF  v_registros.nro_cheque is NULL THEN
                	raise exception  'Tiene que especificar el  nro de cheque';
                END IF;
              ELSE
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
               	select cb.centro
               	into v_centro
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
                   select c.tiene_retencion
                   into v_sw_retenciones
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
                                                      v_registros.id_plan_pago,
                                                      4,
                                                      v_nombre_conexion);
           	end if;
            /******************************** PROCESO PARA GENERAR EL COMPROBANTE PRESUPUESTARIO ********************************/

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','El registro se inserto con Exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_id_obligacion_pago::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'nro_tramite',v_num_tramite::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'personal_sin_presupuesto',v_json_presupuesto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'status',v_status);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************
    #TRANSACCION:  'TES_GENERAR_PVR_ADM'
    #DESCRIPCION:	Generar Obligación Pago Viaticos Administrativos
    #AUTOR:		   franklin.espinoza
    #FECHA:		   01/11/2021 10:28:30
    ***********************************/
    elsif(p_transaccion='TES_GENERAR_PVR_ADM')then
    	begin --raise 'TES_GENERAR_PVR_ADM %, %', v_parametros.codigo_tipo_pago, v_parametros.fecha_pago;
            if v_parametros.codigo_tipo_pago = 'gasto' then

                select tvr.nombre_origen, tvr.fecha_pago, tvr.detalle_con_pago
                into v_registros
                from tes.tplanilla_pvr_con_pago tvr
                where tvr.fecha_pago = v_parametros.fecha_pago and  tvr.nombre_origen = v_parametros.nombre_origen;

                if v_parametros.nombre_origen != 'viatico_administrativo' then
                    raise 'Estimado Usuario: El campo nombre_origen: % no esta correctamente apropiado tiene que definir como "viatico_administrativo"',v_parametros.nombre_origen;
                end if;

                if v_registros.nombre_origen = v_parametros.nombre_origen and v_registros.fecha_pago = v_parametros.fecha_pago  and v_registros.detalle_con_pago::jsonb = v_parametros.json_beneficiarios::jsonb then
                    raise 'Estimado Usuario ya se genero una Obligacion de Pago para la fecha % para el tipo %', to_char(v_parametros.fecha_pago,'dd/mm/yyyy'), v_parametros.nombre_origen;
                else
                    insert into tes.tplanilla_pvr_con_pago(
                        nombre_origen,
                        fecha_pago,
                        ids_funcionario,
                        detalle_con_pago,
                        glosa_pago
                    )values(
                        v_parametros.nombre_origen,
                        v_parametros.fecha_pago,
                        '[]'::jsonb,
                        '[]'::jsonb,
                        v_parametros.glosa_pago
                    )returning id_planilla_pvr_con_pago into v_id_planilla_pvr_con_pago;

                    insert into tes.tplanilla_pvr_sin_pago(
                        nombre_origen,
                        fecha_pago,
                        ids_funcionario,
                        detalle_sin_pago,
                        glosa_pago
                    )values(
                        v_parametros.nombre_origen,
                        v_parametros.fecha_pago,
                        '[]'::jsonb,
                        '[]'::jsonb,
                        v_parametros.glosa_pago
                    )returning id_planilla_pvr_sin_pago into v_id_planilla_pvr_sin_pago;

                    /******* creamos la tabla para consultar presupuesto *******/
                    create temp table tt_saldo_presupuesto (
                        id_centro_costo INTEGER,
                        id_partida INTEGER,
                        vigente NUMERIC
                    )on commit drop;
                    /******* creamos la tabla para consultar presupuesto *******/


                end if;

                select s.id_subsistema
                into v_id_subsistema
                from segu.tsubsistema s where s.codigo = 'TES';

                select pm.id_proceso_macro
                into v_id_proceso_macro
                from wf.tproceso_macro pm
                where pm.codigo = 'PVR';

                select   tp.codigo
                into v_codigo_tipo_proceso
                from wf.ttipo_proceso tp
                where tp.id_proceso_macro = v_id_proceso_macro and tp.estado_reg = 'activo' and tp.inicio = 'si';

                select ges.id_gestion, ges.gestion
                into v_id_gestion, v_gestion
                from param.tgestion ges
                where ges.gestion = date_part('year',v_parametros.fecha_pago);--'31/12/2021'::date current_date

                select per.id_periodo
                into v_id_periodo
                from param.tperiodo per
                where per.periodo = date_part('month',v_parametros.fecha_pago) and per.id_gestion = v_id_gestion; --'31/12/2021'::date

                select usu.id_usuario
                into v_id_usuario
                from orga.vfuncionario vf
                inner join segu.tusuario usu on usu.id_persona = vf.id_persona
                where vf.id_funcionario = v_parametros.id_funcionario_responsable;


                if v_id_usuario is null then
                    raise 'Estimado Usuario: El valor para el campo "id_funcionario_responsable" no existe, pruebe con un identificador valido';
                end if;

                select pxp.list(uge.id_grupo::text)
                into v_filadd
                from segu.tusuario_grupo_ep uge
                where  uge.id_usuario = v_id_usuario;

                select car.id_lugar, car.id_cargo
                into v_id_lugar, v_id_cargo
                from orga.tuo_funcionario tuo
                inner join orga.tcargo car on car.id_cargo = tuo.id_cargo
                where tuo.tipo = 'oficial' and coalesce(tuo.fecha_finalizacion,'31/12/9999'::date) >= current_date and tuo.id_funcionario = v_parametros.id_funcionario_responsable;

                if v_id_cargo is null then
                    raise 'Estimado Usuario: El funcionario responsable no tiene una asignación activa';
                end if;

                execute('
                    SELECT
                    DISTINCT
                    DEPPTO.id_depto,
                    DEPPTO.codigo,
                    DEPPTO.nombre,
                    DEPPTO.nombre_corto,
                    DEPPTO.id_subsistema,
                    DEPPTO.estado_reg,
                    DEPPTO.fecha_reg,
                    DEPPTO.id_usuario_reg,
                    DEPPTO.fecha_mod,
                    DEPPTO.id_usuario_mod,
                    PERREG.nombre_completo1 as usureg,
                    PERMOD.nombre_completo1 as usumod,
                    SUBSIS.codigo||'' - ''||SUBSIS.nombre as desc_subsistema
                FROM param.tdepto DEPPTO
                INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
                INNER JOIN segu.tusuario USUREG on USUREG.id_usuario=DEPPTO.id_usuario_reg
                INNER JOIN segu.vpersona PERREG on PERREG.id_persona=USUREG.id_persona
                LEFT JOIN segu.tusuario USUMOD on USUMOD.id_usuario=DEPPTO.id_usuario_mod
                LEFT JOIN segu.vpersona PERMOD on PERMOD.id_persona=USUMOD.id_persona
                inner join param.tdepto_uo_ep due on due.id_depto =DEPPTO.id_depto
                inner join param.tgrupo_ep gep on gep.estado_reg = ''activo'' and ((gep.id_uo = due.id_uo  and gep.id_ep = due.id_ep )
                or (gep.id_uo = due.id_uo  and gep.id_ep is NULL ) or (gep.id_uo is NULL and gep.id_ep = due.id_ep )) and gep.id_grupo in ('||v_filadd||')
                WHERE DEPPTO.estado_reg = ''activo''  and SUBSIS.codigo = ''TES''  AND '||v_id_lugar||' = ANY(DEPPTO.id_lugares) AND DEPPTO.modulo = ''OP'' ') into v_depto;

                if v_depto is null then
                    raise 'Estimado Usuario: El funcionario responsable no tiene parametrizado la relación con el departamento de Obligacion.';
                end if;
                --obtener el correlativo segun el tipo de documento
                v_num = param.f_obtener_correlativo(
                   'PVR',
                   v_id_periodo,
                   NULL,
                   v_depto.id_depto::integer,
                   v_id_usuario,
                   'TES',
                   NULL
                );

                -- inciar el tramite en el sistema de WF
                SELECT ps_num_tramite, ps_id_proceso_wf, ps_id_estado_wf, ps_codigo_estado
                into v_num_tramite, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado
                FROM wf.f_inicia_tramite(
                   v_id_usuario,
                   null::integer,
                   null::varchar,
                   v_id_gestion,
                   v_codigo_tipo_proceso,
                   v_parametros.id_funcionario_responsable::integer,
                   v_depto.id_depto::integer,
                   'Obligacion de pago ('||v_parametros.nombre_origen||') '||(v_parametros.glosa_pago)::varchar,
                    'PVR'
                );


                --Sentencia de la insercion
                insert into tes.tobligacion_pago(
                    estado,
                    tipo_obligacion,
                    id_moneda,
                    obs,
                    id_subsistema,
                    id_funcionario,
                    estado_reg,
                    id_estado_wf,
                    id_depto,
                    num_tramite,
                    id_proceso_wf,
                    fecha_reg,
                    id_usuario_reg,
                    fecha_mod,
                    id_usuario_mod,
                    numero,
                    fecha,
                    id_gestion,
                    tipo_cambio_conv,    -->  TIPO cambio convenido ....
                    pago_variable,
                    total_nro_cuota,
                    fecha_pp_ini,

                    tipo_anticipo,
                    id_funcionario_gerente,
                    id_contrato,
                    fecha_costo_ini_pp,
                    fecha_costo_fin_pp,
                    fecha_conclusion_pago,
                    presupuesto_aprobado,
                    total_pago,
                    id_plantilla,
                    id_proveedor
                ) values(
                    v_codigo_estado,
                    'pago_pvr',
                    1,
                    v_parametros.glosa_pago,
                    v_id_subsistema,
                    v_parametros.id_funcionario_responsable,
                    'activo',
                    v_id_estado_wf,
                    v_depto.id_depto,
                    v_num_tramite,
                    v_id_proceso_wf,
                    now(),
                    v_id_usuario,
                    null,
                    null,
                    v_num,
                    v_parametros.fecha_pago::date,
                    v_id_gestion,
                    1,--(p_hstore->'tipo_cambio_conv')::numeric,
                    'no',
                    1,
                    current_date,
                    'no'::varchar,
                    null::integer,
                    null::integer,
                    v_parametros.fecha_pago,
                    v_parametros.fecha_pago,
                    v_parametros.fecha_pago,
                    'verificar',
                    v_total_pago,
                    41,
                    1262
                )RETURNING id_obligacion_pago into v_id_obligacion_pago;

                for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.json_beneficiarios)  loop

                    select pre.id_centro_costo, pre.id_ot, vf.desc_funcionario2 funcionario, ('( '||tcc.codigo||' )'||tcc.descripcion) centro_costo
                    into v_presupuestos
                    from orga.tuo_funcionario asig
                    inner join orga.vfuncionario vf on vf.id_funcionario = asig.id_funcionario
                    inner join orga.tcargo_presupuesto pre on pre.id_cargo = asig.id_cargo and pre.id_gestion = v_id_gestion
                    and
                    (
                        ((v_record_json->>'fecha_sol')::date between pre.fecha_ini and coalesce(pre.fecha_fin, ('31/12/'||v_gestion)::date))
                        or
                        ((v_record_json->>'fecha_sol')::date between pre.fecha_ini and coalesce(pre.fecha_fin, ('31/12/'||v_gestion)::date))
                    )

                    inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
                    inner join param.ttipo_cc tcc on tcc.id_tipo_cc = cc.id_tipo_cc
                    where asig.tipo = 'oficial' and asig.id_funcionario = (v_record_json->>'id_funcionario')::integer  and coalesce(asig.fecha_finalizacion,'31/12/9999'::date) >= (v_record_json->>'fecha_sol')::date;

                    if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 and (v_record_json->>'tipo')::varchar = 'administrativo' then
                        v_id_concepto_ingas = 4872;

                        select par.id_partida into v_id_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                            insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                            values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date) - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date));
                        end if;
                    end if;

                    if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 and (v_record_json->>'tipo')::varchar = 'administrativo' then
                        v_id_concepto_ingas = 4873;

                        select par.id_partida into v_id_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;


                        if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                            insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                            values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date) - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date));
                        end if;
                    end if;

                    if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 and (v_record_json->>'tipo')::varchar = 'entrenamiento' then
                        v_id_concepto_ingas = 2912;

                        select par.id_partida into v_id_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                            insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                            values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date) - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date));
                        end if;
                    end if;

                    if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 and (v_record_json->>'tipo')::varchar = 'entrenamiento' then
                        v_id_concepto_ingas = 2914;

                        select par.id_partida into v_id_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tpa on tpa.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tpa.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        if v_saldo_presupuesto.id_centro_costo is null and v_saldo_presupuesto.id_partida is null then
                            insert into tt_saldo_presupuesto(id_centro_costo, id_partida, vigente )
                            values(v_presupuestos.id_centro_costo, v_id_partida, pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'formulado',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date) - pre.f_get_estado_presupuesto_mb_x_fechas(v_presupuestos.id_centro_costo, v_id_partida,'comprometido',('01/01/'||v_gestion)::date,('31/12/'||v_gestion)::date));
                        end if;
                    end if;
                end loop;

                /*create temp table tt_sin_presupuesto(
                    id_funcionario      integer,
                    codigo_cc           varchar,
                    centro_costo		varchar,
                    partida 			varchar
                )on commit drop;*/
                for v_record_json in SELECT * FROM jsonb_array_elements(v_parametros.json_beneficiarios)  loop
                    v_record_json_array = '[]'::jsonb;
                    v_record_json_array = v_record_json_array||v_record_json;
                    --raise 'v_record_json: %, json_beneficiarios: %, compara: %',v_record_json_array, v_parametros.json_beneficiarios, v_parametros.json_beneficiarios @> v_record_json_array;
                    select pre.id_centro_costo, pre.id_ot, vf.id_funcionario, vf.desc_funcionario2 funcionario, ('( '||tcc.codigo||' )'||tcc.descripcion) centro_costo, tcc.codigo codigo_cc
                    into v_presupuestos
                    from orga.tuo_funcionario asig
                    inner join orga.vfuncionario vf on vf.id_funcionario = asig.id_funcionario
                    inner join orga.tcargo_presupuesto pre on pre.id_cargo = asig.id_cargo and pre.id_gestion = v_id_gestion
                    and
                    (
                        ((v_record_json->>'fecha_sol')::date between pre.fecha_ini and coalesce(pre.fecha_fin, ('31/12/'||v_gestion)::date))
                        or
                        ((v_record_json->>'fecha_sol')::date between pre.fecha_ini and coalesce(pre.fecha_fin, ('31/12/'||v_gestion)::date))
                    )
                    inner join param.tcentro_costo cc on cc.id_centro_costo = pre.id_centro_costo
                    inner join param.ttipo_cc tcc on tcc.id_tipo_cc = cc.id_tipo_cc
                    where asig.tipo = 'oficial' and asig.id_funcionario = (v_record_json->>'id_funcionario')::integer  and coalesce(asig.fecha_finalizacion,'31/12/9999'::date) >= (v_record_json->>'fecha_sol')::date ;

                    if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_administrativo' and (v_record_json->>'tipo')::varchar = 'administrativo' then
                        v_id_concepto_ingas = 4872;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then

                            v_verificar_id = true;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;

                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                            values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb,'sin_relacion_contable');

                        else
                            if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_administrativo')::numeric then
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                v_verificar_internacional = true;
                                if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 then

                                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                                    from pre.tconcepto_partida par
                                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                                    where par.id_concepto_ingas = 4873 and tp.id_gestion = v_id_gestion;

                                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                                    from tt_saldo_presupuesto tsp
                                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                                    v_verificar_internacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_internacional_administrativo')::numeric;

                                end if;
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                if v_verificar_internacional then
                                    update tt_saldo_presupuesto set
                                        vigente = vigente - (v_record_json->>'monto_nacional_administrativo')::numeric
                                    where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                                    insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                                     id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                                     fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                                    values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_nacional_administrativo')::numeric, v_id_obligacion_pago,
                                            v_presupuestos.id_centro_costo, (v_record_json->>'monto_nacional_administrativo')::numeric, (v_record_json->>'descripcion')::text/*case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Administrativo Nacional '||v_presupuestos.funcionario end*/, now(), v_id_usuario,
                                            null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar,  (v_record_json->>'descripcion')::text
                                    )RETURNING id_obligacion_det into v_id_obligacion_det;

                                    v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                                    v_verificar_id  = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;

                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_con_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;
                                else
                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_sin_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;
                                end if;
                            else

                                v_verificar_id = true;
                                select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                v_verificar_id = false;
                                select tvr.detalle_sin_pago @> v_record_json_array
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                                values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb, 'sin_presupuesto');
                            end if;
                        end if;
                    end if;

                    if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_administrativo' and (v_record_json->>'tipo')::varchar = 'administrativo' then
                        v_id_concepto_ingas = 4873;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then

                            v_verificar_id = true;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;

                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                            values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb,'sin_relacion_contable');

                        else

                            if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_internacional_administrativo')::numeric then

                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                v_verificar_nacional = true;
                                if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 then

                                    --v_id_concepto_ingas = 4814;
                                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                                    from pre.tconcepto_partida par
                                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                                    where par.id_concepto_ingas = 4872 and tp.id_gestion = v_id_gestion;

                                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                                    from tt_saldo_presupuesto tsp
                                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                                    v_verificar_nacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_nacional_administrativo')::numeric;
                                end if;
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                if v_verificar_nacional then
                                    update tt_saldo_presupuesto set
                                        vigente = vigente - (v_record_json->>'monto_internacional_administrativo')::numeric
                                    where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;
                                    insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                                     id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                                     fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                                    values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_internacional_administrativo')::numeric, v_id_obligacion_pago,
                                            v_presupuestos.id_centro_costo, (v_record_json->>'monto_internacional_administrativo')::numeric,  (v_record_json->>'descripcion')::text/*case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Operativo Internacional '||v_presupuestos.funcionario end*/, now(), v_id_usuario,
                                            null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar, (v_record_json->>'descripcion')::text
                                    )RETURNING id_obligacion_det into v_id_obligacion_det;

                                    v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_con_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;
                                else
                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_sin_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;
                                end if;
                            else

                                v_verificar_id = false;
                                select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                v_verificar_id = false;
                                select tvr.detalle_sin_pago @> v_record_json_array
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                                values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb, 'sin_presupuesto');
                            end if;
                        end if;
                    end if;

                    if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_administrativo' and (v_record_json->>'tipo')::varchar = 'entrenamiento' then
                        v_id_concepto_ingas = 2912;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then

                            v_verificar_id = true;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;

                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                            values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb,'sin_relacion_contable');

                        else

                            if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_nacional_administrativo')::numeric then
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                v_verificar_internacional = true;
                                if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 then

                                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                                    from pre.tconcepto_partida par
                                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                                    where par.id_concepto_ingas = 2914 and tp.id_gestion = v_id_gestion;

                                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                                    from tt_saldo_presupuesto tsp
                                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                                    v_verificar_internacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_internacional_administrativo')::numeric;

                                end if;
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                if v_verificar_internacional then
                                    update tt_saldo_presupuesto set
                                        vigente = vigente - (v_record_json->>'monto_nacional_administrativo')::numeric
                                    where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;

                                    insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                                     id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                                     fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                                    values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_nacional_administrativo')::numeric, v_id_obligacion_pago,
                                            v_presupuestos.id_centro_costo, (v_record_json->>'monto_nacional_administrativo')::numeric, (v_record_json->>'descripcion')::text/*case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Administrativo Nacional '||v_presupuestos.funcionario end*/, now(), v_id_usuario,
                                            null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar,  (v_record_json->>'descripcion')::text
                                    )RETURNING id_obligacion_det into v_id_obligacion_det;

                                    v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                                    v_verificar_id  = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;

                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_con_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;
                                else
                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_sin_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;
                                end if;
                            else

                                v_verificar_id = true;
                                select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                v_verificar_id = false;
                                select tvr.detalle_sin_pago @> v_record_json_array
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                                values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb, 'sin_presupuesto');
                            end if;
                        end if;
                    end if;

                    if (v_record_json->>'monto_internacional_administrativo')::numeric > 0 and v_parametros.nombre_origen = 'viatico_administrativo' and (v_record_json->>'tipo')::varchar = 'entrenamiento' then
                        v_id_concepto_ingas = 2914;

                        select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida, v_partida
                        from pre.tconcepto_partida par
                        inner join pre.tpartida tp on tp.id_partida = par.id_partida
                        where par.id_concepto_ingas = v_id_concepto_ingas and tp.id_gestion = v_id_gestion;

                        select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto
                        from tt_saldo_presupuesto tsp
                        where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida;

                        select rel.id_cuenta, rel.id_partida, rel.id_auxiliar
                        into v_relacion_contable
                        from conta.trelacion_contable rel
                        where rel.id_tabla = v_id_concepto_ingas and rel.id_gestion = v_id_gestion and rel.id_partida = v_id_partida and rel.id_centro_costo = v_presupuestos.id_centro_costo;

                        if v_relacion_contable.id_cuenta is null and v_relacion_contable.id_auxiliar is null then

                            v_verificar_id = true;
                            select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;

                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            v_verificar_id = false;
                            select tvr.detalle_sin_pago @> v_record_json_array
                            into v_verificar_id
                            from tes.tplanilla_pvr_sin_pago tvr
                            where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            if not v_verificar_id then
                                update tes.tplanilla_pvr_sin_pago set
                                 detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                            end if;

                            insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                            values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb,'sin_relacion_contable');

                        else

                            if v_saldo_presupuesto.id_centro_costo is not null and v_saldo_presupuesto.id_partida is not null and  v_saldo_presupuesto.vigente >= (v_record_json->>'monto_internacional_administrativo')::numeric then

                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                v_verificar_nacional = true;
                                if (v_record_json->>'monto_nacional_administrativo')::numeric > 0 then

                                    --v_id_concepto_ingas = 4814;
                                    select par.id_partida, ('( '||tp.codigo||' )'||tp.nombre_partida) into v_id_partida_aux, v_partida_aux
                                    from pre.tconcepto_partida par
                                    inner join pre.tpartida tp on tp.id_partida = par.id_partida
                                    where par.id_concepto_ingas = 2912 and tp.id_gestion = v_id_gestion;

                                    select tsp.id_centro_costo, tsp.id_partida, tsp.vigente into v_saldo_presupuesto_aux
                                    from tt_saldo_presupuesto tsp
                                    where tsp.id_centro_costo = v_presupuestos.id_centro_costo and tsp.id_partida = v_id_partida_aux;

                                    v_verificar_nacional = v_saldo_presupuesto_aux.vigente >= (v_record_json->>'monto_nacional_administrativo')::numeric;
                                end if;
                                /********************* Verificar si tiene saldo vigente en pago de viatico internacional *********************/
                                if v_verificar_nacional then
                                    update tt_saldo_presupuesto set
                                        vigente = vigente - (v_record_json->>'monto_internacional_administrativo')::numeric
                                    where id_centro_costo = v_presupuestos.id_centro_costo and id_partida = v_id_partida;
                                    insert into tes.tobligacion_det( estado_reg, id_partida, id_concepto_ingas, monto_pago_mo, id_obligacion_pago,
                                                                     id_centro_costo, monto_pago_mb, descripcion, fecha_reg, id_usuario_reg,
                                                                     fecha_mod, id_usuario_mod, id_orden_trabajo, id_proveedor, id_cuenta, id_auxiliar, registro_viatico_refrigerio)
                                    values( 'activo', v_id_partida, v_id_concepto_ingas, (v_record_json->>'monto_internacional_administrativo')::numeric, v_id_obligacion_pago,
                                            v_presupuestos.id_centro_costo, (v_record_json->>'monto_internacional_administrativo')::numeric,  (v_record_json->>'descripcion')::text/*case when v_parametros.nombre_origen = 'refrigerio' then 'Refrigerio '||v_presupuestos.funcionario else 'Viatico Operativo Internacional '||v_presupuestos.funcionario end*/, now(), v_id_usuario,
                                            null, null, v_presupuestos.id_ot, (v_record_json->>'id_funcionario')::integer, v_relacion_contable.id_cuenta, v_relacion_contable.id_auxiliar, (v_record_json->>'descripcion')::text
                                    )RETURNING id_obligacion_det into v_id_obligacion_det;

                                    v_total_pago = v_total_pago + coalesce((v_record_json->>'monto_total')::numeric,0);

                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_con_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_con_pago tvr
                                    where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_con_pago set
                                         detalle_con_pago =  detalle_con_pago||v_record_json::jsonb
                                        where id_planilla_pvr_con_pago = v_id_planilla_pvr_con_pago;
                                    end if;
                                else
                                    v_verificar_id = false;
                                    select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;

                                    v_verificar_id = false;
                                    select tvr.detalle_sin_pago @> v_record_json_array
                                    into v_verificar_id
                                    from tes.tplanilla_pvr_sin_pago tvr
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    if not v_verificar_id then
                                        update tes.tplanilla_pvr_sin_pago set
                                         detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                        where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                    end if;
                                end if;
                            else

                                v_verificar_id = false;
                                select tvr.ids_funcionario @> (v_record_json->>'id_funcionario')::jsonb
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     ids_funcionario =  ids_funcionario||(v_record_json->>'id_funcionario')::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                v_verificar_id = false;
                                select tvr.detalle_sin_pago @> v_record_json_array
                                into v_verificar_id
                                from tes.tplanilla_pvr_sin_pago tvr
                                where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                if not v_verificar_id then
                                    update tes.tplanilla_pvr_sin_pago set
                                     detalle_sin_pago =  detalle_sin_pago||v_record_json::jsonb
                                    where id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;
                                end if;

                                insert into tes.tt_sin_presupuesto(id_funcionario, codigo_cc, centro_costo, partida, fecha_pago, orden_viaje, motivo)
                                values((v_record_json->>'id_funcionario')::integer, v_presupuestos.codigo_cc, v_presupuestos.centro_costo, v_partida, v_parametros.fecha_pago, v_record_json::jsonb, 'sin_presupuesto');
                            end if;
                        end if;
                    end if;

                end loop;

                update tes.tobligacion_pago set
                    total_pago = v_total_pago
                where id_obligacion_pago = v_id_obligacion_pago;

                select sin.detalle_sin_pago
                into v_record_json_array
                from tes.tplanilla_pvr_sin_pago sin
                where sin.id_planilla_pvr_sin_pago = v_id_planilla_pvr_sin_pago;

                if JSONB_ARRAY_LENGTH(v_parametros.json_beneficiarios) = JSONB_ARRAY_LENGTH(v_record_json_array::jsonb) then
                    raise 'Estimado Usuario: no se puedo generar la Obligacion de Pago para la fecha % para el tipo % porque ninguno de los funcionarios cuenta con presupuesto', v_parametros.fecha_pago, v_parametros.nombre_origen;
                end if;

                v_json_presupuesto = '[';
                for v_presupuestos in select sp.codigo_cc, sp.centro_costo, sp.id_funcionario,  /*pxp.list(*/sp.partida/*) partida*/, sp.motivo, sp.orden_viaje
                                      from tes.tt_sin_presupuesto sp
                                      --group by sp.codigo_cc,sp.centro_costo,sp.id_funcionario, sp.motivo, sp.orden_viaje
                                      order by sp.codigo_cc asc loop
                    --raise notice 'v_presupuestos: %, %, %',v_presupuestos.orden_viaje, (v_presupuestos.orden_viaje)->>'bandera_ov', (v_presupuestos.orden_viaje)->>'id_orden_viaje';
                    v_json_presupuesto = v_json_presupuesto||'{"id_funcionario":'||v_presupuestos.id_funcionario||',"codigo_cc":'||v_presupuestos.codigo_cc||','||'"centro":"'||v_presupuestos.centro_costo||'","partida":"'||v_presupuestos.partida||'","bandera_ov":'||((v_presupuestos.orden_viaje)->>'bandera_ov')::varchar||',"id_orden_viaje":'||((v_presupuestos.orden_viaje)->>'id_orden_viaje')::varchar||',"descripcion":"'||v_presupuestos.motivo||'"},';
                end loop;
                v_json_presupuesto = v_json_presupuesto||']';

                v_json_presupuesto = replace(v_json_presupuesto, ',]', ']');
                v_json_presupuesto = replace(v_json_presupuesto, ',}', '}');

                if v_json_presupuesto = '[]' then
                    v_status = 'exito';
                else
                    v_status = 'observado';
                end if;

                /******************************** PROCESO DE CAMBIO DE ESTADO Y GENERACIÓN CUOTA ********************************/
            	if v_id_obligacion_pago is not null then
              		v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');

                for v_estados in select tte.codigo
                                 from wf.ttipo_proceso  ttp
                                 inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                                 where ttp.codigo = 'PVR' loop

                    select op.id_proceso_wf, op.id_estado_wf, op.estado, op.id_depto, op.tipo_obligacion,
                      op.total_nro_cuota, op.fecha_pp_ini, op.rotacion, op.id_plantilla, op.tipo_cambio_conv,
                      pr.desc_proveedor, op.pago_variable, op.comprometido, op.id_usuario_reg, op.fecha
                    into v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_id_depto, v_tipo_obligacion,
                      v_total_nro_cuota, v_fecha_pp_ini, v_rotacion, v_id_plantilla, v_tipo_cambio_conv,
                      v_desc_proveedor, v_pago_variable, v_comprometido, v_id_usuario_reg_op, v_fecha_op
                    from tes.tobligacion_pago op
                    left join param.vproveedor pr  on pr.id_proveedor = op.id_proveedor
                    where op.id_obligacion_pago = v_id_obligacion_pago;

                    select te.id_tipo_estado
                    into v_id_tipo_estado
                    from wf.testado_wf te
                    inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                    where te.id_estado_wf = v_id_estado_wf;


                    SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                    into v_id_tipo_estado, v_codigo_estado_siguiente
                    from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                    select tft.id_funcionario
                    into v_id_funcionario
                    from wf.ttipo_estado tip
                    inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                    where tip.id_tipo_estado = v_id_tipo_estado
                    limit 1;

                    ---------------------------------------
                    -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                    ---------------------------------------
                    v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                                   v_id_funcionario,
                                                                   v_id_estado_wf,
                                                                   v_id_proceso_wf,
                                                                   p_id_usuario,
                                                                   null::integer,
                                                                   null::varchar,
                                                                   null::integer,
                                                                   'Cambio en Automatico PVR');


                    IF  v_codigo_estado in ('borrador','vbpoa','vbpresupuestos','liberacion' ) THEN
                        --validamos que el detalle tenga por lo menos un item con valor
                        select sum(od.monto_pago_mo)
                        into v_total_detalle
                        from tes.tobligacion_det od
                        where od.id_obligacion_pago = v_id_obligacion_pago and od.estado_reg ='activo';

                        IF v_total_detalle = 0 or v_total_detalle is null THEN
                            raise exception 'No existe el detalle de obligacion...';
                        END IF;
                        ------------------------------------------------------------
                        --calcula el factor de prorrateo de la obligacion  detalle
                        -----------------------------------------------------------
                        IF (tes.f_calcular_factor_obligacion_det(v_id_obligacion_pago) != 'exito')  THEN
                            raise exception 'error al calcular factores';
                        END IF;
                    END IF;

                    update tes.tobligacion_pago  set
                     id_estado_wf =  v_id_estado_actual,
                     estado = v_codigo_estado_siguiente,
                     id_usuario_mod = p_id_usuario,
                     fecha_mod = now()
                    where id_obligacion_pago  = v_id_obligacion_pago;


                    IF  v_codigo_estado_siguiente = 'registrado'  and v_total_nro_cuota > 0 THEN

                        select ps_descuento_porc, ps_descuento, ps_observaciones into v_registros_plan
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
                                v_ultima_cuota = true;
                            END IF;

                            v_descuentos_ley = v_monto_cuota * v_registros_plan.ps_descuento_porc;



                            --(may)tipo de obligacion SIP para  internacionales pago_especial_spi
                            --pago para bol pago_especial

                            IF v_tipo_obligacion in  ('pago_especial') THEN
                                v_tipo_plan_pago = 'especial';
                            ELSIF v_tipo_obligacion in  ('pago_especial_spi') THEN
                                v_tipo_plan_pago = 'especial_spi';
                            ELSE
                                --verifica que tipo de apgo estan deshabilitados
                                va_tipo_pago = regexp_split_to_array(pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'), E'\\s+');
                                v_tipo_plan_pago = 'devengado_pagado';

                                IF v_tipo_plan_pago =ANY(va_tipo_pago) THEN
                                    v_tipo_plan_pago = 'devengado_pagado_1c';
                                END IF;

                                IF v_tipo_obligacion in  ('spd', 'pgaext') THEN
                                    v_tipo_plan_pago = 'devengado_pagado_1c_sp';
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
                                                            'id_obligacion_pago',v_id_obligacion_pago::varchar,
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
                                                            'fecha_tentativa',v_fecha_pp_ini::varchar
                            ]);

                            --TODO,  bloquear en formulario de OP  facturas con monto excento


                            -- si es un proceso de pago unico,  la primera cuota pasa de borrador al siguiente estado de manera automatica
                            IF  ((v_tipo_obligacion = 'pbr' or v_tipo_obligacion = 'ppm' or v_tipo_obligacion = 'pga' or v_tipo_obligacion = 'pce' or v_tipo_obligacion = 'pago_unico' or v_tipo_obligacion = 'spd' or v_tipo_obligacion ='pgaext') and   v_i = 1)   THEN
                               v_sw_saltar = TRUE;
                            else
                               v_sw_saltar = FALSE;
                            END IF;

                            -- llamada para insertar plan de pagos
                            v_resp = tes.f_inserta_plan_pago_dev(p_administrador, v_id_usuario_reg_op,v_hstore_pp, v_sw_saltar);
                            --raise 'v_resp: %', v_resp;
                            -- calcula la fecha para la siguiente insercion
                            v_fecha_pp_ini =  v_fecha_pp_ini + interval  '1 month'*v_rotacion;
                        END LOOP;

                        IF not tes.f_gestionar_presupuesto_tesoreria(v_id_obligacion_pago, p_id_usuario, 'comprometer')  THEN
                            raise exception 'Error al comprometer el presupeusto';
                        END IF;

                        v_comprometido = 'si';
                        --cambia la bandera del comprometido
                        update tes.tobligacion_pago  set
                            comprometido = v_comprometido
                        where id_obligacion_pago  = v_id_obligacion_pago;

                        EXIT;
                    END IF;
                end loop;
                /*********************************************** Plan Pago Next Estado ***********************************************/
                select pp.id_proceso_wf
                into v_id_proceso_wf
                from tes.tplan_pago pp
                where pp.id_obligacion_pago = v_id_obligacion_pago;

               	for v_estados in select tte.codigo
                               from wf.ttipo_proceso  ttp
                               inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                               where ttp.codigo = 'PVR_DEV' loop

                  select pp.id_plan_pago, pp.id_proceso_wf, pp.id_estado_wf, pp.estado, pp.fecha_tentativa, op.numero, pp.total_prorrateado ,
                         pp.monto_ejecutar_total_mo, pp.estado, pp.id_estado_wf, op.tipo_obligacion, pp.id_depto_lb, pp.monto,
                         pp.id_plantilla, pp.id_obligacion_pago, op.num_tramite, pp.tipo, op.id_moneda
                  into   v_id_plan_pago, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_fecha_tentativa, v_num_obliacion_pago, v_total_prorrateo,
                         v_monto_ejecutar_total_mo, v_estado_aux, v_id_estado_actual, v_tipo_obligacion, v_id_depto_lb_pp, v_monto_pp,
                         v_id_plantilla, v_id_obligacion_pago_pp, v_numero_tramite, vtipo_pp, v_id_moneda
                  from tes.tplan_pago  pp
                  inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                  where pp.id_proceso_wf  = v_id_proceso_wf;

                  select te.id_tipo_estado
                  into v_id_tipo_estado
                  from wf.testado_wf te
                  inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                  where te.id_estado_wf = v_id_estado_wf;


                  SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                  into v_id_tipo_estado, v_codigo_estado_siguiente
                  from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                  select tft.id_funcionario, tft.id_depto
                  into v_id_funcionario, v_id_depto
                  from wf.ttipo_estado tip
                  inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                  where tip.id_tipo_estado = v_id_tipo_estado
                  limit 1;
                  --raise 'v_id_tipo_estado: %, v_codigo_estado_siguiente: %, v_id_funcionario: %, v_id_depto: %',v_id_tipo_estado, v_codigo_estado_siguiente, v_id_funcionario, v_id_depto;
                  /*if  v_codigo_estado_siguiente = 'supconta' then
                      raise 'v_id_funcionario: %, v_id_depto: %',v_id_funcionario, v_id_depto;
                  end if;*/
                  ---------------------------------------
                  -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                  ---------------------------------------
                  v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                                 v_id_funcionario,
                                                                 v_id_estado_wf,
                                                                 v_id_proceso_wf,
                                                                 p_id_usuario,
                                                                 null::integer,
                                                                 null::varchar,
                                                                 v_id_depto,
                                                                 'Cambio en Automatico Plan Pago PVR');


                  update tes.tplan_pago  set
                   id_estado_wf =  v_id_estado_actual,
                   estado = v_codigo_estado_siguiente,
                   id_usuario_mod = p_id_usuario,
                   fecha_mod = now(),

                   id_depto_lb = 15,
                   id_cuenta_bancaria = 61,
                   forma_pago = 'transferencia',
                   id_proveedor_cta_bancaria = 652,
                   id_depto_conta = 4

                  where id_obligacion_pago  = v_id_obligacion_pago;

                  if  v_codigo_estado_siguiente = 'vbconta' then
                      EXIT;
                  end if;

              end loop;
			end if;
    	    /*********************************************** Plan Pago Next Estado ***********************************************/
            /******************************** PROCESO DE CAMBIO DE ESTADO Y GENERACIÓN CUOTA ********************************/

            /******************************** PROCESO PARA GENERAR EL COMPROBANTE PRESUPUESTARIO ********************************/
            --validacion de la cuota
            select pp.*,op.total_pago,op.comprometido, op.id_contrato, op.tipo_obligacion
            into v_registros
            from tes.tplan_pago pp
            inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
            where pp.id_obligacion_pago = v_id_obligacion_pago;

			v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');
			IF v_pre_integrar_presupuestos = 'true' THEN
              /*jrr:29/10/2014
              1) si el presupuesto no esta comprometido*/

              if (v_registros.comprometido = 'no') then

                  /*1.1)Validar que la suma de los detalles igualen al total de la obligacion*/
                  if ((select sum(od.monto_pago_mo)
                        from tes.tobligacion_det od
                        where id_obligacion_pago = v_id_obligacion_pago and estado_reg = 'activo') != v_registros.total_pago) THEN
                        raise exception 'La suma de todos los detalles no iguala con el total de la obligacion. La diferencia se genero al modificar la apropiacion';
                  end if;

                  /*1.2 Comprometer*/
                  select * into v_nombre_conexion from migra.f_crear_conexion();

                  select tes.f_gestionar_presupuesto_tesoreria(v_id_obligacion_pago, p_id_usuario, 'comprometer',NULL,v_nombre_conexion) into v_res;

                  if v_res = false then
                      raise exception 'Error al comprometer el presupuesto';
                  end if;

                  update tes.tobligacion_pago
                  set comprometido = 'si'
                  where id_obligacion_pago = v_id_obligacion_pago;
              end if;
	  		END IF;

            --(may) 20-07-2019 los tipo plan de pago devengado_pagado_1c_sp son para las internacionales -tramites sp con contato
            IF  v_registros.tipo  in ('pagado' ,'devengado_pagado','devengado_pagado_1c','anticipo','ant_parcial','devengado_pagado_1c_sp') and v_registros.tipo_obligacion != 'pago_pvr' THEN

              IF v_registros.forma_pago = 'cheque' THEN
              	IF  v_registros.nro_cheque is NULL THEN
                	raise exception  'Tiene que especificar el  nro de cheque';
                END IF;
              ELSE
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
               	select cb.centro
               	into v_centro
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
                   select c.tiene_retencion
                   into v_sw_retenciones
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
                                                      v_registros.id_plan_pago,
                                                      4,
                                                      v_nombre_conexion);
           	end if;
            /******************************** PROCESO PARA GENERAR EL COMPROBANTE PRESUPUESTARIO ********************************/

            else
                raise 'Estimado Usuario: Aun no se cuenta con la implementación de la funcionalidad para fondos en avance.';
            end if;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','El registro se inserto con Exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_id_obligacion_pago::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'nro_tramite',v_num_tramite::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'personal_sin_presupuesto',v_json_presupuesto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'status',v_status);

            --Devuelve la respuesta
            return v_resp;

        end;
    /*********************************
    #TRANSACCION:  'TES_GEN_PP_PVR_IME'
    #DESCRIPCION:	Generar Plan Pago Viaticos y Refrigerios
    #AUTOR:		   franklin.espinoza
    #FECHA:		   01/11/2021 10:28:30
    ***********************************/
    elsif(p_transaccion='TES_GEN_PP_PVR_IME')then
    	begin
    	    v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');
            for v_estados in select tte.codigo
                             from wf.ttipo_proceso  ttp
                             inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                             where ttp.codigo = 'PVR' loop

                select op.id_proceso_wf, op.id_estado_wf, op.estado, op.id_depto, op.tipo_obligacion,
                  op.total_nro_cuota, op.fecha_pp_ini, op.rotacion, op.id_plantilla, op.tipo_cambio_conv,
                  pr.desc_proveedor, op.pago_variable, op.comprometido, op.id_usuario_reg, op.fecha
                into v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_id_depto, v_tipo_obligacion,
                  v_total_nro_cuota, v_fecha_pp_ini, v_rotacion, v_id_plantilla, v_tipo_cambio_conv,
                  v_desc_proveedor, v_pago_variable, v_comprometido, v_id_usuario_reg_op, v_fecha_op
                from tes.tobligacion_pago op
                left join param.vproveedor pr  on pr.id_proveedor = op.id_proveedor
                where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

                select te.id_tipo_estado
                into v_id_tipo_estado
                from wf.testado_wf te
                inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                where te.id_estado_wf = v_id_estado_wf;


                SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                into v_id_tipo_estado, v_codigo_estado_siguiente
                from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                select tft.id_funcionario
                into v_id_funcionario
                from wf.ttipo_estado tip
                inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                where tip.id_tipo_estado = v_id_tipo_estado
                limit 1;

                ---------------------------------------
                -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                ---------------------------------------
                v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                               v_id_funcionario,
                                                               v_id_estado_wf,
                                                               v_id_proceso_wf,
                                                               p_id_usuario,
                                                               null::integer,
                                                               null::varchar,
                                                               null::integer,
                                                               'Cambio en Automatico PVR');


                IF  v_codigo_estado in ('borrador','vbpoa','vbpresupuestos','liberacion' ) THEN
                    --validamos que el detalle tenga por lo menos un item con valor
                    select sum(od.monto_pago_mo)
                    into v_total_detalle
                    from tes.tobligacion_det od
                    where od.id_obligacion_pago = v_parametros.id_obligacion_pago and od.estado_reg ='activo';

                    IF v_total_detalle = 0 or v_total_detalle is null THEN
                        raise exception 'No existe el detalle de obligacion...';
                    END IF;
                    ------------------------------------------------------------
                    --calcula el factor de prorrateo de la obligacion  detalle
                    -----------------------------------------------------------
                    IF (tes.f_calcular_factor_obligacion_det(v_parametros.id_obligacion_pago) != 'exito')  THEN
                        raise exception 'error al calcular factores';
                    END IF;
                END IF;

                update tes.tobligacion_pago  set
                 id_estado_wf =  v_id_estado_actual,
                 estado = v_codigo_estado_siguiente,
                 id_usuario_mod = p_id_usuario,
                 fecha_mod = now()
                where id_obligacion_pago  = v_parametros.id_obligacion_pago;


                IF  v_codigo_estado_siguiente = 'registrado'  and v_total_nro_cuota > 0 THEN

                    select ps_descuento_porc, ps_descuento, ps_observaciones into v_registros_plan
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
                            v_ultima_cuota = true;
                        END IF;

                        v_descuentos_ley = v_monto_cuota * v_registros_plan.ps_descuento_porc;

                        --pago para bol pago_especial
                        IF v_tipo_obligacion in  ('pago_especial') THEN
                            v_tipo_plan_pago = 'especial';
                        ELSIF v_tipo_obligacion in  ('pago_especial_spi') THEN
                            v_tipo_plan_pago = 'especial_spi';
                        ELSE
                            --verifica que tipo de apgo estan deshabilitados
                            va_tipo_pago = regexp_split_to_array(pxp.f_get_variable_global('tes_tipo_pago_deshabilitado'), E'\\s+');
                            v_tipo_plan_pago = 'devengado_pagado';

                            IF v_tipo_plan_pago =ANY(va_tipo_pago) THEN
                                v_tipo_plan_pago = 'devengado_pagado_1c';
                            END IF;

                            IF v_tipo_obligacion in  ('spd', 'pgaext') THEN
                                v_tipo_plan_pago = 'devengado_pagado_1c_sp';
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
                                                        'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar,
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
                                                        'fecha_tentativa',v_fecha_pp_ini::varchar
                        ]);

                        --TODO,  bloquear en formulario de OP  facturas con monto excento


                        -- si es un proceso de pago unico,  la primera cuota pasa de borrador al siguiente estado de manera automatica
                        IF  ((v_tipo_obligacion = 'pbr' or v_tipo_obligacion = 'ppm' or v_tipo_obligacion = 'pga' or v_tipo_obligacion = 'pce' or v_tipo_obligacion = 'pago_unico' or v_tipo_obligacion = 'spd' or v_tipo_obligacion ='pgaext') and   v_i = 1)   THEN
                           v_sw_saltar = TRUE;
                        else
                           v_sw_saltar = FALSE;
                        END IF;

                        -- llamada para insertar plan de pagos
                        v_resp = tes.f_inserta_plan_pago_dev(p_administrador, v_id_usuario_reg_op,v_hstore_pp, v_sw_saltar);
                        -- calcula la fecha para la siguiente insercion
                        v_fecha_pp_ini =  v_fecha_pp_ini + interval  '1 month'*v_rotacion;
                    END LOOP;

                    IF not tes.f_gestionar_presupuesto_tesoreria(v_parametros.id_obligacion_pago, p_id_usuario, 'comprometer')  THEN
                        raise exception 'Error al comprometer el presupeusto';
                    END IF;

                    v_comprometido = 'si';
                    --cambia la bandera del comprometido
                    update tes.tobligacion_pago  set
                        comprometido = v_comprometido
                    where id_obligacion_pago  = v_parametros.id_obligacion_pago;

                    EXIT;
                END IF;
            end loop;

    	    /*********************************************** Plan Pago Next Estado ***********************************************/
    	    select pp.id_proceso_wf
    	    into v_id_proceso_wf
    	    from tes.tplan_pago pp
    	    where pp.id_obligacion_pago = v_parametros.id_obligacion_pago;

    	     for v_estados in select tte.codigo
                             from wf.ttipo_proceso  ttp
                             inner join wf.ttipo_estado tte on tte.id_tipo_proceso = ttp.id_tipo_proceso
                             where ttp.codigo = 'PVR_DEV' loop

                select pp.id_plan_pago, pp.id_proceso_wf, pp.id_estado_wf, pp.estado, pp.fecha_tentativa, op.numero, pp.total_prorrateado ,
                       pp.monto_ejecutar_total_mo, pp.estado, pp.id_estado_wf, op.tipo_obligacion, pp.id_depto_lb, pp.monto,
                       pp.id_plantilla, pp.id_obligacion_pago, op.num_tramite, pp.tipo, op.id_moneda
                into   v_id_plan_pago, v_id_proceso_wf, v_id_estado_wf, v_codigo_estado, v_fecha_tentativa, v_num_obliacion_pago, v_total_prorrateo,
                       v_monto_ejecutar_total_mo, v_estado_aux, v_id_estado_actual, v_tipo_obligacion, v_id_depto_lb_pp, v_monto_pp,
                       v_id_plantilla, v_id_obligacion_pago_pp, v_numero_tramite, vtipo_pp, v_id_moneda
                from tes.tplan_pago  pp
                inner  join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                where pp.id_proceso_wf  = v_id_proceso_wf;

                select te.id_tipo_estado
                into v_id_tipo_estado
                from wf.testado_wf te
                inner join wf.ttipo_estado tip on tip.id_tipo_estado = te.id_tipo_estado
                where te.id_estado_wf = v_id_estado_wf;


                SELECT ps_id_tipo_estado[1], ps_codigo_estado[1]
                into v_id_tipo_estado, v_codigo_estado_siguiente
                from  wf.f_obtener_estado_wf ( v_id_proceso_wf, v_id_estado_wf, v_id_tipo_estado, 'siguiente', p_id_usuario);

                select tft.id_funcionario, tft.id_depto
                into v_id_funcionario, v_id_depto
                from wf.ttipo_estado tip
                inner join wf.tfuncionario_tipo_estado tft on tft.id_tipo_estado = tip.id_tipo_estado
                where tip.id_tipo_estado = v_id_tipo_estado
                limit 1;

                ---------------------------------------
                -- REGISTA EL SIGUIENTE ESTADO DEL WF.
                ---------------------------------------
                v_id_estado_actual = wf.f_registra_estado_wf(  v_id_tipo_estado,
                                                               v_id_funcionario,
                                                               v_id_estado_wf,
                                                               v_id_proceso_wf,
                                                               p_id_usuario,
                                                               null::integer,
                                                               null::varchar,
                                                               v_id_depto,
                                                               'Cambio en Automatico Plan Pago PVR');


                update tes.tplan_pago  set
                 id_estado_wf =  v_id_estado_actual,
                 estado = v_codigo_estado_siguiente,
                 id_usuario_mod = p_id_usuario,
                 fecha_mod = now()
                where id_obligacion_pago  = v_parametros.id_obligacion_pago;

                if  v_codigo_estado_siguiente = 'vbconta' then
                    EXIT;
                end if;

            end loop;
    	    /*********************************************** Plan Pago Next Estado ***********************************************/

    	    -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de la Obligacion de Pagos)');
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
        v_resp = pxp.f_agrega_clave(v_resp,'foo','barr');
        v_resp = pxp.f_agrega_clave(v_resp,'preguntar',v_preguntar);

		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION tes.ft_obligacion_pago_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
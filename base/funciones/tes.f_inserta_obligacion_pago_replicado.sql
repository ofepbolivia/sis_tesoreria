CREATE OR REPLACE FUNCTION tes.f_inserta_obligacion_pago_replicado (
  p_administrador integer,
  p_id_usuario integer,
  p_hstore public.hstore,
  p_salta boolean = false
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		tes.f_inserta_obligacion_pago_replicado
 DESCRIPCION:   Inserta registro de cotizacion
 AUTOR: 		Alan Kevin Felipez Gutierrez
 FECHA:	        15-11-2019
 COMENTARIOS:	esta funcion duplica una obligacion de pago a partir del id_obligacion_pago
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

    v_resp		           			varchar;
	v_nombre_funcion       			text;

    v_id_obligacion_pago 			integer;
    v_id_periodo					integer;
    v_num  						    varchar;
    v_tipo_documento  			    varchar;
    v_codigo_proceso_macro 		    varchar;
    va_id_funcionario_gerente       INTEGER[];
    v_id_proceso_macro 			    integer;
    v_codigo_tipo_proceso 			varchar;
    v_id_gestion 				    integer;
    v_num_tramite  				    varchar;
    v_id_estado_wf 					integer;
    v_id_proceso_wf 				integer;
    v_codigo_estado					varchar;
    v_anio_ges						integer;
    v_detalle_obligacion			record;
    v_registros_documento 			record;
    v_registros_con 				record;
    v_resp_doc   					boolean;
    v_id_documento_wf_op 		integer;

BEGIN

          v_nombre_funcion = 'tes.f_inserta_obligacion_pago_replicado';
         --buscamos el periodo correspondiente de la obligacion de pago a duplicar
         select id_periodo into v_id_periodo
         from   param.tperiodo per
         where per.fecha_ini <= now()::date
           and per.fecha_fin >= now()::date
           limit 1 offset 0;


         --datos de numero de tramite
         IF   (p_hstore->'tipo_obligacion')::varchar = 'adquisiciones'    THEN
                 raise exception 'Los pagos de adquisiciones tienen que ser habilitados desde el sistema de adquisiciones';

         ELSIF   (p_hstore->'tipo_obligacion')::varchar  in ('pago_directo','pago_unico','pago_especial', 'pga', 'ppm', 'pce', 'pbr', 'sp', 'spd','spi', 'pago_especial_spi')    THEN


                     IF (p_hstore->'tipo_obligacion')::varchar  = 'pago_directo' and p_administrador != 2 THEN
                          v_tipo_documento = pxp.f_get_variable_global('tes_tipo_documento_pago_directo'); --'PGD';
                          v_codigo_proceso_macro = pxp.f_get_variable_global('tes_codigo_macro_pago_directo');--'TES-PD';
                     ELSIF(p_hstore->'tipo_obligacion')::varchar  = 'pago_unico' and p_administrador != 2 THEN
                          v_tipo_documento = pxp.f_get_variable_global('tes_tipo_documento_pago_unico');--'PU';
                          v_codigo_proceso_macro = pxp.f_get_variable_global('tes_codigo_macro_pago_unico');--'PU';
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'pga') THEN
                          v_tipo_documento = 'PGA';
                          v_codigo_proceso_macro = 'PGA';
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'ppm') THEN
                          v_tipo_documento = 'PPM';
                          v_codigo_proceso_macro = 'PPM';
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'pce') THEN
                          v_tipo_documento = 'PCE';
                          v_codigo_proceso_macro = 'PCE';
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'pbr') THEN
                          v_tipo_documento = 'BR';
                          v_codigo_proceso_macro = 'BR';
                     --para las intenacionales SP, SPD, SPI
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'sp') THEN
                          v_tipo_documento = 'SP';
                          v_codigo_proceso_macro = 'SP';
                     ELSIF(p_hstore->'tipo_obligacion')::varchar  = 'spd' and p_administrador != 2 THEN
                          v_tipo_documento = 'SPD';--'SPD';
                          v_codigo_proceso_macro = 'SPD';--pxp.f_get_variable_global('tes_codigo_macro_sol_pago_unico');--'PU';
                     ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'spi') THEN
                          v_tipo_documento = 'SPI';
                          v_codigo_proceso_macro = 'SPI';
                      ELSIF(p_administrador = 2 OR (p_hstore->'tipo_obligacion')::varchar  = 'pago_especial_spi') THEN
                          v_tipo_documento = 'SP';
                          v_codigo_proceso_macro = 'SP';
                     ELSE
                          v_tipo_documento =  pxp.f_get_variable_global('tes_tipo_documento_especial'); --'PE';
                          v_codigo_proceso_macro = pxp.f_get_variable_global('tes_codigo_macro_especial');--'TES-PD';
                     END IF;

                    --obtener el correlativo segun el tipo de documento
                    v_num =   param.f_obtener_correlativo(
                               v_tipo_documento,
                               v_id_periodo,-- par_id,
                               NULL, --id_uo
                               (p_hstore->'id_depto')::integer,    -- id_depto
                               p_id_usuario,
                               'TES',
                               NULL);

                    --si el funcionario que solicita es un gerente .... es el mimso encargado de aprobar

                     IF exists(select 1 from orga.tuo_funcionario uof
                               inner join orga.tuo uo on uo.id_uo = uof.id_uo and uo.estado_reg = 'activo'
                               inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional and no.numero_nivel in (3)
                               where  uof.estado_reg = 'activo' and  uof.id_funcionario = (p_hstore->'id_funcionario')::integer ) THEN

                          va_id_funcionario_gerente[1] = (p_hstore->'id_funcionario')::integer;

                     ELSE
                        --si tiene funcionario identificar el gerente correspondientes
                        IF (p_hstore->'id_funcionario')::integer  is not NULL THEN

                            SELECT
                               pxp.aggarray(id_funcionario)
                             into
                               va_id_funcionario_gerente
                             FROM orga.f_get_aprobadores_x_funcionario((p_hstore->'fecha')::date,  (p_hstore->'id_funcionario')::integer , 'todos', 'si', 'todos', 'ninguno') AS (id_funcionario integer);
                            --NOTA el valor en la primera posicion del array es el genre de menor nivel
                        END IF;
                    END IF;

                     --id_subsistema htore subsitema

            ELSE

              raise exception 'falta agregar la funcionalidad';

            END IF;


           IF (v_num is NULL or v_num ='') THEN

              raise exception 'No se pudo obtener un numero correlativo para la obligación';

            END IF;

            --obtener id del proceso macro
            select
             pm.id_proceso_macro
            into
             v_id_proceso_macro
            from wf.tproceso_macro pm
            where pm.codigo = v_codigo_proceso_macro;


            If v_id_proceso_macro is NULL THEN
             raise exception 'El proceso macro  de codigo % no esta configurado en el sistema WF',v_codigo_proceso_macro;
            END IF;

              --obtener el codigo del tipo_proceso

            select   tp.codigo
                into v_codigo_tipo_proceso
            from  wf.ttipo_proceso tp
            where   tp.id_proceso_macro = v_id_proceso_macro
                    and tp.estado_reg = 'activo' and tp.inicio = 'si';


            IF v_codigo_tipo_proceso is NULL THEN
               raise exception 'No existe un proceso inicial para el proceso macro indicado % (Revise la configuración)',v_codigo_proceso_macro;
            END IF;

            select
             ges.id_gestion
            into
              v_id_gestion
            from param.tgestion ges
            where ges.gestion = (date_part('year', (now())::date))::integer
            limit 1 offset 0;



            -- inciar el tramite en el sistema de WF
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
                   (p_hstore->'id_usuario_ai')::integer,
                   case when (p_hstore->'usuario_ai')::varchar = '' then NULL else (p_hstore->'_nombre_usuario_ai')::varchar end,
                   --(p_hstore->'_nombre_usuario_ai')::varchar,
                   v_id_gestion,
                   v_codigo_tipo_proceso,
                   (p_hstore->'id_funcionario')::integer,
                   (p_hstore->'id_depto')::integer,
                   'Obligacion de pago ('||v_num||') '||(p_hstore->'obs')::varchar,
                   v_num );
         	 --control fecha inicio y fin
              IF ((p_hstore->'fecha_costo_ini_pp')::date > (p_hstore->'fecha_costo_fin_pp')::date) then
                  raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
              END IF;

              --control de fecha inicio y fin que correspondan a la gestion de fecha sol
              v_anio_ges = date_part('year',(now())::date);
              IF NOT ((date_part('year',(p_hstore->'fecha_costo_ini_pp')::date) = v_anio_ges) and (date_part('year',(p_hstore->'fecha_costo_fin_pp')::date)=v_anio_ges)) THEN
                 raise exception 'LA FECHA INICIO Y FECHA FIN NO CORRESPONDEN A LA GESTION DE LA FECHA SOLICITADA. FECHA SOL: % PERTENECE A LA GESTION: %',(now())::date,v_anio_ges;
              END IF;


		   --Sentencia de la insercion
                insert into tes.tobligacion_pago(
                id_proveedor,
                estado,
                tipo_obligacion,
                id_moneda,
                obs,
                --porc_retgar,
                id_subsistema,
                id_funcionario,
                estado_reg,
                --porc_anticipo,
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
                rotacion,
                id_plantilla,
                id_usuario_ai,
                usuario_ai,
                tipo_anticipo,
                id_funcionario_gerente,
                id_contrato,
                fecha_costo_ini_pp,
                fecha_costo_fin_pp,
                fecha_conclusion_pago,
                total_pago

                ) values(
                (p_hstore->'id_proveedor')::integer,
                v_codigo_estado,
                (p_hstore->'tipo_obligacion')::varchar,
                (p_hstore->'id_moneda')::integer,
                (p_hstore->'obs')::varchar,
                --v_parametros.porc_retgar,
                (p_hstore->'id_subsistema')::integer,
                (p_hstore->'id_funcionario')::integer,
                'activo',
                --v_parametros.porc_anticipo,
                v_id_estado_wf,
                (p_hstore->'id_depto')::integer,
                v_num_tramite,
                v_id_proceso_wf,
                now(),
                p_id_usuario,
                null,
                null,
                v_num,
                (now())::date,
                v_id_gestion,
                case when (p_hstore->'tipo_cambio_conv')::varchar !='' then (p_hstore->'tipo_cambio_conv')::integer else null end,                -->  TIPO cambio convenido ....
                (p_hstore->'pago_variable')::varchar,
                (p_hstore->'total_nro_cuota')::integer,
                (p_hstore->'fecha_pp_ini')::date,
                (p_hstore->'rotacion')::integer,
                (p_hstore->'id_plantilla')::integer,
                (p_hstore->'id_usuario_ai')::integer,
                (p_hstore->'usuario_ai')::varchar,
                (p_hstore->'tipo_anticipo')::varchar,
                 va_id_funcionario_gerente[1],
                (p_hstore->'id_contrato')::integer,
                (p_hstore->'fecha_costo_ini_pp')::date,
                (p_hstore->'fecha_costo_fin_pp')::date,
                (p_hstore->'fecha_conclusion_pago')::date,
				(p_hstore->'total_pago')::numeric
                )RETURNING id_obligacion_pago into v_id_obligacion_pago;


                  --------------------------------------------------
                  -- Inserta detalle de obligacion automatica
                  ------------------------------------------------
                  for v_detalle_obligacion in (select obdet.*
                  								from tes.tobligacion_det obdet
                                                where obdet.id_obligacion_pago=(p_hstore->'id_obligacion_pago')::integer)loop
                  		insert into tes.tobligacion_det(
                                            estado_reg,
                                            id_cuenta,
                                            id_partida,
                                            id_auxiliar,
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
                                          values(
                                            'activo',
                                            v_detalle_obligacion.id_cuenta,
                                            v_detalle_obligacion.id_partida,
                                            v_detalle_obligacion.id_auxiliar,
                                            v_detalle_obligacion.id_concepto_ingas,
                                            v_detalle_obligacion.monto_pago_mo,
                                            v_id_obligacion_pago,--nueva obligacion de pago
                                            v_detalle_obligacion.id_centro_costo,
                                            v_detalle_obligacion.monto_pago_mb,
                                            v_detalle_obligacion.descripcion,
                                            now(),
                                            p_id_usuario,
                                            null,
                                            null,
                                            v_detalle_obligacion.id_orden_trabajo
                                            );
                  end loop;


				--actualizar fecha inicio y fecha fin de forma automatica al pasarse a un PGA
                    update tes.tobligacion_pago set
                    fecha_costo_ini_pp = case when p_administrador = 2 then (p_hstore->'fecha')::date else (p_hstore->'fecha_costo_ini_pp')::date end,
                    fecha_costo_fin_pp = case when p_administrador = 2 then (p_hstore->'fecha')::date else (p_hstore->'fecha_costo_fin_pp')::date end
                    where
                    id_proceso_wf = v_id_proceso_wf;

                -- inserta documentos en estado borrador si estan configurados
                v_resp_doc =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);
                -- verificar documentos
                v_resp_doc = wf.f_verifica_documento(p_id_usuario, v_id_estado_wf);
                -------------------------------------
                --CONTRATOS
                -------------------------------------

                --  Si el la referencia al contrato esta presente ..  copiar el documento de contrato
                IF (p_hstore->'id_contrato')::integer  is not  NULL   and (p_hstore->'tipo_obligacion')::varchar != 'adquisiciones'  THEN
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
                     WHERE con.id_contrato = (p_hstore->'id_contrato')::integer;

                      -- octenemos el documentos constro del origen

                      SELECT
                        *
                      into
                       v_registros_documento
                      FROM wf.tdocumento_wf d
                      INNER JOIN wf.ttipo_documento td on td.id_tipo_documento = d.id_tipo_documento
                      WHERE td.codigo = 'CONTRATO' and
                            d.id_proceso_wf = v_registros_con.id_proceso_wf;

                       -- copiamos el link de referencia del contrato de la obligacion de pago
                         select
                         dwf.id_documento_wf
                        into
                         v_id_documento_wf_op
                        from wf.tdocumento_wf dwf
                        inner  join  wf.ttipo_documento td on td.id_tipo_documento = dwf.id_tipo_documento
                        where td.codigo = 'CONTRATO'  and dwf.id_proceso_wf = v_id_proceso_wf;

                        --modificacion de doc chequeado_fisico que no obligue para los internacionales sp
						IF ((p_hstore->'tipo_obligacion') in ('sp', 'spd','spi')) THEN

                            UPDATE
                                wf.tdocumento_wf
                              SET
                                 id_usuario_mod = p_id_usuario,
                                 fecha_mod = now(),
                                 chequeado = v_registros_documento.chequeado,
                                 url = v_registros_documento.url,
                                 extension = v_registros_documento.extension,
                                 obs = v_registros_documento.obs,
                                 --chequeado_fisico = v_registros_documento.chequeado_fisico,
                                 id_usuario_upload = v_registros_documento.id_usuario_upload,
                                 fecha_upload = v_registros_documento.fecha_upload,
                                 id_proceso_wf_ori = v_registros_documento.id_proceso_wf,
                                 id_documento_wf_ori = v_registros_documento.id_documento_wf,
                                 nro_tramite_ori = v_registros_con.nro_tramite
                              WHERE
                                id_documento_wf = v_id_documento_wf_op;

                        ELSE
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
                 v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Obligaciones de Pago almacenado(a) con exito (id_obligacion_pago'||v_id_obligacion_pago||')');
                 v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago', v_id_obligacion_pago::varchar);
                 v_resp = pxp.f_agrega_clave(v_resp,'id_proceso_wf', v_id_proceso_wf::varchar);
                 v_resp = pxp.f_agrega_clave(v_resp,'id_estado_wf', v_id_estado_wf::varchar);
                 v_resp = pxp.f_agrega_clave(v_resp,'num_tramite', v_num_tramite::varchar);
                 v_resp = pxp.f_agrega_clave(v_resp,'id_gestion', v_id_gestion::varchar);

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

ALTER FUNCTION tes.f_inserta_obligacion_pago_replicado (p_administrador integer, p_id_usuario integer, p_hstore public.hstore, p_salta boolean)
  OWNER TO postgres;
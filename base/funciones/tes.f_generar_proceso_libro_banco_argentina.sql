CREATE OR REPLACE FUNCTION tes.f_generar_proceso_libro_banco_argentina (
  p_id_usuario integer,
  p_id_int_comprobante integer,
  p_id_finalidad integer,
  p_id_cbte_endesis integer = NULL::integer,
  p_c31 varchar = ''::character varying,
  p_origen varchar = 'endesis'::character varying
)
RETURNS varchar AS
$body$
/*
	Autor: BVP
    Fecha: 05-12-2019
    Descripción: Función que se encarga de generar la insercion en libro de banco.
*/
DECLARE
	v_posicion_inicial				integer;
    v_posicion_final				integer;
    v_id_estado_wf					integer;
    v_id_proceso_wf					integer;
    
    v_datos_debito					record;
    
    v_respuesta_flujo				varchar;
    v_resp							varchar;
    v_nombre_funcion   				varchar;    
    v_id_deposito					varchar;
    v_respuesta						varchar;
    v_sistema_origen				varchar;
    v_forma_pago					varchar;
    
    
BEGIN



     v_nombre_funcion:='tes.f_generar_proceso_libro_banco_argentina';

     --si el origen es endesis
    if p_origen  = 'nacional' then
    	v_sistema_origen = 'KERP';
    ELSE
    	v_sistema_origen = 'KERP_INT';
    end if;
       --(breydi.vasquez) 
       select tt.forma_pago
       into v_forma_pago
       from conta.tint_comprobante tc
       inner join conta.tint_transaccion tt on tt.id_int_comprobante = tc.id_int_comprobante
       where tc.id_int_comprobante = p_id_int_comprobante and tt.forma_pago is not null limit 1;


       select COALESCE(tra.nombre_cheque_trans,cbte.beneficiario) as beneficiario, 
       		  dpcb.id_depto as id_depto_libro,
       		  cbte.glosa1 as glosa, 
              tra.importe_haber, 
              tra.id_cuenta_bancaria,
       		  substr(depto.codigo, 4) as origen, 
              cbte.nro_tramite, 
              tra.id_cuenta_bancaria_mov as id_libro_bancos_deposito,
              cbte.fecha
       into v_datos_debito
       from conta.tint_comprobante cbte
       inner join conta.tint_transaccion tra on tra.id_int_comprobante = cbte.id_int_comprobante
       left join tes.tdepto_cuenta_bancaria dpcb on dpcb.id_cuenta_bancaria = tra.id_cuenta_bancaria
       left join param.tdepto depto on depto.id_depto=dpcb.id_depto
        where cbte.id_int_comprobante = p_id_int_comprobante and tra.forma_pago = v_forma_pago;

		if(v_datos_debito.id_cuenta_bancaria is null)then
        	raise exception 'El comprobante % no cuenta con el id_cuenta_bancaria', p_id_int_comprobante;
        end if;

        if(v_datos_debito.id_depto_libro is null)then
        	raise exception 'El comprobante % no cuenta con el id_depto_libro', p_id_int_comprobante;
        end if;

        IF(v_datos_debito.id_libro_bancos_deposito is null)THEN
	
        v_resp = pxp.f_intermediario_ime(p_id_usuario::int4,NULL,NULL::varchar,'v58gc566o75102428i2usu08i4',13313,'172.17.45.202','99:99:99:99:99:99','tes.ft_ts_libro_bancos_ime','TES_LBAN_INS',NULL,'no',NULL,
        			array['filtro','ordenacion','dir_ordenacion','puntero','cantidad','_id_usuario_ai','_nombre_usuario_ai','id_cuenta_bancaria','id_depto',  'fecha',  'a_favor','nro_cheque','importe_deposito','nro_liquidacion','detalle','origen','observaciones','importe_cheque','id_libro_bancos_fk','nro_comprobante','comprobante_sigma','tipo','id_finalidad','sistema_origen','id_int_comprobante','fecha_pago', 'debito_argentina'],
                    array[' 0 = 0 ','','','','','NULL','NULL', v_datos_debito.id_cuenta_bancaria::varchar,v_datos_debito.id_depto_libro::varchar,  v_datos_debito.fecha::varchar,   v_datos_debito.beneficiario::varchar,'NULL','0','','PAGO A '||v_datos_debito.glosa::varchar,v_datos_debito.origen::varchar,v_datos_debito.nro_tramite::varchar,v_datos_debito.importe_haber::varchar,'NULL','','C31-'||p_c31,v_forma_pago::varchar,p_id_finalidad::varchar,v_sistema_origen::varchar,''||p_id_int_comprobante::varchar||'', v_datos_debito.fecha::varchar, 'si'::varchar],
                    array['varchar','varchar','varchar','integer','integer','int4','varchar', 'int4','int4',  'varchar',  'varchar','int4','numeric','varchar','text','varchar','text','numeric','int4','varchar','varchar','varchar','int4','varchar','varchar','varchar', 'varchar']
                    ,'',NULL,NULL);
        ELSE
        v_resp = pxp.f_intermediario_ime(p_id_usuario::int4,NULL,NULL::varchar,'v58gc566o75102428i2usu08i4',13313,'172.17.45.202','99:99:99:99:99:99','tes.ft_ts_libro_bancos_ime','TES_LBAN_INS',NULL,'no',NULL,
        			array['filtro','ordenacion','dir_ordenacion','puntero','cantidad','_id_usuario_ai','_nombre_usuario_ai','id_cuenta_bancaria','id_depto', 'fecha', 'a_favor','nro_cheque','importe_deposito','nro_liquidacion','detalle','origen','observaciones','importe_cheque','id_libro_bancos_fk','nro_comprobante','comprobante_sigma','tipo','id_finalidad','sistema_origen','id_int_comprobante','fecha_pago', 'debito_argentina'],
                    array[' 0 = 0 ','','','','','NULL','NULL',v_datos_debito.id_cuenta_bancaria::varchar,v_datos_debito.id_depto_libro::varchar, v_datos_debito.fecha::varchar, v_datos_debito.beneficiario::varchar,'NULL','0','','PAGO A '||v_datos_debito.glosa::varchar,v_datos_debito.origen::varchar,v_datos_debito.nro_tramite::varchar,v_datos_debito.importe_haber::varchar,v_datos_debito.id_libro_bancos_deposito::varchar,'','C31-'||p_c31,v_forma_pago::varchar,p_id_finalidad::varchar,v_sistema_origen::varchar,''||p_id_int_comprobante::varchar||'', v_datos_debito.fecha::varchar, 'si'::varchar],
                    array['varchar','varchar','varchar','integer','integer','int4','varchar','int4','int4', 'varchar'  ,'varchar','int4','numeric','varchar','text','varchar','text','numeric','int4','varchar','varchar','varchar','int4','varchar','varchar', 'varchar']
                    ,'',NULL,NULL);
        END IF;
		
        v_respuesta = substring(v_resp from '%#"tipo_respuesta":"_____"#"%' for '#');

        IF v_respuesta = 'tipo_respuesta":"ERROR"' THEN
            v_posicion_inicial = position('"mensaje":"' in v_resp) + 11;
            v_posicion_final = position('"codigo_error":' in v_resp) - 2;
            RAISE EXCEPTION 'No se pudo ingresar el cheque en libro de bancos ERP-BOA: mensaje: %',substring(v_resp from v_posicion_inicial for (v_posicion_final-v_posicion_inicial));
        ELSE
            v_posicion_inicial = position('"id_libro_bancos":"' in v_resp) + 19;
            v_posicion_final = position('"}' in v_resp);
            v_id_deposito = substring(v_resp from v_posicion_inicial for (v_posicion_final-v_posicion_inicial));

            -- captura de proceso y estado para generar su wokflow solo para forma de pago tipo cheque
           if v_forma_pago = 'debito_automatico' then
              
                  select 
                         tes.id_estado_wf,
                         tes.id_proceso_wf
                  into   v_id_estado_wf,
                         v_id_proceso_wf	
                  from tes.tts_libro_bancos tes
                  where tes.id_libro_bancos = v_id_deposito::integer;
                  
                  if v_id_proceso_wf is not null then 

                      -- funcion que genera el flujo 
                      v_respuesta_flujo = tes.f_generar_flujo_wf_debito_automatico_libro_bancos(p_id_usuario, v_id_estado_wf, v_id_proceso_wf, 545);

                  end if;           
          end if;
        END IF;--fin error respuesta



    v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Cheque generado');
    v_respuesta = pxp.f_agrega_clave(v_respuesta,'operacion','cambio_exitoso');
    v_respuesta = pxp.f_agrega_clave(v_respuesta,'id_libro_bancos',v_id_deposito);

    return v_respuesta;

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

ALTER FUNCTION tes.f_generar_proceso_libro_banco_argentina (p_id_usuario integer, p_id_int_comprobante integer, p_id_finalidad integer, p_id_cbte_endesis integer, p_c31 varchar, p_origen varchar)
  OWNER TO "postgres";
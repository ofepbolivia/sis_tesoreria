CREATE OR REPLACE FUNCTION tes.f_generar_flujo_wf_debito_automatico_libro_bancos (
  p_id_usuario integer,
  p_id_estado_wf_act integer,
  p_id_proceso_wf_act integer,
  p_id_tipo_estado integer
)
RETURNS varchar AS
$body$
/*
	Autor: BVP
    Fecha: 05-12-2019
    Descripción: Función que se encarga de generar solo el flujo automatico de la forma de pagot tipo debito_automatico.
*/
DECLARE

    v_resp							varchar;
    v_nombre_funcion   				varchar;
    v_datos_cheque					record;
	v_posicion_inicial				integer;
    v_posicion_final				integer;
    v_id_deposito					varchar;
    v_respuesta						varchar;
    v_sistema_origen				varchar;
    v_forma_pago					varchar;
BEGIN



     v_nombre_funcion:='tes.f_generar_flujo_wf_debito_automatico_libro_bancos';

     v_resp = pxp.f_intermediario_ime(p_id_usuario::int4, NULL, NULL::varchar,'3pbf87vuncup4ul1b34r0hn1e0', 18542, '172.17.45.185', '99:99:99:99:99:99','tes.ft_ts_libro_bancos_ime', 'TES_SIGELB_IME', NULL, 'no', NULL, 
              array ['filtro', 'ordenacion', 'dir_ordenacion', 'puntero', 'cantidad','_id_usuario_ai', '_nombre_usuario_ai', 'id_proceso_wf_act','id_estado_wf_act', 'id_tipo_estado', 'id_funcionario_wf', 'id_depto_wf','obs', 'json_procesos' ], 
              array [ ' 0 = 0 ', '', '', '', '', 'NULL','NULL', p_id_proceso_wf_act::varchar, p_id_estado_wf_act::varchar, p_id_tipo_estado::varchar, '', '', ' ', '[]' ], 
              array ['varchar', 'varchar', 'varchar', 'integer', 'integer', 'int4', 'varchar','int4', 'int4', 'int4', 'int4', 'int4', 'text', 'text' ],
               '',null,null);
     

     v_respuesta = substring(v_resp from '%#"tipo_respuesta":"_____"#"%' for '#');

      IF v_respuesta = 'tipo_respuesta":"ERROR"' THEN
          v_posicion_inicial = position('"mensaje":"' in v_resp) + 11;
          v_posicion_final = position('"codigo_error":' in v_resp) - 2;
          RAISE EXCEPTION 'No se pudo generar el flujo en libro de bancos ERP-BOA: mensaje: %',substring(v_resp from v_posicion_inicial for (v_posicion_final-v_posicion_inicial));
      END IF;--fin error respuesta


    v_respuesta = pxp.f_agrega_clave(v_respuesta,'mensaje','Flujo generado');
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

ALTER FUNCTION tes.f_generar_flujo_wf_debito_automatico_libro_bancos (p_id_usuario integer, p_id_estado_wf_act integer, p_id_proceso_wf_act integer, p_id_tipo_estado integer)
  OWNER TO "postgres";
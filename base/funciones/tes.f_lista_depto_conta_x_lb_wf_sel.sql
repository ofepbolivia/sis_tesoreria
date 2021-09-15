CREATE OR REPLACE FUNCTION tes.f_lista_depto_conta_x_lb_wf_sel (
  p_id_usuario integer,
  p_id_tipo_estado integer,
  p_fecha date = now(),
  p_id_estado_wf integer = NULL::integer,
  p_count boolean = false,
  p_limit integer = 1,
  p_start integer = 0,
  p_filtro varchar = '0=0'::character varying
)
RETURNS SETOF record AS
$body$
/**************************************************************************
 SISTEMA ENDESIS - SISTEMA DE ...
***************************************************************************
 SCRIPT: 		adq.f_lista_depto_conta_x_lb_wf_sel
 DESCRIPCIÓN: 	Lista los departmatos de tesoreia que coinciden con la EP y UP de la cotizacion adjudicada
 AUTOR: 		Rensi Arteaga Copari
 FECHA:			29/04/2014
 COMENTARIOS:	
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCIÓN:
 AUTOR:       
 FECHA:      

***************************************************************************/

-------------------------
-- CUERPO DE LA FUNCIÓN --
--------------------------

-- PARÁMETROS FIJOS
/*


  p_id_usuario integer,                                identificador del actual usuario de sistema
  p_id_tipo_estado integer,                            idnetificador del tipo estado del que se quiere obtener el listado de funcionario  (se correponde con tipo_estado que le sigue a id_estado_wf proporcionado)                       
  p_fecha date = now(),                                fecha  --para verificar asginacion de cargo con organigrama
  p_id_estado_wf integer = NULL::integer,              identificaro de estado_wf actual en el proceso_wf
  p_count boolean = false,                             si queremos obtener numero de funcionario = true por defecto false
  p_limit integer = 1,                                 los siguiente son parametros para filtrar en la consulta
  p_start integer = 0,
  p_filtro varchar = '0=0'::character varying




*/

DECLARE
	
    v_depto_asignacion    varchar;
    v_nombre_depto_func_list   varchar;
    
    v_consulta varchar;
    v_nombre_funcion varchar;
    v_resp varchar;
    
    
     v_cad_ep varchar;
     v_cad_uo varchar;
    v_id_tabla   integer;
    
    v_a_eps varchar[];
    v_a_uos varchar[];
    v_uos_eps varchar;
    v_size    integer;
    v_i       integer;
    v_codigo_subsistema	varchar;
    v_id_deptos_conta	varchar;
    v_registros_depto   record;
    g_registros 		record;
    --08-09-2021 (may)
    v_id_depto_op		integer;

BEGIN
  v_nombre_funcion ='tes.f_lista_depto_conta_x_lb_wf_sel';

    --recuperamos la cotizacion a partir del id_estado_wf
    --validar de que sistema es el estado_wf
    
    select 
      pp.id_depto_conta,
      pp.id_depto_lb,
      dep.id_lugares
    into
      v_registros_depto
    from tes.tplan_pago pp
    inner join param.tdepto dep on dep.id_depto = pp.id_depto_lb
    where pp.id_estado_wf =  p_id_estado_wf;
    
    --08-09-2021 (may) para buscar su id_depto OP
    select  op.id_depto
	into v_id_depto_op
    from tes.tplan_pago pp
    inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
    where pp.id_estado_wf = p_id_estado_wf;

    v_id_deptos_conta = '0';
   
    
    -- si tenemos libro de banco solo mostramos los depto de conta con el mismo lugar
    IF  v_registros_depto.id_depto_lb is not NULL  THEN
       
       select
         pxp.list(dep.id_depto::varchar)
       into
         v_id_deptos_conta
       from param.tdepto dep
       inner join segu.tsubsistema sub on sub.id_subsistema = dep.id_subsistema and sub.codigo = 'CONTA'
       where dep.id_lugares && v_registros_depto.id_lugares;
    
    
    ELSE
    --si no tenemos libro de bancos mostramos todos los deptos
    	select
         pxp.list(dep.id_depto::varchar)
       into
         v_id_deptos_conta
       from param.tdepto dep
       inner join segu.tsubsistema sub on sub.id_subsistema = dep.id_subsistema and sub.codigo = 'CONTA'
       left join param.tdepto_depto depd on depd.id_depto_destino = dep.id_depto
       where depd.id_depto_origen = v_id_depto_op ;
    
    
    END IF;
     
     
   
    IF not p_count then
    
    
              v_consulta:='SELECT 
                              DISTINCT (DEPPTO.id_depto),
                               DEPPTO.codigo as codigo_depto,
                               DEPPTO.nombre_corto as nombre_corto_depto,
                               DEPPTO.nombre as nombre_depto,
                               1 as prioridad,
                               SUBSIS.nombre as subsistema
                            FROM param.tdepto DEPPTO
                            INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
                            WHERE  DEPPTO.estado_reg = ''activo'' and
                               (
                                DEPPTO.id_depto in ('|| v_id_deptos_conta ||')
                               )
                               and '||p_filtro||'
                      limit '|| p_limit::varchar||' offset '||p_start::varchar; 
                      
                   
                                          
                   FOR g_registros in execute (v_consulta)LOOP     
                     RETURN NEXT g_registros;
                   END LOOP;
                      
      ELSE
                  v_consulta='select
                                  COUNT(DEPPTO.id_depto) as total
                                  FROM param.tdepto DEPPTO
                                  INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
                                where DEPPTO.estado_reg = ''activo'' and
                                
                                (
                                         DEPPTO.id_depto in ('|| v_id_deptos_conta ||')
                                 )
                                and '||p_filtro;   
                                          
                   FOR g_registros in execute (v_consulta)LOOP     
                     RETURN NEXT g_registros;
                   END LOOP;
                       
                       
    END IF;
               
        
       
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
COST 100 ROWS 1000;
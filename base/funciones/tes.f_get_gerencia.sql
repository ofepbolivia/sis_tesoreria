CREATE OR REPLACE FUNCTION tes.f_get_gerencia (
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
DECLARE
	v_consulta				varchar;
    v_nombre_funcion		varchar;    
  	v_registros				record;
    v_resp					varchar;
BEGIN
	v_nombre_funcion ='tes.f_get_gerencia';
    
	if not p_count then
      v_consulta='select
                    fun.id_funcionario,
                    fun.desc_funcionario1 as desc_funcionario,
                    ''Gerente''::text  as desc_funcionario_cargo,
                    1 as prioridad
                  from tes.tobligacion_pago top
                  inner join orga.vfuncionario fun on fun.id_funcionario = top.id_funcionario_gerente
                  where top.id_estado_wf = '||p_id_estado_wf||'
                  and '||p_filtro||'
                  order by fun.desc_funcionario1
                  limit '|| p_limit::varchar||' offset '||p_start::varchar;                   
      FOR v_registros in execute (v_consulta)LOOP     
          RETURN NEXT v_registros;
      END LOOP;
    else
                  v_consulta='select
                              	COUNT(fun.id_funcionario) as total
                              from tes.tobligacion_pago top
                  			  inner join orga.vfuncionario fun on fun.id_funcionario = top.id_funcionario_gerente
                              where top.id_estado_wf = '||p_id_estado_wf||'
                              and '||p_filtro;

                   FOR v_registros in execute (v_consulta)LOOP
                     RETURN NEXT v_registros;
                   END LOOP;


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
COST 100 ROWS 1000;
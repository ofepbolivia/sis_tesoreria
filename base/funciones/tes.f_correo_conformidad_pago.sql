CREATE OR REPLACE FUNCTION tes.f_correo_conformidad_pago (
  p_id_plan_pago integer,
  p_id_usuario integer
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA ENDESIS - SISTEMA DE ...
***************************************************************************
 SCRIPT: 		adq.f_correo_habilitar_pago
 DESCRIPCIÓN: 	Envia correo la auxiliar de adquisiciones encangarda de adjuntar el form 500
 AUTOR: 		Franklin Espinoza Alvarez
 FECHA:			22/2/2018
 COMENTARIOS:	
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCIÓN:
 AUTOR:       
 FECHA:      

***************************************************************************/

-- PARÁMETROS FIJOS


DECLARE
	v_resp            	varchar;
    v_nombre_funcion  	varchar;
    v_id_alarma   		integer;
    v_desc_persona		record;
    v_descripcion 		varchar;
    v_registros_cp		record;

BEGIN
	v_nombre_funcion ='tes.f_correo_conformidad_pago';

  	--Preparamos la alarma para enviar al funcionario Solicitante
    select 
     vf.desc_funcionario1,
     tc.num_tramite,
     vf.email_empresa,
     vf.id_funcionario
    into
      v_registros_cp
    from  tes.tplan_pago tpp 
    inner join adq.tcotizacion tc on tc.id_obligacion_pago = tpp.id_obligacion_pago
    inner join adq.tproceso_compra tpc on tpc.id_proceso_compra = tc.id_proceso_compra
    inner join segu.tusuario tu on tu.id_usuario = tpc.id_usuario_auxiliar
    inner join orga.tfuncionario tfun on tfun.id_persona = tu.id_persona
    INNER JOIN orga.vfuncionario_persona vf ON vf.id_funcionario = tfun.id_funcionario

    where tpp.id_plan_pago = p_id_plan_pago;
            
    v_descripcion =  'Estimad@, '|| v_registros_cp.desc_funcionario1||'<br>'||
    'confirmarte que el tramite # '||v_registros_cp.num_tramite||'<br> ya cuenta con conformidad.<br>'||  
    'Puede dar seguimiento en la ventana (Conformidad de Pagos).';
            
                    
    --preparamos el correo en bandeja para ser enviado.        
    v_id_alarma :=  param.f_inserta_alarma(
                                          v_registros_cp.id_funcionario,
                                          v_descripcion,
                                          '../../../sis_tesoreria/vista/plan_pago/PlanPagoConformidad.php',
                                          now()::date,
                                          'notificacion',
                                          'Ninguna',
                                          p_id_usuario,
                                          'PlanPagoConformidad',
                                          v_registros_cp.desc_funcionario1,--titulo
                                          '{filtro_directo:{campo:"id_plan_pago",valor:"'||p_id_plan_pago::varchar||'"}}',
                                          NULL::integer,
                                          ('Pago Habilitado - '||v_registros_cp.num_tramite)::varchar,
                                          v_registros_cp.email_empresa::text
                                          );
            
   
    return true;

   
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
CREATE OR REPLACE FUNCTION tes.f_obtener_cadena_conexion_argentina (
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Presupuestos
 FUNCION: 		tes.f_obtener_cadena_conexion_argentina
 DESCRIPCION:   Funcion que recupera los datos de conexion al servidor remoto
 AUTOR: 		Maylee Perez Pastor
 FECHA:	        1-04-2019
 COMENTARIOS:	
***************************************************************************/
DECLARE

v_host varchar;
v_puerto varchar;
v_dbname varchar;
p_user varchar;
v_password varchar;
v_sincronizar varchar; 
v_resp varchar;
v_nombre_funcion varchar;
 
BEGIN


 v_nombre_funcion =  'tes.f_obtener_cadena_conexion_argentina';

  v_host=pxp.f_get_variable_global('sincronozar_ip_BUE');
  v_puerto=pxp.f_get_variable_global('sincroniza_puerto');  
  v_dbname=pxp.f_get_variable_global('sincronizar_base_BUE');
  p_user=pxp.f_get_variable_global('sincronizar_user');
  v_password=pxp.f_get_variable_global('sincronizar_password');
  v_sincronizar=pxp.f_get_variable_global('sincronizar');

   IF v_sincronizar = 'false'  THEN
     
     raise exception 'La sincronizacion esta deshabilitada. Verifique la configuración en la tabla de variables globales';
   
   END IF;

  /*
  v_host='192.168.1.108';
  v_puerto='5432';
  v_dbname='dbendesis';
  p_user='postgres';
  v_password='postgres';*/


  RETURN 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password; 

   
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
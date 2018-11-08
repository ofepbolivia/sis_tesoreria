CREATE OR REPLACE FUNCTION tes.ft_conformidad_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Obligaciones de Pago
 FUNCION: 		tes.ft_conformidad_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tconformidad'
 AUTOR: 		 (admin)
 FECHA:	        05-09-2018 20:43:03
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:43:03								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'tes.tconformidad'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'tes.ft_conformidad_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_TCONF_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		05-09-2018 20:43:03
	***********************************/

	if(p_transaccion='TES_TCONF_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tconf.id_conformidad,
						tconf.estado_reg,
						tconf.fecha_conformidad_final,
						tconf.fecha_inicio,
						tconf.fecha_fin,
						tconf.observaciones,
						tconf.id_obligacion_pago,
						tconf.conformidad_final,
						tconf.id_usuario_reg,
						tconf.fecha_reg,
						tconf.id_usuario_ai,
						tconf.usuario_ai,
						tconf.id_usuario_mod,
						tconf.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        op.id_gestion,
                        op.num_tramite

                        from tes.tconformidad tconf
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago = tconf.id_obligacion_pago
						inner join segu.tusuario usu1 on usu1.id_usuario = tconf.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tconf.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
raise notice 'error %',v_consulta ;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'TES_TCONF_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		05-09-2018 20:43:03
	***********************************/

	elsif(p_transaccion='TES_TCONF_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_conformidad)
					    from tes.tconformidad tconf
                        inner join tes.tobligacion_pago op on op.id_obligacion_pago = tconf.id_obligacion_pago
						inner join segu.tusuario usu1 on usu1.id_usuario = tconf.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tconf.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	else

		raise exception 'Transaccion inexistente';

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
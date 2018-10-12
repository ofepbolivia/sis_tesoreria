CREATE OR REPLACE FUNCTION tes.ft_conformidad_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Obligaciones de Pago
 FUNCION: 		tes.ft_conformidad_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tconformidad'
 AUTOR: 		 (admin)
 FECHA:	        05-09-2018 20:43:03
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				05-09-2018 20:43:03								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tconformidad'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_conformidad	    integer;

    v_id_obligacion_pago    integer;
    v_num_tramite			varchar;
    v_fecha_inicio          date;
    v_fecha_fin				date;

    p_id_gestion            integer;

    v_fecha_ini_conf        date;
    v_num_tramite_op		varchar;
    v_fecha_fin_conf        date;
    v_fecha_conf_final		date;

    v_id_persona			integer;
    v_id_usuario_firma		integer;
    v_resp_doc   			boolean;
    v_id_estado_actual		integer;
    v_id_proceso_wf			integer;
    v_partida_codigo		varchar;
    v_partida_desc   	    varchar;

BEGIN

    v_nombre_funcion = 'tes.ft_conformidad_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'TES_TCONF_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		05-09-2018 20:43:03
	***********************************/

	if(p_transaccion='TES_TCONF_INS')then

        begin

            --control de fecha
            IF(v_parametros.fecha_inicio>v_parametros.fecha_fin)THEN
            	RAISE exception 'LA FECHA INICIO ES MAYOR A LA FECHA FIN';
            END IF;

             --control si se repite numero de tramite con fecha ini y fecha fin
            select con.id_obligacion_pago, ob.num_tramite,con.fecha_inicio, con.fecha_fin
            into v_id_obligacion_pago, v_num_tramite,v_fecha_inicio, v_fecha_fin
            from tes.tconformidad con
            inner join tes.tobligacion_pago ob on ob.id_obligacion_pago = con.id_obligacion_pago
            where con.id_obligacion_pago = v_parametros.id_obligacion_pago
            and con.estado_reg = 'activo';

            if v_id_obligacion_pago = v_parametros.id_obligacion_pago THEN
            	raise exception 'EL NUMERO DE TRÁMITE % YA TIENE UNA FECHA DE INICIO % Y FECHA FIN %',v_num_tramite,v_fecha_inicio, v_fecha_fin;
            END IF;

        	--Sentencia de la insercion
        	insert into tes.tconformidad(
			estado_reg,
			fecha_conformidad_final,
			fecha_inicio,
			fecha_fin,
			observaciones,
			id_obligacion_pago,
			conformidad_final,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.fecha_conformidad_final,
            v_parametros.fecha_inicio,
			v_parametros.fecha_fin,
			v_parametros.observaciones,
			v_parametros.id_obligacion_pago,
			v_parametros.conformidad_final,
            p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_conformidad into v_id_conformidad;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conformidad almacenado(a) con exito (id_conformidad'||v_id_conformidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_conformidad',v_id_conformidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_TCONF_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		05-09-2018 20:43:03
	***********************************/

	elsif(p_transaccion='TES_TCONF_MOD')then

		begin
            --control de fecha
            IF(v_parametros.fecha_inicio>v_parametros.fecha_fin)THEN
            	RAISE exception 'LA FECHA INICIO ES MAYOR A LA FECHA FIN';
            END IF;

			--Sentencia de la modificacion
			update tes.tconformidad set
			fecha_conformidad_final = v_parametros.fecha_conformidad_final,
			fecha_inicio = v_parametros.fecha_inicio,
			fecha_fin = v_parametros.fecha_fin,
			observaciones = v_parametros.observaciones,
			id_obligacion_pago = v_parametros.id_obligacion_pago,
			conformidad_final = v_parametros.conformidad_final,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_conformidad=v_parametros.id_conformidad;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conformidad modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_conformidad',v_parametros.id_conformidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'TES_TCONF_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		05-09-2018 20:43:03
	***********************************/

	elsif(p_transaccion='TES_TCONF_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from tes.tconformidad
            where id_conformidad=v_parametros.id_conformidad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conformidad eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_conformidad',v_parametros.id_conformidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'TES_GENCONFIN_IME'
 	#DESCRIPCION:	Actualiza los datos de la conformidad final
 	#AUTOR:		admin
 	#FECHA:		25-09-2018 20:43:03
	***********************************/
    elsif(p_transaccion='TES_GENCONFIN_IME')then

		begin
           --para ver que el empleador es el mismo que registró
           select usu.id_persona
           into v_id_persona
           from segu.vusuario usu
           where usu.id_usuario = p_id_usuario;

           if(not exists (select 1
                from tes.tobligacion_pago op
                inner join orga.tfuncionario fun on fun.id_funcionario = op.id_funcionario
                inner join segu.vusuario usu on usu.id_usuario = op.id_usuario_reg
                where op.id_obligacion_pago = v_parametros.id_obligacion_pago and
                (v_id_persona = fun.id_persona or v_id_persona = usu.id_persona or p_administrador = 1))) then
            	raise exception 'Solo el solicitante y el usuario que registro la obligacion pueden generar la conformidad';
            end if;

         /*   if(not exists (select 1
                from tes.tconformidad conf
                inner join tes.tobligacion_pago op on op.id_obligacion_pago = conf.id_obligacion_pago
                inner join orga.tfuncionario fun on fun.id_funcionario = op.id_funcionario
                inner join segu.vusuario usu on usu.id_usuario = op.id_usuario_reg
                where op.id_obligacion_pago = v_parametros.id_obligacion_pago and
                (v_id_persona = fun.id_persona or v_id_persona = usu.id_persona or p_administrador = 1))) then
            	raise exception 'Solo el solicitante y el usuario que registro la obligacion pueden generar la conformidad';
            end if;
          */
            select usu.id_usuario
            into v_id_usuario_firma
            from  tes.tobligacion_pago op
            inner join orga.tfuncionario fun on op.id_funcionario = fun.id_funcionario
            inner join segu.tusuario usu on fun.id_persona = usu.id_persona
            where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

            if v_id_usuario_firma is null then
            	raise exception 'El funcionario del proceso no tiene usuario en el sistema para firmar el acta de conformidad';
            end if;
           --

           select co.fecha_conformidad_final
           into v_fecha_conf_final
          from  tes.tconformidad co
           JOIN tes.tobligacion_pago op on op.id_obligacion_pago = co.id_obligacion_pago
           where co.id_obligacion_pago = v_parametros.id_obligacion_pago;

          --para que sea campo obligatorio para la partida 49100
           select par.codigo
           into v_partida_codigo
           from adq.tsolicitud_det sd
           join pre.tpartida par on par.id_partida = sd.id_partida
           join tes.tobligacion_det ode on ode.id_partida = sd.id_partida
           join tes.tobligacion_pago op on op.id_obligacion_pago = ode.id_obligacion_pago
           left join tes.tconformidad conf on conf.id_obligacion_pago = op.id_obligacion_pago
           where
           ode.id_obligacion_pago = v_parametros.id_obligacion_pago;

           IF(v_partida_codigo = '49100')then
           		IF (v_parametros.fecha_inicio is null or v_parametros.fecha_fin is null )then
                	raise exception 'SE DEBE REGISTRAR LA FECHA INICIO Y FECHA FIN PARA ESTE NÚMERO DE TRÁMITE';
                end IF;
           	end if;
           --raise exception 'error : %',v_partida_codigo;

           --para control de fechas
           if v_parametros.fecha_conformidad_final < v_parametros.fecha_inicio THEN

                raise exception 'LA FECHA DE CONFORMIDAD FINAL ES MENOR A SU FECHA DE INICIO';
           end if;

           IF(v_parametros.fecha_inicio>v_parametros.fecha_fin)THEN
            	RAISE exception 'LA FECHA INICIO ES MAYOR A LA FECHA FIN';
           END IF;





        IF(v_fecha_conf_final is not null) then
            update tes.tconformidad
             set
             fecha_inicio = v_parametros.fecha_inicio,
             fecha_fin = v_parametros.fecha_fin,
             conformidad_final = v_parametros.conformidad_final,
             fecha_conformidad_final = v_parametros.fecha_conformidad_final,
             observaciones = v_parametros.observaciones
             where id_obligacion_pago = v_parametros.id_obligacion_pago;

         ELSE
         	 --Sentencia de la insercion
        	insert into tes.tconformidad(
			fecha_conformidad_final,
			fecha_inicio,
			fecha_fin,
			id_obligacion_pago,
			conformidad_final,
            id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            observaciones

          	) values(
			v_parametros.fecha_conformidad_final,
            v_parametros.fecha_inicio,
			v_parametros.fecha_fin,
			v_parametros.id_obligacion_pago,
			v_parametros.conformidad_final,
            p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_parametros.observaciones

			)RETURNING id_conformidad into v_id_conformidad;
          end if;


          select op.id_estado_wf,op.id_proceso_wf
          into v_id_estado_actual,v_id_proceso_wf
          from tes.tobligacion_pago op
          where op.id_obligacion_pago = v_parametros.id_obligacion_pago;

          --para eliminar la firma si existiera
           update wf.tdocumento_wf
           set fecha_firma = NULL
           from wf.ttipo_documento td
           where td.id_tipo_documento = wf.tdocumento_wf.id_tipo_documento and td.codigo = 'ACTCONF' and
           wf.tdocumento_wf .estado_reg = 'activo' and td.estado_reg = 'activo' and
           wf.tdocumento_wf .id_proceso_wf = v_id_proceso_wf;


   v_resp_doc = wf.f_verifica_documento(v_id_usuario_firma, v_id_estado_actual);

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se modificaron los datos de la conformidad exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_obligacion_pago',v_parametros.id_obligacion_pago::varchar);
--raise EXCEPTION 'errorrrrrrr %, %',v_id_usuario_firma, v_id_estado_actual;
--raise exception 'error -> %', v_resp_doc;
            --Devuelve la respuesta
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
CREATE OR REPLACE FUNCTION tes.ft_tipo_prorrateo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Tesoreria
 FUNCION: 		tes.ft_tipo_prorrateo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.ttipo_prorrateo'
 AUTOR: 		 (jrivera)
 FECHA:	        31-07-2014 23:29:22
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
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_tipo_prorrateo	integer;
    v_res				varchar;
    v_periodo			record;
    v_tipo_prorrateo	record;
    v_registros			record;
    v_parametrizacion	record;
    v_fin_campos		varchar;
    v_fin_valores		varchar;
    v_query				text;
    v_id_partida		integer;
    v_id_centro_costo_aux	integer;

    v_registros_ruta		record;
    v_id_tipo_cc			integer;
    v_ordenes				integer;
    v_numero_celular		varchar;
    v_num_tramite_op		varchar;

BEGIN

    v_nombre_funcion = 'tes.ft_tipo_prorrateo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'TES_TIPO_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		31-07-2014 23:29:22
	***********************************/

	if(p_transaccion='TES_TIPO_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into tes.ttipo_prorrateo(
			estado_reg,
			descripcion,
			es_plantilla,
			nombre,
			codigo,
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
			tiene_cuenta,
			tiene_lugar
          	) values(
			'activo',
			v_parametros.descripcion,
			v_parametros.es_plantilla,
			v_parametros.nombre,
			v_parametros.codigo,
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null,
			v_parametros.tiene_cuenta,
			v_parametros.tiene_lugar
							
			
			
			)RETURNING id_tipo_prorrateo into v_id_tipo_prorrateo;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Prorrateo almacenado(a) con exito (id_tipo_prorrateo'||v_id_tipo_prorrateo||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_prorrateo',v_id_tipo_prorrateo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'TES_TIPO_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		31-07-2014 23:29:22
	***********************************/

	elsif(p_transaccion='TES_TIPO_MOD')then

		begin
			--Sentencia de la modificacion
			update tes.ttipo_prorrateo set
			descripcion = v_parametros.descripcion,
			es_plantilla = v_parametros.es_plantilla,
			nombre = v_parametros.nombre,
			codigo = v_parametros.codigo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
			tiene_cuenta = v_parametros.tiene_cuenta,
			tiene_lugar = v_parametros.tiene_lugar
			where id_tipo_prorrateo=v_parametros.id_tipo_prorrateo;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Prorrateo modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_prorrateo',v_parametros.id_tipo_prorrateo::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'TES_TIPO_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera	
 	#FECHA:		31-07-2014 23:29:22
	***********************************/

	elsif(p_transaccion='TES_TIPO_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from tes.ttipo_prorrateo
            where id_tipo_prorrateo=v_parametros.id_tipo_prorrateo;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Prorrateo eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_prorrateo',v_parametros.id_tipo_prorrateo::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
        
    /*********************************    
 	#TRANSACCION:  'TES_TIPOEJE_UPD'
 	#DESCRIPCION:	Generación de algun tipo de prorrateo
 	#AUTOR:		jrivera	
 	#FECHA:		31-07-2014 23:29:22
	***********************************/

	elsif(p_transaccion='TES_TIPOEJE_UPD')then

		begin
        	select *
            into v_periodo
            from param.tperiodo
            where id_periodo = v_parametros.id_periodo;

        	select *
            into v_tipo_prorrateo
            from tes.ttipo_prorrateo tp
            where tp.id_tipo_prorrateo = v_parametros.id_tipo_prorrateo;

            if (v_tipo_prorrateo.tiene_cuenta = 'si') then
            	if (v_tipo_prorrateo.tiene_lugar = 'si') then
            		v_res = orga.f_prorratear_x_empleado(v_parametros.id_periodo,v_parametros.monto,v_tipo_prorrateo.codigo,
                    									v_parametros.id_lugar,v_parametros.id_oficina_cuenta,v_parametros.id_proveedor);
                else
                	v_res = orga.f_prorratear_x_empleado(v_parametros.id_periodo,v_parametros.monto,v_tipo_prorrateo.codigo,
                    									NULL,v_parametros.id_oficina_cuenta,v_parametros.id_proveedor);
                end if;
            else
            	v_res = orga.f_prorratear_x_empleado(v_parametros.id_periodo,v_parametros.monto,v_tipo_prorrateo.codigo,NULL,NULL,v_parametros.id_proveedor);
            end if;



           -- para verificar que si existe con un proceso
           UPDATE gecom.tpago_telefonia SET
           nro_tramite = 'si'
           WHERE id_periodo = v_parametros.id_periodo;

        --(may) nueva condicion si ingresan a tabla rutas

            IF (v_tipo_prorrateo.codigo = 'PFIJO') THEN

            		FOR v_registros in (  SELECT *
                                            FROM gecom.tes_temp_prorrateo_ruta
                                            WHERE id_periodo = v_periodo.id_periodo
                                            and  ruta = 'si'
                                            and id_proveedor = v_parametros.id_proveedor
                                            ) LOOP



            			  if (v_parametros.tiene_tipo_cambio = 'si') then
                              v_fin_campos = ',' || v_parametros.nombre_monto_mb;
                              v_fin_valores = ',' || (v_registros.monto * v_parametros.tipo_cambio);
                          else
                              v_fin_campos = '';
                              v_fin_valores = '';
                          end if;

                          v_id_partida = null;

                          select p.id_partida
                          into v_id_partida
                          from pre.tpartida p
                          inner join pre.tconcepto_partida cp on cp.id_partida = p.id_partida
                          inner join pre.tpresup_partida pp on pp.id_partida = p.id_partida
                          where  p.id_gestion = v_periodo.id_gestion and cp.id_concepto_ingas = v_parametros.id_concepto_ingas
                          and pp.id_presupuesto = v_registros.id_centro_costo and pp.estado_reg = 'activo';

                          if (v_id_partida is null) then
                              v_id_centro_costo_aux = NULL;
                              v_id_centro_costo_aux = tes.f_get_uo_presupuesta_prorrateo(v_parametros.id_concepto_ingas,v_registros.id_centro_costo);

                              if (v_id_centro_costo_aux is null) then
                                  raise exception 'No existe un centro de  costo: % relacionado a la partida',v_registros.id_centro_costo;
                              else
                                  v_registros.id_centro_costo = v_id_centro_costo_aux;
                              end if;
                          end if;

                          SELECT *
                          into v_parametrizacion
                          FROM conta.f_get_config_relacion_contable('CUECOMP', v_periodo.id_gestion,
                          v_parametros.id_concepto_ingas, v_registros.id_centro_costo);

                              --raise exception 'llegabd %', v_fin_valores;

                           --(may) para la OT

                           /*select id_tipo_cc
                           into v_id_tipo_cc
                           from param.tcentro_costo cc
                           where cc.id_centro_costo = v_registros.id_centro_costo;

                           IF v_id_tipo_cc is null THEN
                              raise exception 'No fue parametrizaso un tipo para el centro de costos % ',v_parametros.id_centro_costo;
                           END IF;

                           --SELECT pxp.list(c.id_orden_trabajo::VARCHAR)
                           SELECT c.id_orden_trabajo
                           into v_ordenes
                           FROM conta.vot_arb c
                           inner join conta.ttipo_cc_ot tco on tco.id_orden_trabajo = ANY(c.ids)
                           where c.movimiento = 'si'  and tco.id_tipo_cc = v_id_tipo_cc;

                           IF v_ordenes is null THEN
                              raise exception 'Falta Orden de Trabajo';
                           END IF;*/

                           --numero telefonico
                           SELECT num.numero
                           into v_numero_celular
                           FROM gecom.tnumero_celular num
                           where num.id_numero_celular = v_registros.id_tabla;



                          EXECUTE '
                          INSERT INTO
                              ' || v_parametros.nombre_tabla || '
                            (
                              id_usuario_reg,
                              fecha_reg,
                              ' || v_parametros.nombre_id || ',
                              id_concepto_ingas,
                              id_centro_costo,
                              id_orden_trabajo,
                              id_partida,
                              id_cuenta,
                              id_auxiliar,
                              descripcion,
                              ' || v_parametros.nombre_monto ||v_fin_campos || '
                            )
                            VALUES (
                              ' || p_id_usuario || ',
                              now(),
                              ' || v_parametros.id_valor || ',
                              ' || v_parametros.id_concepto_ingas || ',
                              ' || v_registros.id_centro_costo || ',
                              ' || v_registros.id_orden_trabajo || ',
                              ' || v_parametrizacion.ps_id_partida || ',
                              ' || v_parametrizacion.ps_id_cuenta || ',
                              ' || v_parametrizacion.ps_id_auxiliar || ',
                              ''' || coalesce (v_registros.descripcion, '') || '('|| v_numero_celular ||')' || ''',
                              ' || v_registros.monto || v_fin_valores || '

                            )';



                            if (pxp.f_existe_parametro(p_tabla,'nombre_funcion_ejecutar')) then
                                  v_query = 'select ' || v_parametros.nombre_funcion_ejecutar ||
                                           '(' || v_parametros.id_valor || ')';

                                  execute v_query into v_res;
                            end if;

            	END LOOP;

                for v_registros in (
                              select id_centro_costo,id_orden_trabajo,descripcion, sum(monto) as monto, id_tabla
                              from tes_temp_prorrateo
                              where id_proveedor = v_parametros.id_proveedor
                              group by id_centro_costo,id_orden_trabajo,descripcion, id_tabla) loop


                      if (v_parametros.tiene_tipo_cambio = 'si') then
                          v_fin_campos = ',' || v_parametros.nombre_monto_mb;
                          v_fin_valores = ',' || (v_registros.monto * v_parametros.tipo_cambio);
                      else
                          v_fin_campos = '';
                          v_fin_valores = '';
                      end if;
                      v_id_partida = null;

                      select p.id_partida into v_id_partida
                      from pre.tpartida p
                      inner join pre.tconcepto_partida cp on cp.id_partida = p.id_partida
                      inner join pre.tpresup_partida pp on pp.id_partida = p.id_partida
                      where  p.id_gestion = v_periodo.id_gestion and cp.id_concepto_ingas = v_parametros.id_concepto_ingas
                      and pp.id_presupuesto = v_registros.id_centro_costo and pp.estado_reg = 'activo';
                      if (v_registros.id_centro_costo is null) then
                          raise exception 'llega';
                      end if;
                      if (v_id_partida is null) then
                          v_id_centro_costo_aux = NULL;
                          v_id_centro_costo_aux = tes.f_get_uo_presupuesta_prorrateo(v_parametros.id_concepto_ingas,v_registros.id_centro_costo);

                          if (v_id_centro_costo_aux is null) then
                              raise exception 'No existe un centro de  costo: % relacionado a la partida',v_registros.id_centro_costo;
                          else
                              v_registros.id_centro_costo = v_id_centro_costo_aux;
                          end if;
                      end if;



                      SELECT * into v_parametrizacion
                      FROM conta.f_get_config_relacion_contable('CUECOMP', v_periodo.id_gestion,
                      v_parametros.id_concepto_ingas, v_registros.id_centro_costo);


                      --numero telefonico
                       SELECT num.numero
                       into v_numero_celular
                       FROM gecom.tnumero_celular num
                       where num.id_numero_celular = v_registros.id_tabla;


                      EXECUTE '
                      INSERT INTO
                          ' || v_parametros.nombre_tabla || '
                        (
                          id_usuario_reg,
                          fecha_reg,
                          ' || v_parametros.nombre_id || ',
                          id_concepto_ingas,
                          id_centro_costo,
                          id_orden_trabajo,
                          id_partida,
                          id_cuenta,
                          id_auxiliar,
                          descripcion,
                          ' || v_parametros.nombre_monto ||v_fin_campos || '
                        )
                        VALUES (
                          ' || p_id_usuario || ',
                          now(),
                          ' || v_parametros.id_valor || ',
                          ' || v_parametros.id_concepto_ingas || ',
                          ' || v_registros.id_centro_costo || ',
                          ' || v_registros.id_orden_trabajo || ',
                          ' || v_parametrizacion.ps_id_partida || ',
                          ' || v_parametrizacion.ps_id_cuenta || ',
                          ' || v_parametrizacion.ps_id_auxiliar || ',
                          ''' || coalesce (v_registros.descripcion, '') || '('|| v_numero_celular ||')' || ''',
                          ' || v_registros.monto || v_fin_valores || '

                        )';



                        if (pxp.f_existe_parametro(p_tabla,'nombre_funcion_ejecutar')) then
                              v_query = 'select ' || v_parametros.nombre_funcion_ejecutar ||
                                       '(' || v_parametros.id_valor || ')';

                              execute v_query into v_res;
                        end if;

                  end loop;


            ELSE

            	for v_registros in (
                       select p.id_centro_costo, p.id_orden_trabajo, p.descripcion, sum(p.monto) as monto, p.id_tabla, f.desc_funcionario1
                        from tes_temp_prorrateo p
                        inner join orga.vfuncionario f on p.id_funcionario = f.id_funcionario
                        group by p.id_centro_costo, p.id_orden_trabajo, p.descripcion, p.id_tabla, f.desc_funcionario1) loop


                    if (v_parametros.tiene_tipo_cambio = 'si') then
                        v_fin_campos = ',' || v_parametros.nombre_monto_mb;
                        v_fin_valores = ',' || (v_registros.monto * v_parametros.tipo_cambio);
                    else
                        v_fin_campos = '';
                        v_fin_valores = '';
                    end if;
                    v_id_partida = null;

                    select p.id_partida into v_id_partida
                    from pre.tpartida p
                    inner join pre.tconcepto_partida cp on cp.id_partida = p.id_partida
                    inner join pre.tpresup_partida pp on pp.id_partida = p.id_partida
                    where  p.id_gestion = v_periodo.id_gestion and cp.id_concepto_ingas = v_parametros.id_concepto_ingas
                    and pp.id_presupuesto = v_registros.id_centro_costo and pp.estado_reg = 'activo';
                    if (v_registros.id_centro_costo is null) then
                        raise exception 'llega';
                    end if;
                    if (v_id_partida is null) then
                        v_id_centro_costo_aux = NULL;
                        v_id_centro_costo_aux = tes.f_get_uo_presupuesta_prorrateo(v_parametros.id_concepto_ingas,v_registros.id_centro_costo);

                        if (v_id_centro_costo_aux is null) then
                            raise exception 'No existe un centro de  costo: % relacionado a la partida, para el funcionario: %',v_registros.id_centro_costo,v_registros.desc_funcionario1;
                        else
                            v_registros.id_centro_costo = v_id_centro_costo_aux;
                        end if;
                    end if;



                    SELECT * into v_parametrizacion
                    FROM conta.f_get_config_relacion_contable('CUECOMP', v_periodo.id_gestion,
                    v_parametros.id_concepto_ingas, v_registros.id_centro_costo);



                    EXECUTE '
                    INSERT INTO
                        ' || v_parametros.nombre_tabla || '
                      (
                        id_usuario_reg,
                        fecha_reg,
                        ' || v_parametros.nombre_id || ',
                        id_concepto_ingas,
                        id_centro_costo,
                        id_orden_trabajo,
                        id_partida,
                        id_cuenta,
                        id_auxiliar,
                        descripcion,
                        ' || v_parametros.nombre_monto ||v_fin_campos || '
                      )
                      VALUES (
                        ' || p_id_usuario || ',
                        now(),
                        ' || v_parametros.id_valor || ',
                        ' || v_parametros.id_concepto_ingas || ',
                        ' || v_registros.id_centro_costo || ',
                        ' || v_registros.id_orden_trabajo || ',
                        ' || v_parametrizacion.ps_id_partida || ',
                        ' || v_parametrizacion.ps_id_cuenta || ',
                        ' || v_parametrizacion.ps_id_auxiliar || ',
                        ''' || coalesce (v_registros.descripcion, '') || ''',
                        ' || v_registros.monto || v_fin_valores || '

                      )';



                      if (pxp.f_existe_parametro(p_tabla,'nombre_funcion_ejecutar')) then
                            v_query = 'select ' || v_parametros.nombre_funcion_ejecutar ||
                                     '(' || v_parametros.id_valor || ')';

                            execute v_query into v_res;
                      end if;

                end loop;



            END IF;
           
            --Definicion de la respuesta
            
            
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Generacion de prorrateo realizado'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_prorrateo',v_parametros.id_tipo_prorrateo::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje_prorrateo',v_res);
            
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
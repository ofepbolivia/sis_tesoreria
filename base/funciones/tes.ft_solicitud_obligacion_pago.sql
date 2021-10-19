CREATE OR REPLACE FUNCTION tes.ft_solicitud_obligacion_pago (
  v_id_obligacion_pago integer,
  v_id_usuario integer
)
RETURNS SETOF record AS
$body$
/**************************************************************************
 SISTEMA:		Tesoreria
 FUNCION: 		tes.ft_solicitud_obligacion_pago
 DESCRIPCION:   verifica solicitud matriz
 AUTOR: 		maylee.perez
 FECHA:	        25-08-20210
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE
  	v_resp		           	varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
    v_consulta				text;

    v_solicitud_det			record;
    v_id_matriz_modalidad	integer;
    v_modalidades_matriz	record;
    v_id_modalidad_solicitud integer;

    v_desc_ingas			varchar;
    v_total_det				numeric;
    v_codigo_modalidad		varchar;

     --v_id_funcionario_sol	integer;
     v_id_uo  				integer;
     v_solicitud			record;
     va_id_funcionario_gerente  	INTEGER[];
     v_idfun_modalidad		integer;
     v_desc_funcionario		varchar;
     v_nombre_cargo			varchar;
     v_count_concepto_ingas	numeric;
     v_id_matriz_mod		record;
     v_modalidad_solicitud	record;
     v_modalidades_solicitud record;
	 v_modalidad			varchar;
     v_count_modalidad		numeric;
     --v_id_funcionario_aprobador	integer;
     v_solicitud_modalidad	varchar;
     v_solu_modalidades		record;
     v_respuesta_modalidad	varchar;
     v_depto_prioridad		integer;
     v_nom_tipo_contratacion	varchar;
     v_nombre_modalidad		varchar;

     v_funcionario			integer;
     v_id_matriz			record;
     v_id_uo_matriz  		integer;
     v_id_uo_sol			integer;
     v_funcionario_sol		integer;
     v_matriz_id_modalidad	integer;
     v_nom_uo				varchar;

     v_proceso_contratacion	varchar;
     v_count_codigo_modalidad	integer;
     v_id_uo_jefatura		integer;
	 v_id_uo_jefatura_gerencia	integer;
     v_id_uo_jefatura_unidad	integer;
	 v_id_uo_jefatura_direccion	integer;


     v_obligacion_det			record;
     v_obligacion_pago			record;
     v_matriz_modalidad			record;
     bandera_matriz				varchar;
     v_nombre_funcionario		varchar;
     v_matriz_cargo				integer;

     v_nombre_unidad_matriz			varchar;
     v_nombre_unidad_solicitante	varchar;

     v_padres					varchar;

BEGIN


  	  v_nombre_funcion = 'tes.ft_solicitud_obligacion_pago';


	  SELECT op.fecha, op.id_funcionario
      into v_obligacion_pago
      FROM tes.tobligacion_pago op
      WHERE op.id_obligacion_pago = v_id_obligacion_pago;

      --prioridad segun el depto del proceso
      SELECT depto.prioridad
      into v_depto_prioridad
      FROM param.tdepto depto
      join tes.tobligacion_pago op on op.id_depto= depto.id_depto
      WHERE op.id_obligacion_pago = v_id_obligacion_pago;


      --v_id_uo_jefatura =   orga.f_get_uo_jefatura_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date); --nivel 7 Jefatura de departamento
      --v_id_uo_jefatura_gerencia =   orga.f_get_uo_jefa_ger_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date); --nivel 4 Gerencia de Area
      --v_id_uo_jefatura_unidad =   orga.f_get_uo_jefa_unidad_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date);--nivel 8 Unidad
      --v_id_uo_jefatura_direccion =   orga.f_get_uo_direccion_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date); --nivel 3 Direccion


      select funuo.id_uo
      into v_id_uo
      from orga.tuo_funcionario funuo
      where funuo.estado_reg = 'activo' and funuo.id_funcionario = v_obligacion_pago.id_funcionario and
		funuo.fecha_asignacion <= CURRENT_DATE and (funuo.fecha_finalizacion is null or funuo.fecha_finalizacion >= CURRENT_DATE);

      --obtenemos todas las unidades padre incluyendo la enviada como parametro
      v_padres = orga.f_get_arbol_padre_uo (v_id_uo);

      --obtenemos la gerencia a la que pertenece el funcionario
      v_id_uo_sol =   orga.f_get_uo_gerencia_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date);
      --RAISE EXCEPTION 'id_uo_jegatura % - % - %', v_id_uo_jefatura, v_id_uo_jefatura_gerencia, v_id_uo_jefatura_unidad;

       --raise exception  'Los valores son: %, %, %, %, %.', v_id_uo_jefatura, v_id_uo_jefatura_unidad, v_id_uo_jefatura_gerencia, v_id_uo_jefatura_direccion, v_id_uo_sol;

      --RECORRE PARA VERIFICAR EL DETALLLE DE LA SOLICITUD con cada concepto de gasto a que modalidad corresponde o no
	  --raise exception 'llega %', v_id_solicitud;
      FOR v_obligacion_det in(SELECT odet.id_concepto_ingas
                              FROM tes.tobligacion_det odet
                              WHERE odet.id_obligacion_pago= v_id_obligacion_pago
                              and odet.estado_reg = 'activo'
                              GROUP BY odet.id_concepto_ingas
                             )LOOP

                          --nombre del concepto de gasto
                          SELECT cin.desc_ingas
                          into v_desc_ingas
                          FROM param.tconcepto_ingas cin
                          WHERE cin.id_concepto_ingas = v_obligacion_det.id_concepto_ingas;

                           --para diferenciar de las regionales y de la central que si pueden elegir una de ellas
                           --para la central
                           --10445 = Gerencias Regionales
                           --30-09-2021 segun restruccturacion organigrama ya no se diferencia por depto, solo segunparametrizacion de la Matriz

                           --IF (v_depto_prioridad = 1) THEN

                          bandera_matriz = 'no';
                          --raise exception 'llega2 %',v_obligacion_det.id_concepto_ingas;

                          /*SELECT mc.id_matriz_modalidad
                          INTO v_id_matriz_modalidad
                          FROM adq.tmatriz_concepto mc
                          INNER join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                          WHERE mc.id_concepto_ingas = v_obligacion_det.id_concepto_ingas
                          and mc.estado_reg = 'activo'
                          and mm.estado_reg = 'activo'
                          and mm.flujo_sistema in ('TESORERIA','ADQUISICIONES-TESORERIA')
                          --and mm.id_uo in (v_id_uo_jefatura, v_id_uo_jefatura_unidad, v_id_uo_jefatura_gerencia, v_id_uo_jefatura_direccion, v_id_uo_funcionario)
                          and mm.id_uo in (v_padres)
                          and mm.id_uo_gerencia = v_id_uo_sol;*/

                          execute('SELECT mc.id_matriz_modalidad
                          FROM adq.tmatriz_concepto mc
                          INNER join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                          WHERE mc.id_concepto_ingas ='|| v_obligacion_det.id_concepto_ingas||'
                          and mc.estado_reg = ''activo''
                          and mm.estado_reg = ''activo''
                          and mm.flujo_sistema in (''TESORERIA'',''ADQUISICIONES-TESORERIA'')
                          and mm.id_uo in ('||v_padres||')
                          and mm.id_uo_gerencia ='|| v_id_uo_sol||' limit 1') INTO v_id_matriz_modalidad;

                         -- IF (v_id_matriz_modalidad is null) THEN
                         -- 	RAISE EXCEPTION '1. El valor v_id_matriz_modalidad es nulo, los valores recibidos son: id_uo_gerencia= %, id_concepto_ingas=%, v_matriz_id_modalidad= %, ',v_id_uo_sol, v_obligacion_det.id_concepto_ingas, v_id_matriz_modalidad;
                          --END IF;

                           IF (v_id_matriz_modalidad is null) THEN
                           		RAISE EXCEPTION '1. No se encuentra parametrizado el Concepto de Gasto: %, en la Matriz Tipo Contratación Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                           		--RAISE EXCEPTION 'los valores son id_concepto_ingas: %, v_padres:%, v_id_uo_sol: % ',v_obligacion_det.id_concepto_ingas, v_padres, v_id_uo_sol;

                           END IF;

                          --obtenemos todas las modalidades que contienen el concepto de gasto seleccionado y se corresponden con el cargo
                          FOR v_id_matriz IN execute('SELECT mc.id_matriz_modalidad, mm.id_uo, mm.id_cargo
                                                      FROM adq.tmatriz_concepto mc
                                                      inner join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                                      WHERE mc.id_concepto_ingas ='|| v_obligacion_det.id_concepto_ingas||'
                                                      and mc.estado_reg = ''activo''
                                                      and mm.estado_reg = ''activo''
                                                      and mm.flujo_sistema in (''TESORERIA'',''ADQUISICIONES-TESORERIA'')
                                                      and mm.id_uo in ('||v_padres||')
                                                      and mm.id_uo_gerencia ='|| v_id_uo_sol
                                              )LOOP



                              --RAISE EXCEPTION 'MATRIZ % - %',v_id_matriz.id_matriz_modalidad, v_id_matriz.id_uo;
                               /*SELECT fc.id_funcionario
                               into v_funcionario
                               FROM orga.vfuncionario_cargo fc
                               WHERE fc.id_uo = v_id_matriz.id_uo
                               and (fc.fecha_asignacion  <=  now()
                               and (fc.fecha_finalizacion is null or fc.fecha_finalizacion >= now() ));*/

                               --17-09-2021
                               --obtenemos el id_funcionario aprobador en base al cargo
                                SELECT  uofun.id_funcionario
                                INTO v_funcionario
                                FROM orga.tcargo car
                                inner join orga.tuo_funcionario uofun on uofun.id_uo = car.id_uo
                                WHERE car.id_cargo =  v_id_matriz.id_cargo
                                and uofun.fecha_asignacion  <=  now()
                                and (uofun.fecha_finalizacion is null or uofun.fecha_finalizacion >= now() );

                                --obtenemos el nombre del funcionario aprobador
                                SELECT vf.desc_funcionario1
                                INTO	v_desc_funcionario
                                FROM orga.vfuncionario vf
                                WHERE vf.id_funcionario = v_funcionario;

                                --obtenemos el nombre del cargo aprobador
                                SELECT car.nombre
                                INTO	v_nombre_cargo
                                FROM orga.tcargo car
                                WHERE car.id_cargo = v_id_matriz.id_cargo;



                               raise NOTICE 'llegaFUncionario %', v_funcionario;
                              -- recupera la uo en base a la uo registrada en la matriz
                              v_id_uo_matriz =   orga.f_get_uo_gerencia_area_ope(v_id_matriz.id_uo, NULL, now()::Date);
                              raise NOTICE 'llegaUO %', v_id_uo_matriz;

                              --22-09-2021(may)modificacion funcion porque no mostraba del primer que lista si oficial o funcional, ahora sera el oficial segun funcionario
                              --obtenemos el id_uo del funcionario solicitante, si tiene asignacion funcional prevalece ante la asiginacion oficial
                              --v_id_uo_sol =   orga.f_get_uo_gerencia_area_ope(NULL, v_obligacion_pago.id_funcionario, now()::Date);

                              --Comentado por grover
                              --v_id_uo_sol =   orga.f_get_uo_gerencia_area_ope_ofi(NULL, v_obligacion_pago.id_funcionario, now()::Date);


                              raise NOTICE 'llegaUOSol %', v_id_uo_sol;

                              --RAISE EXCEPTION 'MATRIZ % - %', v_id_uo_matriz,v_id_uo_sol;

                              --comparamos las gerencias recuperadas anteriormente sean iguales
                              IF (v_id_uo_matriz = v_id_uo_sol ) THEN


                              		v_matriz_id_modalidad=v_id_matriz.id_matriz_modalidad;

                                  --contamos en cuantas modalidades esta parametrizado el concepto y la gerencia solicitante
                                 /* SELECT count(mc.id_matriz_modalidad)
                                  INTO v_matriz_id_modalidad
                                  FROM adq.tmatriz_concepto mc
                                  inner join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                  WHERE mc.id_concepto_ingas = v_obligacion_det.id_concepto_ingas
                                  and mc.estado_reg = 'activo'
                                  AND mm.id_uo_gerencia = v_id_uo_sol;

                                  --raise exception 'El valor de v_matriz_id_modalidad es: %', v_matriz_id_modalidad;

                                  --si existe mas de 1 registro se toma la condicion por su departamento jefatura
                                  IF (v_matriz_id_modalidad > 1 or v_matriz_id_modalidad = 0) THEN

                                      --obtenemos el id de la matriz en la que esta parametrizada
                                      SELECT mc.id_matriz_modalidad
                                      --INTO v_matriz_id_modalidad
                                      INTO v_id_matriz_modalidad
                                      FROM adq.tmatriz_concepto mc
                                      left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                      WHERE mc.id_concepto_ingas = v_obligacion_det.id_concepto_ingas
                                      and mc.estado_reg = 'activo'
                                      --and mm.id_uo = v_id_uo_jefatura
                                      and mm.id_uo in (v_id_uo_jefatura, v_id_uo_jefatura_unidad, v_id_uo_jefatura_gerencia, v_id_uo_jefatura_direccion)
                                      and mm.id_uo_gerencia = v_id_uo_sol;

                                     -- IF (v_id_matriz_modalidad is null) THEN
                                     -- 	RAISE EXCEPTION '1. El valor v_id_matriz_modalidad es nulo, los valores recibidos son: id_uo_gerencia= %, id_concepto_ingas=%, v_matriz_id_modalidad= %, ',v_id_uo_sol, v_obligacion_det.id_concepto_ingas, v_id_matriz_modalidad;
                                      --END IF;

                                  ELSE

                                      SELECT mc.id_matriz_modalidad
                                      --INTO v_matriz_id_modalidad
                                      INTO v_id_matriz_modalidad
                                      FROM adq.tmatriz_concepto mc
                                      left join adq.tmatriz_modalidad mm on mm.id_matriz_modalidad = mc.id_matriz_modalidad
                                      WHERE mc.id_concepto_ingas = v_obligacion_det.id_concepto_ingas
                                      and mc.estado_reg = 'activo'
                                      AND mm.id_uo_gerencia = v_id_uo_sol;

                                      --IF (v_id_matriz_modalidad is null) THEN

                                      --	RAISE EXCEPTION '2. El valor v_id_matriz_modalidad es nulo, los valores recibidos son: id_uo_gerencia= %, id_concepto_ingas=%, v_matriz_id_modalidad= %, ',v_id_uo_sol, v_obligacion_det.id_concepto_ingas, v_id_matriz_modalidad;
                                      --END IF;

                                  END IF;
									*/
                                  bandera_matriz = 'si';

                                  --RAISE EXCEPTION 'MATRIZ %',v_matriz_id_modalidad;

                                  --v_id_matriz_modalidad =   v_matriz_id_modalidad;

                                  IF (v_id_matriz_modalidad is null) THEN
                                      RAISE EXCEPTION '2. NO se encuentra parametrizado el Concepto de Gasto % en la Matriz Tipo Contratación Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas;
                                  END IF;



                                  IF (v_id_matriz.id_cargo is null) THEN
                                    RAISE EXCEPTION 'No se encuentra parametrizado el Responsable de Aprobación  en la Matriz Tipo Contratación - Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).';
                                  END IF;


                                    --CONDICION PARA RESCATAR DE LA MATRIZ EL FUNCIONARIO RESPONSABLE

                                    --17-09-2021
                                    /*SELECT vc.id_funcionario
                                    INTO v_idfun_modalidad
                                    FROM orga.vfuncionario_cargo vc
                                    WHERE vc.id_cargo = v_id_matriz.id_cargo
                                    and vc.fecha_asignacion  <=  now()
                                    and (vc.fecha_finalizacion is null or vc.fecha_finalizacion >= now() );

                                    SELECT vf.desc_funcionario1
                                    INTO	v_desc_funcionario
                                    FROM orga.vfuncionario vf
                                    WHERE vf.id_funcionario = v_idfun_modalidad;

                                    SELECT car.nombre
                                    INTO	v_nombre_cargo
                                    FROM orga.tcargo car
                                    WHERE car.id_cargo = v_id_matriz.id_cargo;*/

                                    SELECT mm.id_cargo
                                    INTO v_matriz_cargo
                                    FROM adq.tmatriz_modalidad mm
                                    WHERE mm.id_matriz_modalidad =  v_id_matriz_modalidad;

                                    IF (v_matriz_cargo is null) THEN
                                          RAISE EXCEPTION 'No se encontro un valor en la tabla adq.tmatriz_modalidad para el id_matriz_modalidad = %.',v_id_matriz_modalidad ;
                                    END IF;


                                    SELECT  uofun.id_funcionario
                                    INTO v_funcionario
                                    FROM orga.tcargo car
                                    inner join orga.tuo_funcionario uofun on uofun.id_uo = car.id_uo
                                    WHERE car.id_cargo =  v_matriz_cargo
                                    and uofun.fecha_asignacion  <=  now()
                                    and (uofun.fecha_finalizacion is null or uofun.fecha_finalizacion >= now() );

                                    Select car.nombre
                                    into v_nombre_cargo
                                    from orga.tcargo car
                                    where car.id_cargo=v_matriz_cargo;

                                    IF (v_funcionario is null) THEN
                                         RAISE EXCEPTION 'No se pudo encontrar una asignacion activa para el cargo aprobador: %, id_cargo = %. Revise la matriz de aprobacion y la asignacion del cargo.', v_nombre_cargo, v_matriz_cargo ;
                                    END IF;


                                    IF (v_funcionario is null) THEN
                                         RAISE EXCEPTION 'No se encuentra el Funcionario Aprobador % del cargo % en la Matriz Tipo Contratación Aprobado.  Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',v_desc_funcionario, v_nombre_cargo ;
                                    END IF;
                              ELSE

                                  Select uo.nombre_unidad
                                  into v_nombre_unidad_matriz
                                  from orga.tuo uo
                                  where uo.id_uo=v_id_uo_matriz;

                                  Select uo.nombre_unidad
                                  into v_nombre_unidad_solicitante
                                  from orga.tuo uo
                                  where uo.id_uo=v_id_uo_sol;

                                  RAISE EXCEPTION 'Las unidades organizacionales recuperadas son diferentes, Unidad Matriz: %, Unidad Solicitud: %, v_id_uo_matriz=%, v_id_uo_sol=%',v_nombre_unidad_matriz, v_nombre_unidad_solicitante, v_id_uo_matriz, v_id_uo_sol;

                              END IF;

                              ---

                         END LOOP;

                          --si no se pudo encontrar parametrizaciones  mostramos el error
                         IF ( bandera_matriz = 'no')THEN
                              RAISE EXCEPTION '3. No se encuentra parametrizado el Concepto de Gasto: % en la Matriz Tipo Contratación (Aprobador) para el Sistema de Obligaciones de Pago, id_concepto = %. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_desc_ingas, v_obligacion_det.id_concepto_ingas;
                         END IF;
                                   --

      END LOOP;

      SELECT vf.desc_funcionario1
      INTO	v_nombre_funcionario
      FROM orga.vfuncionario vf
      WHERE vf.id_funcionario =  v_obligacion_pago.id_funcionario;

      --par sacar el funcionario aprobador
      SELECT mm.id_cargo
      INTO v_matriz_cargo
      FROM adq.tmatriz_modalidad mm
      WHERE mm.id_matriz_modalidad =  v_id_matriz_modalidad;

      SELECT  uofun.id_funcionario
      INTO v_funcionario
      FROM orga.tcargo car
      inner join orga.tuo_funcionario uofun on uofun.id_uo = car.id_uo
      WHERE car.id_cargo =  v_matriz_cargo
      and uofun.fecha_asignacion  <=  now()
      and (uofun.fecha_finalizacion is null or uofun.fecha_finalizacion >= now() );
      --

      --control para que no sea el mismo funcionario aprobador con el funcionario solicitante
      IF (v_funcionario = v_obligacion_pago.id_funcionario) THEN
          RAISE EXCEPTION 'El Funcionario % esta como Solicitante y como Funcionario Aprobador, verificar la parametrizacion en la  Matriz Tipo Contratación(Aprobador). Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre).', v_nombre_funcionario;
      END IF;

      --raise EXCEPTION 'llega %', v_id_matriz_modalidad;
      UPDATE tes.tobligacion_pago SET
      id_funcionario_gerente = v_funcionario,
      id_matriz_modalidad = v_id_matriz_modalidad
      WHERE id_obligacion_pago = v_id_obligacion_pago;



      --raise exception 'llegan %',v_resp;

          --Devuelve la respuesta
          return ;

        raise notice '%',v_resp;

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

ALTER FUNCTION tes.ft_solicitud_obligacion_pago (v_id_obligacion_pago integer, v_id_usuario integer)
  OWNER TO postgres;
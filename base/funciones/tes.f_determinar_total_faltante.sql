CREATE OR REPLACE FUNCTION tes.f_determinar_total_faltante (
  p_id_obligacion_pago integer,
  p_filtro varchar = 'registrado'::character varying,
  p_id_plan_pago integer = NULL::integer
)
RETURNS numeric AS
$body$
/*
*
*  Autor:   RAC  (KPLIAN)
*  DESC:    Calcula montos totales sobrantes de diferente tipos para el plan de pagos
*  Fecha:   10/06/2014
*
*/

DECLARE
    v_consulta   		 		varchar;
	v_parametros  				record;
    v_registros                 record;
	v_nombre_funcion   			text;
	v_resp						varchar;
     v_monto_total_registrado 	numeric;
     v_monto_total 				numeric;
     v_monto_total_ejecutar_mo 	numeric;
     v_porc_ant 			  	numeric;
     v_monto_ant_descontado   	numeric;
     v_monto_aux                numeric;

     v_tipo_fil  varchar;
     v_monto    numeric;
     v_monto_total_ajuste_ag    numeric;
     v_monto_ajuste_anticipo  numeric;
     v_monto_ajuste_aplicado numeric;
     v_monto_est_sig_gestion numeric;
     v_total_anticipo   numeric;
     v_monto_pagado numeric;
     v_total_anticipo_mix numeric;
     v_total_devengado numeric;
     v_monto_pagado_1c numeric;
     v_total_aplicado numeric;
     v_total_efectivo  numeric;
     v_total_pago_aplicado  numeric;
     v_saldo_anticipo  numeric;
     v_monto_ajuste numeric;
	 v_id_contrato  				numeric;
     v_id_gestion					numeric;
     v_monto_ajuste_temp 			numeric;
     v_monto_total_registrado_temp 	numeric;
     v_monto_ant_descontado_temp 	numeric;
     g_registros					record;
     v_size_temp					numeric;
     g_contratosh					record;	
     v_gestion						numeric;
     v_id_contrato_list				numeric [];
     v_id_contrato_fk				numeric;
     v_has_contratos				boolean;

BEGIN

	v_nombre_funcion = 'tes.f_determinar_total_faltante';

--raise exception 'llega2 %',p_filtro;
			--(may) 18-07-2019 los tipo pp especial_spi son para las internacionales -tramites SIP
            IF p_filtro not in ('registrado','registrado_pagado','registrado_monto_ejecutar','especial_total','anticipo_sin_aplicar','total_registrado_pagado','devengado','pagado','ant_parcial','ant_parcial_descontado','ant_aplicado_descontado','dev_garantia','ant_aplicado_descontado_op_variable','especial','especial_spi','dev_garantia_con','dev_garantia_con_ant') THEN

              		raise exception 'filtro no reconocido (%) para determinar el total faltante ', p_filtro;

            END IF;

            ---------------------------
            -- devengados registrados
            ---------------------------

            IF   p_filtro = 'registrado' THEN

                     -- determina el total por registrar

                     select
                      op.total_pago,
                      op.monto_estimado_sg
                     into
                       v_monto_total,
                       v_monto_est_sig_gestion
                     from tes.tobligacion_pago op
                     where op.id_obligacion_pago = p_id_obligacion_pago;

                     -- determina el total registrado
                      select
                       sum(pp.monto)
                      into
                       v_monto_total_registrado
                      from tes.tplan_pago pp
                      where  pp.estado_reg='activo'
                            and pp.tipo in('devengado','devengado_pagado','rendicion','anticipo','devengado_pagado_1c', 'devengado_pagado_1c_sp')
                            and pp.id_obligacion_pago = p_id_obligacion_pago;


                      v_monto_total  = COALESCE(v_monto_total,0) +  COALESCE(v_monto_est_sig_gestion,0) - COALESCE(v_monto_total_registrado,0);


                      return v_monto_total;

            ---------------------------------------------------------------------------------
            -- MONTO A EJECUTAR REGISTRADO ,   incluye montos a pagar la siguiente gestion
            ---------------------------------------------------------------------------------

            ELSIF   p_filtro = 'registrado_monto_ejecutar' THEN

                     -- determina el total por registrar

                     select
                      op.total_pago,
                      op.monto_estimado_sg
                     into
                       v_monto_total,
                       v_monto_est_sig_gestion
                     from tes.tobligacion_pago op
                     where op.id_obligacion_pago = p_id_obligacion_pago;

                     -- determina el total registrado
                      select
                       sum(pp.monto_ejecutar_total_mo),
                       sum(pp.monto_anticipo)
                      into
                       v_monto_total_registrado,
                       v_total_anticipo_mix
                      from tes.tplan_pago pp
                      where  pp.estado_reg='activo'
                            and pp.tipo in('devengado','devengado_pagado','rendicion','anticipo','devengado_pagado_1c', 'devengado_pagado_1c_sp')
                            and pp.id_obligacion_pago = p_id_obligacion_pago;


                      v_monto_total = COALESCE(v_monto_total,0) +  COALESCE(v_monto_est_sig_gestion,0) - COALESCE(v_monto_total_registrado,0) - COALESCE(v_total_anticipo_mix,0);


                      return v_monto_total;

             ---------------------------------
             --  REGISTRADO PAGADO POR CUOTA
             ----------------------------------

             ELSEIF     p_filtro in ('registrado_pagado') THEN

                       --  fltro para determinar el total de pago registrado par auna cuota de devenngado

                       select
                        pp.monto_ejecutar_total_mo
                       into
                         v_monto_total_ejecutar_mo
                       from tes.tplan_pago pp
                       where pp.id_plan_pago = p_id_plan_pago;

                       -- determina el total registrado
                        select
                         sum(pp.monto)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg = 'activo'
                              and pp.tipo = 'pagado'
                              and pp.id_plan_pago_fk = p_id_plan_pago;


                        v_monto_total  = COALESCE(v_monto_total_ejecutar_mo,0)- COALESCE(v_monto_total_registrado,0);


                        return v_monto_total;


            -----------------------------------
            -- ANTICIPO APLICADO DESCONTADO
            -----------------------------------

             ELSEIF     p_filtro  = 'ant_aplicado_descontado' THEN

                     v_monto = 0;
                     v_total_anticipo = 0;

                     --  fltro para recupera el total del anticipo total

                     select
                      pp.monto
                     into
                       v_monto
                     from tes.tplan_pago pp
                     where  pp.tipo = 'anticipo' and
                            pp.id_plan_pago = p_id_plan_pago;


                     --  es es una cueta de de enga revisamos el monto_acnticipado
                     select
                        pp.monto_anticipo
                     into
                        v_total_anticipo
                     from tes.tplan_pago pp
                     where  pp.tipo in ('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
                            and pp.id_plan_pago = p_id_plan_pago;


                     -- determina el total registrado  como anticipo faltante
                      select
                       sum(pp.monto)
                      into
                       v_monto_total_registrado
                      from tes.tplan_pago pp
                      where  pp.estado_reg = 'activo'
                            and pp.tipo = 'ant_aplicado'                --recupera la suma de los anticipos aplicados
                            and pp.id_plan_pago_fk = p_id_plan_pago;

                     v_monto_total  = COALESCE(v_monto,0) +  COALESCE(v_total_anticipo, 0) - COALESCE(v_monto_total_registrado,0);


                     return v_monto_total;

            ----------------------------------------------------------------------------------------
            --  Calcula el monto aplicado considerando anticipos totales para obligaciones variables
            ----------------------------------------------------------------------------------------

            ELSEIF     p_filtro  = 'ant_aplicado_descontado_op_variable' THEN

                     v_monto = 0;
                     v_total_anticipo = 0;

                     select
                      op.ajuste_anticipo,
                      op.ajuste_aplicado
                     into
                       v_monto_ajuste_anticipo,
                       v_monto_ajuste_aplicado
                     from tes.tobligacion_pago op
                     where op.id_obligacion_pago = p_id_obligacion_pago;

                     --  recupera el total de anticipos totales

                     select
                      sum(pp.monto)
                     into
                       v_monto
                     from tes.tplan_pago pp
                     where pp.estado_reg = 'activo' and
                           pp.id_obligacion_pago = p_id_obligacion_pago and
                           pp.tipo = 'anticipo';

                     select
                        sum(pp.monto_anticipo)
                     into
                        v_total_anticipo
                     from tes.tplan_pago pp
                     where      pp.tipo in ('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
                            and pp.id_obligacion_pago = p_id_obligacion_pago;

                     -- determina el total registrado  con antticipo faltante
                      select
                       sum(pp.monto),
                       sum(COALESCE(pp.monto_ajuste_ag,0)) --montos ajustado a la anterior gestion
                      into
                       v_monto_total_registrado,
                       v_monto_total_ajuste_ag
                      from tes.tplan_pago pp
                      where  pp.estado_reg = 'activo'
                            and pp.tipo = 'ant_aplicado'                --recupera la suma de los anticipos aplicados
                            and pp.id_obligacion_pago = p_id_obligacion_pago;

                     v_monto_total  = COALESCE(v_monto,0) + COALESCE(v_total_anticipo,0)- COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_total_ajuste_ag,0) + COALESCE(v_monto_ajuste_anticipo) -  COALESCE(v_monto_ajuste_aplicado);
                     return v_monto_total;


           ------------------------------------------------------------------------------------------------------------
            -- TOTAL ANTICIPO SIN APLICAR (CUANTO FALTA POR APLICAR)  si es negativo es que le debemos al proveedor
            ------------------------------------------------------------------------------------------------------

             ELSEIF     p_filtro  = 'anticipo_sin_aplicar' THEN

                     select
                      op.total_pago,
                      op.monto_estimado_sg,
                      op.ajuste_anticipo,
                      op.ajuste_aplicado
                     into
                       v_registros
                     from tes.tobligacion_pago op
                     where op.id_obligacion_pago = p_id_obligacion_pago;

                     --  total anticipado con cuota del tipo anticipo total

                     select
                      sum(pp.monto)
                     into
                       v_total_anticipo
                     from tes.tplan_pago pp
                     where pp.estado_reg = 'activo'
                              and pp.tipo in('anticipo')
                              and pp.estado = 'anticipado'
                              and pp.id_obligacion_pago = p_id_obligacion_pago;

                     --total anticipos mixtos , total devengado
                     select
                      sum(pp.monto_anticipo),
                      sum(pp.monto)
                     into
                       v_total_anticipo_mix,
                       v_total_devengado
                     from tes.tplan_pago pp
                     where pp.estado_reg='activo'
                              and pp.tipo in('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
                              and pp.estado in ( 'devengado')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;

                     /*

                     --total monto pagado con un solo comprobante
                     select
                        sum(pp.monto - pp.monto_anticipo)
                     into
                        v_monto_pagado_1c
                     from tes.tplan_pago pp
                     where pp.estado_reg='activo'
                              and pp.tipo in('devengado_pagado_1c')
                              and pp.estado in ( 'devengado')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;

                     */

                     -- determinar el total aplicado
                      select
                       sum(pp.monto)
                      into
                       v_total_aplicado
                      from tes.tplan_pago pp
                      where  pp.estado_reg = 'activo'
                            and pp.tipo = 'ant_aplicado' -- recupera la suma de los anticipos aplicados
                            and pp.estado = 'aplicado'
                            and pp.id_obligacion_pago = p_id_obligacion_pago;




                  v_saldo_anticipo = COALESCE(v_total_anticipo,0) + COALESCE(v_total_anticipo_mix,0)  - COALESCE(v_total_aplicado,0);


                 return v_saldo_anticipo;


          --------------------------
          --  ANTICIPOS PARCIALES
          ---------------------------

           ELSEIF  p_filtro = 'ant_parcial' THEN
                    --  recupera el monto maximo que se puede anticipar segun politica

                     select
                      op.total_pago
                     into
                       v_monto_total
                     from tes.tobligacion_pago op
                     where op.id_obligacion_pago = p_id_obligacion_pago;

                     -- recupera los anticipo ya registrados
                      select
                         sum(pp.monto)
                      into
                         v_monto_total_registrado
                      from tes.tplan_pago pp
                      where  pp.estado_reg='activo'
                              and pp.tipo in('ant_parcial')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;


                     v_porc_ant = pxp.f_get_variable_global('politica_porcentaje_anticipo')::numeric;

                     IF v_porc_ant is NULL THEN
                       raise exception 'No se tiene valor en la variable global,  politica_porcentaje_anticipo';
                     END IF;


                     return (v_monto_total - COALESCE(v_monto_total_registrado) )*v_porc_ant;


          -------------------------------------
          --  ANTICIPOS PARCIALES DESCONTADOS
          -------------------------------------

            ELSEIF  p_filtro = 'ant_parcial_descontado' THEN

                     --recuperar el total de anticipo parcial que falta descontar

                       select
                        op.monto_ajuste_ret_anticipo_par_ga
                       into
                         v_monto_ajuste
                       from tes.tobligacion_pago op
                       where op.id_obligacion_pago = p_id_obligacion_pago;

                     select
                         sum(pp.monto)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('ant_parcial')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;

                     --recuperar el total de ancitipo parcial descontado

                     select
                         sum(pp.descuento_anticipo)
                        into
                         v_monto_ant_descontado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('pagado','devengado_pagado_1c','devengado_pagado_1c_sp')          --un supeusto es que los descuentos de anticipo solo se hacen en el comprobante de pago
                              and pp.id_obligacion_pago = p_id_obligacion_pago;


                      --todos los  devengado_pagado   que no tienen pago registrado (--basta con que tenga uno ya no se lo cnsidera)
                      select
                         sum(pp.descuento_anticipo)
                      into
                         v_monto_aux
                      from tes.tplan_pago pp
                      where  pp.estado_reg='activo'
                            and pp.tipo in('devengado_pagado')
                            and pp.id_obligacion_pago = p_id_obligacion_pago
                            and  pp.id_plan_pago not in (select
                                                             pp2.id_plan_pago_fk
                                                        from tes.tplan_pago pp2
                                                        where  pp2.estado_reg='activo'
                                                              and pp2.tipo in('pagado')
                                                              and pp2.id_obligacion_pago = p_id_obligacion_pago);


                     --devolver la diferencias, el monto que falta descontar

                     return COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_ajuste,0)  - COALESCE(v_monto_ant_descontado,0) - COALESCE(v_monto_aux,0);


             -------------------------------
             --  DEVOLUCIONES DE GARANTIA
             -------------------------------

             ELSEIF  p_filtro = 'dev_garantia' THEN

                       --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente

                       select
                        op.monto_ajuste_ret_garantia_ga
                       into
                         v_monto_ajuste
                       from tes.tobligacion_pago op
                       where op.id_obligacion_pago = p_id_obligacion_pago;

                        select
                         sum(pp.monto_retgar_mo)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;

                       --  recuperar los montos de garantia devueltos

                        select
                         sum(pp.monto)
                        into
                         v_monto_ant_descontado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('dev_garantia')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;


                       return COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_ajuste,0) - COALESCE(v_monto_ant_descontado,0);

				-------------------------------
				--  DEVOLUCIONES DE GARANTIA POR CONTRATO
				-------------------------------
            
             ELSEIF  p_filtro = 'dev_garantia_con' THEN
            
             --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente
				select op.id_contrato, op.id_gestion
				into v_id_contrato, v_id_gestion
				from tes.tobligacion_pago op
				where op.id_obligacion_pago = p_id_obligacion_pago;	 
                v_id_contrato_list := array_append(v_id_contrato_list, v_id_contrato);
                v_size_temp := (SELECT coalesce(array_length(v_id_contrato_list, 1), 0));
                v_has_contratos := true;  
              WHILE v_has_contratos LOOP
              
                 select cc.id_contrato_fk
                 into v_id_contrato_fk
                 from leg.tcontrato cc
                 where cc.id_contrato = v_id_contrato 
                 and   cc.estado != 'anulado';
                 
                 if (v_id_contrato_fk is not null and (SELECT NOT v_id_contrato_fk = ANY (v_id_contrato_list)))then
                    v_id_contrato_list := array_append(v_id_contrato_list, v_id_contrato_fk);
                 end if;
                                        
                 FOR g_contratosh in (
                                   		select cc.id_contrato
                                        from leg.tcontrato cc
                                        where cc.id_contrato_fk = v_id_contrato
                                        and cc.estado != 'anulado'
										) LOOP
                    if (SELECT NOT g_contratosh.id_contrato = ANY (v_id_contrato_list)) then
                        v_id_contrato_list := array_append(v_id_contrato_list, g_contratosh.id_contrato::numeric);
                    end if;        
                            
                 END LOOP;        
                 
               /*if (v_size_temp = 4)THEN
                 raise exception 'holasgg %',v_id_contrato_list::text;  
               end if; */ 
				--SELECT coalesce(array_length('{1,2,3,6,4}'::int[], 1), 0)  
				if ((SELECT coalesce(array_length(v_id_contrato_list, 1), 0)) > v_size_temp ) then
                	v_size_temp := v_size_temp+1;
                	v_id_contrato := v_id_contrato_list[v_size_temp];
                    
                else
                    v_has_contratos := false;
                    --raise exception 'holas %',v_id_contrato_list::text;   
                end if;  

             END LOOP;
             --raise exception 'holasFF %', v_id_contrato_list::text;       
                FOR g_registros in (
								select 	op.id_obligacion_pago
								from 	tes.tobligacion_pago op
								where 	op.id_contrato = any (v_id_contrato_list) 
                                and     op.id_gestion = v_id_gestion
							) LOOP
								v_monto_ajuste_temp = 0;
								v_monto_total_registrado_temp = 0;
								v_monto_ant_descontado_temp = 0;
								
									   select 
										op.monto_ajuste_ret_garantia_ga
									   into
										 v_monto_ajuste_temp
									   from tes.tobligacion_pago op
									   where op.id_obligacion_pago = g_registros.id_obligacion_pago;
									   v_monto_ajuste = COALESCE(v_monto_ajuste,0) + COALESCE(v_monto_ajuste_temp,0);
									   
										select 
										 sum(pp.monto_retgar_mo)
										into
										 v_monto_total_registrado_temp
										from tes.tplan_pago pp
										where  pp.estado_reg='activo'  
											  and pp.tipo in('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
											  and pp.id_obligacion_pago = g_registros.id_obligacion_pago;
										v_monto_total_registrado = COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_total_registrado_temp,0);
 
										select 
										 sum(pp.monto)
										into
										 v_monto_ant_descontado_temp
										from tes.tplan_pago pp
										where  pp.estado_reg='activo'  
											  and pp.tipo in('dev_garantia','dev_garantia_con','dev_garantia_con_ant') 
											  and pp.id_obligacion_pago = g_registros.id_obligacion_pago;
										v_monto_ant_descontado = COALESCE(v_monto_ant_descontado,0) + COALESCE(v_monto_ant_descontado_temp,0);
								
							END LOOP;
                       
                      
                       return COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_ajuste,0) - COALESCE(v_monto_ant_descontado,0);
            
            -------------------------------
             --  DEVOLUCIONES DE GARANTIA POR CONTRATO GESTION ANTERIOR
             -------------------------------
            
             ELSEIF  p_filtro = 'dev_garantia_con_ant' THEN
            
                --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente
				select op.id_contrato, op.id_gestion
				into v_id_contrato, v_id_gestion
				from tes.tobligacion_pago op
				where op.id_obligacion_pago = p_id_obligacion_pago;	
                
                select gg.gestion
                into v_gestion
                from param.tgestion gg
                where gg.id_gestion = v_id_gestion;
                
                select gg.id_gestion
                into v_id_gestion
                from param.tgestion gg
                where gg.gestion =(v_gestion - 1);
                
                v_id_contrato_list := array_append(v_id_contrato_list, v_id_contrato);
                v_size_temp := (SELECT coalesce(array_length(v_id_contrato_list, 1), 0));
                v_has_contratos := true;  
                
                WHILE v_has_contratos LOOP
                
                   select cc.id_contrato_fk
                   into v_id_contrato_fk
                   from leg.tcontrato cc
                   where cc.id_contrato = v_id_contrato 
                   and   cc.estado != 'anulado';
                   
                   if (v_id_contrato_fk is not null and (SELECT NOT v_id_contrato_fk = ANY (v_id_contrato_list)))then
                      v_id_contrato_list := array_append(v_id_contrato_list, v_id_contrato_fk);
                   end if;
                                          
                   FOR g_contratosh in (
                                          select cc.id_contrato
                                          from leg.tcontrato cc
                                          where cc.id_contrato_fk = v_id_contrato
                                          and cc.estado != 'anulado'
                                          ) LOOP
                      if (SELECT NOT g_contratosh.id_contrato = ANY (v_id_contrato_list)) then
                          v_id_contrato_list := array_append(v_id_contrato_list, g_contratosh.id_contrato::numeric);
                      end if;        
                              
                   END LOOP;        
                     
                  if ((SELECT coalesce(array_length(v_id_contrato_list, 1), 0)) > v_size_temp ) then
                      v_size_temp := v_size_temp+1;
                      v_id_contrato := v_id_contrato_list[v_size_temp];
                  else
                      v_has_contratos := false;   
                  end if;  

               END LOOP;
                  
                FOR g_registros in (
								select 	op.id_obligacion_pago
								from 	tes.tobligacion_pago op
								where 	op.id_contrato = any (v_id_contrato_list)
                                and     op.id_gestion = v_id_gestion
							) LOOP
								v_monto_ajuste_temp = 0;
								v_monto_total_registrado_temp = 0;
								v_monto_ant_descontado_temp = 0;
								
									   select 
										op.monto_ajuste_ret_garantia_ga
									   into
										 v_monto_ajuste_temp
									   from tes.tobligacion_pago op
									   where op.id_obligacion_pago = g_registros.id_obligacion_pago;
									   v_monto_ajuste = COALESCE(v_monto_ajuste,0) + COALESCE(v_monto_ajuste_temp,0);
									   
										select 
										 sum(pp.monto_retgar_mo)
										into
										 v_monto_total_registrado_temp
										from tes.tplan_pago pp
										where  pp.estado_reg='activo'  
											  and pp.tipo in('devengado','devengado_pagado','devengado_pagado_1c','devengado_pagado_1c_sp')
											  and pp.id_obligacion_pago = g_registros.id_obligacion_pago;
										v_monto_total_registrado = COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_total_registrado_temp,0);
 
										select 
										 sum(pp.monto)
										into
										 v_monto_ant_descontado_temp
										from tes.tplan_pago pp
										where  pp.estado_reg='activo'  
											  and pp.tipo in('dev_garantia') 
											  and pp.id_obligacion_pago = g_registros.id_obligacion_pago;
										v_monto_ant_descontado = COALESCE(v_monto_ant_descontado,0) + COALESCE(v_monto_ant_descontado_temp,0);
								
							END LOOP;
                       
                      
                       return COALESCE(v_monto_total_registrado,0) + COALESCE(v_monto_ajuste,0) - COALESCE(v_monto_ant_descontado,0);
            
             -------------------------------
             --  Pagos especiales
             -------------------------------

             ELSEIF  p_filtro = 'especial' THEN

                       --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente

                       select
                        sum(op.monto_pago_mo)
                       into
                         v_monto_ajuste
                       from tes.tobligacion_det op
                       where op.id_obligacion_pago = p_id_obligacion_pago
                          and op.estado_reg = 'activo';



                        select
                         sum(pp.monto)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('especial')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;




                       return COALESCE(v_monto_ajuste,0) - COALESCE(v_monto_total_registrado,0);

            -------------------------------
             --  Pagos especiales  en edicion
             -------------------------------

             ELSEIF  p_filtro = 'especial_total' THEN

                       --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente

                       select
                        sum(op.monto_pago_mo)
                       into
                         v_monto_ajuste
                       from tes.tobligacion_det op
                       where op.id_obligacion_pago = p_id_obligacion_pago
                          and op.estado_reg = 'activo';






                       return COALESCE(v_monto_ajuste,0);


             ---------------------------
             -- TOTAL REGISTRADO PAGADO
             ---------------------------

            ELSEIF     p_filtro in ('total_registrado_pagado') THEN

                       --  fltro para determinar el total de pago registrado para
                       --  la obligacion de pago

                       -- determina el total a pagar

                       select
                        op.total_pago,
                        op.monto_estimado_sg
                       into
                         v_monto_total,
                         v_monto_est_sig_gestion
                       from tes.tobligacion_pago op
                       where op.id_obligacion_pago = p_id_obligacion_pago;

                        --recupera el total de dinero pagado

                        select
                         sum(pp.monto)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('pagado','devengado_pagado_1c','anticipo','devengado_pagado_1c_sp')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;


                         --todos los  devengado_pagado   que no tienen pago registrado (--basta con que tenga uno ya no se lo cnsidera)
                        select
                           sum(pp.monto)
                        into
                           v_monto_aux
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('devengado_pagado')
                              and pp.id_obligacion_pago = p_id_obligacion_pago
                              and  pp.id_plan_pago not in (select
                                                               pp2.id_plan_pago_fk
                                                          from tes.tplan_pago pp2
                                                          where  pp2.estado_reg='activo'
                                                                and pp2.tipo in('pagado')
                                                              and pp2.id_obligacion_pago = p_id_obligacion_pago);


                        v_monto_total  = COALESCE(v_monto_total,0)+ COALESCE(v_monto_est_sig_gestion,0) - COALESCE(v_monto_total_registrado,0) - COALESCE(v_monto_aux,0);


                        return v_monto_total;


            --(may) 18-07-2019 para las internacionales tramites SIP
             ----------------------------------------------
             --  Pagos especiales para las internacionales
             ----------------------------------------------

             ELSEIF  p_filtro = 'especial_spi' THEN

                       --recupera los montos retenidos por garantia en las trasacciones de devengado unicamente

                       select
                        sum(op.monto_pago_mo)
                       into
                         v_monto_ajuste
                       from tes.tobligacion_det op
                       where op.id_obligacion_pago = p_id_obligacion_pago
                          and op.estado_reg = 'activo';



                        select
                         sum(pp.monto)
                        into
                         v_monto_total_registrado
                        from tes.tplan_pago pp
                        where  pp.estado_reg='activo'
                              and pp.tipo in('especial_spi')
                              and pp.id_obligacion_pago = p_id_obligacion_pago;




                       return COALESCE(v_monto_ajuste,0) - COALESCE(v_monto_total_registrado,0);



            ELSE

                     raise exception 'Los otros filtros no fueron implementados %',p_filtro;

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
COST 100;

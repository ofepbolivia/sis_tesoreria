CREATE OR REPLACE FUNCTION tes.ft_conciliacion_bancaria_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Obligaciones de Pago
 FUNCION: 		tes.ft_conciliacion_bancaria_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'tes.tconciliacion_bancaria'
 AUTOR: 		Breydi vasquez pacheco
 FECHA:	        19-02-2019
 COMENTARIOS:			
***************************************************************************/

DECLARE

	v_nro_requerimiento    	    integer;
	v_parametros           	    record;
	v_id_requerimiento     	    integer;
	v_resp		                varchar;
	v_nombre_funcion            text;
	v_mensaje_error             text;
	v_id_conciliacion_bancaria	integer;
    v_fecha_ini					date;
    v_fecha_fin 				date;    
    v_periodo					integer;
    v_fecha_repo				date;
    v_consulta					varchar;
   	v_consu			    		varchar;
    v_column 					varchar;
    v_saldo_real_2				numeric;    
    v_saldo_real_1				numeric;    
    resp 						numeric;
    fecha_r						timestamp;
    v_estacion					varchar;
    v_filtro					varchar;
    v_cont 						integer;
	v_offset					integer; 
    v_max						integer;
    v_depo_transito				numeric;
    v_debito_bancario			numeric;
    v_credito_bancario          numeric;			    
BEGIN

    v_nombre_funcion = 'tes.ft_conciliacion_bancaria_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'TES_CONCBAN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Breydi vasquez pacheco	
 	#FECHA:		19-02-2019
	***********************************/

	if(p_transaccion='TES_CONCBAN_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into tes.tconciliacion_bancaria(
			estado_reg,
			id_cuenta_bancaria,
            id_gestion,
            id_periodo,
            id_funcionario_elabo,
            id_funcionario_vb,
            fecha,
            observaciones,
            saldo_banco,
            estado,            
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod
          	) values(
			'activo',
			v_parametros.id_cuenta_bancaria,
			v_parametros.id_gestion,
            v_parametros.id_periodo,
            null,
            null,
            v_parametros.fecha,
            v_parametros.observaciones,
            v_parametros.saldo_banco,
            'proceso',
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null												
			)RETURNING id_conciliacion_bancaria into v_id_conciliacion_bancaria;


            create temporary table tt_conciliacion(_id_usuario_ai int4, _nombre_usuario_ai varchar,
            id_conciliacion_bancaria int4,id_cuenta_bancaria INT4,id_gestion INT4,id_periodo INT4) on commit drop;

            insert into tt_conciliacion
            values (null, 'null', v_id_conciliacion_bancaria,v_parametros.id_cuenta_bancaria,v_parametros.id_gestion,v_parametros.id_periodo);

             v_resp = tes.ft_conciliacion_bancaria_ime (
             p_administrador,
             p_id_usuario,
             'tt_conciliacion',
             'TES_DETCOBREP_INS');

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conciliacion almacenado(a) con exito (id_conciliacion_bancaria'||v_id_conciliacion_bancaria||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_conciliacion_bancaria',v_id_conciliacion_bancaria::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'TES_CONCBAN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Breydi vasquez pacheco	
 	#FECHA:		19-02-2019
	***********************************/

	elsif(p_transaccion='TES_CONCBAN_MOD')then

		begin
			--Sentencia de la modificacion
			update tes.tconciliacion_bancaria set
			id_gestion = v_parametros.id_gestion,
            id_periodo = v_parametros.id_periodo,
            id_funcionario_elabo = null,
            id_funcionario_vb = null,
            fecha = v_parametros.fecha,
            observaciones = v_parametros.observaciones,
            saldo_banco = v_parametros.saldo_banco,
            id_usuario_mod = p_id_usuario,
            fecha_mod = now()                       
			where id_conciliacion_bancaria=v_parametros.id_conciliacion_bancaria;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conciliacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_conciliacion_bancaria',v_parametros.id_conciliacion_bancaria::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'TES_CONCBAN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Breydi vasquez pacheco	
 	#FECHA:		19-02-2019
	***********************************/

	elsif(p_transaccion='TES_CONCBAN_ELI')then

		begin
           if exists(select 1 from tes.tdetalle_conciliacion_bancaria
                where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria) then
              raise exception 'Elimine el detalle previamente y vuelva a intentarlo';
            end if;         
			--Sentencia de la eliminacion
			delete from tes.tconciliacion_bancaria
            where id_conciliacion_bancaria=v_parametros.id_conciliacion_bancaria;

            delete from tes.tconciliacion_bancaria_rep
            where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;
                           
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conciliacion eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_conciliacion_bancaria',v_parametros.id_conciliacion_bancaria::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;

/*********************************
 	#TRANSACCION:  'TES_DETCOBREP_INS'
 	#DESCRIPCION:	Registro de detalle conciliacion bancaria
 	#AUTOR:		BVP
 	#FECHA:		18-09-2019
	***********************************/

	elsif(p_transaccion='TES_DETCOBREP_INS')then

		begin            
       	
	      --Sentencia de la consulta de conteo de registros        
          v_periodo = v_parametros.id_periodo;

              select per.fecha_fin
                     into v_fecha_repo                
              from param.tperiodo per 
              where per.id_periodo = v_periodo;
         
         if ( (select estado from tes.tconciliacion_bancaria where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria) = 'inactivo')then
         	 raise exception 'La conciliacion ya fue Finalizada';
         else 
             delete from tes.tconciliacion_bancaria_rep 
             where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;             
         end if; 
      	 
         for i in 1..3 loop
         
              select per.fecha_ini, per.fecha_fin
               into  v_fecha_ini, v_fecha_fin
              from param.tperiodo per 
              where per.id_periodo = v_periodo;
              
          if i = 1 then
          	v_column = 'periodo_1';
          elsif i = 2 then
	          v_column = 'periodo_2';
          elsif i = 3 then
	          v_column = 'periodo_3';
          end if;  
          
              v_consu = '
                insert into  tes.tconciliacion_bancaria_rep 
                (
                  id_conciliacion_bancaria,
                  id_libro_bancos, 
                  id_libro_bancos_fk,
                  id_periodo,
                  nro_cheque,
                  '||v_column ||',
                  estado,
                  total_haber,
                  detalle,
                  observacion,
                  fecha,
                  estado_reg,
                  fecha_reg,
                  usuario_ai,
                  id_usuario_reg,
                  id_usuario_mod         
                )            
                   select
                    '||v_parametros.id_conciliacion_bancaria||',
                    lb.id_libro_bancos,
                    lb.id_libro_bancos_fk,
                    '||v_periodo||',
                    lb.nro_cheque,
                    case when LB.importe_cheque = 0 and LB.estado <> ''anulado'' then
                        NULL
                    else
                        LB.importe_cheque
                    end as haber,
                    lb.estado, 
                  (Select sum(lbr.importe_cheque)
                                   From tes.tts_libro_bancos lbr
                                   where lbr.fecha BETWEEN  '''||v_fecha_ini||''' and  LB.fecha
                                 and lbr.id_cuenta_bancaria = LB.id_cuenta_bancaria
                                   and 
                                    lbr.estado in (''impreso'', ''entregado'' )
                                    and
                                   lbr.tipo in   (''cheque'',
                                                  ''deposito'',
                                                  ''debito_automatico'',
                                                  ''transferencia_carta'')
                                    ) as total_haber,
                  lb.detalle,
                  lb.observaciones,
                  lb.fecha,
                  ''activo'',
                  now(),
                  '||v_parametros._nombre_usuario_ai||',
                  '||p_id_usuario||',
                  null
                  FROM tes.tts_libro_bancos LB
                  left join tes.tts_libro_bancos lbp on lbp.id_libro_bancos=LB.id_libro_bancos_fk
                  left join tes.tcuenta_bancaria cuba on cuba.id_cuenta_bancaria = lb.id_cuenta_bancaria
                  left join param.tinstitucion ins on ins.id_institucion = cuba.id_institucion
                  left join param.tmoneda mon on mon.id_moneda = cuba.id_moneda
                  WHERE
                  LB.id_cuenta_bancaria = '||v_parametros.id_cuenta_bancaria ||' and
                  LB.fecha BETWEEN  '''||v_fecha_ini||''' and   '''||v_fecha_fin||'''
                  and LB.estado in (''impreso'', ''entregado'' )
                  and
                  LB.tipo in   (''cheque'',
                                                  ''deposito'',
                                                  ''debito_automatico'',
                                                  ''transferencia_carta'')              
                    order by lb.fecha, lbp.indice, lb.nro_cheque asc';
                    
              EXECUTE(v_consu);
              
              v_periodo = v_periodo - 1;
              
          end loop;
          

           v_estacion = pxp.f_get_variable_global('ESTACION_inicio');          
          
           IF v_estacion = 'BOL' THEN
              v_filtro =  'BOL';
            ELSIF v_estacion = 'BUE' THEN
              v_filtro =  'BUE';
            ELSIF v_estacion = 'MIA' THEN
              v_filtro =  'MIA';
            ELSIF v_estacion = 'SAO' THEN
              v_filtro =  'SAO';
            ELSIF v_estacion = 'MAD' THEN
              v_filtro =  'MAD';
            END IF;
            
            select per.fecha_ini, per.fecha_fin
             into  v_fecha_ini, v_fecha_fin
            from param.tperiodo per 
            where per.id_periodo = v_parametros.id_periodo; 
            

            SELECT	
                count(lb.id_cuenta_bancaria)
                into v_offset
                FROM tes.tts_libro_bancos LB
                LEFT JOIN tes.tts_libro_bancos lbp on lbp.id_libro_bancos=LB.id_libro_bancos_fk
                WHERE
                LB.id_cuenta_bancaria = v_parametros.id_cuenta_bancaria and
                LB.fecha BETWEEN  v_fecha_ini and   v_fecha_fin
                and   LB.estado in ('impreso',
                                         'entregado','cobrado',
                                         'anulado','reingresado',
                                         'depositado','transferido',
                                         'sigep_swift' )
              and   LB.tipo in   (select  fpa.codigo
                                  from  param.tforma_pago fpa
                                  where fpa.codigo not in 
                                  ('transf_interna_debe','transf_interna_haber'
                                  ,'transferencia_interna')
                                  and (''||v_filtro||''=ANY(fpa.cod_inter))
                                  )

              and LB.id_finalidad in (select fina.id_finalidad
                                      from tes.tfinalidad fina); 

                   
                if v_offset = 0 then 
                    v_max = 0;
                else                           
                    v_max = v_offset::integer - 1;
                end if;                       
                       
            SELECT
            (Select sum(lbr.importe_deposito) - sum(lbr.importe_cheque)
               From tes.tts_libro_bancos lbr
               where
               lbr.id_cuenta_bancaria = LB.id_cuenta_bancaria
               and lbr.estado not in ('anulado','borrador')
               and ((lbr.fecha <= LB.fecha) or (lbr.fecha = LB.fecha and lbr.indice <= LB.indice))
              )  into resp 
              FROM tes.tts_libro_bancos LB
              LEFT JOIN tes.tts_libro_bancos lbp on lbp.id_libro_bancos=LB.id_libro_bancos_fk
              WHERE
              LB.id_cuenta_bancaria = v_parametros.id_cuenta_bancaria
              and
              lb.fecha between v_fecha_ini and v_fecha_fin 
              and
              LB.estado in ('impreso',
                                       'entregado','cobrado',
                                       'anulado','reingresado',
                                       'depositado','transferido',
                                       'sigep_swift' )

              and   LB.tipo in   (select  fpa.codigo
                                  from  param.tforma_pago fpa
                                  where fpa.codigo not in 
                                  ('transf_interna_debe','transf_interna_haber'
                                  ,'transferencia_interna')
                                  and (''||v_filtro||''=ANY(fpa.cod_inter))
                                  )

              and LB.id_finalidad in (select fina.id_finalidad
                                      from tes.tfinalidad fina)
              order by lb.fecha, lb.indice, lb.nro_cheque asc 
              offset v_max;


          if resp is null  then 
                  Select sum(Coalesce(lbr.importe_deposito,0))-sum(coalesce(lbr.importe_cheque))
                   into resp
                   From tes.tts_libro_bancos lbr
                   Where lbr.fecha < v_fecha_ini
                   and lbr.id_cuenta_bancaria = v_parametros.id_cuenta_bancaria
                   and lbr.estado not in ('anulado', 'borrador');
          end if;
                     
                   
          update tes.tconciliacion_bancaria set
          saldo_libros = resp
          where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;
          
          select ( con.saldo_banco - (coalesce(sum(periodo_1),0) + coalesce(sum(periodo_2),0) + coalesce(sum(periodo_3),0) ))
          into v_saldo_real_2
          from tes.tconciliacion_bancaria_rep cre 
          inner join tes.tconciliacion_bancaria con on con.id_conciliacion_bancaria = cre.id_conciliacion_bancaria
          where  cre.id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria
          group by con.saldo_banco;
          
          select coalesce(sum(importe),0)
	          into v_debito_bancario
          from tes.tdetalle_conciliacion_bancaria 
          where  tipo = 'cheque' and id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;        

          select coalesce(sum(importe),0)
	          into v_credito_bancario
          from tes.tdetalle_conciliacion_bancaria 
          where  tipo = 'deposito' and id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;        
                              
          select coalesce(sum(importe),0)
	          into v_depo_transito
          from tes.tdetalle_conciliacion_bancaria 
          where  tipo = 'transito' and id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;        
                    
          update tes.tconciliacion_bancaria set
          saldo_real_1 = resp - v_debito_bancario + v_credito_bancario,
          saldo_real_2 = v_saldo_real_2 + v_depo_transito          
          where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;
                     
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conciliacion detalle rep almacenado(a) con exito '); 
            --Devuelve la respuesta
            return v_resp;
		end; 
        
      /*********************************
      #TRANSACCION:  'TES_FINCONCI_FIN'
      #DESCRIPCION: Finalizar conciliacion bancaria
      #AUTOR:		BVP
      #FECHA:		18-09-2019
      ***********************************/

      elsif(p_transaccion='TES_FINCONCI_FIN')then

          begin
            update tes.tconciliacion_bancaria set
            estado = 'finalizado'
            where id_conciliacion_bancaria = v_parametros.id_conciliacion_bancaria;            
			
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Conciliacion modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_conciliacion_bancaria',v_parametros.id_conciliacion_bancaria::varchar);
               
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
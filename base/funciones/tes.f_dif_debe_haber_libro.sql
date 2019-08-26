CREATE OR REPLACE FUNCTION tes.f_dif_debe_haber_libro (
  p_id_cuenta_bancaria integer
)
RETURNS numeric AS
$body$
DECLARE
  resp 			numeric;
  fecha_r		timestamp;
  v_estacion	varchar;
  v_filtro		varchar;
  v_cont 		integer;
BEGIN

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
        LB.id_cuenta_bancaria = p_id_cuenta_bancaria
        and
        lb.fecha between current_date and current_date 
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
		order by lb.fecha, lb.indice, lb.nro_cheque asc;

    if resp is null  then 
            Select sum(Coalesce(lbr.importe_deposito,0))-sum(coalesce(lbr.importe_cheque))
             into resp
             From tes.tts_libro_bancos lbr
             Where lbr.fecha < current_date
             and lbr.id_cuenta_bancaria = p_id_cuenta_bancaria
             and lbr.estado not in ('anulado', 'borrador');
    end if;    
    
  	return resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
CREATE OR REPLACE FUNCTION tes.f_dif_debe_haber_libro (
  p_id_cuenta_bancaria integer
)
RETURNS numeric AS
$body$
DECLARE
  resp 			numeric;
BEGIN
select 
         
      (SELECT
      
        coalesce((Select sum(Coalesce(lbr.importe_deposito,0))-sum(coalesce(lbr.importe_cheque,0))
                 From tes.tts_libro_bancos lbr
                 Where lbr.fecha < current_date::date
                 and lbr.id_cuenta_bancaria = p_id_cuenta_bancaria
                 and lbr.estado not in ('anulado', 'borrador') ),0.00) as saldo
      )
      UNION (SELECT


      (Select sum(lbr.importe_deposito) - sum(lbr.importe_cheque)
         From tes.tts_libro_bancos lbr
         where
         lbr.id_cuenta_bancaria = LB.id_cuenta_bancaria
         and lbr.estado not in ('anulado','borrador')
         and ((lbr.fecha < LB.fecha) or (lbr.fecha = LB.fecha and lbr.indice <= LB.indice))
        ) as saldo


        FROM tes.tts_libro_bancos LB
        LEFT JOIN tes.tts_libro_bancos lbp on lbp.id_libro_bancos=LB.id_libro_bancos_fk
        WHERE
        LB.id_cuenta_bancaria = p_id_cuenta_bancaria
        and
        LB.fecha BETWEEN  now() and   now() and
        LB.estado in ('impreso',
                                 'entregado','cobrado',
                                 'anulado','reingresado',
                                 'depositado','transferido',
                                 'sigep_swift' )

        and LB.tipo in   ('cheque',
                                        'deposito',
                                        'debito_automatico',
                                        'transferencia_carta')

        and LB.id_finalidad in (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13)


        ) into resp;

	return resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
CREATE OR REPLACE FUNCTION tes.f_dif_debe_haber_libro (
  p_id_cuenta_bancaria integer
)
RETURNS numeric AS
$body$
DECLARE
  resp 			record;
BEGIN
select 
     lban.id_cuenta_bancaria,
     (sum(lban.importe_deposito) - sum(lban.importe_cheque)) as dif
     into resp
     from tes.vlibro_bancos lban  
     left join cd.tdeposito_cd td on td.id_libro_bancos = lban.id_libro_bancos
     left join cd.tcuenta_doc tc on tc.id_cuenta_doc = td.id_cuenta_doc 
     left join conta.tint_comprobante com on com.id_int_comprobante=tc.id_int_comprobante
     where   lban.id_cuenta_bancaria = p_id_cuenta_bancaria
   --  and tc.id_gestion = 17
	group by 
         lban.id_cuenta_bancaria;

return resp.dif;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
PARALLEL UNSAFE
COST 100;

ALTER FUNCTION tes.f_dif_debe_haber_libro (p_id_cuenta_bancaria integer)
  OWNER TO postgres;
CREATE OR REPLACE FUNCTION tes.f_generar_comprobante_convertido (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_comprobante integer
)
RETURNS varchar [] AS
$body$
/* Autor:   Ismael Valdivia
*  DESC:     Generara las cuotas del comprobante devengado para generar en la siguiente gestion.
*  Fecha:   04/01/2021
*
*/

DECLARE

    v_resp    			varchar;
    v_id_plantilla_comprobante		integer;
    v_registros_cd		record;
    estado_cbte			varchar;

    v_id_proceso_wf		integer;
    v_id_estado_wf		integer;
    v_codigo_estado		varchar;
    v_id_tipo_estado	integer;
    v_codigo_estado_siguiente varchar;
    v_result			varchar;
    v_funcion_comprobante_validado	varchar;
    v_ejecutar			varchar;
    v_respuesta 					varchar[];

BEGIN

	select id_plantilla_comprobante
    into v_id_plantilla_comprobante
    from conta.tint_comprobante
    where id_int_comprobante= p_id_comprobante;

    if (v_id_plantilla_comprobante is null) then
    	raise exception 'El comprobante ID: %, no tiene configurada una plantilla', p_id_comprobante;
    end if;

    select
     pc.funcion_comprobante_validado
    into v_funcion_comprobante_validado
    from conta.tplantilla_comprobante pc
    where pc.id_plantilla_comprobante = v_id_plantilla_comprobante;


    IF  v_funcion_comprobante_validado is not null and v_funcion_comprobante_validado != '' THEN
                 	if p_id_comprobante not in (118556, 113189,111900,111906,111870,111850,111907,113938,115044,111909,113937,113942,111911,111922,113945,113199,111903,111800,
                                                113280,111924,111925,113186,111926,114035,111857,113974,111868,113147,111819,115043,111836,111845,111841,111867,111834,
                                                111831,115337,113956,114007,113996,113957,114002,114000,113986,113993,113989,113948,113951,113953,113787,113274,111981,
                                                113790,116295,116294,114238,115038,115036,111392,112264,114068,115440,110490,111187,111873,111846,111862,111820,113027,
                                                110549,111824,111854,113965,111983,112094,113967,112092,111828,113969,110552,111825,111973,111914,111901,113228,111802,
                                                113041,111927,111875,111877,110546,111863,113340,111811,115228,111842,111808,111814,111916,111928,115231,111908,115224,
                                                113144,111856,113142,113028,115317,115324,113337,113150,111979,113154,113339,113970,113338,113244,113227,113029,113292,
                                                114061,113226,113175,114058,113169,113982,114054,113225,113972,110550,113192,111851,113973,111840,113224,111977,111975,
                                                113246,113223,111806,113030,111804,111816,115226,113220,111792,113176,113218,111920,113217,111918,113215,113975,114843,
                                                113173,113174,113210,113977,113979,113170,115326,113981,113167,113033,115309,114052,116049,116046,116053,116051,116055,
                                                111835,113209,113032,110212,111912,112675,112347,113983,114681,114680,114685,110252,116059,114240,116288,114673,112635,
                                                113377,113376,112309,114460,114671,114027,114403,112693,113325,114318,114319,115548,114748,110082,110076,111699,113831,
                                                115684,116305,116308,116300,116298,116303,109076,109079,109137,109280,114414,115234,110409,113984,110411,110578,110415,
                                                110580,110417,110419,110421,113206,109608,109592,109590,109586,109581,109576,109574,109572,109570,110554,110556,109513,
                                                109518,109527,111826,109536,115333,109543,109553,109557,109559,109563,115308,110345,110340,109758,109756,110341,110342,
                                                109744,114048,109746,109748,109754,109751,110343,110344,109753,114046,110243,110245,109762,109764,109766,109640,109641,
                                                109642,110305,110307,110333,110309,110311,110313,110315,110317,110320,110323,110241,110273,110262,110326,110330,111929,
                                                113959,113248,111930,111864,113201,113250,113203,111931,111923,110247,110128,110130,110134,114587,114260,110008,110017,
                                                113121,113123,110019,110021,114582,114579,109999,116040,116044,109997,109671,110005,110010,110587,112283,115078,110606,
                                                114863,109626,111759,112674,109901,110589,111233,112294,111243,111238,111236,112286,112684,111241,115427,114232,110159,
                                                113095,114321,114316,113085,113098,113100,113102,113093,115546,115089,114415,114689,115556,115555,115558,115550)
                    then
                    	v_ejecutar = (tes.f_gestionar_cuota_plan_pago_devengado (p_id_usuario, p_id_usuario_ai, p_usuario_ai, p_id_comprobante, null))::varchar;
                	end if;
                 end IF;


 v_respuesta[1]= 'TRUE';



RETURN   v_respuesta;



EXCEPTION

	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION tes.f_generar_comprobante_convertido (p_id_usuario integer, p_id_usuario_ai integer, p_usuario_ai varchar, p_id_comprobante integer)
  OWNER TO postgres;

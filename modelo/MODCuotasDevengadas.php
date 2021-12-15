<?php
/**
 *@package pXP
 *@file gen-MODCuotasDevengadas.php
 *@author  (admin)
 *@date 25-11-2021 13:15:23
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODCuotasDevengadas extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarCuotas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.ft_cuotas_devengadas_sel';
        $this->transaccion='TES_DEVEN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        //Definicion de la lista del resultado del query
        $this->captura('id_plan_pago','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('nro_cuota','numeric');
        $this->captura('monto_ejecutar_total_mb','numeric');
        $this->captura('nro_sol_pago','varchar');
        $this->captura('tipo_cambio','numeric');
        $this->captura('fecha_pag','date');
        $this->captura('id_proceso_wf','int4');
        $this->captura('fecha_dev','date');
        $this->captura('estado','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('monto_ejecutar_total_mo','numeric');
        $this->captura('descuento_anticipo_mb','numeric');
        $this->captura('obs_descuentos_anticipo','text');
        $this->captura('id_plan_pago_fk','int4');
        $this->captura('id_obligacion_pago','int4');
        $this->captura('id_plantilla','int4');
        $this->captura('descuento_anticipo','numeric');
        $this->captura('otros_descuentos','numeric');
        $this->captura('tipo','varchar');
        $this->captura('obs_monto_no_pagado','text');
        $this->captura('obs_otros_descuentos','text');
        $this->captura('monto','numeric');
        $this->captura('id_int_comprobante','int4');
        $this->captura('nombre_pago','varchar');
        $this->captura('monto_no_pagado_mb','numeric');
        $this->captura('monto_mb','numeric');
        $this->captura('id_estado_wf','int4');
        $this->captura('id_cuenta_bancaria','int4');
        $this->captura('otros_descuentos_mb','numeric');
        $this->captura('forma_pago','varchar');
        $this->captura('monto_no_pagado','numeric');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('fecha_tentativa','date');
        $this->captura('desc_plantilla','varchar');
        $this->captura('liquido_pagable','numeric');
        $this->captura('total_prorrateado','numeric');
        $this->captura('total_pagado','numeric');
        $this->captura('desc_cuenta_bancaria','text');
        $this->captura('sinc_presupuesto','varchar');
        $this->captura('monto_retgar_mb','numeric');
        $this->captura('monto_retgar_mo','numeric');
        $this->captura('descuento_ley','numeric');
        $this->captura('obs_descuentos_ley','text');
        $this->captura('descuento_ley_mb','numeric');
        $this->captura('porc_descuento_ley','numeric');
        $this->captura('nro_cheque','integer');
        $this->captura('nro_cuenta_bancaria','varchar');
        $this->captura('id_cuenta_bancaria_mov','integer');
        $this->captura('desc_deposito','varchar');
        $this->captura('numero_op','varchar');
        $this->captura('id_depto_conta','integer');
        $this->captura('id_moneda','integer');
        $this->captura('tipo_moneda','varchar');
        $this->captura('desc_moneda','varchar');
        $this->captura('num_tramite','varchar');
        $this->captura('porc_monto_excento_var','numeric');
        $this->captura('monto_excento','numeric');
        $this->captura('obs_wf','text');
        $this->captura('obs_descuento_inter_serv','text');
        $this->captura('descuento_inter_serv','numeric');
        $this->captura('porc_monto_retgar','numeric');
        $this->captura('desc_funcionario1','text');
        $this->captura('revisado_asistente','varchar');
        $this->captura('conformidad','text');
        $this->captura('fecha_conformidad','date');
        $this->captura('tipo_obligacion','varchar');
        $this->captura('monto_ajuste_ag','numeric');
        $this->captura('monto_ajuste_siguiente_pag','numeric');
        $this->captura('pago_variable','varchar');
        $this->captura('monto_anticipo','numeric');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('fecha_conclusion_pago','date');
        $this->captura('funcionario_wf','text');
        $this->captura('tiene_form500','varchar');
        $this->captura('id_depto_lb','integer');
        $this->captura('desc_depto_lb','varchar');
        $this->captura('ultima_cuota_dev','numeric');

        $this->captura('id_depto_conta_pp','integer');
        $this->captura('desc_depto_conta_pp','varchar');
        $this->captura('contador_estados','bigint');
        $this->captura('prioridad_lp','integer');
        $this->captura('es_ultima_cuota','boolean');
        $this->captura('nro_cbte','varchar');
        $this->captura('c31','varchar');
        $this->captura('id_gestion','integer');
        $this->captura('fecha_cbte_ini','date');
        $this->captura('fecha_cbte_fin','date');

        $this->captura('monto_establecido','numeric');
        $this->captura('id_proveedor','int4');
        $this->captura('nit','varchar');

        $this->captura('id_proveedor_cta_bancaria','integer');
        $this->captura('id_multa','integer');
        $this->captura('desc_multa','varchar');

        $this->captura('id_obligacion_pago_extendida','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function solicitarDevPag(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_cuotas_devengadas_ime';
        $this->transaccion='TES_SOL_DEVENG_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');
        $this->setParametro('id_depto_conta','id_depto_conta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>

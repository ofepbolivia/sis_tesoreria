<?php

class MODSolicitudPlanPago extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_solicitud_plan_pago_sel';
        $this->transaccion='TES_SOPLAPA_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('tipo_interfaz','tipo_interfaz','varchar');
        $this->setParametro('historico','historico','varchar');

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
        $this->captura('id_doc_compra_venta','integer');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_solicitud_plan_pago_ime';

        if (in_array($this->objParam->getParametro('tipo'), array("devengado_pagado","devengado","devengado_pagado_1c_sp","especial_spi","especial"))){
            /////////////////////////////
            // Cuotas de primer nivel que tienen prorateo
            //////////////////////////////
            $this->transaccion='TES_SOPLAPA_INS';    //para cuotas de devengado

        }

        elseif (in_array($this->objParam->getParametro('tipo'), array("pagado","ant_aplicado"))){

            ///////////////////////////////////////////////
            // Cuotas de segundo  nivel que tienen prorateo  (dependen de un pan de pago)
            /////////////////////////////////////////////
            $this->transaccion='TES_SOPLAPAPA_INS';  //para cuotas de pago

        }

        elseif (in_array($this->objParam->getParametro('tipo'), array("ant_parcial","anticipo","dev_garantia"))){
            ///////////////////////////////////////////////
            // Cuotas de primer nivel que no tienen prorrateo
            /////////////////////////////////////////////
            $this->transaccion='TES_SOPPANTPAR_INS';  //anticipo parcial

        }
        else{
            throw new Exception('No se reconoce el tipo: '.$this->objParam->getParametro('tipo'));
        }


        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('tipo_pago','tipo_pago','varchar');
        $this->setParametro('monto_ejecutar_total_mo','monto_ejecutar_total_mo','numeric');
        $this->setParametro('obs_descuentos_anticipo','obs_descuentos_anticipo','text');
        $this->setParametro('id_plan_pago_fk','id_plan_pago_fk','int4');
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('id_plantilla','id_plantilla','int4');
        $this->setParametro('descuento_anticipo','descuento_anticipo','numeric');
        $this->setParametro('otros_descuentos','otros_descuentos','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('obs_monto_no_pagado','obs_monto_no_pagado','text');
        $this->setParametro('obs_otros_descuentos','obs_otros_descuentos','text');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('nombre_pago','nombre_pago','varchar');
        $this->setParametro('id_depto_lb','id_depto_lb','int4');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','int4');
        $this->setParametro('forma_pago','forma_pago','varchar');
        $this->setParametro('monto_no_pagado','monto_no_pagado','numeric');
        $this->setParametro('fecha_tentativa','fecha_tentativa','date');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('monto_retgar_mo','monto_retgar_mo','numeric');
        $this->setParametro('descuento_ley','descuento_ley','numeric');
        $this->setParametro('obs_descuentos_ley','obs_descuentos_ley','text');
        $this->setParametro('porc_descuento_ley','porc_descuento_ley','numeric');
        $this->setParametro('nro_cheque','nro_cheque','integer');
        $this->setParametro('nro_cuenta_bancaria','nro_cuenta_bancaria','varchar');
//        $this->setParametro('id_depto_lb','id_depto_lb','integer');
        $this->setParametro('id_cuenta_bancaria_mov','id_cuenta_bancaria_mov','integer');
        $this->setParametro('porc_monto_excento_var','porc_monto_excento_var','numeric');
        $this->setParametro('monto_excento','monto_excento','numeric');
        $this->setParametro('descuento_inter_serv','descuento_inter_serv','numeric');
        $this->setParametro('obs_descuento_inter_serv','obs_descuento_inter_serv','text');
        $this->setParametro('porc_monto_retgar','porc_monto_retgar','numeric');
        $this->setParametro('monto_ajuste_ag','monto_ajuste_ag','numeric');
        $this->setParametro('monto_ajuste_siguiente_pag','monto_ajuste_siguiente_pag','numeric');
        $this->setParametro('monto_anticipo','monto_anticipo','numeric');
        $this->setParametro('fecha_costo_ini','fecha_costo_ini','date');
        $this->setParametro('fecha_costo_fin','fecha_costo_fin','date');
        $this->setParametro('fecha_conclusion_pago','fecha_conclusion_pago','date');
        $this->setParametro('es_ultima_cuota','es_ultima_cuota','boolean');


        $this->setParametro('monto_establecido','monto_establecido','numeric');
        $this->setParametro('id_proveedor_cta_bancaria','id_proveedor_cta_bancaria','int4');

        //franklin.espinoza
        $this->setParametro('documentos','documentos','text');
        $this->setParametro('id_doc_compra_venta','id_doc_compra_venta','integer');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_solicitud_plan_pago_ime';
        $this->transaccion='TES_SOPLAPA_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');
        $this->setParametro('tipo_pago','tipo_pago','varchar');
        $this->setParametro('monto_ejecutar_total_mo','monto_ejecutar_total_mo','numeric');
        $this->setParametro('obs_descuentos_anticipo','obs_descuentos_anticipo','text');
        $this->setParametro('id_plan_pago_fk','id_plan_pago_fk','int4');
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('id_plantilla','id_plantilla','int4');
        $this->setParametro('descuento_anticipo','descuento_anticipo','numeric');
        $this->setParametro('otros_descuentos','otros_descuentos','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('obs_monto_no_pagado','obs_monto_no_pagado','text');
        $this->setParametro('obs_otros_descuentos','obs_otros_descuentos','text');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('nombre_pago','nombre_pago','varchar');
        $this->setParametro('monto_no_pagado_mb','monto_no_pagado_mb','numeric');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','int4');
        $this->setParametro('forma_pago','forma_pago','varchar');
        $this->setParametro('monto_no_pagado','monto_no_pagado','numeric');
        $this->setParametro('fecha_tentativa','fecha_tentativa','date');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('monto_retgar_mo','monto_retgar_mo','numeric');
        $this->setParametro('descuento_ley','descuento_ley','numeric');
        $this->setParametro('obs_descuentos_ley','obs_descuentos_ley','text');
        $this->setParametro('porc_descuento_ley','porc_descuento_ley','numeric');
        $this->setParametro('nro_cheque','nro_cheque','integer');
        $this->setParametro('nro_cuenta_bancaria','nro_cuenta_bancaria','varchar');
        $this->setParametro('id_cuenta_bancaria_mov','id_cuenta_bancaria_mov','integer');
        $this->setParametro('porc_monto_excento_var','porc_monto_excento_var','numeric');
        $this->setParametro('monto_excento','monto_excento','numeric');
        $this->setParametro('descuento_inter_serv','descuento_inter_serv','numeric');
        $this->setParametro('obs_descuento_inter_serv','obs_descuento_inter_serv','text');
        $this->setParametro('porc_monto_retgar','porc_monto_retgar','numeric');
        $this->setParametro('monto_ajuste_ag','monto_ajuste_ag','numeric');
        $this->setParametro('monto_ajuste_siguiente_pag','monto_ajuste_siguiente_pag','numeric');
        $this->setParametro('monto_anticipo','monto_anticipo','numeric');
        $this->setParametro('fecha_costo_ini','fecha_costo_ini','date');
        $this->setParametro('fecha_costo_fin','fecha_costo_fin','date');
        $this->setParametro('fecha_conclusion_pago','fecha_conclusion_pago','date');
        $this->setParametro('id_depto_lb','id_depto_lb','int4');
        $this->setParametro('es_ultima_cuota','es_ultima_cuota','boolean');


        $this->setParametro('monto_establecido','monto_establecido','numeric');
        $this->setParametro('id_proveedor_cta_bancaria','id_proveedor_cta_bancaria','int4');

        //franklin.espinoza
        $this->setParametro('documentos','documentos','varchar');
        $this->setParametro('id_doc_compra_venta','id_doc_compra_venta','int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_solicitud_plan_pago_ime';
        $this->transaccion='TES_SOPLAPA_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function siguienteEstadoPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_solicitud_plan_pago_ime';
        $this->transaccion='TES_SOSIGEPP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('id_depto_lb','id_depto_lb','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



}
?>
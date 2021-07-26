<?php
/**
 *@package pXP
 *@file gen-MODPlanPago.php
 *@author  (admin)
 *@date 10-04-2013 15:43:23
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODPlanPago extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarPlanPago(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PLAPA_SEL';
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
        $this->captura('id_multa','integer');
        $this->captura('desc_multa','varchar');

        $this->captura('id_obligacion_pago_extendida','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';

        if (in_array($this->objParam->getParametro('tipo'), array("devengado_pagado","devengado","devengado_pagado_1c","especial"))){
            /////////////////////////////
            // Cuotas de primer nivel que tienen prorateo
            //////////////////////////////
            $this->transaccion='TES_PLAPA_INS';    //para cuotas de devengado

        }

        elseif (in_array($this->objParam->getParametro('tipo'), array("pagado","ant_aplicado"))){

            ///////////////////////////////////////////////
            // Cuotas de segundo  nivel que tienen prorateo  (dependen de un pan de pago)
            /////////////////////////////////////////////
            $this->transaccion='TES_PLAPAPA_INS';  //para cuotas de pago

        }

        elseif (in_array($this->objParam->getParametro('tipo'), array("ant_parcial","anticipo","dev_garantia","dev_garantia_con","dev_garantia_con_ant"))){
            ///////////////////////////////////////////////
            // Cuotas de primer nivel que no tienen prorrateo
            /////////////////////////////////////////////
            $this->transaccion='TES_PPANTPAR_INS';  //anticipo parcial

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
        $this->setParametro('id_depto_lb','id_depto_lb','integer');
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
        $this->setParametro('id_multa','id_multa','int4');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_PLAPA_MOD';
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
        $this->setParametro('id_multa','id_multa','int4');




        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function siguienteEstadoPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_SIGEPP_IME';
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

    function anteriorEstadoPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_ANTEPP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('operacion','operacion','varchar');

        $this->setParametro('id_funcionario','id_funcionario','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_estado_wf','id_estado_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('estado_destino','estado_destino','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function eliminarPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_PLAPA_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function solicitarDevPag(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_SOLDEVPAG_IME';
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

    function sincronizarPresupuesto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_SINPRE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function generarConformidad(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_GENCONF_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');
        $this->setParametro('conformidad','conformidad','text');
        $this->setParametro('fecha_conformidad','fecha_conformidad','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function marcarRevisadoPlanPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_REVPP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function alertarPagosPendientes(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_pagos_pendientes_ime';
        $this->transaccion='TES_PPPREV_INS';
        $this->tipo_procedimiento='IME';
        //definicion de variables
        $this->tipo_conexion='seguridad';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /*
     *
     * Author: RAC - KPLIAN
     * DATE:   2/3/2015
     * DESCR:  inserta alarmas para pagos que necesitan el form500
     * */

    function alertarPagosForm500(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_pagos_pendientes_ime';
        $this->transaccion='TES_FORM500_INS';
        $this->tipo_procedimiento='IME';
        //definicion de variables
        $this->tipo_conexion='seguridad';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }






    function listarPlanesPagoPorObligacion(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PLAPAOB_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_plan_pago','int4');
        $this->captura('nro_cuota','numeric');
        $this->captura('monto_ejecutar_total_mb','numeric');
        $this->captura('nro_sol_pago','varchar');
        $this->captura('tipo_cambio','numeric');
        $this->captura('fecha_pag','date');
        $this->captura('fecha_dev','date');
        $this->captura('estado','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('monto_ejecutar_total_mo','numeric');
        $this->captura('descuento_anticipo_mb','numeric');
        $this->captura('obs_descuentos_anticipo','text');
        $this->captura('id_plan_pago_fk','int4');
        $this->captura('descuento_anticipo','numeric');
        $this->captura('otros_descuentos','numeric');
        $this->captura('tipo','varchar');
        $this->captura('obs_monto_no_pagado','text');
        $this->captura('obs_otros_descuentos','text');
        $this->captura('monto','numeric');
        $this->captura('nombre_pago','varchar');
        $this->captura('monto_no_pagado_mb','numeric');
        $this->captura('monto_mb','numeric');
        $this->captura('otros_descuentos_mb','numeric');
        $this->captura('forma_pago','varchar');
        $this->captura('monto_no_pagado','numeric');
        $this->captura('fecha_tentativa','date');
        $this->captura('desc_plantilla','varchar');
        $this->captura('liquido_pagable','numeric');
        $this->captura('total_prorrateado','numeric');
        $this->captura('total_pagado','numeric');
        $this->captura('desc_cuenta_bancaria','text');
        $this->captura('sinc_presupuesto','varchar');
        $this->captura('monto_retgar_mb','numeric');
        $this->captura('monto_retgar_mo','numeric');

        $this->captura('nombre','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reportePlanPago(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PLAPAREP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        $this->captura('estado','varchar');
        $this->captura('numero_oc','varchar');
        $this->captura('proveedor','varchar');

        $this->captura('nro_cuota','numeric');
        $this->captura('fecha_devengado','date');
        $this->captura('fecha_pag','date');

        $this->captura('forma_pago','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('moneda','varchar');
        $this->captura('codigo_moneda','varchar');


        $this->captura('tipo_cambio','numeric');

        $this->captura('importe','numeric');
        $this->captura('monto_no_pagado','numeric');
        $this->captura('otros_descuentos','numeric');

        $this->captura('obs_otros_descuentos','text');
        $this->captura('descuento_ley','numeric');
        $this->captura('obs_descuento_ley','text');

        $this->captura('monto_ejecutado_total','numeric');
        $this->captura('liquido_pagable','numeric');
        $this->captura('total_pagado','numeric');
        $this->captura('fecha_reg','timestamp');
        $this->captura('total_pago','numeric');
        $this->captura('tipo','varchar');

        $this->captura('monto_excento','numeric');




        //Ejecuta la respuesta
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }

    function verificarDisponibilidad(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_VERDIS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Definicion de la lista del resultado del query
        $this->captura('id_partida','int4');
        $this->captura('id_centro_costo','int4');
        $this->captura('id_moneda','int4');
        $this->captura('importe','numeric');
        $this->captura('disponibilidad','varchar');
        $this->captura('desc_partida','text');
        $this->captura('desc_cc','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarActaMaestro(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_ACTCONFPP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);


        //Definicion de la lista del resultado del query
        $this->captura('nombre_solicitante','text');
        $this->captura('proveedor','varchar');
        $this->captura('fecha_conformidad','text');
        $this->captura('conformidad','text');
        $this->captura('numero_oc','varchar');
        $this->captura('numero_op','varchar');
        $this->captura('numero_cuota','numeric');
        $this->captura('numero_tramite','varchar');
        $this->captura('detalle','text');
        $this->captura('tipo','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('obs_monto_no_pagado','text');
        $this->captura('total_nro_cuota','integer');
        $this->captura('obs','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();


        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPagosPendientes(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_pagos_pendientes_sel';
        $this->transaccion='TES_PAGOPEN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->captura('id_plan_pago','integer');
        $this->captura('fecha_tentativa','date');
        $this->captura('id_estado_wf','integer');
        $this->captura('id_proceso_wf','integer');
        $this->captura('monto','numeric');
        $this->captura('liquido_pagable','numeric');
        $this->captura('monto_retgar_mo','numeric');
        $this->captura('monto_ejecutar_total_mo','numeric');
        $this->captura('estado','varchar');
        $this->captura('list','text');
        $this->captura('list_unique','text');
        $this->captura('desc_funcionario_solicitante','text');
        $this->captura('email_empresa_fun_sol','varchar');
        $this->captura('email_empresa_usu_reg','varchar');
        $this->captura('desc_funcionario_usu_reg','text');
        $this->captura('tipo','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('tipo_obligacion','varchar');
        $this->captura('tipo_solicitud','varchar');
        $this->captura('tipo_concepto_solicitud','varchar');
        $this->captura('pago_variable','varchar');
        $this->captura('tipo_anticipo','varchar');
        $this->captura('num_tramite','varchar');
        $this->captura('nro_cuota','numeric');
        $this->captura('nombre_pago','varchar');
        $this->captura('obs','varchar');
        $this->captura('codigo_moneda','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



    function listarPagosXConcepto(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PAXCIG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_concepto','id_concepto','int4');
        $this->capturaCount('total_monto_ot','numeric');


        $this->captura('id_plan_pago','int4');
        $this->captura('orden_trabajo','text');
        $this->captura('num_tramite','varchar');
        $this->captura('nro_cuota','numeric');
        $this->captura('desc_proveedor','varchar');
        $this->captura('estado','varchar');
        $this->captura('fecha','date');
        $this->captura('moneda','varchar');
        $this->captura('monto','numeric');
        $this->captura('monto_ejecutar_mo','numeric');
        $this->captura('id_centro_costo','int4');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        /*Aumentando para recuperar el codigo del centro de costo 28/11/2019 (Ismael Valdivia)*/
        $this->captura('codigo_cc','text');
        $this->captura('descripcion','text');
        $this->captura('justificacion','varchar');
        /**************************************************************************************/


        //Ejecuta la instruccion
        $this->armarConsulta();
        echo $this->thisconsulta;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPagosXProveedor(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PAGOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(true);

        $this->captura('id_plan_pago', 'INTEGER');
        $this->captura('id_gestion', 'INTEGER');
        $this->captura('gestion', 'INTEGER');
        $this->captura('id_obligacion_pago', 'INTEGER');
        $this->captura('num_tramite', 'VARCHAR');
        $this->captura('orden_compra ','VARCHAR');
        $this->captura('tipo_obligacion', 'VARCHAR');
        $this->captura('pago_variable', 'VARCHAR');
        $this->captura('desc_proveedor', 'VARCHAR');
        $this->captura('estado', 'VARCHAR');
        $this->captura('usuario_reg', 'VARCHAR');
        $this->captura('fecha', 'DATE');
        $this->captura('fecha_reg', 'TIMESTAMP');
        $this->captura('ob_obligacion_pago', 'VARCHAR');
        $this->captura('fecha_tentativa_de_pago', 'DATE');
        $this->captura('nro_cuota', 'NUMERIC');
        $this->captura('tipo_plan_pago', 'VARCHAR');
        $this->captura('estado_plan_pago', 'VARCHAR');
        $this->captura('obs_descuento_inter_serv', 'TEXT');
        $this->captura('obs_descuentos_anticipo', 'TEXT');
        $this->captura('obs_descuentos_ley', 'TEXT');
        $this->captura('obs_monto_no_pagado', 'TEXT');
        $this->captura('obs_otros_descuentos', 'TEXT');
        $this->captura('codigo', 'VARCHAR');
        $this->captura('monto_cuota', 'NUMERIC');
        $this->captura('monto_anticipo', 'NUMERIC');
        $this->captura('monto_excento', 'NUMERIC');
        $this->captura('monto_retgar_mo', 'NUMERIC');
        $this->captura('monto_ajuste_ag', 'NUMERIC');
        $this->captura('monto_ajuste_siguiente_pago', 'NUMERIC');
        $this->captura('liquido_pagable', 'NUMERIC');
        $this->captura('monto_presupuestado', 'NUMERIC');
        $this->captura('desc_contrato', 'text');
        $this->captura('desc_funcionario1', 'text');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function resumenPagosXProveedor(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PAGOS_CONT';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);

        $this->captura('total', 'BIGINT');
        $this->captura('monto_cuota', 'NUMERIC');
        $this->captura('monto_anticipo', 'NUMERIC');
        $this->captura('monto_excento', 'NUMERIC');
        $this->captura('monto_retgar_mo', 'NUMERIC');
        $this->captura('monto_ajuste_ag', 'NUMERIC');
        $this->captura('monto_ajuste_siguiente_pago', 'NUMERIC');
        $this->captura('liquido_pagable', 'NUMERIC');
        $this->captura('monto_presupuestado', 'NUMERIC');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listadosPagosRelacionados(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_rep_sel';
        $this->transaccion='TES_REPPAGOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_proveedors','id_proveedors','VARCHAR');
        $this->setParametro('id_orden_trabajos','id_orden_trabajos','VARCHAR');
        $this->setParametro('id_concepto_ingas','id_concepto_ingas','VARCHAR');
        $this->setParametro('desde','desde','date');
        $this->setParametro('hasta','hasta','date');
        $this->setParametro('tipo_pago','tipo_pago','VARCHAR');
        $this->setParametro('estado','estado','VARCHAR');
        $this->setParametro('fuera_estado','fuera_estado','VARCHAR');

        $this->capturaCount('monto_mb','numeric');


        $this->captura('id_plan_pago', 'INTEGER');
        $this->captura('desc_proveedor', 'VARCHAR');
        $this->captura('num_tramite', 'VARCHAR');
        $this->captura('estado', 'VARCHAR');
        $this->captura('fecha_tentativa', 'DATE');
        $this->captura('nro_cuota', 'NUMERIC');
        $this->captura('monto ','NUMERIC');
        $this->captura('monto_mb ','NUMERIC');
        $this->captura('codigo', 'VARCHAR');
        $this->captura('conceptos', 'TEXT');
        $this->captura('ordenes', 'TEXT');
        $this->captura('id_proceso_wf', 'INTEGER');
        $this->captura('id_estado_wf', 'INTEGER');
        $this->captura('id_proveedor', 'INTEGER');
        $this->captura('obs', 'VARCHAR');
        $this->captura('tipo', 'VARCHAR');
        $this->captura('cbte_relacionado', 'VARCHAR');






        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function getConfigPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_GETCFGPAGO_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        // $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPagos(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PAGOSB_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(true);

        $this->captura('id_plan_pago', 'INTEGER');
        $this->captura('id_gestion', 'INTEGER');
        $this->captura('gestion', 'INTEGER');
        $this->captura('id_obligacion_pago', 'INTEGER');
        $this->captura('num_tramite', 'VARCHAR');

        $this->captura('tipo_obligacion', 'VARCHAR');
        $this->captura('pago_variable', 'VARCHAR');
        $this->captura('desc_proveedor', 'VARCHAR');
        $this->captura('estado', 'VARCHAR');
        $this->captura('usuario_reg', 'VARCHAR');
        $this->captura('fecha', 'DATE');
        $this->captura('fecha_reg', 'TIMESTAMP');
        $this->captura('ob_obligacion_pago', 'VARCHAR');
        $this->captura('fecha_tentativa_de_pago', 'DATE');
        $this->captura('nro_cuota', 'NUMERIC');
        $this->captura('tipo_plan_pago', 'VARCHAR');
        $this->captura('estado_plan_pago', 'VARCHAR');
        $this->captura('obs_descuento_inter_serv', 'TEXT');
        $this->captura('obs_descuentos_anticipo', 'TEXT');
        $this->captura('obs_descuentos_ley', 'TEXT');
        $this->captura('obs_monto_no_pagado', 'TEXT');
        $this->captura('obs_otros_descuentos', 'TEXT');
        $this->captura('codigo', 'VARCHAR');

        $this->captura('monto_cuota', 'NUMERIC');
        $this->captura('monto_anticipo', 'NUMERIC');
        $this->captura('monto_excento', 'NUMERIC');
        $this->captura('monto_retgar_mo', 'NUMERIC');
        $this->captura('monto_ajuste_ag', 'NUMERIC');
        $this->captura('monto_ajuste_siguiente_pago', 'NUMERIC');


        $this->captura('liquido_pagable', 'NUMERIC');
        $this->captura('id_contrato', 'integer');


        $this->captura('desc_contrato', 'text');
        $this->captura('desc_funcionario1', 'text');
        $this->captura('bancarizacion', 'varchar');

        $this->captura('id_proceso_wf', 'integer');


        $this->captura('id_plantilla', 'integer');
        $this->captura('desc_plantilla', 'VARCHAR');
        $this->captura('tipo_informe', 'VARCHAR');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    //Reporte de proceso con retencion

    function listarProcesoConRetencion(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PROCRE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_proveedor','id_proveedor','integer');
        $this->setParametro('id_contrato','id_contrato','integer');
        $this->setCount(false);

        $this->captura('id_proveedor', 'integer');
        $this->captura('id_moneda', 'integer');
        $this->captura('id_funcionario', 'integer');
        $this->captura('num_tramite', 'varchar');
        $this->captura('proveedor', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('monto', 'NUMERIC');
        $this->captura('fecha_dev', 'DATE');
        $this->captura('nro_cuota', 'NUMERIC');
        $this->captura('monto_retgar_mo', 'NUMERIC');
        $this->captura('moneda', 'varchar');
        $this->captura('liquido_pagable', 'NUMERIC');
        $this->captura('c31', 'varchar');
        $this->captura('numero', 'varchar');

        $this->captura('porc_monto_retgar', 'NUMERIC');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        // var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;


    }

    function setUltimaCuota(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_ULTIMA_CUOTA_SET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('id_plan_pago','id_plan_pago','int4');
        $this->setParametro('es_ultima_cuota','es_ultima_cuota','boolean');
        $this->setParametro('accion','accion','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteActaConformidadTotal()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.f_plan_pago_sel';
        $this->transaccion = 'TES_ACTCONTOTAL_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');

        //Definicion de la lista del resultado del query

        $this->captura('fecha_inicio', 'varchar');
        $this->captura('fecha_fin', 'varchar');
        $this->captura('fecha_conformidad_final', 'varchar');
        $this->captura('conformidad_final', 'text');
        $this->captura('observaciones', 'varchar');
        $this->captura('numero_tramite', 'varchar');
        $this->captura('proveedor', 'varchar');
        $this->captura('nombre_solicitante', 'text');
        $this->captura('nro_po', 'varchar');
        $this->captura('fecha_po', 'varchar');
        $this->captura('nro_cuota_vigente', 'numeric');
        $this->captura('desc_ingas', 'varchar');
        $this->captura('cantidad_adju', 'numeric');
        $this->captura('descripcion_sol', 'varchar');
        $this->captura('firma', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //var_dump($this->respuesta); exit;

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPlanPagoConforFin()
    {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.f_plan_pago_sel';
        $this->transaccion = 'TES_CONFPAGFIN_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('id_funcionario_usu', 'id_funcionario_usu', 'int4');
        $this->setParametro('tipo_interfaz', 'tipo_interfaz', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_obligacion_pago', 'int4');
        $this->captura('id_proveedor', 'int4');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('tipo_obligacion', 'varchar');
        $this->captura('id_moneda', 'int4');
        $this->captura('moneda', 'varchar');
        $this->captura('obs', 'varchar');
        $this->captura('porc_retgar', 'numeric');
        $this->captura('id_subsistema', 'int4');
        $this->captura('nombre_subsistema', 'varchar');
        $this->captura('id_funcionario', 'int4');
        $this->captura('desc_funcionario1', 'text');
        $this->captura('estado_reg', 'varchar');
        $this->captura('porc_anticipo', 'numeric');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('id_depto', 'int4');
        $this->captura('nombre_depto', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('fecha_reg', 'timestamp');

        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('numero', 'varchar');
        $this->captura('tipo_cambio_conv', 'numeric');
        $this->captura('id_gestion', 'integer');
        $this->captura('comprometido', 'varchar');
        $this->captura('nro_cuota_vigente', 'numeric');
        $this->captura('tipo_moneda', 'varchar');
        $this->captura('total_pago', 'numeric');
        $this->captura('pago_variable', 'varchar');
        $this->captura('id_depto_conta', 'integer');
        $this->captura('total_nro_cuota', 'integer');
        $this->captura('fecha_pp_ini', 'date');
        $this->captura('rotacion', 'integer');
        $this->captura('id_plantilla', 'integer');
        $this->captura('desc_plantilla', 'varchar');
        $this->captura('desc_funcionario', 'text');
        $this->captura('ultima_cuota_pp', 'numeric');
        $this->captura('ultimo_estado_pp', 'varchar');
        $this->captura('tipo_anticipo', 'varchar');
        $this->captura('ajuste_anticipo', 'numeric');
        $this->captura('ajuste_aplicado', 'numeric');
        $this->captura('monto_estimado_sg', 'numeric');
        $this->captura('id_obligacion_pago_extendida', 'integer');
        $this->captura('desc_contrato', 'text');
        $this->captura('id_contrato', 'int4');
        $this->captura('obs_presupuestos', 'varchar');
        $this->captura('uo_ex', 'varchar');
        $this->captura('id_conformidad', 'int4');
        $this->captura('conformidad_final', 'text');
        $this->captura('fecha_conformidad_final', 'date');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('observaciones', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();//echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function clonarPP(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.f_plan_pago_ime';
        $this->transaccion='TES_REPPP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_pago','id_plan_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

	function listarProcesoConRetencionAProrrateo(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.f_plan_pago_sel';
        $this->transaccion='TES_PROCREPRO_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_proveedor','id_proveedor','integer');
        $this->setParametro('id_contrato','id_contrato','integer');


        $this->setCount(false);

        $this->captura('proveedor', 'varchar');
        $this->captura('nro_contrato', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('nro_cuota', 'NUMERIC');
        $this->captura('tipo', 'varchar');
        $this->captura('fecha_dev', 'DATE');
        $this->captura('moneda', 'varchar');
        $this->captura('codigo_cc', 'varchar');
        $this->captura('partida', 'varchar');
        $this->captura('codigo_categoria', 'varchar');
        $this->captura('c31', 'varchar');
        $this->captura('monto', 'NUMERIC');
        $this->captura('monto_retgar_mo', 'NUMERIC');
        $this->captura('liquido_pagable', 'NUMERIC');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        // var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;


    }

    function listarProcesoResumenXContrato() {
  			//Definicion de variables para ejecucion del procedimientp
  			$this->procedimiento='tes.f_plan_pago_sel';
  			$this->transaccion='TES_REXCONT_SEL';
  			$this->tipo_procedimiento='SEL';//tipo de transaccion

  			//Definicion de la lista del resultado del query
        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_proveedor','id_proveedor','integer');
        $this->setParametro('id_contrato','id_contrato','integer');
  			$this->setCount(false);

  			$this->captura('jsonData','text');

  			//Ejecuta la instruccion
  			$this->armarConsulta();
  			 // echo $this->consulta;exit;
  			$this->ejecutarConsulta();

  			//Devuelve la respuesta
  			return $this->respuesta;

  	}

}
?>

<?php

class MODSolicitudObligacionPago extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }


    function insertarSolicitudObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_ime';
        $this->transaccion = 'TES_SOOBPG_INS';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proveedor', 'id_proveedor', 'int4');
        $this->setParametro('tipo_obligacion', 'tipo_obligacion', 'varchar');
        $this->setParametro('id_moneda', 'id_moneda', 'int4');
        $this->setParametro('obs', 'obs', 'varchar');
        $this->setParametro('porc_retgar', 'porc_retgar', 'numeric');
        $this->setParametro('id_funcionario', 'id_funcionario', 'int4');
        $this->setParametro('porc_anticipo', 'porc_anticipo', 'numeric');
        $this->setParametro('id_depto', 'id_depto', 'int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('tipo_cambio_conv', 'tipo_cambio_conv', 'numeric');
        $this->setParametro('pago_variable', 'pago_variable', 'varchar');
        $this->setParametro('total_nro_cuota', 'total_nro_cuota', 'int4');
        $this->setParametro('fecha_pp_ini', 'fecha_pp_ini', 'date');
        $this->setParametro('rotacion', 'rotacion', 'int4');
        $this->setParametro('id_plantilla', 'id_plantilla', 'int4');
        $this->setParametro('tipo_anticipo', 'tipo_anticipo', 'varchar');
        $this->setParametro('id_contrato', 'id_contrato', 'int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarSolicitudObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_ime';
        $this->transaccion = 'TES_SOOBPG_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_proveedor', 'id_proveedor', 'int4');
        $this->setParametro('tipo_obligacion', 'tipo_obligacion', 'varchar');
        $this->setParametro('id_moneda', 'id_moneda', 'int4');
        $this->setParametro('obs', 'obs', 'varchar');
        $this->setParametro('porc_retgar', 'porc_retgar', 'numeric');
        $this->setParametro('id_subsistema', 'id_subsistema', 'int4');
        $this->setParametro('id_funcionario', 'id_funcionario', 'int4');
        $this->setParametro('porc_anticipo', 'porc_anticipo', 'numeric');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('id_depto', 'id_depto', 'int4');
        $this->setParametro('tipo_cambio_conv', 'tipo_cambio_conv', 'numeric');
        $this->setParametro('pago_variable', 'pago_variable', 'varchar');

        $this->setParametro('total_nro_cuota', 'total_nro_cuota', 'int4');
        $this->setParametro('fecha_pp_ini', 'fecha_pp_ini', 'date');
        $this->setParametro('rotacion', 'rotacion', 'int4');
        $this->setParametro('id_plantilla', 'id_plantilla', 'int4');

        $this->setParametro('tipo_anticipo', 'tipo_anticipo', 'varchar');
        $this->setParametro('id_contrato', 'id_contrato', 'int4');

        //$this->setParametro('id_funcionario_responsable','id_funcionario_responsable','int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarSolicitudObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_ime';
        $this->transaccion = 'TES_SOOBPG_ELI';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarSolicitudObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_sel';
        $this->transaccion = 'TES_SOOBPG_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion


        $this->setParametro('id_funcionario_usu', 'id_funcionario_usu', 'int4');
        $this->setParametro('tipo_interfaz', 'tipo_interfaz', 'varchar');
        $this->setParametro('historico', 'historico', 'varchar');

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
        $this->captura('ultima_cuota_pp', 'numeric');
        $this->captura('ultimo_estado_pp', 'varchar');
        $this->captura('tipo_anticipo', 'varchar');
        $this->captura('ajuste_anticipo', 'numeric');
        $this->captura('ajuste_aplicado', 'numeric');
        $this->captura('monto_estimado_sg', 'numeric');
        $this->captura('id_obligacion_pago_extendida', 'integer');
        $this->captura('desc_contrato', 'text');
        $this->captura('id_contrato', 'integer');
        $this->captura('obs_presupuestos', 'varchar');
        $this->captura('codigo_poa', 'varchar');
        $this->captura('obs_poa', 'varchar');
        $this->captura('uo_ex', 'varchar');
        //Funcionario responsable de el plan de pagos
        $this->captura('id_funcionario_responsable', 'integer');
        $this->captura('desc_fun_responsable', 'text');

        $this->captura('id_conformidad', 'int4');
        $this->captura('conformidad_final', 'text');
        $this->captura('fecha_conformidad_final', 'date');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('observaciones', 'varchar');
        $this->captura('fecha_certificacion_pres', 'date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarSolicitudObligacionPagoSol()
    {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_sel';
        $this->transaccion = 'TES_SOOBPGSOL_SEL';
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
        $this->captura('id_contrato', 'integer');
        $this->captura('obs_presupuestos', 'varchar');
        $this->captura('uo_ex', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function siguienteEstadoSolObligacion()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_solicitud_obligacion_pago_ime';
        $this->transaccion = 'TES_SOSIGESTOB_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_proceso_wf_act', 'id_proceso_wf_act', 'int4');
        $this->setParametro('id_estado_wf_act', 'id_estado_wf_act', 'int4');
        $this->setParametro('id_funcionario_usu', 'id_funcionario_usu', 'int4');
        $this->setParametro('id_tipo_estado', 'id_tipo_estado', 'int4');
        $this->setParametro('id_funcionario_wf', 'id_funcionario_wf', 'int4');
        $this->setParametro('id_depto_wf', 'id_depto_wf', 'int4');
        $this->setParametro('id_depto_lb', 'id_depto_lb', 'int4');
        $this->setParametro('obs', 'obs', 'text');
        $this->setParametro('json_procesos', 'json_procesos', 'text');
        $this->setParametro('instruc_rpc', 'instruc_rpc', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
}

?>
<?php
/**
 * @package pXP
 * @file MODObligacionPago.php
 * @author  Gonzalo Sarmiento Sejas
 * @date 02-04-2013 16:01:32
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODObligacionPago extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_OBPG_SEL';
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
        $this->captura('presupuesto_aprobado', 'varchar');
        $this->captura('nro_preventivo', 'integer');

        $this->captura('partida', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    /*
      Listado de olbigaciones de pago individual por solicitante

    */
    function listarObligacionPagoSol()
    {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_OBPGSOL_SEL';
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
        $this->captura('presupuesto_aprobado', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
//var_dump('llega maaaaayyyy', $this->respuesta);
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtnerUosEpsDetalleObligacion()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBEPUO_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function insertarObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBPG_INS';
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

    function modificarObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBPG_MOD';
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

    function eliminarObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBPG_ELI';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarObsPresupuestos()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBSPRE_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('obs', 'obs', 'varchar');
        $this->setParametro('fecha_cer_pres', 'fecha_cer_pres', 'date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarObsPoa()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBSPOA_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('obs_poa', 'obs_poa', 'varchar');
        $this->setParametro('codigo_poa', 'codigo_poa', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function extenderOp()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_EXTOP_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_administrador', 'id_administrador', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    /*function extenderPGA(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_EXTOP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('id_administrador','id_administrador','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }*/


    function insertarAjustes()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_OBLAJUS_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('ajuste_aplicado', 'ajuste_aplicado', 'numeric');
        $this->setParametro('ajuste_anticipo', 'ajuste_anticipo', 'numeric');
        $this->setParametro('monto_estimado_sg', 'monto_estimado_sg', 'numeric');
        $this->setParametro('tipo_ajuste', 'tipo_ajuste', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function finalizarRegistro()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_FINREG_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('forzar_fin', 'forzar_fin', 'varchar');
        $this->setParametro('obs', 'obs', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function siguienteEstadoObligacion()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_SIGESTOB_IME';
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


    function anteriorEstadoObligacion()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_ANTEOB_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('operacion', 'operacion', 'varchar');
        $this->setParametro('obs', 'obs', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //fea 12/3/2018
    function anteriorEstadoObligacionPago()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_ANTEOB_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
        $this->setParametro('id_estado_wf', 'id_estado_wf', 'int4');
        $this->setParametro('obs', 'obs', 'text');
        $this->setParametro('operacion', 'operacion', 'varchar');
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtenerFaltante()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_PAFPP_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('ope_filtro', 'ope_filtro', 'varchar');
        $this->setParametro('id_plan_pago', 'id_plan_pago', 'integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function recuperarDatosFiltro()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_GETFILOP_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function estadosPago()
    {
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_ESTOBPG_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

        $this->captura('funcionario', 'text');
        $this->captura('nombre', 'text');
        $this->captura('nombre_estado', 'varchar');
        $this->captura('fecha_reg', 'date');
        $this->captura('id_tipo_estado', 'int');
        $this->captura('id_estado_wf', 'int');
        $this->captura('id_estado_anterior', 'int');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obligacionPagoSeleccionado()
    {
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_OBPGSEL_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');

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
        $this->captura('porc_anticipo', 'numeric');
        $this->captura('id_depto', 'int4');
        $this->captura('nombre_depto', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('numero', 'varchar');
        $this->captura('tipo_cambio_conv', 'numeric');
        $this->captura('comprometido', 'varchar');
        $this->captura('nro_cuota_vigente', 'numeric');
        $this->captura('tipo_moneda', 'varchar');
        $this->captura('pago_variable', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    function listarObligacion()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_COMEJEPAG_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_moneda', 'id_moneda', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_obligacion_det', 'int4');
        $this->captura('id_partida', 'int4');
        $this->captura('nombre_partida', 'text');
        $this->captura('id_concepto_ingas', 'int4');
        $this->captura('nombre_ingas', 'text');
        $this->captura('id_obligacion_pago', 'int4');
        $this->captura('id_centro_costo', 'int4');
        $this->captura('codigo_cc', 'text');
        $this->captura('id_partida_ejecucion_com', 'int4');
        $this->captura('descripcion', 'text');
        $this->captura('comprometido', 'numeric');
        $this->captura('ejecutado', 'numeric');
        $this->captura('pagado', 'numeric');
        $this->captura('revertible', 'numeric');
        $this->captura('revertir', 'numeric');
        $this->captura('moneda', 'varchar');
        $this->captura('desc_orden', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtenerIdsExternos()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_IDSEXT_GET';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('sistema', 'sistema', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function revertirParcialmentePresupuesto()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_REVPARPRE_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_ob_dets', 'id_ob_dets', 'varchar');
        $this->setParametro('revertir', 'revertir', 'varchar');
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarObligacionCompleta()
    {

        //Abre conexion con PDO
        $cone = new conexion();
        $link = $cone->conectarpdo();
        $copiado = false;
        try {
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $link->beginTransaction();

            /////////////////////////
            //  inserta cabecera de la solicitud de compra
            ///////////////////////


            //Definicion de variables para ejecucion del procedimiento
            $this->procedimiento = 'tes.ft_obligacion_pago_ime';
            $this->transaccion = 'TES_OBPG_INS';
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

            $this->setParametro('fecha_costo_ini_pp', 'fecha_costo_ini_pp', 'date');
            $this->setParametro('fecha_costo_fin_pp', 'fecha_costo_fin_pp', 'date');
            $this->setParametro('fecha_conclusion_pago', 'fecha_conclusion_pago', 'date');

            //Ejecuta la instruccion
            $this->armarConsulta();
            $stmt = $link->prepare($this->consulta);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            //recupera parametros devuelto depues de insertar ... (id_obligacion_pago)
            $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
            if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
                throw new Exception("Error al ejecutar en la bd", 3);
            }

            $respuesta = $resp_procedimiento['datos'];

            $id_obligacion_pago = $respuesta['id_obligacion_pago'];

            //////////////////////////////////////////////
            //inserta detalle de la solicitud de compra
            /////////////////////////////////////////////


            //decodifica JSON  de detalles
            $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('json_new_records'));

            //var_dump($json_detalle)	;
            foreach ($json_detalle as $f) {

                $this->resetParametros();
                //Definicion de variables para ejecucion del procedimiento
                $this->procedimiento = 'tes.ft_obligacion_det_ime';
                $this->transaccion = 'TES_OBDET_INS';
                $this->tipo_procedimiento = 'IME';
                //modifica los valores de las variables que mandaremos

                $this->arreglo['id_obligacion_pago'] = $id_obligacion_pago;
                $this->arreglo['id_centro_costo'] = $f['id_centro_costo'];
                $this->arreglo['descripcion'] = $f['descripcion'];
                $this->arreglo['monto_pago_mo'] = $f['monto_pago_mo'];
                $this->arreglo['id_orden_trabajo'] = $f['id_orden_trabajo'];
                $this->arreglo['id_concepto_ingas'] = $f['id_concepto_ingas'];

                //Define los parametros para la funcion
                $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
                $this->setParametro('id_centro_costo', 'id_centro_costo', 'int4');
                $this->setParametro('descripcion', 'descripcion', 'text');
                $this->setParametro('monto_pago_mo', 'monto_pago_mo', 'numeric');
                $this->setParametro('id_orden_trabajo', 'id_orden_trabajo', 'int4');
                $this->setParametro('id_concepto_ingas', 'id_concepto_ingas', 'int4');

                //Ejecuta la instruccion
                $this->armarConsulta();
                $stmt = $link->prepare($this->consulta);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                //recupera parametros devuelto depues de insertar ... (id_solicitud)
                $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
                if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
                    throw new Exception("Error al insertar detalle  en la bd", 3);
                }


            }


            //si todo va bien confirmamos y regresamos el resultado
            $link->commit();
            $this->respuesta = new Mensaje();
            $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
            $this->respuesta->setDatos($respuesta);
        } catch (Exception $e) {
            $link->rollBack();
            $this->respuesta = new Mensaje();
            if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
            } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                $this->respuesta->setMensaje('ERROR', $this->nombre_archivo, $e->getMessage(), $e->getMessage(), 'modelo', '', '', '', '');
            } else {//es un error lanzado con throw exception
                throw new Exception($e->getMessage(), 2);
            }

        }

        return $this->respuesta;
    }

    function listarProcesosPendientes()
    {
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_PROCP_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');

        $this->captura('num_tramite', 'varchar');
        $this->captura('id_obligacion_pago', 'integer');
        $this->captura('estado', 'varchar');
        $this->captura('fecha', 'text');
        $this->captura('desc_funcionario1', 'text');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('total_pago', 'numeric');
        $this->captura('moneda', 'varchar');
        $this->captura('estado_pago', 'varchar');
        $this->captura('nro_cuota', 'numeric');
        $this->captura('liquido_pagable', 'numeric');
        $this->captura('obs', 'varchar');
        $this->captura('fecha_tentativa', 'text');
        $this->captura('nombre', 'text');
        $this->captura('nombre_depto', 'varchar');
        $this->captura('pago_variable', 'varchar');
        $this->captura('tipo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;

    }

    function recuperarPagoSinDocumento()
    {
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_PAGSINDOC_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');

        $this->captura('id_int_comprobante', 'integer');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('c31', 'varchar');
        $this->captura('beneficiario', 'varchar');
        $this->captura('glosa1', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }

    //Reporte Certificaciòn Presupuestaria (F.E.A) 01/08/2017
    function reporteCertificacionP()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_REPCERPRE_SEL';
        $this->tipo_procedimiento = 'SEL';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');


        $this->captura('id_cp', 'int4');
        $this->captura('centro_costo', 'varchar');
        $this->captura('codigo_programa', 'varchar');
        $this->captura('codigo_proyecto', 'varchar');
        $this->captura('codigo_actividad', 'varchar');
        $this->captura('codigo_fuente_fin', 'varchar');
        $this->captura('codigo_origen_fin', 'varchar');

        $this->captura('codigo_partida', 'varchar');
        $this->captura('nombre_partida', 'varchar');
        $this->captura('codigo_cg', 'varchar');
        $this->captura('nombre_cg', 'varchar');
        $this->captura('precio_total', 'numeric');
        $this->captura('codigo_moneda', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('nombre_entidad', 'varchar');
        $this->captura('direccion_admin', 'varchar');
        $this->captura('unidad_ejecutora', 'varchar');
        $this->captura('codigo_ue', 'varchar');
        $this->captura('firmas', 'varchar');
        $this->captura('justificacion', 'varchar');
        $this->captura('codigo_transf', 'varchar');
        $this->captura('unidad_solicitante', 'varchar');
        $this->captura('funcionario_solicitante', 'varchar');
        $this->captura('codigo_proceso', 'varchar');

        $this->captura('fecha_soli', 'date');
        $this->captura('gestion', 'integer');
        $this->captura('codigo_poa', 'varchar');
        $this->captura('codigo_descripcion', 'varchar');
        $this->captura('tipo_obligacion', 'varchar');
        $this->captura('fecha_certificacion_pres', 'date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //(FEA) Reporte Solicitud Centros Pagos Manuales
    function reporteSolicitudCentros()
    {

        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_REPSC_SEL';
        $this->tipo_procedimiento = 'SEL';

        $this->setCount(false);
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');

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
        $this->captura('porc_anticipo', 'numeric');
        $this->captura('id_depto', 'int4');
        $this->captura('nombre_depto', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('numero', 'varchar');
        $this->captura('tipo_cambio_conv', 'numeric');
        $this->captura('comprometido', 'varchar');
        $this->captura('nro_cuota_vigente', 'numeric');
        $this->captura('tipo_moneda', 'varchar');
        $this->captura('pago_variable', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    function reporteProcesoPago()
    {
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_REPROCPAG_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->setCount(false);

        $this->setParametro('monto', 'monto', 'numeric');
        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        //$this->setParametro('fecha_tentativa','fecha_tentativa','date');

        $this->captura('num_tramite', 'varchar');
        $this->captura('id_obligacion_pago', 'integer');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('obs', 'varchar');
        $this->captura('monto', 'numeric');
        $this->captura('moneda', 'varchar');
        $this->captura('fecha', 'text');
        $this->captura('estado', 'varchar');
        $this->captura('nombre_depto', 'varchar');
        $this->captura('fecha_tentativa', 'date');
        $this->captura('nro_cuota', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;

    }

    function listarOblPago()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_LISOBLPA_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion


        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_gestion', 'id_gestion', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_obligacion_pago', 'int4');
        $this->captura('num_tramite', 'varchar');
        $this->captura('id_gestion', 'int4');
        $this->captura('estado', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
//var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function TsLibroBancosExterior() {
		$this->procedimiento='tes.ft_obligacion_pago_sel';
		$this->transaccion='TES_LIBAN_EXT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_gestion','id_gestion','int4');
		$this->captura('id_obligacion_pago','int4');
        $this->captura('num_tramite','varchar');
        $this->captura('fecha','timestamp');
        $this->captura('nro_cuenta','numeric');
        $this->captura('nombre','varchar');
        $this->captura('codigo','varchar');
        $this->captura('nombre_estado','varchar');
        $this->captura('obs','text');
        $this->captura('desc_persona','text');
        $this->captura('fun_usuario_ai','text');
        $this->captura('monto','numeric');
        $this->captura('moneda','varchar');
        $this->captura('cod_moneda','varchar');
        $this->captura('estado_pp','varchar');
        $this->captura('nombre_proveedor','varchar');

        $this->captura('id_plan_pago','int4');

		//Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
    }
    function listarEvoluPresup()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_DETEVPRE_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_partida_ejecucion_com', 'id_partida_ejecucion_com', 'int4');
        $this->setParametro('tipo_interfaz', 'tipo_interfaz', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_partida_ejecucion', 'int4');
        $this->captura('id_partida_ejecucion_fk', 'int4');
        $this->captura('moneda', 'varchar');
        $this->captura('comprometido', 'numeric');
        $this->captura('comprometido_mb', 'numeric');
        $this->captura('ejecutado', 'numeric');
        $this->captura('ejecutado_mb', 'numeric');
        $this->captura('pagado', 'numeric');
        $this->captura('pagado_mb', 'numeric');
        $this->captura('nro_tramite', 'varchar');
        $this->captura('tipo_movimiento', 'varchar');
        $this->captura('nombre_partida', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('codigo_categoria', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('codigo_cc', 'varchar');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('estado_reg', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function clonarOP(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_REPOP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function aprobarPresupuestoSolicitud(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_VALPRE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('aprobar', 'aprobar', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteSolicitud()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_obligacion_pago_sel';
        $this->transaccion = 'TES_SOLREPOBP_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');

        //Definicion de la lista del resultado del query
        $this->captura('id_obligacion_pago', 'int4');
        $this->captura('estado', 'varchar');
        $this->captura('numero', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fecha_apro', 'date');
        $this->captura('desc_moneda', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('desc_gestion', 'integer');
        $this->captura('fecha_soli', 'date');
        $this->captura('fecha_soli_gant', 'date');
        $this->captura('desc_funcionario', 'text');
        $this->captura('desc_uo', 'text');
        $this->captura('desc_depto', 'varchar');
        $this->captura('desc_funcionario_apro', 'text');
        $this->captura('cargo_desc_funcionario', 'varchar');
        $this->captura('cargo_desc_funcionario_apro', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('codigo_poa', 'varchar');
        $this->captura('nombre_usuario_ai', 'varchar');
        $this->captura('codigo_uo', 'varchar');
        $this->captura('prioridad', 'integer');
        $this->captura('id_moneda', 'integer');
        $this->captura('obs', 'varchar');

        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    //{develop: franklin.espinoza date: 12/10/2020, description: Guarda Preventivo para procesos con Preventivo}
    function guardarDocumentoSigep(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_DOCSIGEP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','integer');
        $this->setParametro('nro_preventivo','nro_preventivo','integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //02-12-2020 (may) para insertar la relacion de un proceso
    function insertarRelacionProceso(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_RELACOB_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');

        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    //02-12-2020 (may) para modificar la relacion de un proceso
    function modificarRelacionProceso(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_RELACOB_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_relacion_proceso_pago','id_relacion_proceso_pago','int4');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

     //02-12-2020 (may) para eliminar la relacion de un proceso
    function eliminarRelacionProceso(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_obligacion_pago_ime';
        $this->transaccion='TES_RELACOB_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_relacion_proceso_pago','id_relacion_proceso_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //02-12-2020 (may) para listar la relacion de un proceso
    function listarRelacionProceso(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.ft_obligacion_pago_sel';
        $this->transaccion='TES_RELACOB_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_proceso_wf','id_proceso_wf','integer');
        //$this->setParametro('id_estado_wf','id_estado_wf','integer');
        //$this->setParametro('num_tramite','num_tramite','varchar');
        //$this->setParametro('todos','todos','integer');

        //Definicion de la lista del resultado del query
        $this->captura('id_relacion_proceso_pago','int4');
        $this->captura('observaciones','varchar');
        $this->captura('id_obligacion_pago','int4');
        $this->captura('num_tramite','varchar');

        $this->captura('estado_reg','varchar');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_reg','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //02-12-2020 (may) para listar compo de nros de tramite
    function listarObligacioPagoCombos(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='tes.ft_obligacion_pago_sel';
        $this->transaccion='TES_COMBOP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        //$this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->captura('id_obligacion_pago','int4');
        $this->captura('num_tramite','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta; exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //25-02-2021 (may) Pago de Gestion Anterior Exterior
    function extenderPGAE()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_EXTPGAE_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('id_administrador', 'id_administrador', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    /********************************* BEGIN {developer: franklin.espinoza, date: 11/10/2021} GENERAR PLAN PAGOS PVR *********************************/
    function generarPlanPagoPVR(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_GEN_PP_PVR_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_obligacion_pago', 'id_obligacion_pago', 'int4');
        $this->setParametro('operacion', 'operacion', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /********************************* END {developer: franklin.espinoza, date: 11/10/2021} GENERAR PLAN PAGOS PVR *********************************/

    /********************************* BEGIN {developer: franklin.espinoza, date: 11/10/2021} PAGOS PVR *********************************/
    function generarPagoPVR()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_GENERAR_PVR_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion

        $this->setParametro('nombre_origen', 'nombre_origen', 'varchar');
        $this->setParametro('json_beneficiarios', 'json_beneficiarios', 'jsonb');
        $this->setParametro('fecha_pago', 'fecha_pago', 'date');
        $this->setParametro('glosa_pago', 'glosa_pago', 'varchar');
        $this->setParametro('id_funcionario_responsable', 'id_funcionario_responsable', 'integer');

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');

        $this->captura('personal_sin_presupuesto','json');

        //Ejecuta la instruccion
        $this->armarConsulta();//echo $this->consulta; exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /********************************* END {developer: franklin.espinoza, date: 11/10/2021} PAGOS PVR *********************************/

    /********************************* BEGIN {developer: franklin.espinoza, date: 11/11/2021} PAGOS PVR ADMINISTRATIVO *********************************/
    function generarPagoAdministrativoPVR(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_obligacion_pago_ime';
        $this->transaccion = 'TES_GENERAR_PVR_ADM';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion

        $this->setParametro('nombre_origen', 'nombre_origen', 'varchar');
        $this->setParametro('json_beneficiarios', 'json_beneficiarios', 'jsonb');
        $this->setParametro('fecha_pago', 'fecha_pago', 'date');
        $this->setParametro('glosa_pago', 'glosa_pago', 'varchar');
        $this->setParametro('id_funcionario_responsable', 'id_funcionario_responsable', 'integer');

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('codigo_tipo_pago', 'codigo_tipo_pago', 'varchar');

        $this->captura('personal_sin_presupuesto','json');

        //Ejecuta la instruccion
        $this->armarConsulta();//echo $this->consulta; exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /********************************* END {developer: franklin.espinoza, date: 11/10/2021} PAGOS PVR ADMINISTRATIVO *********************************/

}

?>

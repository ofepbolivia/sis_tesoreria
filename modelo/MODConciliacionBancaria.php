<?php
/**
 * @package pXP
 * @file MODConciliacionBancaria.php
 * @author  BVP
 * @date 19-02-2019
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODConciliacionBancaria extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_CONCBAN_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_conciliacion_bancaria', 'int4');
        $this->captura('id_cuenta_bancaria', 'int4');
        $this->captura('id_funcionario_elabo', 'int4');
        $this->captura('id_funcionario_vb', 'int4');
        $this->captura('id_gestion', 'int4');
        $this->captura('id_periodo', 'int4');
        $this->captura('saldo_banco', 'numeric');
        $this->captura('observaciones', 'text');
        $this->captura('fecha', 'date');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('gestion', 'integer');
        $this->captura('literal', 'varchar');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('fun_elabo', 'text');
        $this->captura('fun_vb', 'text');
        $this->captura('estado', 'varchar');
        $this->captura('saldo_real_1', 'numeric');
        $this->captura('saldo_real_2', 'numeric');
        $this->captura('saldo_libros', 'numeric');
        $this->captura('diferencia', 'numeric');
        $this->captura('jefe_tesoreria', 'varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }


    function insertarConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_ime';
        $this->transaccion = 'TES_CONCBAN_INS';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_gestion', 'id_gestion', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');
        //$this->setParametro('id_funcionario_elabo','id_funcionario_elabo','int4');
        //$this->setParametro('id_funcionario_vb','id_funcionario_vb','int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('observaciones', 'observaciones', 'text');
        $this->setParametro('saldo_banco', 'saldo_banco', 'numeric');
        $this->setParametro('id_usuario_ai', 'id_usuario_ai', 'int4');
        $this->setParametro('nombre_usuario_ai', 'nombre_usuario_ai', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_ime';
        $this->transaccion = 'TES_CONCBAN_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        $this->setParametro('id_gestion', 'id_gestion', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');
        //$this->setParametro('id_funcionario_elabo','id_funcionario_elabo','int4');
        //$this->setParametro('id_funcionario_vb','id_funcionario_vb','int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('observaciones', 'observaciones', 'text');
        $this->setParametro('saldo_banco', 'saldo_banco', 'numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_ime';
        $this->transaccion = 'TES_CONCBAN_ELI';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_RECONCBAN_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');
        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('nombre_institucion', 'varchar');
        $this->captura('nro_cuenta', 'varchar');
        $this->captura('concepto', 'text');
        $this->captura('fecha', 'text');
        $this->captura('saldo', 'numeric');
        $this->captura('moneda', 'varchar');
        $this->captura('denominacion', 'varchar');
        $this->captura('nro_cheque', 'varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo ($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteExtracto()
    {
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_REPEXTRA_SEL';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');

        $this->captura('id_extracto', 'int4');
        $this->captura('id_tipo_movimiento', 'int4');
        $this->captura('nombre_movimiento', 'varchar');
        $this->captura('saldo', 'numeric');
        $this->captura('cod_operacion', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('credito', 'numeric');
        $this->captura('nro_cbte', 'varchar');
        $this->captura('estado_conciliacion', 'varchar');
        $this->captura('importe_conciliar', 'numeric');
        $this->captura('estado_reg', 'varchar');
        $this->captura('nro_documento', 'varchar');
        $this->captura('cuenta_transf', 'varchar');
        $this->captura('secuencial', 'int4');
        $this->captura('glosa', 'text');
        $this->captura('debito', 'numeric');
        $this->captura('id_usuario_ai', 'int4');
        $this->captura('usuario_ai', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->armarConsulta();
        // var_dump($this->getConsulta());
        // die;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function reporteConciliacionBancariaDet()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_RECONCBANDET_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');
        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('nombre_institucion', 'varchar');
        $this->captura('fecha', 'date');
        $this->captura('saldo', 'numeric');
        $this->captura('observaciones', 'text');
        $this->captura('fun_elab', 'text');
        $this->captura('fun_vb', 'text');
        $this->captura('moneda', 'varchar');
        $this->captura('fecha_reg', 'text');
        $this->captura('concepto', 'varchar');
        $this->captura('importe', 'numeric');
        $this->captura('nro_comprobante', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('sal_ext_ban', 'numeric');
        $this->captura('periodo', 'varchar');
        $this->captura('gestion', 'int4');
        $this->captura('nro_cuenta', 'varchar');
        $this->captura('denominacion', 'varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo ($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarDetalleConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_detalle_concili_bancaria_sel';
        $this->transaccion = 'TES_DETCONCBAN_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_detalle_conciliacion_bancaria', 'int4');
        $this->captura('id_conciliacion_bancaria', 'int4');
        $this->captura('fecha', 'date');
        $this->captura('concepto', 'varchar');
        $this->captura('nro_comprobante', 'varchar');
        $this->captura('importe', 'numeric');
        $this->captura('tipo', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('usr_reg', 'varchar');
        $this->captura('cuenta', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }

    function listarDetalleConciliacionBancariaNuevo()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_detalle_concili_bancaria_sel';
        $this->transaccion = 'TES_DETCONCNEW_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_detalle_conciliacion_bancaria', 'int4');
        $this->captura('id_conciliacion_bancaria', 'int4');
        $this->captura('fecha', 'date');
        $this->captura('concepto', 'varchar');
        $this->captura('nro_comprobante', 'varchar');
        $this->captura('importe', 'numeric');
        $this->captura('tipo', 'varchar');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');
        $this->captura('id_periodo', 'integer');
        $this->captura('id_gestion', 'integer');
        $this->captura('periodo', 'integer');
        $this->captura('gestion', 'integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        // var_dump($this->getConsulta());
        // die;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }

    function insertarDetalleConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_detalle_concil_bancaria_ime';
        $this->transaccion = 'TES_DETCONCBAN_INS';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('concepto', 'concepto', 'varchar');
        $this->setParametro('nro_comprobante', 'nro_comprobante', 'varchar');
        $this->setParametro('importe', 'importe', 'numeric');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('id_usuario_ai', 'id_usuario_ai', 'int4');
        $this->setParametro('nombre_usuario_ai', 'nombre_usuario_ai', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarDetalleConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_detalle_concil_bancaria_ime';
        $this->transaccion = 'TES_DETCONCBAN_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_detalle_conciliacion_bancaria', 'id_detalle_conciliacion_bancaria', 'int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('concepto', 'concepto', 'varchar');
        $this->setParametro('nro_comprobante', 'nro_comprobante', 'varchar');
        $this->setParametro('importe', 'importe', 'numeric');
        $this->setParametro('tipo', 'tipo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarDetalleConciliacionBancaria()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'tes.ft_detalle_concil_bancaria_ime';
        $this->transaccion = 'TES_DETCONCBAN_ELI';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_detalle_conciliacion_bancaria', 'id_detalle_conciliacion_bancaria', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function regDetalleRepo()
    {
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_ime';
        $this->transaccion = 'TES_DETCOBREP_INS';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_periodo', 'id_periodo', 'int4');
        $this->setParametro('id_usuario_ai', 'id_usuario_ai', 'int4');
        $this->setParametro('nombre_usuario_ai', 'nombre_usuario_ai', 'varchar');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function detalleConciliaRepo()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_DETREPCONBA_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->capturaCount('total_1', 'numeric');
        $this->capturaCount('total_2', 'numeric');
        $this->capturaCount('total_3', 'numeric');

        //Definicion de la lista del resultado del query
        $this->captura('id_conciliacion_bancaria_rep', 'int4');
        $this->captura('id_conciliacion_bancaria', 'int4');
        $this->captura('nro_cheque', 'varchar');
        $this->captura('periodo_1', 'numeric');
        $this->captura('periodo_2', 'numeric');
        $this->captura('periodo_3', 'numeric');
        $this->captura('total_haber', 'numeric');
        $this->captura('estado', 'varchar');
        $this->captura('detalle', 'text');
        $this->captura('observacion', 'text');
        $this->captura('fecha', 'date');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;		
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function detalleConciliaRepoNuevo()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_sel';
        $this->transaccion = 'TES_DETREPCONEW_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->capturaCount('total_1', 'numeric');
        $this->capturaCount('total_2', 'numeric');
        $this->capturaCount('total_3', 'numeric');

        //Definicion de la lista del resultado del query
        $this->captura('id_conciliacion_bancaria_rep', 'int4');
        $this->captura('id_conciliacion_bancaria', 'int4');
        $this->captura('nro_cheque', 'varchar');
        $this->captura('periodo_1', 'numeric');
        $this->captura('periodo_2', 'numeric');
        $this->captura('periodo_3', 'numeric');
        $this->captura('total_haber', 'numeric');
        $this->captura('estado', 'varchar');
        $this->captura('detalle', 'text');
        $this->captura('observacion', 'text');
        $this->captura('fecha', 'date');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->captura('id_periodo', 'int4');
        $this->captura('gestion', 'int4');
        $this->captura('periodo', 'int4');   // de param.tperiodo

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function finalizarConciliacion()
    {
        $this->procedimiento = 'tes.ft_conciliacion_bancaria_ime';
        $this->transaccion = 'TES_FINCONCI_FIN';
        $this->tipo_procedimiento = 'IME';

        $this->setParametro('id_conciliacion_bancaria', 'id_conciliacion_bancaria', 'int4');
        $this->setParametro('tipo_cambio', 'tipo_cambio', 'varchar');
        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
}

?>
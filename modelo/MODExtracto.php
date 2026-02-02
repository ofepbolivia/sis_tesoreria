<?php
/**
*@package pXP
*@file gen-MODExtracto.php
*@author  (gvelasquez)
*@date 21-11-2016 20:18:54
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODExtracto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarExtracto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='tes.ft_extracto_sel';
		$this->transaccion='TES_EXTRA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_extracto','int4');
		$this->captura('id_tipo_movimiento','int4');
		$this->captura('nombre_movimiento','varchar');
		$this->captura('saldo','numeric');
		$this->captura('cod_operacion','varchar');
		$this->captura('fecha','date');
		$this->captura('credito','numeric');
		$this->captura('nro_cbte','varchar');
		$this->captura('estado_conciliacion','varchar');
		$this->captura('importe_conciliar','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_documento','varchar');
		$this->captura('cuenta_transf','varchar');
		$this->captura('secuencial','int4');
		$this->captura('glosa','text');
		$this->captura('debito','numeric');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
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
			
	function insertarExtracto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_extracto_ime';
		$this->transaccion='TES_EXTRA_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','int4');
		$this->setParametro('id_tipo_movimiento','id_tipo_movimiento','int4');
		$this->setParametro('saldo','saldo','numeric');
		$this->setParametro('cod_operacion','cod_operacion','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('credito','credito','numeric');
		$this->setParametro('nro_cbte','nro_cbte','varchar');
		$this->setParametro('estado_conciliacion','estado_conciliacion','varchar');
		$this->setParametro('importe_conciliar','importe_conciliar','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_documento','nro_documento','varchar');
		$this->setParametro('cuenta_transf','cuenta_transf','varchar');
		$this->setParametro('secuencial','secuencial','int4');
		$this->setParametro('glosa','glosa','text');
		$this->setParametro('debito','debito','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarExtractoNew(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_extracto_ime';
		$this->transaccion='TES_EXTRA_INS_NEW';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','int4');
		$this->setParametro('id_tipo_movimiento','id_tipo_movimiento','int4');

		$this->setParametro('saldo','saldo','numeric');
		$this->setParametro('cod_operacion','cod_operacion','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('credito','credito','numeric');
		$this->setParametro('debito','debito','numeric');
		$this->setParametro('nro_cbte','nro_cbte','varchar');
		$this->setParametro('importe_conciliar','importe_conciliar','numeric');
		$this->setParametro('nro_documento','nro_documento','varchar');
		$this->setParametro('secuencial','secuencial','int4');
		$this->setParametro('glosa','glosa','text');
		$this->setParametro('estado_conciliacion','estado_conciliacion','varchar');
		$this->setParametro('cuenta_transf','cuenta_transf','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		// var_dump($this->getConsulta());
		// exit;
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarExtracto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_extracto_ime';
		$this->transaccion='TES_EXTRA_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_extracto','id_extracto','int4');
		$this->setParametro('id_tipo_movimiento','id_tipo_movimiento','int4');
		$this->setParametro('saldo','saldo','numeric');
		$this->setParametro('cod_operacion','cod_operacion','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('credito','credito','numeric');
		$this->setParametro('nro_cbte','nro_cbte','varchar');
		$this->setParametro('estado_conciliacion','estado_conciliacion','varchar');
		$this->setParametro('importe_conciliar','importe_conciliar','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_documento','nro_documento','varchar');
		$this->setParametro('cuenta_transf','cuenta_transf','varchar');
		$this->setParametro('secuencial','secuencial','int4');
		$this->setParametro('glosa','glosa','text');
		$this->setParametro('debito','debito','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarExtracto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_extracto_ime';
		$this->transaccion='TES_EXTRA_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_extracto','id_extracto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>
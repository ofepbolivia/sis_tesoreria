<?php
/**
*@package pXP
*@file gen-MODTipoMovimiento.php
*@author  (gvelasquez)
*@date 21-11-2016 20:20:09
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODTipoMovimiento extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTipoMovimiento(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='tes.ft_tipo_movimiento_sel';
		$this->transaccion='TES_TIPMOV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_movimiento','int4');
		$this->captura('nombre_movimiento','varchar');
		$this->captura('orden','numeric');
		$this->captura('nivel','int4');
		$this->captura('fk_tipo_movimiento','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		//echo($this->consulta);exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarTipoMovimientoArb(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='tes.ft_tipo_movimiento_sel';
		$this-> setCount(false);
		$this->transaccion='TES_TIPMOV_ARB_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$id_padre = $this->objParam->getParametro('id_padre');

		$this->setParametro('id_padre','id_padre','varchar');

		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_movimiento','int4');
		$this->captura('fk_tipo_movimiento','int4');
		$this->captura('nombre_movimiento','varchar');
		$this->captura('tipo_nodo','varchar');
		$this->captura('tipo','varchar');
		$this->captura('orden','numeric');
		$this->captura('nivel','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		return $this->respuesta;
	}

	function insertarTipoMovimiento(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_tipo_movimiento_ime';
		$this->transaccion='TES_TIPMOV_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('nombre_movimiento','nombre_movimiento','varchar');
		$this->setParametro('orden','orden','numeric');
		$this->setParametro('nivel','nivel','int4');
		$this->setParametro('fk_tipo_movimiento','fk_tipo_movimiento','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTipoMovimiento(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_tipo_movimiento_ime';
		$this->transaccion='TES_TIPMOV_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_movimiento','id_tipo_movimiento','int4');
		$this->setParametro('nombre_movimiento','nombre_movimiento','varchar');
		$this->setParametro('orden','orden','numeric');
		$this->setParametro('nivel','nivel','int4');
		$this->setParametro('fk_tipo_movimiento','fk_tipo_movimiento','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTipoMovimiento(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_tipo_movimiento_ime';
		$this->transaccion='TES_TIPMOV_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_movimiento','id_tipo_movimiento','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>
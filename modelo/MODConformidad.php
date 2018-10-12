<?php
/**
*@package pXP
*@file gen-MODConformidad.php
*@author  (admin)
*@date 05-09-2018 20:43:03
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODConformidad extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarConformidad(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='tes.ft_conformidad_sel';
		$this->transaccion='TES_TCONF_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_gestion','id_gestion','int4');

        //Definicion de la lista del resultado del query
		$this->captura('id_conformidad','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha_conformidad_final','date');
		$this->captura('fecha_inicio','date');
		$this->captura('fecha_fin','date');
		$this->captura('observaciones','varchar');
		$this->captura('id_obligacion_pago','int4');
		$this->captura('conformidad_final','text');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('num_tramite','varchar');

        //Ejecuta la instruccion
		$this->armarConsulta();
		//echo $this->consulta;exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarConformidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_conformidad_ime';
		$this->transaccion='TES_TCONF_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_conformidad_final','fecha_conformidad_final','date');
		$this->setParametro('fecha_inicio','fecha_inicio','date');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('conformidad_final','conformidad_final','text');
        $this->setParametro('id_gestion','id_gestion','int4');

        //Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarConformidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_conformidad_ime';
		$this->transaccion='TES_TCONF_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_conformidad','id_conformidad','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_conformidad_final','fecha_conformidad_final','date');
		$this->setParametro('fecha_inicio','fecha_inicio','date');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
		$this->setParametro('conformidad_final','conformidad_final','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarConformidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='tes.ft_conformidad_ime';
		$this->transaccion='TES_TCONF_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_conformidad','id_conformidad','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function generarConformidadFinal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='tes.ft_conformidad_ime';
        $this->transaccion='TES_GENCONFIN_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_conformidad','id_conformidad','int4');
        $this->setParametro('id_obligacion_pago','id_obligacion_pago','int4');
        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('conformidad_final','conformidad_final','text');
        $this->setParametro('fecha_conformidad_final','fecha_conformidad_final','date');
        $this->setParametro('observaciones','observaciones','varchar');



	        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
 //console.log($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

			
}
?>
<?php
/**
*@package pXP
*@file gen-ACTTipoMovimiento.php
*@author  (gvelasquez)
*@date 21-11-2016 20:20:09
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTipoMovimiento extends ACTbase{    
			
	function listarTipoMovimiento(){
		$this->objParam->defecto('ordenacion','id_tipo_movimiento');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('tipo') != '')
			$this->objParam->addFiltro("tipo = ''".$this->objParam->getParametro('tipo')."''");

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoMovimiento','listarTipoMovimiento');
		} else{
			$this->objFunc=$this->create('MODTipoMovimiento');
			
			$this->res=$this->objFunc->listarTipoMovimiento($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarTipoMovimientoArb(){

		//obtiene el parametro nodo enviado por la vista
		$node=$this->objParam->getParametro('node');

		$id_cuenta=$this->objParam->getParametro('id_tipo_movimiento');
		$tipo_nodo=$this->objParam->getParametro('tipo_nodo');


		if($node=='id'){
			$this->objParam->addParametro('id_padre','%');
		}
		else {
			$this->objParam->addParametro('id_padre',$id_cuenta);
		}

		$this->objFunc=$this->create('MODTipoMovimiento');
		$this->res=$this->objFunc->listarTipoMovimientoArb();

		$this->res->setTipoRespuestaArbol();

		$arreglo=array();

		array_push($arreglo,array('nombre'=>'id','valor'=>'id_tipo_movimiento'));
		array_push($arreglo,array('nombre'=>'id_p','valor'=>'id_tipo_movimiento_padre'));


		array_push($arreglo,array('nombre'=>'text','valores'=>'<b> #tipo_movimiento#</b>'));
		array_push($arreglo,array('nombre'=>'cls','valor'=>'tipo_movimiento'));
		array_push($arreglo,array('nombre'=>'qtip','valores'=>'<b><br> #tipo_movimiento#'));


		$this->res->addNivelArbol('tipo_nodo','raiz',array('leaf'=>false,
				'allowDelete'=>true,
				'allowEdit'=>true,
				'cls'=>'folder',
				'tipo_nodo'=>'raiz',
				'icon'=>'../../../lib/imagenes/a_form.png'),
				$arreglo);

		/*se ande un nivel al arbol incluyendo con tido de nivel carpeta con su arreglo de equivalencias
          es importante que entre los resultados devueltos por la base exista la variable\
          tipo_dato que tenga el valor en texto = 'hoja' */


		$this->res->addNivelArbol('tipo_nodo','hijo',array(
				'leaf'=>false,
				'allowDelete'=>true,
				'allowEdit'=>true,
				'tipo_nodo'=>'hijo',
				'icon'=>'../../../lib/imagenes/a_form.png'),
				$arreglo);


		$this->res->addNivelArbol('tipo_nodo','hoja',array(
				'leaf'=>true,
				'allowDelete'=>true,
				'allowEdit'=>true,
				'tipo_nodo'=>'hoja',
				'icon'=>'../../../lib/imagenes/a_table_gear.png'),
				$arreglo);


		$this->res->imprimirRespuesta($this->res->generarJson());

	}
				
	function insertarTipoMovimiento(){
		$this->objFunc=$this->create('MODTipoMovimiento');	
		if($this->objParam->insertar('id_tipo_movimiento')){
			$this->res=$this->objFunc->insertarTipoMovimiento($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTipoMovimiento($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTipoMovimiento(){
			$this->objFunc=$this->create('MODTipoMovimiento');	
		$this->res=$this->objFunc->eliminarTipoMovimiento($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>
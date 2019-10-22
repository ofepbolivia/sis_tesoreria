<?php
/**
*@package pXP
*@file ACTConciliacionBancaria.php
*@author  BVP
*@date 19-02-2019
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

include_once(dirname(__FILE__).'/../reportes/RConciliacionBancariaPDF.php');

class ACTConciliacionBancaria extends ACTbase{    
			
	function listarConciliacionBancaria (){
		$this->objParam->defecto('ordenacion','id_conciliacion_bancaria');                
		$this->objParam->defecto('dir_ordenacion','asc');
		
        $this->objParam->getParametro('id_cuenta_bancaria')!='' && $this->objParam->addFiltro("conci.id_cuenta_bancaria = ".$this->objParam->getParametro('id_cuenta_bancaria'));
        
        $this->objParam->getParametro('id_gestion')!='' && $this->objParam->addFiltro("conci.id_gestion = ".$this->objParam->getParametro('id_gestion'));

				
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODConciliacionBancaria','listarConciliacionBancaria ');
		} else{
			$this->objFunc=$this->create('MODConciliacionBancaria');
			
			$this->res=$this->objFunc->listarConciliacionBancaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarConciliacionBancaria(){		
		$this->objFunc=$this->create('MODConciliacionBancaria');	
		if($this->objParam->insertar('id_conciliacion_bancaria')){
			$this->res=$this->objFunc->insertarConciliacionBancaria($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarConciliacionBancaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarConciliacionBancaria(){
		$this->objFunc=$this->create('MODConciliacionBancaria');	
		$this->res=$this->objFunc->eliminarConciliacionBancaria($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function reporteConciliacionBancaria () {
        $this->objFunc=$this->create('MODConciliacionBancaria');
        $this->res=$this->objFunc->reporteConciliacionBancaria($this->objParam);
        $this->objFunc=$this->create('MODConciliacionBancaria');
        $this->res1=$this->objFunc->reporteConciliacionBancariaDet($this->objParam);		

        //obtener titulo del reporte
        $titulo = 'Requerimiento Conciliacion Bancaria';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //Instancia la clase de pdf

        $this->objReporteFormato=new RConciliacionBancariaPDF($this->objParam);
        $this->objReporteFormato->setDatos($this->res->datos,$this->res1->datos);
        $this->objReporteFormato->generarReporte();
        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());		
	}
	
	function detalleConciliacionBancaria () {
		$this->objParam->defecto('ordenacion','id_detalle_conciliacion_bancaria');                
		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_conciliacion_bancaria')!=''){
			$this->objParam->addFiltro("detcon.id_conciliacion_bancaria = ".$this->objParam->getParametro('id_conciliacion_bancaria'));					
		}		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODConciliacionBancaria','listarDetalleConciliacionBancaria ');
		} else{
			$this->objFunc=$this->create('MODConciliacionBancaria');
			
			$this->res=$this->objFunc->listarDetalleConciliacionBancaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());			
		
	}
	function insertarDetalleConciliacionBancaria(){		
		$this->objFunc=$this->create('MODConciliacionBancaria');	
		if($this->objParam->insertar('id_detalle_conciliacion_bancaria')){
			$this->res=$this->objFunc->insertarDetalleConciliacionBancaria($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDetalleConciliacionBancaria($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function eliminarDetalleConciliacionBancaria(){
		$this->objFunc=$this->create('MODConciliacionBancaria');	
		$this->res=$this->objFunc->eliminarDetalleConciliacionBancaria($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function regDetalleRepo() {
        $this->objFunc=$this->create('MODConciliacionBancaria');
        $this->res=$this->objFunc->regDetalleRepo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function detalleConciliaRepo() {
		$this->objParam->defecto('ordenacion','id_conciliacion_bancaria_rep');                
		$this->objParam->defecto('dir_ordenacion','asc');
		
        $this->objParam->getParametro('id_conciliacion_bancaria')!='' && $this->objParam->addFiltro("cbre.id_conciliacion_bancaria = ".$this->objParam->getParametro('id_conciliacion_bancaria'));
        
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODConciliacionBancaria','detalleConciliaRepo ');
		} else{
			$this->objFunc=$this->create('MODConciliacionBancaria');
            if ($this->objParam->getParametro('id_conciliacion_bancaria') != '') {
                $this->res=$this->objFunc->detalleConciliaRepo($this->objParam);                
                $temp = Array();
                $temp['total_1'] = $this->res->extraData['total_1'];
                $temp['total_2'] = $this->res->extraData['total_2'];
                $temp['total_3'] = $this->res->extraData['total_3'];
                $temp['tipo_reg'] = 'summary';                
                $this->res->total++;
                $this->res->addLastRecDatos($temp);
            }else{
                $this->res=$this->objFunc->detalleConciliaRepo($this->objParam);
            }
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
    }	
    function finalizarConciliacion() {
        $this->objFunc=$this->create('MODConciliacionBancaria');
        $this->res=$this->objFunc->finalizarConciliacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }	
}

?>
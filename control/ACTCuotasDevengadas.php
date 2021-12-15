<?php
/**
 *@package pXP
 *@file gen-ACTPlanPago.php
 *@author  (admin)
 *@date 10-04-2013 15:43:23
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTCuotasDevengadas extends ACTbase{

    function listarCuotas(){
      $this->objParam->defecto('ordenacion','id_plan_pago');


      if ($this->objParam->getParametro('id_gestion') != '') {
        $this->objParam->addFiltro("op.id_gestion = ".$this->objParam->getParametro('id_gestion'));
      }
      $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODCuotasDevengadas','listarCuotas');
        } else{
            $this->objFunc=$this->create('MODCuotasDevengadas');

            $this->res=$this->objFunc->listarCuotas($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function solicitarDevPag(){
        $this->objFunc=$this->create('MODCuotasDevengadas');
        $this->res=$this->objFunc->solicitarDevPag($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>

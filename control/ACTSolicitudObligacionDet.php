<?php

class ACTSolicitudObligacionDet extends ACTbase{

    function listarSolicitudObligacionDet(){
        $this->objParam->defecto('ordenacion','id_obligacion_det');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODSolicitudObligacionDet','listarSolicitudObligacionDet');
        } else{
            $this->objFunc=$this->create('MODSolicitudObligacionDet');

            $this->res=$this->objFunc->listarSolicitudObligacionDet($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarSolicitudObligacionDet(){
        $this->objFunc=$this->create('MODSolicitudObligacionDet');
        if($this->objParam->insertar('id_obligacion_det')){
            $this->res=$this->objFunc->insertarSolicitudObligacionDet($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarSolicitudObligacionDet($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarSolicitudObligacionDet(){
        $this->objFunc=$this->create('MODSolicitudObligacionDet');
        $this->res=$this->objFunc->eliminarSolicitudObligacionDet($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
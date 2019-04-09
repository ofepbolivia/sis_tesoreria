<?php


//require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
//require_once(dirname(__FILE__).'/../reportes/RSolicitudPlanPago.php');
//require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
//require_once(dirname(__FILE__).'/../reportes/RConformidad.php');
//require_once(dirname(__FILE__).'/../reportes/RProcesoConRetencionXLS.php');
//
//require_once(dirname(__FILE__) . '/../reportes/RConformidadTotal.php');

class ACTSolicitudPlanPago extends ACTbase{

    function listarPlanPago(){

        if($this->objParam->getParametro('tipo_interfaz')=='PlanPagoRegIni'){
            $this->objParam->defecto('ordenacion','nro_cuota');
            $this->objParam->defecto('dir_ordenacion','asc');
        }
        else{
            $this->objParam->defecto('ordenacion','id_plan_pago');
            $this->objParam->defecto('dir_ordenacion','asc');
        }

        if($this->objParam->getParametro('pes_estado')=='internacional'){
            $this->objParam->addFiltro("depto.prioridad = 3");
        }
        if($this->objParam->getParametro('pes_estado')=='nacional'){
            $this->objParam->addFiltro("depto.prioridad  != 3");
        }


        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("op.id_gestion = ".$this->objParam->getParametro('id_gestion'));
        }

        if($this->objParam->getParametro('id_obligacion_pago')!=''){
            $this->objParam->addFiltro("plapa.id_obligacion_pago = ".$this->objParam->getParametro('id_obligacion_pago'));
        }

        if($this->objParam->getParametro('filtro_campo')!=''){
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo')." = ".$this->objParam->getParametro('filtro_valor'));
        }

        if($this->objParam->getParametro('id_gestion') != ''){
            $this->objParam->addFiltro("op.id_gestion = ".$this->objParam->getParametro('id_gestion')." ");

        }

        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODSolicitudPlanPago','listarPlanPago');
        } else{
            $this->objFunc=$this->create('MODSolicitudPlanPago');

            $this->res=$this->objFunc->listarPlanPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarPlanPago(){
        $this->objFunc=$this->create('MODSolicitudPlanPago');
        if($this->objParam->insertar('id_plan_pago')){
            $this->res=$this->objFunc->insertarPlanPago($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarPlanPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function eliminarPlanPago(){
        $this->objFunc=$this->create('MODSolicitudPlanPago');
        $this->res=$this->objFunc->eliminarPlanPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function siguienteEstadoPlanPago(){
        $this->objFunc=$this->create('MODSolicitudPlanPago');

        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

        $this->res=$this->objFunc->siguienteEstadoPlanPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}

?>
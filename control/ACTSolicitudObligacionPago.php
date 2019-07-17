<?php

require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
//require_once(dirname(__FILE__).'/../reportes/RComEjePag.php');
//require_once(dirname(__FILE__).'/../reportes/RPlanesPago.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
//require_once(dirname(__FILE__).'/../reportes/RProcesosPendientesAdquisiciones.php');
//require_once(dirname(__FILE__).'/../reportes/RProcesosPendientesContabilidad.php');
//require_once(dirname(__FILE__).'/../reportes/RPagosSinDocumentosXls.php');
//require_once(dirname(__FILE__).'/../reportes/RCertificacionPresupuestaria.php');
//
//require_once(dirname(__FILE__).'/../reportes/RepProcPago.php');


class ACTSolicitudObligacionPago extends ACTbase{


    function insertarSolicitudObligacionPago(){
        $this->objFunc=$this->create('MODSolicitudObligacionPago');
        if($this->objParam->insertar('id_obligacion_pago')){
            $this->res=$this->objFunc->insertarSolicitudObligacionPago($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarSolicitudObligacionPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarSolicitudObligacionPago(){
        $this->objFunc=$this->create('MODSolicitudObligacionPago');
        $this->res=$this->objFunc->eliminarSolicitudObligacionPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarSolicitudObligacionPago(){
        $this->objParam->defecto('ordenacion','id_obligacion_pago');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);

        if($this->objParam->getParametro('id_obligacion_pago')!=''){
            $this->objParam->addFiltro("obpg.id_obligacion_pago = ".$this->objParam->getParametro('id_obligacion_pago'));
        }

        if($this->objParam->getParametro('pes_estado')=='otros'){
            $this->objParam->addFiltro("obpg.tipo_obligacion not  in (''pago_unico'',''pago_directo'')");
        }

        if($this->objParam->getParametro('pes_estado')=='pago_directo'){
            $this->objParam->addFiltro("obpg.tipo_obligacion  in (''pago_directo'')");
        }
        if($this->objParam->getParametro('pes_estado')=='pago_unico'){
            $this->objParam->addFiltro("obpg.tipo_obligacion  in (''pago_unico'')");
        }

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("obpg.id_gestion = ". $this->objParam->getParametro('id_gestion'));
        }


        //(f.e.a) Pagos de gestiones anteriores
        if($this->objParam->getParametro('pga_estado')=='borrador_pga'){
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if($this->objParam->getParametro('pga_estado')=='proceso_pga'){
            $this->objParam->addFiltro("obpg.estado in (''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(f.e.a) Pagos de procesos Manuales
        if($this->objParam->getParametro('ppm_estado')=='borrador_ppm'){
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if($this->objParam->getParametro('ppm_estado')=='proceso_ppm'){
            $this->objParam->addFiltro("obpg.estado in (''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(f.e.a) Pagos de Compras del Exterior
        if($this->objParam->getParametro('pce_estado')=='borrador_pce'){
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if($this->objParam->getParametro('pce_estado')=='proceso_pce'){
            $this->objParam->addFiltro("obpg.estado in (''vobogerencia'',''vbgaf'',''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(fea) Pagos Moneda base y moneda extranjera
        if($this->objParam->getParametro('moneda_base')=='base' && $this->objParam->getParametro('tipo_interfaz') == 'ObligacionPagoVb'){
            $this->objParam->addFiltro("mn.tipo_moneda = ''base''");
        }else if($this->objParam->getParametro('moneda_base')=='extranjero' && $this->objParam->getParametro('tipo_interfaz') == 'ObligacionPagoVb'){
            $this->objParam->addFiltro("mn.tipo_moneda != ''base''");
        }

        if($this->objParam->getParametro('filtro_campo')!=''){
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo')." = ".$this->objParam->getParametro('filtro_valor'));
        }
        if($this->objParam->getParametro('id_gestion') != ''){
            $this->objParam->addFiltro("obpg.id_gestion = ".$this->objParam->getParametro('id_gestion')." ");

        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODSolicitudObligacionPago','listarSolicitudObligacionPago');
        } else{
            $this->objFunc=$this->create('MODSolicitudObligacionPago');

            $this->res=$this->objFunc->listarSolicitudObligacionPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarSolicitudObligacionPagoSol(){
        $this->objParam->defecto('ordenacion','id_obligacion_pago');
        $this->objParam->defecto('dir_ordenacion','asc');

        $this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]);


        if($this->objParam->getParametro('tipo_interfaz')=='obligacionPagoUnico'){
            $this->objParam->addFiltro("obpg.tipo_obligacion = ''pago_unico''");
        }

        if($this->objParam->getParametro('tipo_interfaz')=='obligacionPagoSol'){
            $this->objParam->addFiltro("obpg.tipo_obligacion in (''pago_directo'',''rrhh'')");
        }

        if($this->objParam->getParametro('tipo_interfaz')=='obligacionPagoAdq'){
            $this->objParam->addFiltro("obpg.tipo_obligacion = ''adquisiciones''");
        }


        if($this->objParam->getParametro('id_obligacion_pago')!=''){
            $this->objParam->addFiltro("obpg.id_obligacion_pago = ".$this->objParam->getParametro('id_obligacion_pago'));
        }

        if($this->objParam->getParametro('filtro_campo')!=''){
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo')." = ".$this->objParam->getParametro('filtro_valor'));
        }
        if($this->objParam->getParametro('id_gestion') != ''){
            $this->objParam->addFiltro("obpg.id_gestion = ".$this->objParam->getParametro('id_gestion')." ");

        }


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODSolicitudObligacionPago','listarSolicitudObligacionPagoSol');
        } else{
            $this->objFunc=$this->create('MODSolicitudObligacionPago');

            $this->res=$this->objFunc->listarSolicitudObligacionPagoSol($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function siguienteEstadoSolObligacion(){
        $this->objFunc=$this->create('MODSolicitudObligacionPago');
        $this->res=$this->objFunc->siguienteEstadoSolObligacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>
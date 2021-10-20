<?php
/**
 * @package pXP
 * @file gen-ACTConformidad.php
 * @author  (admin)
 * @date 05-09-2018 20:43:03
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTConformidad extends ACTbase
{

    function listarConformidad()
    {
        $this->objParam->defecto('ordenacion', 'id_conformidad');

        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("op.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }



        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODConformidad', 'listarConformidad');
        } else {
            $this->objFunc = $this->create('MODConformidad');

            $this->res = $this->objFunc->listarConformidad($this->objParam);
        }


        $this->res->imprimirRespuesta($this->res->generarJson());

    }

    function insertarConformidad()
    {
        $this->objFunc = $this->create('MODConformidad');
        if ($this->objParam->insertar('id_conformidad')) {
            $this->res = $this->objFunc->insertarConformidad($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarConformidad($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarConformidad()
    {
        $this->objFunc = $this->create('MODConformidad');
        $this->res = $this->objFunc->eliminarConformidad($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function generarConformidadFinal()
    {
        $this->objFunc = $this->create('MODConformidad');
        $this->res = $this->objFunc->generarConformidadFinal($this->objParam);
        // {dev: breydi.vasquez, date: 20/10/2021, desc: ejecutar action firma documentos}
        if ($this->res->getTipo() == 'EXITO') {
            include("../../../sis_workflow/control/ActionFirmaDocumentos.php"); 
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>
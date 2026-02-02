<?php
/**
 * @package pXP
 * @file gen-ACTExtracto.php
 * @author  (gvelasquez)
 * @date 21-11-2016 20:18:54
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTExtracto extends ACTbase
{
    function listarExtracto()
    {
        $this->objParam->defecto('ordenacion', 'id_extracto');

        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODExtracto', 'listarExtracto');
        } else {
            if ($this->objParam->getParametro('id_cuenta_bancaria') != '') {
                $this->objParam->addFiltro("id_cuenta_bancaria = " . $this->objParam->getParametro('id_cuenta_bancaria'));
            }
            $this->objFunc = $this->create('MODExtracto');
            $this->res = $this->objFunc->listarExtracto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarExtracto()
    {
        $this->objFunc = $this->create('MODExtracto');
        if ($this->objParam->insertar('id_extracto')) {
            $this->res = $this->objFunc->insertarExtracto($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarExtracto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarExtracto()
    {
        $this->objFunc = $this->create('MODExtracto');
        $this->res = $this->objFunc->eliminarExtracto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
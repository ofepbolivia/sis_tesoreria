<?php
/**
 * @package pXP
 * @file ACTObligacionPago.php
 * @author  Gonzalo Sarmiento Sejas
 * @date 02-04-2013 16:01:32
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__) . '/../reportes/RComEjePag.php');
require_once(dirname(__FILE__) . '/../reportes/RPlanesPago.php');
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__) . '/../reportes/RProcesosPendientesAdquisiciones.php');
require_once(dirname(__FILE__) . '/../reportes/RProcesosPendientesContabilidad.php');
require_once(dirname(__FILE__) . '/../reportes/RPagosSinDocumentosXls.php');
require_once(dirname(__FILE__) . '/../reportes/RCertificacionPresupuestaria.php');

require_once(dirname(__FILE__) . '/../reportes/RepProcPago.php');
require_once(dirname(__FILE__) . '/../reportes/RSolicitudCompraObp.php');

class ACTObligacionPago extends ACTbase
{

    function listarObligacionPago()
    {
        $this->objParam->defecto('ordenacion', 'id_obligacion_pago');
        $this->objParam->defecto('dir_ordenacion', 'asc');
        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);

        if ($this->objParam->getParametro('id_obligacion_pago') != '') {
            $this->objParam->addFiltro("obpg.id_obligacion_pago = " . $this->objParam->getParametro('id_obligacion_pago'));
        }

        if ($this->objParam->getParametro('pes_estado') == 'otros') {
            $this->objParam->addFiltro("obpg.tipo_obligacion not  in (''pago_unico'',''pago_directo'')");
        }

        if ($this->objParam->getParametro('pes_estado') == 'pago_directo') {
            $this->objParam->addFiltro("obpg.tipo_obligacion  in (''pago_directo'')");
        }
        if ($this->objParam->getParametro('pes_estado') == 'pago_unico') {
            $this->objParam->addFiltro("obpg.tipo_obligacion  in (''pago_unico'')");
        }

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("obpg.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }


        //(f.e.a) Pagos de gestiones anteriores
        if ($this->objParam->getParametro('pga_estado') == 'borrador_pga') {
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if ($this->objParam->getParametro('pga_estado') == 'proceso_pga') {
            $this->objParam->addFiltro("obpg.estado in (''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(f.e.a) Pagos de procesos Manuales
        if ($this->objParam->getParametro('ppm_estado') == 'borrador_ppm') {
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if ($this->objParam->getParametro('ppm_estado') == 'proceso_ppm') {
            $this->objParam->addFiltro("obpg.estado in (''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(f.e.a) Pagos de Compras del Exterior
        if ($this->objParam->getParametro('pce_estado') == 'borrador_pce') {
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if ($this->objParam->getParametro('pce_estado') == 'proceso_pce') {
            $this->objParam->addFiltro("obpg.estado in (''vobogerencia'',''vbgaf'',''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }
        //(f.e.a) Pagos Boa Rep
        if ($this->objParam->getParametro('pbr_estado') == 'borrador_pbr') {
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if ($this->objParam->getParametro('pbr_estado') == 'proceso_pbr') {
            $this->objParam->addFiltro("obpg.estado in (''vobogerencia'',''vbgaf'',''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(f.e.a) Pagos Boa Rep
        if($this->objParam->getParametro('pbr_estado')=='borrador_pbr'){
            $this->objParam->addFiltro("obpg.estado in (''borrador'')");
        }
        if($this->objParam->getParametro('pbr_estado')=='proceso_pbr'){
            $this->objParam->addFiltro("obpg.estado in (''vobogerencia'',''vbgaf'',''vbpoa'', ''vb_jefe_aeropuerto'', ''suppresu'', ''vbpresupuestos'', ''registrado'', ''en_pago'', ''finalizado'')");
        }

        //(fea) Pagos Moneda base y moneda extranjera
        if ($this->objParam->getParametro('moneda_base') == 'base' && $this->objParam->getParametro('tipo_interfaz') == 'ObligacionPagoVb') {
            $this->objParam->addFiltro("mn.tipo_moneda = ''base''");
        } else if ($this->objParam->getParametro('moneda_base') == 'extranjero' && $this->objParam->getParametro('tipo_interfaz') == 'ObligacionPagoVb') {
            $this->objParam->addFiltro("mn.tipo_moneda != ''base''");
        }

        if ($this->objParam->getParametro('filtro_campo') != '') {
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo') . " = " . $this->objParam->getParametro('filtro_valor'));
        }
        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("obpg.id_gestion = " . $this->objParam->getParametro('id_gestion') . " ");

        }

        //filtro breydi.vasquez 07/01/2020 
        $this->objParam->getParametro('tramite_sin_presupuesto_centro_c') != '' && $this->objParam->addFiltro("obpg.presupuesto_aprobado = ''sin_presupuesto_cc'' ");

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObligacionPago', 'listarObligacionPago');
        } else {
            $this->objFunc = $this->create('MODObligacionPago');

            $this->res = $this->objFunc->listarObligacionPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    /*

      Listado de obligacion de pago directas, para solicitudes individuales o por asistentes

    */

    function listarObligacionPagoSol()
    {
        $this->objParam->defecto('ordenacion', 'id_obligacion_pago');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);


        if ($this->objParam->getParametro('tipo_interfaz') == 'obligacionPagoUnico') {
            $this->objParam->addFiltro("obpg.tipo_obligacion = ''pago_unico''");
        }

        if ($this->objParam->getParametro('tipo_interfaz') == 'obligacionPagoSol') {
            $this->objParam->addFiltro("obpg.tipo_obligacion in (''pago_directo'',''rrhh'')");
        }

        if ($this->objParam->getParametro('tipo_interfaz') == 'obligacionPagoAdq') {
            $this->objParam->addFiltro("obpg.tipo_obligacion = ''adquisiciones''");
        }

        if ($this->objParam->getParametro('id_obligacion_pago') != '') {
            $this->objParam->addFiltro("obpg.id_obligacion_pago = " . $this->objParam->getParametro('id_obligacion_pago'));
        }

        if ($this->objParam->getParametro('filtro_campo') != '') {
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo') . " = " . $this->objParam->getParametro('filtro_valor'));
        }
        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("obpg.id_gestion = " . $this->objParam->getParametro('id_gestion') . " ");

        }

        //para internacionales SP, SPD, SPI
        if ($this->objParam->getParametro('tipo_interfaz') == 'obligacionPagoS') {
            $this->objParam->addFiltro("obpg.tipo_obligacion in (''sp'')");
        }
        if ($this->objParam->getParametro('tipo_interfaz') == 'solicitudObligacionPagoUnico') {
            $this->objParam->addFiltro("obpg.tipo_obligacion in (''spd'')");
        }

        if ($this->objParam->getParametro('tipo_interfaz') == 'obligacionPagoInterS') {
            $this->objParam->addFiltro("obpg.tipo_obligacion in (''spi'')");
        }

        //filtro breydi.vasquez 07/01/2020 
        $this->objParam->getParametro('tramite_sin_presupuesto_centro_c') != '' && $this->objParam->addFiltro("obpg.presupuesto_aprobado = ''sin_presupuesto_cc'' ");
        //
        
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODObligacionPago','listarObligacionPagoSol');
        } else{
            $this->objFunc=$this->create('MODObligacionPago');
            
            $this->res=$this->objFunc->listarObligacionPagoSol($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    /*
     * Author:  		 RAC - KPLIAN
     * Date:   			 19/02/2015
     * Description		 insertar obligaciones de pago unicas
     * */

    function insertarObligacionCompleta()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        if ($this->objParam->insertar('id_obligacion_pago')) {
            $this->res = $this->objFunc->insertarObligacionCompleta($this->objParam);
        } else {
            //TODO .. trabajar en la edicion
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarObligacionPago()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        if ($this->objParam->insertar('id_obligacion_pago')) {
            $this->res = $this->objFunc->insertarObligacionPago($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarObligacionPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarObligacionPago()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->eliminarObligacionPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function modificarObsPresupuestos()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->modificarObsPresupuestos($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function extenderOp()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->extenderOp($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function modificarObsPoa()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->modificarObsPoa($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function insertarAjustes()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->insertarAjustes($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function finalizarRegistro()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->finalizarRegistro($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function siguienteEstadoObligacion()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->siguienteEstadoObligacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function anteriorEstadoObligacion()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->anteriorEstadoObligacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //fea 12/2/2018
    function anteriorEstadoObligacionPago()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->anteriorEstadoObligacionPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerFaltante()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->obtenerFaltante($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function listarDeptoFiltradoObligacionPago()
    {

        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->obtnerUosEpsDetalleObligacion($this->objParam);

        //si sucede un error
        if ($this->res->getTipo() == 'ERROR') {

            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        }

        //var_dump($this->res->datos);

        $this->datos = array();
        $this->datos = $this->res->getDatos();
        $uos = $this->res->datos['uos'];
        $eps = $this->res->datos['eps'];


        $this->objParam->addParametro('eps', $eps);
        $this->objParam->addParametro('uos', $uos);

        //////////////////////////


        // parametros de ordenacion por defecto
        $this->objParam->defecto('ordenacion', 'depto');
        $this->objParam->defecto('dir_ordenacion', 'asc');


        $this->objFunc = $this->create('sis_parametros/MODDepto');
        //ejecuta el metodo de lista personas a travez de la intefaz objetoFunSeguridad
        $this->res = $this->objFunc->listarDeptoFiltradoXUOsEPs($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());


    }

    function reporteComEjePag()
    {
        $dataSource = new DataSource();

        $this->objParam->addParametroConsulta('ordenacion', 'id_obligacion_pago');
        $this->objParam->addParametroConsulta('dir_ordenacion', 'ASC');
        $this->objParam->addParametroConsulta('cantidad', 1000);
        $this->objParam->addParametroConsulta('puntero', 0);

        //consulta por los datos de la obligacion de pago
        $this->objFunc = $this->create('MODObligacionPago');
        $resultObligacionPago = $this->objFunc->obligacionPagoSeleccionado($this->objParam);

        if ($resultObligacionPago->getTipo() == 'EXITO') {

            $datosObligacionPago = $resultObligacionPago->getDatos();
            $dataSource->putParameter('desc_proveedor', $datosObligacionPago[0]['desc_proveedor']);
            $dataSource->putParameter('estado', $datosObligacionPago[0]['estado']);
            $dataSource->putParameter('tipo_obligacion', $datosObligacionPago[0]['tipo_obligacion']);
            $dataSource->putParameter('obs', $datosObligacionPago[0]['obs']);
            $dataSource->putParameter('nombre_subsistema', $datosObligacionPago[0]['nombre_subsistema']);
            $dataSource->putParameter('porc_retgar', $datosObligacionPago[0]['porc_retgar']);
            $dataSource->putParameter('porc_anticipo', $datosObligacionPago[0]['porc_anticipo']);
            $dataSource->putParameter('nombre_depto', $datosObligacionPago[0]['nombre_depto']);
            $dataSource->putParameter('num_tramite', $datosObligacionPago[0]['num_tramite']);
            $dataSource->putParameter('fecha', $datosObligacionPago[0]['fecha']);
            $dataSource->putParameter('numero', $datosObligacionPago[0]['numero']);
            $dataSource->putParameter('tipo_cambio_conv', $datosObligacionPago[0]['tipo_cambio_conv']);
            $dataSource->putParameter('comprometido', $datosObligacionPago[0]['comprometido']);
            $dataSource->putParameter('nro_cuota_vigente', $datosObligacionPago[0]['nro_cuota_vigente']);
            $dataSource->putParameter('pago_variable', $datosObligacionPago[0]['pago_variable']);
            $dataSource->putParameter('moneda', $datosObligacionPago[0]['moneda']);


            //consulta por el detalle de obligacion
            $this->objParam->addParametroConsulta('ordenacion', 'id_obligacion_det');
            $this->objParam->addParametroConsulta('dir_ordenacion', 'ASC');
            $this->objParam->addParametroConsulta('cantidad', 1000);
            $this->objParam->addParametroConsulta('puntero', 0);

            //listado del detalle
            $this->objFunc = $this->create('MODObligacionPago');
            $resultObligacion = $this->objFunc->listarObligacion($this->objParam);

            if ($resultObligacion->getTipo() == 'EXITO') {

                $datosObligacion = $resultObligacion->getDatos();
                $dataSource->setDataSet($datosObligacion);
                $nombreArchivo = 'Reporte.pdf';
                $reporte = new RComEjePag();
                $reporte->setDataSource($dataSource);
                $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
                $reportWriter->writeReport(ReportWriter::PDF);
                $mensajeExito = new Mensaje();

                $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
                    'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');

                $mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->res = $mensajeExito;
                $this->res->imprimirRespuesta($this->res->generarJson());
            } else {
                $resultObligacion->imprimirRespuesta($resultObligacion->generarJson());

            }
        } else {

            $resultObligacionPago->imprimirRespuesta($resultObligacionPago->generarJson());
        }
    }

    function reportePlanesPago()
    {
        $dataSource = new DataSource();

        $this->objParam->addParametroConsulta('ordenacion', 'id_obligacion_pago');
        $this->objParam->addParametroConsulta('dir_ordenacion', 'ASC');
        $this->objParam->addParametroConsulta('cantidad', 1000);
        $this->objParam->addParametroConsulta('puntero', 0);

        $this->objFunc = $this->create('MODObligacionPago');
        $resultObligacionPago = $this->objFunc->obligacionPagoSeleccionado($this->objParam);


        $datosObligacionPago = $resultObligacionPago->getDatos();
        $dataSource->putParameter('desc_proveedor', $datosObligacionPago[0]['desc_proveedor']);
        $dataSource->putParameter('estado', $datosObligacionPago[0]['estado']);
        $dataSource->putParameter('tipo_obligacion', $datosObligacionPago[0]['tipo_obligacion']);
        $dataSource->putParameter('obs', $datosObligacionPago[0]['obs']);
        $dataSource->putParameter('nombre_subsistema', $datosObligacionPago[0]['nombre_subsistema']);
        $dataSource->putParameter('porc_retgar', $datosObligacionPago[0]['porc_retgar']);
        $dataSource->putParameter('porc_anticipo', $datosObligacionPago[0]['porc_anticipo']);
        $dataSource->putParameter('nombre_depto', $datosObligacionPago[0]['nombre_depto']);
        $dataSource->putParameter('num_tramite', $datosObligacionPago[0]['num_tramite']);
        $dataSource->putParameter('fecha', $datosObligacionPago[0]['fecha']);
        $dataSource->putParameter('numero', $datosObligacionPago[0]['numero']);
        $dataSource->putParameter('tipo_cambio_conv', $datosObligacionPago[0]['tipo_cambio_conv']);
        $dataSource->putParameter('comprometido', $datosObligacionPago[0]['comprometido']);
        $dataSource->putParameter('nro_cuota_vigente', $datosObligacionPago[0]['nro_cuota_vigente']);
        $dataSource->putParameter('pago_variable', $datosObligacionPago[0]['pago_variable']);

        $this->objParam->addParametroConsulta('ordenacion', 'nro_cuota');
        $this->objParam->addParametroConsulta('dir_ordenacion', 'asc');
        $this->objFunc = $this->create('MODPlanPago');
        $resultPlanesPago = $this->objFunc->listarPlanesPagoPorObligacion($this->objParam);
        $datosPlanesPago = $resultPlanesPago->getDatos();
        $dataSource->setDataSet($datosPlanesPago);

        $nombreArchivo = 'Reporte.pdf';

        $reporte = new RPlanesPago();
        $reporte->setDataSource($dataSource);
        $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
        $reportWriter->writeReport(ReportWriter::PDF);

        $mensajeExito = new Mensaje();

        $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $mensajeExito->setArchivoGenerado($nombreArchivo);

        $this->res = $mensajeExito;

        $this->res->imprimirRespuesta($this->res->generarJson());

    }

    function obtenerIdsExternos()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->obtenerIdsExternos($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarObligacionPresupuesto()
    {
        $this->objParam->defecto('ordenacion', 'id_partida');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObligacionPago', 'listarObligacion');
        } else {
            $this->objFunc = $this->create('MODObligacionPago');

            $this->res = $this->objFunc->listarObligacion($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function revertirParcialmentePresupuesto()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->revertirParcialmentePresupuesto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function recuperarDatosFiltro()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->recuperarDatosFiltro($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function reporteProcesosPendientes()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->listarProcesosPendientes($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Procesos Pendientes';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RProcesosPendientesAdquisiciones($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function reporteProcesosPenContabilidad()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->listarProcesosPendientes($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Procesos Pendientes';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RProcesosPendientesContabilidad($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function recuperarPagoSinDocumento()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $cbteHeader = $this->objFunc->recuperarPagoSinDocumento($this->objParam);
        if ($cbteHeader->getTipo() == 'EXITO') {
            return $cbteHeader;
        } else {
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }

    function reportePagoSinDocumento()
    {

        $nombreArchivo = 'PagosSinDocumentos' . uniqid(md5(session_id())) . '.xls';

        $dataSource = $this->recuperarPagoSinDocumento();

        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';

        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        $reporte = new RPagosSinDocumentosXls($this->objParam);
        $reporte->datosHeader($dataSource->getDatos());
        $reporte->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    //Reporte Certificación Presupuestaria (F.E.A) 01/08/2017
    function reporteCertificacionP()
    {
        $this->objFunc = $this->create('MODObligacionPago');
        $dataSource = $this->objFunc->reporteCertificacionP();
        $this->dataSource = $dataSource->getDatos();

        $nombreArchivo = uniqid(md5(session_id()) . '[Reporte-CertificaciónPresupuestaria]') . '.pdf';
        $this->objParam->addParametro('orientacion', 'P');
        $this->objParam->addParametro('tamano', 'LETTER');
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        $this->objReporte = new RCertificacionPresupuestaria($this->objParam);
        $this->objReporte->setDatos($this->dataSource);
        $this->objReporte->generarReporte();
        $this->objReporte->output($this->objReporte->url_archivo, 'F');


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    //(FEA) Reporte Solicitud Centros Pagos Manuales
    function reporteSolicitudCentros()
    {
        $dataSource = new DataSource();

        /*$this->objParam->addParametroConsulta('ordenacion','id_obligacion_pago');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1000);
        $this->objParam->addParametroConsulta('puntero',0);*/

        //consulta por los datos de la obligacion de pago
        $this->objFunc = $this->create('MODObligacionPago');
        $resultObligacionPago = $this->objFunc->reporteSolicitudCentros($this->objParam);

        if ($resultObligacionPago->getTipo() == 'EXITO') {

            $datosObligacionPago = $resultObligacionPago->getDatos();

            $dataSource->putParameter('desc_proveedor', $datosObligacionPago[0]['desc_proveedor']);
            $dataSource->putParameter('estado', $datosObligacionPago[0]['estado']);
            $dataSource->putParameter('tipo_obligacion', $datosObligacionPago[0]['tipo_obligacion']);
            $dataSource->putParameter('obs', $datosObligacionPago[0]['obs']);
            $dataSource->putParameter('nombre_subsistema', $datosObligacionPago[0]['nombre_subsistema']);
            $dataSource->putParameter('porc_retgar', $datosObligacionPago[0]['porc_retgar']);
            $dataSource->putParameter('porc_anticipo', $datosObligacionPago[0]['porc_anticipo']);
            $dataSource->putParameter('nombre_depto', $datosObligacionPago[0]['nombre_depto']);
            $dataSource->putParameter('num_tramite', $datosObligacionPago[0]['num_tramite']);
            $dataSource->putParameter('fecha', $datosObligacionPago[0]['fecha']);
            $dataSource->putParameter('numero', $datosObligacionPago[0]['numero']);
            $dataSource->putParameter('tipo_cambio_conv', $datosObligacionPago[0]['tipo_cambio_conv']);
            $dataSource->putParameter('comprometido', $datosObligacionPago[0]['comprometido']);
            $dataSource->putParameter('nro_cuota_vigente', $datosObligacionPago[0]['nro_cuota_vigente']);
            $dataSource->putParameter('pago_variable', $datosObligacionPago[0]['pago_variable']);
            $dataSource->putParameter('moneda', $datosObligacionPago[0]['moneda']);
            $dataSource->putParameter('id_moneda', $datosObligacionPago[0]['id_moneda']);
            $dataSource->putParameter('id_obligacion_pago', $datosObligacionPago[0]['id_obligacion_pago']);

            $this->objParam->addParametro('id_moneda', $datosObligacionPago[0]['id_moneda']);
            $this->objParam->addParametro('id_obligacion_pago', $datosObligacionPago[0]['id_obligacion_pago']);
            //consulta por el detalle de obligacion
            $this->objParam->addParametroConsulta('ordenacion', 'id_obligacion_det');
            $this->objParam->addParametroConsulta('dir_ordenacion', 'ASC');
            $this->objParam->addParametroConsulta('cantidad', 1000);
            $this->objParam->addParametroConsulta('puntero', 0);


            //listado del detalle
            $this->objFunc = $this->create('MODObligacionPago');
            $resultObligacion = $this->objFunc->listarObligacion($this->objParam);

            if ($resultObligacion->getTipo() == 'EXITO') {

                $datosObligacion = $resultObligacion->getDatos();
                $dataSource->setDataSet($datosObligacion);
                $nombreArchivo = 'Reporte.pdf';
                $reporte = new RComEjePag();
                $reporte->setDataSource($dataSource);
                $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
                $reportWriter->writeReport(ReportWriter::PDF);
                $mensajeExito = new Mensaje();

                $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
                    'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');

                $mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->res = $mensajeExito;
                $this->res->imprimirRespuesta($this->res->generarJson());
            } else {
                $resultObligacion->imprimirRespuesta($resultObligacion->generarJson());

            }
        } else {

            $resultObligacionPago->imprimirRespuesta($resultObligacionPago->generarJson());
        }
    }

    function reporteProcesoPago()
    {

        $this->objFunc = $this->create('MODObligacionPago');
        $this->res = $this->objFunc->reporteProcesoPago($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Procesos pago';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RepProcPago($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function listarOblPago()
    {
        $this->objParam->defecto('ordenacion', 'id_obligacion_pago');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('id_obligacion_pago') != '') {
            $this->objParam->addFiltro("obpg.id_obligacion_pago = " . $this->objParam->getParametro('id_obligacion_pago'));
        }

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("obpg.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }


        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObligacionPago', 'listarOblPago');
        } else {
            $this->objFunc = $this->create('MODObligacionPago');

            $this->res = $this->objFunc->listarOblPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function TsLibroBancosExterior() {
		//$this->objParam->defecto('ordenacion','id_obligacion_pago');
		$this->objParam->defecto('ordenacion','id_plan_pago');
        $this->objParam->defecto('dir_ordenacion','asc');

        //27-01-2020 (may) se añade dos pestañas mas para tramites PGA exterior y locales
        /*
        if ($this->objParam->getParametro('pes_estado') == 'exterior' || $this->objParam->getParametro('pes_estado') == 'pgaexterior') {
            $this->objParam->addFiltro("plbex.prioridad = 3");
        }else{
            $this->objParam->addFiltro("plbex.prioridad <> 3");
        }
        */

        if ($this->objParam->getParametro('pes_estado') == 'exterior' || $this->objParam->getParametro('pes_estado') == 'pgaexterior') {
            if ($this->objParam->getParametro('pes_estado') == 'pgaexterior') {
                $this->objParam->addFiltro("plbex.tipo_obligacion in (''pga'') ");
                $this->objParam->addFiltro("plbex.prioridad = 3 ");
            }else{
                $this->objParam->addFiltro("plbex.prioridad = 3 ");
            }

        }else{
            if ($this->objParam->getParametro('pes_estado') == 'pgainterior' ){
                $this->objParam->addFiltro("plbex.tipo_obligacion in (''pga'') ");
                $this->objParam->addFiltro("plbex.prioridad <> 3 ");
            }else{
                $this->objParam->addFiltro("plbex.prioridad <> 3 ");
            }
        }


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODObligacionPago','TsLibroBancosExterior');
		} else{
			$this->objFunc=$this->create('MODObligacionPago');
			
			$this->res=$this->objFunc->TsLibroBancosExterior($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());        
    }
    function listarEvoluPresup()
    {
        $this->objParam->defecto('ordenacion', 'id_partida_ejecucion');
        $this->objParam->defecto('dir_ordenacion', 'desc');
        
        //$this->objParam->getParametro('tipo_interfaz') == 'comprometido' && $this->objParam->addFiltro("tipo_movimiento = ''comprometido'' and tipo_movimiento is null ");
        //$this->objParam->getParametro('tipo_interfaz') == 'ejecutado'  && $this->objParam->addFiltro("tipo_movimiento = ''ejecutado'' and tipo_movimiento is null ");
        //$this->objParam->getParametro('tipo_interfaz') == 'pagado'  && $this->objParam->addFiltro("tipo_movimiento = ''pagado'' and tipo_movimiento is null");

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODObligacionPago', 'listarEvoluPresup');
        } else {
            $this->objFunc = $this->create('MODObligacionPago');
            $this->res = $this->objFunc->listarEvoluPresup($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }    
    function clonarOP(){
        $this->objFunc=$this->create('MODObligacionPago');
        $this->res=$this->objFunc->clonarOP($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function aprobarPresupuestoSolicitud(){
        $this->objFunc=$this->create('MODObligacionPago');
        $this->res=$this->objFunc->aprobarPresupuestoSolicitud($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    // Reporte tipo solicitud procesos de obligaciones de pago (Breydi vasquez) 21/02/2020

    function obtenerCategoriaProg()
    {
        //crea el objetoFunSeguridad que contiene todos los metodos del sistema de seguridad
        $this->objFunSeguridad = $this->create('sis_seguridad/MODSubsistema');
        $objParam = new CTParametro($aPostData['p'], null, $aPostFiles);
        $objParam->addParametro('codigo', 'pre_verificar_categoria');
        $objFunc = new MODSubsistema($objParam);
        $this->res = $objFunc->obtenerVariableGlobal($this->objParam);

        return $this->res->getDatos();
    }

    function reporteSolicitud($create_file = false, $onlyData = false){

        $dataSource = new DataSource();
        $sw_cat = $this->obtenerCategoriaProg();
        //var_dump($sw_cat); exit;

        $this->objParam->addParametroConsulta('ordenacion', 'id_obligacion_pago');
        $this->objParam->addParametroConsulta('dir_ordenacion', 'ASC');
        $this->objParam->addParametroConsulta('cantidad', 1000);
        $this->objParam->addParametroConsulta('puntero', 0);

        $this->objFunc = $this->create('MODObligacionPago');

        $resultSolicitud = $this->objFunc->reporteSolicitud();

        $datosSolicitud = $resultSolicitud->getDatos();

        //armamos el array parametros y metemos ahi los data sets de las otras tablas
        $dataSource->putParameter('id_obligacion_pago', $datosSolicitud[0]['id_obligacion_pago']);
        $dataSource->putParameter('estado', $datosSolicitud[0]['estado']);
        $dataSource->putParameter('numero', $datosSolicitud[0]['numero']);
        $dataSource->putParameter('num_tramite', $datosSolicitud[0]['num_tramite']);
        $dataSource->putParameter('fecha_apro', $datosSolicitud[0]['fecha_apro']);
        $dataSource->putParameter('desc_moneda', $datosSolicitud[0]['desc_moneda']);
        $dataSource->putParameter('tipo', $datosSolicitud[0]['tipo']);
        $dataSource->putParameter('desc_gestion', $datosSolicitud[0]['desc_gestion']);
        $dataSource->putParameter('fecha_soli', $datosSolicitud[0]['fecha_soli']);
        $dataSource->putParameter('fecha_soli_gant', $datosSolicitud[0]['fecha_soli_gant']);
        $dataSource->putParameter('desc_funcionario', $datosSolicitud[0]['desc_funcionario']);
        $dataSource->putParameter('desc_uo', $datosSolicitud[0]['desc_uo']);
        $dataSource->putParameter('desc_depto', $datosSolicitud[0]['desc_depto']);
        $dataSource->putParameter('desc_funcionario_apro', $datosSolicitud[0]['desc_funcionario_apro']);
        $dataSource->putParameter('cargo_desc_funcionario', $datosSolicitud[0]['cargo_desc_funcionario']);
        $dataSource->putParameter('cargo_desc_funcionario_apro', $datosSolicitud[0]['cargo_desc_funcionario_apro']);
        $dataSource->putParameter('fecha_reg', $datosSolicitud[0]['fecha_reg']);
        $dataSource->putParameter('codigo_poa', $datosSolicitud[0]['codigo_poa']);
        $dataSource->putParameter('nombre_usuario_ai', $datosSolicitud[0]['nombre_usuario_ai']);
        $dataSource->putParameter('codigo_uo', $datosSolicitud[0]['codigo_uo']);
        $dataSource->putParameter('prioridad', $datosSolicitud[0]['prioridad']);
        $dataSource->putParameter('justificacion', $datosSolicitud[0]['obs']);

        //Reset all extra params:
        $this->objParam->defecto('ordenacion', 'id_obligacion_det');
        $this->objParam->defecto('cantidad', 1000);
        $this->objParam->defecto('puntero', 0);

        $this->objParam->addParametro('id_obligacion_pago', $datosSolicitud[0]['id_obligacion_pago']);

        $modSolicitudDet = $this->create('MODObligacionDet');
        //lista el detalle de la solicitud
        $resultSolicitudDet = $modSolicitudDet->listarObligacionDet();

        $solicitudDetAgrupado = $this->groupArray($resultSolicitudDet->getDatos(), 'codigo_partida', 'desc_centro_costo', $datosSolicitud[0]['id_moneda'], $datosSolicitud[0]['estado'], $onlyData);
        $solicitudDetDataSource = new DataSource();

        $solicitudDetDataSource->setDataSet($solicitudDetAgrupado);

        //inserta el detalle de la colistud como origen de datos
        $dataSource->putParameter('detalleDataSource', $solicitudDetDataSource);
        $dataSource->putParameter('sw_cat', $sw_cat["valor"]);

        if ($onlyData) {
            return $dataSource;
        }
        $nombreArchivo = uniqid(md5(session_id()) . 'SolicitudCompraObp') . '.pdf';
        $this->objParam->addParametro('orientacion', 'P');
        $this->objParam->addParametro('tamano', 'LETTER');
        $this->objParam->addParametro('titulo_archivo', 'OBLIGACION DE PAGO');
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        //construir el reporte
        $reporte = new RSolicitudCompraObp($this->objParam);

        $reporte->setDataSource($dataSource);
        $reporte->write();

        if (!$create_file) {
            $mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
            $mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
        } else {
            return dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo;
        }

    }

    function groupArray($array, $groupkey, $groupkeyTwo, $id_moneda, $estado_sol, $onlyData)
    {
        if (count($array) > 0) {
            //recupera las llaves del array
            $keys = array_keys($array[0]);

            $removekey = array_search($groupkey, $keys);
            $removekeyTwo = array_search($groupkeyTwo, $keys);

            if ($removekey === false)
                return array("Clave \"$groupkey\" no existe");
            if ($removekeyTwo === false)
                return array("Clave \"$groupkeyTwo\" no existe");


            //crea los array para agrupar y para busquedas
            $groupcriteria = array();
            $arrayResp = array();

            //recorre el resultado de la consulta de oslicitud detalle
            foreach ($array as $value) {
                //por cada registro almacena el valor correspondiente en $item
                $item = null;
                foreach ($keys as $key) {
                    $item[$key] = $value[$key];
                }

                //buscar si el grupo ya se incerto
                $busca = array_search($value[$groupkey] . $value[$groupkeyTwo], $groupcriteria);

                if ($busca === false) {
                    //si el grupo no existe lo crea
                    //en la siguiente posicicion de crupcriteria agrega el identificador del grupo
                    $groupcriteria[] = $value[$groupkey] . $value[$groupkeyTwo];

                    //en la siguiente posivion cre ArrayResp cre un btupo con el identificaor nuevo
                    //y un bubgrupo para acumular los detalle de semejaste caracteristicas

                    $arrayResp[] = array($groupkey . $groupkeyTwo => $value[$groupkey] . $value[$groupkeyTwo], 'groupeddata' => array(), 'presu_verificado' => "false");
                    $arrayPresuVer[] =
                        //coloca el indice en la ultima posicion insertada
                    $busca = count($arrayResp) - 1;

                }

                //inserta el registro en el subgrupo correspondiente
                $arrayResp[$busca]['groupeddata'][] = $item;

            }


            $cont_grup = 0;
            foreach ($arrayResp as $value2) {
                $grup_desc_centro_costo = "";
                $cc_array = array();
                foreach ($value2['groupeddata'] as $value_det) {


                    if (!in_array($value_det["desc_centro_costo"], $cc_array)) {
                        $grup_desc_centro_costo = $grup_desc_centro_costo . "\n" . $value_det["desc_centro_costo"];
                        $cc_array[] = $value_det["desc_centro_costo"];
                    }
                    //sumamos el monto a comprometer

                }
                $arrayResp[$cont_grup]["grup_desc_centro_costo"] = trim($grup_desc_centro_costo);
                $cont_grup++;
            }
            //solo verificar si el estado es borrador o pendiente
            //suma y verifica el presupuesto

            $estado_sin_presupuesto = array("borrador", "registrado", "vbpoa", "suppresu", "vbpresupuestos", "vbgaf", "vobogerencia");

            if (in_array($estado_sol, $estado_sin_presupuesto) || $onlyData) {

                $cont_grup = 0;
                foreach ($arrayResp as $value2) {
                    $cc_array = array();
                    $total_pre = 0;
                    $grup_desc_centro_costo = "";

                    $busca = array_search($value2[$groupkey] . $value2[$groupkeyTwo], $groupcriteria);

                    foreach ($value2['groupeddata'] as $value_det) {
                        //sumamos el monto a comprometer
                        $total_pre = $total_pre + $value_det["monto_pago_mo"];
                        if (!in_array($value_det["desc_centro_costo"], $cc_array)) {
                            $grup_desc_centro_costo = $grup_desc_centro_costo . "\n" . $value_det["desc_centro_costo"];
                            $cc_array[] = $value_det["desc_centro_costo"];
                        }
                    }

                    $value_det = $value2['groupeddata'][0];

                    $this->objParam = new CTParametro(null, null, null);
                    $this->objParam->addParametro('id_presupuesto', $value_det["id_centro_costo"]);
                    $this->objParam->addParametro('id_partida', $value_det["id_partida"]);
                    $this->objParam->addParametro('id_moneda', $id_moneda);
                    $this->objParam->addParametro('monto_total', $total_pre);
                    $this->objParam->addParametro('id_solicitud', $value_det['id_obligacion_pago']);
                    $this->objParam->addParametro('sis_origen', 'OBP');

                    $this->objFunc = $this->create('sis_presupuestos/MODPresupuesto');
                    $resultSolicitud = $this->objFunc->verificarPresupuesto();

                    $arrayResp[$cont_grup]["presu_verificado"] = $resultSolicitud->datos["presu_verificado"];
                    $arrayResp[$cont_grup]["total_presu_verificado"] = $total_pre;
                    $arrayResp[$cont_grup]["grup_desc_centro_costo"] = $grup_desc_centro_costo;

                    $this->objParam1 = new CTParametro(null, null, null);
                    $this->objParam1->addParametro('id_presupuesto', $value_det["id_centro_costo"]);
                    $this->objParam1->addParametro('id_partida', $value_det["id_partida"]);
                    $this->objParam1->addParametro('id_moneda', $id_moneda);

                    $this->objFunc1 = $this->create('sis_presupuestos/MODPresupuesto');
                    $resultSolicitud1 = $this->objFunc1->capturaPresupuesto();

                    $arrayResp[$cont_grup]["captura_presupuesto"] = $resultSolicitud1->datos["captura_presupuesto"];
                    $cont_grup++;


                    if ($resultSolicitud->getTipo() == 'ERROR') {

                        $resultSolicitud->imprimirRespuesta($resultSolicitud->generarMensajeJson());
                        exit;
                    }


                }

            }

            return $arrayResp;

        } else
            return array();
    }

}

?>
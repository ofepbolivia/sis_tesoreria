<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';
require_once dirname(__FILE__).'/../../pxp/pxpReport/Report.php';
class CustomObpReport extends ReportePDFFormulario{

    private $dataSource;
    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function Header() {
        $height = 20;

        $this->Cell(40, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(16);
        $this->SetFont('','B');
        $this->Cell(105, $height, 'OBLIGACIÓN DE PAGO', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->firmar();
    }

}


Class RSolicitudCompraObp extends Report {
    var $objParam;
    function __construct(CTParametro $objParam) {
        $this->objParam = $objParam;
    }
    function write() {

        $pdf = new CustomObpReport($this->objParam);
        $pdf->setDataSource($this->getDataSource());

        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->firma['datos_documento']['numero'] = $this->getDataSource()->getParameter('numero');
        $pdf->firma['datos_documento']['numero_tramite'] = $this->getDataSource()->getParameter('num_tramite');
        $pdf->firma['datos_documento']['tipo'] = $this->getDataSource()->getParameter('tipo');
        $pdf->firma['datos_documento']['justificacion'] = $this->getDataSource()->getParameter('justificacion');

        $pdf->AddPage();

        $height = 5;
        $width2 = 40;
        $width3 = 30;
        $width4 = 25;
        $width5 = 75;

        $pdf->SetFontSize(8.5);
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(0,0,0);

        $fecha_solicitud = $this->getDataSource()->getParameter('fecha_soli');

        $pdf->Cell($width3, $height, 'Nro Tramite             ', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+18, $height, 'Fecha de Solicitud', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, 'Moneda', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, 'Gestion', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln();

        $pdf->SetFont('', '');

        $pdf->Cell($width3-2, $height, $this->getDataSource()->getParameter('num_tramite'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+18, $height, date_format(date_create($fecha_solicitud), 'd-m-Y'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-2, $height, $this->getDataSource()->getParameter('desc_moneda'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-2, $height, $this->getDataSource()->getParameter('desc_gestion'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln();
        $pdf->Ln();

        $white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));

        $pdf->SetFont('', 'B');

        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Gerente:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->Cell($width4+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario_apro'), $white, 0, 'L', true, '', 0, false, 'T', 'C');

        $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->Cell($width4, $height, 'Funcionario:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->Cell($width4+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario'), $white, 0, 'L', true, '', 0, false, 'T', 'C');
        $pdf->Ln();


        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Unidad Solicitante:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->MultiCell($width4+$width2, $height, $this->getDataSource()->getParameter('desc_uo'), 0,'L', true ,0);


    // caso funcionario AI
        if($this->getDataSource()->getParameter('nombre_usuario_ai')!= ''&&$this->getDataSource()->getParameter('nombre_usuario_ai')!= 'NULL'){
            $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->SetFont('', 'B');
            $pdf->Cell($width4, $height, 'Funcionario AI:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->SetFont('', '');
            $pdf->SetFillColor(192,192,192, true);
            $pdf->MultiCell($width4+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario'), 1,'L', true ,1);
        }
        $pdf->Ln();

        //imprime el detalle de la solicitud

        $this->writeDetalles($this->getDataSource()->getParameter('detalleDataSource'), $pdf);

        //imprime el pie del reporte
        $pdf->setTextColor(0,0,0);
        $pdf->SetFontSize(8);
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Justificación', 0, 0, 'L', false, '', 1, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->MultiCell($width5*2, $height, $this->getDataSource()->getParameter('justificacion'), 0,'L', false ,0);
        $pdf->Ln();
        $res =$pdf->firma;

        $pdf->Output($pdf->url_archivo, 'F');
        return $res;
    }

    function writeDetalles (DataSource $dataSource, TCPDF $pdf)
    {

        $pdf->setTextColor(0, 0, 0);
        $pdf->setFont('', 'B');
        $pdf->setFont('', '');
        //cambia el color de lienas
        $pdf->SetDrawColor(0, -1, -1, -1, false, '');


        $width1 = 15;
        $width2 = 25;
        $width3 = 20;

        $height = 5;
        $pdf->Ln();


        $conf_par_tablewidths = array($width2, $width2 * 2, $width2 * 2 + 15, $width1 + $width2);
        $conf_par_tablealigns = array('L', 'L', 'L', 'R');
        $conf_par_tablenumbers = array(0, 0, 0, 0);
        $conf_tableborders = array();
        $conf_tabletextcolor = array();

        $conf_par_tabletextcolor_rojo = array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 0), array(255, 0, 0));
        $conf_par_tabletextcolor_verde = array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 0), array(35, 142, 35));


        $conf_det_tablewidths = array($width2 + $width1, $width2 + 25 + $width3 * 2, $width1, $width3, $width3);
        $conf_det_tablealigns = array('L', 'L', 'L', 'R', 'R');
        $conf_det_tablenumbers = array(0, 0, 0, 0, 0);


        $conf_det2_tablewidths = array($width2 + $width1, $width2 + 25 + $width3 * 2, $width1, $width3, $width3);
        $conf_det2_tablealigns = array('L', 'L', 'L', 'R', 'R');
        $conf_det2_tablenumbers = array(0, 0, 0, 2, 2);


        $conf_tp_tablewidths = array($width2 + $width1 + $width2 + 25 + ($width3 * 2) + $width1 + $width3, $width3);
        $conf_tp_tablealigns = array('R', 'R');
        $conf_tp_tablenumbers = array(0, 2);
        $conf_tp_tableborders = array(0, 1);

        $total_solicitud = 0;
        $count_partidas = 0;

        foreach ($dataSource->getDataset() as $row) {


            $pdf->tablewidths = $conf_par_tablewidths;
            $pdf->tablealigns = $conf_par_tablealigns;
            $pdf->tablenumbers = $conf_par_tablenumbers;
            $pdf->tableborders = $conf_tableborders;
            $pdf->tabletextcolor = $conf_tabletextcolor;


            $RowArray = array(
                'codigo_partida' => 'Código Partida',
                'nombre_partida' => 'Nombre Partida',
                'desc_centro_costo' => 'Centro de Costo',
                'ejecutado' => 'Presupuesto'
            );

            $pdf->MultiRow($RowArray, false, 0);

            //chequear disponibilidad

            $estado_sin_presupuesto = array("borrador", "registrado", "vbpoa", "suppresu", "vbpresupuestos", "vbgaf", "vobogerencia");

            if (in_array($this->getDataSource()->getParameter('estado'), $estado_sin_presupuesto)) {
                //verifica la disponibilidad de presupeusto para el  agrupador
                if ($row['presu_verificado'] == "true") {
                    $disponibilida = 'DISPONIBLE';
                    $pdf->tabletextcolor = $conf_tabletextcolor;
                } else {
                    $disponibilida = 'NO DISPONIBLE';
                    $pdf->tabletextcolor = $conf_par_tabletextcolor_rojo;
                }
            } else {
                $disponibilida = 'DISPONIBLE Y APROBADO';
                $pdf->tabletextcolor = $conf_par_tabletextcolor_verde;
            }
            if ($this->getDataSource()->getParameter('sw_cat') == 'si') {
                $descCentroCosto = 'Cat. Prog.: ' . $row['groupeddata'][0]['codigo_categoria'] . "\n" . $row['grup_desc_centro_costo'];
            } else {
                $descCentroCosto = $row['grup_desc_centro_costo'];
            }

            // din chequeo disponibilidad
            $RowArray = array(
                'codigo_partida' => $row['groupeddata'][0]['codigo_partida'],
                'nombre_partida' => $row['groupeddata'][0]['nom_partida'],
                'desc_centro_costo' => $descCentroCosto,
                'ejecutado' => $disponibilida
            );

            $pdf->MultiRow($RowArray, false, 0);

            /////////////////////////////////
            //agregar detalle de la solicitud
            //////////////////////////////////

            $pdf->tablewidths = $conf_det_tablewidths;
            $pdf->tablealigns = $conf_det_tablealigns;
            $pdf->tablenumbers = $conf_det_tablenumbers;
            $pdf->tableborders = $conf_tableborders;
            $pdf->tabletextcolor = $conf_tabletextcolor;

            $table = '<table border="1" style="font-size: 7pt; color: black;">
                        <tr>
                            <th width="34%" align="center"><b>Concepto Gasto</b></th>
                            <th width="50%" align="center"><b>Descripción</b></th>
                            <th width="15%" align="center"><b>Precio Total</b></th>
                        </tr>
                        ';

            $totalRef = 0;

            $pdf->tablewidths = $conf_det2_tablewidths;
            $pdf->tablealigns = $conf_det2_tablealigns;
            $pdf->tablenumbers = $conf_det2_tablenumbers;
            $pdf->tableborders = $conf_tableborders;


            foreach ($row['groupeddata'] as $solicitudDetalle) {

                $table .= '<tr>
                            <td style="text-align: justify;">' . $solicitudDetalle['nombre_ingas'] . '</td>
                            <td>' . stripcslashes(nl2br(htmlentities($solicitudDetalle['descripcion']))) . '</td>                                                        
                            <td style="text-align: right;">' . number_format($solicitudDetalle['monto_pago_mo'], 2, ',', '.') . '</td>
                         </tr>
                        ';

                $totalRef = $totalRef + $solicitudDetalle['monto_pago_mo'];
            }
            //coloca el total de la partida
            $pdf->tablewidths = $conf_tp_tablewidths;
            $pdf->tablealigns = $conf_tp_tablealigns;
            $pdf->tablenumbers = $conf_tp_tablenumbers;
            $pdf->tableborders = $conf_tp_tableborders;


            $saldo_comprometer = (double)$row['captura_presupuesto'];

            /*if ($saldo_comprometer < 0) {
                $dif = $saldo_comprometer + $totalRef;
            } else {*/
                $dif = $saldo_comprometer - $totalRef;
            //}

            $table .= '<tr>
                            <td align="center"><b>TOTAL</b></td>                            
                            <td style="font-weight: bold; text-align: right;">(' . $this->getDataSource()->getParameter('desc_moneda') . ')</td>
                            <td style="text-align: right; font-weight: bold">' . number_format($totalRef, 2, ',', '.') . '</td>
                     </tr>';
            if ($disponibilida == "NO DISPONIBLE") {
                $table .= '                     
                         <tr>
                                <td align="center"></td>
                                <td style="text-align: right; color:red;">Saldo Disponible</td>
                                <td style="text-align: right; color:red;">' . number_format($saldo_comprometer, 2, ',', '.') . '</td>                                
                         </tr>
                         <tr>
                                <td align="center"></td>
                                <td style="text-align: right; color:red;">Diferencia</td>
                                <td style="text-align: right; color:red;">' . number_format($dif, 2, ',', '.') . '</td>                                                                
                         </tr>';
            }


            $table .= '</table>';
            $pdf->writeHTML($table);
            $total_solicitud = $total_solicitud + $totalRef;
            $count_partidas = $count_partidas + 1;
            $pdf->Ln();

        }

        //coloca el gran total de la solicitu

        if ($count_partidas > 1) {
            $pdf->tablewidths = $conf_tp_tablewidths;
            $pdf->tablealigns = $conf_tp_tablealigns;
            $pdf->tablenumbers = $conf_tp_tablenumbers;
            $pdf->tableborders = array(0, 0);

            $RowArray = array(
                'precio_unitario' => 'Total Solcitud (' . $this->getDataSource()->getParameter('desc_moneda') . ')',
                'precio_total' => $total_solicitud
            );

            $pdf->MultiRow($RowArray, false, 1);
            $pdf->Ln();
            $pdf->Ln();

        }
    }

}
?>

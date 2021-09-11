<?php
// Extend the TCPDF class to create custom MultiRow

require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';

class RMemoCajaChicaPdf extends  ReportePDF {
    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $total;

    function datosHeader ( $detalle, $totales) {

        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->datos_detalle = $detalle;
        $this->datos_titulo = $totales;
        $this->subtotal = 0;
        $this->SetMargins(15, 35, 5);
    }

    function Header() {

        $titulo1='<br><b>MEMORÁNDUM</b>';
        $newDate = $this->datos_detalle[0]['fecha'] != null? date_format(date_create($this->datos_detalle[0]['fecha']),'d/m/Y'):'';
        $dataSource = $this->datos_detalle;
        ob_start();
        include(dirname(__FILE__).'/../reportes/formularios/cabeceraAsig.php');

        $content = ob_get_clean();
        $this->writeHTML($content, true, false, true, false, '');

    }

    function generarReporte() {
        // get the HTML
        $dataSource = $this->datos_detalle;
        ob_start();
        $genero = 'Señor ';
        $desig = 'designado ';
        $motivo = $dataSource[0]['motivo'];
        $cod_moneda = $dataSource[0]['codigo_mone'];
        $importe_literal = $dataSource[0]['importe_literal'];
        $importe = number_format($dataSource[0]['importe_cheque'], 2, ',', '.');
        $nro_cheque = $dataSource[0]['nro_cheque'];
        $num_memo = $dataSource[0]['codigo_mone'];
        $codigo =  $dataSource[0]['codigo'];
        $aprobador = $dataSource[0]['aprobador'];
        $QR = $this->codigoQr($aprobador, $dataSource[0]['cargo_aprobador'], $codigo);
        if($dataSource[0]['genero_solicitante'] == 'F'){
            $genero = 'Señora ';
            $desig='designada ';
        }


        $resolucion = 'N° 25/11';

// var_dump($fecha_sol, $resolucion);exit;
        include(dirname(__FILE__).'/../reportes/formularios/bodyAsig.php');
        $content = ob_get_clean();

        $this->AddPage();
        $this->writeHTML($content, true, false, true, false, '');
        $this->revisarfinPagina();
    }


    function codigoQr ($gerente, $cargo_gerente, $codigo){
        $cadena = ' '.$codigo.'  Aprobado por: '.$gerente."\n".'Cargo: '.$cargo_gerente."\n";
        $barcodeobj = new TCPDF2DBarcode($cadena, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png";

        return $url_archivo;
    }


    function revisarfinPagina(){
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case

        $startY = $this->GetY();
        $this->getNumLines($row['cell1data'], 80);

        //if (($startY + 10 * 6) + $dimensions['bm'] > ($dimensions['hk'])) {
        if (( $startY + $dimensions['bm'] )> ($dimensions['hk'])) {
            $this->AddPage();

        }
    }
    function Footer() {
        $this->Ln();
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
    }
}
?>

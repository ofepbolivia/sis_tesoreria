<?php

//include_once(dirname(__FILE__).'/../../lib/PHPWord/src/PhpWord/Autoloader.php');
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/PHPWord-master/src/PhpWord/Autoloader.php');
include_once (dirname(__FILE__).'/../../pxp/lib/phpdocx/Classes/Phpdocx/Create/CreateDocx.inc');
\PhpOffice\PhpWord\Autoloader::register();
Class RMemoCajaChica {
	
	private $dataSource;
    
    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;
    }
    
    public function getDataSource() {
        return $this->dataSource;
    }
   
    
    function write($fileName) {
    	
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$document = $phpWord->loadTemplate(dirname(__FILE__).'/template_memo_caja_chica.docx');
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        
        $numm = $this->getDataSource()->getParameter('num_memo');
        $cod = $this->getDataSource()->getParameter('codigo');
        $apro = $this->getDataSource()->getParameter('aprobador');
        $car_apro = $this->getDataSource()->getParameter('cargo_aprobador');
		$document->setValue('CITE', $this->getDataSource()->getParameter('num_memo')); // On section/content
		$document->setValue('FECHA', $this->getDataSource()->getParameter('fecha')); // On section/content
		$document->setValue('NUMEROCHEQUE', $this->getDataSource()->getParameter('nro_cheque')); // On section/content
		$document->setValue('CODIGO', $this->getDataSource()->getParameter('codigo')); // On section/content
		$document->setValue('APROBADOR', $this->getDataSource()->getParameter('aprobador')); // On section/content
		$document->setValue('CARGOAPROBADOR', $this->getDataSource()->getParameter('cargo_aprobador')); // On section/content
		$document->setValue('CAJERO', $this->getDataSource()->getParameter('cajero')); // On section/content
		$document->setValue('CARGOCAJERO', $this->getDataSource()->getParameter('cargo_cajero')); // On section/content
		$document->setValue('IMPORTECHEQUE', $this->getDataSource()->getParameter('importe_cheque')); // On section/content
		$document->setValue('IMPORTELITERAL', $this->getDataSource()->getParameter('importe_literal')); // On section/content
        $document->setValue('BANCO', 'BANCO UNION S.A.'); // On section/content
        $document->setValue('CODIGO_MONE', $this->getDataSource()->getParameter('codigo_mone'));
        ($apro == null || $apro == '')?$document->setValue('QR', ''): $document->setImg('QR', array('src' =>$this->generarImagen($numm, $cod, $apro, $car_apro), 'swh' => '100'));        
		$document->saveAs($fileName);
		        
    }
    
    function generarImagen($n, $c, $a, $c_a){
        $cadena = 'Aprobado por: '.$a."\n".'Cargo: '.$c_a."\n".'N° Memo: '.$n;         
        $barcodeobj = new TCPDF2DBarcode($cadena, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $c . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $c. ".png";

        return $url_archivo;
    }   
     
        
}
?>
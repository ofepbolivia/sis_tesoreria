<?php
class RepProcPago
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    public  $url_archivo;
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }
    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Procesos Pagos');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        $styleTitulos4 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );


        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'TRÁMITES EN PROCESO DE PAGO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:I2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Del: '.  $this->objParam->getParametro('fecha_ini').'   Al: '.  $this->objParam->getParametro('fecha_fin') );
        $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A4:I4');


        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleTitulos4);



        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);




        $this->docexcel->getActiveSheet()->getStyle('A6:J6')->getAlignment()->setWrapText(true);


        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A6','Nº');
        $this->docexcel->getActiveSheet()->setCellValue('B6','N° TRAMITE');
        $this->docexcel->getActiveSheet()->setCellValue('C6','PROVEEDOR');
        $this->docexcel->getActiveSheet()->setCellValue('D6','JUSTIFICACION');
        $this->docexcel->getActiveSheet()->setCellValue('E6','MONTO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','MONEDA');
        $this->docexcel->getActiveSheet()->setCellValue('G6','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('H6','NRO. CUOTA');
        $this->docexcel->getActiveSheet()->setCellValue('I6','FECHA TENTATIVA');
        $this->docexcel->getActiveSheet()->setCellValue('J6','DEPARTAMENTO');



    }
    function generarDatos()
    {
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $this->numero = 1;
        $fila = 7;
        $datos = $this->objParam->getParametro('datos');
        $this->imprimeCabecera(0);

        foreach ( $datos  as $value)
        {
            if ($value['estado'] != 'anulado') {
                if ($value['estado_pago'] != 'devengado' && $value['estado_pago'] != 'pagado'
                    && $value['estado_pago'] != 'contabilizado' && $value['estado_pago'] != 'devuelto'
                    && $value['estado_pago'] != 'anticipado'
                ) {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $this->numero);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['desc_proveedor']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['obs']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['moneda']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['fecha']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['nro_cuota']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['fecha_tentativa']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['nombre_depto']);

                    $this->docexcel->getActiveSheet()->getStyle("J$fila:J$fila")->applyFromArray($styleTitulos3);

                    $fila++;
                    $this->docexcel->getActiveSheet()->getStyle("E$fila:E$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                    $this->numero++;
                }
            }


        }

    }
    function generarReporte(){

        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);


    }
}
?>
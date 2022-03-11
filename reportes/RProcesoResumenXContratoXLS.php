<?php
/*
let info = {
dev: breydi vasquez
date: 10/12/2020
description: Reporte Resumen por Contrato
}
*/
class RProcesoResumenXContratoXLS
{
    private $docexcel;
    private $objWriter;
    private $hoja;
    private $columnas=array();
    private $numero;
    private $fila;
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
        $this->docexcel->getActiveSheet()->setTitle('Proceso con retencion');
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


        $styleTitulos2 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )

            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '0066CC'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
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

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'RESUMEN POR CONTRATO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:H2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'Del: '.  $this->objParam->getParametro('fecha_ini').'   Al: '.  $this->objParam->getParametro('fecha_fin') );
        $this->docexcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A4:H4');

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($styleTitulos2);



        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A5','N?');
        $this->docexcel->getActiveSheet()->setCellValue('B5','PROVEEDOR');
        $this->docexcel->getActiveSheet()->setCellValue('C5','NRO. DE CONTRATO');
        $this->docexcel->getActiveSheet()->setCellValue('D5','MONTO A PAGAR');
        $this->docexcel->getActiveSheet()->setCellValue('E5','MONTO RETENCION DE GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('F5','LIQUIDO PAGABLE');
        $this->docexcel->getActiveSheet()->setCellValue('G5','DEV. GARANTIA');
        $this->docexcel->getActiveSheet()->setCellValue('H5','PENDIENTE DE DEVOLUCION');

    }
    function generarDatos()
    {
        $styleTitulos3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleArrayTotalGral = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'AEB6BF')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );

        $this->numero = 1;
        $fila = 6;
        $datos = $this->objParam->getParametro('datos');
        $dataReal = json_decode($datos[0]['jsondata']);

        $this->imprimeCabecera(0);
        $sumatoria_monto = 0;
        $sumatoria_monto_retgar_mo = 0;
        $sumatoria_liquido_pagable = 0;
        $sumatoria_dev_garantia = 0;
        $sumatoria_total_devol = 0;
        // var_dump($dataReal->data);exit;
        foreach ($dataReal->data as $value)
        {

//            if ($value['monto_retgar_mo']!= 0) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $this->numero);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value->proveedor);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value->numero);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value->monto);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value->monto_retgar_mo);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value->liquido_pagable);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value->dev_garantia);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value->total_devol);

            $this->docexcel-> getActiveSheet () -> getStyle ( "D$fila:D$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel-> getActiveSheet () -> getStyle ( "E$fila:E$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel-> getActiveSheet () -> getStyle ( "F$fila:F$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel-> getActiveSheet () -> getStyle ( "G$fila:G$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel-> getActiveSheet () -> getStyle ( "H$fila:H$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $fila++;

            $this->numero++;
            $sumatoria_monto = $sumatoria_monto + round ($value->monto,2);
            $sumatoria_monto_retgar_mo = $sumatoria_monto_retgar_mo + round ($value->monto_retgar_mo,2);
            $sumatoria_liquido_pagable = $sumatoria_liquido_pagable + round ($value->liquido_pagable,2);
            $sumatoria_dev_garantia = $sumatoria_dev_garantia + round ($value->dev_garantia,2);
            $sumatoria_total_devol = $sumatoria_total_devol + round ($value->total_devol,2);


        }
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,($fila),'TOTAL GENERAL');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,($fila),$sumatoria_monto);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,($fila),$sumatoria_monto_retgar_mo);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,($fila),$sumatoria_liquido_pagable);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,($fila),$sumatoria_dev_garantia);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,($fila),$sumatoria_total_devol);
        $this->docexcel->getActiveSheet()->getStyle("A".($fila).":H".($fila))->applyFromArray($styleArrayTotalGral);
        $this->docexcel-> getActiveSheet () -> getStyle ( "D$fila:D$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel-> getActiveSheet () -> getStyle ( "E$fila:E$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel-> getActiveSheet () -> getStyle ( "F$fila:F$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel-> getActiveSheet () -> getStyle ( "G$fila:G$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel-> getActiveSheet () -> getStyle ( "H$fila:H$fila") -> getNumberFormat () -> setFormatCode (PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        //var_dump($this->objParam); exit;
    }
    function generarReporte(){

        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);


    }
}
?>

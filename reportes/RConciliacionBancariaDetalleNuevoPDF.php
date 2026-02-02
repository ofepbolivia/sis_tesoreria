<?php
/**
 * Reporte de Actualización por Fecha
 * Autor: Nes
 * Formato: PDF (TCPDF estándar de KERP)
 */

class RConciliacionBancariaDetalleNuevoPDF extends ReportePDF {

    private $datos;

    public function setDatos($datos) {
        $this->datos = $datos;

        // var_dump($this->datos);
        // die;
    }

    public function Header() {

        $ruta_imagen = dirname(__FILE__) . '/../../lib/' . $_SESSION['_DIR_LOGO'];

        $periodo_num = $this->datos[0]['periodo'] ?? '';
        $gestion = $this->datos[0]['gestion'] ?? '';

        //var_dump($periodo_num, $gestion);

        $meses = [
            1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
            5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
            9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
        ];

        $mes_literal = $meses[(int)$periodo_num] ?? '';

        $html = '
        <table border="1" cellspacing="0" cellpadding="1" width="100%">
            <tr>
                <td width="20%" align="center">
                    <img src="'.$ruta_imagen.'" width="80">
                </td>
                <td width="60%" align="center" style="font-weight:bold;font-size:10pt;">
                    Detalle de Transferencia / Cheques no Efectivizados<br>
                    Periodo: '.$mes_literal.' '.$gestion.'
                </td>
                <td width="20%"></td>
            </tr>
        </table>';

        $this->writeHTML($html, true, false, true, false, '');
        $this->Ln(2);
    }

    public function generarReporte() {

        $this->AddPage();
        $this->SetFont('', 'B', 6);
        $this->SetFillColor(220,220,220);

        // Anchos corregidos → total = 190 mm
        $w = [
            10,  // Nro
            25,  // Documento
            20,  // Importe Junio
            20,  // Importe Mayo
            20,  // Importe Abril
            15,  // Estado
            15,  // Detalle
            22,  // Observación
            19,  // Fecha
            15  // Fecha creación
        ];

        // Encabezado
        $titulos = [
            'Nro.', 'Nro Documento Gasto', 'Beneficiario', 'Importe','Estado','Detalle','Observación',
            'Fecha','Fecha Creación','Creado Por'
        ];

        foreach($titulos as $i=>$t){
            $this->Cell($w[$i], 6, $t, 1, 0, 'C', true);
        }
        $this->Ln();

        $this->SetFont('', '', 6);
        $contador = 1;

        

        foreach ($this->datos as $row) {

            $suma_periodos = $row['periodo_1'] + $row['periodo_2'] + $row['periodo_3'];
            $this->Cell($w[0], 6, $contador, 1);
            $this->Cell($w[1], 6, $row['nro_cheque'] ?? '', 1);
            $this->Cell($w[2], 6, $row['beneficiario'] ?? '', 1);
            $this->Cell($w[3], 6, $suma_periodos ?? '', 1);
            $this->Cell($w[4], 6, $row['estado'] ?? '', 1);
            $this->Cell($w[5], 6, $row['detalle'] ?? '', 1);
            $this->Cell($w[6], 6, $row['observacion'] ?? '', 1);
            $this->Cell($w[7], 6, $row['fecha'] ?? '', 1);
            $this->Cell($w[8], 6, substr($row['fecha_reg'], 0, 10), 1);
            $this->Cell($w[9], 6, $row['usr_reg'] ?? '', 1);

            $this->Ln();
            $contador++;
        }
    }
}
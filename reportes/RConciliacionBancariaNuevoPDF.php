<?php
/**
 * Reporte de Actualización por Fecha
 * Autor: Nes
 * Formato: PDF (TCPDF estándar de KERP)
 */

class RConciliacionBancariaNuevoPDF extends ReportePDF {
    
    private $datos;
    private $fecha_inicio;
    private $fecha_fin;

    public function setDatos($datos) {
        $this->datos = $datos;

        // var_dump($this->datos);
        // die;
    }

        public function Header() {

        $ruta_imagen = dirname(__FILE__) . '/../../lib/' . $_SESSION['_DIR_LOGO'];

         $periodo_num = $this->datos[0]['periodo'] ?? '';
         $gestion = $this->datos[0]['gestion'] ?? '';

    // Convertimos número de periodo a mes literal
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $mes_literal = $meses[(int)$periodo_num] ?? 'Desconocido';

    $html = '
            <table border="1" cellspacing="0" cellpadding="1" width="100%">
                <tr>
                    <td width="20%" align="center" valign="middle">
                        <table border="0" cellpadding="4" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <img src="' . $ruta_imagen . '" width="100" />
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="60%" align="center" valign="middle" style="font-weight: bold; font-size: 12pt;">
                        Detalle de Conciliación <br>
                        Periodo: ' . $mes_literal . ' ' . $gestion . '
                    </td>
                    <td width="20%" valign="middle">
                    </td>
                </tr>
            </table>';

        // 3. Imprimir
        $this->writeHTML($html, true, false, true, false, '');
        $this->Ln(2);
    }


    public function generarReporte() {

        $this->AddPage();
        $this->SetFont('', '', 10);

        $this->SetFont('', 'B', 9);
        $this->SetFillColor(220, 220, 220);

        // Encabezado de tabla
        $this->Cell(40, 6, 'Tipo', 1, 0, 'C', true);
        $this->Cell(30, 6, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 6, 'Nro. Comprobante', 1, 0, 'C', true);
        $this->Cell(40, 6, 'Importe', 1, 0, 'C', true);
        
        // CAMBIO 1: El último Cell del encabezado debe tener '1' para hacer un salto de línea
        $this->Cell(30, 6, 'Concepto', 1, 1, 'C', true); 

        $this->SetFont('', '', 7);

        foreach ($this->datos as $row) {
            $this->Cell(40, 6, $row['tipo'] ?? '', 1, 0, 'L');
            $this->Cell(30, 6, $row['fecha'] ?? '', 1, 0, 'L');
            $this->Cell(40, 6, $row['nro_comprobante'] ?? '', 1, 0, 'L');
            $this->Cell(40, 6, $row['importe'] ?? '', 1, 0, 'L');
            
            // CAMBIO 2: El último Cell de cada fila de datos debe tener '1' para hacer un salto de línea
            $this->Cell(30, 6, $row['concepto'] ?? '', 1, 1, 'L'); 
        }
    }
}

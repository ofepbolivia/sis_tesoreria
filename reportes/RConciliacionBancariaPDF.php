<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RConciliacionBancariaPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;		   

    function Header(){
        $this->Ln(3);
		
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);		
        $this->ln(3);		
        $this->SetMargins(15, 40, 5);        
        $this->SetFont('','B',11);		
        $this->Cell(215,5,"BOLIVIANA DE AVIACION",0,1,'C');
        $this->Cell(215,8,"CONCILIACIÓN BANCARIA",0,1,'C');
		foreach ($this->datos1 as $value) {													
				$nro_cuenta = $value['nro_cuenta'];
				$banco = $value['nombre_institucion'];
				$moneda = $value['moneda'];
				$denominacion = $value['denominacion'];			
		}
		foreach ($this->datos1 as $value) {			
				$periodo = $value['periodo'];
				$gestion = $value['gestion'];							
		}								
		$height = 2;
		$witdh  = 30;
		$this->Ln(5);
		$this->SetFont('','B',10);								
        $this->Cell($witdh, $height, 'Banco: ', 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(80, $height, $banco, 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Ln();
        $this->Cell($witdh, $height, 'Periodo o mes: ', 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(15, $height, $periodo.' de '.$gestion, 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Ln();
		$this->Cell($witdh, $height, 'Nro. Cta.Cte: ', 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell($witdh, $height, $nro_cuenta, 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(80, $height, 'Moneda: '.$moneda, 0, 0, 'R', false, '', 0, false, 'T', 'C');	
		$this->Ln();
		$this->Cell($witdh, $height, 'Denominación', 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(80, $height, $denominacion, 0, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Ln();											
		
    }
    function setDatos($datos,$datos1) {

        $this->datos = $datos;
		$this->datos1 = $datos1;
        //var_dump($this->datos1);exit;
    }	
	function generarReporte () {
		
        $this->AddPage();        
        $this->SetMargins(12, 40, 12,true);        
		$this->Ln(8);
		$this->SetFont('','',9);
		$sum_debito = 0;
		$sum_abono = 0;
		$sum_cheque = 0;
		$sum_deposito = 0;
		$saldo_erp = 0;
		$total_sal_libro = 0;
		$n = 0;
		$n1 = 0;
		$n2 = 0;
		$n3 = 0;
		$cheque = 0;
		$deposito = 0;
		
	foreach ($this->datos as $value) {					
			$n1 +=1;
			$sum_3ra_tabla += $value['saldo'];			
	}
	foreach ($this->datos1 as $value) {
		$value['tipo']=='cheque' && $cheque += $value['importe'];
		$value['tipo']=='deposito' && $deposito += $value['importe'];
		$value['tipo']=='transito' && $transito += $value['importe'];			
		$value['tipo']=='cheque' && $n += 1; 
		$value['tipo']=='deposito' && $n3 +=1;
		$value['tipo']=='transito' && $n2 +=1;
		$saldo_ext_banca = $value['sal_ext_ban'];
		$saldo_erp = $value['saldo'];
		$fecha_elaboracion = $value['fecha'];
		$observa = $value['observaciones'];
		$fun_elba = $value['fun_elab'];
		$fun_vb = $value['fun_vb'];			
	}
	if(count($this->datos1)<=1 and count($this->datos1)>0 ){
		$value['tipo']!='cheque' && $n = 4; 
		$value['tipo']!='deposito' && $n3 = 4;
		$value['tipo']!='transito' && $n2 = 4;		
	}
	$t1 = 0;
	$t2 = 0;
	$t3 = 0;
	$t4 = 0;
	foreach ($this->datos1 as $value) {
		$value['tipo']=='cheque' && $t1 +=1;
		$value['tipo']=='deposito' && $t2 +=1;
		$value['tipo']=='transito' && $t4 +=1;
	}
	
	$saldo_real1 = $saldo_ext_banca - $cheque + $deposito;
	$saldo_real2 = $saldo_erp - $sum_3ra_tabla + $transito;				
	$con = 3 + $n;
	$con1 = 3 + $n1;
	$con2 = 2 + $n2;
	$con3 = 2 + $n3;
	$fecha_table = explode('-', $fecha_elaboracion);
	$dia = $fecha_table[2];
	$mes = $fecha_table[1];
	$anio = $fecha_table[0];		
			
$html = '<table border="1" cellpadding="2">
<tr>
<table border="1" cellpadding="2">
 <tr align="center">
  <th colspan="4" width="600">CONCEPTOS</th>
  <th width="80">IMPORTE</th>
 </tr>
 <tr>
  <td colspan="4" width="600" align="left"><b>1. SALDO SEGUN LIBROS</b><br />
  <b>Memos: Debitos registrados por el banco y no Cotabilizados:</b></td>
  <td width="80" align="right" rowspan="'.$con.'"><b>'.number_format($saldo_ext_banca,2,',','.').'<br>'.number_format($cheque,2,',','.').'</b></td>
 </tr>  
 <tr align="center">
  <td width="80"><b>Fecha</b></td>
  <td width="340"><b>CONCEPTO</b></td>
  <td width="90"><b>Compbte.No.</b></td>
  <td width="90"><b>Importe</b></td>
 </tr>
 ';
 if($this->datos1[0]['tipo']=='cheque' && $this->datos1[0]['tipo']!=null){
   foreach ($this->datos1 as $row){
	  	if ($row['tipo']=='cheque'){
	        $html.='<tr align="center" style="font-size:6;">
	        <td>'.$row['fecha_reg'].'</td>
	        <td align="left" >'.$row['concepto'].'</td>
	        <td>'.$row['nro_comprobante'].'</td>
	        <td align="right">'.number_format($row['importe'],2,',','.').'</td>
	        </tr>';
			$sum_debito += $row['importe'];		
			}
    	}
    }else{
    	for ($i=0; $i < 4; $i++) { 
		$html.='<tr align="center" style="font-size:6;">
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>			        
			        </tr>';			
		}
    }
 $html.='<tr align="center">
   <td colspan="3">TOTAL</td>
   <td align="right">'.number_format($sum_debito,2,',','.').'</td>
 </tr>
 <tr>
  <td colspan="4"><b>Más: Abonos resitrados por el Banco y no Contabilizados:</b></td>
  <td align="right"><b>'.number_format($deposito,2,',','.').'</b></td>    
 </tr>
 <tr align="center">
  <td width="80"><b>Fecha</b></td>
  <td width="340"><b>CONCEPTO</b></td>
  <td width="90"><b>Compbte.No.</b></td>
  <td width="90"><b>Importe</b></td>
  <td width="80" align="right" rowspan="'.$con3.'"></td>
 </tr>'; 
  if($this->datos1[0]['tipo']=='deposito' && $this->datos1[0]['tipo']!=null){
   foreach ($this->datos1 as $row){
	  	if ($row['tipo']=='deposito'){
	        $html.='<tr align="center" style="font-size:6;">
	        <td>'.$row['fecha_reg'].'</td>
	        <td align="left" >'.$row['concepto'].'</td>
	        <td>'.$row['nro_comprobante'].'</td>
	        <td align="right">'.number_format($row['importe'],2,',','.').'</td>
	        </tr>';
			$sum_abono += $row['importe'];		
			}
	    }
    }else{
    	for ($i=0; $i < 4; $i++) { 
		$html.='<tr align="center" style="font-size:6;">
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>			        
			        </tr>';			
		}
    }     
 $html.='<tr align="center">
   <td colspan="3">TOTAL</td>
   <td align="right">'.number_format($sum_abono,2,',','.').'</td>
 </tr>
 <tr align="center">
   <td colspan="4"><b>SALDO REAL</b></td>
   <td align="right"><b>'.number_format($saldo_real1,2,',','.').'</b></td>
 </tr> 
 <tr>
  <td colspan="4" width="600" align="left"><b>2. SALDO SEGUN EXTRACTO BANCARIO:</b><br />
  <b>Memos: Cheques Girados y no Cobrados(</b> y Otros conceptos no registrados por el Banco<b>):</b></td>
  <td width="80" align="right" rowspan="'.$con1.'"><b>'.number_format($saldo_erp,2,',','.').'<br>'.number_format($sum_3ra_tabla,2,',','.').'</b></td>
 </tr>  
 <tr nobr="true" align="center">
  <td width="80"><b>Fecha</b></td>
  <td width="340"><b>CONCEPTO</b></td>
  <td width="90"><b>Cheque No.</b></td>
  <td width="90"><b>Importe</b></td>
 </tr>';
   foreach ($this->datos as $row){  	
        $html.='<tr align="center" style="font-size:6;">
        <td>'.$row['fecha'].'</td>
        <td align="left" >'.$row['concepto'].'</td>
        <td>'.$row['nro_cheque'].'</td>
        <td align="right">'.number_format($row['saldo'],2,',','.').'</td>
        </tr>';
		$sum_cheque += $row['saldo'];		
    } 
 $html.='<tr align="center">
   <td colspan="3">TOTAL</td>
   <td align="right">'.number_format($sum_cheque,2,',','.').'</td>
 </tr>  
   <tr>
  <td colspan="4"><b>Más: Depósitos y otros conceptos no Registrados por el Banco:</b></td>
  <td align="right"><b>'.number_format($transito,2,',','.').'</b></td>  
 </tr>
 <tr align="center">
  <td width="80"><b>Fecha</b></td>
  <td width="340"><b>CONCEPTO</b></td>
  <td width="90"><b>Compbte.No.</b></td>
  <td width="90"><b>Importe</b></td>
  <td width="80" rowspan="'.$con2.'"></td>
 </tr>';
 if($this->datos1[0]['tipo']=='transito' && $this->datos1[0]['tipo']!=null){
    foreach ($this->datos1 as $row){    	    
	  	if ($row['tipo']=='transito'){
	        $html.='<tr align="center" style="font-size:6;">
	        <td>'.$row['fecha_reg'].'</td>
	        <td align="left" >'.$row['concepto'].'</td>
	        <td>'.$row['nro_comprobante'].'</td>
	        <td align="right">'.number_format($row['importe'],2,',','.').'</td>
	        </tr>';
			$sum_transito += $row['importe'];
			}
	    }
    }else{
    	for ($i=0; $i < 4; $i++) { 
		$html.='<tr align="center" style="font-size:6;">
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>			        
			        </tr>';			
		}
    } 		    
 $html.=' 
 <tr align="center">
   <td colspan="3">TOTAL</td>
   <td align="right">'.number_format($sum_transito,2,',','.').'</td>
 </tr>
 <tr align="center">
   <td colspan="4"><b>SALDO REAL</b></td>
   <td align="right"><b>'.number_format($saldo_real2,2,',','.').'</b></td>
 </tr>
  <tr>
    <td colspan="1" width="150" ><br/><br/>
      <table border="1" style="border-right:none;">
       <tr align="center">
        <th colspan="3"><b>Fecha de Elaboración</b></th>         
       </tr>        
        <tr align="center">
          <td><b>DIA</b></td>
          <td><b>MES</b></td>
          <td><b>AÑO</b></td>
        </tr>
        <tr align="center">
          <td>'.$dia.'</td>
          <td>'.$mes.'</td>
          <td>'.$anio.'</td>
        </tr>        
      </table><br/>
    </td>
    <td colspan="1" width="270"><b>Elaborado por:</b> <br>'.$fun_elba.'</td>
    <td colspan="2"  width="260"><b>Vo.Bo: </b> <br>'. $fun_vb.'</td>
  </tr>
  <tr>
    <td colspan="4"><b>OBSERVACIONES: </b>'. $observa.'</td>
  </tr>
</table>
</tr>
</table>
';
$this->writeHTML($html,true, false, false, false, '');
	$diff = $saldo_real1 - $saldo_real2; 
	if($diff!=0){
		$this->SetFillColor(224, 235, 100);
		$this->SetTextColor(200,0,0);
		$this->SetFont('','B',12);
		$this->Cell(20, 5, 'DIFERENCIA DE SALDOS REALES: '.number_format($diff,2,',','.'), 0, 0, 'L', false, '', 0, false, 'T', 'C');
	 }
    }	
}
?>
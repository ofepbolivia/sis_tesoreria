<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  rcm
*@date 24-06-2103
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
var main;
Phx.vista.PeriodoTes = {
	require:'../../../sis_parametros/vista/periodo_subsistema/PeriodoSubsistema.php',
	requireclase:'Phx.vista.PeriodoSubsistema',
	title:'Estado de Periodos de Tesorería', //fRnk: a) HR01765-2024
    title2:'',
	codSist: 'TES',
	bdel: false,
	bedit: false,
	bnew: false,
	
	constructor: function(config) {
        main=this;
       	Phx.vista.PeriodoTes.superclass.constructor.call(this,config);
		this.init();
		Ext.apply(this.store.baseParams,{codSist: this.codSist});
		this.load({params:{start:0, limit:50}});
        //fRnk: a) HR01765-2024
        document.getElementsByClassName('x-btn-split')[0].addEventListener('click', function (){
            var g=document.getElementsByClassName('fkgestion')[0].value;
            main.title2=g.length>4?'':'Gestión '+g;
        });
	},
    codReporte:'S/C',
	codSist:'TES',
	pdfOrientacion:'L'
};
</script>

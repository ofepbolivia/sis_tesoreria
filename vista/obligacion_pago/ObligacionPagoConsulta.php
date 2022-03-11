<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (fprudencio)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite 
*dar el visto a solicitudes de compra
*
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ObligacionPagoConsulta = {
    bedit:false,
    bnew:false,
    bsave:false,
    bdel:false,
    bgantt: true,
	require:'../../../sis_tesoreria/vista/obligacion_pago/ObligacionPago.php',
	requireclase:'Phx.vista.ObligacionPago',
	title:'Obligacion de Pago (Consulta)',
	nombreVista: 'ObligacionPagoConsulta',
	/*
	 *  Interface heredada para el sistema consulta de obligaciones de pago
	 *  solo para el ver el estado de los diferente procesos
	 * 
	 * */
	
	constructor: function(config) {
        this.tbarItems = ['-',
            'Gestión:',this.cmbGestion,'-'
        ];
        var fecha = new Date();
        Ext.Ajax.request({
            url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
            params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
            success:function(resp){
                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                this.cmbGestion.setRawValue(fecha.getFullYear());
                this.store.baseParams.id_gestion=reg.ROOT.datos.id_gestion;
                this.load({params:{start:0, limit:this.tam_pag}});
            },
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });

	    Phx.vista.ObligacionPagoConsulta.superclass.constructor.call(this,config);

        this.addButtonIndex(4,'docCargaSigep',{
            text:'Cert. SIGEP',
            grupo:[0,1,2],
            iconCls: 'brenew',
            disabled: true,
            handler: this.onCargarDocumentoSigep,
            tooltip: '<b>Registro Preventivo SIGEP</b><br/>Información para los comprobantes que ya tienen Preventivo en el SIGEP.'
        });

        this.getBoton('ini_estado').setVisible(false);
        this.getBoton('ant_estado').setVisible(false);
        this.getBoton('fin_registro').setVisible(false);
        this.cmbGestion.on('select',this.capturarEventos, this);
    },

    capturarEventos: function () {
        this.store.baseParams.id_gestion=this.cmbGestion.getValue();

        this.load({params:{start:0, limit:this.tam_pag}});
    },

    cmbGestion: new Ext.form.ComboBox({
        name: 'gestion',
        id: 'gestion_rev',
        fieldLabel: 'Gestion',
        allowBlank: true,
        emptyText:'Gestion...',
        blankText: 'Año',
        store:new Ext.data.JsonStore(
            {
                url: '../../sis_parametros/control/Gestion/listarGestion',
                id: 'id_gestion',
                root: 'datos',
                sortInfo:{
                    field: 'gestion',
                    direction: 'DESC'
                },
                totalProperty: 'total',
                fields: ['id_gestion','gestion'],
                // turn on remote sorting
                remoteSort: true,
                baseParams:{par_filtro:'gestion'}
            }),
        valueField: 'id_gestion',
        triggerAction: 'all',
        displayField: 'gestion',
        hiddenName: 'id_gestion',
        mode:'remote',
        pageSize:50,
        queryDelay:500,
        listWidth:'280',
        hidden:false,
        width:80
    }),

    preparaMenu:function(n){
         	var data = this.getSelectedData();
         	var tb =this.tbar;
          
            Phx.vista.ObligacionPago.superclass.preparaMenu.call(this,n); 
          	this.getBoton('fin_registro').disable();
          	this.getBoton('ant_estado').disable();
	        this.getBoton('reporte_com_ejec_pag').enable();
	        this.getBoton('reporte_plan_pago').enable();
			this.getBoton('diagrama_gantt').enable();
			this.getBoton('btnChequeoDocumentosWf').enable();
			this.getBoton('ajustes').disable();
			this.getBoton('est_anticipo').disable();

			// may habilita el boton Extension Pagos
            this.getBoton('btnExtender').enable();
            
			//this.getBoton('extenderop').disable();
			this.TabPanelSouth.get(1).enable();
		    if(data.tipo_obligacion == 'adquisiciones'){
              this.getBoton('btnVerifPresup').disable();
              this.menuAdq.enable();
            }
            else{
              //RCM: menú de reportes de adquisiciones
              this.menuAdq.disable();
              //Habilita el reporte de disponibilidad si está en estado borrador
              if (data['estado'] == 'borrador'){
              	this.getBoton('btnVerifPresup').enable();
              } else{
              	//Inhabilita el reporte de disponibilidad
              	this.getBoton('btnVerifPresup').disable();
              }
            }
        //franklin.espinoza 15/10/2020
        this.getBoton('docCargaSigep').enable();
          
     },
     
     
     liberaMenu:function(){
        var tb = Phx.vista.ObligacionPago.superclass.liberaMenu.call(this);
        if(tb){
            this.getBoton('fin_registro').disable();
            this.getBoton('ant_estado').disable();
            this.getBoton('reporte_com_ejec_pag').disable();
			this.getBoton('reporte_plan_pago').disable();
			this.getBoton('diagrama_gantt').disable();
			this.getBoton('btnChequeoDocumentosWf').disable();
			this.getBoton('ajustes').disable();
			this.getBoton('est_anticipo').disable();
			//this.getBoton('extenderop').disable();
			
			//Inhabilita el reporte de disponibilidad
            this.getBoton('btnVerifPresup').disable();
        }
       this.TabPanelSouth.get(1).disable();
       
       //RCM: menú de reportes de adquisiciones
       this.menuAdq.disable();
         //franklin.espinoza 15/10/2020
         this.getBoton('docCargaSigep').disable();
       return tb
    }, 
    
   
    
     tabsouth:[
            { 
             url:'../../../sis_tesoreria/vista/obligacion_det/ObligacionDetConsulta.php',
             title:'Detalle', 
             height:'50%',
             cls:'ObligacionDetConsulta'
            },
            {
              //carga la interface de registro inicial  
              url:'../../../sis_tesoreria/vista/plan_pago/PlanPagoConsulta.php',
              title:'Plan de Pagos (Consulta)', 
              height:'50%',
              cls:'PlanPagoConsulta'
            }
    
       ]
    
};
</script>

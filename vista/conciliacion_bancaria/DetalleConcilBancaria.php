<?php
/**
 *@package pXP
 *@file    DetalleConcilBancaria.php
 *@author  BVP
 *@date    
 *@description Archivo con la interfaz para generación de conciliacion bancaria
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.DetalleConcilBancaria = Ext.extend(Phx.gridInterfaz, {
		
		constructor : function(config) {			
			this.maestro = config.maestro;            
			Phx.vista.DetalleConcilBancaria.superclass.constructor.call(this, config);                        
			this.init();  
            this.iniciarEventos();	          
            var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
            if(dataPadre){                                
                this.onEnablePanel(this, dataPadre);                
            } else {
                this.bloquearMenus();
            }            					
		},
		   
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_detalle_conciliacion_bancaria'
			},
			type : 'Field',
			form : true
		}, {
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_conciliacion_bancaria'
			},
			type : 'Field',
			form : true
		},
		{
			config:{
				name: 'tipo',
				fieldLabel: 'Tipo',
				allowBlank: false,				
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',				
				store:new Ext.data.ArrayStore({
                            fields: ['variable', 'valor'],
                            data : [ ['cheque','Debitos Bancarios'],['deposito','Creditos Bancarios'],
                            		['transito','Deposito en Transito']]
				}),
				anchor : '50%',
				gwidth: 120,
				valueField: 'variable',
				displayField: 'valor',
                renderer:function(value,p,record){
                    if(record.data['tipo'] == 'cheque'){
                        return String.format('{0}','Debitos Bancarios');
                    }else if(record.data['tipo'] == 'deposito'){
                    	return String.format('{0}','Creditos Bancarios');
                    }
                    else{
                        return String.format('{0}','Deposito en Transito');
                    }
                }														
			},
				type:'ComboBox',			
				id_grupo:1,
				grid:true,
				form:true
		},		
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '50%',
				gwidth: 90,
				format: 'd/m/Y',
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'conci.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_comprobante',
				fieldLabel: 'Nro Comprobante',
				allowBlank: false,
				anchor: '50%',
				gwidth: 90				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe',
				fieldLabel: 'Importe',
				allowBlank: false,
				anchor: '50%',
				gwidth: 90,
                renderer: (value, p, record) => { 
                    return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }                				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'concepto',
				fieldLabel: 'Concepto',
				allowBlank: false,
				anchor: '50%',
				gwidth: 100,				
			},
			type:'TextArea',
			filters:{pfiltro:'detacon.concepto',type:'string'},			
			id_grupo:1,
			grid:true,
			form:true
		},													
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'detcon.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'NumberField',
				filters:{pfiltro:'usr_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'detcon.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'NumberField',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}],
	tam_pag:50,
	title : 'Detalle Conciliacino',
	ActSave:'../../sis_tesoreria/control/ConciliacionBancaria/insertarDetalleConciliacionBancaria',
	ActDel:'../../sis_tesoreria/control/ConciliacionBancaria/eliminarDetalleConciliacionBancaria',
	ActList:'../../sis_tesoreria/control/ConciliacionBancaria/detalleConciliacionBancaria',
	id_store : 'id_detalle_conciliacion_bancaria',
		fields: [
		{name:'id_detalle_conciliacion_bancaria', type: 'numeric'},
		{name:'id_conciliacion_bancaria', type: 'numeric'},			
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'concepto', type: 'varchar'},
		{name:'nro_comprobante', type: 'varchar'},				
		{name:'importe', type: 'numeric'},
		{name:'tipo', type: 'varchar'},		
		{name:'fecha_reg', type: 'date', dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'}
	],
	onReloadPage:function(m)
	{		
		this.maestro=m;						        
		this.store.baseParams={id_conciliacion_bancaria:this.maestro.id_conciliacion_bancaria};
		this.load({params:{start:0, limit:50}});			
	},
    iniciarEventos:function(){
        this.cmpFecha = this.getComponente('fecha');
    },
    onButtonNew:function(){
        Phx.vista.DetalleConcilBancaria.superclass.onButtonNew.call(this);			
        var date = new Date();
        this.cmpFecha.setValue(date.dateFormat('d/m/Y'));
    },        
	loadValoresIniciales:function()
	{
		Phx.vista.DetalleConcilBancaria.superclass.loadValoresIniciales.call(this);
		this.getComponente('id_conciliacion_bancaria').setValue(this.maestro.id_conciliacion_bancaria);                				
	},
    preparaMenu:function(n){
         
         Phx.vista.DetalleConcilBancaria.superclass.preparaMenu.call(this,n);          
          if(this.maestro.estado ==  'finalizado'){
               this.getBoton('new').disable();
               this.getBoton('edit').disable();            
               this.getBoton('del').disable();
         }else{
               this.getBoton('edit').enable();
               this.getBoton('new').enable();
               this.getBoton('del').enable();            
         }   
     }, 
     liberaMenu: function() {
         Phx.vista.DetalleConcilBancaria.superclass.liberaMenu.call(this); 
           if(this.maestro&&(this.maestro.estado ==  'finalizado')){               
               this.getBoton('edit').disable();
               this.getBoton('new').disable();
               this.getBoton('del').disable();
         }
    },        	
    sortInfo : {			
        field : 'id_detalle_conciliacion_bancaria',
        direction : 'ASC'
    },
    bdel : true,
    bsave : false,
    btest: false		
	}); 
</script>
<?php
/**
 *@package pXP
 *@file    DetalleReporteConciliacion.php
 *@author  BVP
 *@date    
 *@description Archivo con la interfaz para generación de conciliacion bancaria
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.DetalleReporteConciliacion = Ext.extend(Phx.gridInterfaz, {        
		constructor : function(config) {            
			this.maestro = config.maestro;            
			Phx.vista.DetalleReporteConciliacion.superclass.constructor.call(this, config);            
			this.init();            
		},
		   
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_conciliacion_bancaria_rep'
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
				name: 'nro_cheque',
				fieldLabel: 'Nro Documento Gasto',
				allowBlank: true,
				anchor: '80%',
				gwidth: 80,
                renderer:function (value,p,record){                                        
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div>{0}</div>', record.data['nro_cheque']);
                    }
                    else{
                        return '<hr><center><b><p style=" color:green; font-size:15px;">Total: </p></b></center>';
                    }
                }                 
			},
				type:'TextField',
				filters:{pfiltro:'lban.nro_cheque',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},        
		{
			config:{
				name: 'periodo_1',                
				allowBlank: false,
				anchor: '50%',
				gwidth: 100,                
                renderer: (value, p, record) => {                                                                                
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_1,'0.000,00/i'));
                    }
                }                
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'periodo_2',				
				allowBlank: false,
				anchor: '50%',
				gwidth: 100,
                renderer: (value, p, record) => {                        
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_2,'0.000,00/i'));
                    }
                }                
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'periodo_3',				
				allowBlank: false,
				anchor: '50%',
				gwidth: 100,
                renderer: (value, p, record) => {                        
                    if(record.data.tipo_reg != 'summary'){
                        return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_3,'0.000,00/i'));
                    }
                }                
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},                
		{
			config:{
				name: 'total_haber',
				fieldLabel: 'Total Gasto',
				allowBlank: false,
				anchor: '50%',
				gwidth: 100,
                renderer: (value, p, record) => {                                            
                    return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }                 
			},
				type:'NumberField',				
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 80				
			},
				type:'TextField',
				filters:{pfiltro:'cbre.estado',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},                        
		{
			config:{
				name: 'detalle',
				fieldLabel: 'Detalle',
				allowBlank: true,
				anchor: '250%',
				gwidth: 250				
			},
				type:'TextField',
				filters:{pfiltro:'cbre.detalle',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},        
		{
			config:{
				name: 'observacion',
				fieldLabel: 'Observacion',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100				
			},
				type:'TextField',
				filters:{pfiltro:'cbre.observacion',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},                
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 80,
                format: 'd/m/Y', 
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}                				
			},
				type:'DateField',
				filters:{pfiltro:'cbre.fecha',type:'date'},
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
				filters:{pfiltro:'cbre.fecha_reg',type:'date'},
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
				type:'Field',
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
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}],
	tam_pag:50,
	title : 'Detalle Conciliacion Reporte',		
	ActList:'../../sis_tesoreria/control/ConciliacionBancaria/detalleConciliaRepo',
	id_store : 'id_conciliacion_bancaria_rep',
		fields: [
		{name:'id_conciliacion_bancaria_rep', type: 'numeric'},
		{name:'id_conciliacion_bancaria', type: 'numeric'},			
		{name:'nro_cheque', type: 'string'},
		{name:'periodo_1', type: 'numeric'},
        {name:'periodo_2', type: 'numeric'},
        {name:'periodo_3', type: 'numeric'},
        {name:'total_haber', type:'numeric'},
		{name:'detalle', type: 'varchar'},
        {name:'estado', type: 'varchar'},
        {name:'observacion', tyep: 'varchar'},		
        {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_reg', type: 'date'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
        {name:'tipo_reg', type: 'string'},
        {name:'total_1', type: 'numeric'},
        {name:'total_2', type: 'numeric'},
        {name:'total_3', type: 'numeric'}
	],
    mesAnterior: (mes) => {
    var meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    var value = [];
    var max = meses.length-1;    
        meses.map((n, i) => {
            if (n == mes)
                if (i == 0){
                    value.push(meses[max]);
                    value.push(meses[max-1]); 
                }else if(i == 1){
                    value.push(meses[i-1]);
                    value.push(meses[max]);
                }else{
                    value.push(meses[i-1]);
                    value.push(meses[i-2]);                    
                }            
        }) ;      
    return value;
    },
	onReloadPage:function(m)
	{	                    
		this.maestro=m;        

        data = this.mesAnterior(this.maestro.literal);

        this.changeFieldLabel('periodo_1', `Importe ${this.maestro.literal}`);        
        this.changeFieldLabel('periodo_2', `Importe ${data[0]}`);
        this.changeFieldLabel('periodo_3', `Importe ${data[1]}`);
        
		this.store.baseParams={id_conciliacion_bancaria:this.maestro.id_conciliacion_bancaria};        
		this.load({params:{start:0, limit:50}});        
	},
	loadValoresIniciales:function()
	{        
		Phx.vista.DetalleReporteConciliacion.superclass.loadValoresIniciales.call(this);        
		this.getComponente('id_conciliacion_bancaria').setValue(this.maestro.id_conciliacion_bancaria);
	},	
		sortInfo : {			
			field : 'id_conciliacion_bancaria_rep',
			direction : 'ASC'
		},
		bdel : false,
		bsave : false,
		btest: false,
        bnew: false,
        bedit: false
	}); 
</script>
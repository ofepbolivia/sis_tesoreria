<?php
/**
 *@package pXP
 *@file    ConciliacionBancaria.php
 *@author  BVP
 *@date    
 *@description Archivo con la interfaz para generación de conciliacion bancaria
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.ConciliacionBancaria = Ext.extend(Phx.gridInterfaz, {
		
		constructor : function(config) {			
			this.maestro = config.maestro;            
            this.tbarItems = ['-','<b style="color: green;">Gestion:</b>','-','-',
            this.cmbGestion,'-'];                         
			Phx.vista.ConciliacionBancaria.superclass.constructor.call(this, config);           
			this.init();
			var that = this;		
			this.iniciarEventos();	
			//this.store.baseParams.id_cuenta_bancaria = that.maestro.id_cuenta_bancaria;		
			this.store.baseParams={
					id_cuenta_bancaria : this.id_cuenta_bancaria,
					mycls:this.mycls};
								
			this.load({params:{start:0, limit:this.tam_pag}});			
			this.Atributos[1].valorInicial = this.id_cuenta_bancaria;															
			this.addButton('btnReporteConcBan',
				{
					text: 'Reporte Conciliacion',
					iconCls: 'bpdf',
					disabled: false,
					handler: this.reporteConcilBan,
					tooltip: '<b>Reporte Conciliacion</b>'
				}
			);
            this.addButton('btnRdetalleRep', 
            {
                text: 'Procesar Detalle Conciliacion Bancaria',
                iconCls: 'bsubir',
                disabled: true,
                handler: this.regDetRep,
                tooltip: '<b>Vuelve a procesar los datos para actulizar, el detalle de Cheque Girados y no cobrados'
            });
            this.addButton('btnfinalizado', {				
				text: 'Finalizar',
				iconCls: 'bassign',
				disabled: true,
				handler:this.Finalizar,
				tooltip: '<b>Finalizar</b><br/>Finalizar.'
		    });
            this.addButton('btnRetroceso', {				
				text: 'Habilitar Registro',
				iconCls: 'batras',
				disabled: false,
                hidden: true,
				handler:this.Retroceso,
				tooltip: '<b>Retroceso:</b><br/> Habilita el registro para procesar los datos nuevamente.'
		    });            
            Ext.Ajax.request({
                url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params: {fecha: new Date()},
                success: function (resp) {
                    var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                    this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                    this.load({params: {start: 0, limit: this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

            this.cmbGestion.on('select', this.capturarEventos, this);                        
		},
        capturarEventos: function () {
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.load({params: {start: 0, limit: this.tam_pag}});
        },
        cmbGestion: new Ext.form.ComboBox({
            name: 'gestion',
            id: 'gestion_reg',
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
            pageSize:5,
            queryDelay:100,
            listWidth:'220',
            hidden:false,
            width:80
        }),

		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_conciliacion_bancaria'
			},
			type : 'Field',
			form : true
		}, {
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_cuenta_bancaria'
			},
			type : 'Field',
			form : true
		},
        {
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank:false,
                emptyText:'Gestión...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo:{
                        field: 'gestion',
                        direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion','moneda','codigo_moneda'],                    
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                }),
                valueField: 'id_gestion',
                displayField: 'gestion',                
                hiddenName: 'id_gestion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                listWidth:200,
                resizable:true,
                anchor:'50%',
                gwidth: 50,
				renderer : function(value, p, record) {					
					return String.format('{0}', record.data['gestion']);
				}                
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{
                pfiltro:'gestion',
                type:'string'
            },
            grid:true,
            form:true
        },
        {
            config:{
                name:'id_periodo',
                fieldLabel:'Periodo',
                allowBlank:false,
                emptyText:'Periodo...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Periodo/listarPeriodo',
                    id: 'id_periodo',
                    root: 'datos',
                    sortInfo:{
                        field: 'id_periodo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_periodo','literal','periodo','fecha_ini','fecha_fin'],                    
                    remoteSort: true,
                    baseParams:{par_filtro:'periodo#literal'}
                }),
                valueField: 'id_periodo',
                displayField: 'literal',                
                hiddenName: 'id_periodo',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:12,
                queryDelay:1000,
                listWidth:200,
                resizable:true,
                anchor:'50%',
                gwidth: 70,
				renderer : function(value, p, record) {					
					return String.format('{0}', record.data['literal']);
				}                

            },
            type:'ComboBox',
            id_grupo:0,
            filters:{
                pfiltro:'literal',
                type:'string'
            },
            grid:true,
            form:true
        },
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha Elaboración',
				allowBlank: false,
				anchor: '70%',
				gwidth: 120,
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
				name: 'saldo_banco',
				fieldLabel: 'Saldo Según Extracto Bancario',
				allowBlank: false,
				anchor: '80%',
				gwidth: 170,
                renderer: (value, p, record) => {  
                    return  String.format('<div style="text-align:right;font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'saldo_libros',
				fieldLabel: 'Saldo Segun Libros',
				allowBlank: true,
				anchor: '80%',
				gwidth: 110,
                renderer: (value, p, record) => {  
                    return  String.format('<div style="text-align:right;font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		}, 
		{
			config:{
				name: 'saldo_real_1',
				fieldLabel: 'Saldo Real N° 1',
				allowBlank: true,
				anchor: '80%',
				gwidth: 90,
                renderer: (value, p, record) => {  
                    return  String.format('<div style="text-align:right;font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},               
		{
			config:{
				name: 'saldo_real_2',
				fieldLabel: 'Saldo Real N° 2',
				allowBlank: true,
				anchor: '80%',
				gwidth: 90,
                renderer: (value, p, record) => {  
                    return  String.format('<div style="text-align:right;font-weight:bold;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},        		        		
		{
			config:{
				name: 'diferencia',
				fieldLabel: 'Diferencia Saldos Reales',
				allowBlank: true,
				anchor: '80%',
				gwidth: 140,
                renderer: (value, p, record) => {  
                    if (value == 0 || value == null || value == undefined) {
                        return  String.format('<div style="text-align:right;font-weight:bold;color:green">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }else{
                        return  String.format('<div style="text-align:right;font-weight:bold;color:red;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                    }                    
                }				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},        
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: false,
				anchor: '100%',
				gwidth: 170
			},
			type:'TextArea',
			filters:{pfiltro:'conci.observaciones',type:'string'},			
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '70%',
				gwidth: 70,
                renderer: (value, p, record) => {
                    if (value == 'finalizado'){
                        return '<div style="color:green; font-size:11px; font-weight:bold;">Finalizado</div>';
                    }else{
                        return '<div style="color:blue; font-size:11px; font-weight:bold;">Proceso</div>';
                    }                    
                }
			},
			type:'TextArea',		
			id_grupo:1,
			grid:true,
			form:false
		},				        
		{
   			config:{
       		    name:'id_funcionario_elabo',
       		    hiddenName: 'id_funcionario',
   				origen:'FUNCIONARIO',
   				fieldLabel:'Elaborado por',
   				allowBlank:true,
                gwidth:200,
   				valueField: 'id_funcionario',
   			    gdisplayField: 'fun_elabo',   			    
   			    baseParams: { es_combo_solicitud : 'si' },
      			renderer:function(value, p, record){return String.format('{0}', record.data['fun_elabo']);}
       	     },
   			type:'ComboRec',//ComboRec
   			id_grupo:0,
   			filters:{pfiltro:'fun.desc_funcionario2',type:'string'},
   			bottom_filter:true,
   		    grid:false,
   			form:false
		 },
		{
   			config:{
       		    name:'id_funcionario_vb',
       		    hiddenName: 'id_funcionario',
   				origen:'FUNCIONARIO',
   				fieldLabel:'Vo.Bo',
   				allowBlank:true,
                gwidth:200,
   				valueField: 'id_funcionario',
   			    gdisplayField: 'fun_vb',
   			    baseParams: { es_combo_solicitud : 'no' },
      			renderer:function(value, p, record){return String.format('{0}', record.data['fun_vb']);}
       	     },
   			type:'ComboRec',//ComboRec
   			id_grupo:0,
   			filters:{pfiltro:'fu.desc_funcionario2',type:'string'},
   			bottom_filter:true,
   		    grid:false,
   			form:false
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
				filters:{pfiltro:'conci.fecha_reg',type:'date'},
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
				filters:{pfiltro:'conci.fecha_mod',type:'date'},
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
	title : 'Conciliacion Bancatia',
	ActSave:'../../sis_tesoreria/control/ConciliacionBancaria/insertarConciliacionBancaria',
	ActDel:'../../sis_tesoreria/control/ConciliacionBancaria/eliminarConciliacionBancaria',
	ActList:'../../sis_tesoreria/control/ConciliacionBancaria/listarConciliacionBancaria',
	id_store : 'id_conciliacion_bancaria',
		fields: [
		{name:'id_conciliacion_bancaria', type: 'numeric'},
		{name:'id_cuenta_bancaria', type: 'numeric'},
		{name:'id_funcionario_elabo', type: 'numeric'},
		{name:'id_funcionario_vb', type: 'numeric'},
		{name:'id_gestion', type: 'numeric'},
		{name:'id_periodo', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},				
		{name:'saldo_banco', type: 'numeric'},		
		{name:'observaciones', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'gestion',type:'numeric'},
		{name:'literal',type:'string'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'fun_elabo', type: 'string'},
		{name:'fun_vb', type: 'string'},
        {name:'estado', type: 'varchar'},
        {name:'saldo_real_1', type: 'numeric'},
        {name:'saldo_real_2', type: 'numeric'},
        {name:'saldo_libros', type: 'numeric'},
        {name:'diferencia', type:'numeric'},
        {name:'jefe_tesoreria', type:'string'}
	],
		sortInfo : {			
			field : 'id_periodo',
			direction : 'desc'
		},
		bdel : true,
		bsave : false,
		btest: false,
		fheight:'60%',
            
		onButtonNew:function(){
			Phx.vista.ConciliacionBancaria.superclass.onButtonNew.call(this);			
            var date = new Date();
            this.cmpFecha.setValue(date.dateFormat('d/m/Y'));
		},
		
		onButtonEdit:function(){
			Phx.vista.ConciliacionBancaria.superclass.onButtonEdit.call(this);			
			var data = this.getSelectedData();

            this.Cmp.id_periodo.on('focus',function(c,r,n){
            this.Cmp.id_periodo.reset();
            this.Cmp.id_periodo.store.baseParams={id_gestion:data.id_gestion, vista: 'reporte'};
            this.Cmp.id_periodo.modificado=true;

            },this);            
		},
        preparaMenu:function(n){
		  var data = this.getSelectedData();		                        
          
		  Phx.vista.ConciliacionBancaria.superclass.preparaMenu.call(this,n); 

          if ( data.estado == 'finalizado' && data.jefe_tesoreria == 'no') {
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
                this.getBoton('btnfinalizado').disable();
                this.getBoton('btnRdetalleRep').disable();
          }else if (data.estado == 'finalizado' && data.jefe_tesoreria != 'no'){
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
                this.getBoton('btnfinalizado').disable();
                this.getBoton('btnRdetalleRep').disable();              
                this.getBoton('btnRetroceso').setVisible(true);
          }else if (data.estado != 'finalizado' && data.jefe_tesoreria == 'no'){
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
                this.getBoton('btnRdetalleRep').enable();
                this.getBoton('btnfinalizado').enable();            
          }else if (data.estado != 'finalizado' && data.jefe_tesoreria != 'no'){
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
                this.getBoton('btnRdetalleRep').enable();
                this.getBoton('btnfinalizado').enable();       
                this.getBoton('btnRetroceso').setVisible(true);     
          }
          if ( (data.diferencia < 0.00 ) ||  (data.diferencia > 0.00)) {
                this.getBoton('btnfinalizado').disable();
          }          
        },		
        iniciarEventos:function(){
            this.cmpFecha = this.getComponente('fecha');
            this.Cmp.id_gestion.on('select',function(c,r,n){

                this.Cmp.id_periodo.reset();
                this.Cmp.id_periodo.store.baseParams={id_gestion:c.value, vista: 'reporte'};
                this.Cmp.id_periodo.modificado=true;

            },this);
        },		
		regDetRep: function() {            
            var data = this.getSelectedData();
            var NumSelect=this.sm.getCount();
            if (NumSelect != 0){
                Phx.CP.loadingShow();
				Ext.Ajax.request({
								url:'../../sis_tesoreria/control/ConciliacionBancaria/regDetalleRepo',
								params:{id_cuenta_bancaria:data.id_cuenta_bancaria,
										id_periodo:data.id_periodo,
										id_conciliacion_bancaria:data.id_conciliacion_bancaria},
                                        success:this.successReproceso,

								failure: this.conexionFailure,
								timeout:this.timeout,
								scope:this
				});
            }else{
				Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
			}                            
        },
        successReproceso: function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
                this.reload();
            }
        },
        Finalizar: function(){
            if(confirm('Esta seguro de finalizar')){
            Phx.CP.loadingShow();
            var d = this.sm.getSelected().data;
            Ext.Ajax.request({
                            url:'../../sis_tesoreria/control/ConciliacionBancaria/finalizarConciliacion',
                            params:{id_conciliacion_bancaria: d.id_conciliacion_bancaria, tipo_cambio: 'finalizacion'},
                            success:this.successFinalizar,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
            });
        }},
        successFinalizar:function(resp){
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if(!reg.ROOT.error){
                        this.reload();
                }
        },
        Retroceso:function(){
            var data = this.getSelectedData();            
            var NumSelect=this.sm.getCount();
            if (NumSelect != 0){
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                            url:'../../sis_tesoreria/control/ConciliacionBancaria/finalizarConciliacion',
                            params:{id_conciliacion_bancaria: data.id_conciliacion_bancaria, tipo_cambio: 'retroceso'},
                            success:this.successFinalizar,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                });
            }else{
				Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
			} 
        },                
		reporteConcilBan : function(){					
            var data = this.getSelectedData();
            var NumSelect=this.sm.getCount();            

			if(NumSelect != 0)
			{	
				Phx.CP.loadingShow();					 				
				Ext.Ajax.request({
								url:'../../sis_tesoreria/control/ConciliacionBancaria/reporteConciliacionBancaria',
								params:{id_cuenta_bancaria:data.id_cuenta_bancaria,
										id_periodo:data.id_periodo,
										id_conciliacion_bancaria:data.id_conciliacion_bancaria},
								success: this.successExport,
								failure: this.conexionFailure,
								timeout:this.timeout,
								scope:this
				});				
			}
			else
			{
				Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
			}
		},

		tabsouth:[
        {
            url: '../../../sis_tesoreria/vista/conciliacion_bancaria/DetalleReporteConciliacion.php',
            title: 'Cheques Girados y No Cobrados',
            height: '50%',
            cls: 'DetalleReporteConciliacion'
        },            
        {
			url : '../../../sis_tesoreria/vista/conciliacion_bancaria/DetalleConcilBancaria.php',
			title : 'Detalle Conciliacion',
			height: '50%',
			cls : 'DetalleConcilBancaria'
		}        
        ]
	}); 
</script>
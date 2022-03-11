<?php
/**
*@package pXP
*@file SolicitudRendicionDet.php
*@author  (gsarmiento)
*@date 16-12-2015 15:14:01
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SolicitudRendicionDet=Ext.extend(Phx.gridInterfaz,{
	tipoDoc: 'compra',
	id_estado_workflow : 0,
	id_proceso_workflow : 0,
	constructor:function(config){

		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.SolicitudRendicionDet.superclass.constructor.call(this,config);

		this.init();
		this.addButton('fin_registro',
			{	text:'Mandar Revision',
				iconCls: 'badelante',
				disabled:true,
				handler:this.sigEstado,
				tooltip: '<b>Mandar Revision</b><p>Mandar a revision facturas</p>'
			}
		);

		//15-03-2021 (may) boton Relacionar Factura
        this.addButton('btnNewDoc',
            {
                text: 'Relacionar Factura',
                iconCls: 'blist',
                disabled: false,
                handler: this.modExcento,
                tooltip: 'Permite relacionar una Factura existente al Trámite'
            }
        );

		this.load({params:{start:0, limit:this.tam_pag, id_solicitud_efectivo:this.id_solicitud_efectivo}, me : this, callback:function(r,o,s){

			if(r[0].data.id_estado_wf != '' && r[0].data.id_proceso_wf) {
				o.me.getBoton('fin_registro').enable();
				o.me.id_estado_workflow = r[0].data.id_estado_wf;
				o.me.id_proceso_workflow = r[0].data.id_proceso_wf;
			}
		} });

		if (parseFloat(this.dias_no_rendido) < 0){
			this.getBoton('edit').setVisible(false);
			this.getBoton('new').setVisible(false);
		}
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_solicitud_rendicion_det'
			},
			type:'Field',
			form:true 
		},
		/*{
			config: {
				name: 'id_proceso_caja',
				fieldLabel: 'id_proceso_caja',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_/control/Clase/Metodo',
					id: 'id_',
					root: 'datos',
					sortInfo: {
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
				}),
				valueField: 'id_',
				displayField: 'nombre',
				gdisplayField: 'desc_',
				hiddenName: 'id_proceso_caja',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true
		},*/
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_solicitud_efectivo'
			},
			type:'Field',
			form:true 
		},
		/*{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_plantilla'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_moneda'
			},
			type:'Field',
			form:true 
		},*/
		{
			config:{
				name: 'desc_plantilla',
				fieldLabel: 'Tipo Documento',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'pla.desc_plantilla',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:true,
				form:false
		},		
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				format: 'd/m/Y',
				renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'dc.fecha',type:'date'},
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config: {
				name: 'id_doc_compra_venta',
				fieldLabel: 'Razon Social',
				allowBlank: true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_/control/Clase/Metodo',
					id: 'id_',
					root: 'datos',
					sortInfo: {
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
				}),
				valueField: 'id_',
				displayField: 'nombre',
				gdisplayField: 'desc_',
				hiddenName: 'id_doc_compra_venta',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['razon_social']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'dc.razon_social',type: 'string'},
			grid: true,
			form: true
		},		
		{
			config:{
				name: 'nit',
				fieldLabel: 'Nit',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'dc.nit',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nro_documento',
				fieldLabel: 'Nro Factura',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'dc.nro_documento',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nro_autorizacion',
				fieldLabel: 'Nro Autorizacion',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'dc.nro_autorizacion',type:'string'},
				bottom_filter: true,
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'importe_doc',
				fieldLabel: 'Importe Total',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,				
				renderer:function (value,p,record){
					return  String.format('{0}', value);
				}
			},
			type:'NumberField',
			id_grupo:0,
			grid:true,
			form:true
		},		
		{
			config:{
				name: 'importe_descuento',
				fieldLabel: 'Descuento',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,				
				renderer:function (value,p,record){
					return  String.format('{0}', value);
				}
			},
			type:'NumberField',
			id_grupo:0,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'importe_descuento_ley',
				fieldLabel: 'Descuento Ley',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,				
				renderer:function (value,p,record){
					return  String.format('{0}', value);
				}
			},
			type:'NumberField',
			id_grupo:0,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'importe_excento',
				fieldLabel: 'Excento',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,				
				renderer:function (value,p,record){
					return  String.format('{0}', value);
				}
			},
			type:'NumberField',
			id_grupo:0,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'monto',
				fieldLabel: 'Liquido Pagable',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179650,				
				renderer:function (value,p,record){
						if(record.data.tipo != 'summary'){
							return  String.format('{0}', value);
						}
						else{
							return  String.format('<b><font size=2 >{0}</font><b>', value==null?0:value);
						}
						
					}
			},
				type:'NumberField',
				filters:{pfiltro:'rend.monto',type:'numeric'},
				id_grupo:0,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'rend.estado_reg',type:'string'},
				id_grupo:0,
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
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:0,
				grid:true,
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
				filters:{pfiltro:'rend.fecha_reg',type:'date'},
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'rend.usuario_ai',type:'string'},
				id_grupo:0,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'rend.id_usuario_ai',type:'numeric'},
				id_grupo:0,
				grid:false,
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
				filters:{pfiltro:'rend.fecha_mod',type:'date'},
				id_grupo:0,
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
				id_grupo:0,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Rendicion',
	ActSave:'../../sis_tesoreria/control/SolicitudRendicionDet/insertarSolicitudRendicionDet',
	ActDel:'../../sis_tesoreria/control/SolicitudRendicionDet/eliminarSolicitudRendicionDet',
	ActList:'../../sis_tesoreria/control/SolicitudRendicionDet/listarSolicitudRendicionDet',
	id_store:'id_solicitud_rendicion_det',
	fields: [
		{name:'id_solicitud_rendicion_det', type: 'numeric'},
		{name:'id_proceso_caja', type: 'numeric'},
		{name:'id_solicitud_efectivo', type: 'numeric'},
		{name:'id_doc_compra_venta', type: 'numeric'},
		{name:'desc_plantilla', type: 'string'},
		{name:'desc_moneda', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'id_plantilla', type: 'numeric'},
		{name:'id_moneda', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'nit', type: 'string'},
		{name:'razon_social', type: 'string'},
		{name:'nro_autorizacion', type: 'string'},
		{name:'nro_documento', type: 'string'},
		{name:'nro_dui', type: 'string'},
		{name:'obs', type: 'string'},
		{name:'importe_doc', type: 'string'},
		{name:'importe_pago_liquido', type: 'string'},
		{name:'importe_iva', type: 'string'},
		{name:'importe_descuento', type: 'string'},
		{name:'importe_descuento_ley', type: 'string'},
		{name:'importe_excento', type: 'string'},
		{name:'importe_ice', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'monto', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'id_proceso_wf', type: 'numeric'},
		{name:'id_estado_wf', type: 'numeric'}
	],
	sortInfo:{
		field: 'id_solicitud_rendicion_det',
		direction: 'ASC'
	},
    onButtonNew:function(){
        //abrir formulario de solicitud
        //this.abrirFormulario('new')
        this.abrirFormulario('new',undefined, false)
    },

    onButtonEdit:function(){
        this.abrirFormulario('edit', this.sm.getSelected(), false)
    },
	abrirFormulario:function(tipo, record, readOnly, edit_si_no='no'){
        //abrir formulario de solicitud
        console.log('llegarenvista', edit_si_no)
	   var me = this;
        console.log('llegamtipo',record );
	   me.objSolForm = Phx.CP.loadWindows('../../../sis_tesoreria/vista/solicitud_rendicion_det/FormRendicion.php',
								'Formulario de rendicion',
								{
									modal:true,
									width:'90%',
									height:'90%'
								}, {data:{objPadre: me,
										  tipoDoc: me.tipoDoc,
										  tipo_form : tipo,
										  id_depto : me.id_depto,
										  id_solicitud_efectivo : me.id_solicitud_efectivo,
										  datosOriginales: record,

                                          readOnly: readOnly,
                                          boton_rendicion: edit_si_no
										  },
									id_moneda_defecto : me.id_moneda,
                                    bsubmit: !readOnly
								},
								this.idContenedor,
								'FormRendicion');
    },

	/*
	abrirFormulario: function(tipo, record){
	                                { data: { 
	                                	 id_gestion: me.cmbGestion.getValue(),
	                                	 id_periodo: me.cmbPeriodo.getValue(),
	                                	 id_depto: me.cmbDepto.getValue(),
	                                	 tmpPeriodo: me.tmpPeriodo,
	                                	 tmpGestion: me.tmpGestion,
	                                	}
	                                }, 
	                                this.idContenedor,
	                                'FormCompraVenta',
	                                {
	                                    config:[{
	                                              event:'successsave',
	                                              delegate: this.onSaveForm,
	                                              
	                                            }],
	                                    
	                                    scope:this
	                                 });  
   },*/
   

	
	sigEstado:function(){
		var id_estado_workflow = this.id_estado_workflow;
		var id_proceso_workflow = this.id_proceso_workflow;
		
		if(id_estado_workflow ==0 || id_proceso_workflow==0){
			Ext.MessageBox.alert('Alerta','Debe tener al menos una factura registrada');
		}else{
			Ext.Msg.show({
				   title:'Confirmación',
				   scope: this,
				   msg: 'Todas las facturas seran enviadas a rendicion? Para enviar presione el botón "Si"',
				   buttons: Ext.Msg.YESNO,
				   fn: function(id, value, opt) {			   		
						if (id == 'yes') {
							this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
										'Estado de Wf',
										{
											modal:true,
											width:700,
											height:450
										}, {data:{
											   id_estado_wf:id_estado_workflow,
											   id_proceso_wf:id_proceso_workflow									  
											}}, this.idContenedor,'FormEstadoWf',
										{
											config:[{
													  event:'beforesave',
													  delegate: this.onSaveWizard												  
													}],
											
											scope:this
										 });        
						} else {
							opt.hide;
						}
				   },	
				   animEl: 'elId',
				   icon: Ext.MessageBox.WARNING
				}, this);
		}
	 },
	 
	 liberaMenu:function(n){		 
		  if (this.estado=='finalizado'){
			  this.getBoton('new').disable();
			  this.getBoton('edit').disable();
			  this.getBoton('del').disable();
		  }else{
			  this.getBoton('new').enable();
			  //this.getBoton('edit').enable();
			  //this.getBoton('del').enable();

		  }
		  this.getBoton('fin_registro').disable();
     },
	 
	 preparaMenu:function(n){
		  Phx.vista.SolicitudRendicionDet.superclass.preparaMenu.call(this);
		  if (this.estado=='finalizado'){
			  this.getBoton('edit').disable();
		  }else{
			  this.getBoton('edit').enable();
		  }
		 var data = this.getSelectedData();

		 if (data.id_estado_wf != '' && data.id_proceso_wf != ''){
			 this.getBoton('fin_registro').enable();
			 this.id_estado_workflow = data.id_estado_wf;
			 this.id_proceso_workflow = data.id_proceso_wf;
		 }
     },
	 
	 onSaveWizard:function(wizard,resp){
			Phx.CP.loadingShow();			
			Ext.Ajax.request({
				url:'../../sis_tesoreria/control/SolicitudEfectivo/siguienteEstadoSolicitudEfectivo',
				params:{
						
					id_proceso_wf_act:  resp.id_proceso_wf_act,
					id_estado_wf_act:   resp.id_estado_wf_act,
					id_tipo_estado:     resp.id_tipo_estado,
					id_funcionario_wf:  resp.id_funcionario_wf,
					id_depto_wf:        resp.id_depto_wf,
					obs:                resp.obs,
					//json_procesos:      Ext.util.JSON.encode(resp.procesos)		
					},
				success:this.successWizard,
				failure: this.conexionFailure,
				argument:{wizard:wizard},
				timeout:this.timeout,
				scope:this
			});
		},
		
		successWizard:function(resp){
			Phx.CP.loadingHide();
			resp.argument.wizard.panel.destroy()

			this.reload();
		 },

        modExcento : function () {
            var me = this;
            var simple = new Ext.FormPanel({
                labelWidth: 75, // label settings here cascade unless overridden
                frame:true,
                bodyStyle:'padding:5px 5px 0; background:linear-gradient(45deg, #a7cfdf 0%,#a7cfdf 100%,#23538a 100%);',
                width: 300,
                height:70,
                defaultType: 'textfield',
                items: [
                    new Ext.form.ComboBox(
                        {
                            name: 'id_doc_compra_venta',
                            fieldLabel: 'Facturas',
                            allowBlank: false,
                            emptyText:'Elija una plantilla...',
                            store:new Ext.data.JsonStore(
                                {
                                    url: '../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVenta',
                                    id: 'id_doc_compra_venta',
                                    root:'datos',
                                    sortInfo:{
                                        field:'dcv.nro_documento',
                                        direction:'asc'
                                    },
                                    totalProperty:'total',
                                    fields: ['id_doc_compra_venta','revisado','nro_documento','nit',
                                        'desc_plantilla', 'desc_moneda','importe_doc','nro_documento',
                                        'tipo','razon_social','fecha'],
                                    remoteSort: true,
                                    //baseParams:{par_filtro:'pla.desc_plantilla#dcv.razon_social#dcv.nro_documento#dcv.nit#dcv.importe_doc#dcv.codigo_control', id_periodo: me.maestro.id_periodo, isRendicionDet: 'si'},
                                    baseParams:{par_filtro:'pla.desc_plantilla#dcv.razon_social#dcv.nro_documento#dcv.nit#dcv.importe_doc#dcv.codigo_control', sin_cbte: 'si', isRendicionDetCC: 'si'},
                                }),
                            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{razon_social}</b>,  NIT: {nit}</p><p>{desc_plantilla} </p><p ><span style="color: #F00000">Doc: {nro_documento}</span> de Fecha: {fecha}</p><p style="color: green;"> {importe_doc} {desc_moneda}  </p></div></tpl>',
                            valueField: 'id_doc_compra_venta',
                            hiddenValue: 'id_doc_compra_venta',
                            displayField: 'desc_plantilla',
                            gdisplayField:'nro_documento',
                            listWidth:'401',
                            forceSelection:true,
                            typeAhead: false,
                            triggerAction: 'all',
                            lazyRender:true,
                            mode:'remote',
                            pageSize:20,
                            queryDelay:500,
                            gwidth: 250,
                            minChars:2,
                            resizable: true
                        })
                ]

            });
            this.excento_formulario = simple;

            var win = new Ext.Window({
                title: '<h1 style="height:20px; font-size:15px;"><p style="margin-left:30px;">Listar Facturas<p></h1>', //the title of the window
                width:320,
                height:150,
                //closeAction:'hide',
                modal:true,
                plain: true,
                items:simple,
                buttons: [{
                    text:'<i class="fa fa-floppy-o fa-lg"></i> Guardar',
                    scope:this,
                    handler: function(){
                        this.modificarNuevo(win);
                    }
                },{
                    text: '<i class="fa fa-times-circle fa-lg"></i> Cancelar',
                    handler: function(){
                        win.hide();
                    }
                }]

            });
            win.show();

        },

        modificarNuevo : function (win) {
            if (this.excento_formulario.items.items[0].getValue() == '' || this.excento_formulario.items.items[0].getValue() == 0) {
                Ext.Msg.show({
                    title:'<h1 style="font-size:15px;">Aviso!</h1>',
                    msg: '<p style="font-weight:bold; font-size:12px;">Tiene que seleccionar una factura para continuar</p>',
                    buttons: Ext.Msg.OK,
                    width:320,
                    height:150,
                    icon: Ext.MessageBox.WARNING,
                    scope:this
                });
            } else {
                this.guardarDetalles();
                win.hide();
            }

        },

        guardarDetalles : function(){
            var me = this;
            console.log('llegaguardar', this.excento_formulario.items.items[0].getValue())
            Ext.Ajax.request({
                url:'../../sis_tesoreria/control/SolicitudRendicionDet/getRendicionDetRel',
                params:{'id_doc_compra_venta' : this.excento_formulario.items.items[0].getValue()},
                success: function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var id_doc_compra_venta = this.excento_formulario.items.items[0].getValue();
                    var revisado = reg.ROOT.datos.revisado;
                    var movil = reg.ROOT.datos.movil;
                    var tipo = reg.ROOT.datos.tipo;
                    var importe_excento = reg.ROOT.datos.importe_excento;
                    var id_plantilla = reg.ROOT.datos.id_plantilla;
                    var nro_documento = reg.ROOT.datos.nro_documento;
                    var nit = reg.ROOT.datos.nit;
                    var importe_ice = reg.ROOT.datos.importe_ice;
                    var nro_autorizacion = reg.ROOT.datos.nro_autorizacion;
                    var importe_iva = reg.ROOT.datos.importe_iva;
                    var importe_descuento = reg.ROOT.datos.importe_descuento;
                    var importe_doc = reg.ROOT.datos.importe_doc;
                    var sw_contabilizar = reg.ROOT.datos.sw_contabilizar;
                    var tabla_origen = reg.ROOT.datos.tabla_origen;
                    var estado = reg.ROOT.datos.estado;
                    var id_depto_conta = reg.ROOT.datos.id_depto_conta;
                    var id_origen = reg.ROOT.datos.id_origen;
                    var obs = reg.ROOT.datos.obs;
                    var estado_reg = reg.ROOT.datos.estado_reg;
                    var codigo_control = reg.ROOT.datos.codigo_control;
                    var importe_it = reg.ROOT.datos.importe_it ;
                    var razon_social = reg.ROOT.datos.razon_social ;
                    var id_usuario_ai = reg.ROOT.datos.id_usuario_ai ;
                    var id_usuario_reg = reg.ROOT.datos.id_usuario_reg ;
                    var usuario_ai = reg.ROOT.datos.usuario_ai ;
                    var id_usuario_mod = reg.ROOT.datos.id_usuario_mod ;
                    var usr_reg = reg.ROOT.datos.usr_reg ;
                    var usr_mod = reg.ROOT.datos.usr_mod ;
                    var importe_pendiente = reg.ROOT.datos.importe_pendiente ;
                    var importe_anticipo = reg.ROOT.datos.importe_anticipo ;
                    var importe_retgar = reg.ROOT.datos.importe_retgar ;
                    var importe_neto = reg.ROOT.datos.importe_neto ;
                    var desc_depto = reg.ROOT.datos.desc_depto ;
                    var desc_plantilla = reg.ROOT.datos.desc_plantilla ;
                    var importe_descuento_ley = reg.ROOT.datos.importe_descuento_ley ;
                    var importe_pago_liquido = reg.ROOT.datos.importe_pago_liquido ;
                    var nro_dui = reg.ROOT.datos.nro_dui ;
                    var id_moneda = reg.ROOT.datos.id_moneda ;
                    var desc_moneda = reg.ROOT.datos.desc_moneda ;
                    var id_auxiliar = reg.ROOT.datos.id_auxiliar ;
                    var codigo_auxiliar = reg.ROOT.datos.codigo_auxiliar ;
                    var nombre_auxiliar = reg.ROOT.datos.nombre_auxiliar ;
                    var id_tipo_doc_compra_venta = reg.ROOT.datos.id_tipo_doc_compra_venta ;
                    var desc_tipo_doc_compra_venta = reg.ROOT.datos.desc_tipo_doc_compra_venta ;
                    var fecha = reg.ROOT.datos.fecha ;
                    var sb = {id: "1061",data:{id_solicitud_rendicion_det: "1061",id_doc_compra_venta: id_doc_compra_venta,id_solicitud_efectivo: this.id_solicitud_efectivo,id_procesos_caja:"113",revisado: revisado,movil: movil,tipo: "compra",importe_excento: importe_excento,id_plantilla: id_plantilla,nro_documento: nro_documento,nit: nit,importe_ice: importe_ice,nro_autorizacion: nro_autorizacion,importe_iva: importe_iva,importe_descuento: importe_descuento,importe_doc: importe_doc,sw_contabilizar: sw_contabilizar,tabla_origen: tabla_origen,estado: estado,id_depto_conta: id_depto_conta,id_origen: id_origen,obs: obs,estado_reg: estado_reg,codigo_control: codigo_control,importe_it: importe_it,razon_social: razon_social,id_usuario_ai: id_usuario_ai,id_usuario_reg: id_usuario_reg,usuario_ai: usuario_ai,id_usuario_mod: id_usuario_mod,usr_reg: usr_reg,usr_mod: usr_mod,importe_pendiente: importe_pendiente,importe_anticipo: importe_anticipo,importe_retgar: importe_retgar,importe_neto: importe_neto,tipo_reg: "",desc_depto: desc_depto,desc_plantilla: desc_plantilla,importe_descuento_ley: importe_descuento_ley,importe_pago_liquido: importe_pago_liquido,nro_dui: nro_dui,id_moneda: id_moneda,desc_moneda: desc_moneda,id_auxiliar: id_auxiliar,codigo_auxiliar: codigo_auxiliar,nombre_auxiliar: nombre_auxiliar,isNewRelationEditable:'si',fecha: fecha},json:{id_solicitud_rendicion_det: "1061",id_doc_compra_venta: id_doc_compra_venta,id_solicitud_efectivo: this.id_solicitud_efectivo,id_procesos_caja:"113",revisado: revisado,movil: movil,tipo: "compra",importe_excento: importe_excento,id_plantilla: id_plantilla,nro_documento: nro_documento,nit: nit,importe_ice: importe_ice,nro_autorizacion: nro_autorizacion,importe_iva: importe_iva,importe_descuento: importe_descuento,importe_doc: importe_doc,sw_contabilizar: sw_contabilizar,tabla_origen: tabla_origen,estado: estado,id_depto_conta: id_depto_conta,id_origen: id_origen,obs: obs,estado_reg: estado_reg,codigo_control: codigo_control,importe_it: importe_it,razon_social: razon_social,id_usuario_ai: id_usuario_ai,id_usuario_reg: id_usuario_reg,usuario_ai: usuario_ai,id_usuario_mod: id_usuario_mod,usr_reg: usr_reg,usr_mod: usr_mod,importe_pendiente: importe_pendiente,importe_anticipo: importe_anticipo,importe_retgar: importe_retgar,importe_neto: importe_neto,tipo_reg: "",desc_depto: desc_depto,desc_plantilla: desc_plantilla,importe_descuento_ley: importe_descuento_ley,importe_pago_liquido: importe_pago_liquido,nro_dui: nro_dui,id_moneda: id_moneda,desc_moneda: desc_moneda,id_auxiliar: id_auxiliar,codigo_auxiliar: codigo_auxiliar,nombre_auxiliar: nombre_auxiliar,id_tipo_doc_compra_venta: id_tipo_doc_compra_venta,desc_tipo_doc_compra_venta: desc_tipo_doc_compra_venta, fecha: fecha}};

                    this.abrirFormulario('edit', sb, false,'si')
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },


	
	bdel:true,
	bsave:false,
    bgantt:true
	}
)
</script>
		
		
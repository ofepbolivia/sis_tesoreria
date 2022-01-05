<?php
/**
*@package pXP
*@file gen-CuotasDevengadas.php
*@author  (ismael.valdivia)
*@date 25-11-2021 12:56:39
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CuotasDevengadas=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		var me = this;
    	//llama al constructor de la clase padre
    this.initButtons=[this.cmbGestion];
		Phx.vista.CuotasDevengadas.superclass.constructor.call(this,config);
		this.init();
		this.crearFomularioDepto();
		this.crearFomularioDeptoConvertido();
		this.iniciarEventos();

    var añoActual = new Date().getFullYear();
    this.cmbGestion.store.load({params:{start:0,limit:50},
           callback : function (r) {
             for (var i = 0; i < r.length; i++) {
               if (r[i].data.gestion == añoActual) {
                 this.cmbGestion.setValue(r[i].data.id_gestion);
                 this.cmbGestion.fireEvent('select', this.cmbGestion,this.cmbGestion.store.getById(r[i].data.id_gestion));
               }
             }
            }, scope : this
        });



    this.cmbGestion.on('select', function( combo, record, index){
        this.capturaFiltros();
    },this);


		this.addButton('SolDevPag', {
				text: 'Generar Cbte',
				grupo:[2],
				iconCls: 'bpagar',
				disabled: true,
				handler: this.onBtnDevPag,
				tooltip: '<b>Solicitar Devengado/Pago</b><br/>Genera en cotabilidad el comprobante Correspondiente'
		});


		this.addButton('GenCuotaDeven', {
				text: 'Convertir y Generar Cuota',
				grupo:[1],
				iconCls: 'bredo',
				disabled: true,
				handler: this.onConvertir,
				tooltip: '<b>Convertir y Generar Cuota'
		});

		this.addButton('btnChequeoDocumentosWf',{
				text: 'Documentos',
				grupo: [3],
				iconCls: 'bchecklist',
				disabled: true,
				handler: this.loadCheckDocumentosRecWf,
				tooltip: '<b>Documentos </b><br/>Subir los documetos requeridos.'
		});

    this.bbar.el.dom.style.background='#73AAB3';
    this.tbar.el.dom.style.background='#73AAB3';
    this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#E9E9E9';
    this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#F6F6F6';

		this.store.baseParams.pes_estado = 'devengado';


	},
	beditGroups: [1,2],
	bactGroups:  [1,2,3],
	bexcelGroups: [1,2,3],
	bganttGroups: [3],


	gruposBarraTareas:[
										  {name:'devengado',title:'<H1 style="font-size:11px; color: #009915;" align="center">Devengados <br> Pendientes de Pago</h1>',grupo:1,height:0},
											{name:'pagado',title:'<H1 style="font-size:11px; color: #0031BF;" align="center">Pagos <br> Pendientes</h1>',grupo:2,height:0},
											{name:'convertidos',title:'<H1 style="font-size:11px; color: #060606;" align="center">Devengados <br> Convertidos</h1>',grupo:3,height:0},
										],

	loadCheckDocumentosRecWf:function() {
		var rec=this.sm.getSelected();
		var that=this;
		Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
				'Chequear documento del WF',
				{
						width:'90%',
						height:500,
						onDestroy: function() {
								this.fireEvent('closepanel',this);

								if (this.window) {
										this.window.destroy();
								}
								if (this.form) {
										this.form.destroy();
								}
								Phx.CP.destroyPage(this.idContenedor);
								that.reload();


						},
				},
				rec.data,
				this.idContenedor,
				'DocumentoWf'
		)
	},

	actualizarSegunTab: function(name, indice){

 					 this.store.baseParams.pes_estado = name;

					 if (this.cmbGestion.getValue()) {
						 this.load({params:{start:0, limit:this.tam_pag}});
					 }


 	},

	Grupos: [
			{
					// layout: 'hbox',
					layout: 'column',
					border: false,
					defaults: {
							border: false
					},
					items: [
							{
									bodyStyle: 'padding-right:10px;',
									items: [
											{
													xtype: 'fieldset',
													title: 'Tipo de Pago',
													autoHeight: true,
													//layout:'hbox',
													items: [],
													id_grupo: 0
													//margins: '2 2 2 2'
											},
											{
													xtype: 'fieldset',
													title: 'Periodo al que corresponde el gasto',
													autoHeight: true,
													hiden: true,
													//layout:'hbox',
													items: [],
													margins: '2 2 2 2',
													// margins: '2 10 2 2',
													//19-06-2019 , se comenta botn Dividir gasto
													// buttons: [{
													//     text: 'Dividir gasto',
													//     handler: me.calcularAnticipo,
													//     scope: me,
													//     tooltip: 'Según las fechas,  ayuda con el cálculo  del importe anticipado'
													// }],
													id_grupo: 3

											}
									]
							},
							{
									bodyStyle: 'padding-right:10px;',
									items: [
													{
															xtype: 'fieldset',
															title: 'Detalle de Pago',
															autoHeight: true,
															// layout:'hbox',
															items: [],
															margins: '2 10 2 2',
															id_grupo: 1
													},
													{
															xtype: 'fieldset',
															title: 'Ajustes',
															autoHeight: true,
															hiden: true,
															// layout:'hbox',
															items: [],
															margins: '2 10 2 2',
															id_grupo: 2
													}
									]
							},
							{
									bodyStyle: 'padding-right:10px;',
									items: [
											{
													xtype: 'fieldset',
													title: 'Observaciones',
													autoHeight: true,
													hiden: true,
													// layout:'hbox',
													items: [],
													margins: '2 10 2 2',
													id_grupo: 4
											}

									]
							},
							{
									bodyStyle: 'padding-right:10px;',
									items: [
											{
													xtype: 'fieldset',
													title: 'Multas',
													autoHeight: true,
													hiden: true,
													// layout:'hbox',
													items: [],
													margins: '2 10 2 2',
													id_grupo: 5
											}

									]
							}


					]

			}],

  capturaFiltros:function(combo, record, index){

      //if(this.validarFiltros()){

          this.store.baseParams.id_gestion = this.cmbGestion.getValue();

					this.load();
      //}

  },

  // validarFiltros:function(){
	//
  //     if(this.cmbGestion.validate() ){
  //         return true;
  //     }
  //     else{
  //         return false;
  //     }
  // },

  cmbGestion: new Ext.form.ComboBox({
      fieldLabel: 'Gestion',
      grupo:[1,2,3],
      allowBlank: false,
      blankText:'... ?',
      emptyText:'Gestion...',
      name:'id_gestion',
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
      width:80
  }),

  Atributos: [
			{
					//configuracion del componente
					config: {
							labelSeparator: '',
							inputType: 'hidden',
							name: 'tipo_interfaz',
							//value:'DevengadosSiguienteGestion'
					},
					type: 'Field',
					//valorInicial:'DevengadosSiguienteGestion',
					form: true,
					grid: false
			},
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'id_plan_pago'
          },
          type: 'Field',
          form: true
      },
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'id_int_comprobante'
          },
          type: 'Field',
          form: true,
          grid: false
      },
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'porc_monto_retgar'
          },
          type: 'Field',
          form: true
      },
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'porc_descuento_ley',
              allowDecimals: true,
              decimalPrecision: 10
          },
          type: 'NumberField',
          form: true
      },
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'tipo_excento',
              allowDecimals: true,
              decimalPrecision: 10
          },
          type: 'TextField',
          form: true
      },
      {
          //configuracion del componente
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'valor_excento',
              allowDecimals: true,
              decimalPrecision: 10
          },
          type: 'NumberField',
          form: true
      },
      {
          config: {
              name: 'id_obligacion_pago',
              inputType: 'hidden'
          },
          type: 'Field',
          form: true
      },
      {
          config: {
              name: 'id_plan_pago_fk',
              inputType: 'hidden',
          },
          type: 'Field',
          form: true
      },
      // {
      //     config: {
      //         name: 'revisado_asistente',
      //         fieldLabel: 'Rev',
      //         allowBlank: true,
      //         anchor: '80%',
      //         gwidth: 50,
      //         renderer: function (value, p, record) {
      //             if (record.data['revisado_asistente'] == 'si')
      //                 return String.format('{0}', "<div style='text-align:center'><img title='Revisado / Permite ver pagos relacionados'  src = '../../../lib/imagenes/ball_green.png' align='center' width='24' height='24'/></div>");
      //             else
      //                 return String.format('{0}', "<div style='text-align:center'><img title='No revisado / Permite ver pagos relacionados'  src = '../../../lib/imagenes/ball_white.png' align='center' width='24' height='24'/></div>");
      //         },
      //     },
      //     type: 'Checkbox',
      //     filters: {pfiltro: 'plapa.revisado_asistente', type: 'string'},
      //     id_grupo: 1,
      //     grid: false,
      //     form: false
      // },
      {
          config: {
              name: 'num_tramite',
              fieldLabel: 'Num. Tramite',
              allowBlank: true,
              anchor: '80%',
              gwidth: 150,
              maxLength: 200,
              renderer: function (value, p, record) {
                  if (record.data.usr_reg == 'vitalia.penia' || record.data.usr_reg == 'shirley.torrez' || record.data.usr_reg == 'patricia.lopez' || record.data.usr_reg == 'patricia.lopez') {
                      return String.format('<b><font color="orange">{0}</font></b>', value);
                  }
                  else {
                      //15-01-2021 (may)
                      if (record.data.id_obligacion_pago_extendida > 0) {
                          return String.format('<b><font color="orange">{0}</font></b>', value);
                      }else{
                          return value;
                      }

                  }

              }
          },
          type: 'TextField',
          filters: {pfiltro: 'op.num_tramite', type: 'string'},
					bottom_filter:true,
          id_grupo: 1,
          grid: true,
          form: false
      },
      //
      // {
      //     config: {
      //         name: 'numero_op',
      //         fieldLabel: 'Obl. Pago',
      //         allowBlank: true,
      //         anchor: '80%',
      //         gwidth: 130,
      //         maxLength: 4
      //     },
      //     type: 'NumberField',
      //     filters: {pfiltro: 'op.numero', type: 'string'},
      //     id_grupo: 1,
      //     grid: false,
      //     form: false
      // },
      //
      // {
      //     config: {
      //         name: 'tiene_form500',
      //         fieldLabel: 'Form 500',
      //         allowBlank: true,
      //         anchor: '80%',
      //         gwidth: 70,
      //         maxLength: 4
      //     },
      //     type: 'NumberField',
      //     filters: {pfiltro: 'op.numero', type: 'string'},
      //     id_grupo: 1,
      //     grid: false,
      //     form: false
      // },
      {
          config: {
              name: 'nro_cuota',
              fieldLabel: 'Cuo. N#',
              allowBlank: true,
              gwidth: 70,
              renderer: function (value, p, record) {
                    return String.format('<b><font color="green">{0}</font></b>', value);
              },
              maxLength: 4
          },
          type: 'NumberField',
          filters: {pfiltro: 'plapa.nro_cuota', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'estado',
              fieldLabel: 'Estado - (Rev)',
              allowBlank: true,
              renderer: function (value_ori, p, record) {

                  var value = value_ori;
                  if (value_ori == 'pagado') {
                      value = 'contabilizado '
                  }

                  if (record.data.total_prorrateado != record.data.monto_ejecutar_total_mo || record.data.contador_estados > 1) {
                      return String.format('<div title="Número de revisiones: {1}"><b><font color="red">{0} - ({1})</font></b></div>', value, record.data.contador_estados);
                  }
                  else {
                      return String.format('<div title="Número de revisiones: {1}">{0} - ({1})</div>', value, record.data.contador_estados);
                  }
              },
              anchor: '80%',
              gwidth: 100,
              maxLength: 60
          },
          type: 'Field',
          filters: {pfiltro: 'plapa.estado', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },


      {
          config: {
              name: 'tipo',
              fieldLabel: 'Tipo de Cuota',
              allowBlank: false,
              emptyText: 'Tipo de Cuota',
              renderer: function (value, p, record) {
                  var dato = '';
                  dato = (dato == '' && value == 'devengado') ? 'Devengar' : dato;
                  dato = (dato == '' && value == 'devengado_rrhh') ? 'Devengar' : dato;
                  dato = (dato == '' && value == 'devengado_pagado') ? 'Devengar y pagar (2 cbte)' : dato;

                  dato = (dato == '' && value == 'pagado') ? 'Pagar' : dato;
                  dato = (dato == '' && value == 'pagado_rrhh') ? 'Pagar' : dato;
                  dato = (dato == '' && value == 'anticipo') ? 'Anticipo Fact/Rec' : dato;
                  dato = (dato == '' && value == 'ant_parcial') ? 'Anticipo Parcial' : dato;
                  dato = (dato == '' && value == 'ant_rendicion') ? 'Ant. por Rendir' : dato;
                  dato = (dato == '' && value == 'dev_garantia') ? 'Devolucion de Garantia' : dato;
                  dato = (dato == '' && value == 'dev_garantia_con') ? 'Devolucion de Garantia Gestion Actual' : dato;
                  dato = (dato == '' && value == 'dev_garantia_con_ant') ? 'Devolucion de Garantia Gestion Anterior' : dato;
                  dato = (dato == '' && value == 'ant_aplicado') ? 'Aplicacion de Anticipo' : dato;
                  dato = (dato == '' && value == 'rendicion') ? 'Rendicion Ant.' : dato;
                  dato = (dato == '' && value == 'ret_rendicion') ? 'Detalle de Rendicion' : dato;
                  dato = (dato == '' && value == 'especial') ? 'Pago simple (s/p)' : dato;
                  //17-06-2020 (may) se cambia nombre opcion para un cbte, para combustible usan un comprobante
                  //dato = (dato == '' && value == 'devengado_pagado_1c') ? 'Devengar y pagar (1 cbte)' : dato;
                  dato = (dato == '' && value == 'devengado_pagado_1c') ? 'Aplicación de Anticipo (Combustible)' : dato;
                  return String.format('{0}', dato);
              },

              store: new Ext.data.ArrayStore({
                  fields: ['variable', 'valor'],
                  data: []
              }),

              valueField: 'variable',
              displayField: 'valor',
              forceSelection: true,
              triggerAction: 'all',
              lazyRender: true,
              resizable: true,
              listWidth: '500',
              mode: 'local',
              wisth: 420,
              gwidth: 150,
          },
          type: 'ComboBox',
          filters: {pfiltro: 'plapa.tipo', type: 'string'},
          id_grupo: 0,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'nombre_pago',
              fieldLabel: 'Nombre Pago',
              allowBlank: true,
              anchor: '80%',
              gwidth: 250,
              maxLength: 255
          },
          type: 'TextField',
          filters: {pfiltro: 'plapa.nombre_pago', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'nit',
              fieldLabel: 'Nit',
              allowBlank: true,
              disabled: true,
              anchor: '80%',
              gwidth: 250,
              maxLength: 100
          },
          type: 'TextField',
          filters: {pfiltro: 'pro.nit', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'nro_sol_pago',
              fieldLabel: 'Número',
              allowBlank: true,
              renderer: function (value, p, record) {
                  if (record.data.total_prorrateado != record.data.monto_ejecutar_total_mo) {
                      return String.format('<b><font color="red">{0}</font></b>', value);
                  }
                  else {
                      return String.format('{0}', value);
                  }
              },
              anchor: '80%',
              gwidth: 100,
              maxLength: 60
          },
          type: 'TextField',
          filters: {pfiltro: 'plapa.nro_sol_pago', type: 'string'},
          id_grupo: 1,
          grid: false,
          form: false
      },
      {
          config: {
              name: 'fecha_tentativa',
              fieldLabel: 'Fecha Tent.',
              allowBlank: false,
              gwidth: 80,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_dev', type: 'date'},
          id_grupo: 0,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'id_plantilla',
              fieldLabel: 'Tipo Documento',
              allowBlank: false,
              emptyText: 'Elija una plantilla...',
              store: new Ext.data.JsonStore(
                  {
                      url: '../../sis_parametros/control/Plantilla/listarPlantillaFil',
                      id: 'id_plantilla',
                      root: 'datos',
                      sortInfo: {
                          field: 'desc_plantilla',
                          direction: 'ASC'
                      },
                      totalProperty: 'total',
                      fields: ['id_plantilla',
                          'nro_linea',
                          'desc_plantilla',
                          'tipo', 'sw_tesoro', 'sw_compro', 'sw_monto_excento', 'tipo_excento', 'valor_excento'],
                      remoteSort: true,
                      baseParams: {par_filtro: 'plt.desc_plantilla', sw_compro: 'si', sw_tesoro: 'si'}
                  }),
              tpl: '<tpl for="."><div class="x-combo-list-item"><p>{desc_plantilla}</p></div></tpl>',
              valueField: 'id_plantilla',
              hiddenValue: 'id_plantilla',
              displayField: 'desc_plantilla',
              gdisplayField: 'desc_plantilla',
              listWidth: '280',
              forceSelection: true,
              typeAhead: false,
              triggerAction: 'all',
              lazyRender: true,
              mode: 'remote',
              pageSize: 20,
              queryDelay: 500,

              gwidth: 250,
              minChars: 2,
              renderer: function (value, p, record) {
                  return String.format('{0}', record.data['desc_plantilla']);
              }
          },
          type: 'ComboBox',
          filters: {pfiltro: 'pla.desc_plantilla', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'monto_excento',
              currencyChar: ' ',
              allowNegative: false,
              fieldLabel: 'Monto exento',
              allowBlank: false,
              disabled: true,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          valorInicial: 0,
          filters: {pfiltro: 'plapa.monto_excento', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'id_depto_lb',
              hiddenName: 'id_depto_lb',
              //url: '../../sis_parametros/control/Depto/listarDepto',
              origen: 'DEPTO',
              allowBlank: false,
              fieldLabel: 'Libro de bancos destino',
              disabled: false,
              width: '80%',
              baseParams: {estado: 'activo', codigo_subsistema: 'TES', modulo: 'LB', tipo_filtro: 'DEPTO_UO'},
              gdisplayField: 'desc_depto_lb',
              gwidth: 120
          },
          //type:'TrigguerCombo',
          filters: {pfiltro: 'depto.nombre', type: 'string'},
          type: 'ComboRec',
          id_grupo: 1,
          form: true,
          grid: true
      },
      {
          config: {
              name: 'id_cuenta_bancaria',
              fieldLabel: 'Cuenta Bancaria Pago (BOA)',
              allowBlank: false,
              resizable: true,
              emptyText: 'Elija una Cuenta...',
              store: new Ext.data.JsonStore(
                  {
                      url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancariaUsuario',
                      id: 'id_cuenta_bancaria',
                      root: 'datos',
                      sortInfo: {
                          field: 'id_cuenta_bancaria',
                          direction: 'ASC'
                      },
                      totalProperty: 'total',
                      fields: ['id_cuenta_bancaria', 'nro_cuenta', 'nombre_institucion', 'codigo_moneda', 'centro', 'denominacion'],
                      remoteSort: true,
                      baseParams: {
                          par_filtro: 'nro_cuenta', centro: 'otro'
                      }
                  }),
              tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>Moneda: {codigo_moneda}, {nombre_institucion}</p><p>{denominacion}, Centro: {centro}</p></div></tpl>',
              valueField: 'id_cuenta_bancaria',
              hiddenValue: 'id_cuenta_bancaria',
              displayField: 'nro_cuenta',
              gdisplayField: 'desc_cuenta_bancaria',
              listWidth: '280',
              forceSelection: true,
              typeAhead: false,
              triggerAction: 'all',
              lazyRender: true,
              mode: 'remote',
              pageSize: 20,
              queryDelay: 500,
              gwidth: 250,
              minChars: 2,
              renderer: function (value, p, record) {
                  return String.format('{0}', record.data['desc_cuenta_bancaria']);
              }
          },
          type: 'ComboBox',
          filters: {pfiltro: 'cb.nro_cuenta', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'id_cuenta_bancaria_mov',
              fieldLabel: 'Depósito',
              allowBlank: true,
              emptyText: 'Depósito...',
              store: new Ext.data.JsonStore({
                  url: '../../sis_tesoreria/control/TsLibroBancos/listarTsLibroBancosDepositosConSaldo',
                  id: 'id_cuenta_bancaria_mov',
                  root: 'datos',
                  sortInfo: {
                      field: 'fecha',
                      direction: 'DESC'
                  },
                  totalProperty: 'total',
                  fields: ['id_libro_bancos', 'id_cuenta_bancaria', 'fecha', 'detalle', 'observaciones', 'importe_deposito', 'saldo'],
                  remoteSort: true,
                  baseParams: {par_filtro: 'detalle#observaciones#fecha'}
              }),
              valueField: 'id_libro_bancos',
              displayField: 'importe_deposito',
              gdisplayField: 'desc_deposito',
              hiddenName: 'id_cuenta_bancaria_mov',
              forceSelection: true,
              typeAhead: false,
              triggerAction: 'all',
              listWidth: 350,
              lazyRender: true,
              mode: 'remote',
              pageSize: 10,
              queryDelay: 1000,
              anchor: '100%',
              gwidth: 200,
              minChars: 2,
              tpl: '<tpl for="."><div class="x-combo-list-item"><p>{detalle}</p><p>Fecha:<strong>{fecha}</strong></p><p>Importe:<strong>{importe_deposito}</strong></p><p>Saldo:<strong>{saldo}</strong></p></div></tpl>',
              renderer: function (value, p, record) {
                  return String.format('{0}', record.data['desc_deposito']);
              }
          },
          type: 'ComboBox',
          filters: {pfiltro: 'cbanmo.detalle#cbanmo.nro_doc_tipo', type: 'string'},
          id_grupo: 1,
          grid: false,
          form: false
      },
      {
          config:{
              name: 'forma_pago',
              fieldLabel: 'Forma de Pago',
              allowBlank: false,
              emptyText:'Forma de Pago...',
              store:new Ext.data.JsonStore(
                  {
                      url: '../../sis_parametros/control/FormaPago/listarFormaPagofil',
                      id: 'id_forma_pago',
                      root:'datos',
                      sortInfo:{
                          field:'orden',
                          direction:'ASC'
                      },
                      totalProperty:'total',
                      fields: ['id_forma_pago','desc_forma_pago','observaciones','cod_inter','codigo', 'orden'],
                      remoteSort: true,
                      baseParams:{par_filtro:'desc_forma_pago#codigo', cheque: 'no'}
                  }),
              tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_forma_pago}</p></div></tpl>',
              valueField: 'codigo',
              hiddenValue: 'id_forma_pago',
              displayField: 'codigo',
              gdisplayField:'codigo',
              listWidth:'280',
              forceSelection:true,
              typeAhead: false,
              triggerAction: 'all',
              lazyRender:true,
              mode:'remote',
              pageSize:20,
              queryDelay:500,

              gwidth: 250,
              minChars:2,
              renderer:function (value, p, record){return String.format('{0}', record.data['forma_pago']);}
          },
          type:'ComboBox',
          filters:{pfiltro:'plapa.forma_pago',type:'string'},
          id_grupo:1,
          grid:false,
          form:true
      },
      {
          config: {
              name: 'nro_cheque',
              fieldLabel: 'Número Cheque',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              maxLength: 255
          },
          type: 'NumberField',
          filters: {pfiltro: 'plapa.nro_cheque', type: 'numeric'},
          id_grupo: 1,
          grid: false,
          form: true
      },

      // {
      //     config:{
      //
      //         name:'nro_cuenta_bancaria',
      //         fieldLabel:'Cuenta Bancaria(Prov.)',
      //         allowBlank:true,
      //         emptyText:'Elija una opción...',
      //         resizable:true,
      //         // dato: 'reclamo',
      //         qtip:'Ingrese la Cuenta Bancaria Dest.,si no se encuentra la opción deseada registre Nuevo con el botón de Lupa.',
      //         store: new Ext.data.JsonStore({
      //             url: '../../sis_parametros/control/ProveedorCtaBancaria/listarProveedorCtaBancariaActivo',
      //             id: 'id_proveedor_cta_bancaria',
      //             root: 'datos',
      //             sortInfo:{
      //                 field: 'nro_cuenta',
      //                 direction: 'ASC'
      //             },
      //             totalProperty: 'total',
      //             fields: ['id_proveedor_cta_bancaria','nro_cuenta', 'id_proveedor'],
      //             // turn on remote sorting
      //             remoteSort: true,
      //             baseParams:{par_filtro:'nro_cuenta'}
      //         }),
      //         valueField: 'nro_cuenta',
      //         displayField: 'nro_cuenta',
      //         gdisplayField:'nro_cuenta',//mapea al store del grid
      //         tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nro_cuenta}</p></div></tpl>',
      //         hiddenName: 'id_proveedor_cta_bancaria',
      //         forceSelection:true,
      //         typeAhead: false,
      //         triggerAction: 'all',
      //         lazyRender:true,
      //         mode:'remote',
      //         pageSize:10,
      //         queryDelay:1000,
      //         // width:250,
      //         gwidth: 250,
      //         minChars:1,
      //         turl:'../../../sis_parametros/vista/proveedor_cta_bancaria/FormProvCta.php',
      //         ttitle:'Banco y Cuenta Bancaria Dest.',
      //         tconfig:{width: '35%' ,height:'50%'},
      //         tdata:{},
      //         tcls:'FormProvCta',
      //         pid:this.idContenedor,
      //
      //         renderer:function (value, p, record){return String.format('{0}', record.data['nro_cuenta_bancaria']);}
      //     },
      //     type:'TrigguerCombo',
      //     bottom_filter:true,
      //     id_grupo:1,
      //     filters:{
      //         pfiltro:'plapa.nro_cuenta_bancaria',
      //         type:'string'
      //     },
      //     grid:true,
      //     form:false
      // },

      {
          config: {
              name: 'id_proveedor_cta_bancaria',
              fieldLabel: 'Cuenta Bancaria(Prov.)',
              allowBlank: true,
              resizable:true,
              emptyText: 'Elija una Cuenta...',
              store: new Ext.data.JsonStore(
                  {
                      url: '../../sis_parametros/control/ProveedorCtaBancaria/listarProveedorCtaBancariaActivo',
                      id: 'id_proveedor_cta_bancaria',
                      root: 'datos',
                      sortInfo: {
                          field: 'prioridad',
                          direction: 'ASC'
                      },
                      totalProperty: 'total',
                      fields: ['id_proveedor_cta_bancaria', 'nro_cuenta', 'banco_beneficiario','prioridad'],
                      remoteSort: true,
                      baseParams: {
                          par_filtro: 'id_proveedor#nro_cuenta'
                      }
                  }),
              tpl: '<tpl for="."><div class="x-combo-list-item"><b>Nro Cuenta: {nro_cuenta}</b></p><p><b>Banco Beneficiario:</b> {banco_beneficiario}</p><p><b>Prioridad:</b>{prioridad}</p><p></div></tpl>',
              valueField: 'id_proveedor_cta_bancaria',
              displayField: 'nro_cuenta',
              gdisplayField: 'nro_cuenta_bancaria',
              hiddenName: 'id_proveedor_cta_bancaria',
              forceSelection:true,
              typeAhead: false,
              triggerAction: 'all',
              lazyRender:true,
              mode:'remote',
              pageSize:10,
              queryDelay:1000,
              // width:250,
              gwidth: 250,
              listWidth: '290',
              // minChars:2,
              lazyRender:true,
              // tinit: true,
              // tname:'id_proveedor_cta_bancaria',
              // tasignacion:true,
              turl:'../../../sis_parametros/vista/proveedor_cta_bancaria/FormProvCta.php',
              ttitle:'Banco y Cuenta Bancaria Dest.',
              tconfig:{width: '35%' ,height:'50%'},
              tdata:{},
              tcls:'FormProvCta',
              pid:this.idContenedor,
              renderer: function (value, p, record) {
                  return String.format('{0}', record.data['nro_cuenta_bancaria']);
              }
          },
          type: 'TrigguerCombo',
          filters: {pfiltro: 'plapa.nro_cuenta_bancaria', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'desc_moneda',
              fieldLabel: 'Moneda',
              gwidth: 70,
          },
          type: 'Field',
          id_grupo: 1,
          filters: {
              pfiltro: 'mon.codigo',
              type: 'string'
          },
          grid: true,
          form: false
      },


      {
          config: {
              name: 'monto',
              currencyChar: ' ',
              allowNegative: false,
              fieldLabel: 'Monto a Pagar',
              allowBlank: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'monto_anticipo',
              currencyChar: ' ',
              allowNegative: false,
              qtip: 'Este monto incrementa el liquido pagable y figura como un anticipo',
              fieldLabel: 'Monto anticipado',
              allowBlank: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto_anticipo', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'descuento_anticipo',
              qtip: 'Si anteriormente se le dio un anticipo parcial,  en este campo se colocan las retenciones para recuperar el anticipo',
              currencyChar: ' ',
              fieldLabel: 'Desc. Anticipo',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.descuento_anticipo', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config : {
              name:'porc_monto_retgar',
              fieldLabel: 'Porcentaje Ret. Garantia',
              currencyChar: ' ',
              resizable:true,
              allowBlank:true,
              gwidth: 100,
              emptyText:'Seleccione ...',
              store: new Ext.data.JsonStore({
                  url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                  id: 'id_catalogo',
                  root: 'datos',
                  sortInfo:{
                      field: 'orden',
                      direction: 'ASC'
                  },
                  totalProperty: 'total',
                  fields: ['id_catalogo','codigo','descripcion'],
                  // turn on remote sorting
                  remoteSort: true,
                  baseParams: {par_filtro:'descripcion',cod_subsistema:'TES',catalogo_tipo:'tplan_pago_retencion'}
              }),
              enableMultiSelect:true,
              valueField: 'codigo',
              displayField: 'descripcion',
              gdisplayField: 'porc_monto_retgar',
              forceSelection:true,
              typeAhead: false,
              triggerAction: 'all',
              lazyRender:true,
              mode:'remote',
              pageSize:10,
              listWidth: 350,
              queryDelay:1000
          },
          type : 'ComboBox',
          id_grupo: 1,
          form : true,
          grid: true,
      },
      {
          config: {
              name: 'monto_retgar_mo',
              currencyChar: ' ',
              fieldLabel: 'Ret. Garantia',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto_retgar_mo', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'monto_no_pagado',
              currencyChar: ' ',
              fieldLabel: 'Monto no pagado',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto_no_pagado', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'otros_descuentos',
              currencyChar: ' ',
              fieldLabel: 'Multas o Impuestos Retenidos',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.otros_descuentos', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'descuento_inter_serv',
              currencyChar: ' ',
              fieldLabel: 'Desc. Inter Servicio',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.descuento_inter_serv', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'descuento_ley',
              currencyChar: ' ',
              fieldLabel: 'Decuentos de Ley',
              allowBlank: true,
              readOnly: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.descuento_ley', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'monto_ejecutar_total_mo',
              currencyChar: ' ',
              fieldLabel: 'Monto a Ejecutar',
              allowBlank: true,
              readOnly: true,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto_ejecutar_total_mo', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },

      {
          config: {
              name: 'monto_establecido',
              currencyChar: ' ',
              fieldLabel: 'Monto sin IVA',
              allowBlank: true,
              readOnly: true,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.monto_establecido', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: false
      },

      {
          config: {
              name: 'liquido_pagable',
              currencyChar: ' ',
              fieldLabel: 'Liquido Pagable',
              allowBlank: true,
              readOnly: true,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.liquido_pagable', type: 'numeric'},
          id_grupo: 1,
          grid: true,
          form: true
      },

      {
          config: {
              name: 'obs_wf',
              fieldLabel: 'Obs',
              allowBlank: true,
              anchor: '80%',
              gwidth: 300,
              maxLength: 1000
          },
          type: 'TextArea',
          filters: {pfiltro: 'ew.obs', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'fecha_dev',
              fieldLabel: 'Fecha Dev.',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_dev', type: 'date'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'fecha_pag',
              fieldLabel: 'Fecha Pago',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_pag', type: 'date'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'obs_monto_no_pagado',
              //04-07-2019 cambiar
              // fieldLabel: 'Obs. Pago',
              fieldLabel: 'Glosa',
              qtip: 'Estas observaciones van a la glosa del comprobante que se genera',
              allowBlank: true,
              // anchor: '80%',
              width: 280,
              gwidth: 300,
              maxLength: 500
          },
          type: 'TextArea',
          filters: {pfiltro: 'plapa.obs_monto_no_pagado', type: 'string'},
          id_grupo: 4,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'obs_descuentos_anticipo',
              fieldLabel: 'Obs. Desc. Antic.',
              allowBlank: true,
              // anchor: '80%',
              width: 280,
              gwidth: 250,
              maxLength: 300
          },
          type: 'TextArea',
          filters: {pfiltro: 'plapa.obs_descuentos_anticipo', type: 'string'},
          id_grupo: 4,
          grid: true,
          form: true
      },

      {
          config: {
              name: 'obs_otros_descuentos',
              fieldLabel: 'Obs. otros desc.',
              allowBlank: true,
              width: 280,
              // anchor: '80%',
              gwidth: 300,
              maxLength: 300
          },
          type: 'TextArea',
          filters: {pfiltro: 'plapa.obs_otros_descuentos', type: 'string'},
          id_grupo: 4,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'obs_descuentos_ley',
              fieldLabel: 'Obs. desc. ley',
              allowBlank: true,
              // anchor: '80%',
              width: 280,
              gwidth: 300,
              readOnly: true,
              maxLength: 300
          },
          type: 'TextArea',
          filters: {pfiltro: 'plapa.obs_descuentos_ley', type: 'string'},
          id_grupo: 4,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'obs_descuento_inter_serv',
              fieldLabel: 'Obs. desc. inter. serv.',
              allowBlank: true,
              // anchor: '80%',
              width: 280,
              gwidth: 300,
              maxLength: 300
          },
          type: 'TextArea',
          filters: {pfiltro: 'plapa.obs_descuento_inter_serv', type: 'string'},
          id_grupo: 4,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'desc_funcionario1',
              fieldLabel: 'Fun Solicitante',
              allowBlank: true,
              anchor: '80%',
              gwidth: 250,
              maxLength: 255
          },
          type: 'TextField',
          filters: {pfiltro: 'fun.desc_funcionario1', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'monto_ajuste_ag',
              currencyChar: ' ',
              fieldLabel: 'Ajuste Anterior Gestión',
              qtip: 'Si en la anterior gestión el proveedor quedo con anticipo a favor de nuestra empresa, acá colocamos el monto que queremos cubrir con dicho sobrante',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.descuento_inter_serv', type: 'numeric'},
          id_grupo: 2,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'monto_ajuste_siguiente_pag',
              currencyChar: ' ',
              fieldLabel: 'Ajuste Anticipo siguiente',
              qtip: 'Si el anticipo no alcanza para cubrir, acá colocamos el monto a cubrir con el siguiente anticipo',
              allowBlank: true,
              allowNegative: false,
              gwidth: 100,
              maxLength: 1245186
          },
          type: 'MoneyField',
          filters: {pfiltro: 'plapa.descuento_inter_serv', type: 'numeric'},
          id_grupo: 2,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'fecha_costo_ini',
              fieldLabel: 'Fecha Inicio.',
              allowBlank: false,
              gwidth: 100,
              format: 'd/m/Y',
							disabled: true,
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_costo_ini', type: 'date'},
          id_grupo: 3,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'fecha_costo_fin',
              fieldLabel: 'Fecha Fin.',
              allowBlank: false,
              gwidth: 100,
              format: 'd/m/Y',
							disabled: true,
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_costo_fin', type: 'date'},
          id_grupo: 3,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'fecha_conclusion_pago',
              fieldLabel: 'Fecha Vencimiento de Pago',
              allowBlank: true,
              gwidth: 100,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_conclusion_pago', type: 'date'},
          id_grupo: 3,
          grid: true,
          form: true
      },
      {
          config: {
              name: 'fecha_cbte_ini',
              fieldLabel: 'Fecha Inicial(cbte)',
              allowBlank: true,
              width: 100,
              gwidth: 120,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {
              pfiltro: 'tcon.fecha_costo_ini',
              type: 'date'
          },
          id_grupo: 3,
          egrid: true,
          grid: true,
          form: false
      }, {
          config: {
              name: 'fecha_cbte_fin',
              fieldLabel: 'Fecha Final(cbte)',
              allowBlank: true,
              width: 100,
              gwidth: 120,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y') : ''
              }
          },
          type: 'DateField',
          filters: {
              pfiltro: 'tcon.fecha_costo_fin',
              type: 'date'
          },
          id_grupo: 3,
          egrid: true,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'id_multa',
              fieldLabel: 'Tipo de Multa',
              allowBlank: true,
              emptyText: 'Tipo de Multa...',
              store: new Ext.data.JsonStore(
                  {
                      url: '../../sis_sigep/control/Multa/listarMulta',
                      id: 'id_multa',
                      root: 'datos',
                      sortInfo: {
                          field: 'codigo',
                          direction: 'ASC'
                      },
                      totalProperty: 'total',
                      fields: ['id_multa', 'codigo', 'desc_multa'],
                      remoteSort: true,
                      baseParams: {par_filtro: 'desc_multa#codigo'}
                  }),
              tpl: '<tpl for="."><div class="x-combo-list-item"><p>{codigo}  {desc_multa}</p></div></tpl>',
              valueField: 'id_multa',
              hiddenValue: 'id_multa',
              displayField: 'desc_multa',
              gdisplayField: 'desc_multa',
              listWidth: '280',
              forceSelection: true,
              typeAhead: false,
              triggerAction: 'all',
              resizable: true,
              lazyRender: true,
              mode: 'remote',
              pageSize: 20,
              queryDelay: 500,
              width: 280,
              gwidth: 280,
              minChars: 2,
              renderer: function (value, p, record) {
                  return String.format('{0}', record.data['desc_multa']);
              }
          },
          type: 'ComboBox',
          filters: {pfiltro: 'plapa.desc_multa', type: 'string'},
          id_grupo: 5,
          grid: true,
          form: true
      },

      {
          config: {
              name: 'funcionario_wf',
              fieldLabel: 'Funcionario Res WF',
              anchor: '80%',
              gwidth: 250
          },
          type: 'Field',
          filters: {pfiltro: 'funwf.desc_funcionario1', type: 'string'},
          //bottom_filter: true,
          id_grupo: 1,
          grid: true,
          form: false
      },

      {
          config: {
              name: 'estado_reg',
              fieldLabel: 'Estado Reg.',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              maxLength: 10
          },
          type: 'TextField',
          filters: {pfiltro: 'plapa.estado_reg', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'fecha_reg',
              fieldLabel: 'Fecha creación',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y H:i:s') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_reg', type: 'date'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'usr_reg',
              fieldLabel: 'Creado por',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              maxLength: 4
          },
          type: 'NumberField',
          filters: {pfiltro: 'usu1.cuenta', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'fecha_mod',
              fieldLabel: 'Fecha Modif.',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              format: 'd/m/Y',
              renderer: function (value, p, record) {
                  return value ? value.dateFormat('d/m/Y H:i:s') : ''
              }
          },
          type: 'DateField',
          filters: {pfiltro: 'plapa.fecha_mod', type: 'date'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              name: 'usr_mod',
              fieldLabel: 'Modificado por',
              allowBlank: true,
              anchor: '80%',
              gwidth: 100,
              maxLength: 4
          },
          type: 'NumberField',
          filters: {pfiltro: 'usu2.cuenta', type: 'string'},
          id_grupo: 1,
          grid: true,
          form: false
      },
      {
          config: {
              labelSeparator: '',
              inputType: 'hidden',
              name: 'desc_depto_conta_pp'
          },
          type: 'Field',
          form: true
      }
  ],
	tam_pag:50,
	fheight: '90%',
	fwidth: '85%',
	title:'Cuotas Devengadas',
	ActSave:'../../sis_tesoreria/control/PlanPago/insertarPlanPago',
	ActDel:'../../sis_tesoreria/control/PlanPago/eliminarPlanPago',
	ActList:'../../sis_tesoreria/control/CuotasDevengadas/listarCuotas',
	id_store:'id_plan_pago',
	fields: [
    {name: 'id_plan_pago', type: 'numeric'},
    {name: 'id_int_comprobante', type: 'numeric'},
    'id_obligacion_pago',
		{name: 'estado_reg', type: 'string'},
    {name: 'tipo_interfaz', type: 'string'},
    {name: 'nro_cuota', type: 'numeric'},
    {name: 'monto_ejecutar_totamonto_ejecutar_totall_mb', type: 'numeric'},
    {name: 'nro_sol_pago', type: 'string'},
    {name: 'tipo_cambio', type: 'numeric'},
    {name: 'fecha_pag', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'id_proceso_wf', type: 'numeric'},
    {name: 'fecha_tentativa', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'fecha_dev', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'estado', type: 'string'},
    {name: 'tipo_pago', type: 'string'},

    {name: 'descuento_anticipo_mb', type: 'numeric'},
    {name: 'obs_descuentos_anticipo', type: 'string'},
    {name: 'id_plan_pago_fk', type: 'numeric'},

    {name: 'id_plantilla', type: 'numeric'},
    {name: 'descuento_anticipo', type: 'numeric'},
    {name: 'otros_descuentos', type: 'numeric'},
    {name: 'tipo', type: 'string'},
    {name: 'obs_monto_no_pagado', type: 'string'},
    {name: 'obs_otros_descuentos', type: 'string'},
    {name: 'monto', type: 'numeric'},
    {name: 'id_comprobante', type: 'numeric'},
    {name: 'nombre_pago', type: 'string'},
    {name: 'monto_no_pagado_mb', type: 'numeric'},
    {name: 'monto_mb', type: 'numeric'},
    {name: 'id_estado_wf', type: 'numeric'},
    {name: 'id_cuenta_bancaria', type: 'numeric'},
    {name: 'otros_descuentos_mb', type: 'numeric'},
    {name: 'total_prorrateado', type: 'numeric'},
    {name: 'monto_ejecutar_total_mo', type: 'numeric'},
    {name: 'forma_pago', type: 'string'},
    {name: 'monto_no_pagado', type: 'numeric'},
    {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
    {name: 'id_usuario_reg', type: 'numeric'},
    {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
    {name: 'id_usuario_mod', type: 'numeric'},
    {name: 'usr_reg', type: 'string'},
    {name: 'usr_mod', type: 'string'},
    'liquido_pagable',
    {name: 'total_pagado', type: 'numeric'},
    {name: 'monto_retgar_mo', type: 'numeric'},
    {name: 'descuento_ley', type: 'numeric'},
    {name: 'porc_descuento_ley', type: 'numeric'},
    {name: 'porc_monto_excento_var', type: 'numeric'},

    'desc_plantilla', 'desc_cuenta_bancaria', 'sinc_presupuesto', 'obs_descuentos_ley',
    {name: 'nro_cheque', type: 'numeric'},
    {name: 'nro_cuenta_bancaria', type: 'string'},
    {name: 'id_cuenta_bancaria_mov', type: 'numeric'},
		{name: 'desc_deposito', type: 'string'},
    {name: 'num_tramite', type: 'string'},
    'numero_op',
    'id_estado_wf',
    'id_depto_conta',
    'id_moneda', 'tipo_moneda', 'desc_moneda',
    'monto_excento',
    'proc_monto_excento_var', 'obs_wf', 'descuento_inter_serv',
    'obs_descuento_inter_serv',
    {name: 'porc_monto_retgar', type: 'numeric'},
    'desc_funcionario1', 'revisado_asistente',
    {name: 'fecha_conformidad', type: 'date', dateFormat: 'Y-m-d'},
    'conformidad',
    'tipo_obligacion',
    'monto_ajuste_ag',
    'monto_ajuste_siguiente_pag', 'pago_variable', 'monto_anticipo', 'contador_estados',
    {name: 'fecha_costo_ini', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'fecha_costo_fin', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'fecha_conclusion_pago', type: 'date', dateFormat: 'Y-m-d'},
    'id_depto_conta_pp', 'desc_depto_conta_pp', 'funcionario_wf', 'tiene_form500',
    'id_depto_lb', 'desc_depto_lb', 'prioridad_lp', {name: 'ultima_cuota_dev', type: 'numeric'},
    'nro_cbte',
    'c31',
    {name: 'id_gestion', type: 'numeric'},
    {name: 'es_ultima_cuota', type: 'boolean'},
    {name: 'fecha_cbte_ini', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'fecha_cbte_fin', type: 'date', dateFormat: 'Y-m-d'},
    {name: 'monto_establecido', type: 'numeric'},
    {name: 'id_proveedor', type: 'numeric'},
    {name: 'nit', type: 'string'},
    'id_proveedor_cta_bancaria',
    'id_multa',
    'desc_multa',
    'id_obligacion_pago_extendida'
	],

	arrayStore: {
			'TODOS': [
					['devengado_pagado', 'Devengar y pagar (2 comprobantes)'],
					['devengado_pagado_1c', 'Caso especial'],
					['devengado', 'Devengar'],
					['devengado_rrhh', 'Devengar RH'],
					['rendicion', 'Agrupar Dev y Pagar (Agrupa varios documentos)'], //es similr a un devengar y pagar pero no genera prorrateo directamente
					['anticipo', 'Anticipo Fact/Rec (No ejecuta presupuesto, necesita Documento)'],
					['ant_parcial', 'Anticipo Parcial(No ejecuta presupuesto, Con retenciones parciales en cada pago)'],
					['pagado', 'Pagar'],
					['pagado_rrh', 'Pagar RH'],
					['ant_aplicado', 'Aplicacion de Anticipo'],
					['dev_garantia', 'Devolucion de Garantia'],
					['det_rendicion', 'Rendicion Ant'],
					['especial', 'Pago simple (sin efecto presupuestario)']
			],

			'INICIAL': [
					['devengado_pagado', 'Devengar y pagar (2 comprobantes)'],
					['devengado', 'Devengar'],
					//['devengado_rrhh','Devengar RH'],
					['dev_garantia', 'Devolucion de Garantia'], //es similr a un devengar y pagar pero no genera prorrateo directamente
					['dev_garantia_con', 'Devolucion de Garantia Gestion Actual'],
					['dev_garantia_con_ant', 'Devolucion de Garantia Gestion Anterior'],
					['anticipo', 'Anticipo Fact/Rec (No ejecuta presupuesto, necesita Documento)'],
					['ant_parcial', 'Anticipo Parcial(No ejecuta presupuesto, Con retenciones parciales en cada pago)'],
					//17-06-2020 (may) se cambia nombre opcion para un cbte, para combustible usan un comprobante
					//['devengado_pagado_1c', 'Devengar y pagar (1 comprobante)'],
					['devengado_pagado_1c', 'Aplicación de Anticipo (Combustible)'],
					//03-12-2020 (may) se aumenta opcion Pagar para que se registre sin necesidad de relacionar su cuota
					['pagado', 'Pagar']
			],

			'ANT_PARCIAL': [
					['ant_parcial', 'Anticipo Parcial(No ejecuta presupuesto, Con retenciones parciales en cada pago)']
			],

			'DEVENGAR': [['pagado', 'Pagar'],
					['pagado_rrh', 'Pagar RH'],
					['ant_aplicado', 'Aplicacion de Anticipo']],

			'ANTICIPO': [['ant_aplicado', 'Aplicacion de Anticipo']],

			'RENDICION': [['det_rendicion', 'Rendicion Ant']],

			'ESPECIAL': [['especial', 'Pago simple (sin efecto presupuestario)']]

	},

	sortInfo : {
			field : 'id_plan_pago',
			direction : 'ASC'
	},

	arrayDefaultColumHidden: ['id_fecha_reg', 'id_fecha_mod',
			'fecha_mod', 'usr_reg', 'usr_mod', 'estado_reg', 'fecha_reg', 'numero_op', 'id_plantilla', 'monto_excento', 'forma_pago', 'nro_cheque', 'nro_cuenta_bancaria',
			'descuento_anticipo', 'monto_retgar_mo', 'monto_no_pagado', 'otros_descuentos', 'descuento_inter_serv', 'descuento_ley', 'id_depto_lb',
			'id_depto_lb', 'id_cuenta_bancaria', 'obs_wf', 'fecha_dev', 'fecha_pag', 'obs_descuentos_anticipo', 'obs_monto_no_pagado',
			'obs_otros_descuentos', 'obs_descuentos_ley', 'obs_descuento_inter_serv', 'monto_ajuste_ag', 'monto_ajuste_siguiente_pag', 'fecha_costo_ini',
			'fecha_costo_fin', 'funcionario_wf', 'monto_anticipo', 'monto', 'monto_ejecutar_total_mo', 'monto_establecido','nit','porc_monto_retgar'],


  rowExpander: new Ext.ux.grid.RowExpander({
      tpl: new Ext.Template(
          '<br>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obligación de pago:&nbsp;&nbsp;</b> {numero_op}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Documento:&nbsp;&nbsp;</b> {desc_plantilla}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Monto:&nbsp;&nbsp;</b> {monto}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Monto Excento:&nbsp;&nbsp;</b> {monto_excento}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Anticipo:&nbsp;&nbsp;</b> {monto_anticipo}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Descuento Anticipo:&nbsp;&nbsp;</b> {descuento_anticipo}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Retención de garantia:&nbsp;&nbsp;</b> {monto_retgar_mo}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Monto que no se pagara:&nbsp;&nbsp;</b> {monto_no_pagado}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Multas:&nbsp;&nbsp;</b> {otros_descuentos}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Descuento por intercambio de servicios:&nbsp;&nbsp;</b> {descuento_inter_serv}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Descuentos de Ley:&nbsp;&nbsp;</b> {descuento_ley}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total a ejecutar presupuestariamente:&nbsp;&nbsp;</b> {monto_ejecutar_total_mo}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Liquido pagable:&nbsp;&nbsp;</b> {liquido_pagable}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Libro de Bancos:&nbsp;&nbsp;</b> {desc_depto_lb}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cuenta bancaria:&nbsp;&nbsp;</b> {desc_cuenta_bancaria}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Deposito:&nbsp;&nbsp;</b> {desc_deposito}</p>',
          '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Intrucciones:&nbsp;&nbsp;</b> {obs_wf}</p><br>'
      )
  }),

	crearFomularioDepto: function () {

			this.cmpDeptoConta = new Ext.form.ComboBox({
					xtype: 'combo',
					name: 'id_depto_conta',
					hiddenName: 'id_depto_conta',
					fieldLabel: 'Depto. Conta.',
					allowBlank: false,
					emptyText: 'Elija un Depto',
					store: new Ext.data.JsonStore(
							{
									//url: '../../sis_tesoreria/control/ObligacionPago/listarDeptoFiltradoObligacionPago',
									url: '../../sis_parametros/control/Depto/listarDepto',
									id: 'id_depto',
									root: 'datos',
									sortInfo: {
											field: 'deppto.nombre',
											direction: 'ASC'
									},
									totalProperty: 'total',
									fields: ['id_depto', 'nombre'],
									// turn on remote sorting
									remoteSort: true,
									baseParams: {
											par_filtro: 'deppto.nombre#deppto.codigo',
											estado: 'activo',
											codigo_subsistema: 'CONTA'
									}
							}),
					valueField: 'id_depto',
					displayField: 'nombre',
					tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p></div></tpl>',
					hiddenName: 'id_depto_tes',
					forceSelection: true,
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'remote',
					pageSize: 10,
					queryDelay: 1000,
					width: 250,
					listWidth: '280',
					minChars: 2
			});

			this.formDEPTO = new Ext.form.FormPanel({
					baseCls: 'x-plain',
					autoDestroy: true,
					layout: 'form',
					items: [this.cmpDeptoConta]
			});


			this.wDEPTO = new Ext.Window({
					title: 'Depto Tesoreria',
					collapsible: true,
					maximizable: true,
					autoDestroy: true,
					width: 400,
					height: 200,
					layout: 'fit',
					plain: true,
					bodyStyle: 'padding:5px;',
					buttonAlign: 'center',
					items: this.formDEPTO,
					modal: true,
					closeAction: 'hide',
					buttons: [{
							text: 'Guardar',
							handler: this.onSubmitDepto,
							scope: this

					}, {
							text: 'Cancelar',
							handler: function () {
									this.wDEPTO.hide()
							},
							scope: this
					}]
			});

	},


	crearFomularioDeptoConvertido: function () {

			this.cmpDeptoContaConvertido = new Ext.form.ComboBox({
					xtype: 'combo',
					name: 'id_depto_conta',
					hiddenName: 'id_depto_conta',
					fieldLabel: 'Depto. Conta.',
					allowBlank: false,
					emptyText: 'Elija un Depto',
					store: new Ext.data.JsonStore(
							{
									//url: '../../sis_tesoreria/control/ObligacionPago/listarDeptoFiltradoObligacionPago',
									url: '../../sis_parametros/control/Depto/listarDepto',
									id: 'id_depto',
									root: 'datos',
									sortInfo: {
											field: 'deppto.nombre',
											direction: 'ASC'
									},
									totalProperty: 'total',
									fields: ['id_depto', 'nombre'],
									// turn on remote sorting
									remoteSort: true,
									baseParams: {
											par_filtro: 'deppto.nombre#deppto.codigo',
											estado: 'activo',
											codigo_subsistema: 'CONTA'
									}
							}),
					valueField: 'id_depto',
					displayField: 'nombre',
					tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nombre}</p></div></tpl>',
					hiddenName: 'id_depto_tes',
					forceSelection: true,
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'remote',
					pageSize: 10,
					queryDelay: 1000,
					width: 250,
					listWidth: '280',
					minChars: 2
			});

			this.formDEPTO2 = new Ext.form.FormPanel({
					baseCls: 'x-plain',
					autoDestroy: true,
					layout: 'form',
					items: [this.cmpDeptoContaConvertido]
			});


			this.wDEPTO2 = new Ext.Window({
					title: 'Depto Tesoreria',
					collapsible: true,
					maximizable: true,
					autoDestroy: true,
					width: 400,
					height: 200,
					layout: 'fit',
					plain: true,
					bodyStyle: 'padding:5px;',
					buttonAlign: 'center',
					items: this.formDEPTO2,
					modal: true,
					closeAction: 'hide',
					buttons: [{
							text: 'Guardar',
							handler: this.onSubmitDeptoConvertido,
							scope: this

					}, {
							text: 'Cancelar',
							handler: function () {
									this.wDEPTO2.hide();
									this.reload();
							},
							scope: this
					}]
			});

	},



	onBtnDevPag: function () {
			var data = this.getSelectedData();
			this.wDEPTO.show();
			this.cmpDeptoConta.reset();
			this.cmpDeptoConta.store.baseParams = Ext.apply(this.cmpDeptoConta.store.baseParams, {id_depto_origen: data.id_depto_lb})
			this.cmpDeptoConta.modificado = true;
			this.store.baseParams.accion = 'generar_comprobante';
	},


	onConvertir: function () {
			var data = this.getSelectedData();
			Phx.CP.loadingShow();
			Ext.Ajax.request({
					url: '../../sis_tesoreria/control/CuotasDevengadas/solicitarDevPag',
					params: {
							id_plan_pago: data.id_plan_pago,
							id_depto_conta: null,
							estado_interfaz: this.store.baseParams.pes_estado,
							accion: 'convertir'
					},
					success: this.successSincConvertido,
					failure: this.conexionFailure,
					timeout: this.timeout,
					scope: this
			})
	},

	onSubmitDepto: function (x, y, id_depto_conta) {
			var data = this.getSelectedData();
			if (this.formDEPTO.getForm().isValid() || id_depto_conta) {
					Phx.CP.loadingShow();
					Ext.Ajax.request({
							url: '../../sis_tesoreria/control/CuotasDevengadas/solicitarDevPag',
							params: {
									id_plan_pago: data.id_plan_pago,
									id_depto_conta: id_depto_conta ? id_depto_conta : this.cmpDeptoConta.getValue(),
									estado_interfaz: this.store.baseParams.pes_estado,
									accion: this.store.baseParams.accion
							},
							success: this.successSincGC,
							failure: this.conexionFailure,
							timeout: this.timeout,
							scope: this
					})
			}

	},

	successSincGC: function (resp) {
			Phx.CP.loadingHide();
			this.wDEPTO.hide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if (reg.ROOT.datos.resultado != 'falla') {

					this.reload();
			} else {
					alert(reg.ROOT.datos.mensaje)
			}
	},

	successSincConvertido: function (resp) {
		Phx.CP.loadingHide();
		var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
		var data = this.getSelectedData();

		this.id_plan_pago_convertido = reg.ROOT.datos.id_plan_pago;
		this.wDEPTO2.show();
		this.cmpDeptoContaConvertido.reset();
		this.cmpDeptoContaConvertido.store.baseParams = Ext.apply(this.cmpDeptoContaConvertido.store.baseParams, {id_depto_origen: data.id_depto_lb})
		this.cmpDeptoContaConvertido.modificado = true;
		this.store.baseParams.accion = 'generar_comprobante';
	},


	onSubmitDeptoConvertido: function (x, y, id_depto_conta) {
			var data = this.getSelectedData();
			if (this.formDEPTO2.getForm().isValid() || id_depto_conta) {
					Phx.CP.loadingShow();
					Ext.Ajax.request({
							url: '../../sis_tesoreria/control/CuotasDevengadas/solicitarDevPag',
							params: {
									id_plan_pago: this.id_plan_pago_convertido,
									id_depto_conta: id_depto_conta ? id_depto_conta : this.cmpDeptoConta.getValue(),
									estado_interfaz: this.store.baseParams.pes_estado,
									accion: this.store.baseParams.accion
							},
							success: this.successSincGC2,
							failure: this.conexionFailure,
							timeout: this.timeout,
							scope: this
					})
			}

	},

	successSincGC2: function (resp) {
			Phx.CP.loadingHide();
			this.wDEPTO2.hide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if (reg.ROOT.datos.resultado != 'falla') {

					this.reload();
			} else {
					alert(reg.ROOT.datos.mensaje)
			}
	},


	preparaMenu: function (n) {
			var data = this.getSelectedData();
			var tb = this.tbar;

			Phx.vista.CuotasDevengadas.superclass.preparaMenu.call(this, n);

			if (this.store.baseParams.pes_estado == 'devengado') {
				this.getBoton('SolDevPag').hide(true);
				if (data['estado'] == 'pagado') {
					this.getBoton('GenCuotaDeven').disable();
					this.getBoton('edit').disable();
				}
				else {
					this.getBoton('GenCuotaDeven').enable();
					this.getBoton('edit').enable();
				}

			} else {
				if (data['estado'] == 'pagado') {
					this.getBoton('SolDevPag').disable();
					this.getBoton('edit').disable();
				}
				else {
					this.getBoton('SolDevPag').enable();
					this.getBoton('edit').enable();
				}
			}

					if (data['estado'] == 'pagado') {
							this.getBoton('SolDevPag').disable();
							this.getBoton('edit').disable();

					}
					else {

								this.getBoton('SolDevPag').enable();
								this.getBoton('edit').enable();
								this.getBoton('btnChequeoDocumentosWf').setDisabled(false)
					}

	},

	liberaMenu: function () {
			var tb = Phx.vista.CuotasDevengadas.superclass.liberaMenu.call(this);

			if (tb) {
					this.getBoton('SolDevPag').disable();
					this.getBoton('btnChequeoDocumentosWf').setDisabled(true);
			}
			return tb
	},

	onButtonEdit: function () {
			this.accionFormulario = 'EDIT';
			var data = this.getSelectedData();
			var diaActual = new Date().getDate();
			var mesActual = new Date().getMonth() + 1;
			var añoActual = new Date().getFullYear();

			if (diaActual < 10) {
				diaActual = "0"+diaActual;
			}

			if (mesActual < 10) {
				mesActual = "0"+mesActual;
			}

			var fechaFormateada = diaActual + "/" + mesActual + "/" + añoActual



			//deshabilita el cambio del tipo de pago
			//may 08-01-2020 solo para visto bueno conta puede modificar el tipo de cuota

			console.log('llegamay', data);
			if (data.estado == 'vbconta') {
					this.Cmp.tipo.enable();
			}else{
					this.Cmp.tipo.disable();
			}

			if (data.estado == 'vbfin' || data.estado == 'vbcostos') {
					this.Cmp.id_cuenta_bancaria.allowBlank = true;
			} else {
					this.Cmp.id_cuenta_bancaria.allowBlank = false;
			}

			if (this.Cmp.id_depto_lb.getValue() > 0) {
					this.Cmp.id_cuenta_bancaria.store.baseParams = Ext.apply(this.Cmp.id_cuenta_bancaria.store.baseParams, {
							id_depto_lb: this.Cmp.id_depto_lb.getValue(),
							permiso: 'todos'
					});
					this.Cmp.id_cuenta_bancaria.modificado = true;
			}

			this.Cmp.fecha_tentativa.enable();
			//08-01-2020 (may) modificacion para que pueda listar lo mismo que un new
			//this.Cmp.tipo.store.loadData(this.arrayStore.TODOS);
			this.Cmp.tipo.store.loadData(this.arrayStore.INICIAL);
			this.ocultarGrupo(2); //ocultar el grupo de ajustes

			//08-01-2020 (may) solo en vbconta que todo sea visible para el boton del edit
			//segun el tipo define los campo visibles y no visibles
			if (data.estado != 'vbconta') {
					//this.setTipoPago[data.tipo](this, data);
					this.mostrarGrupo(3);
			} else {
				this.mostrarGrupo(3);
			}

			this.tmp_porc_monto_excento_var = undefined;

			if (data.estado == 'vbsolicitante' || data.estado == 'vbfin' || data.estado == 'vbdeposito' || (data.estado == 'vbcostos' && data['prioridad_lp'] != 3)) {
					this.Cmp.fecha_tentativa.disable();
					// this.Cmp.forma_pago.disable();
					this.Cmp.nombre_pago.disable();
					this.Cmp.nro_cheque.disable();
					// this.Cmp.nro_cuenta_bancaria.disable();
					this.Cmp.id_proveedor_cta_bancaria.disable();
					this.Cmp.monto_no_pagado.disable();

					if (data.estado == 'vbfin' || data.estado == 'vbdeposito') {
							this.Cmp.id_depto_lb.enable();
							this.Cmp.monto_retgar_mo.disable();
							this.Cmp.id_plantilla.disable();
					}
					else {
							this.Cmp.id_depto_lb.disable();
							this.Cmp.monto_retgar_mo.enable();
							this.Cmp.id_plantilla.enable();
					}

					this.Cmp.id_cuenta_bancaria.disable();
					// this.Cmp.id_cuenta_bancaria_mov.disable();
					this.Cmp.obs_monto_no_pagado.enable();
					this.Cmp.obs_descuentos_ley.disable();
			}


			//may

			if (data.estado == 'vbsolicitante') {

					this.ocultarComponente(this.Cmp.id_cuenta_bancaria);
					this.ocultarComponente(this.Cmp.id_depto_lb);
					// this.Cmp.nro_cuenta_bancaria.allowBlank = true;
					// this.ocultarComponente(this.Cmp.nro_cuenta_bancaria);
					this.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
					this.ocultarComponente(this.Cmp.id_proveedor_cta_bancaria);
					this.ocultarComponente(this.Cmp.forma_pago);
			}
			if (data.estado == 'vbfin') {
					this.ocultarComponente(this.Cmp.id_cuenta_bancaria);
					// this.ocultarComponente(this.Cmp.nro_cuenta_bancaria);
					this.ocultarComponente(this.Cmp.id_proveedor_cta_bancaria);
					this.ocultarComponente(this.Cmp.forma_pago);

			}
			if (data.estado == 'vbcostos') {
					this.ocultarComponente(this.Cmp.id_cuenta_bancaria);
					// this.ocultarComponente(this.Cmp.nro_cuenta_bancaria);
					this.ocultarComponente(this.Cmp.id_proveedor_cta_bancaria);
					// this.ocultarComponente(this.Cmp.id_depto_lb);
					this.Cmp.id_depto_lb.disable();
					this.ocultarComponente(this.Cmp.forma_pago);

			}
			if (data.estado == 'vbdeposito') {
					this.ocultarComponente(this.Cmp.id_cuenta_bancaria);
					// this.ocultarComponente(this.Cmp.nro_cuenta_bancaria);
					this.ocultarComponente(this.Cmp.id_proveedor_cta_bancaria);
					this.ocultarComponente(this.Cmp.forma_pago);

			}

			if (data.tipo == 'pagado') {
					this.accionFormulario = 'EDIT_PAGO';
					this.porc_ret_gar = data.porc_monto_retgar;
			}

			if (data.tipo == 'ant_aplicado' || data.tipo == 'pagado') {
					this.tmp_porc_monto_excento_var = data.porc_monto_excento_var;
			}
			else {
					this.tmp_porc_monto_excento_var = undefined;
			}

			if (data.estado == 'vbconta') {

					this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
					this.mostrarComponente(this.Cmp.id_proveedor_cta_bancaria);
					this.Cmp.obs_monto_no_pagado.allowBlank = false;

					if (data.forma_pago == 'transferencia' && data.forma_pago == 'transferencia_propia' &&  data.forma_pago == 'transferencia_ext'  &&  data.forma_pago == 'transferencia_b_u') {
							this.Cmp.id_proveedor_cta_bancaria.enable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
					}else {
							this.Cmp.id_proveedor_cta_bancaria.disable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
					}
			}

			Phx.vista.CuotasDevengadas.superclass.onButtonEdit.call(this);
			if (this.Cmp.id_plantilla.getValue()) {
					this.getPlantilla(this.Cmp.id_plantilla.getValue());
			}

			// this.Cmp.nro_cuenta_bancaria.store.baseParams.id_proveedor = data.id_proveedor;
			// this.Cmp.nro_cuenta_bancaria.tdata.id_padre = this.idContenedor;
			//(may)modificacion para campo multas
			//this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = data.id_proveedor;
			//this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;

			if(data.otros_descuentos == 0){
					// this.mostrarGrupo(5); //mostra el grupo multas
					this.ocultarGrupo(5); //ocultar el grupo de multas
					this.ocultarComponente(this.Cmp.id_multa);
					this.Cmp.id_multa.modificado=true;
			}else {
					this.mostrarGrupo(5); //ocultar el grupo de multas
					this.Cmp.id_multa.modificado=false;
			}

			//(may)filtro para las Cuenta Bancaria Pago (BOA)  61-05780102002-78-5970034001-79-00578019201
			if (this.Cmp.id_cuenta_bancaria.getValue() == '61' || this.Cmp.id_cuenta_bancaria.getValue() == '78' || this.Cmp.id_cuenta_bancaria.getValue() == '79') {

					if (this.Cmp.forma_pago.getValue() == 'transferencia_propia') {
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = 'conLbr';
							this.Cmp.id_proveedor_cta_bancaria.enable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;

							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 2374; //proveedor boa
							this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
							this.Cmp.id_proveedor_cta_bancaria.modificado = true;

							//this.Cmp.id_proveedor_cta_bancaria.setValue('');
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_lb.getValue();
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.permiso = 'todos';
							//this.Cmp.id_proveedor_cta_bancaria.modificado=true;

					} else if (this.Cmp.forma_pago.getValue() == 'transferencia') {
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
							this.Cmp.id_proveedor_cta_bancaria.enable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;

							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = data.id_proveedor; //proveedores del plan de pago
							this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
							this.Cmp.id_proveedor_cta_bancaria.modificado = true;

					}else if (this.Cmp.forma_pago.getValue() == 'transferencia_ext') {
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
							this.Cmp.id_proveedor_cta_bancaria.enable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;

							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 27; //proveedor banco central
							this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
							this.Cmp.id_proveedor_cta_bancaria.modificado = true;

							//this.Cmp.id_proveedor_cta_bancaria.setValue('');
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_lb.getValue();
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.permiso = 'todos';

					}else if (this.Cmp.forma_pago.getValue() == 'transferencia_b_u') {
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
							this.Cmp.id_proveedor_cta_bancaria.enable();
							this.Cmp.id_proveedor_cta_bancaria.allowBlank = false;

							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 28; //proveedor banco union
							this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
							this.Cmp.id_proveedor_cta_bancaria.modificado = true;

							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_lb.getValue();
							this.Cmp.id_proveedor_cta_bancaria.store.baseParams.permiso = 'todos';
					}

			} else {
					this.Cmp.id_proveedor_cta_bancaria.disable();
					this.Cmp.id_proveedor_cta_bancaria.allowBlank = true;

					this.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = data.id_proveedor;
					this.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
					this.Cmp.id_proveedor_cta_bancaria.modificado = true;
			}

			this.Cmp.fecha_costo_ini.reset();
			this.Cmp.fecha_costo_fin.reset();
			this.Cmp.fecha_costo_ini.setValue(fechaFormateada);
			this.Cmp.fecha_costo_fin.setValue(fechaFormateada);
			this.Cmp.tipo_interfaz.setValue('deven_SigGestion');


	},

	getPlantilla: function (id_plantilla) {
			Phx.CP.loadingShow();
			Ext.Ajax.request({
					// form:this.form.getForm().getEl(),
					url: '../../sis_parametros/control/Plantilla/listarPlantilla',
					params: {id_plantilla: id_plantilla, start: 0, limit: 1},
					success: this.successPlantilla,
					failure: this.conexionFailure,
					timeout: this.timeout,
					scope: this
			});

	},
	successPlantilla: function (resp) {
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if (reg.total == 1) {

					this.Cmp.id_plantilla.fireEvent('select', this.Cmp.id_plantilla, {data: reg.datos[0]}, 0);
			} else {
					alert('Error al recuperar la plantilla para editar, actualice su navegador');
			}
	},

	setTipoPago: {
			'devengado': function (me) {
					//plantilla (TIPO DOCUMENTO)
					me.mostrarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.monto_excento);
					me.mostrarComponente(me.Cmp.monto_no_pagado);
					me.mostrarComponente(me.Cmp.obs_monto_no_pagado);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponente(me.Cmp.monto_retgar_mo)
					me.mostrarComponente(me.Cmp.descuento_ley);
					me.mostrarComponente(me.Cmp.monto_anticipo);
					me.mostrarComponente(me.Cmp.obs_descuentos_ley);
					me.mostrarComponente(me.Cmp.porc_monto_retgar);
					me.deshabilitarDescuentos(me);
					me.ocultarComponentesPago(me);
					me.Cmp.monto_retgar_mo.setReadOnly(false);
					me.mostrarGrupo(3); //mostra el grupo rango de costo
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(5); //mostra el grupo multas

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);


			},

			'devengado_rrhh': function (me) {
					//plantilla (TIPO DOCUMENTO)
					me.mostrarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.monto_excento);
					me.mostrarComponente(me.Cmp.monto_no_pagado);
					me.mostrarComponente(me.Cmp.obs_monto_no_pagado);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponente(me.Cmp.monto_retgar_mo)
					me.mostrarComponente(me.Cmp.descuento_ley);
					me.mostrarComponente(me.Cmp.monto_anticipo);
					me.mostrarComponente(me.Cmp.obs_descuentos_ley);
					me.deshabilitarDescuentos(me);
					me.ocultarComponentesPago(me);
					me.Cmp.monto_retgar_mo.setReadOnly(false);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo
					me.ocultarGrupo(5); //ocultar el grupo de multas

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);


			},

			'devengado_pagado': function (me) {
					//plantilla (TIPO DOCUMENTO)
					me.mostrarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.monto_excento);
					me.mostrarComponente(me.Cmp.monto_no_pagado);
					me.mostrarComponente(me.Cmp.obs_monto_no_pagado);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponente(me.Cmp.monto_retgar_mo);
					me.mostrarComponente(me.Cmp.obs_descuentos_ley);
					me.mostrarComponente(me.Cmp.descuento_ley);
					me.mostrarComponente(me.Cmp.monto_anticipo);

					me.mostrarComponente(me.Cmp.porc_monto_retgar);

					me.habilitarDescuentos(me);
					me.mostrarComponentesPago(me);
					me.Cmp.monto_retgar_mo.setReadOnly(false);
					me.mostrarGrupo(3); //mostra el grupo rango de costo
					me.ocultarGrupo(2); //ocultar el grupo de ajustes

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);


			},

			'devengado_pagado_1c': function (me) {
					//plantilla (TIPO DOCUMENTO)
					me.setTipoPago['devengado_pagado'](me);

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);
					me.mostrarComponente(me.Cmp.id_multa);

			},

			'rendicion': function (me) {
					//plantilla (TIPO DOCUMENTO)
					me.mostrarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.monto_excento);
					me.mostrarComponente(me.Cmp.monto_no_pagado);
					me.mostrarComponente(me.Cmp.obs_monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.habilitarDescuentos(me);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},
			'dev_garantia': function (me) {
					me.ocultarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponentesPago(me);
					me.deshabilitarDescuentos(me);
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_anticipo);

					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);


			},
			'dev_garantia_con': function (me) {
					me.ocultarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponentesPago(me);
					me.deshabilitarDescuentos(me);
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);


			},
			'dev_garantia_con_ant': function (me) {
					me.ocultarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponentesPago(me);
					me.deshabilitarDescuentos(me);
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},
			'especial': function (me) {
					me.ocultarComponente(me.Cmp.id_plantilla);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponentesPago(me);
					me.deshabilitarDescuentos(me);
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);
			},

			'pagado': function (me) {
					//03/122020 (may) que sea enable porque no solo se utilizara cuando se relacione una cuota
					//me.Cmp.id_plantilla.disable();
					me.Cmp.id_plantilla.enable();

					me.habilitarDescuentos(me);
					me.mostrarComponentesPago(me);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.Cmp.monto_retgar_mo.setReadOnly(true);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},
			'pagado_rrhh': function (me) {
					me.Cmp.id_plantilla.disable();
					me.habilitarDescuentos(me);
					me.mostrarComponentesPago(me);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.Cmp.monto_retgar_mo.setReadOnly(true);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},
			'ant_parcial': function (me) {
					me.ocultarComponente(me.Cmp.id_plantilla);

					me.mostrarComponentesPago(me);
					me.ocultarComponente(me.Cmp.descuento_anticipo);
					me.ocultarComponente(me.Cmp.descuento_inter_serv);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.otros_descuentos);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.obs_descuento_inter_serv);
					me.ocultarComponente(me.Cmp.obs_descuentos_anticipo);
					me.ocultarComponente(me.Cmp.obs_otros_descuentos);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},

			'anticipo': function (me) {

					me.mostrarComponente(me.Cmp.id_plantilla);
					me.mostrarComponentesPago(me);
					me.ocultarComponente(me.Cmp.descuento_anticipo);
					me.ocultarComponente(me.Cmp.descuento_inter_serv);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.otros_descuentos);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_ejecutar_total_mo);
					me.ocultarComponente(me.Cmp.obs_descuento_inter_serv);
					me.ocultarComponente(me.Cmp.obs_descuentos_anticipo);
					me.ocultarComponente(me.Cmp.obs_otros_descuentos);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.mostrarComponente(me.Cmp.liquido_pagable);
					me.mostrarComponente(me.Cmp.descuento_ley);
					me.mostrarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.ocultarGrupo(2); //ocultar el grupo de ajustes
					me.mostrarGrupo(3); //ocultar el grupo de periodo del costo

					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

			},
			'ant_aplicado': function (me, data) {
					me.Cmp.id_plantilla.disable();
					me.ocultarComponente(me.Cmp.descuento_ley);
					me.ocultarComponente(me.Cmp.obs_descuentos_ley);
					me.ocultarComponente(me.Cmp.monto_retgar_mo);
					me.ocultarComponente(me.Cmp.monto_no_pagado);
					me.ocultarComponente(me.Cmp.monto_anticipo);
					me.ocultarComponente(me.Cmp.liquido_pagable);
					me.ocultarComponente(me.Cmp.porc_monto_retgar);
					me.deshabilitarDescuentos(me);

					me.ocultarComponentesPago(me);
					// solo para pagos variable se pueden insertar ajustes
					if (data.pago_variable == 'si') {
							me.mostrarGrupo(2); //mostra el grupo de ajustes
							me.Cmp.monto_ajuste_siguiente_pag.setReadOnly(true);
					}
					else {
							me.ocultarGrupo(2); //ocultar el grupo de ajustes
					}

					me.mostrarGrupo(3);
					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);
			}
	},

	ocultarFP: function (me, pFormaPago) {

			if ((this.Cmp.id_cuenta_bancaria.getValue() == 61) || (this.Cmp.id_cuenta_bancaria.getValue() == 78) || (this.Cmp.id_cuenta_bancaria.getValue() == 79) ) {
					me.Cmp.forma_pago.enable();
					me.Cmp.forma_pago.setValue('');
					me.Cmp.id_proveedor_cta_bancaria.setValue('');

					me.Cmp.id_proveedor_cta_bancaria.enable();
					me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;

			} else {
					me.Cmp.forma_pago.disable();
					me.Cmp.forma_pago.setValue('cheque');

					me.Cmp.id_proveedor_cta_bancaria.disable();
					me.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
					me.Cmp.id_proveedor_cta_bancaria.setValue('');
			}




	},
	mostrarComponentesPago: function (me) {
			me.mostrarComponente(me.Cmp.nombre_pago);
			if (me.Cmp.id_cuenta_bancaria) {

					me.mostrarComponente(me.Cmp.id_depto_lb);
					me.mostrarComponente(me.Cmp.id_cuenta_bancaria);
					// me.mostrarComponente(me.Cmp.id_cuenta_bancaria_mov);
			}
			if (me.Cmp.forma_pago) {
					me.mostrarComponente(me.Cmp.forma_pago);
			}
			// if (me.Cmp.nro_cuenta_bancaria) {
			//     me.mostrarComponente(me.Cmp.nro_cuenta_bancaria);
			// }
			if (me.Cmp.id_proveedor_cta_bancaria) {
					me.mostrarComponente(me.Cmp.id_proveedor_cta_bancaria);
			}
			if (me.Cmp.nro_cheque) {
					// me.mostrarComponente(me.Cmp.nro_cheque);
					me.ocultarComponente(me.Cmp.nro_cheque);
			}

	},
	ocultarComponentesPago: function (me) {
			me.ocultarComponente(me.Cmp.nombre_pago);

			if (me.Cmp.id_cuenta_bancaria) {
					me.ocultarComponente(me.Cmp.id_depto_lb);
					me.ocultarComponente(me.Cmp.id_cuenta_bancaria);
					me.ocultarComponente(me.Cmp.id_cuenta_bancaria_mov);
			}
			if (me.Cmp.forma_pago) {
					me.ocultarComponente(me.Cmp.forma_pago);
			}
			// if (me.Cmp.nro_cuenta_bancaria) {
			//     me.ocultarComponente(me.Cmp.nro_cuenta_bancaria);
			// }
			if (me.Cmp.id_proveedor_cta_bancaria) {
					me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);
			}
			if (me.Cmp.nro_cheque) {
					me.ocultarComponente(me.Cmp.nro_cheque);
			}

	},

	habilitarDescuentos: function (me) {

			me.mostrarComponente(me.Cmp.otros_descuentos);
			me.mostrarComponente(me.Cmp.descuento_inter_serv);
			me.mostrarComponente(me.Cmp.descuento_anticipo);
			//me.mostrarComponente(me.Cmp.monto_retgar_mo);
			//me.mostrarComponente(me.Cmp.descuento_ley);


			me.mostrarComponente(me.Cmp.obs_descuento_inter_serv);
			me.mostrarComponente(me.Cmp.obs_descuentos_anticipo);
			me.mostrarComponente(me.Cmp.obs_otros_descuentos);
			//me.mostrarComponente(me.Cmp.obs_descuentos_ley);

	},
	deshabilitarDescuentos: function (me) {

			me.ocultarComponente(me.Cmp.otros_descuentos);
			me.ocultarComponente(me.Cmp.descuento_inter_serv);
			me.ocultarComponente(me.Cmp.descuento_anticipo);
			//me.ocultarComponente(me.Cmp.monto_retgar_mo);
			//me.ocultarComponente(me.Cmp.descuento_ley);
			me.ocultarComponente(me.Cmp.obs_descuento_inter_serv);
			me.ocultarComponente(me.Cmp.obs_descuentos_anticipo);
			me.ocultarComponente(me.Cmp.obs_otros_descuentos);
			//me.ocultarComponente(me.Cmp.obs_descuentos_ley);


	},

	getDecuentosPorAplicar: function (id_plantilla) {
			var data = this.getSelectedData();
			Phx.CP.loadingShow();

			Ext.Ajax.request({
					// form:this.form.getForm().getEl(),
					url: '../../sis_contabilidad/control/PlantillaCalculo/recuperarDescuentosPlantillaCalculo',
					params: {id_plantilla: id_plantilla},
					success: this.successAplicarDesc,
					failure: this.conexionFailure,
					timeout: this.timeout,
					scope: this
			});
	},

	successAplicarDesc: function (resp) {
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if (!reg.ROOT.error) {
					this.Cmp.porc_descuento_ley.setValue(reg.ROOT.datos.descuento_porc * 1.00);
					this.Cmp.obs_descuentos_ley.setValue(reg.ROOT.datos.observaciones);
					this.calculaMontoPago();
			} else {
					alert(reg.ROOT.mensaje)
			}
	},

	calculaMontoPago: function () {
			var descuento_ley = 0.00;

			//TODO monto exento en pp de segundo nivel

			console.log('this.tmp_porc_monto_excento_var', this.tmp_porc_monto_excento_var)
			if (this.tmp_porc_monto_excento_var) {
					this.Cmp.monto_excento.setValue(this.Cmp.monto.getValue() * this.tmp_porc_monto_excento_var)
			}
			else {

					console.log('...', this.Cmp.tipo_excento.getValue(), this.Cmp.tipo_excento.getValue())
					if (this.Cmp.tipo_excento.getValue() == 'constante') {
							this.Cmp.monto_excento.setValue(this.Cmp.valor_excento.getValue())
					}

					if (this.Cmp.tipo_excento.getValue() == 'porcentual') {
							this.Cmp.monto_excento.setValue(this.Cmp.monto.getValue() * this.Cmp.valor_excento.getValue())
					}


			}

			if (this.Cmp.monto_excento.getValue() == 0) {
					descuento_ley = this.Cmp.monto.getValue() * this.Cmp.porc_descuento_ley.getValue() * 1.00;
					this.Cmp.descuento_ley.setValue(descuento_ley);
			}
			else {
					if (this.Cmp.monto_excento.getValue() > 0) {
							descuento_ley = (this.Cmp.monto.getValue() * 1.00 - this.Cmp.monto_excento.getValue() * 1.00) * this.Cmp.porc_descuento_ley.getValue();
							this.Cmp.descuento_ley.setValue(descuento_ley);
					}
					else {
							alert('El monto exento no puede ser menor que cero');
							return;
					}

			}


			var monto_ret_gar = 0;
			if (this.porc_ret_gar > 0 && (this.Cmp.tipo.getValue() == 'pagado' || this.Cmp.tipo.getValue() == 'pagado_rrhh')) {
					this.Cmp.monto_retgar_mo.setValue(this.porc_ret_gar * this.Cmp.monto.getValue());
			}

			//24-06-2021 (may)para calcular Retenciones de garantia con porcentaje de retenciones de garantia
			//monto_ret_gar = this.Cmp.monto_retgar_mo.getValue();
			monto_ret_gar =  this.Cmp.monto.getValue() * this.Cmp.porc_monto_retgar.getValue();
			this.Cmp.monto_retgar_mo.setValue(monto_ret_gar);

			var liquido = this.Cmp.monto.getValue() - this.Cmp.monto_no_pagado.getValue() - this.Cmp.otros_descuentos.getValue() - monto_ret_gar - this.Cmp.descuento_ley.getValue() - this.Cmp.descuento_inter_serv.getValue() - this.Cmp.descuento_anticipo.getValue();
			//this.Cmp.liquido_pagable.setValue(liquido > 0 ? liquido : 0);


			this.Cmp.liquido_pagable.setValue(liquido);
			var eje = this.Cmp.monto.getValue() - this.Cmp.monto_no_pagado.getValue() - this.Cmp.monto_anticipo.getValue();
			this.Cmp.monto_ejecutar_total_mo.setValue(eje > 0 ? eje : 0);

	},

	iniciarEventos: function () {


			this.Cmp.monto.on('change', this.calculaMontoPago, this);
			this.Cmp.descuento_anticipo.on('change', this.calculaMontoPago, this);
			this.Cmp.monto_no_pagado.on('change', this.calculaMontoPago, this);
			this.Cmp.otros_descuentos.on('change', this.calculaMontoPago, this);
			this.Cmp.monto_retgar_mo.on('change', this.calculaMontoPago, this);
			this.Cmp.descuento_ley.on('change', this.calculaMontoPago, this);
			this.Cmp.descuento_inter_serv.on('change', this.calculaMontoPago, this);
			this.Cmp.monto_anticipo.on('change', this.calculaMontoPago, this);
			this.Cmp.monto_excento.on('change', this.calculaMontoPago, this);

			this.Cmp.id_plantilla.on('select', function (cmb, rec, i) {
					this.getDecuentosPorAplicar(rec.data.id_plantilla);
					//this.Cmp.monto_excento.reset();
					if (rec.data.sw_monto_excento == 'si') {
							var row_data = this.getSelectedData();
							this.Cmp.monto_excento.enable();
							this.Cmp.tipo_excento.setValue(rec.data.tipo_excento);
							this.Cmp.valor_excento.setValue(rec.data.valor_excento);
							this.Cmp.monto_excento.setValue(row_data.monto_excento);
					}
					else {
							this.Cmp.monto_excento.disable();
							this.Cmp.tipo_excento.setValue('variable');
							this.Cmp.monto_excento.setValue(0);
							this.Cmp.valor_excento.setValue(0);
					}

			}, this);

			this.Cmp.tipo.on('change', function (groupRadio, radio) {
					this.enableDisable(radio.inputValue);
			}, this);


			//Eventos
			/* this.Cmp.id_cuenta_bancaria.on('select',function(a,b,c){
					 this.Cmp.id_cuenta_bancaria_mov.setValue('');
					 this.Cmp.id_cuenta_bancaria_mov.store.baseParams.id_cuenta_bancaria = this.Cmp.id_cuenta_bancaria.getValue();
					 Ext.apply(this.Cmp.id_cuenta_bancaria_mov.store.baseParams,{id_cuenta_bancaria: this.Cmp.id_cuenta_bancaria.getValue()})
					 this.Cmp.id_cuenta_bancaria_mov.modificado=true;
			 },this);
			*/

			// this.Cmp.fecha_tentativa.on('blur', function (a) {
			//     this.Cmp.id_cuenta_bancaria_mov.setValue('');
			//     Ext.apply(this.Cmp.id_cuenta_bancaria_mov.store.baseParams, {fecha: this.Cmp.fecha_tentativa.getValue()})
			//     this.Cmp.id_cuenta_bancaria_mov.modificado = true;
			// }, this);


			this.Cmp.id_depto_lb.on('select', function (a, b, c) {
					this.Cmp.id_cuenta_bancaria.setValue('');
					this.Cmp.id_cuenta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_lb.getValue();
					this.Cmp.id_cuenta_bancaria.store.baseParams.permiso = 'todos';
					this.Cmp.id_cuenta_bancaria.modificado = true;
			}, this);

			//Evento para filtrar los depósitos a partir de la cuenta bancaria
			// this.Cmp.id_cuenta_bancaria.on('select', function (data, rec, ind) {
			//
			//     if (rec.data.centro == 'no') {
			//         this.Cmp.id_cuenta_bancaria_mov.allowBlank = false;
			//         if (this.Cmp.desc_depto_conta_pp.value == 'CON-CBB') {
			//             this.Cmp.id_cuenta_bancaria_mov.allowBlank = true;
			//         }
			//     }
			//     else {
			//         this.Cmp.id_cuenta_bancaria_mov.allowBlank = true;
			//     }
			//     this.Cmp.id_cuenta_bancaria_mov.setValue('');
			//     this.Cmp.id_cuenta_bancaria_mov.modificado = true;
			//     Ext.apply(this.Cmp.id_cuenta_bancaria_mov.store.baseParams, {id_cuenta_bancaria: rec.id});
			// }, this);

			//(may)para controlar que id de estas cuentas bancarias sean desactivados los campos en forma de pago (61,78,79)
			this.Cmp.id_cuenta_bancaria.on('select', function (groupRadio, radio) {
					this.ocultarFP(this, radio.inputValue);

			}, this);

			//Evento para ocultar/motrar componentes por cheque o transferencia
			this.Cmp.forma_pago.on('change', function (groupRadio, radio) {
					this.ocultarCheCue(this, radio.inputValue);
			}, this);

			//eventos de fechas de costo
			this.Cmp.fecha_costo_ini.on('change', function (o, newValue, oldValue) {
					this.Cmp.fecha_costo_fin.setMinValue(newValue);
					this.Cmp.fecha_costo_fin.reset();

			}, this);

			//eventos de fechas de costo
			this.Cmp.fecha_costo_fin.on('change', function (o, newValue, oldValue) {
					this.Cmp.fecha_costo_ini.setMaxValue(newValue);
			}, this);

			this.Cmp.fecha_costo_ini.on('select', function (value, date) {

					var anio = date.getFullYear();

					var fecha_inicio = new Date(anio + '/01/1');
					var fecha_fin = new Date(anio + '/12/31');
					//control de fechas de inicio y fin de costos

					this.Cmp.fecha_costo_ini.setMinValue(fecha_inicio);
					this.Cmp.fecha_costo_ini.setMaxValue(fecha_fin);
					this.Cmp.fecha_costo_fin.setMinValue(fecha_inicio);
					this.Cmp.fecha_costo_fin.setMaxValue(fecha_fin);
			}, this);


	},

  bnew:false,
	bdel:false,
	bsave:false,
  btest:false,
	bgantt: true,
	}
)
</script>

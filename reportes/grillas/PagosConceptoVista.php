<?php
/**
 * @package pxP
 * @file 	repkardex.php
 * @author 	RCM
 * @date	10/07/2013
 * @description	Archivo con la interfaz de usuario que permite la ejecucion de las funcionales del sistema
 */
header("content-type:text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.PagosConceptoVista = Ext.extend(Phx.gridInterfaz, {
		constructor : function(config) {
			var me = this;
			this.maestro = config;
			this.title = "PAGOS X CONCEPTO";
			this.description = this.maestro.concepto + "-" + this.maestro.gestion;
			Phx.vista.PagosConceptoVista.superclass.constructor.call(this, config);
			this.init();	
			this.store.baseParams = {
				id_gestion: this.id_gestion && this.maestro.id_gestion,
				id_concepto: this.id_concepto && this.maestro.id_concepto,
				id_partida: this.id_partida && this.maestro.id_partida,
				tipo_reporte: this.tipo_reporte && this.maestro.tipo_reporte
			}
			this.load({
				params : {
					start: 0,
					limit: 50					
				}
			});
		},
		tam_pag:50,
		Atributos : [{
			config : {
				labelSeparator : '',
				inputType : 'hidden',
				name : 'id_plan_pago'
			},
			type : 'Field',
			form : true
		},
		{
			config : {
				name : 'num_tramite',
				fieldLabel : 'No Tramite',
				gwidth : 150
			},
			type : 'Field',
			filters : {
			    pfiltro : 'op.num_tramite',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		{
			config : {
				name : 'nombre_ingas',
				fieldLabel : 'Concepto Ingreso Gasto',
				gwidth : 250
			},
			type : 'Field',
			grid : true,
			form : false			
		},		
		{
			config : {
				name : 'orden_trabajo',
				fieldLabel : 'Orden de Trabajo',
				gwidth : 250
			},
			type : 'Field',
			filters : {
			    pfiltro : 'opc.desc_orden#ot.desc_orden',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		/*Aumentando estos dos campos 28/11/2019 (Ismael Valdivia)*/
		{
			config : {
				name : 'justificacion',
				fieldLabel : 'Justificación',
				gwidth : 250
			},
			type : 'Field',
			filters : {
			    pfiltro : 'op.obs',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		{
			config : {
				name : 'descripcion',
				fieldLabel : 'Descripción',
				gwidth : 250
			},
			type : 'Field',
			filters : {
			    pfiltro : 'od.descripcion',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		/**********************************************************/
		{
			config : {
				name : 'nro_cuota',
				fieldLabel : 'No Cuota',
				gwidth : 80
			},
			type : 'Field',
			filters : {
			    pfiltro : 'pp.nro_cuota',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		{
			config: {
				name: 'desc_funcionario1',								
				fieldLabel: 'Funcionario Solictante',
				allowBlank: false,
				gwidth: 200,				
			},
			type: 'Field',//ComboRec
			id_grupo: 1,			
			bottom_filter: true,
			grid: true,
			form: true
		},
		{
			config : {
				name : 'desc_proveedor',
				fieldLabel : 'Proveedor',
				gwidth : 170
			},
			type : 'Field',
			filters : {
			    pfiltro : 'prov.rotulo_comercial',
				type : 'string'
			},
			grid : true,
			form : false,
			bottom_filter: true
		},
		/*Remplazando el id_centro_costo por codigo_cc 28/11/2019 (Ismael Valdivia)*/
		// {
		// 	config : {
		// 		name : 'id_centro_costo',
		// 		fieldLabel : 'Centro Costo',
		// 		gwidth : 150
		// 	},
		// 	type : 'Field',
		// 	filters : {
		// 	    pfiltro : 'od.id_centro_costo',
		// 		type : 'string'
		// 	},
		// 	grid : true,
		// 	form : false
		// },
		{
			config : {
				name : 'codigo_cc',
				fieldLabel : 'Centro Costo',
				gwidth : 250
			},
			type : 'Field',
			filters : {
			    pfiltro : 'cc.codigo_cc',
				type : 'string'
			},
			grid : true,
			form : false
		},
		{
			config : {
				name : 'nombre_partida',
				fieldLabel : 'Partida',
				gwidth : 250,
				renderer: function(value, p, record) {
					var nom_partida = record.data.nombre_partida + ' - ('+record.data.codigo_partida+')';

					if(record.data.tipo_reg != 'summary'){
						return String.format('{0}', nom_partida);
					} else {
						return '';
					}										
				}

			},
			type : 'Field',
			grid : true,
			form : false,
			bottom_filter: true
		},
		/***********************************************/
		{
			config : {
				name : 'estado',
				fieldLabel : 'Estado',
				gwidth : 100,
				renderer:function(value, p, record) {
					var aux;
					if(record.data.estado == 'borrador'){
						aux='<b><font color="brown">';
					}
					else {
						aux='<b><font color="green">';
					}
					aux = aux +value+'</font></b>';
					return String.format('{0}', aux);
				}
			},
			type : 'Field',
			filters : {
			    pfiltro : 'pp.estado',
				type : 'string'
			},
			grid : true,
			form : false
		},
		{
			config : {
				name : 'fecha',
				fieldLabel : 'Fecha de Pago ',
				gwidth : 200,
				renderer : function(value, p, record) {
					return value ? value.dateFormat('d/m/Y') : ''
				}
			},
			type : 'Field',
			filters : {
			    pfiltro : 'pp.fecha_tentativa#com.fecha',
				type : 'date'
			},
			grid : true,
			form : false
		},
		{
			config : {
				name : 'fecha_costo_ini',
				fieldLabel : 'Fecha Inicio Costo',
				gwidth : 200,
				renderer : function(value, p, record) {
					return value ? value.dateFormat('d/m/Y') : ''
				}
			},
			type : 'Field',
			filters : {
			    pfiltro : 'pp.fecha_costo_ini',
				type : 'date'
			},
			grid : true,
			form : false
		},
		{
			config : {
				name : 'fecha_costo_fin',
				fieldLabel : 'Fecha Fin Costo',
				gwidth : 200,
				renderer : function(value, p, record) {
					return value ? value.dateFormat('d/m/Y') : ''
				}
			},
			type : 'Field',
			filters : {
			    pfiltro : 'pp.fecha_costo_fin',
				type : 'date'
			},
			grid : true,
			form : false
		},

		{
			config : {
				name : 'moneda',
				fieldLabel : 'Moneda',
				gwidth : 120,
				galign:'right',
				renderer:function (value,p,record){
					// console.log("llega aqui el dato",record.data);
					if(record.data.tipo_reg != 'summary'){
						return  String.format('{0}', value);
					}
					else{
						return '<b><p style="padding-top:10px; font-size:18px; color:blue;">Total Monto: </p></b>';
					}
			},
			},
			type : 'Field',
			filters : {
			    pfiltro : 'mon.moneda',
				type : 'string'
			},
			grid : true,
			form : false
		},
		/*quitamos este campo para no causar confucion 28/11/2019 (Ismael Valdivia)*/
		// {
		// 	config : {
		// 		name : 'monto',
		// 		fieldLabel : 'Monto',
		// 		gwidth : 100,
		// 		renderer:function(value, p, record) {
		// 			var aux;
		// 			if(record.data.estado == 'borrador'){
		// 				aux='<b><font color="brown">';
		// 			}
		// 			else {
		// 				aux='<b><font color="green">';
		// 			}
		// 			aux = aux +value+'</font></b>';
		// 			return String.format('{0}', aux);
		// 		}
		// 	},
		// 	type : 'NumberField',
		// 	filters : {
		// 	    pfiltro : 'pp.monto',
		// 		type : 'numeric'
		// 	},
		// 	grid : true,
		// 	form : false
		// },

		{
			config : {
				name : 'monto_ejecutar_mo',
				fieldLabel : 'Monto Ejecucion OT',
				gwidth : 200,
				galign:'right',
				renderer:function(value, p, record) {
					var aux;
						if (record.data.tipo_reg != 'summary') {
							if(record.data.estado == 'borrador'){
								aux='<b><font color="brown">';
							}
							else {
								aux='<b><font color="green">';
							}
						/*Aumentando el Formato de decimales 28/11/2019(Ismael Valdivia)*/
							aux = aux +Ext.util.Format.number(value,'0,000.00')+'</font></b>';
							return String.format('{0}', aux);
					} else {
							return  String.format('<div style="font-size:18px; text-align:rigth; color:#FF7323; padding-top:10px;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_monto_ot,'0,000.00'));
					}
				}
			},
			type : 'NumberField',
			filters : {
			    pfiltro : 'pro.monto_ejecutar_mo',
				type : 'numeric'
			},
			grid : true,
			form : false
		}
		],
		title : 'Pagos por Concepto',
		ActList : '../../sis_tesoreria/control/PlanPago/listarPagosXConcepto',
		id_store : 'id',
		fields : [{
			name : 'id_plan_pago'
		},{
			name : 'orden_trabajo',
			type : 'string'
		},{
			name : 'num_tramite',
			type : 'string'
		},{
			name : 'nro_cuota',
			type : 'string'
		},{
			name : 'desc_proveedor',
			type : 'string'
		},{
			name : 'estado',
			type : 'string'
		},{
			name : 'moneda',
			type : 'string'
		},{
			name : 'monto',
			type : 'numeric'
		},
		/*Remplazando esta parte 28/11/2019(Ismael Valdivia)*/
		// {
		// 	name : 'id_centro_costo',
		// 	type : 'numeric'
		// },
		{
			name : 'codigo_cc',
			type : 'string'
		},
		{
			name: 'tipo_reg',
			type: 'string'
		},
		{
			name: 'total_monto_ot',
			type: 'numeric'
		},
		{
			name: 'justificacion',
			type: 'string'
		},
		{
			name: 'descripcion',
			type: 'string'
		},
		/*************************/
		{
			name : 'id_centro_costo',
			type : 'numeric'
		},

		{
			name : 'monto_ejecutar_mo',
			type : 'numeric'
		}, {
			name : 'fecha_costo_ini',
			type : 'date',
			dateFormat : 'Y-m-d'
		}, {
			name : 'fecha_costo_fin',
			type : 'date',
			dateFormat : 'Y-m-d'
		}, {
			name : 'fecha',
			type : 'date',
			dateFormat : 'Y-m-d'
		},		
		{ name : 'desc_funcionario1', type : 'string' },
		{ name : 'id_partida', type : 'numeric' },
		{ name : 'nombre_partida', type : 'string' },
		{ name : 'codigo_partida', type : 'string' },
		{ name : 'nombre_ingas', type : 'string' }
	],
		sortInfo : {
			field : 'id',
			direction : 'ASC'
		},
		bdel : false,
		bnew: false,
		bedit: false,
		fwidth : '90%',
		fheight : '80%'		
	});
</script>

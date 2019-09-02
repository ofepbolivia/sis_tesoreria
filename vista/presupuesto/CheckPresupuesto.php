<?php
/**
 *@package   pXP
 *@file      VerificacionPresup.php
 *@author    RCM
 *@date      20/12/2013
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.CheckPresupuesto = Ext.extend(Phx.gridInterfaz, {
		
		constructor: function(config) {
			this.maestro = config;
			Phx.vista.CheckPresupuesto.superclass.constructor.call(this, config);
			this.init();
			//18-06-2019, se oculta boton Revertir/Incrementar.
            //17-07-2019 a solicitud se pide habilitar el boton
			 this.addButton('inserAuto',{ text: 'Revertir/Incrementar', iconCls: 'blist', disabled: false, handler: this.revertirParcial, tooltip: '<b>Configurar autorizaciones</b><br/>Permite seleccionar desde que modulos  puede selecionarse el concepto'});
    
			this.grid.on('validateedit',function(event){
				if((event.record.data.comprometido - event.record.data.ejecutado) < event.value){
					return false;
				}
			},this);
			
			this.load({
				params : {
					start : 0,
					limit : this.tam_pag,
					id_obligacion_pago: this.maestro.id_obligacion_pago,
					id_moneda: this.maestro.id_moneda
				}
			});
		},
		
		Atributos : [
		 {
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_partida_ejecucion'
			},
			type:'Field',
			form:true 
		 },
		 {
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_obligacion_det'
			},
			type:'Field',
			form:true 
		 },
		 {
			config : {
				name : 'nombre_partida',
				fieldLabel : 'Partida',
				gwidth : 250
			},
			type : 'TextField',
			filters : {
				pfiltro : 'nombre_partida',
				type : 'string'
			},
			id_grupo : 1,
			grid : true,
			form : false
		}, {
			config : {
				name : 'codigo_cc',
				fieldLabel : 'Presupuesto',
				gwidth : 280
			},
			filters : {
				pfiltro : 'codigo_cc',
				type : 'string'
			},
			type : 'TextField',
			id_grupo : 1,
			grid : true,
			form : false
		}, {
			config : {
				name : 'descripcion',
				fieldLabel : 'Descripcion ',
				gwidth : 280
			},
			filters : {
				pfiltro : 'descripcion',
				type : 'string'
			},
			type : 'TextField',
			id_grupo : 1,
			grid : true,
			form : false
		}, { 
            config : {
                name : 'desc_orden',
                fieldLabel : 'Orden Trabajo',
                gwidth : 150
            },
            filters : {
                pfiltro : 'desc_orden',
                type: 'string'
            },
            type : 'TextField',
            id_grupo : 1,
            grid : 1,
            form : false
        },
        {
            config : {
                name : 'moneda',
                fieldLabel : 'Moneda',
                gwidth : 100
            },
            filters : {
                pfiltro : 'moneda',
                type: 'string'
            },
            type : 'TextField',
            id_grupo : 1,
            grid : true,
            form : false
        },        
        {
			config : {
				name : 'comprometido',
				fieldLabel : 'Comprometido',
				gwidth : 100,
                renderer:function (value,p,record){
                    return  String.format('<div style="text-align:right;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));                    
                }
			},
			type : 'NumberField',
			id_grupo : 1,
			grid : true,
			form : false
		},{
			config : {
				name : 'ejecutado',
				fieldLabel : 'Ejecutado',
				gwidth : 100,
                renderer:function (value,p,record){
                    return  String.format('<div style="text-align:right;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));
                }
			},
			type : 'TextField',
			id_grupo : 1,
			grid : true,
			form : false
		},{
			config : {
				name : 'pagado',
				fieldLabel : 'Pagado',
				gwidth : 100,
                renderer:function (value,p,record){
                    return  String.format('<div style="text-align:right;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));
                }
			},
			type : 'NumberField',
			id_grupo : 1,
			grid : true,
			form : false
		},{
			config : {
				name : 'revertible',
				fieldLabel : 'Revertible',
				gwidth : 100,
                renderer:function (value,p,record){
                    return  String.format('<div style="text-align:right;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));
                }
			},
			type : 'NumberField',
			filters : {
				pfiltro : 'revertible',
				type : 'numeric'
			},
			id_grupo : 1,
			grid : true,
			egrid: false,
			form : false
		},
        {
			config : {
				name : 'revertir',
				fieldLabel : 'Revertir/Incrementar',
				gwidth : 100
			},
			type : 'NumberField',
			id_grupo : 1,
            //17-07-2019 a solicitud se pide habilitar el boton
			// grid : false,
			// egrid: false,
            grid : true,
			egrid: true,
			form : false
		}],
		title : 'Verificación presupuestaria',
		ActList : '../../sis_tesoreria/control/ObligacionPago/listarObligacionPresupuesto',
		
		fields : [
		            'id_obligacion_det',
                    'id_partida',
                    'nombre_partida',
                    'id_concepto_ingas',
                    'nombre_ingas',
                    'id_obligacion_pago',
                    'id_centro_costo',
                    'codigo_cc',
                    'id_partida_ejecucion_com',
                    'descripcion',
                    'comprometido',
                    'ejecutado',
                    'pagado',
                    'revertible','revertir',
                    'moneda',                    
                    'desc_orden'
		],
		sortInfo : {
			field : 'nombre_partida',
			direction : 'ASC'
		},

        tabsouth: [
            {
                url: '../../../sis_tesoreria/vista/presupuesto/EvoluPresupDet.php',
                title: '<span style="font-size:15;">Comprometido</span>',
                height: '50%',
                cls: 'EvoluPresupDet'
            },
            {
                url: '../../../sis_tesoreria/vista/presupuesto/EvoluPresupDetEjecutado.php',
                title: '<span style="font-size:15;">Ejecutado</span>',
                height: '50%',
                cls: 'EvoluPresupDetEjecutado'
            },
            {
                url: '../../../sis_tesoreria/vista/presupuesto/EvoluPresupDetPagado.php',
                title: '<span style="font-size:15;">Pagado</span>',
                height: '50%',
                cls: 'EvoluPresupDetPagado'
            }                        
        ],

		bdel :   false,
		bsave :  false,
		bnew:    false,
		bedit:   false,
        btest:   false,
		tam_pag: 1000,
		
		successSinc:function(resp){
	            Phx.CP.loadingHide();
	            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
	            if(!reg.ROOT.error){
	            	this.reload();
	             }else{
	                alert('ocurrio un error durante el proceso')
	            }
	    },
			
		revertirParcial:function(){
			var me = this;
			var filas=this.store.getModifiedRecords();            
            		
	 	    if(filas.length>0){	
			     if(confirm("Está seguro de revertir el presupeusto?")){
					if(confirm("Realmente seguro?, no podrá retroceder")){
						
						Phx.CP.loadingShow();
						//recupera registros modificados
						var id_ob_dets = [],
						    rev = [],
						    sw = false;
						    
						for(var i=0;i<filas.length;i++){
							 if(filas[i].data.revertir != 0){
							 	//armar array de ids
								 id_ob_dets[i] = filas[i].data.id_obligacion_det;
								 //armar array de montos a revertir
								 rev[i] =  filas[i].data.revertir;
								 sw = true;
							 }
							 
						}
						//si por lo menos tiene un monto a revertir mayor a cero
						if(sw){
							
				            Ext.Ajax.request({
				                url:'../../sis_tesoreria/control/ObligacionPago/revertirParcialmentePresupuesto',
				                params: { 
				                	      id_ob_dets: id_ob_dets.toString(),
				                	      id_obligacion_pago: me.maestro.id_obligacion_pago,
				                	      revertir: rev.toString()
				                	    },
				                success: this.successSinc,
				                failure: this.conexionFailure,
				                timeout: this.timeout,
				                scope: this
				            });		
						}
						else{
							Phx.CP.loadingHide();
							alert('Nada para revertir ...')
						}
							
					}
					
				}
			}//if tamano
			
		}
	}); 
</script>
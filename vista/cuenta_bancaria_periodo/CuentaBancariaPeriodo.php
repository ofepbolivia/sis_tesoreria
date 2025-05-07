<?php
/**
*@package pXP
*@file gen-CuentaBancariaPeriodo.php
*@author  (gsarmiento)
*@date 09-04-2015 18:40:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CuentaBancariaPeriodo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.CuentaBancariaPeriodo.superclass.constructor.call(this,config);
		this.init();

		this.addButton('btnAbrirCerrarPeriodo',
			{
				text: 'Cerrar/Abrir',
				iconCls: 'block',
				disabled: true,
				handler: this.abrirCerrarPeriodo,
				tooltip: '<b>Cerrar/Abrir</b><br/>Cerrar/Abrir el periodo de una cuenta bancaria'
			}
		);
		//this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cuenta_bancaria_periodo'
			},
			type:'Field',
			form:true
		},
		{
			config: {
				name: 'id_cuenta_bancaria',
				fieldLabel: 'Cuenta bancaria',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancaria',
					//id: 'id_',
					root: 'datos',
					sortInfo: {
						field: 'inst.nombre', //fRnk
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_cuenta_bancaria', 'nro_cuenta', 'nombre_institucion'],
					remoteSort: true,
					baseParams: {par_filtro: 'movtip.nombre#movtip.codigo'}
				}),
				valueField: 'id_cuenta_bancaria',
				displayField: 'nro_cuenta',
				gdisplayField: 'nro_cuenta',
				hiddenName: 'id_cuenta_bancaria',
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
				/*renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_']);
				}*/
                tpl: '<tpl for="."><div class="x-combo-list-item"><p>{nro_cuenta}</p></div></tpl>',
                renderer:function(value, p, record){return String.format('{0}', record.data['nro_cuenta']);},
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: false,
			form: true
		},
		{
			config:{
				name: 'gestion',
				fieldLabel: 'Gestión',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'TextField',
				filters:{pfiltro:'perctab.gestion',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nombre_periodo',
				fieldLabel: 'Mes',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'perctab.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config: {
				name: 'id_periodo',
				fieldLabel: 'Periodo',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({ //HR01765-2024
					//url: '../../sis_tesoreria/control/CuentaBancariaPeriodo/listarCuentaBancariaPeriodo',//Correcion en la direccion la obtencion de id_periodo
					url: '../../sis_parametros/control/PeriodoSubsistema/listarPeriodoSubsistema',
					//id: 'id_',
					root: 'datos',
					sortInfo: {field: 'gestion,periodo',direction: 'DESC'},
					totalProperty: 'total',
					fields: ['id_periodo_subsistema', 'periodo', 'id_periodo', 'gestion'],
					remoteSort: true,
					baseParams: {codSist: 'TES'}
				}),
				valueField: 'id_periodo',
				displayField: 'periodo',
				gdisplayField: 'periodo',
				hiddenName: 'id_periodo',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '100%',
				gwidth: 80,
				minChars: 2,
                tpl: '<tpl for="."><div class="x-combo-list-item"><p>{gestion} - {periodo}</p></div></tpl>',

			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:15,
			},
				type:'TextField',
				filters:{pfiltro:'perctab.estado',type:'string'},
				id_grupo:1,
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
				filters:{pfiltro:'perctab.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'perctab.id_usuario_ai',type:'numeric'},
				id_grupo:1,
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
				filters:{pfiltro:'perctab.fecha_reg',type:'date'},
				id_grupo:1,
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
				filters:{pfiltro:'perctab.usuario_ai',type:'string'},
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
				filters:{pfiltro:'usu1.cuenta',type:'string'},
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
				filters:{pfiltro:'perctab.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Periodos por Cuenta Bancaria',
	ActSave:'../../sis_tesoreria/control/CuentaBancariaPeriodo/insertarCuentaBancariaPeriodo',
	ActDel:'../../sis_tesoreria/control/CuentaBancariaPeriodo/eliminarCuentaBancariaPeriodo',
	ActList:'../../sis_tesoreria/control/CuentaBancariaPeriodo/listarCuentaBancariaPeriodo',
	id_store:'id_cuenta_bancaria_periodo',
	fields: [
		{name:'id_cuenta_bancaria_periodo', type: 'numeric'},
		{name:'id_cuenta_bancaria', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'id_periodo', type: 'numeric'},
		{name:'periodo', type: 'string'},
		{name:'nombre_periodo', type: 'string'},
		{name:'gestion', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},

	],

	preparaMenu:function(tb){
        Phx.vista.CuentaBancariaPeriodo.superclass.preparaMenu.call(this,tb)

		var data = this.getSelectedData();
		if(data['estado']== 'cerrado'){
			this.getBoton('btnAbrirCerrarPeriodo').setIconClass('bunlock');
		}
		else{
			this.getBoton('btnAbrirCerrarPeriodo').setIconClass('block');
		}
    },

	onReloadPage:function(m)
	{
		this.maestro=m;
		this.store.baseParams={id_cuenta_bancaria:this.maestro.id_cuenta_bancaria};
		this.load({params:{start:0, limit:50}});
        this.getBoton('btnAbrirCerrarPeriodo').enable();
	},

	abrirCerrarPeriodo:function(){
        var d = this.sm.getSelected();
        if(d){//fRnk
            d = d.data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_tesoreria/control/CuentaBancariaPeriodo/abrirCerrarCuentaBancariaPeriodo',
                params:{id_cuenta_bancaria_periodo:d.id_cuenta_bancaria_periodo, estado:d.estado},
                success:this.successAbrirCerrarPeriodo,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }else{
            alert('Debe seleccionar un periodo para abrir o cerrar.');
        }
	},

	successAbrirCerrarPeriodo:function(resp){
       Phx.CP.loadingHide();
       var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
       if(!reg.ROOT.error){
         this.reload();
       }
    },

	sortInfo:{
		field: 'per.id_gestion DESC, per.periodo',
		direction: 'DESC'
	},
	bdel:false,
	bsave:false,
	bnew:true,
	bedit:false
	}
)
</script>
		
		
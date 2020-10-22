<?php
/**
*@package pXP
*@file CuentaBancaria.php
*@author  Gonzalo Sarmiento Sejas
*@date 24-04-2013 15:19:30
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.CuentaBancaria=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.initButtons=[this.cmbDepto];
    	//llama al constructor de la clase padre
		Phx.vista.CuentaBancaria.superclass.constructor.call(this,config);
		this.init();
		this.cmbDepto.on('select',this.capturaFiltros,this);
		//this.load({params:{start:0, limit:this.tam_pag}})		
	},
	
	capturaFiltros:function(combo, record, index){
		this.store.baseParams.id_depto_lb=this.cmbDepto.getValue();
		this.store.load({params:{start:0, limit:50, permiso : 'libro_bancos'}});	
	},
	
	cmbDepto:new Ext.form.ComboBox({
		fieldLabel: 'Departamento',
		allowBlank: true,
		emptyText:'Departamento...',
		store:new Ext.data.JsonStore(
		{
			url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
			id: 'id_depto',
			root: 'datos',
			sortInfo:{
				field: 'deppto.nombre',
				direction: 'ASC'
			},
			totalProperty: 'total',
			fields: ['id_depto','nombre'],
			// turn on remote sorting
			remoteSort: true,
			baseParams:{par_filtro:'nombre',tipo_filtro:'DEPTO_UO',estado:'activo',codigo_subsistema:'TES',modulo:'LB'}
		}),
		valueField: 'id_depto',
		triggerAction: 'all',
		displayField: 'nombre',
		hiddenName: 'id_depto',
		mode:'remote',
		pageSize:50,
		queryDelay:500,
		listWidth:'280',
		width:250
	}),
		
	tam_pag:50,
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_cuenta_bancaria'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_depto_lb'
			},
			type:'Field',
			form:true 
		},
		{
			config: {
				name: 'id_institucion',
				fieldLabel: 'Institucion',
				tinit: true,
				allowBlank: false,
				origen: 'INSTITUCION',
				baseParams:{es_banco:'si'},
				gdisplayField: 'nombre_institucion',
                anchor: '80%',
                gwidth: 250,
				renderer:function (value, p, record){return String.format('{0}', record.data['nombre_institucion']);}
			},
			type: 'ComboRec',
			id_grupo: 0,
			filters:{pfiltro:'inst.nombre',type:'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'nro_cuenta',
				fieldLabel: 'Nro Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
			type:'TextField',
			filters:{pfiltro:'ctaban.nro_cuenta',type:'string'},
			id_grupo:1,
			bottom_filter: true,
			grid:true,
			form:true
		},
		
		{
			config:{
				name: 'denominacion',
				fieldLabel: 'Denominación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 250,
				maxLength:100
			},
			type:'TextField',
			filters:{pfiltro:'ctaban.denominacion',type:'string'},
			id_grupo:1,
			bottom_filter: true,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'centro',
				fieldLabel: 'Central',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:25,
				typeAhead:true,
				triggerAction:'all',
				mode:'local',
				store:['si','no','otro']
			},
			valorInicial:'no',
			type:'ComboBox',
			filters:{pfiltro:'ctaban.centro',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'fecha_alta',
				fieldLabel: 'Fecha Alta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
			type:'DateField',
			filters:{pfiltro:'ctaban.fecha_alta',type:'date'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'saldo',
				fieldLabel: 'Saldo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
                    if(record.data.saldo == null){
                        return '';
                    }else{                        
                        return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(record.data.saldo,'0.000,00/i'));					
                        }
                    }				                				
			},
				type:'NumberField',				
				id_grupo:1,
				grid:true,
				form:false
		},                
		// {
         //    config:{
         //        name:'id_moneda',
         //        origen:'MONEDA',
         //        allowBlank:true,
         //        fieldLabel:'Moneda',
         //        gdisplayField:'codigo_moneda',//mapea al store del grid
         //        gwidth:50,
         //      //   renderer:function (value, p, record){return String.format('{0}', record.data['codigo_moenda']);}
         //     },
         //    type:'ComboRec',
         //    id_grupo:1,
         //    filters:{
         //        pfiltro:'mon.codigo',
         //        type:'string'
         //    },
         //    grid:true,
         //    form:true
         //  },
        {
            config:{
                name:'id_moneda',
                fieldLabel:'Moneda',
                allowBlank:false,
                anchor: '80%',
                gwidth: 250,
                emptyText:'Moneda...',
                store: new Ext.data.JsonStore({

                    url: '../../sis_parametros/control/Moneda/listarMoneda',
                    id: 'id_moneda',
                    root: 'datos',
                    sortInfo:{
                        field: 'prioridad',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_moneda','codigo','moneda'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'codigo#moneda'}
                }),
                valueField: 'id_moneda',
                displayField: 'moneda',
                gdisplayField:'codigo_moneda',
                hiddenName: 'id_moneda',
                forceSelection:true,
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                width:300,
                minChars:2
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   pfiltro:'mon.codigo',
                type:'string'
            },
            grid:true,
            form:true
        },
		{
			config:{
				name: 'fecha_baja',
				fieldLabel: 'Fecha Baja',
				allowBlank: true,
				anchor: '80%',
				gwidth: 250,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
			type:'DateField',
			filters:{pfiltro:'ctaban.fecha_baja',type:'date'},
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
			filters:{pfiltro:'ctaban.estado_reg',type:'string'},
			id_grupo:1,
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
			filters:{pfiltro:'ctaban.fecha_reg',type:'date'},
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
			filters:{pfiltro:'usu1.cuenta',type:'string'},
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
			filters:{pfiltro:'ctaban.fecha_mod',type:'date'},
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
		},
		{
       			config:{
       				name:'id_finalidads',
       				fieldLabel:'Finalidades',
       				allowBlank:true,
                    anchor: '80%',
                    gwidth: 250,
       				emptyText:'Roles...',
       				store: new Ext.data.JsonStore({
              			url: '../../sis_tesoreria/control/Finalidad/listarFinalidad',
       					id: 'id_finalidad',
       					root: 'datos',
       					sortInfo:{
       						field: 'nombre_finalidad',
       						direction: 'ASC'
       					},
       					totalProperty: 'total',
       					fields: ['id_finalidad','nombre_finalidad'],
       					// turn on remote sorting
       					remoteSort: true,
       					baseParams:{par_filtro:'nombre_finalidad'}
       					
       				}),
       				valueField: 'id_finalidad',
       				displayField: 'nombre_finalidad',
       				forceSelection:true,
       				typeAhead: true,
           			triggerAction: 'all',
           			lazyRender:true,
       				mode:'remote',
       				pageSize:10,
       				queryDelay:1000,
       				width:250,
       				minChars:2,
	       			enableMultiSelect:true

       			},
       			type:'AwesomeCombo',
       			id_grupo:0,
       			grid:false,
       			form:true
       	},
        {
            config:{
                name: 'forma_pago',
                fieldLabel: 'Forma de Pago',
                allowBlank: false,
                anchor: '80%',
                gwidth: 250,
                emptyText:'Forma de Pago...',
                store:new Ext.data.JsonStore(
                    {
                        // url: '../../sis_parametros/control/FormaPago/listarFormaPago',
                        url: '../../sis_parametros/control/FormaPago/listarFormaPagofil',
                        id: 'id_forma_pago',
                        root:'datos',
                        sortInfo:{
                            field:'orden',
                            direction:'ASC'
                        },
                        totalProperty:'total',
                        fields: ['id_forma_pago','desc_forma_pago','observaciones','cod_inter', 'codigo', 'orden'],
                        remoteSort: true,
                        baseParams:{par_filtro:'desc_forma_pago#codigo'}
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
            filters:{pfiltro:'ctaban.forma_pago',type:'string'},
            id_grupo:1,
            grid:false,
            form:true
        },

        {
            config:{
                name: 'id_proveedor_cta_bancaria',
                fieldLabel: 'Cuenta Bancaria',
                allowBlank: true,
                anchor: '80%',
                gwidth: 250,
                emptyText:'Cuenta Bancaria',
                store:new Ext.data.JsonStore(
                    {
                        url: '../../sis_parametros/control/ProveedorCtaBancaria/listarProveedorCtaBancaria',
                        id: 'id_proveedor_cta_bancaria',
                        root:'datos',
                        sortInfo:{
                            field:'nro_cuenta',
                            direction:'ASC'
                        },
                        totalProperty:'total',
                        fields: ['id_proveedor_cta_bancaria','nro_cuenta'],
                        remoteSort: true,
                        baseParams:{par_filtro:'nro_cuenta'}
                    }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nro_cuenta}</p></div></tpl>',
                valueField: 'id_proveedor_cta_bancaria',
                hiddenValue: 'id_proveedor_cta_bancaria',
                displayField: 'nro_cuenta',
                gdisplayField:'nro_cuenta_prov',
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
                renderer:function (value, p, record){return String.format('{0}', record.data['nro_cuenta_prov']);}
            },
            type:'ComboBox',
            filters:{pfiltro:'pctaban.nro_cuenta_prov',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        }
	],
	
	title:'Cuenta Bancaria',
	ActSave:'../../sis_tesoreria/control/CuentaBancaria/insertarCuentaBancaria',
	ActDel:'../../sis_tesoreria/control/CuentaBancaria/eliminarCuentaBancaria',
	ActList:'../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancariaUsuario',
	id_store:'id_cuenta_bancaria',
	fields: [
		{name:'id_cuenta_bancaria', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'fecha_baja', type: 'date',dateFormat:'Y-m-d'},
		{name:'nro_cuenta', type: 'string'},
		{name:'denominacion', type: 'string'},
		{name:'centro', type: 'string'},
		{name:'fecha_alta', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_institucion', type: 'numeric'},
		{name:'nombre_institucion', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},'id_moneda','codigo_moneda','id_finalidads',
        {name:'forma_pago', type: 'string'},
        {name:'saldo', type: 'numeric'},
        {name:'id_proveedor_cta_bancaria', type: 'numeric'},
        {name:'nro_cuenta_prov', type: 'string'}

	],
    arrayDefaultColumHidden:['forma_pago', 'id_proveedor_cta_bancaria'],
				
	sortInfo:{
		field: 'id_cuenta_bancaria',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,	
		
	onButtonEdit: function(){
		Phx.vista.CuentaBancaria.superclass.onButtonEdit.call(this);
		this.Cmp.nro_cuenta.disable();
	},
	onButtonNew: function(){
		Phx.vista.CuentaBancaria.superclass.onButtonNew.call(this);
		this.Cmp.id_depto_lb.setValue(this.cmbDepto.getValue());
		this.Cmp.nro_cuenta.enable();
	},
		
	tabsouth:[{
          url:'../../../sis_tesoreria/vista/cuenta_bancaria_periodo/CuentaBancariaPeriodo.php',
          title:'Periodos por Cuenta Bancaria',
          height : '50%',
          cls:'CuentaBancariaPeriodo'
        },
        {
          url:'../../../sis_tesoreria/vista/usuario_cuenta_banc/UsuarioCuentaBanc.php',
          title:'Usuarios',
          height : '50%',
          cls:'UsuarioCuentaBanc'
        },
        {
          url:'../../../sis_tesoreria/vista/tipo_cc_cuenta_libro/TipoCcCuentaLibro.php',
          title:'Tipo de Centros Permitidos',
          height : '50%',
          cls:'TipoCcCuentaLibro'
        }
     ]
   
})	
</script>
		
		
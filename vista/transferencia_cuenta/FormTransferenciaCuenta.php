<?php
/**
*@package pXP
*@file    FormTransferenciaCuenta.php
*@author  Gonzalo Sarmiento Sejas
*@date    09-10-2015
*@description muestra un formulario que muestra la cuenta y el monto de la transferencia
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormTransferenciaCuenta=Ext.extend(Phx.frmInterfaz,{
    ActSave:'../../sis_tesoreria/control/TsLibroBancos/transferirCuenta',
    layout:'fit',
    maxCount:0,
    constructor:function(config){   
        Phx.vista.FormTransferenciaCuenta.superclass.constructor.call(this,config);
        this.init(); 
		this.iniciarEventos();		
        this.loadValoresIniciales();		
    },
   
    Atributos:[
        {
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_cuenta_bancaria_origen'
            },
            type:'Field',
            form:true 
        },
		{
            //configuracion del componente
            config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_depto_lb'
            },
            type:'Field',
            form:true 
        },		
		{
            config:{
                name: 'id_cuenta_bancaria',
                fieldLabel: 'Cuenta Bancaria Destino',
                allowBlank: false,
                emptyText:'Elija una Cuenta...',
                store:new Ext.data.JsonStore(
                {
                    url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancariaUsuario',
                    id: 'id_cuenta_bancaria',
                    root:'datos',
                    sortInfo:{
                        field:'id_cuenta_bancaria',
                        direction:'ASC'
                    },
                    totalProperty:'total',
                    fields: ['id_cuenta_bancaria','nro_cuenta','nombre_institucion','codigo_moneda','centro','denominacion','id_moneda'],
                    remoteSort: true,
                    baseParams : {
						par_filtro :'nro_cuenta'
					}
                }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>Moneda: {codigo_moneda}, {nombre_institucion}</p><p>{denominacion}, Centro: {centro}</p></div></tpl>',
                valueField: 'id_cuenta_bancaria',
                hiddenValue: 'id_cuenta_bancaria',
                displayField: 'nro_cuenta',
                gdisplayField:'desc_cuenta_bancaria',
                listWidth:'280',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:20,
                anchor: '80%',
                queryDelay:500,
                gwidth: 250,
                minChars:2,
                resizable: true,
                renderer:function(value, p, record){return String.format('{0}', record.data['desc_cuenta_bancaria']);}
             },
            type:'ComboBox',
            filters:{pfiltro:'cb.nro_cuenta',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'tipo_cambio',
				fieldLabel: 'Tipo Cambio',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,				
			},
				type:'NumberField',                
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
				gwidth: 90,
				format: 'd/m/Y', 
				renderer:function (value,p,record){
					//return value?value.dateFormat('d/m/Y'):''
					if(value == null)
						value = '';
					else 
						value = value.dateFormat('d/m/Y');
					
					if(record.data['sistema_origen']=='FONDOS_AVANCE'){
						return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+'F.A. '+value+'</b></FONT>');
					}else{
						if(record.data['sistema_origen']=='KERP')						
							return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+'PG '+value+'</b></FONT>');					
						else
							return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+value+'</b></FONT>');
					}
				}
			},
				type:'DateField',
				filters:{pfiltro:'lban.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'a_favor',
				fieldLabel: 'A favor de',
				allowBlank: false,
				anchor: '80%',
				gwidth: 125,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'lban.a_favor',type:'string'},
				bottom_filter: true,
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'detalle',
				fieldLabel: 'Detalle',
				allowBlank: false,
				anchor: '80%',
				gwidth: 125,
				maxLength:400
			},
				type:'TextArea',
				filters:{pfiltro:'lban.detalle',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},	
		{
			config:{
				name: 'importe_transferencia',
				fieldLabel: 'Importe',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1310722
			},
			type:'NumberField',
			//filters:{pfiltro:'lban.importe_cheque',type:'numeric'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
            config:{
                name:'id_finalidad',
                fieldLabel:'Finalidad',
                allowBlank:false,
                emptyText:'Finalidad...',
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
                width:220,
                minChars:2,
                resizable:true,
                listWidth:'230',
                renderer : function(value, p, record) {
					//return String.format(record.data['nombre_finalidad']);
					return String.format('{0}', '<FONT COLOR="'+record.data['color']+'"><b>'+record.data['nombre_finalidad']+'</b></FONT>');
				}
            },
            type:'ComboBox',
            id_grupo:0,
            grid:true,
            form:true
        }
    ],
    
    title:'Transferencia Cuenta',
    
	iniciarEventos:function(){
		this.cmpIdCuentaBancariaOrigen = this.getComponente('id_cuenta_bancaria_origen');	
		this.cmpIdCuentaBancariaOrigen.setValue(this.data.id_cuenta_bancaria);
		this.cmpIdDeptoLb = this.getComponente('id_depto_lb');
        var fecha_ini = new Date();
        this.getComponente('fecha').setValue(fecha_ini.format('d/m/Y'));        	
		this.cmpIdDeptoLb.setValue(this.id_depto_lb);
        this.cmpTipoCambio = this.getComponente('tipo_cambio');
        this.ocultarComponente(this.cmpTipoCambio);
        var moneda_origen = this.data.id_moneda; 

        this.Cmp.id_cuenta_bancaria.on('select', (com, dat) => {            
            var moneda_destino = dat.data.id_moneda;            
            if( moneda_origen != moneda_destino) {
                this.cmpTipoCambio.reset();
                this.mostrarComponente(this.cmpTipoCambio);

                Ext.Ajax.request({
                        url:'../../sis_parametros/control/TipoCambio/listarTipoCambio',
                        params:{start:0,limit:1, fecha_trans:'si'},
                        success: function (resp){
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            this.cmpTipoCambio.setValue(reg.datos[0].oficial); 
                        },
                        failure: this.conexionFailure,
                        timeout:this.timeout,
                        scope:this
                    });
                    
                this.Cmp.tipo_cambio.modificado = true;                
            }else{
                this.cmpTipoCambio.reset();
                this.ocultarComponente(this.cmpTipoCambio);                
                this.Cmp.tipo_cambio.modificado = true;
            }            
        },this);          
		Ext.apply(this.Cmp.id_cuenta_bancaria.store.baseParams,{id_depto_lb:this.id_depto_lb,permiso : 'libro_bancos',id_cuenta_bancaria_origen:this.data.id_cuenta_bancaria});		
	},
	
	onSubmit:function(){
       //TODO passar los datos obtenidos del wizard y pasar  el evento save 
       if (this.form.getForm().isValid()) {
		   this.fireEvent('beforesave',this,this.getValues());
		   this.getValues();
       }
    },
	
    getValues:function(){
        var resp = {
                   id_cuenta_bancaria_origen:this.Cmp.id_cuenta_bancaria_origen.getValue(),
                   id_depto_lb:this.Cmp.id_depto_lb.getValue(),
				   id_cuenta_bancaria:this.Cmp.id_cuenta_bancaria.getValue(),
				   fecha:this.Cmp.fecha.getValue(),
				   a_favor:this.Cmp.a_favor.getValue(),
				   detalle:this.Cmp.detalle.getValue(),
				   importe_transferencia:this.Cmp.importe_transferencia.getValue(),
				   id_finalidad:this.Cmp.id_finalidad.getValue(),
                   tipo_cambio: this.Cmp.tipo_cambio.getValue()				   
            }   
         return resp;   
     }
    
})    
</script>

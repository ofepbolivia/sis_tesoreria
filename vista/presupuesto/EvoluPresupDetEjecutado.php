<?php
/**
 *@package pXP
*@file EvoluPresupDetEjecutado.php
*@author  BVP
*@date 
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.EvoluPresupDetEjecutado=Ext.extend(Phx.gridInterfaz,{
        nombreVista: 'ejecutado',        
        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.EvoluPresupDetEjecutado.superclass.constructor.call(this,config);
            this.init();
            var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();                        
            if(dataPadre){
                this.onEnablePanel(this, dataPadre);
            } else {
                this.bloquearMenus();
            }            
        },
        
        Atributos:[
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
                config:{
                    labelSeparator:'',
                    name: 'id_partida_ejecucion_fk',
                    fieldLabel: 'id_partida_ejecucion_fk',
                    inputType:'hidden'
                },
                type:'Field',
                form:true
            },            
            {
                config:{
                    name: 'nro_tramite',
                    fieldLabel: 'Nro. Tramite.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:10,
                    renderer:(value) => {
                        if(value == 'TOTAL'){
                            return  String.format('<div style="color:#004DFF; font-size:15px; font-weight: bold; text-align:center;"><b>{0}</b></div>',value);
                        }else{
                            return  value;
                        }                        
                    }                    
                },
                type:'TextField',
                filters:{pfiltro:'nro_tramite',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },           
            {
                config:{
                    name: 'ejecutado',
                    fieldLabel: 'Ejecutado',
                    currencyChar:' ',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,            
                    renderer:(value, p, record) => {
                        if(record.data.nro_tramite == 'TOTAL'){
                            return  String.format('<div style="color:#004DFF; font-size:12px; font-weight: bold; text-align:center;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));
                        }else{
                            return  String.format('<div style="text-align:right;"><b>{0}</b></div>',Ext.util.Format.number(value,'0,000.00'));
                        }                        
                    }                    
                },
                type:'MoneyField',
                filters:{pfiltro:'ejecutado',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'moneda',
                    fieldLabel: 'Moneda',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,                    
                },
                type:'TextField',
                filters:{pfiltro:'moneda',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },            
            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha Ejecucion',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'pej.fecha',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
		    },            
            {
                config:{
                    name: 'codigo_cc',
                    //name: 'desc_pres',
                    fieldLabel: 'Desc. Presupuesto',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type:'TextField',
                //filters:{pfiltro:'pre.descripcion',type:'string'},
                filters:{pfiltro:'vpre.codigo_cc',type:'string'},
                id_grupo:1,
                bottom_filter: true,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo_categoria',
                    fieldLabel: 'Código Categoría Programatica',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:1000
                },
                type:'TextField',
                //filters:{pfiltro:'pareje.nro_tramite',type:'string'},
                filters:{pfiltro:'cat.codigo_categoria',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo',
                    fieldLabel: 'Codigo Partida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 60,
                    maxLength:-5
                },
                type:'TextField',
                filters:{pfiltro:'par.codigo',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre_partida',
                    fieldLabel: 'Nombre Partida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength:-5
                },
                type:'TextField',
                filters:{pfiltro:'par.nombre_partida',type:'string'},
                bottom_filter: true,
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
                filters:{pfiltro:'obdet.estado_reg',type:'string'},
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
                filters:{pfiltro:'obdet.fecha_reg',type:'date'},
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
                filters:{pfiltro:'obdet.fecha_mod',type:'date'},
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
            }                       
        ],

        title:'Detalle',                        
        ActList:'../../sis_tesoreria/control/ObligacionPago/listarEvoluPresup',
        id_store:'id_partida_ejecucion',
        fields: [
            {name:'id_partida_ejecucion', type: 'numeric'},
            {name:'id_partida_ejecucion_fk', type: 'numeric'},
            {name:'moneda', type: 'string'},            
            {name:'ejecutado', type: 'numeric'},            
            {name:'nro_tramite', type: 'string'},
            {name:'nombre_partida', type: 'string'},
            {name:'codigo', type: 'string'},
            {name:'codigo_categoria', type:'string'},
            {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
            {name:'codigo_cc', type:'string'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},            
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'estado_reg', type: 'string'}            
        ],
        sortInfo:{
            field: 'id_partida_ejecucion',
            direction: 'DESC'
        },        
        bdel:false,
        bsave:false,
        bnew:false,
        bedit:false,
        btest:false,
        tam_pag: 1000,

        onReloadPage:function(m){        
        this.maestro=m;                       
        this.store.baseParams={id_partida_ejecucion_com:this.maestro.id_partida_ejecucion_com,tipo_interfaz:this.nombreVista}; 
        this.load({params:{start:0, limit:this.tam_pag}});        
        }
})
</script>
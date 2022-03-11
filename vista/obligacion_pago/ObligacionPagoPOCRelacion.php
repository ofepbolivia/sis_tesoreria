<?php
/**
 *@package pXP
 *@file gen-ObligacionPagoPOCRelacion.php
 *@author  Maylee Perez Pastor
 *@date 02-12-2020 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ObligacionPagoPOCRelacion=Ext.extend(Phx.gridInterfaz,{
            constructor:function(config){
                this.maestro=config.maestro;
                if(config.hasOwnProperty('idContenedorPadre')){
                    this.paginaMaestro = Phx.CP.getPagina(config.idContenedorPadre);
                } else {
                    this.paginaMaestro = undefined;
                }

                this.tbarItems = ['-',{
                    text: 'Ver todas las observaciones',
                    enableToggle: true,
                    pressed: false,
                    toggleHandler: function(btn, pressed) {

                        if(pressed){
                            this.store.baseParams.todos = 1;

                        }
                        else{
                            this.store.baseParams.todos = 0;
                        }

                        this.onButtonAct();
                    },
                    scope: this
                }];


                //llama al constructor de la clase padre
                Phx.vista.ObligacionPagoPOCRelacion.superclass.constructor.call(this, config);
                this.init();



                this.on('closepanel',function () {
                    this.paginaMaestro.reload();
                }, this);
                //this.store.baseParams = {  todos: 0, id_proceso_wf: config.id_proceso_wf, id_estado_wf: config.id_estado_wf, num_tramite: config.num_tramite};
                console.log('relacionar2', config.id_proceso_wf )
                this.store.baseParams = { id_proceso_wf: config.id_proceso_wf};
                this.load({params: { start:0, limit: this.tam_pag } })
            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_relacion_proceso_pago'
                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_proceso_wf'
                    },
                    type:'Field',
                    form:true
                },

                {
                    config: {
                        name: 'id_obligacion_pago',
                        hiddenName: 'id_obligacion_pago',
                        fieldLabel: 'Nro. Trámite',
                        // typeAhead: false,
                        forceSelection: true,
                        allowBlank: false,
                        // disabled: true,
                        emptyText: 'Nro. Trámite...',
                        store: new Ext.data.JsonStore({
                            url:'../../sis_tesoreria/control/ObligacionPago/listarObligacioPagoCombos',
                            id: 'id_obligacion_pago',
                            root: 'datos',
                            sortInfo:{
                                field: 'num_tramite',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_obligacion_pago','num_tramite'],
                            // turn on remote sorting
                            remoteSort: true//,
                            //baseParams:Ext.apply({par_filtro:'num_tramite'})

                        }),
                        valueField: 'id_obligacion_pago',
                        displayField: 'num_tramite',
                        gdisplayField: 'num_tramite',
                        triggerAction: 'all',
                        lazyRender: true,
                        resizable: true,
                        mode: 'remote',
                        pageSize: 10,
                        queryDelay: 1000,
                        listWidth: 280,
                        minChars: 2,
                        gwidth: 100,
                        anchor: '80%',
                        renderer: function (value, p, record) {
                            //if (record.data['num_tramite']) {
                                return String.format('{0}', record.data['num_tramite']);
                            //}
                            //return '';

                        },
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{num_tramite}</b></p></div></tpl>',
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'op.num_tramite',
                        type: 'string'
                    },
                    bottom_filter: true,
                    grid: true,
                    form: true
                },


                {
                    config:{
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 250,
                        maxLength:2000
                    },
                    type:'TextArea',
                    filters: { pfiltro: 'rp.observaciones', type:'string' },
                    id_grupo:1,
                    grid:true,
                    form:true
                },

                {
                    config:{
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'rp.fecha_reg',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },


                {
                    config:{
                        name: 'desc_fin',
                        fieldLabel: 'Desc fin',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:-100
                    },
                    type: 'TextField',
                    filters: { pfiltro: 'rp.desc_fin', type: 'string' },
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config:{
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type:'TextField',
                    filters: { pfiltro: 'rp.estado_reg', type:'string' },
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
                    filters:{pfiltro:'rp.fecha_mod',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:false
                }
            ],
            tam_pag:50,
            title:'Relación Proceso',
            ActSave:'../../sis_tesoreria/control/ObligacionPago/insertarRelacionProceso',
            ActDel:'../../sis_tesoreria/control/ObligacionPago/eliminarRelacionProceso',
            ActList:'../../sis_tesoreria/control/ObligacionPago/listarRelacionProceso',
            id_store:'id_relacion_proceso_pago',
            fields: [
                {name:'id_relacion_proceso_pago', type: 'numeric'},
                {name:'observaciones', type: 'string'},
                {name:'id_obligacion_pago', type: 'numeric'},
                {name:'num_tramite', type: 'string'},
                {name:'id_proceso_wf', type: 'numeric'},

                {name:'estado_reg', type: 'string'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'}

            ],

            onButtonNew:function(){
                Phx.vista.ObligacionPagoPOCRelacion.superclass.onButtonNew.call(this);

                this.Cmp.id_proceso_wf.setValue( this.id_proceso_wf );

            },

            onButtonEdit:function(){
                Phx.vista.ObligacionPagoPOCRelacion.superclass.onButtonEdit.call(this);

            },
            preparaMenu:function(n){
                var data = this.getSelectedData();
                var tb =this.tbar;

                Phx.vista.ObligacionPagoPOCRelacion.superclass.preparaMenu.call(this,n);

                /*if(this.store.baseParams.todos == 1){

                    this.getBoton('new').disable();
                    this.getBoton('edit').disable();
                    this.getBoton('del').disable();
                    this.getBoton('btnCerrar').disable();
                }
                else{
                    if(data.estado == 'abierto'){
                        this.getBoton('btnCerrar').enable();
                    }
                    else{
                        this.getBoton('btnCerrar').disable();
                    }
                }
                return tb*/
            },
            liberaMenu:function(){
                var tb = Phx.vista.ObligacionPagoPOCRelacion.superclass.liberaMenu.call(this);
                //this.getBoton('btnCerrar').disable();
                //return tb
            },








            sortInfo:{
                field: 'id_relacion_proceso_pago',
                direction: 'ASC'
            },
            bdel: true,
            bedit: false,
            bsave: false
        }
    )
</script>

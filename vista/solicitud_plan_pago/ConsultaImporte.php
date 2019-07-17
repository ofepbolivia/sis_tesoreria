<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsultaImporte = Ext.extend(Phx.gridInterfaz, {
            tam_pag: 50,
            constructor: function (config) {

                this.maestro = config;
                //llama al constructor de la clase padre
                Phx.vista.ConsultaImporte.superclass.constructor.call(this, config);

            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_cotizacion'
                    },
                    type: 'Field',
                    form: true
                },


                // {
                //     config:{
                //         name: 'estado',
                //         fieldLabel: 'Estado',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 110,
                //         renderer: function(value,p,record){
                //             if(record.data.estado=='anulado'){
                //                 return String.format('<b><font color="red">{0}</font></b>', value);
                //             }
                //             else if(record.data.estado=='adjudicado'){
                //                 return String.format('<div title="Esta cotización tiene items adjudicados"><b><font color="green">{0}</font></b></div>', value);
                //             }
                //             else{
                //                 return String.format('{0}', value);
                //             }},
                //         maxLength:30
                //     },
                //     type:'TextField',
                //     filters:{pfiltro:'cot.estado',
                //         options: ['borrador','cotizado','adjudicado','recomendado','contro_pendiente','contrato_eleborado','pago_habilitado','finalizada','anulada'],
                //         type:'list'},
                //
                //     id_grupo:1,
                //     bottom_filter: true,
                //     grid:true,
                //     form:false
                // },

                // {
                //     config:{
                //         name: 'obs',
                //         fieldLabel: 'Obs',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         maxLength:100
                //     },
                //     type:'TextArea',
                //     filters:{pfiltro:'cot.obs',type:'string'},
                //     id_grupo:1,
                //     grid:true,
                //     form:true
                // },
                // {
                //     config:{
                //         name: 'fecha_adju',
                //         fieldLabel: 'Fecha Adju',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         format: 'd/m/Y',
                //         renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                //     },
                //     type:'DateField',
                //     filters:{pfiltro:'cot.fecha_adju',type:'date'},
                //     id_grupo:1,
                //     grid:true,
                //     form:true
                // },

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
                    filters: {pfiltro: 'cot.estado_reg', type: 'string'},
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
                    filters: {pfiltro: 'cot.fecha_reg', type: 'date'},
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
                    filters: {pfiltro: 'cot.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        hidden: true,
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
                }
            ],

            title: 'Consulta',
            ActSave: '../../sis_adquisiciones/control/Cotizacion/insertarCotizacion',
            ActDel: '../../sis_adquisiciones/control/Cotizacion/eliminarCotizacion',
            ActList: '../../sis_adquisiciones/control/Cotizacion/listarCotizacion',
            id_store: 'id_cotizacion',
            fields: [
                {name: 'id_cotizacion', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                // {name:'estado', type: 'string'},

                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'}
            ],

            // arrayDefaultColumHidden:['id_fecha_reg'],
            sortInfo: {
                field: 'id_cotizacion',
                direction: 'ASC'
            },


            bdel: true,
            bsave: false

        }
    )
</script>
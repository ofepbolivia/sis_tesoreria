<?php
/**
 * @package pXP
 * @file gen-PlanPagoPOC.php
 * @author  Maylee Perez Pastor
 * @date 02-12-2020 10:22:05
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PlanPagoPOC = Ext.extend(Phx.gridInterfaz, {
        fheight: '90%',
        fwidth: '85%',
        accionFormulario: undefined, //define la accion que se ejcuta en formulario new o edit
        porc_ret_gar: 0,//valor por defecto de retencion de garantia
        constructor: function (config) {
            this.idContenedor = config.idContenedor;
            //definicion de grupos para fomrulario
            var me = this;
            this.Grupos = [
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

                }];


            //llama al constructor de la clase padre
            Phx.vista.PlanPagoPOC.superclass.constructor.call(this, config);
            this.init();
            this.addButton('ini_estado', {
                grupo: [0, 1],
                argument: {estado: 'inicio'},
                text: 'Dev. a borrador',
                iconCls: 'batras',
                disabled: true,
                handler: this.antEstado,
                tooltip: '<b>Retorna el plan de pagos al estado borrador</b>'
            });
            this.addButton('ant_estado', {
                grupo: [0, 1],
                argument: {estado: 'anterior'},
                text: 'Anterior',
                iconCls: 'batras',
                disabled: true,
                handler: this.antEstado,
                tooltip: '<b>Pasar al Anterior Estado</b>'
            });
            this.addButton('sig_estado', {
                grupo: [0, 1],
                text: 'Aprobar/Sig.',
                iconCls: 'badelante',
                disabled: true,
                handler: this.sigEstado,
                tooltip: '<b>Aprueba y pasa al Siguiente Estado</b>'
            });
            this.addButton('SolPlanPago', {
                grupo: [0, 1],
                text: 'Sol. Plan Pago.',
                iconCls: 'bpdf32',
                disabled: true,
                handler: this.onBtnSolPlanPago,
                tooltip: '<b>Solicitud Plan Pago</b><br/> Incremeta el presupuesto exacto para proceder con el pago'
            });
            this.addButton('btnChequeoDocumentosWf',
                {
                    text: 'Documentos',
                    grupo: [0, 1],
                    iconCls: 'bchecklist',
                    disabled: true,
                    handler: this.loadCheckDocumentosSolWf,
                    tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documentos requeridos en la solicitud seleccionada.'
                }
            );

            //08-05-2020 (may) Doc compra venta desde la cuota
            this.addButton('btnDocCmpVnt', {
                text: 'Doc. Cmp/Vnt',
                iconCls: 'brenew',
                disabled: true,
                handler: this.loadDocCmpVnt,
                tooltip: '<b>Documentos de Compra/Venta</b><br/>Muestra los documentos relacionados'
            });

            this.addButton('btnPagoRel',
                {
                    text: 'Pagos Rel.',
                    grupo: [0, 1],
                    iconCls: 'binfo',
                    disabled: true,
                    handler: this.loadPagosRelacionados,
                    tooltip: '<b>Pagos Relacionados</b><br/>Abre una venta con pagos similares o relacionados.'
                }
            );


            this.addButton('btnObs', {
                grupo: [0, 1],
                text: 'Obs Wf',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.onOpenObs,
                tooltip: '<b>Observaciones</b><br/><b>Observaciones del WF</b>'
            });
            //18-06-2019, se oculta boton Inc. Pres.
            // this.addButton('SincPresu', {
            //     grupo: [0, 1],
            //     text: 'Inc. Pres.',
            //     iconCls: 'balert',
            //     disabled: true,
            //     handler: this.onBtnSincPresu,
            //     tooltip: '<b>Incrementar Presupuesto</b><br/> Incremeta el presupuesto exacto para proceder con el pago'
            // });

        },
        tam_pag: 50,

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
                ['devengado_pagado_1c', 'Aplicación de Anticipo (Combustible)']
            ],

            'ANT_PARCIAL': [
                ['ant_parcial', 'Anticipo Parcial(No ejecuta presupuesto, Con retenciones parciales en cada pago)']
            ],

            'DEVENGAR': [['pagado', 'Pagar']],

            'ANTICIPO': [['ant_aplicado', 'Aplicacion de Anticipo']],

            'RENDICION': [['det_rendicion', 'Rendicion Ant']],

            'ESPECIAL': [['especial', 'Pago simple (sin efecto presupuestario)']]

        },

        Atributos: [
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
            {
                config: {
                    name: 'revisado_asistente',
                    fieldLabel: 'Rev',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 50,
                    renderer: function (value, p, record) {
                        if (record.data['revisado_asistente'] == 'si')
                            return String.format('{0}', "<div style='text-align:center'><img title='Revisado / Permite ver pagos relacionados'  src = '../../../lib/imagenes/ball_green.png' align='center' width='24' height='24'/></div>");
                        else
                            return String.format('{0}', "<div style='text-align:center'><img title='No revisado / Permite ver pagos relacionados'  src = '../../../lib/imagenes/ball_white.png' align='center' width='24' height='24'/></div>");
                    },
                },
                type: 'Checkbox',
                filters: {pfiltro: 'plapa.revisado_asistente', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },
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
                            return value;
                        }

                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'op.num_tramite', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },

            {
                config: {
                    name: 'numero_op',
                    fieldLabel: 'Obl. Pago',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 130,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {pfiltro: 'op.numero', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },

            {
                config: {
                    name: 'tiene_form500',
                    fieldLabel: 'Form 500',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 70,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {pfiltro: 'op.numero', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },


            {
                config: {
                    name: 'nro_cuota',
                    fieldLabel: 'Cuo. N#',
                    allowBlank: true,
                    gwidth: 70,
                    renderer: function (value, p, record) {
                        if (record.data.total_pagado == record.data.monto_ejecutar_total_mo) {
                            return String.format('<b><font color="green">{0}</font></b>', value);
                        }
                        else {

                            if (record.data.total_prorrateado != record.data.monto_ejecutar_total_mo) {
                                return String.format('<b><font color="red">{0}</font></b>', value);
                            }
                            else {
                                if (record.data.total_pagado != record.data.monto_ejecutar_total_mo
                                    && (record.data.tipo == 'devengado' || record.data.tipo == 'devengado_pagado')) {
                                    return String.format('<b><font color="orange">{0}</font></b>', value);
                                }
                                else {
                                    if (record.data.tipo == 'pagado' || record.data.tipo == 'ant_aplicado') {
                                        return String.format('--> {0}', value);
                                    }
                                    else {
                                        return String.format('{0}', value);
                                    }

                                }

                            }
                        }
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
                        /*dato = (dato == '' && value == 'devengado') ? 'Devengar' : dato;
                        dato = (dato == '' && value == 'devengado_rrhh') ? 'Devengar' : dato;
                        dato = (dato == '' && value == 'devengado_pagado') ? 'Devengar y pagar (2 cbte)' : dato;
                        */
                        dato = (dato == '' && value == 'pagado') ? 'Pagar' : dato;
                        /*
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
                        */
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
                form: false,
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
                form: false
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
            // {
            //     config: {
            //         name: 'forma_pago',
            //         fieldLabel: 'Forma de Pago',
            //         allowBlank: false,
            //         gwidth: 100,
            //         maxLength: 30,
            //         items: [
            //             {boxLabel: 'Cheque', name: 'fp-auto', inputValue: 'cheque', checked: true},
            //             {boxLabel: 'Transferencia', name: 'fp-auto', inputValue: 'transferencia'}
            //             //,{boxLabel: 'Caja',name: 'fp-auto', inputValue: 'Caja'}
            //         ]
            //     },
            //     type: 'RadioGroupField',
            //     filters: {pfiltro: 'plapa.forma_pago', type: 'string'},
            //     id_grupo: 1,
            //     grid: false,
            //     form: false
            // },

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
                form:false
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
                bottom_filter: true,
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

        title: 'Plan Pago',
        ActSave: '../../sis_tesoreria/control/PlanPago/insertarPlanPago',
        ActDel: '../../sis_tesoreria/control/PlanPago/eliminarPlanPago',
        ActList: '../../sis_tesoreria/control/PlanPago/listarPlanPago',
        id_store: 'id_plan_pago',
        fields: [
            {name: 'id_plan_pago', type: 'numeric'},
            {name: 'id_int_comprobante', type: 'numeric'},
            'id_obligacion_pago',
            {name: 'estado_reg', type: 'string'},
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
            'numero_op',
            'id_estado_wf',
            'id_depto_conta',
            'id_moneda', 'tipo_moneda', 'desc_moneda',
            'num_tramite', 'monto_excento',
            'proc_monto_excento_var', 'obs_wf', 'descuento_inter_serv',
            'obs_descuento_inter_serv', 'porc_monto_retgar', 'desc_funcionario1', 'revisado_asistente',
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
            'desc_multa'
        ],

        arrayDefaultColumHidden: ['id_fecha_reg', 'id_fecha_mod',
            'fecha_mod', 'usr_reg', 'usr_mod', 'estado_reg', 'fecha_reg', 'numero_op', 'id_plantilla', 'monto_excento', 'forma_pago', 'nro_cheque', 'nro_cuenta_bancaria',
            'descuento_anticipo', 'monto_retgar_mo', 'monto_no_pagado', 'otros_descuentos', 'descuento_inter_serv', 'descuento_ley', 'id_depto_lb',
            'id_depto_lb', 'id_cuenta_bancaria', 'obs_wf', 'fecha_dev', 'fecha_pag', 'obs_descuentos_anticipo', 'obs_monto_no_pagado',
            'obs_otros_descuentos', 'obs_descuentos_ley', 'obs_descuento_inter_serv', 'monto_ajuste_ag', 'monto_ajuste_siguiente_pag', 'fecha_costo_ini',
            'fecha_costo_fin', 'funcionario_wf', 'monto_anticipo', 'monto', 'monto_ejecutar_total_mo', 'monto_establecido','nit'],


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
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total a ejecutar presupeustariamente:&nbsp;&nbsp;</b> {monto_ejecutar_total_mo}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Liquido pagable:&nbsp;&nbsp;</b> {liquido_pagable}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Libro de Bancos:&nbsp;&nbsp;</b> {desc_depto_lb}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cuenta bancaria:&nbsp;&nbsp;</b> {desc_cuenta_bancaria}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Deposito:&nbsp;&nbsp;</b> {desc_deposito}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Intrucciones:&nbsp;&nbsp;</b> {obs_wf}</p><br>'
            )
        }),
        onBtnSincPresu: function () {
            var d = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url: '../../sis_tesoreria/control/PlanPago/sincronizarPresupuesto',
                params: {id_plan_pago: d.id_plan_pago},
                success: this.successSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },


        antEstado: function (res) {
            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
                'Estado de Wf',
                {
                    modal: true,
                    width: 450,
                    height: 250
                }, {data: rec.data, estado_destino: res.argument.estado}, this.idContenedor, 'AntFormEstadoWf',
                {
                    config: [{
                        event: 'beforesave',
                        delegate: this.onAntEstado,
                    }
                    ],
                    scope: this
                })
        },

        onAntEstado: function (wizard, resp) {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url: '../../sis_tesoreria/control/PlanPago/anteriorEstadoPlanPago',
                params: {
                    id_proceso_wf: resp.id_proceso_wf,
                    id_estado_wf: resp.id_estado_wf,
                    obs: resp.obs,
                    estado_destino: resp.estado_destino
                },
                argument: {wizard: wizard},
                success: this.successEstadoSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },

        successEstadoSinc: function (resp) {
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },

        sigEstado: function () {
            var rec = this.sm.getSelected();

            if ((rec.data.estado == 'vbsolicitante' && rec.data.tipo_obligacion == 'adquisiciones') &&
                (rec.data['fecha_conformidad'] == '' || rec.data['fecha_conformidad'] == undefined || rec.data['fecha_conformidad'] == null)
                && (rec.data.tipo == 'devengado' || rec.data.tipo == 'devengado_pagado' || rec.data.tipo == 'devengado_pagado_1c')) {
                Ext.Msg.show({
                    title: 'Confirmación',
                    scope: this,
                    msg: 'Esta segur@ de solicitar el pago sin generar la conformidad? Para generarla presione el botón "Conformidad"',
                    buttons: Ext.Msg.YESNO,
                    fn: function (id, value, opt) {
                        if (id == 'yes') {
                            this.mostrarWizard(rec);
                        } else {
                            opt.hide;
                        }
                    },
                    animEl: 'elId',
                    icon: Ext.MessageBox.WARNING
                }, this);
            } else if ((rec.data.estado == 'borrador' && rec.data.tipo_obligacion != 'adquisiciones') &&
                (rec.data['fecha_conformidad'] == '' || rec.data['fecha_conformidad'] == undefined || rec.data['fecha_conformidad'] == null)
                && (rec.data.tipo == 'devengado' || rec.data.tipo == 'devengado_pagado' || rec.data.tipo == 'devengado_pagado_1c')
                && this.maestro.uo_ex == 'no') {
                Ext.Msg.show({
                    title: 'Confirmación',
                    scope: this,
                    msg: 'Al solicitar el pago se generará una conformidad implícita bajo responsabilidad del funcionario solicitante. Desea Continuar?',
                    buttons: Ext.Msg.YESNO,
                    fn: function (id, value, opt) {
                        if (id == 'yes') {
                            this.mostrarWizard(rec);
                        } else {
                            opt.hide;
                        }
                    },
                    animEl: 'elId',
                    icon: Ext.MessageBox.WARNING
                }, this);

            } else {
                this.mostrarWizard(rec);
            }

        },

        mostrarWizard: function (rec) {
            var configExtra = [];
            //si el estado es vbfinanzas agregamos la opcion para selecionar el depto de Libro bancos
            /*
            if(rec.data.estado == 'vbfin'){
                 configExtra = [{
                                      config:{
                                       name:'id_depto_lb',
                                       hiddenName: 'id_depto_lb',
                                       url: '../../sis_parametros/control/Depto/listarDepto',
                                          origen: 'DEPTO',
                                          allowBlank: false,
                                          fieldLabel: 'Libro de bancos destino',
                                          disabled: false,
                                          width: '80%',
                                       baseParams: { estado:'activo', codigo_subsistema: 'TES',modulo:'LB' },
                                      },
                                      //type:'TrigguerCombo',
                                      type:'ComboRec',
                                      id_grupo: 1,
                                      form:true
                                  }];
             }*/


            this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
                'Estado de Wf',
                {
                    modal: true,
                    width: 700,
                    height: 450
                }, {
                    configExtra: configExtra,
                    data: {
                        id_estado_wf: rec.data.id_estado_wf,
                        id_proceso_wf: rec.data.id_proceso_wf,
                        fecha_ini: rec.data.fecha_tentativa,
                        //url_verificacion:'../../sis_tesoreria/control/PlanPago/siguienteEstadoPlanPago'


                    }
                }, this.idContenedor, 'FormEstadoWf',
                {
                    config: [{
                        event: 'beforesave',
                        delegate: this.onSaveWizard,

                    },
                        {
                            event: 'requirefields',
                            delegate: function (wizard, mensaje) {
                                this.onButtonEdit();

                                if (mensaje.indexOf("Fecha Inicio,Fecha Fin") != -1) {
                                    this.Cmp.fecha_costo_ini.allowBlank = false;
                                    this.Cmp.fecha_costo_fin.allowBlank = false;
                                    this.form.getForm().isValid();
                                } else {
                                    alert(mensaje);
                                }

                                this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
                                this.formulario_wizard = 'si';
                            }

                        }],

                    scope: this
                });
        },


        onSaveWizard: function (wizard, resp) {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/PlanPago/siguienteEstadoPlanPago',
                params: {

                    id_proceso_wf_act: resp.id_proceso_wf_act,
                    id_estado_wf_act: resp.id_estado_wf_act,
                    id_tipo_estado: resp.id_tipo_estado,
                    id_funcionario_wf: resp.id_funcionario_wf,
                    id_depto_wf: resp.id_depto_wf,
                    obs: resp.obs,
                    id_depto_lb: resp.id_depto_lb,
                    json_procesos: Ext.util.JSON.encode(resp.procesos)
                },
                success: this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        successSave: function (resp) {
            Phx.vista.PlanPago.superclass.successSave.call(this, resp);
            var rec = this.sm.getSelected();
            if (this.formulario_wizard == 'si') {
                this.mostrarWizard(rec);
                this.formulario_wizard = 'no';
                this.Cmp.fecha_costo_ini.allowBlank = true;
                this.Cmp.fecha_costo_fin.allowBlank = true;
            }
            console.log('llega, formulario_wizard', this.formulario_wizard)
        },

        successWizard: function (resp) {
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },


        successSinc: function (resp) {
            Phx.CP.loadingHide();
            //this.wDEPTO.hide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (reg.ROOT.datos.resultado != 'falla') {

                this.reload();
            } else {
                alert(reg.ROOT.datos.mensaje)
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

        onBtnSolPlanPago: function () {
            var rec = this.sm.getSelected();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/PlanPago/solicitudPlanPago',
                params: {'id_plan_pago': rec.data.id_plan_pago, id_obligacion_pago: rec.data.id_obligacion_pago},
                success: this.successExport,
                failure: function () {
                    console.log("fail");
                },
                timeout: function () {
                    console.log("timeout");
                },
                scope: this
            });
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

            monto_ret_gar = this.Cmp.monto_retgar_mo.getValue();


            var liquido = this.Cmp.monto.getValue() - this.Cmp.monto_no_pagado.getValue() - this.Cmp.otros_descuentos.getValue() - monto_ret_gar - this.Cmp.descuento_ley.getValue() - this.Cmp.descuento_inter_serv.getValue() - this.Cmp.descuento_anticipo.getValue();
            //this.Cmp.liquido_pagable.setValue(liquido > 0 ? liquido : 0);
            this.Cmp.liquido_pagable.setValue(liquido);
            var eje = this.Cmp.monto.getValue() - this.Cmp.monto_no_pagado.getValue() - this.Cmp.monto_anticipo.getValue();
            this.Cmp.monto_ejecutar_total_mo.setValue(eje > 0 ? eje : 0);

        },

        onButtonEdit: function () {
            this.accionFormulario = 'EDIT';
            var data = this.getSelectedData();
            //deshabilita el cambio del tipo de pago
            //may 08-01-2020 solo para visto bueno conta puede modificar el tipo de cuota
            console.log('llegamay', data.estado);
            if (data.estado == 'vbconta') {
                this.Cmp.tipo.enable();
            }else{
                this.Cmp.tipo.disable();
            }

            this.Cmp.fecha_tentativa.enable();
            //08-01-2020 (may) modificacion para que pueda listar lo mismo que un new
            //this.Cmp.tipo.store.loadData(this.arrayStore.TODOS);
            this.Cmp.tipo.store.loadData(this.arrayStore.DEVENGAR);
            this.ocultarGrupo(2); //ocultar el grupo de ajustes

            //08-01-2020 (may) solo en vbconta que todo sea visible para el boton del edit
            //segun el tipo define los campo visibles y no visibles
            if (data.estado != 'vbconta') {
                this.setTipoPago[data.tipo](this, data);
            }

            this.tmp_porc_monto_excento_var = undefined;


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

            Phx.vista.PlanPagoPOC.superclass.onButtonEdit.call(this);
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

        loadCheckDocumentosSolWf: function () {
            var rec = this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;

            if (this.nombreEstadoVista == 'vbconta') {
                rec.data.check_fisico = 'si';
            }


            var tmp = {};
            tmp = Ext.apply(tmp, rec.data);

            if (rec.data.estado == 'vbgerente') {
                rec.data.esconder_toogle = 'si';
            }

            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Chequear documento del WF',
                {
                    width: '90%',
                    height: 500
                },
                Ext.apply(tmp, {
                    tipo: 'plan_pago',
                    lblDocProcCf: 'Solo doc de Pago',
                    lblDocProcSf: 'Todo del Trámite',
                    todos_documentos: 'si'

                }),
                this.idContenedor,
                'DocumentoWf'
            );
        },

        //08-05-2020 (may) Doc compra venta desde la cuota
        loadDocCmpVnt: function () {
            var rec = this.sm.getSelected();
            console.log('rec', rec);
            rec.data.disparador = 'obligacion';
            console.log('despues', rec);
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/doc_compra_venta/DocCompraVentaCbte.php', 'Documentos del Cbte', {

                width: '70%',
                height: '80%'
            }, rec.data, this.idContenedor, 'DocCompraVentaCbte');
        },


        loadPagosRelacionados: function () {
            var rec = this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;


            Phx.CP.loadWindows('../../../sis_tesoreria/vista/plan_pago/RepFilPlanPago.php',
                'Pagos similares',
                {
                    width: '90%',
                    height: '90%'
                },
                rec.data,
                this.idContenedor,
                'RepFilPlanPago'
            )
        },

        creaFormularioConformidad: function () {

            this.formConformidad = new Ext.form.FormPanel({
                id: this.idContenedor + '_CONFOR',
                items: [new Ext.form.TextArea({
                    fieldLabel: 'Conformidad',
                    name: 'conformidad',
                    height: 150,
                    allowBlank: false,
                    width: '95%'
                }),
                    new Ext.form.DateField({
                        fieldLabel: 'Fecha Recepción/Informe',
                        format: 'd/m/Y',
                        name: 'fecha_conformidad',
                        allowBlank: false,
                        width: '70%'
                    })],
                autoScroll: false,
                //height: this.fheight,
                autoDestroy: true,
                autoScroll: true
            });


            // Definicion de la ventana que contiene al formulario
            this.windowConformidad = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Datos Acta Conformidad',
                modal: true,
                width: 400,
                height: 300,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [{
                    text: 'Guardar',
                    arrowAlign: 'bottom',
                    handler: this.onSubmitConformidad,
                    argument: {
                        'news': false
                    },
                    scope: this

                },
                    {
                        text: 'Declinar',
                        handler: this.onDeclinarConformidad,
                        scope: this
                    }],
                items: this.formConformidad,
                // autoShow:true,
                autoDestroy: true,
                closeAction: 'hide'
            });
        },

        onButtonConformidad: function () {
            var data = this.sm.getSelected().data;
            if (data['fecha_conformidad'] == '' || data['fecha_conformidad'] == undefined || data['fecha_conformidad'] == null) {
                this.formConformidad.getForm().findField('conformidad').setValue(data.conformidad);
                this.formConformidad.getForm().findField('fecha_conformidad').setValue(data.fecha_conformidad);
                this.windowConformidad.show();
            } else {
                Ext.Msg.show({
                    title: 'Alerta',
                    scope: this,
                    msg: 'El acta de conformidad ya se encuentra firmada. Esta seguro de volver a firmarla?',
                    buttons: Ext.Msg.YESNO,
                    fn: function (id, value, opt) {
                        if (id == 'yes') {
                            this.formConformidad.getForm().findField('conformidad').setValue(data.conformidad);
                            this.formConformidad.getForm().findField('fecha_conformidad').setValue(data.fecha_conformidad);
                            this.windowConformidad.show();
                        }
                    },
                    animEl: 'elId',
                    icon: Ext.MessageBox.WARNING
                }, this);
            }

        },

        onSubmitConformidad: function () {
            var d = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/PlanPago/generarConformidad',
                success: this.successConformidad,
                failure: this.failureConformidad,
                params: {
                    'id_plan_pago': d.id_plan_pago,
                    'conformidad': this.formConformidad.getForm().findField('conformidad').getValue(),
                    'fecha_conformidad': this.formConformidad.getForm().findField('fecha_conformidad').getValue().dateFormat('d/m/Y')
                },
                timeout: this.timeout,
                scope: this
            });

        },

        successConformidad: function (resp) {
            this.windowConformidad.hide();
            Phx.vista.PlanPagoPOC.superclass.successDel.call(this, resp);

        },

        failureConformidad: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.PlanPagoPOC.superclass.conexionFailure.call(this, resp);

        },

        onDeclinarConformidad: function () {
            this.windowConformidad.hide();
        },


        inicioValores: function () {

            this.Cmp.fecha_tentativa.minValue = new Date();
            this.Cmp.fecha_tentativa.setValue(new Date());
            this.Cmp.nombre_pago.setValue(this.maestro.desc_proveedor);
            this.Cmp.monto.setValue(0);
            this.Cmp.descuento_anticipo.setValue(0);
            this.Cmp.descuento_inter_serv.setValue(0);
            this.Cmp.descuento_ley.setValue(0)
            this.Cmp.porc_descuento_ley.setValue(0)
            this.Cmp.monto_anticipo.setValue(0);
            this.Cmp.monto_no_pagado.setValue(0);
            this.Cmp.otros_descuentos.setValue(0);
            this.Cmp.liquido_pagable.setValue(0);
            this.Cmp.monto_ejecutar_total_mo.setValue(0);
            this.Cmp.monto_retgar_mo.setValue(0);
            this.Cmp.descuento_ley.setValue(0);
            this.Cmp.liquido_pagable.setReadOnly(true);
            this.Cmp.monto_ejecutar_total_mo.setReadOnly(true);

            this.Cmp.monto_ajuste_ag.setValue(0);
            this.Cmp.monto_ajuste_siguiente_pag.setValue(0);
            this.Cmp.monto_anticipo.setValue(0);

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

        ocultarCheCue: function (me, pFormaPago) {

            /*  if (pFormaPago == 'transferencia') {

                  //Deshabilita campo cheque
                  // me.Cmp.nro_cheque.allowBlank=true;
                  // me.Cmp.nro_cheque.setValue('');
                  // me.Cmp.nro_cheque.disable();

                  //Habilita nrocuenta bancaria destino
                  // me.Cmp.nro_cuenta_bancaria.allowBlank = false;
                  // me.Cmp.nro_cuenta_bancaria.enable();
                  me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                  me.Cmp.id_proveedor_cta_bancaria.enable();

                  // me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                  // me.Cmp.id_proveedor_cta_bancaria.enable();

              }
              else {

                  //cheque
                  //Habilita campo cheque
                  // me.Cmp.nro_cheque.allowBlank=false;
                  // me.Cmp.nro_cheque.enable();
                  //Habilita nrocuenta bancaria destino
                  // me.Cmp.nro_cuenta_bancaria.allowBlank = true;
                  // me.Cmp.nro_cuenta_bancaria.setValue('');
                  // me.Cmp.nro_cuenta_bancaria.disable();
                  me.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
                  me.Cmp.id_proveedor_cta_bancaria.setValue('');
                  me.Cmp.id_proveedor_cta_bancaria.disable();

                  // me.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
                  // me.Cmp.id_proveedor_cta_bancaria.setValue('');
                  // me.Cmp.id_proveedor_cta_bancaria.disable();
              }*/
            //(may)para las FORMAS DE PAGO
            //record recupera los campos del maestro
            var record = Phx.CP.getPagina(this.idContenedor).getSelectedData();

            if(this.Cmp.forma_pago.getValue() == 'transferencia_propia'){
                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = 'conLbr';
                me.Cmp.id_proveedor_cta_bancaria.enable();
                me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                me.Cmp.id_proveedor_cta_bancaria.setValue('');


                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 2374; //proveedor boa
                me.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
                me.Cmp.id_proveedor_cta_bancaria.modificado = true;

            }else if(this.Cmp.forma_pago.getValue() == 'transferencia'){
                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
                me.Cmp.id_proveedor_cta_bancaria.enable();
                me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                me.Cmp.id_proveedor_cta_bancaria.setValue('');
                //me.Cmp.id_proveedor_cta_bancaria.reset();

                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = record.id_proveedor; //proveedores del plan de pago
                me.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
                me.Cmp.id_proveedor_cta_bancaria.modificado = true;

            }else if(this.Cmp.forma_pago.getValue() == 'transferencia_ext'){
                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
                me.Cmp.id_proveedor_cta_bancaria.enable();
                me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                me.Cmp.id_proveedor_cta_bancaria.setValue('');


                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 27; //proveedor banco central
                me.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
                me.Cmp.id_proveedor_cta_bancaria.modificado = true;

            }else if(this.Cmp.forma_pago.getValue() == 'transferencia_b_u'){
                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
                me.Cmp.id_proveedor_cta_bancaria.enable();
                me.Cmp.id_proveedor_cta_bancaria.allowBlank = false;
                me.Cmp.id_proveedor_cta_bancaria.setValue('');


                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = 28; //proveedor banco union
                me.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
                me.Cmp.id_proveedor_cta_bancaria.modificado = true;

            }else{
                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.lbrTP = '';
                me.Cmp.forma_pago.enable();
                me.Cmp.forma_pago.setValue('cheque');

                me.Cmp.id_proveedor_cta_bancaria.disable();
                me.Cmp.id_proveedor_cta_bancaria.allowBlank = true;
                me.Cmp.id_proveedor_cta_bancaria.setValue('');

                me.Cmp.id_proveedor_cta_bancaria.store.baseParams.id_proveedor = record.id_proveedor;
                me.Cmp.id_proveedor_cta_bancaria.tdata.id_padre = this.idContenedor;
                me.Cmp.id_proveedor_cta_bancaria.modificado = true;
            }
        },


        /*
         * DATE    09/07/2014
         * Author  RAC
         * DESC    Prepara los componentes segun el tipo de pago
         */


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
                me.ocultarGrupo(2); //ocultar el grupo de ajustes
                me.ocultarGrupo(3); //ocultar el grupo de periodo del costo

                me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);
            },

            'pagado': function (me) {
                //me.Cmp.id_plantilla.disable();
                me.habilitarDescuentos(me);
                me.mostrarComponentesPago(me);
                me.mostrarComponente(me.Cmp.liquido_pagable);
                me.ocultarComponente(me.Cmp.monto_anticipo);
                me.Cmp.monto_retgar_mo.setReadOnly(true);
                me.ocultarGrupo(2); //ocultar el grupo de ajustes
                me.ocultarGrupo(3); //ocultar el grupo de periodo del costo
                me.ocultarGrupo(5); //ocultar el grupo de  Multas

                me.ocultarComponente(me.Cmp.id_proveedor_cta_bancaria);

                me.mostrarComponente(me.Cmp.id_multa);

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


        calcularAnticipo: function () {

            var fecha_ini = this.Cmp.fecha_costo_ini.getValue(),
                fecha_fin = this.Cmp.fecha_costo_fin.getValue();

            if (fecha_ini && fecha_fin && (fecha_ini < fecha_fin)) {
                var monto = this.Cmp.monto.getValue(),
                    dias_total = this.restaFechas(fecha_ini, fecha_fin).dias + 1,
                    costo_dia = monto / dias_total,
                    fecha_fin_mes = new Date(fecha_ini.getFullYear(), fecha_ini.getMonth() + 1, 0),
                    dias_mes = this.restaFechas(fecha_ini, fecha_fin_mes).dias + 1,
                    dias_restantes = dias_total - dias_mes,
                    monto_anticipo = dias_restantes * costo_dia;
                this.Cmp.monto_anticipo.setValue(monto_anticipo.toFixed(2));
            }
        },

        restaFechas: function (f1, f2) {
            var segundos = (f2 - f1) / 1000,
                minutos = segundos / 60,
                horas = minutos / 60,
                horas = Math.round(horas),
                dias = horas / 24,
                dias = Math.round(dias);


            return {'dias': dias, 'horas': horas, 'minutos': minutos, 'segundos': segundos};
        },

        onButtonNew: function () {
            this.accionFormulario = 'NEW';
            Phx.vista.PlanPagoPOC.superclass.onButtonNew.call(this);
            this.ocultarGrupo(2); //ocultar el grupo de ajustes
            this.ocultarGrupo(3); //ocultar el grupo de ajustes

            this.ocultarComponente(this.Cmp.id_proveedor_cta_bancaria);

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
        onBtnDevPag: function () {
            var data = this.getSelectedData();

            this.wDEPTO.show();
            this.cmpDeptoConta.reset();
            //Phx.CP.setValueCombo(this.cmpDeptoConta, data.id_depto_conta_pp, data.desc_depto_conta_pp );
            this.cmpDeptoConta.store.baseParams = Ext.apply(this.cmpDeptoConta.store.baseParams, {id_depto_origen: data.id_depto_lb})
            this.cmpDeptoConta.modificado = true;


        },


        onOpenObs: function () {
            var rec = this.sm.getSelected();

            var data = {
                id_proceso_wf: rec.data.id_proceso_wf,
                id_estado_wf: rec.data.id_estado_wf,
                num_tramite: rec.data.num_tramite
            }

            Phx.CP.loadWindows('../../../sis_workflow/vista/obs/Obs.php',
                'Observaciones del WF',
                {
                    width: '80%',
                    height: '70%'
                },
                data,
                this.idContenedor,
                'Obs'
            )
        }
        ,
        // cargarCuenta : function (nro_cuenta_bancaria,nro_cuenta_bancaria ) {
        //     this.Cmp.nro_cuenta_bancaria.setValue(nro_cuenta_bancaria);
        //     this.Cmp.nro_cuenta_bancaria.setRawValue(nro_cuenta_bancaria.toUpperCase());
        // }

        cargarCuenta: function (id_proveedor_cta_bancaria, nro_cuenta) {
            this.Cmp.id_proveedor_cta_bancaria.setValue(id_proveedor_cta_bancaria);
            this.Cmp.id_proveedor_cta_bancaria.setRawValue(nro_cuenta);
        }



    })
</script>
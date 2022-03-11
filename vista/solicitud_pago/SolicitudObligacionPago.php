<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolicitudObligacionPago = Ext.extend(Phx.gridInterfaz, {
        fheight: '90%',
        fwidth: '70%',

        nombreVista: 'obligacionPago',
        constructor: function (config) {


            this.maestro = config;
            //llama al constructor de la clase padre
            Phx.vista.SolicitudObligacionPago.superclass.constructor.call(this, config);
            this.init();

            this.store.baseParams = {
                tipo_interfaz: this.nombreVista,
                id_obligacion_pago: this.maestro.id_obligacion_pago
            }

            if (config.filtro_directo) {
                this.store.baseParams.filtro_valor = config.filtro_directo.valor;
                this.store.baseParams.filtro_campo = config.filtro_directo.campo;
            }
            //carga de grilla
            if (this.nombreVista != 'ObligacionPagoVb') {
                this.load({params: {start: 0, limit: this.tam_pag}});
            }

            //crear venta de ajuste para pagos variable
            this.crearFormAjustes();
            this.iniciarEventos();
            this.addButton('ini_estado', {
                grupo: [0, 1, 2],
                argument: {estado: 'inicio'},
                text: 'Dev. al Solicitante',
                iconCls: 'batras',
                disabled: true,
                handler: this.antEstado,
                tooltip: '<b>Retorna la Obligación al estado Borrador</b>'
            });

            this.addButton('ant_estado', {
                grupo: [0, 1, 2],
                argument: {estado: 'anterior'},
                text: 'Retroceder',
                iconCls: 'batras',
                disabled: true,
                handler: this.antEstado,
                tooltip: '<b>Pasar al Anterior Estado</b>'
            });

            this.addButton('fin_registro', {
                grupo: [0, 1, 2],
                text: 'Siguiente',
                iconCls: 'badelante',
                disabled: true,
                handler: this.fin_registro,
                tooltip: '<b>Siguiente</b><p>Pasa al siguiente estado, si esta en borrador comprometerá presupuesto</p>'
            });
            this.addButton('reporte_com_ejec_pag', {
                grupo: [0, 1, 2],
                text: 'Rep.',
                iconCls: 'bpdf32',
                disabled: true,
                handler: this.repComEjePag,
                tooltip: '<b>Reporte</b><p>Reporte Obligación de Pago</p>'
            });
            this.addButton('reporte_plan_pago', {
                grupo: [0, 1, 2],
                text: 'Planes de Pago',
                iconCls: 'bpdf32',
                disabled: true,
                handler: this.repPlanPago,
                tooltip: '<b>Reporte Plan Pago</b><p>Reporte Planes de Pago</p>'
            });

            this.disableTabPagos();


            this.addButton('btnChequeoDocumentosWf',
                {
                    text: 'Documentos',
                    grupo: [0, 1, 2],
                    iconCls: 'bchecklist',
                    disabled: true,
                    handler: this.loadCheckDocumentosSolWf,
                    tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
                }
            );


            //RCM: Se agrega menú de reportes de adquisiciones
            this.addBotones();

            //RCM: reporte de verificacion presupeustaria
            this.addButton('btnVerifPresup', {
                text: 'Disponibilidad',
                grupo: [0, 1, 2],
                iconCls: 'bassign',
                disabled: true,
                handler: this.onBtnVerifPresup,
                tooltip: '<b>Verificación de la disponibilidad presupuestaria</b>'
            });

            //RCM: reporte de verificacion presupeustaria
            this.addButton('btnCheckPresupeusto', {
                text: 'Rev. Pres.',
                grupo: [0, 1, 2],
                iconCls: 'bassign',
                disabled: false,
                handler: this.onBtnCheckPresup,
                tooltip: '<b>Revertir  presupuestos,  permite ver la evolucón presupuestaria y revertir parcialmente</b>'
            });

            //this.addButton('diagrama_gantt',{grupo:[0,1,2],text:'Gant', iconCls: 'bgantt', disabled:true, handler:diagramGantt,tooltip: '<b>Diagrama Gantt de proceso macro</b>'});

            this.addBotonesGantt();

            this.addButton('ajustes', {
                grupo: [0, 1, 2],
                text: 'Ajus.',
                iconCls: 'blist',
                disabled: true,
                handler: this.showAjustes,
                tooltip: '<b>Ajustes a los anticipos totales para pagos variables</b>'
            });
            this.addButton('est_anticipo', {
                grupo: [0, 1, 2],
                text: 'Ampli.',
                iconCls: 'blist',
                disabled: true,
                handler: this.showAnticipo,
                tooltip: '<b>Define el monto de ampliación útil cuando necesitamos hacer pagos anticipados para la siguiente gestión</b>'
            });
            //this.addButton('extenderop',{grupo:[0,1,2],text:'Ext.', iconCls: 'blist', disabled: true, handler: this.extenederOp,tooltip: '<b>Extender la obligación de pago para la siguiente gestión</b>'});
            this.addButton('btnExtender',
                {
                    iconCls: 'brenew',
                    xtype: 'splitbutton',
                    grupo: [0, 4],
                    tooltip: '<b>Ext. Pagos</b><br>Nos permite elegir extender un pago de una gestión anterior.',
                    text: 'Ext. Pagos',
                    /*argument: {
                        'news': true,
                        def: 'reset'
                    },*/
                    scope: this,
                    menu: [ {
                                text: 'Pago Gestiones Anteriores (PGAE)',
                                //iconCls: 'btemplate',
                                /*argument: {
                                    'news': true,
                                    def: 'pdf'
                                },*/
                                tooltip: '<b>Permite Extender un Pago Gestión Anterior</b>',
                                handler: this.extenderPGAE,
                                scope: this
                            }]
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

            this.addButton('clonarOP', {
                text: 'Clonar Obligacion de Pago',
                iconCls: 'blist',
                disabled: true,
                handler: this.clonarOP,
                tooltip: 'Clonar el registro de una Obligacion'
            });

            this.construyeVariablesContratos();

        },
        tam_pag: 50,

        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_obligacion_pago'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    name: 'num_tramite',
                    fieldLabel: 'Núm. Trámite',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 200,
                    renderer: function (value, p, record) {
                        if (record.data.monto_estimado_sg > 0 && !record.data.id_obligacion_pago_extendida) {
                            return String.format('<div ext:qtip="La extención de la obligación esta pendiente"><b><font color="red">{0}</font></b><br><b>Monto ampliado: </b>{1}</div>', value, record.data.monto_estimado_sg);
                        }
                        else {
                            if (record.data.monto_estimado_sg > 0 && record.data.id_obligacion_pago_extendida > 0) {
                                return String.format('<div ext:qtip="La obligación fue extendida"><b><font color="orange">{0}</font></b><br><b>Monto ampliado: </b>{1}</div>', value, record.data.monto_estimado_sg);
                            }
                            else {
                                if (record.data.id_obligacion_pago_extendida > 0) {
                                    return String.format('<div ext:qtip="La obligación fue extendida"><b><font color="orange">{0}</font></b></div>', value, record.data.monto_estimado_sg);
                                }
                                else {

                                }
                                return String.format('{0}', value);
                            }
                        }
                    }
                },
                type: 'TextField',
                filters: {pfiltro: 'obpg.num_tramite', type: 'string'},
                id_grupo: 1,
                bottom_filter: true,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'estado',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {pfiltro: 'obpg.estado', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'numero',
                    fieldLabel: 'Número',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 180,
                    renderer: function (value, p, record) {
                        if (record.data.comprometido == 'si') {
                            return String.format('<b><font color="green">{0}</font></b>', value);
                        }
                        else {
                            return String.format('{0}', value);
                        }
                    },
                    maxLength: 50
                },
                type: 'Field',
                filters: {pfiltro: 'obpg.numero', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'ultima_cuota_pp',
                    fieldLabel: 'Ult. PP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 50
                },
                type: 'Field',
                filters: {pfiltro: 'obpg.ultima_cuota_pp', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'ultimo_estado_pp',
                    fieldLabel: 'Ult. Est. PP',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 50
                },
                type: 'Field',
                filters: {pfiltro: 'obpg.ultimo_estado_pp', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'tipo_obligacion',
                    fieldLabel: 'Tipo Obligación',
                    allowBlank: false,
                    anchor: '80%',
                    emptyText: 'Tipo Obligacion',
                    renderer: function (value, p, record) {
                        var dato = '';
                        dato = (dato == '' && value == 'sp') ? 'Pago Directo' : dato;
                        dato = (dato == '' && value == 'aduisiciones') ? 'Adquisiciones' : dato;
                        return String.format('{0}', dato);
                    },

                    store: new Ext.data.ArrayStore({
                        fields: ['variable', 'valor'],
                        data: [
                            ['sp', 'Pago Directo SP']
                        ]
                    }),
                    valueField: 'variable',
                    displayField: 'valor',
                    forceSelection: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    wisth: 250
                },
                type: 'ComboBox',
                filters: {pfiltro: 'obpg.tipo_obligacion', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true
            },


            {
                config: {
                    name: 'fecha',
                    //minValue:(Phx.CP.config_ini.sis_integracion=='ENDESIS')?new Date('1/1/2014'):undefined,
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    readOnly: false,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'obpg.fecha', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_funcionario',
                    hiddenName: 'id_funcionario',
                    origen: 'FUNCIONARIOCAR',
                    fieldLabel: 'Funcionario',
                    allowBlank: false,
                    gwidth: 200,
                    valueField: 'id_funcionario',
                    gdisplayField: 'desc_funcionario1',
                    baseParams: {es_combo_solicitud: 'si'},
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_funcionario1']);
                    }
                },
                type: 'ComboRec',//ComboRec
                id_grupo: 1,
                filters: {pfiltro: 'fun.desc_funcionario1', type: 'string'},
                bottom_filter: true,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_depto',
                    fieldLabel: 'Depto',
                    allowBlank: false,
                    anchor: '80%',
                    origen: 'DEPTO',
                    tinit: false,
                    baseParams: {tipo_filtro: 'DEPTO_UO', estado: 'activo', codigo_subsistema: 'TES', modulo: 'OP'},//parametros adicionales que se le pasan al store
                    gdisplayField: 'nombre_depto',
                    gwidth: 100
                },
                type: 'ComboRec',
                filters: {pfiltro: 'dep.nombre', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_proveedor',
                    hiddenName: 'id_proveedor',
                    fieldLabel: 'Proveedor',
                    // typeAhead: false,
                    forceSelection: true,
                    allowBlank: false,
                    // disabled: true,
                    emptyText: 'Proveedor...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
                        id: 'id_proveedor',
                        root: 'datos',
                        sortInfo: {
                            field: 'rotulo_comercial',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_proveedor', 'desc_proveedor', 'codigo', 'nit', 'rotulo_comercial', 'lugar', 'email'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: Ext.apply({par_filtro: 'desc_proveedor#codigo#nit#rotulo_comercial'})

                    }),
                    valueField: 'id_proveedor',
                    displayField: 'desc_proveedor',
                    // gdisplayField: 'desc_contrato',
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
                        if (record.data['desc_proveedor']) {
                            return String.format('{0}', record.data['desc_proveedor']);
                        }
                        return '';

                    },
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{rotulo_comercial}</b></p><p>{desc_proveedor}</p><p>{codigo}</p><p>NIT:{nit}</p><p>Lugar:{lugar}</p><p>Email: {email}</p></div></tpl>',
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'pv.desc_proveedor',
                    type: 'string'
                },
                bottom_filter: true,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'id_contrato',
                    hiddenName: 'id_contrato',
                    fieldLabel: 'Contrato',
                    typeAhead: false,
                    forceSelection: false,
                    allowBlank: false,
                    disabled: true,
                    emptyText: 'Contratos...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_workflow/control/Tabla/listarTablaCombo',
                        id: 'id_contrato',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_contrato',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_contrato','nro_tramite', 'numero', 'tipo', 'objeto', 'estado', 'desc_proveedor', 'monto', 'moneda', 'fecha_inicio', 'fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'con.nro_tramite#con.numero#con.tipo#con.monto#prov.desc_proveedor#con.objeto#con.monto',
                            tipo_proceso: "CON",
                            tipo_estado: "finalizado"
                        }
                    }),
                    valueField: 'id_contrato',
                    displayField: 'numero',
                    gdisplayField: 'desc_contrato',
                    triggerAction: 'all',
                    lazyRender: true,
                    resizable: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 200,
                    listWidth: 380,
                    minChars: 2,
                    gwidth: 100,
                    anchor: '80%',
                    renderer: function (value, p, record) {
                        if (record.data['desc_contrato']) {
                            return String.format('{0}', record.data['desc_contrato']);
                        }
                        return '';

                    },
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Nro: {numero} ({tipo})</b></p><p>Obj: <strong>{objeto}</strong></p><p>Prov : {desc_proveedor}</p> <p>Nro.Trámite: {nro_tramite}</p><p>Monto: {monto} {moneda}</p><p>Rango: {fecha_inicio} al {fecha_fin}</p></div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'con.numero',
                    type: 'numeric'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'total_pago',
                    currencyChar: ' ',
                    fieldLabel: 'Total a Pagar',
                    allowBlank: false,
                    gwidth: 130,
                    maxLength: 1245184
                },
                type: 'MoneyField',
                filters: {pfiltro: 'obdet.monto_pago_mo', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_moneda',
                    hiddenName: 'id_moneda',
                    fieldLabel: 'Moneda',
                    // typeAhead: false,
                    forceSelection: true,
                    allowBlank: false,
                    // disabled: true,
                    emptyText: 'Moneda...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Moneda/listarMoneda',
                        id: 'id_moneda',
                        root: 'datos',
                        sortInfo: {
                            field: 'moneda',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_moneda', 'moneda', 'codigo', 'tipo_moneda', 'codigo_internacional'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: Ext.apply({par_filtro: 'moneda#codigo', filtrar: 'si'})

                    }),
                    valueField: 'id_moneda',
                    displayField: 'moneda',
                    // gdisplayField: 'desc_contrato',
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
                        if (record.data['moneda']) {
                            return String.format('{0}', record.data['moneda']);
                        }
                        return '';

                    },
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Moneda:{moneda}</b></p><p>Codigo:{codigo}</p> <p>Codigo Internacional:{codigo_internacional}</p></div></tpl>',
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'mn.moneda',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'pago_variable',
                    fieldLabel: 'Pago Variable',
                    gwidth: 100,
                    maxLength: 30,
                    items: [
                        {
                            boxLabel: 'Si',
                            name: 'pg-var',
                            inputValue: 'si',
                            qtip: 'Los pagos variables se utilizan cuando NO se conocen los montos exactos que serán pagados (devengados o pagos presupuestariamente).<br> En el caso de anticipos se utiliza pagos variable cuando no sabemos si el total anticipado va ser el total gastado.<br> Ejemplo combustibles, si anticipamos 7000 $us no conocemos con exactitud si vamos a consumir este total puede sobrar o faltar'
                        },
                        {
                            boxLabel: 'No',
                            name: 'pg-var',
                            inputValue: 'no',
                            checked: true,
                            qtip: 'Los pagos no variable (fijos) se utilizan cuando se conocen los montos exactos que se pagaran.<br> Ejemplo los sueldos de los consultores de línea. Por lo general esta es la opcion mas utiliza (además permite que el sistema le ayude con el cálculo del prorrateo lo que no se puede hacer automáticamente cuando el pago es variable) '
                        }
                    ]
                },
                type: 'RadioGroupField',
                filters: {pfiltro: 'pago_variable', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'tipo_cambio_conv',
                    fieldLabel: 'Tipo Cambio',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 131074,
                    decimalPrecision: 10
                },
                type: 'NumberField',
                filters: {pfiltro: 'obpg.tipo_cambio_conv', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'funcionario_proveedor',
                    fieldLabel: 'Funcionario/<br/>Proveedor',
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 30,
                    items: [
                        {boxLabel: 'Funcionario', name: 'rg-auto', inputValue: 'funcionario', checked: true},
                        {boxLabel: 'Proveedor', name: 'rg-auto', inputValue: 'proveedor'}
                    ]
                },
                type: 'RadioGroup',
                id_grupo: 1,
                grid: false,
                form: true
            },

            {
                config: {
                    name: 'tipo_anticipo',
                    fieldLabel: 'Tiene Anticipo Parcial',
                    allowBlank: false,
                    qtip: 'Se habilita en SI,  solo para el caso de anticipos parcial, estos anticipos se tendran que descontar de los pagos sucesivos (Se descuenta del liquido  pagable). Los anticipos parciales no van contra factura u otro similar. <br>Para el caso de anticipo totales  escoger la opcion NO',
                    anchor: '80%',
                    emptyText: 'Tipo Obligacion',
                    store: new Ext.data.ArrayStore({
                        fields: ['variable', 'valor'],
                        data: [['si', 'si'],
                            ['no', 'no']]
                    }),
                    valueField: 'variable',
                    value: 'no',
                    displayField: 'valor',
                    forceSelection: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    wisth: 250
                },
                type: 'ComboBox',
                valorInicial: 'no',
                filters: {pfiltro: 'obpg.tipo_anticipo', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: false
            },


            {
                config: {
                    name: 'obs',
                    fieldLabel: 'Justificación',
                    allowBlank: false,
                    qtip: 'Descripción del objetivo del pago, o Si el proveedor es PASAJEROS PERJUDICADOS aqui va el nombre del pasajero',
                    anchor: '80%',
                    gwidth: 250,
                    maxLength: 1000
                },
                type: 'TextArea',
                filters: {pfiltro: 'obpg.obs', type: 'string'},
                id_grupo: 1,
                bottom_filter: true,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'total_nro_cuota',
                    fieldLabel: 'Nro. Cuotas',
                    allowBlank: false,
                    allowDecimals: false,
                    anchor: '80%',
                    gwidth: 50,
                    value: 0,
                    mimValue: 0,
                    maxLength: 131074,
                    maxValue: 24
                },
                type: 'NumberField',
                valorInicial: 0,
                filters: {pfiltro: 'obpg.total_nro_cuota', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_plantilla',
                    fieldLabel: 'Tipo Documento',
                    allowBlank: false,
                    emptyText: 'Elija una plantilla...',
                    store: new Ext.data.JsonStore(
                        {
                            url: '../../sis_parametros/control/Plantilla/listarPlantilla',
                            id: 'id_plantilla',
                            root: 'datos',
                            sortInfo: {
                                field: 'desc_plantilla',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_plantilla', 'nro_linea', 'desc_plantilla', 'tipo', 'sw_tesoro', 'sw_compro'],
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
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'fecha_pp_ini',
                    fieldLabel: 'Fecha Pago',
                    allowBlank: false,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'obpg.fecha_pp_ini', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'rotacion',
                    fieldLabel: 'Rotación (Meses)',
                    allowBlank: false,
                    allowDecimals: false,
                    anchor: '80%',
                    gwidth: 50,
                    value: 0,
                    maxLength: 131074,
                    mimValue: 1,
                    maxValue: 100
                },
                type: 'NumberField',
                filters: {pfiltro: 'obpg.rotacion', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'porc_anticipo',
                    fieldLabel: 'Porc. Anticipo',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 131074,
                    maxValue: 100
                },
                type: 'NumberField',
                filters: {pfiltro: 'obpg.porc_anticipo', type: 'numeric'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    name: 'porc_retgar',
                    fieldLabel: '%. Retgar',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 131074,
                    maxValue: 100
                },
                type: 'NumberField',
                filters: {pfiltro: 'obpg.porc_retgar', type: 'numeric'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    fieldLabel: 'Obs. Presupuestos',
                    gwidth: 180,
                    name: 'obs_presupuestos'
                },
                type: 'Field',
                filters: {pfiltro: 'obpg.obs_presupuestos', type: 'string'},
                grid: true,
                form: false
            },
            {
                config: {
                    fieldLabel: 'Estado Reg.',
                    name: 'estado_reg'
                },
                type: 'Field',
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
                filters: {pfiltro: 'obpg.fecha_reg', type: 'date'},
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
                filters: {pfiltro: 'obpg.fecha_mod', type: 'date'},
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
            }
        ],

        title: 'Obligaciones de Pago',

        ActSave: '../../sis_tesoreria/control/ObligacionPago/insertarObligacionPago',
        ActDel: '../../sis_tesoreria/control/ObligacionPago/eliminarObligacionPago',
        ActList: '../../sis_tesoreria/control/ObligacionPago/listarObligacionPago',
        id_store: 'id_obligacion_pago',
        fields: [
            {name: 'id_obligacion_pago', type: 'numeric'},
            {name: 'id_proveedor', type: 'numeric'},
            {name: 'desc_proveedor', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'tipo_obligacion', type: 'string'},
            {name: 'id_moneda', type: 'numeric'},
            {name: 'moneda', type: 'string'},
            {name: 'obs', type: 'string'},
            {name: 'porc_retgar', type: 'numeric'},
            {name: 'id_subsistema', type: 'numeric'},
            {name: 'nombre_subsistema', type: 'string'},
            {name: 'id_funcionario', type: 'numeric'},
            {name: 'desc_funcionario'},
            {name: 'estado_reg', type: 'string'},
            {name: 'porc_anticipo', type: 'numeric'},
            {name: 'id_estado_wf', type: 'numeric'},
            {name: 'id_depto', type: 'numeric'},
            {name: 'nombre_depto', type: 'string'},
            {name: 'uo_ex', type: 'string'},
            {name: 'num_tramite', type: 'string'},
            {name: 'id_proceso_wf', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'tipo_cambio_conv', type: 'numeric'},
            {name: 'id_depto_conta', type: 'numeric'},
            'numero', 'pago_variable', 'total_pago',
            'id_gestion', 'comprometido', 'nro_cuota_vigente', 'tipo_moneda',
            'total_nro_cuota', 'id_plantilla', 'desc_plantilla',
            {name: 'fecha_pp_ini', type: 'date', dateFormat: 'Y-m-d'},
            'rotacion',
            'ultima_cuota_pp',
            'ultimo_estado_pp',
            'tipo_anticipo',
            'ajuste_anticipo', 'desc_funcionario1',
            'ajuste_aplicado', 'codigo_poa', 'obs_poa',
            'monto_estimado_sg', 'id_obligacion_pago_extendida', 'obs_presupuestos', 'id_contrato', 'desc_contrato',
            {name: 'id_conformidad', type: 'numeric'},
            {name: 'conformidad_final', type: 'string'},
            {name: 'fecha_conformidad_final', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'observaciones', type: 'string'}

        ],

        arrayDefaultColumHidden: ['id_fecha_reg', 'id_fecha_mod', 'fecha_mod', 'usr_reg', 'estado_reg', 'fecha_reg', 'usr_mod',
            'numero', 'tipo_obligacion', 'id_depto', 'id_contrato', 'tipo_cambio_conv', 'tipo_anticipo', 'obs', 'total_nro_cuota', 'id_plantilla', 'fecha_pp_ini',
            'rotacion', 'porc_anticipo', 'obs_presupuestos'],


        rowExpander: new Ext.ux.grid.RowExpander({
            tpl: new Ext.Template('<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obligación de pago:&nbsp;&nbsp;</b> {numero}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Contrato:&nbsp;&nbsp;</b> {desc_contrato}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Depto:&nbsp;&nbsp;</b> {nombre_depto}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Justificación:&nbsp;&nbsp;</b> {obs}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obs del área de presupeustos:&nbsp;&nbsp;</b> {obs_presupuestos}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obs del área de POA:&nbsp;&nbsp;</b> {codigo_poa} - {obs_poa}</p><br>'
            )
        }),

        sortInfo: {
            field: 'obpg.fecha_reg',
            direction: 'DESC'
        },

        repComEjePag: function () {
            var rec = this.sm.getSelected();
            if (rec) {
                Phx.CP.loadWindows('../../../sis_tesoreria/vista/obligacion_pago/ReporteComEjePag.php',
                    'Reporte de Obligacion',
                    {
                        width: 400,
                        height: 200
                    },
                    rec.data, this.idContenedor, 'ReporteComEjePag')
            }
        },

        repPlanPago: function () {
            var rec = this.sm.getSelected();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/ObligacionPago/reportePlanesPago',
                params: {'id_obligacion_pago': rec.data.id_obligacion_pago},
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


        iniciarEventos: function () {

            this.cmpProveedor = this.getComponente('id_proveedor');
            this.cmpFuncionario = this.getComponente('id_funcionario');
            this.cmpFuncionarioProveedor = this.getComponente('funcionario_proveedor');
            this.cmpFecha = this.getComponente('fecha');
            this.cmpTipoObligacion = this.getComponente('tipo_obligacion');
            this.cmpMoneda = this.getComponente('id_moneda');
            this.cmpDepto = this.getComponente('id_depto');
            this.cmpTipoCambioConv = this.getComponente('tipo_cambio_conv');

            // this.cmpPorcAnticipo=this.getComponente('porc_anticipo');
            // this.cmpPorcRetgar=this.getComponente('porc_retgar');

            this.ocultarComponente(this.cmpProveedor);
            this.ocultarComponente(this.cmpFuncionario);
            this.ocultarComponente(this.cmpFuncionarioProveedor);

            this.cmpMoneda.on('select', function (com, dat) {

                if (dat.data.tipo_moneda == 'base') {
                    this.cmpTipoCambioConv.disable();
                    this.cmpTipoCambioConv.setValue(1);

                }
                else {
                    this.cmpTipoCambioConv.enable();
                    this.obtenerTipoCambio();
                }


            }, this);


            this.Cmp.id_proveedor.on('select', function (cmb, rec, ind) {
                //console.log ('nombreVista', this.nombreVista)
                //(may) modificacion campo contrato segun el tipo de obligacion
                if (this.nombreVista == 'solicitudObligacionPagoUnico' || this.nombreVista == 'solicitudSinImputacionPresupuestaria'){
                    this.ocultarComponente(this.Cmp.id_contrato);
                    //this.Cmp.id_contrato.disable();
                    this.Cmp.id_contrato.reset();
                    this.Cmp.id_contrato.modificado = true;
                }else{
                    this.Cmp.id_contrato.enable();
                    this.Cmp.id_contrato.reset();
                    this.Cmp.id_contrato.store.baseParams.filter = "[{\"type\":\"numeric\",\"comparison\":\"eq\", \"value\":\"" + cmb.getValue() + "\",\"field\":\"CON.id_proveedor\"}]";


                    this.Cmp.id_contrato.modificado = true;
                }



            }, this);

            this.cmpTipoObligacion.on('select', function (c, rec, ind) {

                n = rec.data.variable;

                // if (n == 'adquisiciones' || n == 'pago_directo') {
                if (n == 'adquisiciones' || n == 'sp') {
                    this.cmpProveedor.enable();
                    this.mostrarComponente(this.cmpProveedor);
                    this.ocultarComponente(this.cmpFuncionario);
                    this.ocultarComponente(this.cmpFuncionarioProveedor);
                    this.cmpFuncionario.reset();
                } else {
                    if (n == 'viatico' || n == 'fondo_en_avance') {
                        this.cmpFuncionario.enable();
                        this.mostrarComponente(this.cmpFuncionario);
                        this.ocultarComponente(this.cmpProveedor);
                        this.ocultarComponente(this.cmpFuncionarioProveedor);
                        this.cmpProveedor.reset();
                    } else {
                        this.cmpFuncionarioProveedor.reset();
                        this.cmpFuncionarioProveedor.enable();
                        this.mostrarComponente(this.cmpFuncionarioProveedor);
                        this.mostrarComponente(this.cmpFuncionario);
                        this.ocultarComponente(this.cmpProveedor);

                        this.cmpFuncionarioProveedor.on('change', function (groupRadio, radio) {
                            this.enableDisable(radio.inputValue);
                        }, this);
                    }
                }
            }, this);


            //validaciones para registro de plan de pagos por defecto
            //this.Cmp.total_nro_cuota.setValue(0);

            this.ocultarComponente(this.Cmp.id_plantilla);
            this.ocultarComponente(this.Cmp.fecha_pp_ini);
            this.ocultarComponente(this.Cmp.rotacion);

            this.Cmp.total_nro_cuota.on('change', function (cmp, newValue, oldValue) {

                if (newValue > 0) {
                    this.mostrarComponente(this.Cmp.id_plantilla);
                    this.mostrarComponente(this.Cmp.fecha_pp_ini);
                    this.mostrarComponente(this.Cmp.rotacion);
                }
                else {
                    this.ocultarComponente(this.Cmp.id_plantilla);
                    this.ocultarComponente(this.Cmp.fecha_pp_ini);
                    this.ocultarComponente(this.Cmp.rotacion);

                }

            }, this);


        },


        obtenerTipoCambio: function () {

            var fecha = this.cmpFecha.getValue().dateFormat(this.cmpFecha.format);
            var id_moneda = this.cmpMoneda.getValue();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url: '../../sis_parametros/control/TipoCambio/obtenerTipoCambio',
                params: {fecha: fecha, id_moneda: id_moneda},
                success: this.successTC,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        successTC: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (!reg.ROOT.error) {

                this.cmpTipoCambioConv.setValue(reg.ROOT.datos.tipo_cambio);
            } else {

                alert('ocurrio al obtener el tipo de Cambio')
            }
        },

        enableDisable: function (val) {
            var cmbIt = this.getComponente('id_funcionario');
            var cmbServ = this.getComponente('id_proveedor');
            if (val == 'funcionario') {
                cmbServ.reset();
                cmbIt.reset();
                this.mostrarComponente(cmbIt);
                this.ocultarComponente(cmbServ);
            } else {
                cmbServ.reset();
                cmbIt.reset();
                this.mostrarComponente(cmbServ);
                this.ocultarComponente(cmbIt);
            }
        },

        fin_registro: function (a, b, forzar_fin, paneldoc) {
            var d = this.sm.getSelected().data;
            if (d.estado != 'en_pago') {
                this.mostrarWizard(this.sm.getSelected());
            }
            else {
                if (d.estado == 'en_pago') {
                    if (confirm('¿Está seguro de finalizar la obligacion?. \n Esta acción no  puede revertirse')) {


                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url: '../../sis_tesoreria/control/ObligacionPago/finalizarRegistro',
                            params: {
                                'id_obligacion_pago': d.id_obligacion_pago,
                                'operacion': 'fin_registro',
                                'forzar_fin': forzar_fin ? 'si' : 'no'
                            },
                            success: this.successSinc,
                            failure: this.conexionFailureFin,
                            timeout: this.timeout,
                            scope: this
                        });
                    }
                }
            }
        },
        successSinc: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (!reg.ROOT.error) {
                if (this.wEstado) {
                    this.wEstado.hide();
                }

                if (resp.argument && resp.argument.paneldoc) {
                    resp.argument.paneldoc.panel.destroy();
                }
                this.reload();
            } else {
                alert('ocurrio un error durante el proceso')
            }
        },

        conexionFailureFin: function (r1, r2) {
            Phx.CP.loadingHide();

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(r1.responseText));
            if (reg.ROOT.datos.preguntar == "si") {

                alert(reg.ROOT.detalle.mensaje)
                if (confirm('Si va extender esta obligacón para las gestión siguiente puede forzar. ¿Desea forzar la finalización?. ')) {
                    this.fin_registro(undefined, undefined, true);
                }
            }
            else {
                this.conexionFailure(r1, r2);
            }

        },
        antEstado: function (res, eve) {
            var d = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            var operacion = 'cambiar';
            operacion = res.argument.estado == 'inicio' ? 'inicio' : operacion;

            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/ObligacionPago/anteriorEstadoObligacion',
                params: {
                    id_obligacion_pago: d.id_obligacion_pago,
                    operacion: operacion
                },
                success: this.successSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },

        enableTabPagos: function () {
            if (this.TabPanelSouth.get(1)) {
                this.TabPanelSouth.get(1).enable();
                this.TabPanelSouth.setActiveTab(1)
            }
        },

        disableTabPagos: function () {
            if (this.TabPanelSouth.get(1)) {
                this.TabPanelSouth.get(1).disable();
                this.TabPanelSouth.setActiveTab(0)

            }
        },
        preparaMenu: function (n) {
            var data = this.getSelectedData();
            var tb = this.tbar;


            Phx.vista.SolicitudObligacionPago.superclass.preparaMenu.call(this, n);
            this.getBoton('clonarOP').enable();
            if (data['estado'] == 'borrador') {
                this.getBoton('edit').enable();
                if (this.getBoton('new'))
                    this.getBoton('new').enable();
                if (this.getBoton('del'))
                    this.getBoton('del').enable();
                this.getBoton('fin_registro').enable();
                this.getBoton('ant_estado').disable();

                this.getBoton('reporte_com_ejec_pag').disable();
                this.getBoton('reporte_plan_pago').disable();
                this.getBoton('ajustes').disable();

                this.disableTabPagos();
                // this.enableTabPagos();


            }
            else {


                if (data['estado'] == 'registrado') {
                    this.getBoton('ant_estado').enable();
                    // this.getBoton('fin_registro').disable();
                    this.getBoton('fin_registro').enable();
                    this.enableTabPagos();
                    this.getBoton('est_anticipo').enable();
                    this.getBoton('btnExtender').enable();
                }

                if (data['estado'] == 'en_pago') {
                    this.enableTabPagos();
                    this.getBoton('ant_estado').enable();
                    this.getBoton('fin_registro').enable();
                    this.getBoton('est_anticipo').enable();
                    this.getBoton('btnExtender').enable();
                }

                if (data['estado'] == 'anulado') {
                    this.getBoton('fin_registro').disable();
                    this.disableTabPagos();
                    this.getBoton('ant_estado').disable();
                    this.getBoton('est_anticipo').disable();
                }
                if (data['estado'] == 'finalizado') {
                    this.getBoton('ant_estado').disable();
                    this.getBoton('fin_registro').disable();
                    this.getBoton('est_anticipo').disable();
                    if (data['id_obligacion_pago_extendida'] == '' || !data['id_obligacion_pago_extendida']) {
                        this.getBoton('btnExtender').enable();
                    }
                    else {
                        this.getBoton('btnExtender').disable();
                    }

                }
                /*
                               else{
                                    this.getBoton('extenderop').disable();
                               }*/

                // if (data['estado'] == 'vbpresupuestos' || data['estado'] == 'vbpoa' || data['estado'] == 'suppresu') {
                if (data['estado'] == 'borrador') {
                    this.disableTabPagos();
                    // this.enableTabPagos();
                    //if (this.nombreVista == 'ObligacionPagoVb' || this.nombreVista == 'ObligacionPagoVbPoa') {
                    if (this.nombreVista == 'SP'){
                        this.getBoton('fin_registro').enable();
                        this.getBoton('ant_estado').enable();
                    }
                    // else {
                    //     // this.getBoton('fin_registro').disable();
                    //     this.getBoton('fin_registro').enable();
                    //     this.getBoton('ant_estado').disable();
                    // }
                }

                // if (this.nombreVista == 'ObligacionPagoVb') {
                if (this.nombreVista == 'SP') {
                    this.getBoton('fin_registro').enable();
                    this.getBoton('ant_estado').enable();
                }

                if (data['pago_variable'] == 'si' && data['estado'] == 'en_pago') {
                    this.getBoton('ajustes').enable();
                }
                else {
                    this.getBoton('ajustes').disable();
                }

                if (data['pago_variable'] != 'finalizado' && data['estado'] != 'anulado') {
                    this.getBoton('btnCheckPresupeusto').enable();
                }


                if (this.getBoton('edit')) {
                    /*
                    if(data['tipo_obligacion'] == 'adquisiciones' && data.estado != 'finalizado'){
                        this.getBoton('edit').enable();
                    }
                    else{
                        this.getBoton('edit').disable();
                    }*/
                    if (data.estado != 'finalizado') {
                        this.getBoton('edit').enable();
                    }
                    else {
                        this.getBoton('edit').disable();
                    }

                }
                if (this.getBoton('del'))
                    this.getBoton('del').disable();
                this.getBoton('reporte_com_ejec_pag').enable();
                this.getBoton('reporte_plan_pago').enable();


            }


            if (data.tipo_obligacion == 'adquisiciones') {
                //RCM: menú de reportes de adquisiciones
                this.menuAdq.enable();
                //Inhabilita el reporte de disponibilidad
                this.getBoton('btnVerifPresup').disable();

            }
            else {
                //RCM: menú de reportes de adquisiciones
                this.menuAdq.disable();

                //Habilita el reporte de disponibilidad si está en estado borrador
                if (data['estado'] == 'borrador' || data['estado'] == 'vbpresupuestos') {
                    this.getBoton('btnVerifPresup').enable();
                } else {
                    //Inhabilita el reporte de disponibilidad
                    this.getBoton('btnVerifPresup').disable();
                }

            }
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            this.getBoton('btnObs').enable();
        },


        liberaMenu: function () {
            var tb = Phx.vista.SolicitudObligacionPago.superclass.liberaMenu.call(this);
            if (tb) {
                this.getBoton('fin_registro').disable();
                this.getBoton('ant_estado').disable();
                this.getBoton('reporte_com_ejec_pag').disable();
                this.getBoton('reporte_plan_pago').disable();
                this.getBoton('diagrama_gantt').disable();
                this.getBoton('btnChequeoDocumentosWf').disable();
                this.getBoton('ajustes').disable();
                this.getBoton('est_anticipo').disable();
                this.getBoton('btnExtender').disable();
                this.getBoton('btnCheckPresupeusto').disable();
                this.getBoton('btnObs').disable();
                this.getBoton('clonarOP').disable();
                //Inhabilita el reporte de disponibilidad
                this.getBoton('btnVerifPresup').disable();
            }


            this.disableTabPagos();


            //RCM: menú de reportes de adquisiciones
            this.menuAdq.disable();

            return tb
        },

        tabsouth: [

            {
                url: '../../../sis_tesoreria/vista/solicitud_obligacion_det/SolicitudObligacionDet.php',
                title: 'Detalle',
                height: '50%',
                cls: 'SolicitudObligacionDet'
            },
            // {
            //     url: '../../../sis_tesoreria/vista/obligacion_det/ObligacionDet.php',
            //     title: 'Detalle',
            //     height: '50%',
            //     cls: 'ObligacionDet'
            // },


            {
                url: '../../../sis_tesoreria/vista/solicitud_plan_pago/SoliPlanPagoReq.php',
                title: 'Plan de Pagos',
                height: '50%',
                cls: 'SoliPlanPagoReq'
            }


        ],


        bdel: true,
        bedit: true,
        bsave: false,

        loadCheckDocumentosSolWf: function () {
            var rec = this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Chequear documento del WF',
                {
                    width: '90%',
                    height: 500
                },
                rec.data,
                this.idContenedor,
                'DocumentoWf'
            )
        },

        showAnticipo: function () {
            this.windowAjustes.show();
            this.formAjustes.getForm().reset();
            var d = this.sm.getSelected().data;
            this.formAjustes.getForm().findField('monto_estimado_sg').show();
            this.formAjustes.getForm().findField('ajuste_anticipo').hide();
            this.formAjustes.getForm().findField('ajuste_aplicado').hide();
            this.formAjustes.getForm().findField('monto_estimado_sg').setValue(d.monto_estimado_sg);
            this.formAjustes.getForm().findField('tipo_ajuste').setValue('anticipo_sg');
        },

        showAjustes: function () {
            this.windowAjustes.show();
            this.formAjustes.getForm().reset();
            var d = this.sm.getSelected().data;
            this.formAjustes.getForm().findField('ajuste_anticipo').show();
            this.formAjustes.getForm().findField('ajuste_aplicado').show();
            this.formAjustes.getForm().findField('ajuste_anticipo').setValue(d.ajuste_anticipo);
            this.formAjustes.getForm().findField('ajuste_aplicado').setValue(d.ajuste_aplicado);
            this.formAjustes.getForm().findField('monto_estimado_sg').hide();
            this.formAjustes.getForm().findField('tipo_ajuste').setValue('ajuste');

        },

        crearFormAjustes: function () {
            var me = this;
            me.formAjustes = new Ext.form.FormPanel({
                id: me.idContenedor + '_AJUSTES',
                margins: ' 10 10 10 10',
                items: [
                    {
                        name: 'ajuste_anticipo',
                        xtype: 'numberfield',
                        fieldLabel: 'Ajuste anticipo',
                        allowDecimals: true,
                        value: 0,
                        allowNegative: false,
                        qtip: 'Si se debe al proveedor por aplicar mas de lo que anticipaste'

                    },
                    {
                        name: 'ajuste_aplicado',
                        xtype: 'numberfield',
                        fieldLabel: 'Ajuste aplicacón',
                        allowDecimals: true,
                        value: 0,
                        allowNegative: false,
                        qtip: 'Si el proveedor debe a la empesa por que anticipo mas de lo aplicado'

                    },
                    {
                        name: 'monto_estimado_sg',
                        xtype: 'numberfield',
                        fieldLabel: 'Ampliación',
                        allowDecimals: true,
                        value: 0,
                        allowNegative: false,
                        qtip: 'La ampliacion te permite sobrepasar el montar a pagar previsto inicialmente'

                    },
                    {
                        xtype: 'field',
                        name: 'tipo_ajuste',
                        labelSeparator: '',
                        inputType: 'hidden'
                    }],
                autoScroll: false,
                autoDestroy: true
            });

            // Definicion de la ventana que contiene al formulario
            me.windowAjustes = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Ajustes para anticipos totales',
                margins: ' 10 10 10 10',
                modal: true,
                width: 400,
                height: 200,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [{
                    text: 'Guardar',
                    arrowAlign: 'bottom',
                    handler: me.saveAjustes,
                    argument: {
                        'news': false
                    },
                    scope: me

                },
                    {
                        text: 'Declinar',
                        handler: me.onDeclinarAjustes,
                        scope: me
                    }],
                items: me.formAjustes,
                autoDestroy: true,
                closeAction: 'hide'
            });


        },
        saveAjustes: function () {
            var me = this,
                d = me.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/ObligacionPago/insertarAjustes',
                success: me.successAjustes,
                failure: me.failureAjustes,
                params: {
                    'id_obligacion_pago': d.id_obligacion_pago,
                    'ajuste_aplicado': me.formAjustes.getForm().findField('ajuste_aplicado').getValue(),
                    'ajuste_anticipo': me.formAjustes.getForm().findField('ajuste_anticipo').getValue(),
                    'monto_estimado_sg': me.formAjustes.getForm().findField('monto_estimado_sg').getValue(),
                    'tipo_ajuste': me.formAjustes.getForm().findField('tipo_ajuste').getValue()

                },
                timeout: me.timeout,
                scope: me
            });


        },
        successAjustes: function (resp) {
            Phx.CP.loadingHide();
            this.windowAjustes.hide();
            this.reload();

        },

        failureAjustes: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.PlanPago.superclass.conexionFailure.call(this, resp);

        },
        onDeclinarAjustes: function () {
            this.windowAjustes.hide();

        },
        extenderPGAE: function () {
            var me = this,
                d = me.sm.getSelected().data;

            console.log(d);
            if (!d.id_obligacion_pago_extendida && d.id_obligacion_pago_extendida != '') {
                if (confirm('¿Está seguro de extender la obligación para pago de Gestion Anterior?. \n Si  no existen   registros de presupuestos y partidas para la siguiente gestión , no se copiara nada, tendrá que hacer los registros faltantes manualmente. No podrá volver a ejecutar este comando')) {
                    if (confirm('¿Está realmente seguro?')) {
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url: '../../sis_tesoreria/control/ObligacionPago/extenderPGAE',
                            success: function () {
                                Phx.CP.loadingHide();
                                me.reload();
                            },
                            failure: me.conexionFailure,
                            params: {
                                'id_obligacion_pago': d.id_obligacion_pago,
                                'id_administrador': 3
                            },
                            timeout: me.timeout,
                            scope: me
                        });

                    }
                }
            }
            else {

                alert('La obligación ya fue extendida')
            }
        },

        extenederOp: function () {
            var me = this,
                d = me.sm.getSelected().data;

            if (!d.id_obligacion_pago_extendida && d.id_obligacion_pago_extendida != '') {
                if (confirm('¿Está seguro de extender la obligación para la gestión siguiente?. \n Si  no existen   registros de presupuestos y partidas para la siguiente gestión , no se copiara nada, tendrá que hacer los registros faltantes manualmente. No podrá volver a ejecutar este comando')) {
                    if (confirm('¿Está realmente seguro?')) {
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url: '../../sis_tesoreria/control/ObligacionPago/extenderOp',
                            success: function () {
                                Phx.CP.loadingHide();
                                me.reload();
                            },
                            failure: me.conexionFailure,
                            params: {
                                'id_obligacion_pago': d.id_obligacion_pago,
                                'id_administrador': 1

                            },
                            timeout: me.timeout,
                            scope: me
                        });

                    }
                }
            }
            else {

                alert('La obligación ya fue extendida')
            }

        },
        addBotones: function () {
            this.menuAdq = new Ext.Toolbar.SplitButton({
                id: 'btn-adq-' + this.idContenedor,
                text: 'Orden Compra',
                grupo: [0, 1, 2],
                handler: this.onBtnAdq,
                disabled: true,
                scope: this,
                iconCls: 'bcharge',
                menu: {
                    items: [{
                        id: 'btn-cot-' + this.idContenedor,
                        text: 'Cotización',
                        tooltip: '<b>Reporte de la Cotización</b>',
                        handler: this.onBtnCot,
                        scope: this
                    }, {
                        id: 'btn-proc-' + this.idContenedor,
                        text: 'Cuadro Comparativo',
                        tooltip: '<b>Reporte de Cuadro Comparativo</b>',
                        handler: this.onBtnProc,
                        scope: this
                    }, {
                        id: 'btn-sol-' + this.idContenedor,
                        text: 'Solicitud de Compra',
                        tooltip: '<b>Reporte de la Solicitud de Compra</b>',
                        handler: this.onBtnSol,
                        scope: this
                    }
                    ]
                }
            });

            //Adiciona el menú a la barra de herramientas
            this.tbar.add(this.menuAdq);
        },

        diagramGantt: function () {
            var data = this.sm.getSelected().data.id_proceso_wf;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params: {'id_proceso_wf': data},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },

        diagramGanttDinamico: function () {
            var data = this.sm.getSelected().data.id_proceso_wf;
            window.open('../../../sis_workflow/reportes/gantt/gantt_dinamico.html?id_proceso_wf=' + data)
        },

        addBotonesGantt: function () {
            this.menuAdqGantt = new Ext.Toolbar.SplitButton({
                id: 'b-diagrama_gantt-' + this.idContenedor,
                text: 'Gantt',
                disabled: true,
                grupo: [0, 1, 2],
                iconCls: 'bgantt',
                handler: this.diagramGanttDinamico,
                scope: this,
                menu: {
                    items: [{
                        id: 'b-gantti-' + this.idContenedor,
                        text: 'Gantt Imagen',
                        tooltip: '<b>Mues un reporte gantt en formato de imagen</b>',
                        handler: this.diagramGantt,
                        scope: this
                    }, {
                        id: 'b-ganttd-' + this.idContenedor,
                        text: 'Gantt Dinámico',
                        tooltip: '<b>Muestra el reporte gantt facil de entender</b>',
                        handler: this.diagramGanttDinamico,
                        scope: this
                    }
                    ]
                }
            });
            this.tbar.add(this.menuAdqGantt);
        },

        onBtnAdq: function () {
            Phx.CP.loadingShow();
            var rec = this.sm.getSelected();
            var data = rec.data;
            if (data) {
                //Obtiene los IDS
                this.auxFuncion = 'onBtnAdq';
                this.obtenerIDS(data);
            } else {
                alert('Seleccione un registro y vuelva a intentarlo');
            }
        },

        onBtnCot: function () {
            Phx.CP.loadingShow();
            var rec = this.sm.getSelected();
            var data = rec.data;
            if (data) {
                //Obtiene los IDS
                this.auxFuncion = 'onBtnCot';
                this.obtenerIDS(data);
            } else {
                alert('Seleccione un registro y vuelva a intentarlo');
            }
        },

        onBtnSol: function () {
            Phx.CP.loadingShow();
            var rec = this.sm.getSelected();
            var data = rec.data;
            if (data) {
                //Obtiene los IDS
                this.auxFuncion = 'onBtnSol';
                this.obtenerIDS(data);
            } else {
                alert('Seleccione un registro y vuelva a intentarlo');
            }
        },

        onBtnProc: function () {
            Phx.CP.loadingShow();
            var rec = this.sm.getSelected();
            var data = rec.data;
            if (data) {
                //Obtiene los IDS
                this.auxFuncion = 'onBtnProc';
                this.obtenerIDS(data);
            } else {
                alert('Seleccione un registro y vuelva a intentarlo');
            }
        },


        obtenerIDS: function (data) {
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/ObligacionPago/obtenerIdsExternos',
                params: {
                    id_obligacion_pago: data.id_obligacion_pago,
                    sistema: this.sistema
                },
                success: this.successobtenerIDS,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        successobtenerIDS: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (!reg.ROOT.error) {
                //Setea los valores a variables locales
                var ids = reg.ROOT.datos;
                this.id_cotizacion = ids.id_cotizacion,
                    this.id_proceso_compra = ids.id_proceso_compra,
                    this.id_solicitud = ids.id_solicitud

                //Genera el reporte en función del botón presionado
                if (this.auxFuncion == 'onBtnAdq') {
                    Ext.Ajax.request({
                        url: '../../sis_adquisiciones/control/Cotizacion/reporteOC',
                        params: {'id_cotizacion': this.id_cotizacion},
                        success: this.successExport,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });

                } else if (this.auxFuncion == 'onBtnCot') {
                    Ext.Ajax.request({
                        url: '../../sis_adquisiciones/control/Cotizacion/reporteCotizacion',
                        params: {'id_cotizacion': this.id_cotizacion, tipo: 'cotizado'},
                        success: this.successExport,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });

                } else if (this.auxFuncion == 'onBtnSol') {
                    Ext.Ajax.request({
                        url: '../../sis_adquisiciones/control/Solicitud/reporteSolicitud',
                        params: {'id_solicitud': this.id_solicitud},
                        success: this.successExport,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });

                } else if (this.auxFuncion == 'onBtnProc') {
                    Ext.Ajax.request({
                        url: '../../sis_adquisiciones/control/ProcesoCompra/cuadroComparativo',
                        params: {id_proceso_compra: this.id_proceso_compra},
                        success: this.successExport,
                        success: this.successExport,
                        failure: this.conexionFailure,
                        scope: this
                    });

                }

                else {
                    alert('Reporte no reconocido');
                }

            } else {

                alert('ocurrio un error durante el proceso')
            }

        },

        onBtnVerifPresup: function () {
            var rec = this.sm.getSelected();
            //Se define el nombre de la columna de la llave primaria
            rec.data.tabla_id = this.tabla_id;
            rec.data.tabla = this.tabla;

            Phx.CP.loadWindows('../../../sis_presupuestos/vista/verificacion_presup/VerificacionPresup.php', 'Disponibilidad Presupuestaria', {
                modal: true,
                width: '80%',
                height: '50%',
            }, rec.data, this.idContenedor, 'VerificacionPresup');
        },

        onBtnCheckPresup: function () {
            var rec = this.sm.getSelected();
            //Se define el nombre de la columna de la llave primaria


            Phx.CP.loadWindows('../../../sis_tesoreria/vista/presupuesto/CheckPresupuesto.php', 'Evolución presupuestaria (' + rec.data.moneda + ')', {
                modal: true,
                width: '98%',
                height: '70%',
            }, rec.data, this.idContenedor, 'CheckPresupuesto');
        },

        construyeVariablesContratos: function () {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_workflow/control/Tabla/cargarDatosTablaProceso',
                params: {"tipo_proceso": "CON", "tipo_estado": "finalizado", "limit": "100", "start": "0"},
                success: this.successCotratos,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });


        },
        successCotratos: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (reg.datos) {

                this.ID_CONT = reg.datos[0].atributos.id_tabla

                this.Cmp.id_contrato.store.baseParams.id_tabla = this.ID_CONT;

            } else {
                alert('Error al cargar datos de contratos')
            }
        },

        sistema: 'ADQ',
        id_cotizacion: 0,
        id_proceso_compra: 0,
        id_solicitud: 0,
        auxFuncion: 'onBtnAdq',
        tabla_id: 'id_obligacion_pago',
        tabla: 'tes.tobligacion_pago',


        mostrarWizard: function (rec) {
            var configExtra = [],
                obsValorInicial;

            if (this.nombreVista == 'solicitudvbpresupuestos') {
                obsValorInicial = rec.data.obs_presupuestos;
            }
            if (this.nombreVista == 'PGA') {
                if (this.Cmp.id_plantilla.getValue() == '' || this.Cmp.fecha_pp_ini.getValue() == '') {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado usuario:</b><br>No puede pasar al siguiente estado porque tiene campos pendientes, para completar con información necesaria, <br> Pulse el boton Editar.',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.INFO
                    });


                } else {
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
                                id_obligacion_pago: rec.data.id_obligacion_pago,
                                fecha_ini: rec.data.fecha_tentativa

                            },
                            obsValorInicial: obsValorInicial,
                        }, this.idContenedor, 'FormEstadoWf',
                        {
                            config: [{
                                event: 'beforesave',
                                delegate: this.onSaveWizard,

                            },
                                {
                                    event: 'requirefields',
                                    delegate: function () {
                                        this.onButtonEdit();
                                        this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
                                        this.formulario_wizard = 'si';
                                    }

                                }],

                            scope: this
                        });
                }
            } else {
                if (rec.data.tipo_obligacion == 'pbr' && rec.data.estado == 'vbpresupuestos' && (rec.data.fecha_certificacion_pres == '' || rec.data.fecha_certificacion_pres == null)) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado usuario:</b><br>No puede pasar al siguiente estado porque no ha definido la fecha de Certificación Presupuestaria.',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.INFO
                    });
                } else {

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
                                id_obligacion_pago: rec.data.id_obligacion_pago,
                                fecha_ini: rec.data.fecha_tentativa

                            },
                            obsValorInicial: obsValorInicial,
                        }, this.idContenedor, 'FormEstadoWf',
                        {
                            config: [{
                                event: 'beforesave',
                                delegate: this.onSaveWizard,

                            },
                                {
                                    event: 'requirefields',
                                    delegate: function () {
                                        this.onButtonEdit();
                                        this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
                                        this.formulario_wizard = 'si';
                                    }

                                }],

                            scope: this
                        });
                }
            }
        },
        onSaveWizard: function (wizard, resp) {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/ObligacionPago/siguienteEstadoObligacion',
                // url: '../../sis_tesoreria/control/SolicitudObligacionPago/siguienteEstadoSolObligacion',
                params: {

                    id_obligacion_pago: wizard.data.id_obligacion_pago,
                    id_proceso_wf_act: resp.id_proceso_wf_act,
                    id_estado_wf_act: resp.id_estado_wf_act,
                    id_tipo_estado: resp.id_tipo_estado,
                    id_funcionario_wf: resp.id_funcionario_wf,
                    id_depto_wf: resp.id_depto_wf,
                    obs: resp.obs,
                    json_procesos: Ext.util.JSON.encode(resp.procesos)
                },
                success: this.successWizard,
                failure: this.conexionFailure, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },
        successWizard: function (resp) {
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
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
        },
        clonarOP:function(){
            if(confirm('¿Está seguro de clonar? ')){
                var rec = this.sm.getSelected();
                console.log('obligacion_pago',rec);
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_tesoreria/control/ObligacionPago/clonarOP',
                    params: {
                        id_obligacion_pago: rec.data.id_obligacion_pago
                    },
                    success: this.successSinc,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }
        },


    })
</script>

<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConformidadFinal = Ext.extend(Phx.gridInterfaz, {
        fheight: '80%',
        fwidth: '95%',
        accionFormulario: undefined, //define la accion que se ejcuta en formulario new o edit
        porc_ret_gar: 0,//valor por defecto de retencion de garantia
        constructor: function (config) {
            this.maestro = config;
            //llama al constructor de la clase padre


            Phx.vista.ConformidadFinal.superclass.constructor.call(this, config);
            //this.creaFormularioConformidad();
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
            this.addButton('btnConformidad', {
                text: 'Conformidad',
                grupo: [0, 1],
                iconCls: 'bok',
                disabled: true,
                handler: this.onButtonConformidad,
                tooltip: 'Generar Conformidad Final'
            });
            this.addButton('btnChequeoDocumentosWf',
                {
                    text: 'Documentos',
                    grupo: [0, 1],
                    iconCls: 'bchecklist',
                    disabled: true,
                    handler: this.loadCheckDocumentosSolWf,
                    tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
                }
            );

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
                    fieldLabel: 'Num. Tramite',
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
                    fieldLabel: 'estado',
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
                    fieldLabel: 'Numero',
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
                    fieldLabel: 'Ult PP',
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
                    fieldLabel: 'Tipo Obligacion',
                    allowBlank: false,
                    anchor: '80%',
                    emptyText: 'Tipo Obligacion',
                    renderer: function (value, p, record) {
                        var dato = '';
                        dato = (dato == '' && value == 'pago_directo') ? 'Pago Directo' : dato;
                        dato = (dato == '' && value == 'aduisiciones') ? 'Adquisiciones' : dato;
                        return String.format('{0}', dato);
                    },

                    store: new Ext.data.ArrayStore({
                        fields: ['variable', 'valor'],
                        data: [
                            ['pago_directo', 'Pago Directo']
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
                    fieldLabel: 'Proveedor',
                    anchor: '80%',
                    tinit: false,
                    allowBlank: false,
                    origen: 'PROVEEDOR',
                    gdisplayField: 'desc_proveedor',
                    gwidth: 100,
                    listWidth: '280',
                    resizable: true
                },
                type: 'ComboRec',
                id_grupo: 1,
                filters: {pfiltro: 'pv.desc_proveedor', type: 'string'},
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
                        fields: ['id_contrato', 'numero', 'tipo', 'objeto', 'estado', 'desc_proveedor', 'monto', 'moneda', 'fecha_inicio', 'fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'con.numero#con.tipo#con.monto#prov.desc_proveedor#con.objeto#con.monto',
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
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p>Nro: {numero} ({tipo})</p><p>Obj: <strong>{objeto}</strong></p><p>Prov : {desc_proveedor}</p> <p>Monto: {monto} {moneda}</p><p>Rango: {fecha_inicio} al {fecha_fin}</p></div></tpl>'
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
                    fieldLabel: 'Moneda',
                    anchor: '80%',
                    tinit: false,
                    allowBlank: false,
                    origen: 'MONEDA',
                    gdisplayField: 'moneda',
                    gwidth: 100,
                },
                type: 'ComboRec',
                id_grupo: 1,
                filters: {pfiltro: 'mn.moneda', type: 'string'},
                grid: true,
                form: true
            }, {
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
                grid: true,
                form: true
            },


            {
                config: {
                    name: 'obs',
                    fieldLabel: 'Desc',
                    allowBlank: false,
                    qtip: 'Descripcion del objetivo del pago, o Si el proveedor es PASAJEROS PERJUDICADOS aqui va el nombre del pasajero',
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 1000
                },
                type: 'TextArea',
                filters: {pfiltro: 'obpg.obs', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'total_nro_cuota',
                    fieldLabel: 'Nro Cuotas',
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
                    fieldLabel: 'Obs Presupuestos',
                    gwidth: 180,
                    name: 'obs_presupuestos'
                },
                type: 'Field',
                filters: {pfiltro: 'obpg.obs_presupuestos', type: 'string'},
                grid: true,
                form: false
            },
            //
            {
                config: {
                    name: 'fecha_inicio',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 120,
                    //format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'conf.fecha_inicio', type: 'date'},
                id_grupo: 1,
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 120,
                    //format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'conf.fecha_fin', type: 'date'},
                id_grupo: 1,
                grid: false,
                form: true
            },

            {
                config: {
                    name: 'fecha_conformidad_final',
                    fieldLabel: 'Fecha Conformidad Final',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    //format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'conf.fecha_conformidad_final', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'conformidad_final',
                    fieldLabel: 'Conformidad',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {pfiltro: 'conf.conformidad_final', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'observaciones',
                    fieldLabel: 'Observaciones',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {pfiltro: 'conf.observaciones', type: 'string'},
                id_grupo: 1,
                grid: false,
                form: true
            },
            //
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
        title: 'Conformidad Final',
        //ActSave:'../../sis_tesoreria/control/PlanPago/insertarPlanPago',
        // ActDel:'../../sis_tesoreria/control/PlanPago/eliminarPlanPago',
        ActList: '../../sis_tesoreria/control/PlanPago/listarPlanPagoConforFin',
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
            {name: 'desc_funcionario1'},
            {name: 'estado_reg', type: 'string'},
            {name: 'porc_anticipo', type: 'numeric'},
            {name: 'id_estado_wf', type: 'numeric'},
            {name: 'id_depto', type: 'numeric'},
            {name: 'nombre_depto', type: 'string'},
            {name: 'num_tramite', type: 'string'},
            {name: 'id_proceso_wf', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},

            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'numero', type: 'string'},
            {name: 'tipo_cambio_conv', type: 'numeric'},
            'id_gestion', 'comprometido', 'nro_cuota_vigente', 'tipo_moneda',
            'total_pago', 'pago_variable',
            {name: 'id_depto_conta', type: 'numeric'},
            'total_nro_cuota',
            {name: 'fecha_pp_ini', type: 'date', dateFormat: 'Y-m-d'},
            'rotacion',
            'id_plantilla', 'desc_plantilla',
            'desc_funcionario',
            'ultima_cuota_pp',
            'ultimo_estado_pp',
            'tipo_anticipo',
            'ajuste_anticipo',
            'ajuste_aplicado',
            'monto_estimado_sg',
            'id_obligacion_pago_extendida', 'desc_contrato', 'id_contrato',
            'obs_presupuestos',
            {name: 'uo_ex', type: 'string'},
            {name: 'id_conformidad', type: 'numeric'},

            {name: 'conformidad_final', type: 'string'},
            {name: 'fecha_conformidad_final', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'observaciones', type: 'string'},


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

        creaFormularioConformidad: function () {

            this.formConformidad = new Ext.form.FormPanel({
                id: this.idContenedor + '_CONFOR',
                items: [
                    new Ext.form.DateField({
                        fieldLabel: 'Fecha Conformidad Final',
                        format: 'd/m/Y',
                        name: 'fecha_conformidad_final',
                        allowBlank: false,
                        width: '95%'
                    }),
                    new Ext.form.TextArea({
                        fieldLabel: 'Conformidad Final',
                        name: 'conformidad_final',
                        height: 60,
                        allowBlank: false,
                        width: '95%',
                        maxLength: 5000
                    }),
                    new Ext.form.DateField({
                        fieldLabel: 'Fecha Inicio',
                        format: 'd/m/Y',
                        name: 'fecha_inicio',
                        //height: 150,
                        allowBlank: true,
                        width: '95%'
                    }),
                    new Ext.form.DateField({
                        fieldLabel: 'Fecha Fin',
                        format: 'd/m/Y',
                        name: 'fecha_fin',
                        //height: 150,
                        allowBlank: true,
                        width: '95%'
                    }),
                    new Ext.form.TextArea({
                        fieldLabel: 'Observaciones',
                        name: 'observaciones',
                        height: 50,
                        allowBlank: true,
                        width: '95%',
                        maxLength: 5000
                    })

                ],
                autoScroll: false,
                //height: this.fheight,
                autoDestroy: true,
                autoScroll: true
            });


            // Definicion de la ventana que contiene al formulario
            this.windowConformidad = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Datos Acta Conformidad Final',
                modal: true,
                width: 400,
                height: 400,
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
            this.creaFormularioConformidad();
            console.log('data', data);
            if (data['fecha_conformidad_final'] == '' || data['fecha_conformidad_final'] == undefined || data['fecha_conformidad_final'] == null) {
                console.log('a');
                this.formConformidad.getForm().findField('fecha_inicio').setValue(data.fecha_inicio);
                this.formConformidad.getForm().findField('fecha_fin').setValue(data.fecha_fin);
                this.formConformidad.getForm().findField('fecha_conformidad_final').setValue(data.fecha_conformidad_final);
                this.formConformidad.getForm().findField('conformidad_final').setValue(data.conformidad_final);
                this.formConformidad.getForm().findField('observaciones').setValue(data.observaciones);
                this.windowConformidad.show();
            } else {
                console.log('b');
                Ext.Msg.show({
                    title: 'Alerta',
                    scope: this,
                    msg: 'El acta de Conformidad Final ya se encuentra firmada. Esta seguro de volver a firmar?',
                    buttons: Ext.Msg.YESNO,
                    fn: function (id, value, opt) {
                        if (id == 'yes') {
                            this.formConformidad.getForm().findField('fecha_inicio').setValue(data.fecha_inicio);
                            console.log('conformidad 1', this.formConformidad.getForm().findField('fecha_inicio').getValue());
                            this.formConformidad.getForm().findField('fecha_fin').setValue(data.fecha_fin);
                            console.log('conformidad 2', this.formConformidad.getForm().findField('fecha_fin').getValue());
                            this.formConformidad.getForm().findField('fecha_conformidad_final').setValue(data.fecha_conformidad_final);
                            this.formConformidad.getForm().findField('conformidad_final').setValue(data.conformidad_final);
                            this.formConformidad.getForm().findField('observaciones').setValue(data.observaciones);
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
                //url:'../../sis_tesoreria/control/PlanPago/generarConformidad',
                url: '../../sis_tesoreria/control/Conformidad/generarConformidadFinal',
                success: this.successConformidad,
                failure: this.failureConformidad,
                params: {
                    //'id_conformidad': d.id_conformidad,
                    'id_obligacion_pago': d.id_obligacion_pago,
                    'fecha_inicio': this.formConformidad.getForm().findField('fecha_inicio').getValue(),
                    'fecha_fin': this.formConformidad.getForm().findField('fecha_fin').getValue(),
                    'conformidad_final': this.formConformidad.getForm().findField('conformidad_final').getValue(),
                    'fecha_conformidad_final': this.formConformidad.getForm().findField('fecha_conformidad_final').getValue().dateFormat('d/m/Y'),
                    'observaciones': this.formConformidad.getForm().findField('observaciones').getValue()
                },
                timeout: this.timeout,
                scope: this

            });
            // function acta() {
            //     window.open(../../sis_tesoreria/control/PlanPago/reporteActaConformidadTotal);
            // };

            // function acta() {
            //     Ext.Ajax.request({
            //         url: '../../sis_tesoreria/control/PlanPago/reporteActaConformidadTotal',
            //         params: {'id_obligacion_pago': d},
            //         timeout: this.timeout,
            //         scope: this
            //     });
            // };




        },


        successConformidad: function (resp) {
            this.windowConformidad.hide();
            Phx.vista.ConformidadFinal.superclass.successDel.call(this, resp);
            // var d = this.sm.getSelected().data;
            // Phx.CP.loadingShow();
            // Ext.Ajax.request({
            //     url: '../../sis_tesoreria/control/PlanPago/reporteActaConformidadTotal',
            //
            //     timeout: this.timeout,
            //     scope: this
            // });
            //window.open(url, '../../sis_tesoreria/control/PlanPago/reporteActaConformidadTotal', 'scrollbars=NO,statusbar=NO,left=500,top=250');

        },

        failureConformidad: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.ConformidadFinal.superclass.conexionFailure.call(this, resp);
        },

        onDeclinarConformidad: function () {
            this.windowConformidad.hide();
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


    })
</script>
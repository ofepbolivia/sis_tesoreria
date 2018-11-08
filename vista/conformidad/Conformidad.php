<?php
/**
 * @package pXP
 * @file gen-Conformidad.php
 * @author  (admin)
 * @date 05-09-2018 20:43:03
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.Conformidad = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {

                this.maestro = config.maestro;

                this.tbarItems = ['-',
                    'Gestión:', this.cmbGestion, '-'
                ];
                var fecha = new Date();
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params: {fecha: fecha.getDate() + '/' + (fecha.getMonth() + 1) + '/' + fecha.getFullYear()},
                    success: function (resp) {
                        var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                        this.cmbGestion.setRawValue(fecha.getFullYear());
                        this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                        this.load({params: {start: 0, limit: this.tam_pag}});
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


                //llama al constructor de la clase padre
                Phx.vista.Conformidad.superclass.constructor.call(this, config);

                //this.cmbGestion.on('select', this.capturarEventos, this);
                this.cmbGestion.on('select', function () {
                    if (this.validarFiltros()) {
                        this.capturaFiltros();
                    }
                }, this);

                this.init();
                this.load({params: {start: 0, limit: this.tam_pag}})

                this.iniciarEventos();
            },
            cmbGestion: new Ext.form.ComboBox({
                name: 'gestion',
                fieldLabel: 'Gestion',
                allowBlank: true,
                emptyText: 'Gestion...',
                blankText: 'Año',
                store: new Ext.data.JsonStore(
                    {
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo: {
                            field: 'gestion',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion', 'gestion'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {par_filtro: 'gestion'}
                    }),
                valueField: 'id_gestion',
                triggerAction: 'all',
                displayField: 'gestion',
                hiddenName: 'id_gestion',
                mode: 'remote',
                pageSize: 50,
                queryDelay: 500,
                listWidth: '280',
                hidden: false,
                width: 80
            }),

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_conformidad'
                    },
                    type: 'Field',
                    form: true
                },
                // {
                //     //configuracion del componente
                //     config: {
                //         labelSeparator: '',
                //         inputType: 'hidden',
                //         name: 'id_gestion',
                //         gwidth: 50
                //     },
                //     type: 'Field',
                //     grid: false,
                //     form: false
                // },


                {
                    config: {
                        name: 'id_obligacion_pago',
                        fieldLabel: 'Núm. de Trámite',
                        qtip: 'Tipo de relacion entre comprobantes',
                        allowBlank: false,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_tesoreria/control/ObligacionPago/listarOblPago',
                           // url: '../../sis_tesoreria/control/ObligacionPago/listarObligacionPagoSol',
                            id: 'id_obligacion_pago',
                            root: 'datos',
                            sortInfo: {
                                field: 'id_obligacion_pago',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_obligacion_pago', 'num_tramite', 'estado'],
                            remoteSort: true,
                            baseParams: {
                                par_filtro: 'obpg.num_tramite'
                            }
                        }),
                        valueField: 'id_obligacion_pago',
                        displayField: 'num_tramite',
                        gdisplayField: 'num_tramite',
                        hiddenName: 'id_obligacion_pago',
                        //forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        width: 250,
                        anchor: '100%',
                        gwidth: 150,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['num_tramite']);

                        },
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>Nro: {num_tramite} </p><p>Estado: <strong>{estado}</strong></p></div></tpl>'

                    },
                    type: 'ComboBox',
                    id_grupo: 1,
                    filters: {
                        pfiltro: 'op.num_tramite',
                        type: 'string'
                    },
                    bottom_filter: true,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'fecha_inicio',
                        fieldLabel: 'Fecha Inicio',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'tconf.fecha_inicio', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_fin',
                        fieldLabel: 'Fecha Fin',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'tconf.fecha_fin', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'fecha_conformidad_final',
                        fieldLabel: 'Fecha Conformidad Final',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'tconf.fecha_conformidad_final', type: 'date'},
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
                        maxLength: 800
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'tconf.conformidad_final', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'observaciones',
                        fieldLabel: 'Observaciones',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 250,
                        maxLength: 500
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'tconf.observaciones', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
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
                    filters: {pfiltro: 'tconf.estado_reg', type: 'string'},
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
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
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
                    filters: {pfiltro: 'tconf.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'tconf.id_usuario_ai', type: 'numeric'},
                    id_grupo: 1,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'tconf.usuario_ai', type: 'string'},
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
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
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
                    filters: {pfiltro: 'tconf.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Conformidad',
            ActSave: '../../sis_tesoreria/control/Conformidad/insertarConformidad',
            ActDel: '../../sis_tesoreria/control/Conformidad/eliminarConformidad',
            ActList: '../../sis_tesoreria/control/Conformidad/listarConformidad',
            id_store: 'id_conformidad',
            fields: [
                {name: 'id_conformidad', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'fecha_conformidad_final', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'observaciones', type: 'string'},
                {name: 'id_obligacion_pago', type: 'numeric'},
                {name: 'conformidad_final', type: 'string'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'id_gestion', type: 'numeric'},
                {name: 'num_tramite', type: 'string'},

            ],
            sortInfo: {
                field: 'id_conformidad',
                direction: 'ASC'
            },
            validarFiltros: function () {
                if (this.cmbGestion.isValid()) {
                    return true;
                }
                else {
                    return false;
                }

            },
            capturaFiltros: function (combo, record, index) {

                //this.desbloquearOrdenamientoGrid();
                this.getParametrosFiltro();
                this.load({params: {start: 0, limit: 50}});
            },
            getParametrosFiltro: function () {
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();

            },
            onButtonNew: function () {

                //Phx.vista.Conformidad.superclass.onButtonNew.call(this);
                this.getComponente('id_obligacion_pago').enable();



                if (this.validarFiltros()) {
                    Phx.vista.Conformidad.superclass.onButtonNew.call(this);
                    //this.Cmp.id_gestion.setValue(this.cmbGestion.getValue());
                    this.Cmp.id_obligacion_pago.reset();
                    //this.Cmp.id_obligacion_pago.store.baseParams.id_gestion = this.cmbGestion.getRawValue();
                    this.Cmp.id_obligacion_pago.store.baseParams.id_gestion = this.cmbGestion.getValue();
                    this.Cmp.id_obligacion_pago.modificado = true;

                }

            },
            onButtonAct: function () {
                if (!this.validarFiltros()) {
                    alert('Seleccione una gestion primero')
                }
                else {
                    this.getParametrosFiltro();
                    Phx.vista.Conformidad.superclass.onButtonAct.call(this);
                }
            },
            onButtonEdit: function () {
                Phx.vista.Conformidad.superclass.onButtonEdit.call(this);
                this.getComponente('id_obligacion_pago').disable();


            },



            bdel: true,
            bsave: false,
            bnew: false,
            bdel: true
        }
    )
</script>
		
		
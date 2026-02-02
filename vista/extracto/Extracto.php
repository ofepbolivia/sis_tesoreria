<?php
/**
 * @package pXP
 * @file gen-Extracto.php
 * @author  (gvelasquez)
 * @date 21-11-2016 20:18:54
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.Extracto = Ext.extend(Phx.gridInterfaz, {
            constructor: function (config) {
                this.maestro = config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.Extracto.superclass.constructor.call(this, config);
                this.init();
                //this.load({params:{start:0, limit:this.tam_pag}})
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_extracto'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'id_tipo_movimiento',
                        fieldLabel: 'Tipo de Movimiento',
                        allowBlank: false,
                        emptyText: 'Elija una opción...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_tesoreria/control/TipoMovimiento/listarTipoMovimiento',
                            id: 'id_',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre_movimiento',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_tipo_movimiento', 'nombre_movimiento'],
                            remoteSort: true,
                            baseParams: {par_filtro: 'movtip.nombre_movimiento', tipo: 'transaccion'}
                        }),
                        valueField: 'id_tipo_movimiento',
                        displayField: 'nombre_movimiento',
                        gdisplayField: 'nombre_movimiento',
                        hiddenName: 'id_tipo_movimiento',
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
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['nombre_movimiento']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'tipmov.nombre_movimiento', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'extra.fecha', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'glosa',
                        fieldLabel: 'Glosa',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'extra.glosa', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nro_documento',
                        fieldLabel: 'Nro Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'extra.nro_documento', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'credito',
                        fieldLabel: 'Credito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'extra.credito', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'debito',
                        fieldLabel: 'Debito',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'extra.debito', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'saldo',
                        fieldLabel: 'Saldo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'extra.saldo', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nro_cbte',
                        fieldLabel: 'Nro Cbte',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'extra.nro_cbte', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'cod_operacion',
                        fieldLabel: 'Código Operación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength: 20,
                        mode: 'local',
                        triggerAction: 'all',
                        editable: false,
                        store: new Ext.data.ArrayStore({
                            fields: ['codigo', 'descripcion'],
                            data: [
                                ['DEP', 'Depósito'],
                                ['CRV', 'Crédito'],
                                ['TFD', 'Transferencia Débito'],
                                ['TEC', 'Transferencia Crédito'],
                                ['AJT', 'Abonos']
                            ]
                        }),
                        valueField: 'codigo',
                        displayField: 'descripcion'
                    },
                    type: 'ComboBox',
                    filters: {
                        pfiltro: 'extra.cod_operacion',
                        type: 'string'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_conciliacion',
                        fieldLabel: 'Estado Conciliacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'extra.estado_conciliacion', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_conciliar',
                        fieldLabel: 'Importe Conciliar',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'extra.importe_conciliar', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'cuenta_transf',
                        fieldLabel: 'Cuenta Transf',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'extra.cuenta_transf', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'secuencial',
                        fieldLabel: 'Secuencial',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'extra.secuencial', type: 'numeric'},
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
                    filters: {pfiltro: 'extra.estado_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'extra.id_usuario_ai', type: 'numeric'},
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
                    filters: {pfiltro: 'extra.usuario_ai', type: 'string'},
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
                    filters: {pfiltro: 'extra.fecha_reg', type: 'date'},
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
                    filters: {pfiltro: 'extra.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_cuenta_bancaria'
                    },
                    type: 'Field',
                    form: true
                }
            ],
            tam_pag: 50,
            title: 'Extracto',
            ActSave: '../../sis_tesoreria/control/Extracto/insertarExtracto',
            ActDel: '../../sis_tesoreria/control/Extracto/eliminarExtracto',
            ActList: '../../sis_tesoreria/control/Extracto/listarExtracto',
            id_store: 'id_extracto',
            fields: [
                {name: 'id_extracto', type: 'numeric'},
                {name: 'id_tipo_movimiento', type: 'numeric'},
                {name: 'nombre_movimiento', type: 'string'},
                {name: 'saldo', type: 'numeric'},
                {name: 'cod_operacion', type: 'string'},
                {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'credito', type: 'numeric'},
                {name: 'nro_cbte', type: 'string'},
                {name: 'estado_conciliacion', type: 'string'},
                {name: 'importe_conciliar', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'nro_documento', type: 'string'},
                {name: 'cuenta_transf', type: 'string'},
                {name: 'secuencial', type: 'numeric'},
                {name: 'glosa', type: 'string'},
                {name: 'debito', type: 'numeric'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'id_cuenta_bancaria', type: 'numeric'},

            ],
            sortInfo: {
                field: 'id_extracto',
                direction: 'ASC'
            },
            bdel: true,
            bsave: true,

            onReloadPage: function (m) {
                this.maestro = m;
                //this.cmpIdFinalidad.store.baseParams.id_cuenta_bancaria =this.maestro.id_cuenta_bancaria;
                this.store.baseParams = {id_cuenta_bancaria: this.maestro.id_cuenta_bancaria, mycls: this.cls};
                this.load({params: {start: 0, limit: this.tam_pag}});
            },
            onButtonNew: function () {
                Phx.vista.Extracto.superclass.onButtonNew.call(this);
                this.Cmp.id_cuenta_bancaria.setValue(this.maestro.id_cuenta_bancaria);
            },
            onButtonEdit: function () {
                Phx.vista.Extracto.superclass.onButtonEdit.call(this);
                this.Cmp.id_cuenta_bancaria.setValue(this.maestro.id_cuenta_bancaria);
            }
        }
    )
</script>
<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PlanPagoConformidadFinal = {
        bedit: false,
        bnew: false,
        bsave: false,
        bdel: false,
        beditGroups: [],
        bdelGroups: [],
        bactGroups: [0, 1],
        btestGroups: [],
        bexcelGroups: [0, 1],
        require: '../../../sis_tesoreria/vista/plan_pago/ConformidadFinal.php',
        requireclase: 'Phx.vista.ConformidadFinal',
        title: 'Conformidad Final',
        nombreVista: 'PlanPagoConformidadFinal',
        //ActList: '../../sis_tesoreria/control/ObligacionPago/listarObligacionPagoSol',
        //ActList: '../../sis_tesoreria/control/PlanPago/listarPlanPagoConformidadFin',
        ActList: '../../sis_tesoreria/control/PlanPago/listarPlanPagoConforFin',

        gruposBarraTareas: [{
            name: 'pendiente',
            title: '<H1 align="center"><i class="fa fa-thumbs-o-down"></i>Actas sin firma</h1>',
            grupo: 0,
            height: 0
        },
            {
                name: 'realizada',
                title: '<H1 align="center"><i class="fa fa-eye"></i>Actas con firma</h1>',
                grupo: 1,
                height: 0
            }],

        actualizarSegunTab: function (name, indice) {
            if (this.finCons) {
                if (name == 'pendiente') {
                    this.store.baseParams.tipo_interfaz = 'planpagoconformidadpendiente';
                } else {
                    this.store.baseParams.tipo_interfaz = 'planpagoconformidadrealizada';
                }

                this.load({params: {start: 0, limit: this.tam_pag}});
            }
        },
        constructor: function (config) {

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


            Phx.vista.PlanPagoConformidadFinal.superclass.constructor.call(this, config);
            this.creaFormularioConformidad();
            this.iniciarEventos();

            // this.addButton('btnConformidad', {
            //     text: 'Conformidad',
            //     grupo: [0, 1],
            //     iconCls: 'bok',
            //     disabled: true,
            //     handler: this.onButtonConformidad,
            //     tooltip: 'Generar conformidad para el pago (Firma acta de conformidad)'
            // });

            this.addButton('diagrama_gantt', {
                text: 'Gantt',
                grupo: [0, 1],
                iconCls: 'bgantt',
                disabled: true,
                handler: diagramGantt,
                tooltip: '<b>Diagrama Gantt de proceso macro</b>'
            });


            this.cmbGestion.on('select', this.capturarEventos, this);


            this.load({
                params: {
                    start: 0,
                    limit: this.tam_pag
                }
            });
            function diagramGantt() {
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
            }

            this.store.baseParams.tipo_interfaz = 'planpagoconformidadpendiente';

            this.load({
                params: {
                    start: 0,
                    limit: this.tam_pag
                }
            });
            this.finCons = true;

        },

        iniciarEventos: function () {

        },
        capturarEventos: function () {
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();

            this.load({params: {start: 0, limit: this.tam_pag}});
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

        preparaMenu: function (n) {
            //var data = this.getSelectedData();
            var tb = this.tbar;
            Phx.vista.PlanPagoConformidadFinal.superclass.preparaMenu.call(this, n);
            this.getBoton('btnConformidad').enable();
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            this.getBoton('btnPagoRel').enable();
            this.getBoton('btnObs').enable();

        },

        liberaMenu: function () {

        },
        // south: {
        //     url: '../../../sis_tesoreria/vista/prorrateo/Prorrateo.php',
        //     title: 'Prorrateo',
        //     height: '40%',
        //     cls: 'Prorrateo'
        // },
        // sortInfo: {
        //     field: 'numero_op',
        //     direction: 'ASC'
        // }
    };


</script>

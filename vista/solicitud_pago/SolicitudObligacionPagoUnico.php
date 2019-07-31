<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  (Maylee Perez Pastor
 *@date 29-03-2019 10:22:05
 *@description Archivo con la interfaz de usuario que permite
 *dar el visto a solicitudes de compra
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolicitudObligacionPagoUnico = {
        bedit: true,
        bnew: true,
        bsave: false,
        bdel: true,
        require:'../../../sis_tesoreria/vista/solicitud_pago/SolicitudObligacionPago.php',
        requireclase: 'Phx.vista.SolicitudObligacionPago',
        title: 'Pago Únicos Excepcionales (Sin Proceso)',
        // nombreVista: 'obligacionPagoUnico',
        nombreVista: 'solicitudObligacionPagoUnico',
        ActList:'../../sis_tesoreria/control/ObligacionPago/listarObligacionPagoSol',
        /*
         *  Interface heredada para el sistema de adquisiciones para que el reposnable
         *  de adqusiciones registro los planes de pago , y ase por los pasos configurados en el WF
         *  de validacion, aprobacion y registro contable
         * */

        constructor: function(config) {

            this.tbarItems = ['-',
                'Gestión:',this.cmbGestion,'-'
            ];
            var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(fecha.getFullYear());
                    this.store.baseParams.id_gestion=reg.ROOT.datos.id_gestion;
                    this.load({params:{start:0, limit:this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });


            Phx.vista.SolicitudObligacionPagoUnico.superclass.constructor.call(this,config);
            this.getBoton('ini_estado').setVisible(false);

            this.cmbGestion.on('select', this.capturarEventos, this);
        },

        cmbGestion: new Ext.form.ComboBox({
            //name: 'gestion',
            // id: 'gestion_reg',
            fieldLabel: 'Gestion',
            allowBlank: true,
            emptyText: 'Gestion...',
            blankText: 'Año',
            editable: false,
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
            pageSize: 5,
            queryDelay: 500,
            listWidth: '280',
            hidden: false,
            width: 80
        }),

        capturarEventos: function () {

            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.load({params: {start: 0, limit: this.tam_pag}});
        },

        tabsouth:[
            {
                url:'../../../sis_tesoreria/vista/obligacion_det/ObligacionDet.php',
                title:'Detalle',
                height:'50%',
                cls:'ObligacionDet'
            },
            {
                //carga la interface de registro inicial
                url:'../../../sis_tesoreria/vista/solicitud_plan_pago/SolPlanPagoRegIni.php',
                title:'Plan de Pagos (Reg. Adq.)',
                height:'50%',
                cls:'SolPlanPagoRegIni'
            }

        ],
        onButtonEdit:function(){

            var data= this.sm.getSelected().data;
            this.cmpTipoObligacion.disable();
            this.cmpDepto.disable();
            this.cmpFecha.disable();
            this.cmpTipoCambioConv.disable();
            this.Cmp.id_moneda.disable();


            this.mostrarComponente(this.Cmp.id_funcionario);
            this.Cmp.id_funcionario.disable();


            Phx.vista.SolicitudObligacionPagoUnico.superclass.onButtonEdit.call(this);

            this.Cmp.id_contrato.store.baseParams.filter = "[{\"type\":\"numeric\",\"comparison\":\"eq\", \"value\":\""+ this.Cmp.id_proveedor.getValue()+"\",\"field\":\"CON.id_proveedor\"}]";
            this.Cmp.id_contrato.modificado = true;

            this.cmpFuncionario.store.baseParams.fecha = this.Cmp.fecha.getValue().dateFormat(this.Cmp.fecha.format);


            if(data.estado != 'borrador'){
                this.Cmp.tipo_anticipo.disable();

                this.Cmp.id_proveedor.disable();

            }
            else{

                this.Cmp.id_proveedor.enable();
                this.mostrarComponente(this.Cmp.id_proveedor);
            }


        },

        onButtonNew:function(){


            //Phx.vista.SolicitudObligacionPagoUnico.superclass.onButtonNew.call(this);



            //abrir formulario de solicitud
            var me = this;
            // me.objSolForm = Phx.CP.loadWindows('../../../sis_tesoreria/vista/obligacion_pago/FormObligacion.php',
            me.objSolForm = Phx.CP.loadWindows('../../../sis_tesoreria/vista/solicitud_pago/FormObligacionPagoUnico.php',
                'Formulario de Obligación de Pago Directo',
                {
                    modal:true,
                    width:'90%',
                    height:'90%'
                }, {data:{objPadre: me}
                },
                this.idContenedor,
                'FormObligacionPagoUnico',
                {
                    config:[{
                        event:'successsave',
                        delegate: this.onSaveForm,

                    }],

                    scope:this
                });

        },
        onSaveForm: function(form,  objRes){
            var me = this;
            //muestra la ventana de documentos para este proceso wf
            //16-07-2019se quita paradisminuir pasos al registro
            /*Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Documentos del pago directo',
                {
                    width:'90%',
                    height:500
                },
                {
                    id_obligacion_pago: objRes.ROOT.datos.id_obligacion_pago,
                    id_proceso_wf: objRes.ROOT.datos.id_proceso_wf,
                    num_tramite: objRes.ROOT.datos.num_tramite,
                    estao: objRes.ROOT.datos.estado,
                    nombreVista: 'Formulario de solicitud de compra',
                    tipo: 'solcom'  //para crear un boton de guardar directamente en la ventana de documentos

                },
                this.idContenedor,
                'DocumentoWf',
                {
                    config:[{
                        event:'finalizarsol',
                        delegate: this.onCloseDocuments,

                    }],

                    scope:this
                }
            )*/

            form.panel.destroy();
            me.reload();

        },
        onCloseDocuments: function(paneldoc, data){
            var newrec = this.store.getById(data.id_obligacion_pago);
            if(newrec){
                this.sm.selectRecords([newrec]);
                paneldoc.panel.destroy();
                this.fin_registro( undefined, undefined, undefined,  paneldoc);

            }
        },
        rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template('<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obligación de pago:&nbsp;&nbsp;</b> {numero}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Depto:&nbsp;&nbsp;</b> {nombre_depto}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Justificación:&nbsp;&nbsp;</b> {obs}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obs. del área de presupuestos:&nbsp;&nbsp;</b> {obs_presupuestos}</p><br>'
            )
        }),

    };
</script>
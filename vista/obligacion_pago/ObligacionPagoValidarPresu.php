<?php
/**
 *@package pXP
 *@file ObligacionPagoValidarPresu.php
 *@author  (breydi.vasquez)
 *@date 08/01/2020
 *@description Archivo con la interfaz de usuario que permite
 *
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ObligacionPagoValidarPresu = {
        bedit: true,
        bnew: false,
        bsave: false,
        bdel: false,
        require: '../../../sis_tesoreria/vista/obligacion_pago/ObligacionPago.php',
        requireclase: 'Phx.vista.ObligacionPago',
        title: 'Validador Presupuestario',
        nombreVista: 'ObligacionPagoValidarPresu',
        ActList:'../../sis_tesoreria/control/ObligacionPago/listarObligacionPagoSol',

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
                    this.store.baseParams.tramite_sin_presupuesto_centro_c = 'sin';
                    this.load({params:{start:0, limit:this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });


            Phx.vista.ObligacionPagoValidarPresu.superclass.constructor.call(this,config);
            this.addButton('btnPresuAprobado', {                
                text: 'Autorizar',            
                iconCls:'bball_white',            
                handler:this.validarPresupuesto,
                tooltip: '<b>Autoriza</b> el tramite a nivel centro de costo<br/>'
            });            
            this.getBoton('ini_estado').setVisible(false);
            this.getBoton('ant_estado').setVisible(false);
            this.getBoton('fin_registro').setVisible(false);
            this.getBoton('reporte_com_ejec_pag').setVisible(false);
            this.getBoton('reporte_plan_pago').setVisible(false);
            this.getBoton('btnCheckPresupeusto').setVisible(false);
            this.getBoton('btnVerifPresup').setVisible(false);
            this.getBoton('btnObs').setVisible(false);
            this.getBoton('clonarOP').setVisible(false);
            this.getBoton('btnExtender').setVisible(false);
            this.getBoton('ajustes').setVisible(false);
            this.getBoton('est_anticipo').setVisible(false);
            this.getBoton('edit').setVisible(false);
            this.menuAdq.setVisible(false);
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
        preparaMenu:function(n){ 
            var data = this.getSelectedData();                      
            var tb =this.tbar;
            Phx.vista.ObligacionPagoValidarPresu.superclass.preparaMenu.call(this,n);            
            if (data['presupuesto_aprobado'] == 'sin_presupuesto_cc') {
                this.getBoton('btnPresuAprobado').setIconClass('bball_green');
                this.getBoton('btnPresuAprobado').enable();
            }                                                       
            return tb
        },        
        validarPresupuesto:function(){
            var d= this.sm.getSelected().data;                         
            Phx.CP.loadingHide();            
            Ext.Ajax.request({                
                url:'../../sis_tesoreria/control/ObligacionPago/aprobarPresupuestoSolicitud',
                params: { id_obligacion_pago: d.id_obligacion_pago, aprobar: 'si'},                
                success: function(resp){
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));                    
                    if(!reg.ROOT.error) {
                        this.reload();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        capturarEventos: function () {

            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.store.baseParams.tramite_sin_presupuesto_centro_c = 'sin';
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
                url:'../../../sis_tesoreria/vista/plan_pago/PlanPagoRegIni.php',
                title:'Plan de Pagos (Reg. Adq.)',
                height:'50%',
                cls:'PlanPagoRegIni'
            }

        ],

        rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template('<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obligación de pago:&nbsp;&nbsp;</b> {numero}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Depto:&nbsp;&nbsp;</b> {nombre_depto}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Justificación:&nbsp;&nbsp;</b> {obs}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obs del área de presupeustos:&nbsp;&nbsp;</b> {obs_presupuestos}</p><br>'
            )
        }),

    };
</script>
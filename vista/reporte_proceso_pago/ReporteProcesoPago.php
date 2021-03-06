<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteProcesoPago = Ext.extend(Phx.frmInterfaz, {
        Atributos : [

            {
                config:{
                    name: 'monto',
                    fieldLabel: 'Montos mayor a',
                    allowBlank: false,
                    anchor: '35%',
                    gwidth: 180,
                    maxLength:8
                },
                type:'NumberField',
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            }
        ],
        title : 'Generar Reporte',
        ActSave : '../../sis_tesoreria/control/ObligacionPago/reporteProcesoPago',
        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte</b>',
        constructor : function(config) {
            Phx.vista.ReporteProcesoPago.superclass.constructor.call(this, config);
            this.init();

        },


        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>
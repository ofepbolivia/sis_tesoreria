<?php
/**
 *@package pXP
 *@file    ObligacionPagoComprasExterior.php
 *@author  franklin.espinoza
 *@date    01-06-2018
 *@description  Archivo con la interfaz de usuario que permite
 *              dar seguimiento a un proceso de pago compras en el exterior.
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ObligacionPagoComprasExterior = {
        bedit: true,
        bnew: true,
        bsave: false,
        bdel: true,
        require: '../../../sis_tesoreria/vista/obligacion_pago/ObligacionPago.php',
        requireclase: 'Phx.vista.ObligacionPago',
        title: 'Pago Compras Exterior',
        nombreVista: 'PCE',

        gruposBarraTareas:[
            {name:'borrador_pce',title:'<H1 align="center"><i class="fa fa-paper-plane"></i>Borrador</h1>',grupo:0,height:0},
            {name:'proceso_pce',title:'<H1 align="center"><i class="fa fa-plus-circle"></i>Proceso</h1>',grupo:1,height:0}],

        actualizarSegunTab: function(name, indice){
            if (this.getBoton('ini_estado')!= undefined){
                this.getBoton('ini_estado').setVisible(false);
            }

            if(this.getBoton('ant_estado')!=undefined && name=='borrador_pce'){
                this.getBoton('ant_estado').setVisible(false);
            }

            if(name=='proceso_pce'){
                this.getBoton('edit').setVisible(true);
            }
            this.store.baseParams.pce_estado = name;
            this.load({params:{start:0, limit:this.tam_pag}});

        },

        bactGroups:  [0,1],
        bexcelGroups: [0,1],

        constructor: function(config) {
            this.Atributos[this.getIndAtributo('id_contrato')].config.allowBlank = true;
            Phx.vista.ObligacionPagoComprasExterior.superclass.constructor.call(this,config);
            this.getBoton('ini_estado').setVisible(false);
            this.getBoton('ant_estado').setVisible(false);
            this.store.baseParams = {tipo_interfaz:this.nombreVista};
            this.store.baseParams.pce_estado = 'borrador_pce';
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

        preparaMenu: function(n){

            var data = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.ObligacionPagoComprasExterior.superclass.preparaMenu.call(this,n);
            /*if (data.tipo_obligacion == 'pga' && (data.estado == 'vbpoa' || data.estado == 'vb_jefe_aeropuerto' || data.estado == 'vbpresupuestos' ||
                data.estado == 'suppresu' || data.estado == 'registrado' || data.estado == 'en_pago')) {
                this.getBoton('edit').setVisible(true);
            }*/

            return tb;
        },

        liberaMenu: function(){
            var tb = Phx.vista.ObligacionPagoComprasExterior.superclass.liberaMenu.call(this);
            if(tb){

            }
            return tb;
        },
        onButtonEdit:function(){

            var data= this.sm.getSelected().data;

            //(f.e.a)habilitar campo contrato
            if(data.tipo_obligacion == 'pce'){
                this.Cmp.id_contrato.enable();
            }

            this.cmpTipoObligacion.disable();
            this.cmpDepto.disable();
            this.cmpFecha.disable();
            this.cmpTipoCambioConv.disable();
            this.Cmp.id_moneda.disable();

            this.mostrarComponente(this.Cmp.id_plantilla);
            this.mostrarComponente(this.Cmp.fecha_pp_ini);

            this.mostrarComponente(this.Cmp.id_funcionario);
            this.Cmp.id_funcionario.disable();


            Phx.vista.ObligacionPagoComprasExterior.superclass.onButtonEdit.call(this);

            this.Cmp.id_contrato.store.baseParams.filter = "[{\"type\":\"numeric\",\"comparison\":\"eq\", \"value\":\""+ this.Cmp.id_proveedor.getValue()+"\",\"field\":\"CON.id_proveedor\"}]";
            this.Cmp.id_contrato.modificado = true;

            this.cmpFuncionario.store.baseParams.fecha = this.Cmp.fecha.getValue().dateFormat(this.Cmp.fecha.format);


            if(data.estado != 'borrador'){
                this.Cmp.tipo_anticipo.disable();

                if(data.tipo_obligacion == 'pce'){
                    this.mostrarComponente(this.Cmp.id_proveedor);
                    this.Cmp.id_proveedor.enable();
                }else{
                    this.Cmp.id_proveedor.disable();
                }


            }
            else{

                this.Cmp.id_proveedor.enable();
                this.mostrarComponente(this.Cmp.id_proveedor);
            }


        },

        onButtonNew:function(){
            //abrir formulario de solicitud
            var me = this;
            me.objSolForm = Phx.CP.loadWindows('../../../sis_tesoreria/vista/obligacion_pago/FormPagoCompraExterior.php',
                'Formulario de Pagos Compras en el Exerior',
                {
                    modal:true,
                    width:'90%',
                    height:'90%'
                }, {data:{objPadre: me}
                },
                this.idContenedor,
                'FormPagoCompraExterior',
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
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Documentos del pago único',
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
            )

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
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Obs del área de presupeustos:&nbsp;&nbsp;</b> {obs_presupuestos}</p><br>'
            )
        }),

    };
</script>

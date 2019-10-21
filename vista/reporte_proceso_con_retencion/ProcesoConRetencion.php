<?Php
/**
 *@package pXP
 *@file   PagosSinFacturasAsociadas.php
 *@author  MAM
 *@date    09-11-2016
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ProcesoConRetencion = Ext.extend(Phx.frmInterfaz, {
        Atributos : [
            {
                config:{
                    name: 'tipo_reporte',
                    fieldLabel: 'Tipo de Reporte',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['codigo', 'nombre'],
                        data : [['reporte_nor', 'Reporte normal'], ['reporte_pro', 'Reporte con Prorrateo']]
                    }),
                    anchor : '30%',
                    valueField : 'codigo',
                    displayField : 'nombre',
                    gwidth:100
                },
                type:'ComboBox',
                id_grupo:1,
                bottom_filter: true,
                grid:true,
                form:true
            },
			{
            config:{
                name: 'fecha_ini',
                fieldLabel: 'Fecha Inicio',
                allowBlank: true,
                anchor: '30%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'fecha_ini',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
            },
            {
            config:{
                name: 'fecha_fin',
                fieldLabel: 'Fecha Fin',
                allowBlank: true,
                anchor: '30%',
                gwidth: 100,
                format: 'd/m/Y',
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
            },
            type:'DateField',
            filters:{pfiltro:'fecha_fin',type:'date'},
            id_grupo:1,
            grid:true,
            form:true
            },
            {
                config:{
                    name:'id_proveedor',
                    hiddenName:'id_proveedor',
                    fieldLabel:'Proveedor',
                    // typeAhead:false,
                    forceSelection:true,
                    allowBlank:true,
                    // disabled:true,
                    emptyText:'Proveedor...',
                    store:new Ext.data.JsonStore({
                        url:'../../sis_parametros/control/Proveedor/listarProveedorCombos',
                        id:'id_proveedor',
                        root:'datos',
                        sortInfo:{
                            field:'rotulo_comercial',
                            direction:'ASC'
                        },
                        totalProperty:'total',
                        fields:[
                            'id_proveedor',
                            'desc_proveedor',
                            'codigo',
                            'nit',
                            'rotulo_comercial',
                            'lugar',
                            'email'
                        ],
                        // turn on remote sorting
                        remoteSort:true,
                        baseParams: {_adicionar:'si', par_filtro: 'desc_proveedor#codigo#nit#rotulo_comercial'}
                    }      ),
                    valueField:'id_proveedor',
                    displayField:'desc_proveedor',
                    gdisplayField:'desc_proveedor',
                    triggerAction:'all',
                    lazyRender:true,
                    resizable:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    listWidth:280,
                    minChars:2,
                    gwidth:100,
                    anchor:'30%',
                    renderer:function (value,
                                       p,
                                       record){
                        if (record.data[
                            'desc_proveedor'
                            ]         ){
                            return String.format(            '{0}',
                                record.data[
                                    'desc_proveedor'
                                    ]            );
                        }         return         ''         ;
                    },
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{rotulo_comercial}</b></p><p>{desc_proveedor}</p><p>{codigo}</p><p>NIT:{nit}</p><p>Lugar:{lugar}</p><p>Email: {email}</p></div></tpl>',

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'pv.desc_proveedor',
                    type:'string'
                },
                bottom_filter:true,
                grid:true,
                form:true
            },
            {
                config: {
                    name: 'id_contrato',
                    hiddenName: 'id_contrato',
                    fieldLabel: 'Contrato',
                    //typeAhead: false,
                    forceSelection: false,
                    allowBlank: true,
                    //disabled: true,
                    emptyText: 'Contratos...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_contratos/control/Contrato/listarContratos',
                        id: 'id_contrato',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_contrato',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_contrato','nro_tramite', 'numero', 'tipo', 'objeto', 'estado', 'id_proveedor', 'monto', 'id_moneda', 'fecha_inicio', 'fecha_fin'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {
                            _adicionar:'si',
                            par_filtro: 'con.numero#con.tipo#con.monto#con.id_proveedor#con.objeto#con.monto',
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
                    anchor: '30%',
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
            }],

            title : 'Proceso con retencion del 7% ',
            ActSave : '../../sis_tesoreria/control/PlanPago/reporteProcesoConRetencion',

            topBar : true,
            botones : false,
            labelSubmit : 'Imprimir',
            tooltipSubmit : '<b>Proceso con retencion del 7% </b>',

            constructor : function(config) {
            Phx.vista.ProcesoConRetencion.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
            },

            iniciarEventos:function(){
            this.cmpFechaIni = this.getComponente('fecha_ini');
            this.cmpFechaFin = this.getComponente('fecha_fin');
			this.cmpProveedor = this.getComponente('id_proveedor');
            this.cmpContrato = this.getComponente('id_contrato');

            this.Cmp.id_proveedor.on('select',function(c,r,i) {
                
                this.Cmp.id_contrato.reset();
                this.Cmp.id_contrato.store.baseParams.pruebass = r.data.id_proveedor;
                this.Cmp.id_contrato.modificado = true;
            },this);
            },
            tipo : 'reporte',
            clsSubmit : 'bprint'

})
</script>

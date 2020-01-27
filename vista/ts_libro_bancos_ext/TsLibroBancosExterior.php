<script>
Phx.vista.TsLibroBancosExterior=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
        this.initButtons = ['Gestión','-',this.cmbGestion,'-'];
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
		Phx.vista.TsLibroBancosExterior.superclass.constructor.call(this,config);
		this.init();        
        this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag}});
        this.store.baseParams.pes_estado = 'exterior'; 
        this.finCons = true;        
	},
    gruposBarraTareas:[        
        {name:'exterior',title:'<H1 align="center"><i class="fa fa-list-ul"></i>Exterior</h1>',grupo:0,height:0},
        {name:'interior',title:'<H1 align="center"><i class="fa fa-list-ul"></i>Locales</h1>',grupo:2,height:0},
        {name:'pgaexterior',title:'<H1 align="center"><i class="fa fa-list-ul"></i>PGA - Exterior</h1>',grupo:3,height:0},
        {name:'pgainterior',title:'<H1 align="center"><i class="fa fa-list-ul"></i>PGA - Locales</h1>',grupo:4,height:0},
    ],
    bactGroups:  [0,2 ,3,4],
    bexcelGroups: [0,2,3,4],

    actualizarSegunTab: function(name, indice){
        this.cmbGestion.show(true);        
        console.log('name',this.initButtons);
            if(this.finCons){
                this.store.baseParams.pes_estado = name;
                this.load({params:{start:0, limit:this.tam_pag}});
            }
        },    
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_obligacion_pago'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'num_tramite',
				fieldLabel: 'N° Tramite',
				allowBlank: false,
				anchor: '100%',
				gwidth: 120			
			},
				type:'TextField',
				filters:{pfiltro:'plbex.num_tramite',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter: true
		},
		{
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha VoBo.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 80,
                format: 'd/m/Y', 
                renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'plbex.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},        
		{
			config:{
				name: 'nro_cuenta',
				fieldLabel: 'N° Cuota',
				allowBlank: false,
				anchor: '70%',
				gwidth: 70,
                renderer:function(value, p, record){
                    return  String.format('<div style="text-align:center;">{0}</div>', Ext.util.Format.number(value,'0.000/i'));
                }
			},
				type:'TextField',
				filters:{pfiltro:'plbex.nro_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'monto',
				fieldLabel: 'Importe Cuota',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
                renderer: function (value, p, record){                    
                    return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                }
			},
				type:'TextField',
				filters:{pfiltro:'plbex.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'cod_moneda',
				fieldLabel: 'Moneda',
				allowBlank: false,
				anchor: '60%',
				gwidth: 60,
                renderer: function (value, p, record){                    
                    if(value == 'Bs') return  String.format('<div style="text-align:center;color:green;">{0}</div>',value);
                    else return  String.format('<div style="text-align:center;color:blue;">{0}</div>',value);                    
                }
			},
				type:'TextField',
				filters:{pfiltro:'plbex.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},                
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Libro de Bancos',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150
			},
				type:'TextField',
				filters:{pfiltro:'plbex.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter: true
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'Codigo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 80
			},
				type:'TextField',
				filters:{pfiltro:'plbex.codigo',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'nombre_estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '100%',
				gwidth: 100
			},
				type:'TextField',
				filters:{pfiltro:'plbex.nombre_estado',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'obs',
				fieldLabel: 'Proveido',
				allowBlank: true,
				anchor: '290%',
				gwidth: 290
			},
				type:'TextField',
				filters:{pfiltro:'plbex.obs',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
                bottom_filter: true
		},
		{
			config:{
				name: 'desc_persona',
				fieldLabel: 'Funcionario',
				allowBlank: true,
				anchor: '100%',
				gwidth: 200
			},
				type:'TextField',
				filters:{pfiltro:'plbex.desc_persona',type:'string'},
				id_grupo:1,
				grid:true,
				form:false,
                bottom_filter: true
		},        
		{
			config:{
				name: 'fun_usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '100%',
				gwidth: 200		
			},
				type:'TextField',
				filters:{pfiltro:'plbex.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
                form:true,				
                bottom_filter: true
		},
        {
            config:{
                name: 'estado_pp',
                fieldLabel: 'Estado',
                allowBlank: true,
                anchor: '100%',
                gwidth: 100
            },
            type:'TextField',
            filters:{pfiltro:'plbex.estado_pp',type:'string'},
            id_grupo:1,
            grid:true,
            form:true,
            bottom_filter: true
        }

	],
	tam_pag:50,	
	title:'procesos pagos exterior',	
	ActList:'../../sis_tesoreria/control/ObligacionPago/TsLibroBancosExterior',
	id_store:'id_obligacion_pago',
	fields: [
		{name:'id_obligacion_pago', type: 'numeric'},
		{name:'num_tramite', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'nro_cuenta', type: 'string'},
		{name:'codigo', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'nombre_estado', type: 'string'},		
		{name:'obs', type: 'string'},
		{name:'desc_persona', type: 'string'},	
		{name:'fun_usuario_ai', type: 'string'},		
        {name:'monto', type: 'numeric'},
        {name:'moneda', type: 'string'},
        {name:'cod_moneda', type: 'string'},
        {name:'estado_pp', type: 'string'}
	],
	sortInfo:{
		field: 'fecha',
		direction: 'DESC'
	},
	bdel:false,
	bsave:false,
    btest:false,
    bedit:false,
    bnew:false,
    cmbGestion: new Ext.form.ComboBox({            
            fieldLabel: 'Gestion',            
            allowBlank: false,
            blankText: '... ?',
            emptyText: 'Gestion...',
            name: 'id_gestion',
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
            width: 80
        }),
    iniciarEventos: function(){
        this.cmbGestion.on('select', function () {            
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.load({params:{start:0, limit:this.tam_pag}});
            }, this);
    }
})
</script>
		
		
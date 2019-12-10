<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolPlanPagoRegIni = {
        bdel:true,
        bedit:true,
        bsave:false,
        require:'../../../sis_tesoreria/vista/solicitud_plan_pago/SoliPlanPago.php',
        requireclase:'Phx.vista.SoliPlanPago',
        title:'Registro de Planes de Pago',
        nombreVista: 'SolPlanPagoRegIni',
        constructor: function(config) {
            this.maestro=config.maestro;
            //this.Cmp.desc_moneda
            //console.log('moneda',this.maestro.id_moneda);
            this.Atributos.splice(12,0,
                {
                    config:{
                        name:'es_ultima_cuota',
                        fieldLabel:'Ultima Cuota',
                        allowBlank: true,
                        anchor: '70%',
                        gwidth: 85,
                        renderer: function (value, p, record, rowIndex, colIndex) {
                            if (value == true) {
                                var checked = 'checked';
                            }
                            return String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:30px;width:30px;"  type="checkbox"  {0}></div>', checked);
                        }
                    },
                    type:'Checkbox',
                    id_grupo: 0,
                    grid: true,
                    form:true
                }
            );
            this.Atributos.splice(14,0,
                {
                    config:{
                        name: 'nro_cbte',
                        fieldLabel: 'Nro. Comprobante',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth:120,
                        maxLength:255,
                        renderer: function (vale,p, record) {
                            if(record.data.nro_cbte == null)
                                return String.format('{0}', '');
                            else
                                return String.format('{0}', "<div style='color: green'><b>"+record.data.nro_cbte+"</b></div>");
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'tcon.nro_cbte',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false,
                    bottom_filter: true
                }
            );
            this.Atributos.splice(15,0,
                {
                    config:{
                        name: 'c31',
                        fieldLabel: 'C31',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth:90,
                        maxLength:255,
                        renderer: function (vale,p, record) {
                            if(record.data.c31==null)
                                return String.format('{0}', '');
                            else
                                return String.format('{0}', "<div style='color: green'><b>"+record.data.c31+"</b></div>");
                        }
                    },
                    type:'TextField',
                    filters:{pfiltro:'tcon.c31',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false,
                    bottom_filter: true
                }
            );
            Phx.vista.SolPlanPagoRegIni.superclass.constructor.call(this,config);
            //this.creaFormularioConformidad();
            ////formulario de departamentos
            //this.crearFormularioEstados();
            //si la interface es pestanha este código es para iniciar
            var dataPadre = Phx.CP.getPagina(this.idContenedorPadre).getSelectedData()
            if(dataPadre){
                this.onEnablePanel(this, dataPadre);
            }
            else
            {
                this.bloquearMenus();
            }

            /*this.addButton('btnVerifPresup', {
                  text : 'Disponibilidad',
                  iconCls : 'bassign',
                  disabled : true,
                  handler : this.onBtnVerifPresup,
                  tooltip : '<b>Verificación de la disponibilidad presupuestaria</b>'
              });*/
            this.creaFormularioConformidad();
            this.crearFomularioDepto();
            this.iniciarEventos();


            this.addButton('clonarPP', {
                text: 'Clonar Plan de Pago',
                iconCls: 'blist',
                disabled: false,
                handler: this.clonarPP,
                tooltip: 'Clonar el registro de una Cuota'
            });
            //esconde boton para mandar a borrador

            this.getBoton('ini_estado').hide();
            this.getBoton('solDevPagPP').hide();
            this.getConfigPago();
            this.grid.addListener('cellclick', this.marcar_ultima_cuota,this);
        },
        marcar_ultima_cuota: function(grid, rowIndex, columnIndex, e){
            var record = this.store.getAt(rowIndex).data,
                fieldName = grid.getColumnModel().getDataIndex(columnIndex);
            if(fieldName == 'es_ultima_cuota') {
                Ext.Ajax.request({
                    url: '../../sis_tesoreria/control/PlanPago/setUltimaCuota',
                    params: {
                        id_obligacion_pago: record.id_obligacion_pago,
                        id_plan_pago: record.id_plan_pago,
                        es_ultima_cuota: record.es_ultima_cuota,
                        accion: 'grid'
                    },
                    success: function (resp) {
                        this.reload();
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }
        },
        getConfigPago: function(id_plantilla){
            var data = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_tesoreria/control/PlanPago/getConfigPago',
                params: { test: 'test'},
                success: function(resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if(!reg.ROOT.error){
                        console.log('----llega...', reg);
                        var tipHab = reg.ROOT.datos.tes_tipo_pago_deshabilitado.split(",");
                        console.log('tipHab',tipHab)
                        for (var ele = 0; ele <  tipHab.length ; ele++) {
                            for (var k = 0; k <  this.arrayStore['INICIAL'].length ; k++) {
                                console.log(tipHab[ele], this.arrayStore['INICIAL'][k][0])
                                if(this.arrayStore['INICIAL'][k][0] == tipHab[ele]){
                                    this.arrayStore['INICIAL'].splice(k, 1);
                                }
                            }
                        }
                    }
                    else{
                        alert(reg.ROOT.mensaje);
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        iniciarEventos:function(){
            //(f.e.a)
            /*this.Cmp.fecha_tentativa.on('select', function(value, date){
            var anio = this.maestro.num_tramite.substring(13,18);//date.getFullYear();
            var fecha_inicio = new Date(anio+'/01/1');
            var fecha_fin = new Date(anio+'/12/31');
            //control de fechas de inicio y fin de costos
            this.Cmp.fecha_costo_ini.setMinValue(fecha_inicio);
            this.Cmp.fecha_costo_fin.setMaxValue(fecha_fin);
        }, this);*/
            this.Cmp.monto.on('change',this.calculaMontoPago,this);
            this.Cmp.descuento_anticipo.on('change',this.calculaMontoPago,this);
            this.Cmp.monto_no_pagado.on('change',this.calculaMontoPago,this);
            this.Cmp.otros_descuentos.on('change',this.calculaMontoPago,this);
            this.Cmp.monto_retgar_mo.on('change',this.calculaMontoPago,this);
            this.Cmp.descuento_ley.on('change',this.calculaMontoPago,this);
            this.Cmp.descuento_inter_serv.on('change',this.calculaMontoPago,this);
            this.Cmp.monto_anticipo.on('change',this.calculaMontoPago,this);
            this.Cmp.monto_excento.on('change',this.calculaMontoPago,this);
            this.Cmp.id_plantilla.on('select',function(cmb,rec,i){
                this.getDecuentosPorAplicar(rec.data.id_plantilla);
                
              //  if(this.Cmp.id_plantilla.getValue() == '52')
              if (rec.data.id_plantilla == 52 )
                {                
                    //this.Cmp.forma_pago.reset();
                    this.Cmp.forma_pago.store.baseParams = {tipo:"debito_automatico"};
                    this.Cmp.forma_pago.modificado = true;
                    this.Cmp.forma_pago.store.load({params:{start:0,limit:10},
                        callback:function(){
                        }, scope : this
                    });
                }else{
                    this.Cmp.forma_pago.reset();
                    this.Cmp.forma_pago.store.baseParams='';
                    this.Cmp.forma_pago.modificado = true;
                }
                this.Cmp.monto_excento.reset();
                if(rec.data.sw_monto_excento=='si'){
                    this.Cmp.monto_excento.enable();
                    this.Cmp.tipo_excento.setValue(rec.data.tipo_excento);
                    this.Cmp.valor_excento.setValue(rec.data.valor_excento);
                }
                else{
                    this.Cmp.monto_excento.disable();
                    this.Cmp.tipo_excento.setValue('variable');
                    this.Cmp.monto_excento.setValue(0);
                    this.Cmp.valor_excento.setValue(0);
                }
            },this);
            //evento para definir los tipos de pago
            this.Cmp.tipo.on('select',function(cmb,rec,i){
                var data = this.getSelectedData();
                //segun el tipo define los campo visibles y no visibles
                this.setTipoPago[rec.data.variable](this,data);
                this.unblockGroup(1);
                this.window.doLayout();
                //(f.e.a)control de fechas de inicio y fin de costos
                //var fecha = this.Cmp.fecha_tentativa.getValue();
                //var anio = fecha.getFullYear();
                /*var anio = this.maestro.num_tramite.substring(13,18);
                    var fecha_inicio = new Date(anio+'/01/1');
                    var fecha_fin = new Date(anio+'/12/31');
                    this.Cmp.fecha_costo_ini.setMinValue(fecha_inicio);
                    this.Cmp.fecha_costo_fin.setMaxValue(fecha_fin);*/
                if(this.accionFormulario == 'NEW'){
                    if(rec.data.variable == 'devengado'||
                        rec.data.variable =='devengado_pagado'||
                        rec.data.variable =='devengado_pagado_1c'||
                        rec.data.variable =='devengado_pagado_1c_sp'||
                        rec.data.variable =='rendicion'||
                        rec.data.variable =='anticipo'){
                        this.obtenerFaltante('registrado,ant_parcial_descontado');
                    }
                    if(rec.data.variable == 'ant_parcial'){
                        this.obtenerFaltante('ant_parcial');
                    }
                    if(rec.data.variable == 'dev_garantia'){
                        this.obtenerFaltante('dev_garantia');
                    }
                    if(rec.data.variable == 'especial'){
                        this.obtenerFaltante('especial');
                    }
                }
                if (this.accionFormulario == 'NEW_PAGO' || this.accionFormulario == 'NEW' || this.accionFormulario ==  'NEW_ANT_APLI'){
                    if(rec.data.variable == 'pagado'){
                        this.iniciaPagoDelDevengado(data);
                    }
                    if(rec.data.variable == 'ant_aplicado'){
                        this.iniciaAplicacion(data);
                    }
                }
            },this);
            this.Cmp.monto_ajuste_ag.on('change',function(cmp, newValue, oldValue){
                if(newValue > this.Cmp.monto_ejecutar_total_mo.getValue()){
                    cmp.setValue(oldValue);
                }
            },this);
            //eventos de fechas de costo
            this.Cmp.fecha_costo_ini.on('change',function( o, newValue, oldValue ){
                this.Cmp.fecha_costo_fin.setMinValue(newValue);
                this.Cmp.fecha_costo_fin.reset();
            }, this);
            //eventos de fechas de costo
            this.Cmp.fecha_costo_fin.on('change',function(o, newValue, oldValue){
                this.Cmp.fecha_costo_ini.setMaxValue(newValue);
            }, this);
            //
            this.Cmp.tipo.on('change',function(o, newValue, oldValue){
                this.Cmp.fecha_costo_ini.setMaxValue(newValue);
                this.Cmp.fecha_costo_ini.reset();
                this.Cmp.fecha_costo_fin.reset();
            }, this);
            //(may)para controlar que id de estas cuentas bancarias sean desactivados los campos en forma de pago (61,78,79)
            // this.Cmp.id_cuenta_bancaria.on('select', function (groupRadio,radio) {
            //     this.ocultarFP(this,radio.inputValue);
            //
            // }, this);
            this.Cmp.forma_pago.on('change',function(groupRadio,radio){
                this.ocultarCheCue(this,radio);
            },this);
            //para filtro id_cuenta_bancaria
            this.Cmp.id_depto_lb.on('select',function(a,b,c){
                this.Cmp.id_cuenta_bancaria.setValue('');
                this.Cmp.id_cuenta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_lb.getValue();
                this.Cmp.id_cuenta_bancaria.store.baseParams.permiso = 'todos';
                this.Cmp.id_cuenta_bancaria.modificado=true;
            },this);
            // //para filtro de id_proveedor_cta_bancaria
            // //this.maestro.id_proveedor.on('blur',function(a,b,c){
            //     this.Cmp.id_proveedor_cta_bancaria.setValue('');
            //     this.Cmp.id_proveedor_cta_bancaria.store.baseParams.nombre_pago = this.maestro.desc_proveedor;
            //     // this.Cmp.id_cuenta_bancaria.store.baseParams.permiso = 'todos';s
            //     this.Cmp.id_proveedor_cta_bancaria.modificado=true;
            // //},this);
        },
        onBtnSolPlanPago:function(){
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_tesoreria/control/PlanPago/solicitudPlanPago',
                params:{'id_plan_pago':rec.data.id_plan_pago,id_obligacion_pago:this.maestro.id_obligacion_pago},
                success: this.successExport,
                failure: function() {
                    //console.log("fail");
                },
                timeout: function() {
                    //console.log("timeout");
                },
                scope:this
            });
        },
        setTipoPagoNormal:function(){
            this.mostrarComponente(this.Cmp.monto_ejecutar_total_mo)
            this.habilitarDescuentos();
        },
        enableDisable:function(val){
            //devengado
            this.Cmp.nombre_pago.disable();
            this.ocultarComponente(this.Cmp.nombre_pago);
            //pago
            this.deshabilitarDescuentos();
            this.Cmp.nombre_pago.enable();
            this.mostrarComponente(this.Cmp.nombre_pago);
            this.habilitarDescuentos();
            this.Cmp.monto_no_pagado.setValue(0);
            this.Cmp.otros_descuentos.setValue(0);
            this.Cmp.liquido_pagable.setValue(0);
            this.Cmp.monto_ejecutar_total_mo.setValue(0);
            this.Cmp.monto_retgar_mo.setValue(0);
            this.Cmp.descuento_ley.setValue(0);
            this.calculaMontoPago()
        },
        iniciaAplicacion:function(data){
            //carga la plantilla con el mismo documento que el devengado
            var me = this;
            this.Cmp.id_plantilla.store.load({
                params:{start:0,limit:1,id_plantilla:data.id_plantilla},
                callback:function(){
                    me.Cmp.id_plantilla.setValue(data.id_plantilla);
                    me.Cmp.id_plantilla.modificado = true;
                    me.getDecuentosPorAplicar(data.id_plantilla);
                }
            });
            this.inicioValores();
            this.tmp_porc_monto_excento_var = data.porc_monto_excento_var;
            //obtiene el monto de apgo que falta registrar
            //y el monto de anticpo parcial que falta por descontar
            if(data.pago_variable == 'si'){
                this.obtenerFaltante('ant_aplicado_descontado_op_variable',data.id_plan_pago);
            }
            else{
                this.obtenerFaltante('ant_aplicado_descontado',data.id_plan_pago);
            }
        },
        iniciaPagoDelDevengado:function(data){
            //carga la plantilla con el mismo documento que el devengado
            var me = this;
            this.Cmp.id_plantilla.store.load({
                params:{start:0, limit:1, id_plantilla: data.id_plantilla},
                callback:function(){
                    me.Cmp.id_plantilla.setValue(data.id_plantilla);
                    me.Cmp.id_plantilla.modificado = true;
                    me.getDecuentosPorAplicar(data.id_plantilla);
                }
            });
            this.inicioValores();
            //calcula el porcentaje de retencio de garantia si en el
            //devengado es mayor a cero, se utiliza en la funcion calculaMontoPago
            this.Cmp.porc_monto_retgar.setValue(data.porc_monto_retgar);
            this.porc_ret_gar =  data.porc_monto_retgar;
            this.tmp_porc_monto_excento_var = data.porc_monto_excento_var;
            //obtiene el monto de apgo que falta registrar
            //y el monto de anticpo parcial que falta por descontar
            this.obtenerFaltante('registrado_pagado,ant_parcial_descontado',data.id_plan_pago);
        },
        onButtonNew:function(){
            this.porc_ret_gar = 0; //resetea valor por defecto de retencion de garantia
            var data = this.getSelectedData();
            this.ocultarGrupo(2); //ocultar el grupo de ajustes
            //variables temporales
            this.tmp_porc_monto_excento_var = undefined;
            if(data){
                // para habilitar registros de cuotas de pago
                //sobre los devengados
                Phx.vista.SolPlanPagoRegIni.superclass.onButtonNew.call(this);
                this.Cmp.tipo.enable();
                // this.blockGroup(1);//bloqueaos el grupo , detalle de pago
                this.Cmp.id_obligacion_pago.setValue(this.maestro.id_obligacion_pago);
                console.log('obligacion pago',this.Cmp.id_obligacion_pago.getValue());
                this.Cmp.id_plan_pago_fk.setValue(data.id_plan_pago);
                if( data.tipo == 'devengado'||data.tipo == 'devengado_pagado'){
                    //
                    this.accionFormulario = 'NEW_PAGO';  //esta bandera modifica el ,  obtenerFaltante
                    if(data.estado =='devengado'){
                        if(data.monto*1  > data.total_pagado*1){
                            this.Cmp.tipo.store.loadData(this.arrayStore.DEVENGAR);
                        }else{
                            alert('No queda nada por pagar');
                        }
                    }
                    else{
                        alert('El devengado no fue completado');
                    }
                }
                else{
                    if(data.tipo == 'anticipo'){
                        this.accionFormulario = 'NEW_ANT_APLI';
                        if(data.monto*1  > data.total_pagado*1  && data.estado =='anticipado'){
                            this.Cmp.tipo.store.loadData(this.arrayStore.ANTICIPO);
                        }
                    }
                }
            }
            else{
                this.accionFormulario = 'NEW';
                //para habilitar registros de cuota de devengado
                Phx.vista.SolPlanPagoRegIni.superclass.onButtonNew.call(this);
                this.Cmp.tipo.enable();
                this.Cmp.id_obligacion_pago.setValue(this.maestro.id_obligacion_pago);
                console.log('obligacion pago',this.maestro.tipo_moneda);
                console.log('obligacion pago',this.maestro.moneda);
                console.log('obligacion pago',this.maestro.id_moneda);
                /*if(this.maestro.tipo_moneda=='Base'){
                    if (this.maestro.moneda=='Bolivianos')
                        this.Cmp.desc_moneda.setValue('Bs');
                    else
                        this.Cmp.desc_moneda.setValue('$');
                }*/
                if (this.maestro.moneda=='Bolivianos')
                    this.Cmp.desc_moneda.setValue('Bs');
                else
                    this.Cmp.desc_moneda.setValue('$');
                /*else */if ( this.maestro.moneda=='Dolares Americanos')
                    this.Cmp.desc_moneda.setValue('$us');
                // this.blockGroup(1)//bloqueaos el grupo , detalle de pago
                //tipo pago (OPERACION)
                if(this.maestro.tipo_obligacion === 'pago_especial'){
                    //prepara pagos de enticipo
                    this.Cmp.tipo.store.loadData(this.arrayStore.ESPECIAL)
                }
                else{
                    if(this.maestro.nro_cuota_vigente == 0 && this.maestro.tipo_anticipo == 'si'){
                        //prepara pagos de enticipo
                        this.Cmp.tipo.store.loadData(this.arrayStore.ANT_PARCIAL)
                    }
                    else{
                        //prepara pagos iniciales
                        //modificacion para las internacionales muestren solo genere un comprobante
                        // this.Cmp.tipo.store.loadData(this.arrayStore.INICIAL)
                        this.Cmp.tipo.store.loadData(this.arrayStore.INT)
                    }
                }
                this.inicioValores()
            }
            //para filtro id_cuenta_bancaria
            if(this.Cmp.id_depto_lb.getValue() > 0){
                this.Cmp.id_cuenta_bancaria.store.baseParams = Ext.apply(this.Cmp.id_cuenta_bancaria.store.baseParams,{ id_depto_lb:this.Cmp.id_depto_lb.getValue(), permiso: 'todos'});
                this.Cmp.id_cuenta_bancaria.modificado = true;
            }
            // //para filtro de id_proveedor_cta_bancaria
            // this.Cmp.id_proveedor_cta_bancaria.store.baseParams = Ext.apply(this.Cmp.id_proveedor_cta_bancaria.store.baseParams,{ id_proveedor: this.maestro.id_proveedor});
            // this.Cmp.id_proveedor_cta_bancaria.modificado = true;
        },
        onButtonEdit:function(){
            Phx.vista.SolPlanPagoRegIni.superclass.onButtonEdit.call(this);
            this.Cmp.forma_pago.enable();
            var data = this.getSelectedData();
            //para filtro id_cuenta_bancaria
            if(this.Cmp.id_depto_lb.getValue() > 0){
                this.Cmp.id_cuenta_bancaria.store.baseParams = Ext.apply(this.Cmp.id_cuenta_bancaria.store.baseParams,{ id_depto_lb:this.Cmp.id_depto_lb.getValue(), permiso: 'todos'});
                this.Cmp.id_cuenta_bancaria.modificado = true;
            }
            // //para filtro de id_proveedor_cta_bancaria
            // this.Cmp.id_proveedor_cta_bancaria.store.baseParams = Ext.apply(this.Cmp.id_proveedor_cta_bancaria.store.baseParams,{ id_proveedor: this.maestro.id_proveedor});
            // this.Cmp.id_proveedor_cta_bancaria.modificado = true;
        },
        obtenerFaltante:function(_filtro,_id_plan_pago){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_tesoreria/control/ObligacionPago/obtenerFaltante',
                params:{ope_filtro:_filtro,
                    id_obligacion_pago: this.maestro.id_obligacion_pago,
                    id_plan_pago:_id_plan_pago},
                success:this.successOF,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        successOF:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
                if(reg.ROOT.datos.monto_total_faltante > 0){
                    this.Cmp.monto.setValue(reg.ROOT.datos.monto_total_faltante);
                    //si se trata de un nuevo pago
                    if(this.accionFormulario == 'NEW_PAGO'){
                        this.Cmp.monto_retgar_mo.setValue(this.porc_ret_gar*reg.ROOT.datos.monto_total_faltante);
                    }
                    if (this.tmp_porc_monto_excento_var > 0 ){
                        this.Cmp.monto_excento.setValue(reg.ROOT.datos.monto_total_faltante*this.tmp_porc_monto_excento_var);
                    }
                    if(this.Cmp.tipo.getValue()=='devengado_pagado'||this.Cmp.tipo.getValue()=='devengado_pagado_1c'||this.Cmp.tipo.getValue()=='pagado'||this.Cmp.tipo.getValue()=='devengado_pagado_1c_sp'){
                        //si es un pago calculamos el descuento de anticipo
                        this.Cmp.descuento_anticipo.setValue(reg.ROOT.datos.ant_parcial_descontado);
                        this.Cmp.descuento_anticipo.maxValue=reg.ROOT.datos.ant_parcial_descontado;
                    }
                }
                else{
                    this.Cmp.monto.setValue(0);
                }
                this.calculaMontoPago();
            }else{
                alert('error al obtener saldo por registrar')
            }
        },
        onReloadPage:function(m){
            this.maestro=m;
            this.store.baseParams={id_obligacion_pago:this.maestro.id_obligacion_pago,tipo_interfaz:this.nombreVista};
            this.load({params:{start:0, limit:this.tam_pag}})
        },
        successSave: function(resp) {
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            Phx.vista.SolPlanPagoRegIni.superclass.successSave.call(this,resp);
        },
        successDel:function(resp){
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            Phx.vista.SolPlanPagoRegIni.superclass.successDel.call(this,resp);
        },
        preparaMenu:function(n){
            var data = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.SolPlanPagoRegIni.superclass.preparaMenu.call(this,n);
            this.getBoton('ant_estado').disable();
            this.getBoton('sig_estado').disable();
            this.getBoton('solDevPagPP').disable();
            //alert('pasa el constructor ....')
            //alert(data['estado'])
            //this.getBoton('clonarPP').enable();
            console.log('datos pp: ',data);
            if (data['estado'] == 'borrador'){
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
                this.getBoton('new').disable();
                this.getBoton('SolPlanPago').enable();
               if(data['desc_plantilla']=='Extracto Bancario'){
                    console.log('llega',data['desc_plantilla']);
                    this.getBoton('sig_estado').disable();
                    this.getBoton('sig_estado').setVisible(false);
                    this.getBoton('solDevPagPP').enable();
                    this.getBoton('solDevPagPP').setVisible(true);
                }
                else{
                    this.getBoton('sig_estado').enable();
                    this.getBoton('sig_estado').setVisible(true);
                    this.getBoton('solDevPagPP').disable();
                    this.getBoton('solDevPagPP').hide();
                }

            }
            else{
                //alert('lega ......')
                if ((data['tipo'] == 'devengado'||data['tipo']== 'devengado_pagado') && data['estado']== 'devengado'&& (data.monto*1)  > (data.total_pagado*1) ){
                    this.getBoton('new').enable();
                }
                else{
                    this.getBoton('new').disable();
                }
                if(data['estado']== 'anticipado' && data['tipo']== 'anticipo'&& (data.monto*1)  > (data.total_pagado*1)){
                    this.getBoton('new').enable();
                }
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
                this.getBoton('SolPlanPago').enable();
            }
            if(data['sinc_presupuesto']=='si'&& (data['estado']== 'vbconta'||data['estado']== 'borrador')){
                this.getBoton('SincPresu').enable();
            }
            else{
                this.getBoton('SincPresu').disable();
            }
            /*if (data.tipo=='devengado'  || data.tipo=='devengado_pagado' || data.tipo=='devengado_pagado_1c') {
              this.getBoton('btnConformidad').enable();
           } else {
              this.getBoton('btnConformidad').disable();
           }*/
            // this.getBoton('btnVerifPresup').enable();
            // this.getBoton('btnDocCmpVnt').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            this.getBoton('btnPagoRel').enable();
            this.getBoton('btnDocCmpVnt').enable();
            this.getBoton('btnImportePP').enable();
            // if (data['id_int_comprobante'] != 'Null' || data['id_int_comprobante'] != ''){
            //     this.getBoton('btnDocCmpVnt').enable();
            //     // this.getBoton('btnDocCmpVnt').disable();
            // }else{
            //     this.getBoton('btnDocCmpVnt').disable();
            //     // this.getBoton('btnDocCmpVnt').enable();
            // }
        },
        liberaMenu:function(){
            var tb = Phx.vista.SolPlanPagoRegIni.superclass.liberaMenu.call(this);
            console.log('tb: ',tb);
            this.getBoton('sig_estado').setVisible(true);
            if(tb){
                this.getBoton('SincPresu').disable();
                this.getBoton('SolPlanPago').disable();
                //this.getBoton('btnVerifPresup').disable();
                this.getBoton('ant_estado').disable();
                this.getBoton('solDevPagPP').hide();
                this.getBoton('sig_estado').disable();
                //this.getBoton('btnConformidad').disable();
                this.getBoton('btnChequeoDocumentosWf').disable();
                this.getBoton('btnPagoRel').disable();
                this.getBoton('btnDocCmpVnt').disable();
                this.getBoton('btnImportePP').disable();
                //this.getBoton('clonarPP').disable();



            }
            return tb
        },
        getDecuentosPorAplicar:function(id_plantilla){
            var data = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_contabilidad/control/PlantillaCalculo/recuperarDescuentosPlantillaCalculo',
                params:{id_plantilla:id_plantilla},
                success:this.successAplicarDesc,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        onBtnVerifPresup : function() {
            var rec = this.sm.getSelected();
            //Se define el nombre de la columna de la llave primaria
            rec.tabla_id = this.tabla_id;
            rec.tabla = this.tabla;
            Phx.CP.loadWindows('../../../sis_presupuestos/vista/verificacion_presup/VerificacionPresup.php', 'Disponibilidad Presupuestaria', {
                modal : true,
                width : '80%',
                height : '50%',
            }, rec.data, this.idContenedor, 'VerificacionPresup');
        },
        onSubmitDepto: function (x, y, id_depto_conta) {
            var data = this.getSelectedData();
            if (this.formDEPTO.getForm().isValid() || id_depto_conta) {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    // form:this.form.getForm().getEl(),
                    url: '../../sis_tesoreria/control/PlanPago/solicitarDevPag',
                    params: {
                        id_plan_pago: data.id_plan_pago,
                        id_depto_conta: id_depto_conta ? id_depto_conta : this.cmpDeptoConta.getValue()
                    },
                    success: this.successSincGC,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                })
            }
        },
        // successSincGC:function(resp){
        //     Phx.CP.loadingHide();
        //     this.wDEPTO.hide();
        //     var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        //     if(reg.ROOT.datos.resultado != 'falla'){
        //
        //         this.reload();
        //     }else{
        //         Ext.Msg.show({
        //             title: 'Información',
        //             msg: reg.ROOT.datos.mensaje,
        //             buttons: Ext.Msg.OK,
        //             width: 700,
        //             icon: Ext.Msg.INFO
        //         });
        //         //alert(reg.ROOT.datos.mensaje)
        //     }
        // },
        successSincGC: function (resp) {
            Phx.CP.loadingHide();
            this.wDEPTO.hide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (reg.ROOT.datos.resultado != 'falla') {
                this.reload();
            } else {
                alert(reg.ROOT.datos.mensaje)
            }
        },
        clonarPP:function(){
            if(confirm('¿Está seguro de clonar? ')){
                var rec = this.sm.getSelected();
                console.log('plan_pago ',rec.data.id_plan_pago);
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_tesoreria/control/PlanPago/clonarPP',
                    params: {
                        id_plan_pago: rec.data.id_plan_pago
                    },
                    success: this.successSinc,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }
        },
        east:{
            url:'../../../sis_tesoreria/vista/prorrateo/Prorrateo.php',
            title:'Prorrateo',
            width:400,
            cls:'Prorrateo'
        },
        tabla_id: 'id_plan_pago',
        tabla: 'tes.tplan_pago'
    };
</script>
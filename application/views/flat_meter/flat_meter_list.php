<div id="meter_row_controller" class="justify-content-center mx-4 my-4">
    <div>  
        <div class="col-lg-9">
            <div class="form-group col-lg-4 float-left">
                <select class="form-control float-left col-lg-12 meter_type_id" v-model="meter_type_id" multiple style="height:200px !important; width: 250px">                    
                    <option v-for="{id,name} in meter_type_list" :value="id">{{name}}</option>
                </select>
                <div class="clearfix"></div>
                <button class="btn btn-danger" v-on:click="deselect_type_id()">X</button>
            </div>
            <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
                <label class="col-lg-4 c float-left">Номер квартиры</label>
                <input class="form-control col-lg-8 float-left" type="text" v-model="flat_name">
            </div>
            <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
                <label class="col-lg-4 c float-left">ID квартиры</label>
                <input class="form-control col-lg-8 float-left" type="number" v-model="flat_id">
            </div>
            <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
                <label class="col-lg-4 c float-left">Номер стояка</label>
                <input class="form-control col-lg-8 float-left" type="text" v-model="tube">
            </div>
            <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
                <label class="col-lg-4 c float-left">Значение</label>
                <input class="form-control col-lg-8 float-left" type="text" v-model="flat_meter_value">
            </div>
        </div>
        
        <div class="form-group col-lg-3 float-left">
            <button class="btn btn-success search_button" v-on:click="search(0)">Найти</button>
            <button class="btn btn-primary add_flat_meter" data-toggle="modal" data-target="#add_flat_meter" ref="add_button" v-on:click="">Добавить</button>
        </div>
        
        <div class="clearfix"></div>
        <div>Всего записей : {{total_rows}}</div>
    </div>
    <?php $this->load->view("/flat_meter/flat_meter_add");?>
    <div>
        <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>

        <table class="table table-bordered" v-if="meter_list.length>0">
            <thead>
            <tr>
                <th>id</th>
                <th>DEV EUI</th>
                <th>Номер квартиры</th>
                <th>ID квартиры</th>
                <th>Датчик</th>
                <th>Порт</th>
                <th>Дата получения значения</th>
                <th>Значение</th>                
                <!--
                <th>tarif_id</th>
                <th>correction</th>
                <th>measure</th>
                -->
                <th>Номер стояка</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(meter_row,index) in meter_list">
                <td>{{meter_row.id}}</td>
                <td>{{meter_row.deveui}}</td>
                <td>{{meter_row.flat_name}} </td>
                <td>{{meter_row.flat_id}}</td>
                <td>{{meter_row.meter_name}} ({{meter_row.name}})</td>                
                <td>{{meter_row.port}}</td>
                <td>{{meter_row.stamp}}</td>
                <td>{{meter_row.value}}</td>                
                <!--
                <td>{{meter_row.tarif_id}}</td>
                <td>{{meter_row.correction}}</td>
                <td>{{meter_row.measure}}</td>
                -->
                <td>{{meter_row.tube}}</td>
                <td>
                    <span class="fa fa-pencil edit-row" v-on:click="edit_row(index)"></span>
                    <span class="fa fa-remove delete-row float-right" v-on:click="delete_row(index,meter_row.id)"></span>
                </td>
            </tr>
            </tbody>
        </table>

        <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    </div>
</div>

<script src="/resources/js/components.js"></script>
<script type="text/javascript">
    el = new Vue({
        el: "#meter_row_controller",        
        data: {            
            current_page: 0,
            total_pages: 0,
            total_rows: 0,
            flat_id: 0,
            tube:'',
            per_page: 25,
            pages: [],
            flat_meter_value:'',
            flat_name: '',
            date_to: '',
            error: "",
            new_row : {edit_id:0},
            meter_type_id:[],
            meter_type_list:{},
            meter_list: []
        },
        methods: {
            delete_row: function (index, id) {
                if(window.confirm("Вы подтверждаете удаление?")){
                    axios.post("/flat_meter/delete/" + id, {
                        id: id,
                    }).then(function (result) {
                        switch (result.data.status) {
                            case 200:
                                el._data.meter_list.splice(index, 1)
                                break;
                            case 300:
                                alert(result.message)
                                break;
                            default:
                                alert(result.data.message)
                                break;
                        }
                    }).catch(function (e) {
                        console.log(e)
                    })
                }
            },
            get_type_list:function(){
                axios.post("/flat_meter/get_type_list/", {}).then(function (result) {
                    //el._data.meter_type_list.push({id:0,name:'Не выбрано'})
                    el._data.meter_type_list = result.data.contents;
                }).catch(function (e) {
                    console.log(e)
                })
            },
            check_form: function (new_row) {
                var errors = [];
                if (!new_row.name) {
                    errors.push("Укажите наименование!");
                }
                if (!new_row.flat_id) {
                    errors.push("Укажите ID квартиры!");
                }
                if (!new_row.acc_id) {
                    errors.push("Укажите ID счетчика!");
                }
                if (!new_row.meter_type_id) {
                    errors.push("Укажите тип счетчика!");
                }
                if (!new_row.value) {
                    errors.push("Укажите значение!");
                }
                if (!new_row.tube) {
                    errors.push("Укажите номер стояка!");
                }
                return errors;
            },
            add_new_row: function (new_row) {
                var errors = this.check_form(new_row)
                if (errors.length > 0) {
                    this.error = errors.join(" ")
                    return;
                }
                var url = "/flat_meter/add_new_meter";
                if (this.new_row.edit_id != 0) {
                    url = "/flat_meter/edit_meter/" + this.new_row.edit_id;
                }

                axios.post(url, new_row).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            alert(result.data.message);
                            document.querySelector(".close_dialog").click();
                            el.search(1);
                            break;
                        case 300:
                            alert(result.data.message)
                            break;
                        default:
                            alert(result.data.message)
                            break;
                    }
                }).catch(function (e) {
                    console.log(e)
                })
            },
            edit_row: function (index) {
                this.new_row = el.$data.meter_list[index]
                this.new_row.edit_id = this.new_row.id
                this.$refs.add_button.click()
            },
            search: function (page = 0) {
                this.current_page = page
                axios.post("/flat_meter/search/" + page, {
                    meter_type_id: el._data.meter_type_id,
                    flat_id : el._data.flat_id,
                    tube: el._data.tube,
                    flat_name: el._data.flat_name,
                    flat_meter_value : el._data.flat_meter_value
                }).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.pages.splice(0);                            
                            el._data.meter_list.splice()
                            el._data.meter_list = result.data.content;
                            el._data.total_rows = result.data.total_rows;
                            el._data.total_pages = Math.ceil(el._data.total_rows / el._data.per_page);

                            if(el._data.total_rows > 25){
                                el._data.pages.splice(0);
                                el._data.pages.push(1)
                                let tmp_page = page === 0 ? page + 1 : page;
                                let z = 0;
                                while (tmp_page > 2) {
                                    z++;
                                    el._data.pages.push(tmp_page--)
                                    if (z === 5) {
                                        break;
                                    }
                                }
                                z = 0;
                                tmp_page = page === 0 ? page + 2 : page + 1
                                while (tmp_page < el._data.total_pages) {
                                    z++;
                                    el._data.pages.push(tmp_page++)
                                    if (z === 5) {
                                        break;
                                    }
                                }
                                if(el._data.total_pages !== page){
                                    el._data.pages.push(el._data.total_pages)
                                }
                                el._data.pages.sort(function (a, b) {
                                    return a - b;
                                });
                            }
                            break;
                        case 300:
                            break;
                        default:
                            alert(result.data.message)
                            break;
                    }
                }).catch(function (e) {
                    console.log(e)
                })
            },
            deselect_type_id: function(){              
                el._data.meter_type_id.splice(0)                
            }
        },
        mounted(){
            setTimeout(function(){
                el.get_type_list();
                el.search()
            },100)            
        } 
    })
</script>
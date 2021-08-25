<div id="vue-container" class="container-fluid">
    <div class="col-lg-12 col-md-12 col-sm-12 my-3">
        <div class="form-group col-lg-3 col-md-6 col-sm-12 float-left">
            <label class="col-lg-3 c float-left">ФИО</label>
            <input class="form-control col-lg-9 float-left" type="text" v-model="name">
        </div>
        <div class="form-group col-lg-3 col-md-6 col-sm-12 float-left">            
            <select class="form-control float-left col-lg-12" v-model="role_id">
                <option v-for="{id,name} in role_list" :value="id">{{name}}</option>
            </select>
        </div>
        <button class="btn btn-success float-left" v-on:click="search(0)">Найти</button>
        <button class="btn btn-primary add_users float-right" data-toggle="modal" data-target="#add_user_modal" ref="add_button">Добавить</button>
        <div class="clearfix"></div>
        <div>Всего записей : {{total_rows}}</div>
    </div>
    <div class="clearfix"></div>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    <table class="table table-bordered" v-if="users.length>0">
        <thead>
        <tr>
            <th>#</th>
            <th>ФИО</th>
            <th>Роль</th>
            <th>Логин</th>
            <th>Пароль</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>

        <tr class="user_row" v-for="(user, index) in users">
            <td>{{user.id}}</td>                     
            <td>{{user.name}}</td>
            <td>{{user.role_name}} ({{user.role_id}})</td>
            <td>{{user.login}}</td>            
            <td>{{user.password}}</td>
            <td>
                <span class="fa fa-pencil edit-user" v-on:click="edit_row(index)"></span>
                <span class="fa fa-remove edit-user float-right" v-on:click="delete_row(index,user.id)"></span>
            </td>
        </tr>
        </tbody>
    </table>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    <?php $this->load->view("user/user_add");?>
</div>

<script src="/resources/js/components.js"></script>
<script type="text/javascript">
    el = new Vue({
        el: "#vue-container",
        data: {
            name: '',            
            role_id: '',           
            page_number: 0,
            current_page: 1,
            total_rows: 0,
            per_page: 25,
            pages: [],
            fio_search: '',
            error: "",
            new_row : { edit_id: '0',  },
            users: [],
            role_list: []
        },
        methods: {
            get_role_list:function(){
                axios.post("/user/get_role_list/", {}).then(function (result) {
                    el._data.role_list = result.data.contents;
                }).catch(function (e) {
                    console.log(e)
                })
            },
            add_row: function (new_row) {                
                var errors = this.check_form(new_row)
                if (errors.length > 0) {
                    this.error = errors.join(" ")
                    return;
                }
                
                var url = "/user/add_new_user";
                if (this.new_row.edit_id != 0) {
                    url = "/user/edit_user/" + this.new_row.edit_id;
                }

                axios.post(url, new_row).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            alert(result.data.message);
                            document.querySelector(".close_dialog").click();
                            el.search(1);
                            el.$data.new_row = {};
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
            check_form: function (new_row) {
                var errors = [];
                if (!new_row.name) {
                    errors.push("Укажите ФИО!");
                }
                if (!new_row.login) {
                    errors.push("Укажите логин!");
                }                
                if (!new_row.password) {
                    errors.push("Укажите пароль!");
                }
                if (!new_row.role_id) {
                    errors.push("Укажите роль!");
                }
                return errors;
            },
            edit_row: function (index) {
                this.new_row = el.$data.users[index]
                this.new_row.edit_id = this.new_row.id
                this.$refs.add_button.click()
            },
            delete_row: function (index, id) {
                if(window.confirm("Вы подтверждаете удаление?")){
                    axios.post("/user/delete/" + id, {
                        id: id,
                    }).then(function (result) {
                        switch (result.data.status) {
                            case 200:
                                el._data.users.splice(index, 1)
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
            search: function (page) {
                axios.post("/user/search/"+page, {                    
                    name: this._data.name,
                    role_id: el._data.role_id,
                }).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.users.splice()
                            el._data.pages.splice(0);
                            el._data.users = result.data.content;
                            el._data.total_rows = result.data.total_rows;
                            el._data.total_pages = Math.ceil(el._data.total_rows / el._data.per_page);
                            if(el._data.total_rows > 25){
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
        },
        mounted(){
            setTimeout(function(){
                el.search(0)
                el.get_role_list()
            },100)
        }
    })
</script>
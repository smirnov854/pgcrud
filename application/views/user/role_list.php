<div id="vue-container" class="container-fluid">
    <div class="col-lg-12 col-md-12 col-sm-12 my-3">       
        <button class="btn btn-success float-left" v-on:click="search(0)">Найти</button>
        <button class="btn btn-primary add_users float-right" data-toggle="modal" data-target="#add_role_modal" ref="add_button">Добавить</button>
        <div class="clearfix"></div>
        <div>Всего записей : {{total_rows}}</div>
    </div>
    <div class="clearfix"></div>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    <table class="table table-bordered" v-if="role_list.length>0">
        <thead>
        <tr>
            <th>#</th>            
            <th>Роль</th>
            <th>Список разрешений</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <tr class="user_row" v-for="(role, index) in role_list">
            <td>{{role.id}}</td>
            <td>{{role.name}}</td>
            <td>{{role.name}}</td>            
            <td>
                <span class="fa fa-pencil edit-user" v-on:click="edit_row(index)"></span>
                <span class="fa fa-remove edit-user float-right" v-on:click="delete_row(index,role.id)"></span>
            </td>
        </tr>
        </tbody>
    </table>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    <?php $this->load->view("user/role_add");?>
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
            role_list: [],            
        },
        methods: {           
            add_row: function (new_row) {
                var errors = this.check_form(new_row)
                if (errors.length > 0) {
                    this.error = errors.join(" ")
                    return;
                }
                
                var url = "/role/add_new_role";
                if (this.new_row.edit_id != 0) {
                    url = "/role/edit_role/" + this.new_row.edit_id;
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
                return errors;
            },
            edit_row: function (index) {
                this.new_row = el.$data.role_list[index]
                this.new_row.edit_id = this.new_row.id
                this.$refs.add_button.click()
            },
            delete_row: function (index, id) {
                if(window.confirm("Вы подтверждаете удаление?")){
                    axios.post("/role/delete/" + id, {
                        id: id,
                    }).then(function (result) {
                        switch (result.data.status) {
                            case 200:
                                el._data.role_list.splice(index, 1)
                                break;
                            case 300:
                                alert(result.message)
                                break;
                        }
                    }).catch(function (e) {
                        console.log(e)
                    })
                }
            },
            search: function (page) {
                axios.post("/role/search/"+page, {
                    name: this._data.name,
                    role_id: el._data.role_id,
                }).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.role_list.splice()
                            el._data.pages.splice(0);
                            el._data.role_list = result.data.content;
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
                    }
                }).catch(function (e) {
                    console.log(e)
                })
            },
        },
        mounted(){
            setTimeout(function(){
                el.search(0)               
            },100)
        }
    })
</script>
<div id="vue-container" class="container-fluid">
    <div class="col-lg-12 col-md-12 col-sm-12 my-3">
        <div class="form-group col-lg-3 col-md-6 col-sm-12 float-left">
            <label class="col-lg-4 c float-left">Наименование</label>
            <input class="form-control col-lg-8 float-left" type="text" v-model="name">
        </div>
        <button class="btn btn-success float-left" v-on:click="search(0)">Найти</button>
        <button class="btn btn-primary add_flat float-right" data-toggle="modal" data-target="#add_flat" ref="add_button" v-on:click="">Добавить</button>
        <div class="clearfix"></div>
        <div>Всего записей : {{total_rows}}</div>
    </div>
    <?php $this->load->view("/flat/flat_add"); ?>
    <div class="clearfix"></div>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
    <table class="table table-bordered" v-if="flats.length>0">
        <thead>
        <tr>
            <th>#</th>
            <th>Наименование</th>
            <!--
            <th>stored</th>
            <th>mac</th>
            <th>ip</th>
            <th>sip_login</th>
            <th>sip_password</th>
            <th>disabled</th>
            <th>key</th>          
            -->
            <th></th>
        </tr>
        </thead>
        <tbody>

        <tr class="user_row" v-for="(flat, index) in flats">
            <td>{{flat.id}}</td>
            <td>{{flat.name}}</td>
            <!--
            <td>{{flat.stored}}</td>
            <td>{{flat.mac}}</td>
            <td>{{flat.ip}}</td>
            <td>{{flat.sip_login}}</td>
            <td>{{flat.sip_password}}</td>
            <td>{{flat.disabled}}</td>
            <td>{{flat.key}}</td>
            -->
            <td>
                <span class="fa fa-pencil edit-user" v-on:click="edit_row(index)"></span>
                <span class="fa fa-remove delete float-right" v-on:click="delete_row(index,flat.id)"></span>
            </td>
        </tr>
        </tbody>
    </table>
    <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>
</div>

<script src="/resources/js/components.js"></script>
<script type="text/javascript">
    el = new Vue({
        el: "#vue-container",
        data: {
            name: '',
            page_number: 0,
            current_page: 1,
            total_rows: 0,
            per_page: 25,
            pages: [],
            fio_search: '',
            error: "",
            new_row: {
                edit_id: 0,
                name: ''
            },
            flats: [],
        },
        methods: {
            add_new_row: function (new_row) {
                var errors = this.check_form(new_row)
                if (errors.length > 0) {
                    this.error = errors.join(" ")
                    return;
                }
                var url = "/flat/add_new_flat";
                if (this.new_row.edit_id != 0) {
                    url = "/flat/edit_flat/" + this.new_row.edit_id;
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
                    }
                }).catch(function (e) {
                    console.log(e)
                })
            },
            check_form: function (new_row) {
                var errors = [];
                if (!new_row.name) {
                    errors.push("Укажите наименование!");
                }
                return errors;
            },
            edit_row: function (index) {
                this.new_row = el.$data.flats[index]
                this.new_row.edit_id = this.new_row.id
                this.$refs.add_button.click()
            },
            delete_row: function (index, id) {
                if (window.confirm("Вы подтверждаете удаление?")) {
                    axios.post("/flat/delete/" + id, {
                        id: id,
                    }).then(function (result) {
                        switch (result.data.status) {
                            case 200:
                                el._data.flats.splice(index, 1)
                                break;
                            case 300:
                                alert(result.data.message)
                                break;
                        }
                    }).catch(function (e) {
                        console.log(e)
                    })
                }
            },
            search: function (page) {
                axios.post("/flat/search/" + page, {
                    name: this._data.name,
                }).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.flats.splice()
                            el._data.pages.splice(0);
                            el._data.flats = result.data.content;
                            el._data.total_rows = result.data.total_rows;
                            if (el._data.total_rows > 25) {
                                el._data.total_pages = Math.ceil(el._data.total_rows / el._data.per_page);                               
                                
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
                                    if (el._data.total_pages !== page) {
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
        mounted() {
            setTimeout(function () {              
                el.search(0)
            }, 100)
        }

    })
</script>
<div id="warning_row_controller" class="justify-content-center mx-4 my-4">
    <div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
            <label class="col-lg-4 c float-left">ID квартиры</label>
            <input class="form-control col-lg-8 float-left" type="number" v-model="flat_id">
        </div>
        <div class="form-group col-lg-4 col-md-6 col-sm-12 float-left">
            <label class="col-lg-4 c float-left">ID жильца</label>
            <input class="form-control col-lg-8 float-left" type="text" v-model="person_id">
        </div>
        <div class="form-group col-lg-4 float-left">
            <button class="btn btn-success search_button" v-on:click="search(0)">Найти</button>
            <button class="btn btn-primary add_warning" data-toggle="modal" data-target="#add_warning" ref="add_button">Добавить</button>
        </div>
        <div class="clearfix"></div>
        <div>Всего записей : {{total_rows}}</div>
    </div>
    <?php $this->load->view("/warning/warning_add");?>
    <div>
        <paginator v-bind:total_pages="pages" v-bind:current_page="current_page"></paginator>

        <table class="table table-bordered" v-if="warning_list.length>0">
            <thead>
            <tr>
                <th>#</th>
                <th>Время</th>
                <th>Квартира</th>
                <th>Жилец</th>
                <th>Телефон</th>
                <th>Сообщение</th>
                <th>Отправлено</th>
                <th>Доставлено</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(warning_row,index) in warning_list">
                <td>{{warning_row.id}}</td>
                <td>{{warning_row.stamp}}</td>
                <td>{{warning_row.flat_name}} ({{warning_row.flat_id}})</td>
                <td>{{warning_row.person_name}}</td>
                <td>{{warning_row.phone}}</td>
                <td>{{warning_row.message}}</td>
                <td>{{warning_row.pickup}}</td>
                <td>{{warning_row.delivery}}</td>
                <td>
                    <span class="fa fa-pencil edit-user" v-on:click="edit_row(index)"></span>
                    <span class="fa fa-remove delete float-right" v-on:click="delete_row(index,warning_row.id)"></span>
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
        el: "#warning_row_controller",        
        data: {           
            current_page: 0,
            flat_id:0,
            person_id:0,
            total_pages: 0,
            total_rows: 0,
            per_page: 25,
            pages: [],
            date_from: '',
            date_to: '',
            error: "",
            warning_list: [],
            new_row: {edit_id:0}
        },
        methods: {
            search: function (page = 0) {
                this.current_page = page
                axios.post("/warnings/search/" + page, {
                    person_id: el._data.person_id,
                    flat_id : el._data.flat_id
                }).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.warning_list.splice()
                            el._data.pages.splice(0);
                            el._data.warning_list = result.data.content;
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
                    }
                }).catch(function (e) {
                    console.log(e)
                })
            },
            delete_row: function (index, id) {
                if (window.confirm("Вы подтверждаете удаление?")) {
                    axios.post("/warnings/delete/" + id, {
                        id: id,
                    }).then(function (result) {
                        switch (result.data.status) {
                            case 200:
                                el._data.warning_list.splice(index, 1)
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
            edit_row: function (index) {
                this.new_row = el.$data.warning_list[index]
                this.new_row.edit_id = this.new_row.id
                this.$refs.add_button.click()
            },

            add_new_row: function (new_row) {
                var errors = this.check_form(new_row)
                if (errors.length > 0) {
                    this.error = errors.join(" ")
                    return;
                }
                var url = "/warnings/add_new_warn";
                if (this.new_row.edit_id != 0) {
                    url = "/warnings/edit_warn/" + this.new_row.edit_id;
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
                if (!new_row.flat_id) {
                    errors.push("Укажите ID квартиры!");
                }
                if (!new_row.person_id) {
                    errors.push("Укажите ID жильца!");
                }
                if (!new_row.phone) {
                    errors.push("Укажите телефон!");
                }
                if (!new_row.message) {
                    errors.push("Укажите текст сообщения!");
                }
                return errors;
            },
            
        },
        mounted() {
            setTimeout(function () {
                el.search(0)
            }, 100)
        }
    })
</script>
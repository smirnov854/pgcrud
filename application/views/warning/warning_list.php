<div id="warning_row_controller" class="justify-content-center mx-4 my-4">
    <div>
        <div class="form-group col-lg-4 float-left"></div>
        <div class="form-group col-lg-4 float-left">
            <button class="btn btn-success search_button" v-on:click="search(0)">Найти</button>
            <button class="btn btn-primary add_job" data-toggle="modal" data-target="#add_job">Добавить</button>
        </div>
        <div class="clearfix"></div>
        <div v-if="total_rows">Всего записей : {{total_rows}}</div>
    </div>

    <div>
        <paginator v-bind:pages="pages" v-bind:current_page="current_page"></paginator>

        <table class="table table-bordered" v-if="warning_list.length>0">
            <thead>
            <tr>
                <th>#</th>
                <th>stamp</th>
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

        <paginator v-bind:pages="pages" v-bind:current_page="current_page"></paginator>
    </div>
</div>

<script src="/resources/js/components.js"></script>
<script src="https://unpkg.com/vue-pure-lightbox/dist/VuePureLightbox.umd.min.js"></script>
<script type="text/javascript">
    el = new Vue({
        el: "#warning_row_controller",
        components: {
            'vue-pure-lightbox': window.VuePureLightbox,
        },
        data: {
            options: {
                // https://momentjs.com/docs/#/displaying/
                format: 'DD.MM.YYYY',
                useCurrent: false,
                showClear: true,
                showClose: true,
            },
            current_page: 0,
            total_pages: 0,
            total_rows: 0,
            per_page: 25,
            pages: [],
            date_from: '',
            date_to: '',
            error: "",
            warning_list: []
        },
        methods: {
            search: function (page = 0) {
                this.current_page = page
                axios.post("/warnings/search/" + page, {}).then(function (result) {
                    switch (result.data.status) {
                        case 200:
                            el._data.warning_list.splice()
                            el._data.pages.splice(0);
                            el._data.warning_list = result.data.content;
                            el._data.total_rows = result.data.total_rows;
                            el._data.total_pages = Math.ceil(el._data.total_rows / el._data.per_page);

                            if (el._data.total_rows > 25) {
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
                if (!new_row.name) {
                    errors.push("Укажите наименование!");
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
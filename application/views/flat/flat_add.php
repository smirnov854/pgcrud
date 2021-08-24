<div id="add_flat" class="modal  fade" role="dialog" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span v-if="new_row.edit_id>0">Редактирование</span>
                    <span v-if="new_row.edit_id==0">Добавление</span> квартиры
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" v-if="error">{{error}}</div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">
                    <label class="col-lg-2 col-md-2 col-sm-2 text-right">Наименование</label>
                    <input type="text" class="form-control" v-model="new_row.name">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger close_dialog float-left" data-dismiss="modal" id="close_dialog">Закрыть</button>
                <button class="btn btn-success float-right" id="confirm_add_flat" v-on:click="add_new_row(new_row)">Добавить</button>
            </div>
        </div>
    </div>
</div>
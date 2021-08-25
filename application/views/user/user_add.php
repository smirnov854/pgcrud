<div id="add_user_modal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span v-if="new_row.edit_id>0">Редактирование</span>
                    <span v-if="new_row.edit_id==0">Добавление</span> пользователя</div>
            </div>
            <div class="modal-body">
                <input type="hidden" v-model="new_row.edit_id">
                <div class="alert alert-danger" v-if="error">{{error}}</div>
                <div class="form-group">
                    <label>ФИО</label>
                    <input class="form-control" type="text" v-model="new_row.name" placeholder="ФИО" required>
                </div>
                <div class="form-group">
                    <label>Логин</label>
                    <input class="form-control" type="text" v-model="new_row.login" placeholder="Логин" required>
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">
                    <label>Роль</label>
                    <select class="form-control float-left col-lg-12" v-model="new_row.role_id">
                        <option v-for="{id,name} in role_list" :value="id">{{name}}</option>
                    </select>
                </div>                
                <div class="form-group">
                    <label>Пароль</label>
                    <input class="form-control" type="text" v-model="new_row.password" placeholder="Пароль" required>
                </div>              
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger close_dialog" data-dismiss="modal">Закрыть</button>
                <button class="btn btn-success" id="confirm_add_user" v-on:click="add_row(new_row)">
                    <span v-if="new_row.edit_id>0">Редактировать</span>
                    <span v-if="new_row.edit_id==0">Добавить</span></div>            
                </button>
            </div>
        </div>
    </div>
</div>
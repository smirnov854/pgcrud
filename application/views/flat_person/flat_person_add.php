<div id="add_user_modal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span v-if="new_user.edit_id>0">Редактирование</span>
                    <span v-if="new_user.edit_id==0">Добавление</span> пользователя</div>
            </div>
            <div class="modal-body">
                <input type="hidden" v-model="new_user.edit_id">
                <div class="alert alert-danger" v-if="error">{{error}}</div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input class="form-control" type="text" v-model="new_user.phone" placeholder="Телефон" required>
                </div>
                <div class="form-group">
                    <label>ID квартиры</label>
                    <input class="form-control" type="text" v-model="new_user.flat_id" placeholder="ID квартиры" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="text" v-model="new_user.email" placeholder="email" required>
                </div>
                <div class="form-group">
                    <label>ФИО</label>
                    <input class="form-control" type="text" v-model="new_user.name" placeholder="Фамилия Имя Отчество" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input class="form-control" type="text" v-model="new_user.password" placeholder="Пароль" required>
                </div>              
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger close_dialog" data-dismiss="modal">Закрыть</button>
                <button class="btn btn-success" id="confirm_add_user" v-on:click="add_row(new_user)">
                    <span v-if="new_user.edit_id>0">Редактировать</span>
                    <span v-if="new_user.edit_id==0">Добавить</span></div>            
                </button>
            </div>
        </div>
    </div>
</div>
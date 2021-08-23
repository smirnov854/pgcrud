<div id="add_flat_meter" class="modal  fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span v-if="new_row.edit_id>0">Редактирование</span>
                    <span v-if="new_row.edit_id==0">Добавление</span> датчика
                    <span v-if="new_row.edit_id>0">ID {{new_row.edit_id}}</span> 
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" v-if="error">{{error}}</div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">                    
                    <input type="text" class="form-control" v-model="new_row.name" placeholder="Наименование">
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">                    
                    <input type="text" class="form-control" v-model="new_row.flat_id" placeholder="ID квартиры">
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">
                    <input type="text" class="form-control" v-model="new_row.acc_id" placeholder="ID счетчика">
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">                    
                    <input type="text" class="form-control" v-model="new_row.port" placeholder="Порт">
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">                    
                    <input type="text" class="form-control" v-model="new_row.value" placeholder="Значение">
                </div>
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">                    
                    <select class="form-control float-left col-lg-12" v-model="new_row.meter_type_id">
                        <option v-for="{id,name} in meter_type_list" :value="id">{{name}}</option>
                    </select>
                </div>                
                <div class="form-row col-lg-12 col-md-12 col-sm-12 float-left my-2">
                    <input type="text" class="form-control" v-model="new_row.tube" placeholder="Номер стояка">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger close_dialog float-left" data-dismiss="modal" id="close_dialog" v-on:click="">Закрыть</button>
                <button class="btn btn-success float-right" id="confirm_add_flat" v-on:click="add_new_row(new_row)">Добавить</button>
            </div>
        </div>
    </div>
</div>
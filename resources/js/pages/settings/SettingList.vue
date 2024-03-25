<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import SettingListItem from './SettingListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';


const toastr = useToastr();
const settings = ref({'data': []});
const editing = ref(false);
const form = ref(null);
const formValues = ref();

const settingIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedSettings = ref([]);

const toggleSelection = (setting) => {
    const index = selectedSettings.value.indexOf(setting.id);
    if (index === -1) {
        selectedSettings.value.push(setting.id);
    } else {
        selectedSettings.value.splice(index, 1);
    }
};

const getSettings = (page = 1) => {
    axios.get(`/api/settings?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            settings.value = response.data;
            console.log("getSettings, response.data: ", response.data);
            selectedSettings.value = [];
            selectAll.value = false;
        })
}

const createSettingSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const editSettingSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const updateSetting = (values, { setErrors }) => {
    axios.put('/api/settings/' + values.id, values)
        .then((response) => {
            const index = settings.value.findIndex(setting => setting.id === response.data.id);
            settings.value.data[index] = response.data;
            $('#settingFormModal').modal('hide');
            toastr.success('Setting updated successfully!');
        }).catch((error) => {
        console.log("updateSetting-error: ", error);
        setErrors(error.response.data.errors);
    });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateSetting(values, actions);
    } else {
        createSetting(values, actions);
    }
}

const addSetting = () => {
    editing.value = false;
    form.value.resetForm();
    $('#settingFormModal').modal('show');
}

const editSetting = (setting) => {
    editing.value = true;
    form.value.resetForm();
    $('#settingFormModal').modal('show');

    // formValues.value = {
    //     id: setting.id,
    //     name: setting.name,
    //     email: setting.email,
    // };

    form.value.setValues({
        id: setting.id,
        name: setting.name,
        email: setting.email,
    });
};

const confirmSettingDeletion = (setting) => {
    settingIdBeingDeleted.value = setting.id;
    $('#deleteSettingModal').modal('show');
};

const deleteSetting = () => {
    axios.delete(`/api/settings/${settingIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteSettingModal').modal('hide');
            toastr.success('Setting deleted successfully!');
            settings.value.data = settings.value.data.filter(setting => setting.id !== settingIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/settings', {
        data: {
            ids: selectedSettings.value
        }
    })
        .then(response => {
            settings.value.data = settings.value.data.filter(setting => !selectedSettings.value.includes(setting.id));
            selectedSettings.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllSettings = () => {
    if (selectAll.value) {
        selectedSettings.value = settings.value.data.map(setting => setting.id);
    } else {
        selectedSettings.value = [];
    }
}

watch(searchQuery, debounce(() => {
    getSettings();
}, 300));

onMounted(() => {
    getSettings();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link to="/admin/settings/create">
                        <button type="button" class="mb-2 btn btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Add New Setting
                        </button>
                    </router-link>
                    <div v-if="selectedSettings.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedSettings.length }} settings</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllSettings" /></th>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Group</th>
                            <th>Main Group</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="settings.data.length > 0">
                        <SettingListItem v-for="(setting, index) in settings.data"
                                      key="setting.id"
                                      :setting=setting :index=index
                                      @edit-setting="editSetting"
                                      @confirm-setting-deletion="confirmSettingDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll"
                                      :selectedSettings="selectedSettings"
                        />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="6" class="text-center">No results found...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="settings" @pagination-change-page="getSettings" align="right" />

        </div>
    </div>

    <div class="modal fade" id="deleteSettingModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Setting</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this setting ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteSetting" type="button" class="btn btn-primary">Delete Setting</button>
                </div>
            </div>
        </div>
    </div>
</template>

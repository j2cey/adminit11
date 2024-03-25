<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import SettingGroupList from "./SettingGroupList.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";
import SettingListItem from './SettingListItem.vue';
import { useSettingStore } from '../../stores/SettingStore.js';


const settingStore = useSettingStore();
const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    name: '',
    value: '',
    type: '',
    description: '',
});

const editMode = ref(false);

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateSetting(values, actions);
    } else {
        createSetting(values, actions);
    }
};

const createSetting = (values, actions) => {
    axios.post('/api/settings', form)
        .then((response) => {
            // router.push('/admin/settings');
            setting.value = response.data;
            settingid.value = response.data.id;
            editMode.value = true;

            updateGroupSettings(response.data);
            settingStore.getSettings();
            toastr.success('Setting created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

const updateSetting = (values, actions) => {
    axios.put(`/api/settings/${route.params.id}`, form)
        .then((response) => {

            setting.value = response.data.setting;
            updateGroupSettings(response.data.setting);
            settingStore.getSettings();
            toastr.success('Setting updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

const updateGroupSettings = (setting) => {
    const index = settings.value.data.findIndex(s => s.id === setting.id);;

    if (index === -1) {
        settings.value.data.push(setting);
    } else {
        settings.value.data.splice(index, 1, setting);
    }
};

const setting = ref({});
const settingid = ref(null);
const settinggroupid = ref(null);
const getSetting = () => {
    axios.get(`/api/settings/${route.params.id}/edit`)
        .then(({ data }) => {
            form.name = data.name;
            form.value = data.value;
            form.type = data.type;
            form.description = data.description;

            setting.value = data;
            settinggroupid.value = data.group.id;
            console.log('getSetting, data:', data);
            getSettingGroup();
        })
};

const getSettingGroup = (page = 1) => {
    axios.get(`/api/settinggroups?page=${page}`, {
        params: {
            query: searchQuery.value,
            groupid: settinggroupid.value
        }
    })
        .then((response) => {
            console.log('getSettingGroup, response.data:', response.data);
            settings.value = response.data;

            selectedSettings.value = [];
            selectAllSettings.value = false;
            selectAll.value = false;
        })
};

const searchQuery = ref(null);
const settings = ref({'data': []});
const selectedSettings = ref([]);
const selectAll = ref(false);

const toggleSelection = (setting, target_ig) => {
    const index = selectedSettings.value.indexOf(setting.id);

    if (index === -1) {
        selectedSettings.value.push(setting.id);
    } else {
        selectedSettings.value.splice(index, 1);
    }
};
const selectAllSettings = () => {
    if (selectAll.value) {
        selectedSettings.value = settings.value.data.map(setting => setting.id);
    } else {
        selectedSettings.value = [];
    }
};

const confirmSettingDeletion = (setting) => {
    settingIdBeingDeleted.value = setting.id;
    $('#deleteSettingModal').modal('show');
};

const initComponent = () => {
    if (route.name === 'admin.settings.edit') {
        settingid.value = route.params.id;
        editMode.value = true;
        getSetting();
    }
    console.log('initComponent, route.params.id', route.params.id);
    console.log('initComponent, setting.value', setting.value);
}

watch(route,() => {
    initComponent();
});

watch(searchQuery, debounce(() => {
    getSettingGroup();
}, 300));

onMounted(() => {
    initComponent();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Create</span>
                        Setting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/settings">Settings</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row" :key="$route.path">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Name</label>
                                            <input readonly v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" id="name" placeholder="Enter Name">
                                            <span class="invalid-feedback">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Value</label>
                                            <input v-model="form.value" type="text" class="form-control" :class="{ 'is-invalid': errors.value }" id="guard_name" placeholder="Enter Value">
                                            <span class="invalid-feedback">{{ errors.value }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Type</label>
                                            <input readonly v-model="form.type" type="text" class="form-control" :class="{ 'is-invalid': errors.type }" id="guard_name" placeholder="Enter Type">
                                            <span class="invalid-feedback">{{ errors.type }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Description</label>
                                            <input v-model="form.description" type="text" class="form-control" :class="{ 'is-invalid': errors.description }" id="guard_name" placeholder="Enter Description">
                                            <span class="invalid-feedback">{{ errors.description }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">Submit</button>
                                    <router-link to="/admin/settings">
                                        <button type="submit" class="btn btn-sm btn-default m-2">Back</button>
                                    </router-link>
                                </div>
                            </Form>
                        </div>

                    </div>
                </div>
            </div>

            <div v-if="editMode" class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Setting Group</h3>
                            <div class="card-tools">

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <div v-if="selectedSettings.length > 0">
                                            <span class="ml-2">Selected {{ selectedSettings.length }} settings</span>
                                        </div>
                                    </div>

                                    <div class="d-flex">
                                        <div class="input-group input-group-sm">
                                            <input style="height: 23pt" type="text" v-model="searchQuery" class="form-control text-xs" placeholder="Search..." />
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-xs btn-default">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                </div>
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
                                <Bootstrap4Pagination :data="settings" @pagination-change-page="getSettingGroup" align="right" />
                            </div>
                        </div>
                        <div class="card-footer">

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.has-search .form-control {
    padding-left: 2.375rem;
}

.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
}
</style>

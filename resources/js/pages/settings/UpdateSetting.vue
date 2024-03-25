<script setup>
import { onMounted, ref } from 'vue';
import { useToastr } from '@/toastr';

const settings = ref([]);
const toastr = useToastr()

const errors = ref();
const updateSettings = () => {
    errors.value = '';
    axios.post('/api/settings', settings.value)
        .then((response) => {
            toastr.success('Settings updated successfully!');
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        });
};

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
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">General Setting</h3>
                        </div>

                        <form @submit.prevent="updateSettings()">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="appName">App Display Name</label>
                                    <input v-model="settings.app.name" type="text" class="form-control" id="appName"
                                           placeholder="Enter app display name">
                                    <span class="text-danger text-xs" v-if="errors && errors.app.name">{{ errors.app_name[0] }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="dateFormat">Date Format</label>
                                    <select v-model="settings.date.format" class="form-control" id="dateFormat">
                                        <option value="m/d/Y">MM/DD/YYYY</option>
                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                        <option value="F j, Y">Month DD, YYYY</option>
                                        <option value="j F Y">DD Month YYYY</option>
                                    </select>
                                    <span class="text-danger text-sm" v-if="errors && errors.date_format">{{ errors.date_format[0] }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="paginationLimit">Pagination Limit</label>
                                    <input v-model="settings.pagination.limit" type="text" class="form-control" id="paginationLimit"
                                           placeholder="Enter pagination limit">
                                    <span class="text-danger text-sm" v-if="errors && errors.pagination_limit">{{ errors.pagination_limit[0] }}</span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i>Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import {ref, onMounted, reactive, watch} from "vue";
import {Form, Field, useResetForm } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr.js';
import RoleListItem from './RoleListItem.vue';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const roles = ref({'data': []});
const editing = ref(false);
const form = ref(null);
const formValues = ref();

const roleIdBeingDeleted = ref(null);

const searchQuery = ref(null);
const selectedRoles = ref([]);

const toggleSelection = (role) => {
    const index = selectedRoles.value.indexOf(role.id);
    if (index === -1) {
        selectedRoles.value.push(role.id);
    } else {
        selectedRoles.value.splice(index, 1);
    }
};

const getRoles = (page = 1) => {
    axios.get(`/api/roles?page=${page}`, {
        params: {
            query: searchQuery.value
        }
    })
        .then((response) => {
            roles.value = response.data;
            selectedRoles.value = [];
            selectAll.value = false;
        })
}

const createRoleSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const editRoleSchema = yup.object({
    name: yup.string().required(),
    guard_name: yup.string().required(),
});

const updateRole = (values, { setErrors }) => {
    axios.put('/api/roles/' + values.id, values)
        .then((response) => {
            const index = roles.value.findIndex(role => role.id === response.data.id);
            roles.value.data[index] = response.data;
            $('#roleFormModal').modal('hide');
            toastr.success('Role updated successfully!');
        }).catch((error) => {
        console.log("updateRole-error: ", error);
        setErrors(error.response.data.errors);
    });
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateRole(values, actions);
    } else {
        createRole(values, actions);
    }
}

const addRole = () => {
    editing.value = false;
    form.value.resetForm();
    $('#roleFormModal').modal('show');
}

const editRole = (role) => {
    editing.value = true;
    form.value.resetForm();
    $('#roleFormModal').modal('show');

    // formValues.value = {
    //     id: role.id,
    //     name: role.name,
    //     email: role.email,
    // };

    form.value.setValues({
        id: role.id,
        name: role.name,
        email: role.email,
    });
};

const confirmRoleDeletion = (role) => {
    roleIdBeingDeleted.value = role.id;
    $('#deleteRoleModal').modal('show');
};

const deleteRole = () => {
    axios.delete(`/api/roles/${roleIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteRoleModal').modal('hide');
            toastr.success('Role deleted successfully!');
            roles.value.data = roles.value.data.filter(role => role.id !== roleIdBeingDeleted.value);
        });
};

const bulkDelete = () => {
    axios.delete('/api/roles', {
        data: {
            ids: selectedRoles.value
        }
    })
        .then(response => {
            roles.value.data = roles.value.data.filter(role => !selectedRoles.value.includes(role.id));
            selectedRoles.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        });
};

const selectAll = ref(false);
const selectAllRoles = () => {
    if (selectAll.value) {
        selectedRoles.value = roles.value.data.map(role => role.id);
    } else {
        selectedRoles.value = [];
    }
}

watch(searchQuery, debounce(() => {
    getRoles();
}, 300));

onMounted(() => {
    getRoles();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Roles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <router-link to="/admin/roles/create">
                        <button type="button" class="mb-2 btn btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Add New Role
                        </button>
                    </router-link>
                    <div v-if="selectedRoles.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedRoles.length }} roles</span>
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
                            <th><input type="checkbox" v-model="selectAll" @change="selectAllRoles" /></th>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered Date</th>
                            <th>Permissions</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody v-if="roles.data.length > 0">
                        <RoleListItem v-for="(role, index) in roles.data"
                                      key="role.id"
                                      :role=role :index=index
                                      @edit-role="editRole"
                                      @confirm-role-deletion="confirmRoleDeletion"
                                      @toggle-selection="toggleSelection"
                                      :selectAll="selectAll" />
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="6" class="text-center">No results found...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="roles" @pagination-change-page="getRoles" align="right" />

        </div>
    </div>

    <div class="modal fade" id="deleteRoleModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete Role</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this role ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteRole" type="button" class="btn btn-primary">Delete Role</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import {reactive, onMounted, ref, watch, computed} from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import { Form } from 'vee-validate';
import {debounce} from "lodash";
import PermissionListItem from "@/pages/roles/PermissionListItem.vue";
import {Bootstrap4Pagination} from "laravel-vue-pagination";

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    name: '',
    guard_name: 'web',
    created_at: '',
});

const editMode = ref(false);
const rolepermissions = ref({});
const affectedpermissions = ref({});

const handleSubmit = (values, actions) => {
    if (editMode.value) {
        updateRole(values, actions);
    } else {
        createRole(values, actions);
    }
};

const createRole = (values, actions) => {
    axios.post('/api/roles', form)
        .then((response) => {
            // router.push('/admin/roles');
            role.value = response.data;
            roleid.value = response.data.id;
            editMode.value = true;
            toastr.success('Role created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

const updateRole = (values, actions) => {
    axios.put(`/api/roles/${route.params.id}`, form)
        .then((response) => {
            toastr.success('Role updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
};

const role = ref({})
const roleid = ref(null)
const getRole = () => {
    axios.get(`/api/roles/${route.params.id}/edit`)
        .then(({ data }) => {
            form.name = data.name;
            form.guard_name = data.guard_name;

            role.value = data;
            rolepermissions.value = data.permissions;
        })
};

const searchQuery = ref(null);
const permStatus = ref('all');
const permissions = ref({'data': []});
const permissionscount = ref(0);
const selectedPermissions = ref([]);
const getPermissionsCount = () => {
    axios.get('/api/permissions/count')
        .then((response) => {
            permissionscount.value = response.data;
        })
}

const getPermissionsByStatus = (status) => {
    permStatus.value = status;
    getPermissions();
}
const getPermissions = (page = 1) => {
    axios.get(`/api/permissions?page=${page}`, {
        params: {
            query: searchQuery.value,
            roleid: roleid.value,
            permstatus: permStatus.value
        }
    })
        .then((response) => {
            permissions.value = response.data;
            permissionscount.value = response.data.total;

            selectedPermissions.value = [];
            selectAllPermissions.value = false;
            selectAll.value = false;
        })
}

const selectAll = ref(false);

const toggleSelection = (permission, target_ig) => {
    const index = selectedPermissions.value.indexOf(permission.id);

    if (index === -1) {
        selectedPermissions.value.push(permission.id);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
};
const selectAllPermissions = () => {
    if (selectAll.value) {
        selectedPermissions.value = permissions.value.data.map(permission => permission.id);
    } else {
        selectedPermissions.value = [];
    }
}

const bulkAssign = () => {
    axios.patch(`/api/roles/${role.value.id}/assign-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then(response => {
            // let permissions_not_in = response.data.permissions.filter(perm => !rolepermissions.value.includes(perm.id));
            // let permissions_not_in = response.data.permissions.filter(o => !rolepermissions.value.some(i => i.id === o.id));
            affectedpermissions.value = response.data.permissions;
            rolepermissions.value.push(...affectedpermissions.value);

            updatePermissionCurrentList('out_role', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const bulkRevoke = () => {
    axios.patch(`/api/roles/${role.value.id}/revoke-permissions`, {
        permissionsids: selectedPermissions.value
    })
        .then((response) => {
            affectedpermissions.value = response.data.permissions;
            rolepermissions.value = rolepermissions.value.filter(rp => !affectedpermissions.value.some(ap => ap.id === rp.id));
            updatePermissionCurrentList('in_role', affectedpermissions.value);

            selectedPermissions.value = [];
            selectAll.value = false;

            toastr.success(response.data.message);
        });
};

const permissionAffectationChanged = (permission) => {
    const index = rolepermissions.value.findIndex(p => p.id === permission.id);
    if (index === -1) {
        // add to rolepermissions, in_role case
        rolepermissions.value.push(permission.id);
        // remove from permissions if current permission status is out_role
        // removeOnePermissionCurrentList('out_role', permission);
        updatePermissionCurrentList('out_role', [permission]);
    } else {
        // remove from rolepermissions, out_role case
        rolepermissions.value.splice(index, 1);
        // remove from permissions if current permission status is in_role
        // removeOnePermissionCurrentList('in_role', permission);
        updatePermissionCurrentList('in_role', [permission]);
    }

    toggleIsInRole([permission]);
};

const updatePermissionCurrentList = (permstatus, affectedpermissions) => {
    // remove permissions to current list i any
    if ( permStatus.value ===  permstatus) {
        // permissions.value.data = permissions.value.data.filter(perm => !permissionIds.includes(perm.id));
        permissions.value.data = permissions.value.data.filter(rp => !affectedpermissions.some(ap => ap.id === rp.id));
    }

    // toggle permissions is_in_role attribute value
    toggleIsInRole(affectedpermissions);
}

const toggleIsInRole = (affectedpermissions) => {
    if ( permStatus.value ===  'all') {
        permissions.value.data.forEach((permission) => {
            const idx = affectedpermissions.findIndex(p => p.id === permission.id);
            if ( idx !== -1 ) {
                permission.is_in_role = !permission.is_in_role;
                const chkbx = document.getElementById('permission_' + permission.id);
                chkbx.setAttribute('checked', false);
            }
        });
    }
}

const outRolePermissionsCount = computed(() => {
    return  (permissionscount.value === 0) ? 0 : (rolepermissions.value.length ? permissionscount.value - rolepermissions.value.length : permissionscount.value);
});

watch(searchQuery, debounce(() => {
    getPermissions();
}, 300));

onMounted(() => {
    if (route.name === 'admin.roles.edit') {
        roleid.value = route.params.id;
        editMode.value = true;
        getRole();
    }
    getPermissions();
    // getPermissionsCount();
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
                        Role</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/roles">Roles</router-link>
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
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Name</label>
                                            <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }" id="name" placeholder="Enter Name">
                                            <span class="invalid-feedback">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Guard Name</label>
                                            <input v-model="form.guard_name" type="text" class="form-control" :class="{ 'is-invalid': errors.guard_name }" id="guard_name" placeholder="Enter Guard Name">
                                            <span class="invalid-feedback">{{ errors.guard_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary m-2">Submit</button>
                                    <router-link to="/admin/roles">
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
                            <h3 class="card-title">Role Permissions</h3>
                            <div class="card-tools">
                                <span data-toggle="tooltip" title="3 New Messages" class="badge badge-success"></span>

                                <div class="btn-group">
                                    <button @click="getPermissionsByStatus('all')" type="button" class="btn btn-xs btn-outline-info" :class="permStatus === 'all' ? 'active' : ''">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ permissionscount }}</span>
                                    </button>

                                    <button :disabled="!editMode" @click="getPermissionsByStatus('in_role')" type="button" class="btn btn-xs btn-outline-success" :class="permStatus === 'in_role' ? 'active' : ''">
                                        <span class="mr-1">In Role</span>
                                        <span class="badge badge-pill badge-info">{{ rolepermissions.length ? rolepermissions.length : 0 }}</span>
                                    </button>

                                    <button :disabled="!editMode" @click="getPermissionsByStatus('out_role')" type="button" class="btn btn-xs btn-outline-warning" :class="permStatus === 'out_role' ? 'active' : ''">
                                        <span class="mr-1">Out Role</span>
                                        <span class="badge badge-pill badge-info">{{ outRolePermissionsCount }}</span>
                                    </button>

                                </div>

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <div v-if="selectedPermissions.length > 0">
                                            <button :disabled="permStatus === 'in_role'" @click="bulkAssign" type="button" class="ml-2 mb-2 btn btn-success">
                                                <i class="fa fa-link mr-1"></i>
                                                Give Selected
                                            </button>
                                            <button :disabled="permStatus === 'out_role'" @click="bulkRevoke" type="button" class="ml-2 mb-2 btn btn-warning">
                                                <i class="fa fa-ban mr-1"></i>
                                                Revoke Selected
                                            </button>
                                            <span class="ml-2">Selected {{ selectedPermissions.length }} roles</span>
                                        </div>
                                    </div>
                                    <div>
                                        <input style="height: 20pt" type="text" v-model="searchQuery" class="form-control pt-2 text-xs" placeholder="Search..." />
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" v-model="selectAll" @change="selectAllPermissions" /></th>
                                        <th style="width: 10px">#</th>
                                        <th>Name</th>
                                        <th>Guard Name</th>
                                        <th>Level</th>
                                        <th>Registered Date</th>
                                        <th>Options</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="permissions.data.length > 0">
                                    <PermissionListItem v-for="(permission, index) in permissions.data"
                                                        key="permission.id"
                                                        :editMode=editMode
                                                        :role=role
                                                        :permission=permission
                                                        :index=index
                                                        @toggle-selection="toggleSelection"
                                                        @permission-affectation-changed="permissionAffectationChanged"
                                                        :selectAll="selectAll"
                                                        :selectedPermissions="selectedPermissions"
                                    />
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <td colspan="6" class="text-center">No results found...</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <Bootstrap4Pagination :data="permissions" @pagination-change-page="getPermissions" align="right" />
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

<script setup>
import {computed, onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    role: Object,
    permission: Object,
    index: Number,
    editMode: Boolean,
    selectAll: Boolean,
    selectedPermissions: Array,
});

const emit = defineEmits(['confirmRoleDeletion']);

const permissionAssign = () => {
    axios.patch(`/api/roles/${props.role.id}/assign-permissions`, {
        permissionsids: [props.permission.id]
    })
        .then(response => {
            // props.permission.is_in_role = true;
            emit('permissionAffectationChanged', props.permission);

            toastr.success(response.data.message);
        });
};

const permissionRevoke = () => {
    axios.patch(`/api/roles/${props.role.id}/revoke-permissions`, {
        permissionsids: [props.permission.id]
    })
        .then(response => {
            // props.permission.is_in_role = false;
            emit('permissionAffectationChanged', props.permission);

            toastr.success(response.data.message);
        });
};

const toggleSelection = (event) => {
    emit('toggleSelection', props.permission, event.target.id);
}

const permissionChecked = computed(() => {
    return props.selectedPermissions.includes(props.permission.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="permissionChecked" @change="toggleSelection" :key="permission.id" :id="'permission_' + permission.id" :ref="'permission_' + permission.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ permission.name }}</td>
        <td class="text text-xs" >{{ permission.guard_name }}</td>
        <td class="text text-xs" >{{ permission.level }}</td>
        <td class="text text-xs" >{{ formatDate(permission.created_at) }}</td>
        <td>
            <a :class="props.editMode ? '' : 'disabled'" class="text text-xs" v-if="! permission.is_in_role" href="#" @click.prevent="permissionAssign"><i class="fa fa-link text-success ml-2" :class="props.editMode ? '' : 'text-muted'"></i></a>
            <a :class="props.editMode ? '' : 'disabled'" disabled="true" class="text text-xs" v-else href="#" @click.prevent="permissionRevoke"><i class="fa fa-ban text-orange ml-2" :class="props.editMode ? '' : 'text-muted'"></i></a>
        </td>
    </tr>
</template>

<style>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
</style>

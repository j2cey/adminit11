<script setup>
import {onMounted, ref} from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion']);

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
        .then(() => {
            toastr.success('Role changed successfully!');
        })
};

const toggleSelection = () => {
    emit('toggleSelection', props.user);
}

onMounted(() => {

});
</script>
<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="user.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ user.name }}</td>
        <td class="text text-xs" >{{ user.email }}</td>
        <td class="text text-xs" >{{ user.formatted_created_at }}</td>
        <td class="text text-xs" >
            <span v-for="role in user.roles" class="badge text-muted">{{ role.name }}</span>
        </td>
        <td>
            <a class="text text-xs" v-if="can('user-update')" href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a class="text text-xs" v-if="can('user-delete')" href="#" @click.prevent="$emit('confirmUserDeletion', user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

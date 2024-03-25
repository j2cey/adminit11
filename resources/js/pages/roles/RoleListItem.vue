<script setup>
import { onMounted, ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import { formatDate } from '../../helper.js'

const { can, cannot } = useAbility();

const toastr = useToastr();

const props = defineProps({
    role: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['roleDeleted', 'confirmRoleDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.role);
}

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection" :key="role.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ role.name }}</td>
        <td class="text text-xs" >{{ role.guard_name }}</td>
        <td class="text text-xs" >{{ formatDate(role.created_at) }}</td>
        <td class="text text-xs" >
            ({{role.permissions.length}})<span v-for="permission in role.permissions.slice(0, 10)" class="badge" :class="(permission.level === 1 ? 'text-danger' : (permission.level === 2 || permission.level === 3 ? 'text-orange' : 'text-default'))">{{ permission.name }}</span>
        </td>
        <td>
            <router-link v-if="can('role-update')" :to="`/admin/roles/${role.id}/edit`">
                <i class="fa fa-edit mr-2 text text-xs"></i>
            </router-link>
            <a class="text text-xs" v-if="can('role-delete')" href="#" @click.prevent="$emit('confirmRoleDeletion', role)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

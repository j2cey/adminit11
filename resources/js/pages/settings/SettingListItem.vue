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
    setting: Object,
    index: Number,
    selectAll: Boolean,
    selectedSettings: Array,
});

const emit = defineEmits(['confirmRoleDeletion']);

const toggleSelection = () => {
    emit('toggleSelection', props.setting);
}

const settingChecked = computed(() => {
    return props.selectedSettings.includes(props.setting.id) ? true : (!!props.selectAll);
});

onMounted(() => {

});
</script>

<template>
    <tr>
        <td><input type="checkbox" :checked="settingChecked" @change="toggleSelection" :key="setting.id" :id="'setting_' + setting.id" :ref="'setting_' + setting.id" /></td>
        <td class="text text-xs" >{{ index + 1 }}</td>
        <td class="text text-xs" >{{ setting.full_path }}</td>
        <td class="text text-xs" >{{ setting.value }}</td>
        <td class="text text-xs" >{{ setting.description }}</td>
        <td class="text text-xs" >{{ setting.group?.name }}</td>
        <td class="text text-xs" >{{ setting.maingroup?.name }}</td>
        <td>
            <router-link v-if="can('setting-update')" :to="`/admin/settings/${setting.id}/edit`">
                <i class="fa fa-edit mr-2 text text-xs"></i>
            </router-link>
            <a class="text text-xs" v-if="can('setting-delete')" href="#" @click.prevent="$emit('confirmRoleDeletion', setting)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>
</template>

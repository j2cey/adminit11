import axios from "axios";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useStorage } from '@vueuse/core'

export const useSettingStore = defineStore('SettingStore', () => {
    const settings = ref({
        app: {name: ''},
    });

    const theme = useStorage('SettingStore:theme', ref('light'));

    const changeTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light';
    };

    const getSettings = async () => {
        await axios.get('/api/settings/fetch')
            .then((response) => {
                settings.value = response.data;
                console.log('STORE setting, response.data: ', response.data);
                console.log('STORE setting: setting.value', settings.value);
            });
    };

    return { settings, getSettings, theme, changeTheme };
});

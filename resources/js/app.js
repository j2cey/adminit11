import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from "vue/dist/vue.esm-bundler.js";
import { createPinia } from 'pinia';
import {createRouter, createWebHistory} from "vue-router";
import Routes from './routes.js'
import Login from './pages/auth/Login.vue';
import App from './App.vue';
import { useAuthUserStore } from './stores/AuthUserStore';
import { useSettingStore } from './stores/SettingStore';

import { abilitiesPlugin, Can } from '@casl/vue';
import ability from './services/ability';

const pinia = createPinia();
const app = createApp(App);

const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
});

app
    .use(abilitiesPlugin, ability, {useGlobalProperties: true})
    .component(Can.name, Can);
router.beforeEach(async (to, from) => {
    const authUserStore = useAuthUserStore();
    if (authUserStore.user.name === '' && to.name !== 'admin.login') {
        const settingStore = useSettingStore();
        await Promise.all([
            authUserStore.getAuthUser(),
            settingStore.getSettings(),
            authUserStore.getAbilities(),
        ]);
    }
});

app.use(pinia);
app.use(router);

// if (window.location.pathname === '/login') {
//     const currentApp = createApp({});
//     currentApp.component('Login', Login);
//     currentApp.mount('#login');
// } else {
//     app.mount('#app');
// }

app.mount('#app');

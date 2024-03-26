<script setup>
import {onMounted, onUnmounted} from 'vue';
import { useAuthUserStore } from '../stores/AuthUserStore';
import { useRouter } from 'vue-router';
import { useSettingStore } from '../stores/SettingStore';
import { Can } from "@casl/vue";
import { useAbility } from "@casl/vue";
import imgUrl from '../../assets/img/app_logo.png'


const { can, cannot } = useAbility();

const router = useRouter();
const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const logout = () => {
    axios.post('/logout')
        .then((response) => {
            // window.location.href = '/login';
            authUserStore.user.name = '';
            router.push('/login');

        });
};

const goToProfile = () => {
    router.push('/admin/profile');
}

onMounted( () => {
    $('[data-widget = "treeview"]').Treeview('init');
    // $('#mainSidebar').Treeview('init');

    console.log('Main sidebar mounted.');
})

onUnmounted(() => {
    $(document).on('click','[data-widget="treeview"] .nav-link', function (e) {
        e.stopImmediatePropagation();
    });

    console.log('Treeview stopped.');
})
</script>

<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <a href="index3.html" class="brand-link">
            <img :src="imgUrl" alt="Admin-IT Logo" class="brand-image img-rounded elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ settingStore.settings.app.name }}</span>
        </a>

        <div class="sidebar">

            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="authUserStore.user.avatar" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" @click.prevent="goToProfile" class="d-block">{{ authUserStore.user.name }}</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <router-link to="/admin/dashboard" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-columns"></i>
                            <p>
                                Reports
                                <i class="fas fa-angle-left right"></i>
                                <span class="badge badge-info right">6</span>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-circle nav-icon text-success"></i>
                                    <p>Reports List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-circle nav-icon text-danger"></i>
                                    <p>
                                        Treatments
                                        <span class="badge badge-danger right">6</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Report Parameters
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Report Types</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li v-if="can('profile-list')" class="nav-item">
                        <router-link to="/admin/profile" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Profile
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('manage-all')" class="nav-header">ADMINISTRATION</li>

                    <li v-if="can('manage-all')" class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Parameters
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        IT Ressource
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Account Access</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/register.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Servers</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        IT Parameters
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="pages/examples/login-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>File MimeTypes</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/register-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Access Protocoles</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pages/examples/forgot-password-v2.html" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>OS Server</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('user-list')" to="/admin/users" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <router-link v-if="can('role-list')" to="/admin/roles" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-id-badge"></i>
                            <p>
                                Roles
                            </p>
                        </router-link>
                    </li>

                    <li v-if="can('setting-list')" class="nav-item">
                        <router-link to="/admin/settings" active-class="active" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Settings
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item">
                        <form class="nav-link">
                            <a href="#" @click.prevent="logout">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>

        </div>

    </aside>
</template>

<style scoped>

</style>

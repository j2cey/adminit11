import Dashboard from "./components/Dashboard.vue";
import UserList from "./pages/users/UserList.vue";
import RoleList from "./pages/roles/RoleList.vue";
import RoleForm from "./pages/roles/RoleForm.vue";
import SettingList from "./pages/settings/SettingList.vue";
import SettingForm from "./pages/settings/SettingForm.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import Login from './pages/auth/Login.vue';

export default [
    {
        path: '/login',
        name: 'admin.login',
        component: Login,
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
    },
    {
        path: '/admin/roles',
        name: 'admin.roles',
        component: RoleList,
    },
    {
        path: '/admin/roles/create',
        name: 'admin.roles.create',
        component: RoleForm,
    },
    {
        path: '/admin/roles/:id/edit',
        name: 'admin.roles.edit',
        component: RoleForm,
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: SettingList,
    },
    {
        path: '/admin/settings/:id/edit',
        name: 'admin.settings.edit',
        component: SettingForm,
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: UpdateProfile,
    }
]

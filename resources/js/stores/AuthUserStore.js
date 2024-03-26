import axios from 'axios';
import { defineStore } from 'pinia';
import {inject, ref} from 'vue';
import { AbilityBuilder, Ability } from "@casl/ability";
//import ability from "../services/ability.js";
import { ABILITY_TOKEN } from "@casl/vue";

export const useAuthUserStore = defineStore('AuthUserStore', () => {
    const user = ref({
        name: '',
        email: '',
        avatar: '',
    });

    const userroles = ref({});

    const getAuthUser = async () => {
        await axios.get('/api/profile')
            .then((response) => {
                user.value = response.data;
                userroles.value = response.data.roles.map(role => role.name.toLowerCase());

                console.log('STORE user.value: ', user.value);
                console.log('STORE userroles.value: ', userroles.value);
            });
    };

    const includesAny = (arr, values) => values.some(v => arr.includes(v));

    const adminroles = ref(['admin','super admin']);
    const permissions = ref({});

    const ability = inject(ABILITY_TOKEN);
    const getAbilities = async() => {
        axios.get('/api/abilities').then(response => {
            permissions.value = response.data;
            const { can, rules } = new AbilityBuilder(Ability);

            if (includesAny(userroles.value, adminroles.value)) {
                permissions.value.push('manage-all');
            }

            can(permissions.value);
            ability.update(rules);
            console.log('STORE abilities: ', permissions.value);
        });
    };

    return { user, getAuthUser, getAbilities };
});

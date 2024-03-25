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
        role: '',
        avatar: '',
    });

    const getAuthUser = async () => {
        await axios.get('/api/profile')
            .then((response) => {
                user.value = response.data;
            });
    };

    const permissions = ref({});

    const ability = inject(ABILITY_TOKEN);
    const getAbilities = async() => {
        axios.get('/api/abilities').then(response => {
            permissions.value = response.data;
            const { can, rules } = new AbilityBuilder(Ability);
            can(response.data);
            ability.update(rules);
            console.log('STORE abilities: ', response.data);
        });
    };

    return { user, getAuthUser, getAbilities };
});

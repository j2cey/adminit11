import { inject } from 'vue';
import { AbilityBuilder, Ability } from '@casl/ability';
import { ABILITY_TOKEN } from '@casl/vue';

export default function useAuth() {
    const ability = inject(ABILITY_TOKEN);
    const getAbilities = async() => {
        axios.get('/api/abilities').then(response => {
            const permissions = response.data;
            const { can, rules } = new AbilityBuilder(Ability);
            can(permissions);
            ability.update(rules);
            console.log('AUTH abilities: ', response.data);
        });
    };
    return { getAbilities };
}

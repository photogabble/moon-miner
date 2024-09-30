import {usePage} from "@inertiajs/vue3";
import type {PageProps, User} from "@/types";

export function usePlayerState() {
    const {props} = usePage<PageProps>();

    return {
        player: props.auth.user,
        encounter: props.auth?.user?.current_encounter ?? null,
        ship: props.auth?.user?.ship ?? null,
        system: props.auth?.user?.ship?.system ?? null,
        presets: props.auth?.user?.presets ?? null,
        stats: props.stats,
        config: props.config,
    }
}

import {PageProps as InertiaPageProps} from "@inertiajs/core";
import {WarpRouteResource} from "@/types/resources/link";
import {EncounterResource} from "@/types/resources/encounter";
import {SystemResource} from "@/types/resources/system";

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    // navigation: boolean;
    // route: null|WarpRouteResource;
    // encounters: Array<EncounterResource>;
    system: SystemResource

    navicom_view_mode: 'map' | 'details'
}

export interface DashboardPageProps extends InertiaPageProps, PageProps {}

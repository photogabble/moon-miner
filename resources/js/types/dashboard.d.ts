import {PageProps as InertiaPageProps} from "@inertiajs/core";
import {SystemResource} from "@/types/resources/system";
import type {PageProps} from "@/types/index";

export interface DashboardPageProps extends InertiaPageProps, PageProps {
    // navigation: boolean;
    // route: null|WarpRouteResource;
    // encounters: Array<EncounterResource>;
    system: SystemResource

    navicom_view_mode: 'map' | 'details'
}

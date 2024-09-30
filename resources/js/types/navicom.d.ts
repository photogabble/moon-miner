import {PageProps as InertiaPageProps} from "@inertiajs/core";
import {SystemResource} from "@/types/resources/system";
import {WaypointResource} from "@/types/resources/waypoint";

/**
 * Used by System.vue for the /navicom and /navicom/{system} routes
 */
export interface NaviComSystemProps extends InertiaPageProps {
    system: SystemResource
}

/**
 * Used by Planet.vue for the /navicom/planet/{planet} route
 */
export interface NaviComPlanetProps extends InertiaPageProps {
    // TypeScripts built in Required<T> makes all optional properties of T required this might not be
    // what we want to do, in which case Required<T, K> from package/utility-types should be used
    // instead: https://www.npmjs.com/package/utility-types#requiredt-k
    planet: Required<WaypointResource<'Planet'>>
}

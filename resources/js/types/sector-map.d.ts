import type {PageProps as InertiaPageProps} from "@inertiajs/core";
import type {PageProps} from "@/types/index";
import type {SectorResource} from "@/types/resources/sector";
import type {SectorMapLinkResource, SectorMapSystemResource} from "@/types/resources/sector";

export interface AutoPilotResource {
    on_route: boolean,
    remaining_systems: number,
    next_system_id: number|null,
    system_ids: Array<number>,
    path: Array<string>,
}

export interface SectorMapPageProps extends InertiaPageProps, PageProps {
    stats: {
        visited: number,
        known: number,
        discovery_percentage: number,
    },
    autopilot?: AutoPilotResource,
    sector: SectorResource,
    systems: Array<SectorMapSystemResource>,
    links: Array<SectorMapLinkResource>,
}

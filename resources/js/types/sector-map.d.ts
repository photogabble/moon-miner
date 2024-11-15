import type {PageProps as InertiaPageProps} from "@inertiajs/core";
import type {PageProps} from "@/types/index";
import type {SectorResource} from "@/types/resources/sector";
import type {SectorMapLinkResource, SectorMapSystemResource} from "@/types/resources/sector";

export interface SectorMapPageProps extends InertiaPageProps, PageProps {
    stats: {
        visited: number,
        known: number,
        discovery_percentage: number,
    },
    sector: SectorResource,
    systems: Array<SectorMapSystemResource>,
    links: Array<SectorMapLinkResource>,
}

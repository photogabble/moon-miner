import type {ZoneResource} from "@/types/resources/zone";
import type {LinkResource} from "@/types/resources/link";
import type {SectorDefenseResource} from "@/types/resources/sector-defense";
import type {PlanetResource} from "@/types/resources/planet";
import type {SystemResource} from "@/types/resources/system";

export type SectorType = 'unknown' | 'none' | 'port-goods' | 'port-energy' | 'port-ore' | 'port-special';

// TODO: rename to SystemResource to match backend
export interface SectorResource
{
    id: number;
    name: string;
    beacon: string;
    port_type: SectorType;

    // Scanned Resources:
    zone?: ZoneResource;
    planets?: Array<PlanetResource>;
    ports?: Array<any>;
    defenses?: Array<SectorDefenseResource>;
    links?: Array<LinkResource>;
}

export interface SectorResourceWithPlayerMeta extends SectorResource
{
    is_current_sector: boolean
    has_visited: boolean;
    has_danger: boolean;
}

export interface SectorMapLinkResource
{
    from: { x: number, y: number },
    to: { x: number, y: number },
    is_internal: boolean,
    is_route: boolean, // Is this link part of a plotted route
    has_visited: boolean, // Has the player visited either end of this link
}

export interface SectorMapSystemResource extends SystemResource
{
    coords: { x: number, y: number },
    is_internal: boolean,
    is_next_door: boolean,
    actions: Array<{
        title: string,
        method?: 'get' | 'post' | 'patch' | 'put' | 'delete',
        href: string,
    }>,
}

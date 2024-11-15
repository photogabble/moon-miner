import {ZoneResource} from "@/types/resources/zone";
import {WaypointResource} from "@/types/resources/waypoint";

export interface SystemResource {
    id: number
    sector_id: number
    name: string
    waypoints?: Array<WaypointResource>
    zone?: ZoneResource

    is_current_system: boolean
    has_visited: boolean // Has the player entered this system at least once
    has_knowledge: boolean // Has the player entered a system that links to this one
    has_danger: boolean
}

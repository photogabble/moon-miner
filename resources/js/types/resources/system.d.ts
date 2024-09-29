import {ZoneResource} from "@/types/resources/zone";
import {WaypointResource} from "@/types/resources/waypoint";

export interface SystemResource {
    id: number
    name: string
    waypoints?: Array<WaypointResource>
    zone?: ZoneResource

    is_current_sector: boolean
    has_visited: boolean
    has_danger: boolean
}

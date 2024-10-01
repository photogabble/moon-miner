import type {PageProps as InertiaPageProps} from "@inertiajs/core";
import type {PaginatedResource} from "@/types/laravel/pagination";
import type {RouteParams} from "ziggy-js";

export interface PlayerRankingResource {
    name: string;
    is_player: boolean;
    is_admin: boolean;
    rank: number;
    insignia: string;
    turns_used: number;
    score: number;
    last_active: string;
    rating: number;
    efficiency: number;
    team: any; // todo TeamResource
}

export type PlayerRankingParams = RouteParams & {
    sort_players_by: string;
    sort_players_direction?: string;
}

export interface TeamRankingResource {
    name: string;
    player_count: number;
    turns_used: number;
    score: number;
    rating: number;
    efficiency: number;
}

export type TeamRankingParams = RouteParams & {
    sort_teams_by: string;
    sort_teams_direction?: string;
}

export type Ranking<T> = {
    sorts: Array<string>;
    sorting_by: string;
    sorting_direction: 'ASC' | 'DESC';
    ranking: T
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    player: Ranking<PaginatedResource<PlayerRankingResource>>,
    team: Ranking<PaginatedResource<TeamRankingResource>>
}

export interface RankingPageProps extends InertiaPageProps, PageProps {
}

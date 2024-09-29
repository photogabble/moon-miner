import {Owner} from "@/types";

export enum WaypointType {
    Star = 'Star',
    Planet = 'Planet'
}
export enum SpectralType {
    O = 'O',
    B = 'B',
    A = 'A',
    F = 'F',
    G = 'G',
    K = 'K',
    M = 'M',
    C = 'C',
    S = 'S',
    D = 'D'
}

export interface StarProperties {
    spectral_type: SpectralType
    mass: number
    luminosity: number
    lifetime: number
    age: number
    radius: number
    density: number
    temperatureK: number
    colour: string
}

export interface PlanetProperties {
    // TODO
}

export interface WaypointResource<T = any> {
    id: number
    name: string
    type: WaypointType
    owner?: Owner
    primary_id: number

    properties: T

    orbit: {
        angle: number
        distance: number
        eccentricity: number
        inclination: number
    }
}

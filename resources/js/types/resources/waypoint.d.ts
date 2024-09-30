import {Owner} from "@/types";
import {SystemResource} from "@/types/resources/system";

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

export interface CelestialProperties {
    density: number
    eccentricity: number
    escapeVelocity: number
    massDust: number
    massGas: number
    orbitPeriod: number
    orbitRadius: number
    pressure: 'very low' | 'low' | 'medium' | 'high' | 'very high'
    radius: number
    surfaceGravity: number
    temperatureK: number
}

export type TrackPropertiesOf<T extends WaypointType> =
    T extends "Star" ? StarProperties :
    T extends "Planet" ? CelestialProperties :
    never;

export interface WaypointResource<T extends WaypointType> {
    id: number
    name: string
    type: T
    owner?: Owner
    primary_id: number
    system?: SystemResource

    properties: TrackPropertiesOf<T>

    orbit: {
        angle: number
        distance: number
        eccentricity: number
        inclination: number
    }
}

export type WaypointList = Array<
    WaypointResource<'Star'> | WaypointResource<'Planet'>
>;

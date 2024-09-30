export type EncounterType = string;

export interface EncounterOption {
    link: string;
    text: string;
}

export interface EncounterResource {
    id: number;
    title: string;
    type: EncounterType;
    options: Record<string, EncounterOption>;
}

import { Feature } from "./feature";

interface UsedFeatures {
    id: number;
    feature: Feature;
    created_at: string;
    data: [];
    credits: number;
}

interface Links {
    url: string;
    active: boolean;
    label: string;
}

export interface UsedFeature {
    data: UsedFeatures[];
    meta: { links: Links[] };
    links: {};
}

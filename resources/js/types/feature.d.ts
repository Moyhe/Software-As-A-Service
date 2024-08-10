export interface Feature {
    id: number;
    image: string;
    route_name: string;
    name: string;
    description: string;
    required_credits: number;
    active: boolean;
}

export interface Features {
    data: Feature[];
}

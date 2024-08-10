interface Package {
    id: number;
    name: string;
    price: number;
    credits: number;
}

export interface Packages {
    data: Package[];
}

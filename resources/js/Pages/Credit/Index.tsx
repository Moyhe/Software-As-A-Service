import { PageProps } from "@/types";
import { Features } from "@/types/feature";
import { Packages } from "@/types/package";
import { Head, usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import PackagesPricingCards from "@/Components/PackagesPricingCards";

interface Props {
    features: Features;
    packages: Packages;
    success: string;
    error: string;
}

const Index = ({ features, packages, success, error }: Props) => {
    const { user } = usePage<PageProps>().props.auth;

    const availableCredits = user.available_credits;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Your Credits
                </h2>
            }
        >
            <Head title="Your Credits" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {success && (
                        <div className="rounded-lg mb-4 p-3 rounded text-gray-100 bg-emerald-600">
                            {success}
                        </div>
                    )}
                    {error && (
                        <div className="rounded-lg mb-4 p-3 rounded text-gray-100 bg-red-500">
                            {error}
                        </div>
                    )}

                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg relative">
                        <div className="flex flex-col gap-3 items-center p-4">
                            <img
                                src="/img/coin.jpg"
                                alt=""
                                className="w-[100px]"
                            />
                            <h3 className="text-white text-2xl">
                                you have {availableCredits} credits
                            </h3>
                        </div>
                    </div>
                    <PackagesPricingCards
                        packages={packages}
                        features={features}
                    />
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Index;

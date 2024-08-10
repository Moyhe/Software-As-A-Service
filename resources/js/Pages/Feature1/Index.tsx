import Features from "@/Components/Features";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Feature } from "@/types/feature";
import { useForm } from "@inertiajs/react";
import { FormEvent } from "react";

interface Props {
    feature: Feature;
    answer: number;
}

const Index = ({ feature, answer }: Props) => {
    const { data, setData, post, processing, errors, reset } = useForm({
        number1: "",
        number2: "",
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();

        post(route("feature1.calculate"), {
            onSuccess: () => reset(),
        });
    };

    return (
        <Features feature={feature} answer={answer}>
            <form onSubmit={submit} className="p-8 grid grid-cols-2 gap-3">
                <div>
                    <InputLabel
                        htmlFor="number1"
                        value="number 1"
                        className="mt-2"
                    />
                    <TextInput
                        id="number1"
                        type="text"
                        className="mt-1 block w-full"
                        name="number1"
                        value={data.number1}
                        onChange={(e) => setData("number1", e.target.value)}
                    />
                    <InputError message={errors.number1} />
                </div>

                <div>
                    <InputLabel
                        htmlFor="number2"
                        value="number 2"
                        className="mt-2"
                    />
                    <TextInput
                        id="number2"
                        type="text"
                        name="number2"
                        className="mt-1 block w-full"
                        value={data.number2}
                        onChange={(e) => setData("number2", e.target.value)}
                    />
                    <InputError message={errors.number2} className="mt-2" />
                </div>

                <div className="flex items-center justify-end mt-4 col-span-2">
                    <PrimaryButton className="ms-4" disabled={processing}>
                        Calculate
                    </PrimaryButton>
                </div>
            </form>
        </Features>
    );
};

export default Index;

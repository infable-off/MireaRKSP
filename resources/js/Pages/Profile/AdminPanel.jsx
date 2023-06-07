import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link, useForm, usePage} from '@inertiajs/react';
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import Checkbox from "@/Components/Checkbox";
import PrimaryButton from "@/Components/PrimaryButton";
import {useEffect} from "react";
import '../../../sass/stylesDashboard.scss'
export default function AdminPanel({auth}) {
    const user = usePage().props.auth.user;
    const {data, setData, post, processing, errors, reset} = useForm({

    });

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Панель администратора видна только админу</h2>}
        >
            <Head title="Admin panel"/>

            <div >
                <div >
                    <div className="MenuBtn">
                        <a className="btnMenu btn" href={route('articlesOnReview')}>Что то будет для проверки</a>
                        <br/>
                        <a className="btnMenu btn" href={route('feedbackList')}>Мб будет обратная связь тут</a>
                        <br/>
                        <a className="btnMenu btn" href={route('addArticleZIP')}>Тут можно будет что то добавить, но не сейчас</a>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

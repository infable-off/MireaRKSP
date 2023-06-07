import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import '../../sass/stylesDashboard.scss';

import { BrowserRouter as Router, Routes, Route } from "react-router-dom"
import Header from '../Components/header/Header';
import Home from './home/home';
import MovieList from '../Components/movieList/movieList';
import Movie from './movieDetail/movie';



export default function Dashboard({ auth, can }) {
    return (
        <AuthenticatedLayout
            user={auth.user}

        >

            <Router>
                <Header />
                <Routes>
                    <Route path="/home" element={<Home />}></Route>
                    <Route path="movie/:id" element={<Movie />}></Route>
                    <Route path="movies/:type" element={<MovieList />}></Route>
                    {/*<Route path="/*" element={<h1>Error Page</h1>}></Route>*/}


                </Routes>
            </Router>



                            {/*<div className="MenuBtn">*/}
                            {/*    <a href={route('addArticle')} className="btnMenu btn btn-primary">Добавить то</a>*/}

                            {/*    <a href={route('myArticles')} className="btnMenu btn btn-primary">Список чего нибудь</a>*/}

                            {/*    <a href={route('searchArticles')} className="btnMenu btn btn-primary">Поиск чего нибудь</a>*/}

                            {/*    <a href={route('addFeedback')} className="btnMenu btn btn-primary">Заполнить форму обратной связи когда будет готова</a>*/}

                            {/*    {*/}
                            {/*        can ? (<a className="btnMenu btn" href={route('adminPanel')}>Панель администратора видна только администратору</a>) : null*/}
                            {/*    }*/}
                            {/*</div>*/}



        </AuthenticatedLayout>
    );
}

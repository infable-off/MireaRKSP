<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use ZipArchive;
use function Webmozart\Assert\Tests\StaticAnalysis\uuid;

class ArticleController extends Controller
{
    public function addSingleArticle(Request $request)
    {
        if (!$request->file('article')) {
            return abort(500);
        }
        $article_data = $request->validate([
            'title' => 'required',
            'indexability' => 'required',
            'udc' => 'required',
            'scientific_adviser' => '',
            'publication_place' => 'required'
        ]);

//        $request->validate([
//            'article' => ['required', 'mimes:docx']
//        ]);

        //dd($request->file());
        $article = new Article($article_data);
        $article->id = uniqid();

        $article->file_dir = $request->file('article')[0]->
        store('/public/' . date("Y") . '/' . date("m"));

        $article->verification_status = "on review";
        $article->user_id = Auth::user()->id;
        $article->save();
        return redirect(route('dashboard'));
    }

    public function getSingleArticle(Request $request, $id)
    {
        if(!$id){
            abort(500);
        }

        $article = Article::where('articles.id', '=', $id)->select('articles.*', 'users.name')->join('users', 'users.id', '=', 'articles.user_id')->first();
        if($article->verification_status != 'accepted'){
            abort(404);
        }
        return Inertia::render('SingleArticle', ["article" => $article]);

    }

    public function getArticleByProfileId()
    {
        return Inertia::render('Profile/MyArticles', ["articles" => Article::where(['user_id' => Auth::user()->id])->orderBy('updated_at')->get()]);
    }

    public function getArticlesByUDC($udc)
    {
        if(!$udc){
            abort(500);
        }
        $articles = Article::where(['articles.udc' => $udc, 'verification_status' => 'accepted'])->select('articles.*', 'users.name')->join('users', 'users.id', '=', 'articles.user_id')->orderBy('updated_at')->get();
        return Inertia::render('Search', ["articles" => $articles, "info" => "Статьи по указанному УДК"]);
    }

    public function getArticlesOnReview()
    {
        $user = Auth::user();
        if(!$user->can('admin')){
            abort(404);
        }
        return Inertia::render('ArticlesOnReview', ["articles" => Article::where(['verification_status' => "on review"])->orderBy('updated_at')->get()]);
    }

    public function changeArticleStatus(Request $request)
    {
        $user = Auth::user();
        if(!$user->can('admin')){
            abort(404);
        }
        if(!$request->id){
            abort(500);
        }
        $article = Article::find($request->id);
        $article->verification_status = $request->result;

        $article->save();

    }

    public function searchArticle(Request $request)
    {
        $query = $request->search;
        $articles_array = Article::where([['title', 'like', '%' . $query . '%'], ['verification_status', '=', 'accepted']])->select('articles.*', 'users.name')->join('users', 'users.id', '=', 'articles.user_id')->orderBy('articles.updated_at')->get();
        $articles_array = $articles_array->merge(Article::query()->select('articles.*', 'users.name')->join('users', 'users.id', '=', 'articles.user_id')->where([['users.name', 'like', '%' . $query . '%'], ['verification_status', '=', 'accepted']])->orderBy('articles.updated_at')->get());
        $articles_array = $articles_array->merge(Article::where([['udc', 'like', '%' . $query . '%'], ['verification_status', '=', 'accepted']])->select('articles.*', 'users.name')->join('users', 'users.id', '=', 'articles.user_id')->orderBy('articles.updated_at')->get());
        return Inertia::render('Search', ["articles" => $articles_array]);
    }
    public function downloadFile(Request $request, $id)
    {
        $article = Article::find($id);
        if (!$article) {
            abort(500);
        }
        return Storage::download($article->file_dir, $article->title . '.docx');

    }

    public function addArticleZIP(Request $request)
    {
        $user = Auth::user();
        if(!$user->can('admin')){
            abort(404);
        }
        $request->validate([
           'title' => 'required',
           'indexability' => 'required',
           'articleZIP' => 'required'
        ]);
        $storage_prefix = 'public/' . date("Y") . '/' . date("m");
        $storage_app_prefix = 'app/'.$storage_prefix.'/'.$request->title;
        $storage_app_prefix_without_app = $storage_prefix.'/'.$request->title;
        Storage::makeDirectory($storage_app_prefix_without_app);
        $zip_file_dir = $request->file('articleZIP')[0]->store($storage_prefix);
        $zip_file_dest = Storage::path($zip_file_dir);
        $zip_file = new ZipArchive();
        $status = $zip_file->open($zip_file_dest);
        $status = $zip_file->extractTo(storage_path($storage_app_prefix));
        $zip_file->close();
        Storage::delete($zip_file_dir);

        $articles = Storage::files($storage_app_prefix_without_app);

        foreach ($articles as $article){
            $dir = $article;
            $article = pathinfo($article)['basename'];
            $args = explode('_', $article);
            $new_article = new Article();
            $new_article->title = $args[0];
            $new_article->file_dir = $dir;
            $new_article->indexability = $request->indexability;
            $new_article->udc = str_replace('.docx', '', $args[3]);
            $new_article->scientific_adviser = $args[2];
            $new_article->publication_place = $request->title;
            $new_article->verification_status = "accepted";
            $article_user = User::where('name', '=', $args[1])->first();
            if(!$article_user){
                $new_user = new User();
                $uid = uniqid();
                $new_user->id = $uid;
                $new_user->email = $uid.'@MAhostmail.ru';
                $new_user->password = uniqid();
                $new_user->save();
                $new_article->user_id = $uid;
            } else{
                $new_article->user_id = $article_user->id;
            }
            $new_article->save();
        }

    }
}

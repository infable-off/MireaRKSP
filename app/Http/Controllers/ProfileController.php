<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function fillInfo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'course' => 'required',
            'institute' => 'required',
            'faculty' => 'required',
            'group' => 'required',
            'phone' => 'required',
        ]);

        Auth::user()->fill($validated);
        Auth::user()->save();
        return Redirect::route('dashboard');
    }

    public function getAnotherProfileInfo($id)
    {
        if(!$id){
            return abort(500);
        }

        $articles = Article::where([['user_id', '=', $id], ['verification_status', '=', 'accepted']])->orderBy('updated_at')->get();
        $user_info = User::find($id);
        return Inertia::render('Profile/AnotherProfile', ["articles" => $articles, "another_user" => $user_info]);

    }
}

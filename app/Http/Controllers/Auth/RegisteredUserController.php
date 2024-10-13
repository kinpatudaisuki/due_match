<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'area' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'formats' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'area' => $request->area,
            'password' => Hash::make($request->password),
        ]);

        // フォーマットが選択されている場合は保存
        if ($request->has('formats') && !empty($request->input('formats'))) {
            $formatsArray = explode(',', $request->input('formats'));
            $user->formats()->sync($formatsArray);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('user.index', absolute: false));
    }
}

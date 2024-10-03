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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'area' => ['required', 'in:北海道,青森,岩手,宮城,秋田,山形,福島,茨城,栃木,群馬,埼玉,千葉,東京,神奈川,新潟,富山,石川,福井,山梨,長野,岐阜,静岡,愛知,三重,滋賀,京都,大阪,兵庫,奈良,和歌山,鳥取,島根,岡山,広島,山口,徳島,香川,愛媛,高知,福岡,佐賀,長崎,熊本,大分,宮崎,鹿児島,沖縄'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'area' => $request->area,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('user.index', absolute: false));
    }
}

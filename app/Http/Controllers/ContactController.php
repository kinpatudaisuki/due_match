<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create');
    }

    public function store(Request $request)
    {
        // バリデーション
        $inputs = $request->validate([
            'title' => 'required|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|max:1000',
            'g-recaptcha-response' => 'required',
        ]);

        // reCAPTCHAの検証
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $verify = $response->json(); // レスポンスのJSONデータを取得

        // reCAPTCHA認証が失敗した場合
        if (!($verify['success'] ?? false)) {
            return back()->with('message', 'reCAPTCHA認証に失敗しました。もう一度お試しください。')->withInput();
        }

        $contact = new Contact();
        $contact->title = $inputs['title'];
        $contact->email = $inputs['email'];
        $contact->body = $inputs['body'];
        $contact->save();

        Mail::to(config('mail.admin'))->send(new ContactForm($inputs));
        Mail::to($inputs['email'])->send(new ContactForm($inputs));

        return back()->with('message', 'メールを送信したのでご確認ください');
    }
}

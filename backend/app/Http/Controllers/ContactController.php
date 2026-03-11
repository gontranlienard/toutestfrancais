<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactMessage;

class ContactController extends Controller
{

    public function show()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required|min:10'
        ]);

        // Sauvegarde en base
        ContactMessage::create([
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message']
        ]);

        try {

            Mail::raw(
                "Nouveau message depuis Komparo Moto\n\n".
                "Email : ".$data['email']."\n\n".
                "Sujet : ".$data['subject']."\n\n".
                "Message :\n".$data['message'],
                function ($message) use ($data) {

                    $message->to('contact@komparo-moto.fr')
                        ->subject('Contact Komparo Moto : '.$data['subject'])
                        ->replyTo($data['email']);

                }
            );

        } catch (\Exception $e) {

            // même si le mail échoue, le message est stocké
        }

        return back()->with('success', 'Votre message a été envoyé.');

    }
}
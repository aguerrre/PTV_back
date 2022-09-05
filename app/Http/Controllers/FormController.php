<?php

namespace App\Http\Controllers;

use App\Form;
use App\User;
use Exception;
use PDF;
use Mail;
use Illuminate\Http\Request;

class FormController extends Controller
{

    function createAndSendEmail(Request $request)
    {
        $imgReq = request('img');
        $user = $request->user();
        if (is_null($imgReq) || is_null($user)) {
            return response()->json([
                'ok' => false,
                'msg' => "Compruebe los campos introducidos."
            ], 400);
        }
        try {
            $imgReq = str_replace('data:image/png;base64,', '', $imgReq);
            $imgReq = str_replace(' ', '+', $imgReq);
            $data = base64_decode($imgReq);
            $file = './images/forms/' . uniqid() . '.png';
            $success = file_put_contents($file, $data);
            $image = str_replace('./images/forms/', '', $file);

            $form = Form::create(['img' => $image, 'user_id' => $user->id]);
            $this->createPdfAndSendEmail($form);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Ha ocurrido un error'
            ], 500);
        }
        return response()->json([
            'ok' => true,
            'msg' => 'Email enviado',
            'to' => $user->email
        ], 200);
    }

    private function createPdfAndSendEmail($form)
    {
        $user = User::find($form->user_id);
        $data = [
            'title' => 'Informe de formulario',
            'name' => $user->name,
            'email' => $user->email,
            'img' => './images/forms/' . $form->img
        ];
        $pdf = PDF::loadView('emailTemplate', $data);
        Mail::send('emailTemplate', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->subject($data["title"])
                ->attachData($pdf->output(), "informe.pdf");
        });
    }
}

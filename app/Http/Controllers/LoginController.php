<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Cliente de Guzzle para realizar la petición al endpoint
        $guzzleClient = new Client();
        // Obtener los datos del formulario de login y comprobar que no sean nulos.
        // Si son correctos, se monta la url del endpoint.
        $userReq = request('user');
        $dniReq = request('dni');
        if (is_null($userReq) || is_null($dniReq)) {
            return response()->json([
                'ok' => false,
                'msg' => 'Compruebe los datos introducidos.'
            ], 403);
        };
        $url = 'http://212.225.255.130:8010/ws/accesotec/' . $userReq . '/' . $dniReq;
        // Se realiza la petición con Guzzle, se maneja la posible excepción,
        // y se parsean los resultados, primero de xml a json y luego a un array.
        try {
            $resp = $guzzleClient->request('POST', $url);
            if ($resp->getStatusCode() == 200) {
                $xmlResp = simplexml_load_string($resp->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);
                $json = json_encode($xmlResp);
                $array = json_decode($json, true);
                // Obtiene los datos del array y crea o actualiza un usuario.
                $attr = $array['Registro']['@attributes'];
                $data = $this->createUserAndToken($attr);
            } else {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Ha ocurrido un error, contacte con el administrador.'
                ], 500);
            }
        } catch (RequestException $e) {
            $xmlResp = simplexml_load_string($e->getResponse()->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($xmlResp);
            $array = json_decode($json, true);
            return response()->json([
                'ok' => false,
                'msg' => $array
            ], 401);
        }

        return response()->json([
            'ok' => true,
            'data' => $data
        ]);
    }

    /**
     * Crea un usuario y un token para ese usuario
     */
    private function createUserAndToken($formData)
    {
        $attrUser = [
            'code' => trim($formData['CodTecnico']),
            'name' => trim($formData['Nombre']),
            'email' => trim($formData['Email']),
            'delegation' => trim($formData['Delegacion']),
            'points' => trim($formData['Puntos']),
            'position' => trim($formData['Cargo']),
        ];
        $user = User::updateOrCreate(['code' => trim($formData['CodTecnico'])],$attrUser);
        // Genera un token de acceso usando Passport
        $token = $user->createToken('authToken')->accessToken;

        $data = ['user' => $user, 'token' => $token];
        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function buscar(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->query('email'); // pega da query string
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '❌ Nenhum usuário encontrado com esse e-mail.'
            ]);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ]
        ]);
    }
}

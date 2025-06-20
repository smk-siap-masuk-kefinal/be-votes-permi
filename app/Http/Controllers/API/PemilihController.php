<?php

namespace App\Http\Controllers\API;

use App\Models\Pemilih;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PemilihController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'nik' => 'required',
        ]);

        $nik = Pemilih::where('nik', $request->nik)->first();
        if(!$nik){
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'NIK not found'
            ], 404);
        }

        if($nik->is_voted){
            return response()->json([
                'success' => false,
                'data' => [
                    'nik' => $nik->nik,
                    'nama' => $nik->nama,
                    'tps' => $nik->tps,
                    'is_voted' => $nik->is_voted,
                ],
                'message' => 'You have already voted'
            ], 403);
        }

        $token = $nik->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'nik' => $nik->nik,
                'nama' => $nik->nama,
                'tps' => $nik->tps,
                // 'is_voted' => true,  // gunakan ini jika sudah siap production
                'is_voted' => $nik->is_voted,
                'access_token' => $token,
                'token_type' => 'Bearer',
                // 'user' => $nik
            ],
            'message' => 'Login Sucessful',
            ], 200);

    }
}

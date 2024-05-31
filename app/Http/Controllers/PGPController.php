<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Crypt\RSA;
use OpenPGP;
use OpenPGP_Crypt_RSA;
use OpenPGP_Message;
use OpenPGP_PublicKeyPacket;

class PGPController extends Controller
{
    public function generateKeys()
    {
        // Generate RSA key pair
        $rsaKey = RSA::createKey(2048);
        $privateKey = $rsaKey->toString('PKCS8');
        $publicKey = $rsaKey->getPublicKey()->toString('PKCS8');

        // Save keys
        Storage::disk('local')->put('private_key.pem', $privateKey);
        Storage::disk('local')->put('public_key.asc', $publicKey);

        // Return keys in view
        return view('pgp', [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
            'filePath' => storage_path('app/public_key.asc')
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use OpenPGP;
use OpenPGP_Crypt_RSA;
use OpenPGP_Message;
use OpenPGP_LiteralDataPacket;
use OpenPGP_Crypt_Symmetric;
use OpenPGP_SignaturePacket;
use Storage;
use phpseclib3\Crypt\RSA;

class EmailController extends Controller
{
    public function index()
    {
        return view('email_form');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'recipient' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $emailBody = $request->message;

        // Sign message if selected
        if ($request->has('signing') && $request->signing === 'yes') {
            $emailBody = $this->signMessage($emailBody);
        }

        // Encrypt message if selected
        if ($request->has('encryption') && $request->encryption === 'openpgp') {
            $emailBody = $this->encryptMessage($emailBody);
        }

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 3;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'Mailer');
            $mail->addAddress($request->recipient);

            // Content
            $mail->isHTML(false);
            $mail->Subject = $request->subject;
            $mail->Body = $emailBody;

            $mail->send();
            return redirect('/')->with('status', 'Email sent!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    private function signMessage($message)
    {
        // Load the private key
        $privateKeyData = file_get_contents(storage_path('app/private_key.pem'));
        $privateKey = RSA::loadPrivateKey($privateKeyData);

        // Create a signature packet
        $signature = new OpenPGP_SignaturePacket($privateKey->sign($message));

        // Return the signed message
        return $message . "\n\n" . OpenPGP::enarmor($signature->to_bytes(), 'PGP SIGNATURE');
    }

    private function encryptMessage($message)
    {
        // Load the public key
        $publicKeyData = file_get_contents(storage_path('app/public_key.asc'));

        // Parse the public key
        $publicKey = OpenPGP::unarmor($publicKeyData, 'PGP PUBLIC KEY BLOCK');
        $publicKeyMessage = OpenPGP_Message::parse($publicKey);

        // Encrypt the message using the public key
        $literal = new OpenPGP_LiteralDataPacket($message);
        $encrypted = OpenPGP_Crypt_Symmetric::encrypt(
            $publicKeyMessage,
            new OpenPGP_Message([$literal])
        );

        return OpenPGP::enarmor($encrypted->to_bytes(), 'PGP MESSAGE');
    }
}


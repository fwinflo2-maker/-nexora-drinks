<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        Log::info('OTP sendOtp appelé', ['email' => $request->email]);

        try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
            ], [
                'email.unique' => 'Cet email est déjà utilisé par un autre compte.',
            ]);
        } catch (ValidationException $e) {
            Log::error('OTP validation échouée', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('OTP erreur inattendue', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue.',
            ], 500);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('otp_'.$request->email, $otp, now()->addMinutes(15));

        Log::info('OTP généré', ['email' => $request->email]);

        try {
            Mail::raw(
                "Bonjour,\n\nVoici votre code de vérification NEXORA : {$otp}\n\nCe code est valable pendant 15 minutes.\n\nL'équipe NEXORA",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Votre code de vérification NEXORA');
                }
            );
            Log::info('OTP mail envoyé (ou loggé)', ['email' => $request->email]);
        } catch (\Exception $e) {
            Log::error('OTP mail échoué', ['error' => $e->getMessage()]);

            if (app()->environment('local')) {
                return response()->json(['success' => true, 'dev_otp' => $otp]);
            }

            return response()->json(['success' => false, 'message' => "Impossible d'envoyer l'email."], 500);
        }

        if (app()->environment('local')) {
            return response()->json(['success' => true, 'dev_otp' => $otp]);
        }

        return response()->json(['success' => true]);
    }

    public function verifyOtp(Request $request)
    {
        Log::info('OTP verifyOtp appelé', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $cachedOtp = Cache::get('otp_'.$request->email);

        Log::info('OTP comparaison', [
            'email' => $request->email,
            'has_cached' => $cachedOtp !== null,
        ]);

        if (! $cachedOtp || ! hash_equals($cachedOtp, (string) $request->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Le code est invalide ou a expiré.'],
            ]);
        }

        Cache::put('otp_verified_'.$request->email, true, now()->addMinutes(30));
        Cache::forget('otp_'.$request->email);

        return response()->json(['success' => true]);
    }
}

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

        $email = $request->email;
        $ip = $request->ip();
        $lockKeyEmail = 'otp_lock_'.$email;
        $attemptsKeyEmail = 'otp_attempts_'.$email;
        $lockKeyIp = 'otp_lock_ip_'.$ip;

        // Vérifier verrouillage par email
        if (Cache::has($lockKeyEmail)) {
            $ttl = Cache::get($lockKeyEmail.'_ttl', 15);
            throw ValidationException::withMessages([
                'otp' => ["Compte temporairement verrouillé. Réessayez dans {$ttl} minutes."],
            ]);
        }

        // Vérifier verrouillage par IP
        if (Cache::has($lockKeyIp)) {
            throw ValidationException::withMessages([
                'otp' => ['Trop de tentatives depuis cette adresse IP. Réessayez plus tard.'],
            ]);
        }

        $cachedOtp = Cache::get('otp_'.$email);

        Log::info('OTP comparaison', [
            'email' => $email,
            'has_cached' => $cachedOtp !== null,
        ]);

        if (! $cachedOtp || ! hash_equals($cachedOtp, (string) $request->otp)) {
            // Incrémenter compteur de tentatives
            $attempts = (int) Cache::get($attemptsKeyEmail, 0) + 1;
            Cache::put($attemptsKeyEmail, $attempts, now()->addMinutes(30));

            if ($attempts >= 3) {
                // Backoff exponentiel : 15min * 2^(attempts-3)
                $lockMinutes = min(15 * (2 ** ($attempts - 3)), 240);
                Cache::put($lockKeyEmail, true, now()->addMinutes($lockMinutes));
                Cache::put($lockKeyEmail.'_ttl', $lockMinutes, now()->addMinutes($lockMinutes));
                Cache::put($lockKeyIp, true, now()->addMinutes($lockMinutes));
                Cache::forget($attemptsKeyEmail);

                Log::warning('OTP: compte verrouillé après échecs répétés', [
                    'email' => $email,
                    'ip' => $ip,
                    'attempts' => $attempts,
                    'lock_minutes' => $lockMinutes,
                ]);

                throw ValidationException::withMessages([
                    'otp' => ["Trop de tentatives incorrectes. Compte verrouillé {$lockMinutes} minutes."],
                ]);
            }

            $remaining = 3 - $attempts;
            throw ValidationException::withMessages([
                'otp' => ["Le code est invalide ou a expiré. {$remaining} tentative(s) restante(s)."],
            ]);
        }

        // Succès : nettoyer tous les verrous et l'OTP
        Cache::forget('otp_'.$email);
        Cache::forget($attemptsKeyEmail);
        Cache::forget($lockKeyEmail);
        Cache::forget($lockKeyEmail.'_ttl');
        Cache::put('otp_verified_'.$email, true, now()->addMinutes(30));

        return response()->json(['success' => true]);
    }
}

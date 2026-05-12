<?php

// Dans routes/web.php — ajouter cette route (pas besoin d'auth) :

use App\Http\Controllers\NexaChatController;

// Route NEXA AI — appelée depuis register.tsx
Route::post('/nexa-chat', [NexaChatController::class, 'chat'])
    ->middleware(['web', 'throttle:30,1']) // max 30 appels/minute par IP
    ->name('nexa.chat');

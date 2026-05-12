<?php

// Dans config/services.php — ajouter cette entrée :

return [

    // ... autres services existants ...

    /*
    |--------------------------------------------------------------------------
    | OpenAI — NEXA AI Configurateur
    |--------------------------------------------------------------------------
    | Clé API OpenAI pour alimenter NEXA, l'assistant IA d'inscription.
    | Ajouter dans .env : OPENAI_API_KEY=sk-...
    */
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

];

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Models\Drinks\Article;
use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Loss;
use App\Models\Drinks\Payment;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\Sale;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AgentChatController extends Controller
{
    public function chat(Request $request, Team $current_team): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $role = $request->user()->teamRole($current_team);
        $context = $this->buildContext($current_team, $role);

        $systemPrompt = "Tu es l'assistant IA de l'entreprise \"{$current_team->name}\" pour le module Distribution Boissons.\n"
            ."Tu aides l'équipe à analyser les données de l'entreprise et à prendre des décisions éclairées.\n"
            ."Réponds en français, de façon concise et professionnelle.\n\n"
            ."DONNÉES DU MOIS EN COURS :\n{$context}\n"
            ."Rôle de l'utilisateur : ".($role?->label() ?? 'Administrateur');

        $messages = array_map(
            fn (array $m) => ['role' => $m['role'], 'content' => $m['content']],
            $validated['history'] ?? []
        );
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = Http::withToken(config('services.groq.key'))
            ->withOptions(['verify' => config('services.groq.verify', true)])
            ->timeout(30)
            ->post(config('services.groq.base_url').'/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => array_merge([['role' => 'system', 'content' => $systemPrompt]], $messages),
                'max_tokens' => 512,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Service IA indisponible. Vérifiez la clé GROQ_API_KEY.'], 503);
        }

        return response()->json([
            'reply' => $response->json('choices.0.message.content', 'Désolé, je n\'ai pas pu générer de réponse.'),
        ]);
    }

    private function buildContext(Team $team, ?TeamRole $role): string
    {
        $tid = $team->id;
        $from = today()->startOfMonth()->toDateString();
        $to = today()->toDateString();

        $salesCount = Sale::where('team_id', $tid)->validated()->between($from, $to)->count();
        $salesTotal = (float) Sale::where('team_id', $tid)->validated()->between($from, $to)->sum('total_ttc');
        $expenses = (float) Expense::where('team_id', $tid)->validated()->between($from, $to)->sum('amount');
        $payments = (float) Payment::where('team_id', $tid)->between($from, $to)->sum('amount');
        $procure = Procurement::where('team_id', $tid)->validated()->between($from, $to)->count();
        $lowStock = Article::where('team_id', $tid)->where('stock_qty', '<=', 10)->count();
        $cashInputs = (float) CashInput::where('team_id', $tid)->validated()->between($from, $to)->sum('amount');
        $cashDeposits = (float) CashDeposit::where('team_id', $tid)->validated()->between($from, $to)->sum('total_amount');
        $losses = Loss::where('team_id', $tid)->validated()->between($from, $to)->count();

        return "- Période : {$from} → {$to}\n"
            ."- Ventes validées : {$salesCount} (CA {$salesTotal} XAF)\n"
            ."- Charges : {$expenses} XAF\n"
            ."- Règlements clients reçus : {$payments} XAF\n"
            ."- Apports de fonds : {$cashInputs} XAF\n"
            ."- Versements banque : {$cashDeposits} XAF\n"
            ."- Approvisionnements validés : {$procure}\n"
            ."- Articles en stock faible (≤10 unités) : {$lowStock}\n"
            ."- Pertes déclarées : {$losses}";
    }
}

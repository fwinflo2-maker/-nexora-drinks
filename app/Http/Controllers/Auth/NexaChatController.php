<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LlmScrubberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NexaChatController extends Controller
{
    public function __construct(private readonly LlmScrubberService $scrubber) {}

    public function handleChat(Request $request)
    {
        $request->validate([
            'step' => 'required|integer',
            'intent' => 'nullable|string',
            'input' => 'required',
            'companyData' => 'nullable|array',
        ]);

        $apiKey = config('services.groq.key') ?: env('GROQ_API_KEY') ?: env('OPENAI_API_KEY');
        $baseUrl = config('services.groq.base_url') ?: env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1');
        $model = config('services.groq.model') ?: env('GROQ_MODEL', 'llama-3.3-70b-versatile');

        if (! $apiKey) {
            return response()->json(['message' => 'Clé GROQ manquante'], 503);
        }

        $step = $request->input('step');
        $intent = $request->input('intent', 'standard');
        $input = is_array($request->input('input')) ? json_encode($request->input('input')) : $request->input('input');
        $companyData = $request->input('companyData', []);

        $companyName = $companyData['name'] ?? null;
        $companySector = $companyData['sector'] ?? null;
        $companyCity = $companyData['city'] ?? null;

        $sectorKnowledge = match (true) {
            str_contains(strtolower((string) $companySector), 'boisson') ||
            str_contains(strtolower((string) $companySector), 'drink') ||
            str_contains(strtolower((string) $companySector), 'distribution') => 'spécialisé dans la distribution de boissons (brasseries, soft drinks, eaux minérales)',

            str_contains(strtolower((string) $companySector), 'aliment') ||
            str_contains(strtolower((string) $companySector), 'food') ||
            str_contains(strtolower((string) $companySector), 'agro') => 'dans l\'agro-alimentaire (épiceries, grossistes alimentaires, GMS)',

            str_contains(strtolower((string) $companySector), 'restau') => 'dans la restauration',

            default => 'dans le commerce B2B',
        };

        $systemPrompt = <<<'PROMPT'
Tu es NEXA ✦, l'assistant IA intelligent de NEXORA — la plateforme ERP conçue pour les PME africaines.
Tu guides les nouveaux clients lors de leur inscription avec chaleur, précision et professionnalisme.

Contexte produit :
- NEXORA couvre : gestion des stocks, ventes, approvisionnements, finance, rapports analytiques
- Modules spécialisés selon le secteur : distribution boissons, alimentaire, restauration, etc.
- Déployé en Afrique Centrale et de l'Ouest, adapté aux réalités locales (XAF, CFA, TVA locale)
- Interface multilingue, tableau de bord temps réel, gestion multi-dépôts

Ton comportement :
- Réponds TOUJOURS en français, 2-3 phrases maximum, ton chaleureux mais professionnel
- Adapte tes réponses au contexte de l'entreprise si connu
- Montre que tu comprends le secteur d'activité de l'entreprise
- Pas de markdown, pas de listes, texte simple et fluide
- Tu peux tutoyer ou vouvoyer mais reste cohérent
PROMPT;

        if ($intent === 'correction') {
            $ctx = $companyName ? " de {$companyName}" : '';
            $userPrompt = "L'utilisateur{$ctx} veut corriger une information de son inscription. Invite-le gentiment à préciser quel champ modifier (nom d'entreprise, secteur, ville, téléphone, email ou mot de passe) et dis-lui que c'est facile à corriger.";
        } else {
            switch ($step) {
                case 0:
                    $userPrompt = "Le client vient de saisir le nom de son entreprise : '{$input}'. Félicite-le chaleureusement pour ce nom, montre de l'enthousiasme, et demande-lui maintenant son secteur d'activité principal.";
                    break;

                case 1:
                    $name = $companyName ?? 'l\'entreprise';
                    $userPrompt = "L'utilisateur de {$name} indique son secteur : '{$input}'. Montre que tu connais bien ce secteur en Afrique et mentionne brièvement une fonctionnalité NEXORA adaptée. Puis demande la ville du siège.";
                    break;

                case 2:
                    $name = $companyName ?? 'l\'entreprise';
                    $sector = $companySector ? "({$sectorKnowledge})" : '';
                    $userPrompt = "L'utilisateur de {$name} {$sector} donne sa ville : '{$input}'. Confirme que NEXORA est bien présent dans cette région et demande maintenant le numéro de téléphone de contact.";
                    break;

                case 3:
                    $name = $companyName ?? 'l\'entreprise';
                    $city = $companyCity ?? 'votre ville';
                    $userPrompt = "L'utilisateur de {$name} basé à {$city} donne son téléphone : '{$input}'. Confirme brièvement et dis que l'étape finale est la création du compte administrateur (email + mot de passe sécurisé).";
                    break;

                default:
                    $ctx = $companyName ? " pour {$companyName}" : '';
                    $userPrompt = "L'utilisateur est à l'étape {$step} de la configuration{$ctx}. Encourage-le à continuer ou à corriger un champ si nécessaire.";
                    break;
            }
        }

        try {
            // Scrubber PII avant envoi au LLM externe
            $scrubbed = $this->scrubber->scrubPrompt($userPrompt);
            $cleanUserPrompt = $scrubbed['clean'];

            if ($scrubbed['had_pii']) {
                Log::channel('llm_audit')->info('LLM: PII détecté et masqué avant envoi Groq', [
                    'step' => $step,
                    'intent' => $intent,
                ]);
            }

            $verify = filter_var(config('services.groq.verify', true), FILTER_VALIDATE_BOOLEAN);
            $response = Http::withOptions(['verify' => $verify])->timeout(12)->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $cleanUserPrompt],
                ],
                'temperature' => 0.75,
                'max_tokens' => 220,
            ]);

            if ($response->successful()) {
                $reply = $response->json()['choices'][0]['message']['content'] ?? null;

                return response()->json(['reply' => trim((string) $reply)]);
            }

            $body = $response->json();
            $errorMessage = $body['error']['message'] ?? 'Erreur inconnue GROQ';
            Log::error('GROQ API Error', ['status' => $response->status(), 'body' => $body]);

            return response()->json([
                'message' => 'Erreur API GROQ: '.$errorMessage,
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('GROQ Exception: '.$e->getMessage());

            return response()->json(['message' => 'Erreur de connexion à GROQ: '.$e->getMessage()], 500);
        }
    }
}

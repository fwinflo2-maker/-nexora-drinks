<?php

namespace App\Http\Controllers\Api;

use App\Models\AgentConversation;
use App\Models\DashboardAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardAgentController
{
    /**
     * Liste les agents disponibles pour le tenant
     */
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $agents = DashboardAgent::where('team_id', $team->id)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'data' => $agents,
        ]);
    }

    /**
     * Récupère les conversations de l'utilisateur avec un agent
     */
    public function conversations(Request $request, DashboardAgent $agent): JsonResponse
    {
        $conversations = AgentConversation::where('agent_id', $agent->id)
            ->where('user_id', $request->user()->id)
            ->with('messages')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $conversations->items(),
            'meta' => [
                'total' => $conversations->total(),
                'current_page' => $conversations->currentPage(),
            ],
        ]);
    }

    /**
     * Crée une nouvelle conversation avec un agent
     */
    public function createConversation(Request $request, DashboardAgent $agent): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'context' => 'nullable|array',
        ]);

        $conversation = AgentConversation::create([
            'team_id' => $request->user()->current_team_id,
            'user_id' => $request->user()->id,
            'agent_id' => $agent->id,
            'title' => $request->title ?? 'Nouvelle conversation - '.now()->format('d/m/y H:i'),
            'context' => $request->context,
        ]);

        return response()->json([
            'data' => $conversation,
            'message' => 'Conversation créée',
        ], 201);
    }

    /**
     * Envoie un message à un agent et reçoit une réponse IA
     */
    public function sendMessage(Request $request, AgentConversation $conversation): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        // Ajouter le message de l'utilisateur
        $userMessage = $conversation->addMessage('user', $request->content);

        // Appeler l'API IA (OpenAI, Claude, etc.)
        $agentResponse = $this->callAIAgent(
            $conversation->agent,
            $conversation,
            $request->content
        );

        // Sauvegarder la réponse
        $agentMessage = $conversation->addMessage('agent', $agentResponse['content'], [
            'model' => $agentResponse['model'] ?? null,
            'tokens_used' => $agentResponse['tokens'] ?? null,
        ]);

        // Mettre à jour le compteur et la date
        $conversation->update([
            'message_count' => $conversation->message_count + 2,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'user_message' => $userMessage,
                'agent_message' => $agentMessage,
            ],
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Appelle l'API IA avec le prompt du dashboard agent
     * Implémentation avec OpenAI/Claude
     */
    private function callAIAgent(DashboardAgent $agent, AgentConversation $conversation, string $userMessage): array
    {
        $systemPrompt = $agent->system_prompt;

        // Construire l'historique de contexte
        $recentMessages = $conversation->messages()
            ->where('created_at', '>=', now()->subHours(1))
            ->limit(10)
            ->get()
            ->map(fn ($msg) => [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->content,
            ])
            ->toArray();

        // Vérifier quelle API utiliser
        $openaiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        $anthropicKey = config('services.anthropic.api_key') ?? env('ANTHROPIC_API_KEY');

        // Priorité à OpenAI si disponible, sinon Claude, sinon fallback mock
        if ($openaiKey) {
            return $this->callOpenAI($agent, $systemPrompt, $recentMessages, $userMessage, $openaiKey);
        }

        if ($anthropicKey) {
            return $this->callAnthropic($agent, $systemPrompt, $recentMessages, $userMessage, $anthropicKey);
        }

        // Fallback: retourner réponse mock intelligente
        Log::info('Aucune clé API configurée, utilisation du mode mock pour l\'agent IA', [
            'agent_id' => $agent->id,
            'agent_name' => $agent->agent_name,
        ]);

        return [
            'content' => $this->generateMockResponse($agent, $userMessage),
            'model' => 'mock',
            'tokens' => 0,
        ];
    }

    /**
     * Appel à l'API OpenAI
     */
    private function callOpenAI(DashboardAgent $agent, string $systemPrompt, array $recentMessages, string $userMessage, string $apiKey): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$recentMessages,
            ['role' => 'user', 'content' => $userMessage],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer de réponse.';
                $tokens = $data['usage']['total_tokens'] ?? 0;

                Log::info('Réponse OpenAI reçue avec succès', [
                    'agent_id' => $agent->id,
                    'tokens_used' => $tokens,
                ]);

                return [
                    'content' => $content,
                    'model' => 'gpt-4o-mini',
                    'tokens' => $tokens,
                ];
            }

            Log::error('Erreur API OpenAI', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Erreur OpenAI: '.$response->status());
        } catch (\Exception $e) {
            Log::error('Échec appel OpenAI', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id,
            ]);

            // Fallback sur mock en cas d'erreur
            return [
                'content' => $this->generateMockResponse($agent, $userMessage).' [Mode dégradé - erreur API]',
                'model' => 'mock-fallback',
                'tokens' => 0,
            ];
        }
    }

    /**
     * Appel à l'API Anthropic (Claude)
     */
    private function callAnthropic(DashboardAgent $agent, string $systemPrompt, array $recentMessages, string $userMessage, string $apiKey): array
    {
        // Convertir le format pour Anthropic
        $messages = collect($recentMessages)
            ->filter(fn ($msg) => $msg['role'] !== 'system')
            ->values()
            ->toArray();

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 1000,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['content'][0]['text'] ?? 'Désolé, je n\'ai pas pu générer de réponse.';
                $inputTokens = $data['usage']['input_tokens'] ?? 0;
                $outputTokens = $data['usage']['output_tokens'] ?? 0;
                $tokens = $inputTokens + $outputTokens;

                Log::info('Réponse Anthropic reçue avec succès', [
                    'agent_id' => $agent->id,
                    'tokens_used' => $tokens,
                ]);

                return [
                    'content' => $content,
                    'model' => 'claude-3-haiku',
                    'tokens' => $tokens,
                ];
            }

            Log::error('Erreur API Anthropic', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Erreur Anthropic: '.$response->status());
        } catch (\Exception $e) {
            Log::error('Échec appel Anthropic', [
                'error' => $e->getMessage(),
                'agent_id' => $agent->id,
            ]);

            // Fallback sur mock en cas d'erreur
            return [
                'content' => $this->generateMockResponse($agent, $userMessage).' [Mode dégradé - erreur API]',
                'model' => 'mock-fallback',
                'tokens' => 0,
            ];
        }
    }

    /**
     * Génère une réponse mock basée sur le rôle de l'agent
     */
    private function generateMockResponse(DashboardAgent $agent, string $userMessage): string
    {
        $responses = [
            'admin' => "Analyse demandée pour l'administration. Je recommande de consulter les rapports mensuels et d'ajuster les paramètres système selon vos besoins.",
            'comptable' => 'Sur le plan financier, les données montrent une tendance positive. Consultez les rapports détaillés pour les ajustements fiscaux.',
            'magasinier' => "Pour l'entrepôt, je suggère de vérifier les niveaux de stock et de planifier les mouvements pour la semaine prochaine.",
            'commercial' => 'Excellente question commerciale. Analysez les tendances de vente et focalisez sur les clients à haut potentiel.',
            'logisticien' => 'Pour les tournées, optimisez les itinéraires et réduisez les distances. Vérifiez la capacité des véhicules.',
        ];

        return $responses[$agent->role] ?? 'Je suis prêt à vous aider avec '.$agent->agent_name.'. Comment puis-je vous assister?';
    }
}

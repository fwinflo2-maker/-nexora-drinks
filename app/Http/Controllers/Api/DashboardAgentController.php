<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentConversation;
use App\Models\DashboardAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardAgentController extends Controller
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
        $team = $request->user()->currentTeam;
        abort_if($agent->team_id !== $team?->id, 403);

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
        $team = $request->user()->currentTeam;
        abort_if($agent->team_id !== $team?->id, 403);

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
        abort_if($conversation->user_id !== $request->user()->id, 403);

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
     * Utilise OpenAI (gpt-4o-mini) ou Anthropic (claude-3-haiku) selon les clés disponibles
     */
    private function callAIAgent(DashboardAgent $agent, AgentConversation $conversation, string $userMessage): array
    {
        $systemPrompt = $agent->system_prompt;

        // Construire l'historique de contexte
        $messages = $conversation->messages()
            ->limit(20)
            ->get()
            ->map(fn ($msg) => [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->content,
            ])
            ->toArray();

        // Ajouter le message courant
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        // Essayer OpenAI d'abord
        if (config('services.openai.api_key')) {
            return $this->callOpenAI($systemPrompt, $messages);
        }

        // Sinon essayer Anthropic
        if (config('services.anthropic.api_key')) {
            return $this->callAnthropic($systemPrompt, $messages);
        }

        // Mode fallback mock si aucune clé n'est disponible
        return [
            'content' => $this->generateMockResponse($agent, $userMessage),
            'model' => 'mock',
            'tokens' => 150,
        ];
    }

    /**
     * Appelle l'API OpenAI (gpt-4o-mini)
     */
    private function callOpenAI(string $systemPrompt, array $messages): array
    {
        try {
            $response = Http::withToken(config('services.openai.api_key'))
                ->withHeader('Content-Type', 'application/json')
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        ...$messages,
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API error: '.$response->body());

                return [
                    'content' => 'Désolé, une erreur est survenue lors de la communication avec OpenAI. Veuillez réessayer.',
                    'model' => 'gpt-4o-mini',
                    'tokens' => 0,
                ];
            }

            $data = $response->json();

            return [
                'content' => $data['choices'][0]['message']['content'] ?? 'Pas de réponse',
                'model' => 'gpt-4o-mini',
                'tokens' => $data['usage']['total_tokens'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI API exception: '.$e->getMessage());

            return [
                'content' => 'Désolé, une erreur est survenue lors de la communication avec OpenAI. Veuillez réessayer.',
                'model' => 'gpt-4o-mini',
                'tokens' => 0,
            ];
        }
    }

    /**
     * Appelle l'API Anthropic (claude-3-haiku)
     */
    private function callAnthropic(string $systemPrompt, array $messages): array
    {
        try {
            $response = Http::withToken(config('services.anthropic.api_key'))
                ->withHeader('x-api-version', '2023-06-01')
                ->withHeader('Content-Type', 'application/json')
                ->post('https://api.anthropic.com/v1/messages', [
                    'model' => 'claude-3-5-haiku-20241022',
                    'max_tokens' => 2000,
                    'system' => $systemPrompt,
                    'messages' => $messages,
                ]);

            if ($response->failed()) {
                Log::error('Anthropic API error: '.$response->body());

                return [
                    'content' => 'Désolé, une erreur est survenue lors de la communication avec Anthropic. Veuillez réessayer.',
                    'model' => 'claude-3-haiku',
                    'tokens' => 0,
                ];
            }

            $data = $response->json();

            return [
                'content' => $data['content'][0]['text'] ?? 'Pas de réponse',
                'model' => 'claude-3-haiku',
                'tokens' => ($data['usage']['output_tokens'] ?? 0) + ($data['usage']['input_tokens'] ?? 0),
            ];
        } catch (\Exception $e) {
            Log::error('Anthropic API exception: '.$e->getMessage());

            return [
                'content' => 'Désolé, une erreur est survenue lors de la communication avec Anthropic. Veuillez réessayer.',
                'model' => 'claude-3-haiku',
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

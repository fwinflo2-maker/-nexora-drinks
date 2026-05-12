<?php

namespace App\Http\Controllers\Api;

use App\Models\AgentConversation;
use App\Models\DashboardAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        // Mock implementation - remplacer par vraie API IA
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

        // TODO: Implémenter appel réel OpenAI/Claude
        // Pour maintenant, retourner réponse mock intelligente

        return [
            'content' => $this->generateMockResponse($agent, $userMessage),
            'model' => 'gpt-4',
            'tokens' => 150,
        ];
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

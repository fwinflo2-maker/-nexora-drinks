import { motion } from 'framer-motion';
import { Zap, MessageCircle, Plus, MessageSquare, Send, Loader } from 'lucide-react';
import { useState } from 'react';
import { HoverButton } from '@/components/ui/hover-button';

export function DashboardAgentSection({ teamSlug, userRole }: { teamSlug: string; userRole: string }) {
    const [isOpen, setIsOpen] = useState(false);
    const [agent, setAgent] = useState<any>(null);
    const [conversations, setConversations] = useState<any[]>([]);
    const [activeConversation, setActiveConversation] = useState<any>(null);
    const [messages, setMessages] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [sending, setSending] = useState(false);
    const [inputValue, setInputValue] = useState('');

    const agentDescriptions: Record<string, { name: string; prompt: string; emoji: string }> = {
        admin: {
            name: 'Agent Administratif',
            emoji: '⚙️',
            prompt: 'Vous êtes un expert en gestion entreprise aide l\'administrateur avec les configs, les statistiques globales, et les insights métier.'
        },
        comptable: {
            name: 'Agent Financier',
            emoji: '💰',
            prompt: 'Vous êtes un expert comptable IA qui aide à analyser les finances, générer des rapports, et identifier les tendances.'
        },
        magasinier: {
            name: 'Agent Entrepôt',
            emoji: '📦',
            prompt: 'Vous êtes un expert en gestion d\'entrepôt qui optimise les stocks, prépare les consignes et gère les mouvements.'
        },
        commercial: {
            name: 'Agent Commercial',
            emoji: '📊',
            prompt: 'Vous êtes un expert commercial qui analyse les ventes, suggère des optimisations, et aide avec la CRM.'
        },
        logisticien: {
            name: 'Agent Logistique',
            emoji: '🚚',
            prompt: 'Vous êtes un expert logistique qui optimise tournées, livraisons et collectes.'
        },
    };

    const loadAgent = async () => {
        setLoading(true);

        try {
            const response = await fetch(`/api/v1/dashboard-agents`, {
                headers: { 'Accept': 'application/json', 'X-Team-Slug': teamSlug }
            });
            const result = await response.json();
            const agentData = result.data.find((a: any) => a.role === userRole);
            setAgent(agentData);
            
            if (agentData) {
                await loadConversations(agentData.id);
            }
        } catch (error) {
            console.error('Erreur chargement agent:', error);
        } finally {
            setLoading(false);
        }
    };

    const loadConversations = async (agentId: number) => {
        try {
            const response = await fetch(`/api/v1/dashboard-agents/${agentId}/conversations`, {
                headers: { 'Accept': 'application/json', 'X-Team-Slug': teamSlug }
            });
            const result = await response.json();
            setConversations(result.data || []);
        } catch (error) {
            console.error('Erreur chargement conversations:', error);
        }
    };

    const createConversation = async () => {
        if (!agent) {
return;
}

        setSending(true);

        try {
            const response = await fetch(`/api/v1/dashboard-agents/${agent.id}/conversations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Team-Slug': teamSlug,
                },
                body: JSON.stringify({
                    title: null,
                    context: {},
                }),
            });
            const result = await response.json();
            setActiveConversation(result.data);
            setMessages([]);
        } catch (error) {
            console.error('Erreur création conversation:', error);
        } finally {
            setSending(false);
        }
    };

    const sendMessage = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!inputValue.trim() || !activeConversation) {
return;
}

        setSending(true);
        const userMessage = inputValue;
        setInputValue('');

        try {
            const response = await fetch(
                `/api/v1/dashboard-agents/conversations/${activeConversation.id}/messages`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Team-Slug': teamSlug,
                    },
                    body: JSON.stringify({ content: userMessage }),
                }
            );
            const result = await response.json();
            setMessages([
                ...messages,
                {
                    id: result.data.user_message.id,
                    sender: 'user',
                    content: userMessage,
                    created_at: new Date().toISOString(),
                },
                {
                    id: result.data.agent_message.id,
                    sender: 'agent',
                    content: result.data.agent_message.content,
                    created_at: new Date().toISOString(),
                },
            ]);
        } catch (error) {
            console.error('Erreur envoi message:', error);
        } finally {
            setSending(false);
        }
    };

    const toggleSection = () => {
        if (!isOpen) {
            loadAgent();
        }

        setIsOpen(!isOpen);
    };

    const agentInfo = agentDescriptions[userRole as keyof typeof agentDescriptions];

    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="rounded-2xl border-2 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 border-amber-300 dark:border-amber-700 p-6 shadow-lg"
        >
            {!activeConversation ? (
                // Vue agent inactif
                <div
                    onClick={toggleSection}
                    className="flex items-center justify-between cursor-pointer group"
                >
                    <div className="flex items-center gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-800 dark:to-orange-800 group-hover:scale-110 transition-transform">
                            <Zap className="h-6 w-6 text-amber-700 dark:text-amber-200" />
                        </div>
                        <div>
                            <h3 className="text-lg font-bold text-amber-950 dark:text-amber-100">
                                {agentInfo?.emoji} {agentInfo?.name}
                            </h3>
                            <p className="text-sm text-amber-700 dark:text-amber-300">
                                Assistez-moi pour optimiser votre opération quotidienne
                            </p>
                        </div>
                    </div>
                </div>
            ) : (
                // Vue chat actif
                <div className="flex flex-col h-96 gap-4">
                    <div className="flex items-center justify-between">
                        <h3 className="font-semibold text-amber-950 dark:text-amber-100">
                            {agentInfo?.emoji} Conversation avec {agentInfo?.name}
                        </h3>
                        <button
                            onClick={() => {
                                setActiveConversation(null);
                                setMessages([]);
                            }}
                            className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                        >
                            Fermer
                        </button>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 overflow-y-auto space-y-4 bg-white dark:bg-slate-900 rounded-lg p-4 border border-amber-200/50 dark:border-amber-700/50">
                        {messages.map((msg, idx) => (
                            <motion.div
                                key={idx}
                                initial={{ opacity: 0, y: 10 }}
                                animate={{ opacity: 1, y: 0 }}
                                className={`flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}`}
                            >
                                <div
                                    className={`max-w-xs px-4 py-2 rounded-lg ${
                                        msg.sender === 'user'
                                            ? 'bg-amber-600 text-white rounded-br-none'
                                            : 'bg-amber-100 dark:bg-amber-900/30 text-foreground rounded-bl-none'
                                    }`}
                                >
                                    <p className="text-sm">{msg.content}</p>
                                </div>
                            </motion.div>
                        ))}
                        {sending && (
                            <div className="flex justify-start">
                                <div className="bg-amber-100 dark:bg-amber-900/30 px-4 py-2 rounded-lg rounded-bl-none">
                                    <Loader className="h-4 w-4 animate-spin" />
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Input formulaire */}
                    <form onSubmit={sendMessage} className="flex gap-2">
                        <input
                            type="text"
                            value={inputValue}
                            onChange={(e) => setInputValue(e.target.value)}
                            placeholder="Posez votre question..."
                            className="flex-1 px-4 py-2 rounded-lg border border-amber-200 dark:border-amber-700 bg-white dark:bg-slate-900 text-foreground focus:outline-none focus:ring-2 focus:ring-amber-500"
                        />
                        <button
                            type="submit"
                            disabled={!inputValue.trim() || sending}
                            className="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white transition-colors disabled:opacity-50 flex items-center gap-2"
                        >
                            <Send className="h-4 w-4" />
                        </button>
                    </form>
                </div>
            )}

            {isOpen && !activeConversation && (
                <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: 'auto' }}
                    exit={{ opacity: 0, height: 0 }}
                    className="mt-6 border-t border-amber-200 dark:border-amber-700 pt-6 space-y-4"
                >
                    {loading ? (
                        <div className="text-center py-8">
                            <Loader className="h-6 w-6 animate-spin mx-auto text-amber-600" />
                        </div>
                    ) : (
                        <>
                            <p className="text-sm text-amber-800 dark:text-amber-200">
                                {agentInfo?.prompt}
                            </p>

                            {conversations.length > 0 && (
                                <div className="space-y-2">
                                    <p className="text-xs font-semibold text-amber-900 dark:text-amber-200">Conversations récentes:</p>
                                    {conversations.slice(0, 3).map((conv) => (
                                        <button
                                            key={conv.id}
                                            onClick={() => setActiveConversation(conv)}
                                            className="w-full text-left p-3 rounded-lg bg-white dark:bg-slate-800 border border-amber-200/50 dark:border-amber-700/50 hover:shadow-md transition-all flex items-start gap-2"
                                        >
                                            <MessageSquare className="h-4 w-4 text-amber-600 dark:text-amber-400 mt-1 flex-shrink-0" />
                                            <div className="flex-1 truncate">
                                                <p className="text-sm font-medium text-foreground truncate">{conv.title}</p>
                                                <p className="text-xs text-muted-foreground">{conv.message_count} messages</p>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}

                            <HoverButton
                                onClick={createConversation}
                                disabled={sending}
                                className="w-full flex items-center justify-center gap-2"
                            >
                                <Plus className="h-4 w-4" />
                                Démarrer nouvelle conversation
                            </HoverButton>
                        </>
                    )}
                </motion.div>
            )}
        </motion.div>
    );
}

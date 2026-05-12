import { motion } from 'framer-motion';
import { Send, Download, MessageSquare, Clock, Sparkles, Bot } from 'lucide-react';
import { useState } from 'react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };

interface Message {
    id: string;
    sender: 'user' | 'agent';
    content: string;
    timestamp: string;
}

interface Conversation {
    id: string;
    title: string;
    lastMessageAt: string;
    messageCount: number;
    messages: Message[];
}

const simulatedResponses: Record<string, string> = {
    default: "Bonjour ! Je suis votre assistant IA NEXORA. Je peux analyser vos données de distribution, ventes, stocks et consignations. Comment puis-je vous aider ?",
    stock: "D'après les données actuelles, vous avez des alertes de stock sur plusieurs produits. Je vous recommande de passer commande auprès de votre fournisseur principal dans les 48h.",
    ca: "Le chiffre d'affaires de ce mois montre une tendance positive. Les zones Akwa et Deido sont vos meilleures performers.",
    livraison: "Toutes vos tournées actives progressent normalement. Le taux de livraison est conforme aux objectifs.",
};

function getResponse(msg: string): string {
    if (msg.toLowerCase().includes('stock')) {
return simulatedResponses.stock;
}

    if (msg.toLowerCase().includes('ca') || msg.toLowerCase().includes('chiffre')) {
return simulatedResponses.ca;
}

    if (msg.toLowerCase().includes('livraison') || msg.toLowerCase().includes('tournée')) {
return simulatedResponses.livraison;
}

    return simulatedResponses.default;
}

const initialMessages: Message[] = [
    {
        id: 'msg_1',
        sender: 'user',
        content: 'Quel est notre chiffre d\'affaires ce mois ?',
        timestamp: '14:20',
    },
    {
        id: 'msg_2',
        sender: 'agent',
        content: 'Basé sur les données disponibles, votre chiffre d\'affaires pour le mois actuel est de 45.2M XAF, en hausse de 12% par rapport au mois précédent. Les zones d\'Akwa et Bonanjo sont vos principaux moteurs.',
        timestamp: '14:21',
    },
    {
        id: 'msg_3',
        sender: 'user',
        content: 'Quels sont les clients débiteurs prioritaires ?',
        timestamp: '14:22',
    },
    {
        id: 'msg_4',
        sender: 'agent',
        content: 'Voici les 5 clients avec le plus haut solde négatif :\n\n1. Cave Royale — 2.5M XAF (30 jours)\n2. Cave Akwa Palace — 1.8M XAF (25 jours)\n3. Super U Bonanjo — 1.2M XAF (20 jours)\n4. Restaurant Le Wouri — 890K XAF (15 jours)\n5. Boulangerie Saker — 450K XAF (10 jours)\n\nJe recommande une relance immédiate auprès des 3 premiers pour éviter un impayé.',
        timestamp: '14:23',
    },
];

export default function AdminAgent({ userName, teamName }: { userName: string; teamName?: string }) {
    const [selectedConversationId, setSelectedConversationId] = useState<string | null>('conv_1');
    const [messageInput, setMessageInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const [conversations, setConversations] = useState<Conversation[]>([
        {
            id: 'conv_1',
            title: 'Analytics Q1 2026',
            lastMessageAt: 'Aujourd\'hui à 14:32',
            messageCount: 4,
            messages: initialMessages,
        },
        {
            id: 'conv_2',
            title: 'Optimisation logistique',
            lastMessageAt: 'Hier à 09:15',
            messageCount: 8,
            messages: [],
        },
        {
            id: 'conv_3',
            title: 'Rapport de paie',
            lastMessageAt: '27 Avril à 16:45',
            messageCount: 5,
            messages: [],
        },
        {
            id: 'conv_4',
            title: 'Alerte stock critique',
            lastMessageAt: '26 Avril à 11:20',
            messageCount: 3,
            messages: [],
        },
    ]);

    const selectedConversation = conversations.find(c => c.id === selectedConversationId) ?? null;
    const messages = selectedConversation?.messages ?? [];

    const handleSendMessage = async () => {
        if (!messageInput.trim() || !selectedConversationId) {
return;
}

        const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const userMsg: Message = {
            id: `msg_${Date.now()}`,
            sender: 'user',
            content: messageInput,
            timestamp: now,
        };
        const inputText = messageInput;
        setMessageInput('');
        setIsLoading(true);

        setConversations(prev => prev.map(c =>
            c.id === selectedConversationId
                ? { ...c, messages: [...c.messages, userMsg], messageCount: c.messageCount + 1, lastMessageAt: 'À l\'instant' }
                : c
        ));

        setTimeout(() => {
            const agentMsg: Message = {
                id: `msg_${Date.now() + 1}`,
                sender: 'agent',
                content: getResponse(inputText),
                timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
            };
            setConversations(prev => prev.map(c =>
                c.id === selectedConversationId
                    ? { ...c, messages: [...c.messages, agentMsg], messageCount: c.messageCount + 1 }
                    : c
            ));
            setIsLoading(false);
        }, 1500);
    };

    const handleNewConversation = () => {
        const newId = `conv_${Date.now()}`;
        const now = new Date();
        const dateLabel = now.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
        setConversations(prev => [
            {
                id: newId,
                title: `Conversation du ${dateLabel}`,
                lastMessageAt: 'À l\'instant',
                messageCount: 0,
                messages: [],
            },
            ...prev,
        ]);
        setSelectedConversationId(newId);
    };

    return (
        <div className="flex flex-col gap-6">
            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <div>
                    <div className="flex items-center gap-3 mb-2">
                        <div className="h-3 w-3 rounded-full bg-emerald-500 animate-pulse" />
                        <h2 className="text-2xl font-bold tracking-tight text-foreground">
                            Agent Administratif IA
                        </h2>
                    </div>
                    <p className="text-sm text-muted-foreground">
                        Posez des questions sur vos KPIs, analytics et décisions stratégiques
                    </p>
                </div>
            </motion.div>

            {/* Main Chat Layout */}
            <motion.div
                variants={item}
                initial="hidden"
                animate="visible"
                className="grid grid-cols-1 lg:grid-cols-4 gap-4 h-[600px]"
            >
                {/* Conversations Sidebar */}
                <div className="lg:col-span-1 rounded-2xl border border-border bg-card flex flex-col overflow-hidden">
                    <div className="p-4 border-b border-border flex items-center justify-between">
                        <h3 className="font-semibold text-foreground">Historique</h3>
                        <button
                            onClick={handleNewConversation}
                            className="p-1.5 rounded-lg hover:bg-secondary transition-colors text-primary"
                            title="Nouvelle conversation"
                        >
                            <MessageSquare className="h-4 w-4" />
                        </button>
                    </div>
                    <div className="flex-1 overflow-y-auto space-y-1 p-2">
                        {conversations.map(conv => (
                            <button
                                key={conv.id}
                                onClick={() => setSelectedConversationId(conv.id)}
                                className={`w-full text-left px-3 py-2.5 rounded-lg transition-all text-sm ${
                                    selectedConversationId === conv.id
                                        ? 'bg-primary/10 border border-primary text-foreground'
                                        : 'text-muted-foreground hover:bg-secondary border border-transparent'
                                }`}
                            >
                                <p className="font-medium truncate">{conv.title}</p>
                                <div className="flex items-center gap-1.5 mt-1 text-[10px]">
                                    <Clock className="h-3 w-3" />
                                    <span>{conv.lastMessageAt}</span>
                                </div>
                            </button>
                        ))}
                    </div>
                </div>

                {/* Chat Area */}
                <div className="lg:col-span-3 rounded-2xl border border-border bg-card flex flex-col overflow-hidden">
                    {selectedConversationId === null ? (
                        /* Empty state — no conversation selected */
                        <div className="flex-1 flex flex-col items-center justify-center text-center p-8">
                            <Bot className="h-14 w-14 text-muted-foreground/30 mb-4" />
                            <h4 className="text-foreground font-semibold mb-1">Aucune conversation sélectionnée</h4>
                            <p className="text-sm text-muted-foreground">
                                Sélectionnez ou créez une conversation pour commencer
                            </p>
                        </div>
                    ) : (
                        <>
                            {/* Messages */}
                            <div className="flex-1 overflow-y-auto p-4 space-y-4">
                                {messages.length === 0 ? (
                                    <div className="h-full flex flex-col items-center justify-center text-center">
                                        <Sparkles className="h-12 w-12 text-muted-foreground/30 mb-3" />
                                        <h4 className="text-foreground font-semibold mb-1">Nouvelle conversation</h4>
                                        <p className="text-sm text-muted-foreground">
                                            Posez une question pour commencer la conversation
                                        </p>
                                    </div>
                                ) : (
                                    messages.map(msg => (
                                        <motion.div
                                            key={msg.id}
                                            initial={{ opacity: 0, y: 8 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            className={`flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}`}
                                        >
                                            <div
                                                className={`max-w-xs lg:max-w-sm px-4 py-3 rounded-2xl ${
                                                    msg.sender === 'user'
                                                        ? 'bg-primary text-primary-foreground rounded-br-none'
                                                        : 'bg-secondary text-foreground rounded-bl-none'
                                                }`}
                                            >
                                                <p className="text-sm whitespace-pre-line">{msg.content}</p>
                                                <p className={`text-xs mt-1.5 ${msg.sender === 'user' ? 'text-primary-foreground/70' : 'text-muted-foreground'}`}>
                                                    {msg.timestamp}
                                                </p>
                                            </div>
                                        </motion.div>
                                    ))
                                )}
                                {isLoading && (
                                    <div className="flex justify-start">
                                        <div className="px-4 py-3 rounded-2xl bg-secondary flex gap-1.5 items-center">
                                            <div className="h-2 w-2 rounded-full bg-muted-foreground animate-bounce" />
                                            <div className="h-2 w-2 rounded-full bg-muted-foreground animate-bounce" style={{ animationDelay: '0.1s' }} />
                                            <div className="h-2 w-2 rounded-full bg-muted-foreground animate-bounce" style={{ animationDelay: '0.2s' }} />
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Input Area */}
                            <div className="border-t border-border p-4">
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        value={messageInput}
                                        onChange={(e) => setMessageInput(e.target.value)}
                                        onKeyDown={(e) => e.key === 'Enter' && !e.shiftKey && handleSendMessage()}
                                        placeholder="Posez une question..."
                                        disabled={isLoading}
                                        className="flex-1 px-4 py-2.5 rounded-lg border border-border bg-card text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary disabled:opacity-50"
                                    />
                                    <button
                                        onClick={handleSendMessage}
                                        disabled={isLoading || !messageInput.trim()}
                                        className="p-2.5 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <Send className="h-4 w-4" />
                                    </button>
                                    <button className="p-2.5 rounded-lg text-muted-foreground hover:bg-secondary transition-colors">
                                        <Download className="h-4 w-4" />
                                    </button>
                                </div>
                                <p className="text-[10px] text-muted-foreground mt-2">
                                    Astuce : posez des questions sur le CA, les tendances, les alertes stock...
                                </p>
                            </div>
                        </>
                    )}
                </div>
            </motion.div>

            {/* Quick Questions */}
            <motion.div
                variants={item}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card p-4"
            >
                <p className="text-xs font-semibold text-muted-foreground mb-3 uppercase tracking-wider">Questions rapides</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                    {[
                        'Quel est notre CA ce mois ?',
                        'Top 5 clients par zone ?',
                        'Clients débiteurs ?',
                        'Tendances stock ?',
                    ].map((question, i) => (
                        <button
                            key={i}
                            onClick={() => {
                                setMessageInput(question);

                                if (selectedConversationId === null && conversations.length > 0) {
                                    setSelectedConversationId(conversations[0].id);
                                }
                            }}
                            className="text-left px-3 py-2 rounded-lg text-xs text-foreground bg-secondary hover:bg-secondary/70 transition-colors truncate"
                        >
                            {question}
                        </button>
                    ))}
                </div>
            </motion.div>
        </div>
    );
}

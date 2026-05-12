import { usePage } from '@inertiajs/react';
import { Bot, Send, X, ChevronDown, Loader2, MessageSquare } from 'lucide-react';
import React, { useRef, useState, useEffect } from 'react';
import type { Team } from '@/types';

interface Message {
    role: 'user' | 'assistant';
    content: string;
}

export default function DrinksChatWidget() {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const [open, setOpen] = useState(false);
    const [input, setInput] = useState('');
    const [messages, setMessages] = useState<Message[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const endRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        if (open) {
            endRef.current?.scrollIntoView({ behavior: 'smooth' });
            inputRef.current?.focus();
        }
    }, [messages, open]);

    const send = async () => {
        const text = input.trim();

        if (!text || loading) {
return;
}

        const userMsg: Message = { role: 'user', content: text };
        setMessages(prev => [...prev, userMsg]);
        setInput('');
        setLoading(true);
        setError(null);

        try {
            const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

            const res = await fetch(route('drinks.agent.chat', { current_team: team.slug }), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    history: messages.slice(-10),
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                setError(data.error ?? 'Erreur inattendue.');
            } else {
                setMessages(prev => [...prev, { role: 'assistant', content: data.reply }]);
            }
        } catch {
            setError('Impossible de joindre le serveur.');
        } finally {
            setLoading(false);
        }
    };

    const handleKey = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !e.shiftKey) {
 e.preventDefault(); send(); 
}
    };

    return (
        <div className="fixed bottom-6 right-6 z-50">
            {/* Bubble */}
            {!open && (
                <button
                    onClick={() => setOpen(true)}
                    className="w-12 h-12 rounded-full bg-amber-500 hover:bg-amber-400 shadow-lg flex items-center justify-center transition-colors"
                    title="Assistant IA"
                >
                    <Bot className="h-5 w-5 text-white" />
                </button>
            )}

            {/* Panel */}
            {open && (
                <div className="flex flex-col w-80 h-[480px] rounded-2xl border border-border bg-card shadow-2xl overflow-hidden">
                    {/* Header */}
                    <div className="flex items-center gap-2.5 px-4 py-3 bg-amber-500/10 border-b border-border flex-shrink-0">
                        <div className="w-7 h-7 rounded-full bg-amber-500/20 flex items-center justify-center">
                            <Bot className="h-4 w-4 text-amber-400" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-semibold text-foreground">Assistant IA</p>
                            <p className="text-[10px] text-muted-foreground truncate">{team.name}</p>
                        </div>
                        <button
                            onClick={() => setOpen(false)}
                            className="p-1 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
                        >
                            <ChevronDown className="h-4 w-4" />
                        </button>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-3">
                        {messages.length === 0 && (
                            <div className="flex flex-col items-center justify-center h-full gap-3 text-center">
                                <MessageSquare className="h-8 w-8 text-muted-foreground/40" />
                                <p className="text-xs text-muted-foreground">
                                    Posez-moi vos questions sur l'activité du mois : ventes, charges, stock, etc.
                                </p>
                                <div className="flex flex-col gap-1.5 w-full">
                                    {[
                                        'Comment se portent les ventes ce mois ?',
                                        'Quels articles sont en rupture de stock ?',
                                        'Résume les flux financiers du mois.',
                                    ].map(q => (
                                        <button
                                            key={q}
                                            onClick={() => {
 setInput(q); inputRef.current?.focus(); 
}}
                                            className="text-left text-xs px-3 py-2 rounded-lg bg-muted hover:bg-muted/70 text-muted-foreground hover:text-foreground transition-colors"
                                        >
                                            {q}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}

                        {messages.map((msg, i) => (
                            <div key={i} className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                                {msg.role === 'assistant' && (
                                    <div className="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center mr-2 mt-0.5 flex-shrink-0">
                                        <Bot className="h-3 w-3 text-amber-400" />
                                    </div>
                                )}
                                <div className={`max-w-[85%] rounded-xl px-3 py-2 text-xs leading-relaxed whitespace-pre-wrap ${
                                    msg.role === 'user'
                                        ? 'bg-amber-500 text-white rounded-br-sm'
                                        : 'bg-muted text-foreground rounded-bl-sm'
                                }`}>
                                    {msg.content}
                                </div>
                            </div>
                        ))}

                        {loading && (
                            <div className="flex justify-start">
                                <div className="w-5 h-5 rounded-full bg-amber-500/20 flex items-center justify-center mr-2 mt-0.5 flex-shrink-0">
                                    <Bot className="h-3 w-3 text-amber-400" />
                                </div>
                                <div className="bg-muted rounded-xl rounded-bl-sm px-3 py-2">
                                    <Loader2 className="h-3.5 w-3.5 text-muted-foreground animate-spin" />
                                </div>
                            </div>
                        )}

                        {error && (
                            <div className="rounded-lg bg-red-500/10 border border-red-500/20 px-3 py-2 text-xs text-red-400">
                                {error}
                            </div>
                        )}

                        <div ref={endRef} />
                    </div>

                    {/* Input */}
                    <div className="px-3 pb-3 flex-shrink-0 border-t border-border pt-3">
                        <div className="flex items-center gap-2 bg-muted rounded-xl px-3 py-1.5">
                            <input
                                ref={inputRef}
                                value={input}
                                onChange={e => setInput(e.target.value)}
                                onKeyDown={handleKey}
                                placeholder="Posez votre question…"
                                className="flex-1 bg-transparent text-xs text-foreground placeholder:text-muted-foreground outline-none"
                                disabled={loading}
                            />
                            <button
                                onClick={send}
                                disabled={!input.trim() || loading}
                                className="p-1.5 rounded-lg text-amber-500 hover:bg-amber-500/10 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                            >
                                <Send className="h-3.5 w-3.5" />
                            </button>
                        </div>
                        <p className="text-[10px] text-muted-foreground text-center mt-1.5">
                            Données en temps réel · Groq AI
                        </p>
                    </div>
                </div>
            )}
        </div>
    );
}

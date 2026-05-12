<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardAgent extends Model
{
    protected $fillable = [
        'team_id',
        'role',
        'agent_name',
        'system_prompt',
        'capabilities',
        'config',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AgentConversation::class, 'agent_id');
    }

    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities ?? []);
    }
}

class AgentConversation extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'agent_id',
        'title',
        'context',
        'message_count',
        'last_message_at',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(DashboardAgent::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AgentMessage::class, 'conversation_id')
            ->orderBy('created_at', 'asc');
    }

    public function addMessage(string $sender, string $content, ?array $metadata = null): AgentMessage
    {
        return $this->messages()->create([
            'sender' => $sender,
            'content' => $content,
            'metadata' => $metadata,
        ]);
    }
}

class AgentMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender',
        'content',
        'metadata',
        'attachments',
    ];

    protected $casts = [
        'metadata' => 'array',
        'attachments' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AgentConversation::class);
    }
}

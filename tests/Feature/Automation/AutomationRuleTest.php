<?php

use App\Domain\Automation\Models\AutomationRule;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\DefaultAutomationRulesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = $this->user->currentTeam;
    $this->actingAs($this->user);
    app(DefaultAutomationRulesSeeder::class)->seedForTeam($this->team->id);
});

test('AutomationRule evaluate returns true when condition is met', function () {
    $rule = AutomationRule::withoutGlobalScopes()->create([
        'team_id' => $this->team->id,
        'name' => 'Test rule',
        'trigger_event' => 'test.event',
        'condition_field' => 'order.total',
        'condition_operator' => 'gt',
        'condition_value' => '1000',
        'action_type' => 'alert_manager',
    ]);

    expect($rule->evaluate(['order' => ['total' => 2000]]))->toBeTrue()
        ->and($rule->evaluate(['order' => ['total' => 500]]))->toBeFalse();
});

test('DELETE system rule returns 422', function () {
    $rule = AutomationRule::withoutGlobalScopes()
        ->where('team_id', $this->team->id)
        ->where('is_system', true)
        ->first();

    $this->deleteJson("/api/v1/automation/rules/{$rule->id}")
        ->assertStatus(422)
        ->assertJsonFragment(['message' => 'Les règles système ne peuvent pas être supprimées.']);
});

test('GET rules returns only current team rules', function () {
    $otherTeam = Team::factory()->create();
    AutomationRule::withoutGlobalScopes()->create([
        'team_id' => $otherTeam->id,
        'name' => 'Règle autre team',
        'trigger_event' => 'order.confirming',
        'condition_field' => 'order.total',
        'condition_operator' => 'gt',
        'condition_value' => '0',
        'action_type' => 'alert_manager',
    ]);

    $expectedCount = AutomationRule::withoutGlobalScopes()
        ->where('team_id', $this->team->id)
        ->count();

    $this->getJson('/api/v1/automation/rules')
        ->assertOk()
        ->assertJsonCount($expectedCount, 'data');
});

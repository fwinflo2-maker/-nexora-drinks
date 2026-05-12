<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Automation\Models\AutomationRule;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $rules = AutomationRule::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->orderBy('priority')
            ->get();

        return response()->json(['data' => $rules]);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'trigger_event' => ['required', 'string', 'max:100'],
            'condition_field' => ['required', 'string', 'max:100'],
            'condition_operator' => ['required', 'string', 'max:10'],
            'condition_value' => ['required', 'string', 'max:255'],
            'action_type' => ['required', 'string', 'max:50'],
            'action_params' => ['nullable', 'array'],
            'priority' => ['nullable', 'integer'],
        ]);

        $rule = AutomationRule::create(array_merge($validated, ['team_id' => $teamId]));

        return response()->json(['data' => $rule], 201);
    }

    public function update(Request $request, AutomationRule $rule): JsonResponse
    {
        if ($rule->team_id !== $request->user()->current_team_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'trigger_event' => ['sometimes', 'string', 'max:100'],
            'condition_field' => ['sometimes', 'string', 'max:100'],
            'condition_operator' => ['sometimes', 'string', 'max:10'],
            'condition_value' => ['sometimes', 'string', 'max:255'],
            'action_type' => ['sometimes', 'string', 'max:50'],
            'action_params' => ['nullable', 'array'],
            'priority' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($rule->is_system) {
            $validated = array_intersect_key($validated, ['is_active' => true]);
        }

        $rule->update($validated);

        return response()->json(['data' => $rule->fresh()]);
    }

    public function destroy(Request $request, AutomationRule $rule): JsonResponse
    {
        if ($rule->team_id !== $request->user()->current_team_id) {
            abort(403);
        }

        if ($rule->is_system) {
            return response()->json(['message' => 'Les règles système ne peuvent pas être supprimées.'], 422);
        }

        $rule->delete();

        return response()->json(null, 204);
    }
}

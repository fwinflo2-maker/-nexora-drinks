<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function activate(Request $request, Team $team, string $module): JsonResponse
    {
        $mod = Module::from($module);

        $team->activateModule($mod->value, $request->user());

        return response()->json([
            'message' => "Module «{$mod->label()}» activé pour {$team->name}.",
            'module' => $mod->value,
            'active' => true,
        ]);
    }

    public function deactivate(Request $request, Team $team, string $module): JsonResponse
    {
        $mod = Module::from($module);

        $team->deactivateModule($mod->value);

        return response()->json([
            'message' => "Module «{$mod->label()}» désactivé pour {$team->name}.",
            'module' => $mod->value,
            'active' => false,
        ]);
    }

    public function index(Team $team): JsonResponse
    {
        $active = $team->activeModules()->pluck('module')->toArray();

        $modules = array_map(fn (Module $m) => [
            'value' => $m->value,
            'label' => $m->label(),
            'active' => in_array($m->value, $active, strict: true),
        ], Module::cases());

        return response()->json(['data' => $modules]);
    }
}

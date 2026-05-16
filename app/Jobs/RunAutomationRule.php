<?php

namespace App\Jobs;

use App\Domain\Automation\Models\AutomationRule;
use App\Domain\Automation\Services\AutomationActionExecutor;
use App\Domain\Automation\Services\AutomationContextBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RunAutomationRule implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly AutomationRule $rule,
        public readonly object $event,
    ) {}

    public function handle(AutomationContextBuilder $contextBuilder, AutomationActionExecutor $executor): void
    {
        $context = $contextBuilder->build($this->event);

        if ($this->rule->evaluate($context)) {
            $executor->execute($this->rule, $context);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::critical('Job échoué : '.static::class, [
            'error' => $e->getMessage(),
            'rule_id' => $this->rule->id ?? null,
            'payload' => $this->job?->getRawBody(),
        ]);
    }
}

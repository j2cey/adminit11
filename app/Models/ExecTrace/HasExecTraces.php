<?php

namespace App\Models\ExecTrace;

use App\Jobs\ExecTraceJob;
use App\Contracts\ExecTrace\IHasExecTrace;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasExecTraces
{
    /**
     * Get all of the model's exec traces
     * @return MorphMany
     */
    public function exectraces()
    {
        return $this->morphMany(ExecTrace::class, 'hasexectrace');
    }

    public function addExecTrace(string $process_name, string $process_code, string $message, string|null $description) {
        $this->dispatch($this,$process_name, $process_code, $message, $description);
    }

    private function dispatch(IHasExecTrace $hasexectrace, string $process_name, string $process_code, string $message, string|null $description) {
        dispatch(new ExecTraceJob($hasexectrace, $process_name, $process_code, $message, $description));
    }

    protected function initializeHasExecTraces()
    {
        $this->with = array_unique(array_merge($this->with, ['exectraces']));
    }
}

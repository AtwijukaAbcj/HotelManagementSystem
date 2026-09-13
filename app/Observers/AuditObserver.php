<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getOriginal(), []);
    }

    private function record(Model $model, string $action, array $oldValues, array $newValues): void
    {
        AuditLog::create([
            'property_id' => $model->getAttribute('property_id'),
            'user_id' => Auth::id(),
            'event' => $action . ' ' . class_basename($model),
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}
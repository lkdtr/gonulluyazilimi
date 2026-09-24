<?php

namespace App\Models\Concerns;

use App\Support\Audit;

/**
 * Records the model's creation, changes and deletion in the audit log with
 * old and new values. A model may define auditLabel() for the description.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $values = collect($model->getAttributes())->map(fn ($value) => [null, $value])->all();
            Audit::record('create', $model, $values, $model->auditDescription('eklendi'));
        });

        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getChanges() as $key => $value) {
                $changes[$key] = [$model->getOriginal($key), $value];
            }
            Audit::record('change', $model, $changes, $model->auditDescription('düzenlendi'));
        });

        static::deleted(function ($model) {
            $values = collect($model->getAttributes())->map(fn ($value) => [$value, null])->all();
            Audit::record('delete', $model, $values, $model->auditDescription('silindi'));
        });
    }

    public function auditDescription(string $action): string
    {
        $label = method_exists($this, 'auditLabel') ? $this->auditLabel() : '#'.$this->getKey();

        return trim(Audit::subjectLabel($this->getMorphClass()).' '.$action.': '.$label);
    }
}

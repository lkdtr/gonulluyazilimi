<?php

namespace App\Support;

use App\Models\ProcessLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * Structured audit records in process_logs: who changed which record, and
 * the old and new values. Models record themselves through the Auditable
 * trait; other changes (e.g. a role's permissions) call record() directly.
 */
class Audit
{
    /** Never stored. */
    private const HIDDEN = ['password', 'remember_token', 'verification_code', 'verify_token', 'created_at', 'updated_at', 'deleted_at'];

    /** Stored masked: personal identifiers. */
    private const MASKED = ['national_id', 'identity_number'];

    /** Labels of the record types shown in the admin panel. */
    public const SUBJECTS = [
        \App\Models\User::class => 'Hesap',
        \App\Models\Contact::class => 'Kişi / kurum',
        \App\Models\ContactAffiliation::class => 'Sıfat',
        \App\Models\AffiliationType::class => 'Sıfat türü',
        \App\Models\Role::class => 'Rol',
        \App\Models\Setting::class => 'Kurum ayarı',
        \App\Models\Agreement::class => 'Sözleşme',
        \App\Models\AgreementVersion::class => 'Sözleşme sürümü',
        \App\Models\ContactPhoto::class => 'Fotoğraf',
        \Modules\IdCard\Models\IdCard::class => 'Kimlik kartı',
        \Modules\IdCard\Models\IdCardTemplate::class => 'Kart şablonu',
    ];

    private static bool $paused = false;

    /**
     * @param  array<string, array{0: mixed, 1: mixed}>  $changes  attribute => [old, new]
     */
    public static function record(string $type, Model $subject, array $changes, string $description): void
    {
        if (self::$paused) {
            return;
        }

        $changes = self::clean($changes);
        if ($type === 'change' && $changes === []) {
            return;
        }

        try {
            $log = new ProcessLogs();
            $log->process_type = $type;
            $log->process = mb_substr($description, 0, 255);
            $log->subject_type = $subject->getMorphClass();
            $log->subject_id = $subject->getKey();
            $log->changes = $changes ?: null;
            $log->process_by = Auth::id();
            $log->request_ip = app()->runningInConsole() ? null : request()->ip();
            $log->save();
        } catch (Throwable $e) {
            // Auditing must never break the change itself.
            report($e);
        }
    }

    /**
     * Run without recording, e.g. for bulk data migrations.
     */
    public static function withoutRecording(callable $callback): mixed
    {
        $paused = self::$paused;
        self::$paused = true;

        try {
            return $callback();
        } finally {
            self::$paused = $paused;
        }
    }

    public static function subjectLabel(?string $type): ?string
    {
        return $type === null ? null : (self::SUBJECTS[$type] ?? class_basename($type));
    }

    private static function clean(array $changes): array
    {
        $clean = [];
        foreach ($changes as $key => [$old, $new]) {
            if (in_array($key, self::HIDDEN, true)) {
                continue;
            }
            if (in_array($key, self::MASKED, true)) {
                [$old, $new] = [$old === null ? null : '•••', $new === null ? null : '•••'];
                if ($old === $new && $old !== null) {
                    $new = '••• (değişti)';
                }
            }
            $clean[$key] = [self::scalar($old), self::scalar($new)];
        }

        return $clean;
    }

    private static function scalar(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if (is_string($value) && mb_strlen($value) > 500) {
            return mb_substr($value, 0, 500).'…';
        }

        return $value;
    }
}

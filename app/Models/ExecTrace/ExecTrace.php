<?php

namespace App\Models\ExecTrace;

use App\Models\Execution;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ExecTrace
 * @package App\Models\ExecTrace
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property string|null $tags
 *
 * @property string|null $process_name
 * @property string|null $process_code
 * @property string|null $message
 *
 * @property Carbon $traced_at
 * @property string|null $description
 *
 * @property string|null $hasexectrace_type
 * @property int|null $hasexectrace_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static ExecTrace create(string[] $array)
 */
class ExecTrace extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    #region Validation Rules

    public static function defaultRules() {
        return [
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [

        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function messagesRules() {
        return [

        ];
    }

    #endregion

    #region Eloquent Relationships

    /**
     * @return MorphTo
     * Get the format rule owner model.
     */
    public function hasexectrace()
    {
        return $this->morphTo();
    }

    #endregion

    #region Custom Functions

    public static function register(string $hasexectrace_type, int $hasexectrace_id, string $process_name, string $process_code, string $message, Carbon $traced_at, string|null $description): ExecTrace
    {
        return ExecTrace::create([
            'process_name' => $process_name,
            'process_code' => $process_code,
            'message' => $message,
            'traced_at' => $traced_at,
            'description' => $description,
            'hasexectrace_type' => $hasexectrace_type,
            'hasexectrace_id' => $hasexectrace_id,
        ]);
    }

    #endregion
}

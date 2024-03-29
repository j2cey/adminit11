<?php

namespace App\Models\Format;

use App\Models\BaseModel;
use App\Services\Time\Period;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use App\Contracts\Format\IIsFormattable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class FormattingResult
 * @package App\Models\Import
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Carbon|null $start_at
 *
 * @property int|null $nb_to_format
 * @property int|null $nb_formatting_success
 * @property float $formatting_success_rate
 * @property int|null $last_formatting_success
 * @property int|null $nb_formatting_failed
 * @property float $formatting_failed_rate
 * @property int|null $last_formatting_failed
 * @property int|null $last_formatted
 * @property int|null $nb_being_formatted
 * @property int|null $nb_formatted
 * @property bool $formatting_done
 *
 * @property int $attempts
 * @property int $attempts_session_count
 *
 * @property float $min_formatted_success_rate
 * @property int $formatted
 * @property Carbon|null $end_at
 * @property int|null $duration
 * @property string|null $duration_hhmmss
 *
 * @property string|null $formattable_type
 * @property int|null $formattable_id
 * @property string|null $last_failed_message
 * @property bool $merging_ready
 *
 * @property int|null $posi
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property FormattingResult|null $upperformattingresult
 * @property IIsFormattable $formattable
 * @method static FormattingResult create(array $data)
 */
class FormattingResult extends BaseModel implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'formatted' => 'boolean',
        'start_at' => 'datetime:Y-m-d H:i:s',
        'end_at' => 'datetime:Y-m-d H:i:s',
    ];

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

    public function upperformattingresult() {
        return $this->belongsTo(FormattingResult::class, 'upper_formattingresult_id');
    }

    public function subformattingresults() {
        return $this->hasMany(FormattingResult::class, 'upper_formattingresult_id');
    }

    /**
     * @return MorphTo
     * Get the formatted model.
     */
    public function formattable()
    {
        return $this->morphTo();
    }

    #endregion

    #region Custom Functions

    public static function createNew(array $data): FormattingResult {
        return FormattingResult::create($data);
    }

    public function setUpperFormattingResult(FormattingResult|null $upperformattingresult) {
        if ( ! is_null( $upperformattingresult ) ) {
            $upperformattingresult->addSubFormattingResult($this);
        }
    }

    private function addSubFormattingResult(FormattingResult $formattingresult) {
        $formattingresult->posi = $this->subformattingresults()->count() + 1;
        $formattingresult->upperformattingresult()->associate($this)->save();
    }

    public function addToFormat(int $amount) {
        $this->update(['nb_to_format' => $this->nb_to_format+= $amount,]
        );
        $this->setFormattingUpToDate(true);

        $this->upperformattingresult?->addToFormat($amount);
    }

    public function setStarting(bool $force = false) {

        //$this->posi = 1;
        //$upper_formattingresult?->addSubFormattingResult($this);

        if ( is_null($this->start_at) || $force ) {
            $this->start_at = Carbon::now();
            $this->setNewAttempt();

            //$this->nb_to_format = $nb_to_format;
            $this->incrementFormatting(false);

            $this->save();
        }

        $this->upperformattingresult?->setStarting();

        //return $this;
    }

    private function setNewAttempt() {
        $this->attempts++;
        $this->attempts_session_count++;
    }

    public function incrementFormatting(bool $save) {
        $this->nb_being_formatted++;
        $this->saveObject($save);
    }

    /**
     * @param string $formatting_attribute formatting attribute to increment 'nb_formatting_success' or 'nb_formatting_failed'
     * @param int $amount items amount
     * @param bool $save
     * @return void
     */
    private function decrementFormatting(string $formatting_attribute, int $amount, bool $save) {
        $this->{$formatting_attribute} += $amount;
        $this->nb_being_formatted -= ($amount > $this->nb_being_formatted) ? $this->nb_being_formatted : $amount;
        $this->saveObject($save);
    }

    public function allFormattingSucceed() {
        $this->setAllFormattingDone("nb_formatting_success", "last_formatting_success");
    }

    public function allFormattingFailed(string $message) {
        $this->last_failed_message = $message;
        $this->setAllFormattingDone("nb_formatting_failed", "last_formatting_failed");
    }

    public function itemFormattingSucceed(int $item) {
        $this->last_formatting_success = $item;
        $this->setFormattingDone("nb_formatting_success",1, 1);

        $this->upperformattingresult?->itemFormattingSucceed($item);
    }

    public function itemFormattingFailed(int $item, string $message) {
        $this->last_formatting_failed = $item;
        $this->last_failed_message = $message;
        $this->setFormattingDone("nb_formatting_failed",1, 1);

        $this->upperformattingresult?->itemFormattingFailed($item, $message);
    }


    /**
     * @param string $nb_formatting_attribute formatting attribute to increment 'nb_formatting_success' or 'nb_formatting_failed'
     * @param string $last_formatting_attribute last formatting attribute to increment 'last_formatting_success' or 'last_formatting_failed'
     * @return void
     */
    private function setAllFormattingDone(string $nb_formatting_attribute, string $last_formatting_attribute) {
        $last_item = $this->nb_to_format - 1;

        $this->{$nb_formatting_attribute} = 0;
        $this->{$last_formatting_attribute} = $last_item;
        $this->nb_being_formatted = $this->nb_to_format;
        $this->setFormattingDone($nb_formatting_attribute, $this->nb_to_format, $last_item);
    }

    private function setFormattingDone(string $formatting_attribute, int $amount, int $last_item) {
        //\Log::info("setFormattingDone (" . $this->id . "), amount: " . $amount . ", formatting_attribute: " . $formatting_attribute . ", formattable: " . $this->formattable_type . "(" . $this->formattable_id . ")");
        if ( is_null( $this->start_at ) ) {
            $this->setStarting();
        }
        $this->last_formatted = $last_item;
        $this->decrementFormatting($formatting_attribute, $amount, false);
        $this->setFormattingUpToDate(false);

        $this->save();
    }

    private function setFormattingUpToDate(bool $save) {
        $this->formatting_success_rate = ($this->nb_formatting_success / $this->nb_to_format) * 100;
        $this->formatting_failed_rate = ($this->nb_formatting_failed / $this->nb_to_format) * 100;

        $this->formatted = ( $this->formatting_success_rate >= $this->min_formatted_success_rate );
        $this->saveObject($save);

        $this->formatting_done = ($this->nb_formatting_success + $this->nb_formatting_failed) >= $this->nb_to_format;

        $this->endFormatting();
    }

    private function endFormatting() {
        if ($this->formatting_done) {
            $period = Period::start($this->start_at)->end();

            $this->end_at = $period->getEndAt();
            $this->duration = $period->getDurationMilliseconds();
            $this->duration_hhmmss = $period->getDurationHhmmss();
        }
    }

    public function setMergingReady() {
        $this->merging_ready = true;
        $this->save();
    }

    #endregion

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            $model->subformattingresults()->each(function($subformattingresult) {
                $subformattingresult->delete(); // <-- direct deletion
            });
        });
    }
}

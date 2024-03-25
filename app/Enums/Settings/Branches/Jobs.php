<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;
use App\Enums\Settings\LevelUnit;
use App\Enums\Settings\LevelState;
use App\Enums\Settings\LevelAction;

/**
 * Class Jobs. Raw Jobs settings
 * @package App\Enums\Settings\Branches
 *
 * @method max_reserved()
 * @method max_pending()
 */
class Jobs extends SettingNode
{
    public function __construct()
    {
        parent::__construct("jobs",null,null,null,null,"settings Jobs.");

        $this->addChild("max_reserved", null, null, "max reserved")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::MINUTE, "30", LevelAction::NONE);
        //$max_reserved->addChild("unit", "min", "string", "max reserved unit.");
        //$max_reserved->addChild("value", "30", "integer", "max reserved value.");

        $this->addChild("max_pending", null, null, "max pending")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::MINUTE, "15", LevelAction::NONE);
        //$max_pending->addChild("unit", "min", "string", "max pending unit.");
        //$max_pending->addChild("value", "15", "integer", "max pending value.");
    }
}

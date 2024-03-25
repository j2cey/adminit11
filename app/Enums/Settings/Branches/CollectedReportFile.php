<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\LevelUnit;
use App\Enums\Settings\SettingNode;
use App\Enums\Settings\LevelState;
use App\Enums\Settings\LevelAction;

/**
 * Class CollectedReportFile. CollectedReportFile settings
 * @package App\Enums\Settings\Branches
 *
 * @method delete_expired()
 */
class CollectedReportFile extends SettingNode
{
    public function __construct()
    {
        parent::__construct("collectedreportfile",null,null,null,null,"settings Job Batches.");

        $delete_expired = $this->addChild("delete_expired", null, null, "delete expired CollectedReportFile");
        $delete_expired->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "6", LevelAction::NONE);

        $delete_expired->addChild("rows", null, null, "delete expired CollectedReportFile rows")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "5", LevelAction::NONE);

        $delete_expired->addChild("values", null, null, "delete expired CollectedReportFile values")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "2", LevelAction::NONE);
    }
}

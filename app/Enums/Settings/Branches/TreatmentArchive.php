<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;
use App\Enums\Settings\LevelUnit;
use App\Enums\Settings\LevelState;
use App\Enums\Settings\LevelAction;
use App\Enums\Settings\SubSettings\LeveledNodeConfig;
use App\Enums\Settings\SubSettings\LeveledSettingNode;

/**
 * Class TreatmentArchive. Raw TreatmentArchive settings
 * @package App\Enums\Settings\Branches
 *
 * @method LeveledSettingNode completed()
 * @method LeveledSettingNode uncompleted()
 */
class TreatmentArchive extends SettingNode
{
    public function __construct()
    {
        parent::__construct("treatmentarchive",null,null,null,null,"settings Treatment Archive.");

        $completed_level0_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::DAY, "5", LevelAction::DELETE);
        $completed_level1_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::DAY, "4", LevelAction::DELETE);
        $completed_level2_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::DAY, "3", LevelAction::DELETE);
        $completed_level3_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::DAY, "2", LevelAction::DELETE);
        $completed_levelOver3_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::DAY, "1", LevelAction::DELETE);

        $this->addLeveldChildren("completed","Completed Treatment to Archive",$completed_level0_conf,$completed_level1_conf,$completed_level2_conf,$completed_level3_conf,$completed_levelOver3_conf);
        // treatmentarchive.completed.lelev_0.state
        // treatmentarchive.completed.lelev_0.unit
        // treatmentarchive.completed.lelev_0.value


        $uncompleted_level0_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::MINUTE, "20", LevelAction::DISPATCH);
        $uncompleted_level1_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::MINUTE, "20", LevelAction::DISPATCH);
        $uncompleted_level2_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::MINUTE, "10", LevelAction::DISPATCH);
        $uncompleted_level3_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::MINUTE, "10", LevelAction::DISPATCH);
        $uncompleted_levelOver3_conf = new LeveledNodeConfig(LevelState::ENABLED, LevelUnit::MINUTE, "10", LevelAction::DISPATCH);

        $this->addLeveldChildren("uncompleted","Un-Completed Treatment to Archive",$uncompleted_level0_conf,$uncompleted_level1_conf,$uncompleted_level2_conf,$uncompleted_level3_conf,$uncompleted_levelOver3_conf);

        /*
        $level_0 = $this->addChild("level_0", null, null, "Archives settings for Treatment with level 0.");
        $level_0->addChild("max_before_del", null, null, "Max before deletion for level 0.")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "5");

        $level_1 = $this->addChild("level_1", null, null, "Archives settings for Treatment with level 1.");
        $level_1->addChild("max_before_del", null, null, "Max before deletion for level 1.")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "4");

        $level_2 = $this->addChild("level_2", null, null, "Archives settings for Treatment with level 2.");
        $level_2->addChild("max_before_del", null, null, "Max before deletion for level 2.")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "3");

        $level_3 = $this->addChild("level_3", null, null, "Archives settings for Treatment with level 3.");
        $level_3->addChild("max_before_del", null, null, "Max before deletion for level 3.")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "2");

        $level_over_3 = $this->addChild("level_over_3", null, null, "Archives settings for Treatment with level over 3.");
        $level_over_3->addChild("max_before_del", null, null, "Max before deletion for level over 3.")
            ->addArchiveChildren(LevelState::ENABLED, LevelUnit::DAY, "1");
        */
    }
}

<?php

namespace App\Enums\Settings\SubSettings;

use App\Enums\Settings\LevelUnit;
use App\Enums\Settings\LevelState;
use App\Enums\Settings\SettingNode;

/**
 * Class LeveledSettingNode. Raw Leveled settings
 * @package App\Enums\Settings\SubSettings
 *
 * @method level_0()
 * @method level_1()
 * @method level_2()
 * @method level_3()
 * @method level_over_3()
 */
class LeveledSettingNode extends SettingNode
{
    private static LevelState $DEFAULT_STATE = LevelState::ENABLED;
    private static LevelUnit $DEFAULT_UNIT = LevelUnit::DAY;
    private static string $DEFAULT_VALUE = "1";

    public function __construct(SettingNode $parent, string $name, string $description, LeveledNodeConfig $level0_conf, LeveledNodeConfig $level1_conf, LeveledNodeConfig $level2_conf, LeveledNodeConfig $level3_conf, LeveledNodeConfig $levelOver3_conf)
    {
        parent::__construct($name, null, null, $parent, null, $description);

        $this->addChild("level_0", null, null, "settings for level 0.")
            ->addArchiveChildren($level0_conf->getState(), $level0_conf->getUnit(), $level0_conf->getValue(), $level0_conf->getAction());

        $this->addChild("level_1", null, null, "settings for level 1.")
            ->addArchiveChildren($level1_conf->getState(), $level1_conf->getUnit(), $level1_conf->getValue(), $level1_conf->getAction());

        $this->addChild("level_2", null, null, "settings for level 2.")
            ->addArchiveChildren($level2_conf->getState(), $level2_conf->getUnit(), $level2_conf->getValue(), $level2_conf->getAction());

        $this->addChild("level_3", null, null, "settings for level 3.")
            ->addArchiveChildren($level3_conf->getState(), $level3_conf->getUnit(), $level3_conf->getValue(), $level3_conf->getAction());

        $this->addChild("level_over_3", null, null, "settings for level over 3.")
            ->addArchiveChildren($levelOver3_conf->getState(), $levelOver3_conf->getUnit(), $levelOver3_conf->getValue(), $levelOver3_conf->getAction());
    }
}

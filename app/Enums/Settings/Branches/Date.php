<?php

namespace App\Enums\Settings\Branches;

use App\Enums\DateFormat;
use App\Enums\Settings\SettingNode;

/**
 * Class Date. Raw Date settings
 * @package App\Enums\Settings\Branches
 *
 * @method format()
 */
class Date extends SettingNode
{
    public function __construct()
    {
        parent::__construct("date",null,null,null,null,"Date Settings");

        $this->addChild("format", DateFormat::dmY_dash, "string", "Application Name.");
    }
}

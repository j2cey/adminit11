<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;

/**
 * Class App. Raw Application settings
 * @package App\Enums\Settings\Branches
 *
 * @method name()
 * @method version()
 */
class Pagination extends SettingNode
{
    public function __construct()
    {
        parent::__construct("pagination",null,null,null,null,"Pagination Settings");

        $this->addChild("limit", "10", "integer", "Pagination limit.");
    }
}

<?php

namespace App\Enums\Settings;

use App\Traits\Enum\EnumTrait;
use App\Enums\Attributes\Description;

enum LevelAction: string
{
    use EnumTrait;

    #[Description('None')]
    case NONE = 'none';

    #[Description('Delete')]
    case DELETE = 'delete';

    #[Description('Dispatch')]
    case DISPATCH = 'dispatch';

    #[Description('Rewind State')]
    case REWINDSTATE = 'rewindstate';
}

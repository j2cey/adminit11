<?php

namespace App\Enums;

use App\Traits\Enum\EnumTrait;
use App\Enums\Attributes\Description;

enum DateFormat: string
{
    use EnumTrait;

    #[Description('m/d/Y')]
    case mdY_slash = "MM/DD/YYYY";

    #[Description('m-d-Y')]
    case mdY_dash = "MM-DD-YYYY";

    #[Description('d/m/Y')]
    case dmY_slash = "DD/MM/YYYY";

    #[Description('d-m-Y')]
    case dmY_dash = "DD-MM-YYYY";

    #[Description('Y/m/d')]
    case Ymd_slash = "YYYY/MM/DD";

    #[Description('Y-m-d')]
    case Ymd_dash = "YYYY-MM-DD";

    #[Description('F j, Y')]
    case FjY = "Month DD, YYYY";

    #[Description('Tableau')]
    case jFY = "DD Month YYYY";
}

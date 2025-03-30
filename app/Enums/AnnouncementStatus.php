<?php

namespace App\Enums;

enum AnnouncementStatus: int
{
    case Pending  = 0;
    case Active   = 1;
    case Expired  = 4;
    case Deactive = 2;
    case Rejected = 3;
}

<?php

namespace App\Enums;

enum AnnouncementStatus: int
{
    case Pending  = 1;
    case Active   = 2;
    case Expired  = 3;
    case Deactive = 4;
    case Rejected = 5;
}

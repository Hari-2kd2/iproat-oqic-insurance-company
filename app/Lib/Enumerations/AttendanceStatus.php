<?php

namespace App\Lib\Enumerations;

class AttendanceStatus
{
    public static $PRESENT  = 1;
    public static $ABSENT  = 2;
    public static $LEAVE  = 3;
    public static $HOLIDAY  = 4;
    public static $FUTURE  = 5;
    public static $UPDATE  = 6;
    public static $ERROR  = 7;
    public static $ONETIMEINPUNCH  = 8;
    public static $ONETIMEOUTPUNCH = 9;
    public static $LESSHOURS  = 10;
    public static $COMPOFF  = 11;
    public static $INCENTIVE  = 12;
}

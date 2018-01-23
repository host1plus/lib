<?php

namespace Host1Plus\Enums;

final class CloudStatisticTypes
{
    const All     = 'all';
    const Moment  = 'moment';
    const Hourly  = 'hourly';
    const Daily   = 'daily';
    const Weekly  = 'weekly';
    const Monthly = 'monthly';
    const Allowed = [self::All, self::Moment, self::Hourly, self::Daily, self::Weekly, self::Monthly];
}
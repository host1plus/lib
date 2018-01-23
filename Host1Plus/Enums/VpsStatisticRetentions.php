<?php

namespace Host1Plus\Enums;

final class VpsStatisticRetentions
{
    const OneWeek         = '1w';
    const TwentyFourWeeks = '24w';
    const Infinite        = 'inf';
    const Valid           = [self::OneWeek, self::TwentyFourWeeks, self::Infinite];
}
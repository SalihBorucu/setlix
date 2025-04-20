<?php

namespace App\Types;

class StripeStatusTypes
{
    const _ACTIVE = 'active';
    const _PAST_DUE = 'past_due';
    const _CANCELLED = 'canceled';
    const _INACTIVE = 'inactive';
    const _ACTIVE_UNTIL_END = 'active_until_end';
}

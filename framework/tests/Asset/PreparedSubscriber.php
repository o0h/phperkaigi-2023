<?php

declare(strict_types=1);

namespace O0h\KantanFw\Test\Asset;

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber as IPreparedSubscriber;

class PreparedSubscriber implements IPreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        HeaderStack::reset();
    }

}

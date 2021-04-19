<?php

namespace Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Utilities\Action;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_it_checks_for_a_valid_callable()
    {
        $this->assertIsCallable(Action::normalize([$this, 'test_it_checks_for_a_valid_callable']));

        $this->expectException(InvalidArgumentException::class);

        Action::normalize(['some', 'array']);
    }
}

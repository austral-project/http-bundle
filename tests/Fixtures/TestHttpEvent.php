<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Fixtures;

use Austral\HttpBundle\Event\HttpEvent;

/**
 * Concrete HTTP event used by the tests.
 *
 * @extends HttpEvent<object>
 */
final class TestHttpEvent extends HttpEvent
{
}

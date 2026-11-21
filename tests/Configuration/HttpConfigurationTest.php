<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Configuration;

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Austral\HttpBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

final class HttpConfigurationTest extends TestCase
{
  public function testValuesAreReadWithADottedKey(): void
  {
    $configuration = new HttpConfiguration(new Configuration()->getConfigDefault());

    self::assertSame('https', $configuration->get('protocol'));
    self::assertSame('prod', $configuration->get('env.current'));
    self::assertSame(['prod'], $configuration->get('env.list'));
    self::assertTrue($configuration->get('compression_gzip.default'));
  }

  public function testMissingKeyReturnsTheDefaultValue(): void
  {
    $configuration = new HttpConfiguration(new Configuration()->getConfigDefault());

    self::assertTrue($configuration->get('compression_gzip.website', true));
    self::assertNull($configuration->get('unknown'));
  }
}

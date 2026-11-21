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

use Austral\HttpBundle\Controller\HttpController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Concrete controller exposing the protected helpers to the tests.
 *
 * @extends HttpController<object>
 */
final class TestHttpController extends HttpController
{
  /**
   * @param array<string, string> $headers
   */
  public function callJson(mixed $data, int $status = 200, array $headers = [], bool $json = false): JsonResponse
  {
    return $this->json($data, $status, $headers, $json);
  }
}

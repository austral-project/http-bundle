<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\EntityManager;

use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Austral\HttpBundle\Repository\DomainRepository;

use Austral\EntityBundle\EntityManager\EntityManager;
use Doctrine\ORM\Query\QueryException;

/**
 * Austral Domain EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DomainEntityManager extends EntityManager
{

  /**
   * @var DomainRepository
   */
  protected $repository;

  /**
   * @param array $values
   *
   * @return DomainInterface
   */
  public function create(array $values = array()): DomainInterface
  {
    return parent::create($values);
  }

  /**
   * @param string $host
   *
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByDomain(string $host): ?DomainInterface
  {
    return $this->repository->retreiveByDomain($host);
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByMaster(): ?DomainInterface
  {
    return $this->repository->retreiveByMaster();
  }

  /**
   * @return array
   * @throws QueryException
   */
  public function selectAllEnabledDomains(): array
  {
    return $this->repository->selectAllEnabledDomains();
  }

}

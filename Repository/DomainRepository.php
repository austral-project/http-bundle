<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Repository;

use Austral\EntityBundle\Repository\EntityRepository;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;

/**
 * Austral Domain Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DomainRepository extends EntityRepository
{

  /**
   * @param string $host
   *
   * @return DomainInterface|null
   * @throws NonUniqueResultException
   */
  public function retreiveByDomain(string $host): ?DomainInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.domain = :domain")
      ->andWhere("root.isEnabled = :isEnabled")
      ->setParameter("domain", $host)
      ->setParameter("isEnabled", true);

    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

  /**
   * @return DomainInterface|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function retreiveByMaster(): ?DomainInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.isMaster = :isMaster")
      ->andWhere("root.isEnabled = :isEnabled")
      ->setParameter("isMaster", true)
      ->setParameter("isEnabled", true)
      ->setMaxResults(1);

    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

  /**
   * @return array
   * @throws QueryException
   */
  public function selectAllEnabledDomains(): array
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->andWhere("root.isEnabled = :isEnabled")
      ->setParameter("isEnabled", true)
      ->indexBy("root", "root.domain")
      ->orderBy("root.position", "ASC");

    $query = $queryBuilder->getQuery();
    try {
      $objects = $query->execute();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

}

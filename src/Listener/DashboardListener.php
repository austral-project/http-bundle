<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Listener;

use Austral\AdminBundle\Event\DashboardEvent;

use Austral\AdminBundle\Dashboard\Values as DashboardValues;
use Austral\HttpBundle\Services\DomainsManagement;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral DashboardListener Listener.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DashboardListener
{

  /**
   * @param DomainsManagement<T> $domainsManagement
   */
  public function __construct(
    #[Autowire(service: "austral.http.domains.management")] protected DomainsManagement $domainsManagement)
  {
  }

  /**
   * @param DashboardEvent<T> $dashboardEvent
   *
   * @throws \Exception
   */
  public function dashboard(DashboardEvent $dashboardEvent): void
  {
    if($this->domainsManagement->getEnabledDomainWithoutVirtual())
    {
      $dashboardTileDomains = new DashboardValues\Tile("domains");
      $dashboardTileDomains->setEntitled("dashboard.tiles.domains.count.entitled")
        ->setIsTranslatableText(true)
        ->setPicto("browser")
        ->setPosition(1)
        ->setValue(count($this->domainsManagement->getDomainsWithoutVirtual()));
      $dashboardTileDomains->setColorNum(1);

      $dashboardEvent->getDashboardBlock()->getChild("austral_tiles_values")?->addValue($dashboardTileDomains);
    }
  }

}
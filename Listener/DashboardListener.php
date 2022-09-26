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

/**
 * Austral DashboardListener Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DashboardListener
{

  /**
   * @var DomainsManagement
   */
  protected DomainsManagement $domainsManagement;

  /**
   * @param DomainsManagement $domainsManagement
   */
  public function __construct(DomainsManagement $domainsManagement)
  {
    $this->domainsManagement = $domainsManagement;
  }


  /**
   * @param DashboardEvent $dashboardEvent
   *
   * @throws \Exception
   */
  public function dashboard(DashboardEvent $dashboardEvent)
  {
    if($this->domainsManagement->getEnabledDomainWithoutVirtual())
    {
      $dashboardTileDomains = new DashboardValues\Tile("domains");
      $dashboardTileDomains->setEntitled("dashboard.tiles.domains.count.entitled")
        ->setIsTranslatableText(true)
        ->setColorNum(1)
        ->setPicto("browser")
        ->setPosition(1)
        ->setValue(count($this->domainsManagement->getDomainsWithoutVirtual()));

      $dashboardEvent->getDashboardBlock()->getChild("austral_tiles_values")
        ->addValue($dashboardTileDomains);
    }
  }

}
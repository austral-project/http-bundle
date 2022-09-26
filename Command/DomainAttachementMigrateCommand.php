<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Command;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\HttpBundle\Mapping\DomainFilterMapping;
use Austral\ToolsBundle\Command\Base\Command;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Austral Roles Command.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DomainAttachementMigrateCommand extends Command
{

  /**
   * @var string
   */
  protected static $defaultName = 'austral:domain:migrate';

  /**
   * @var string
   */
  protected string $titleCommande = "Attachement domain with migrate 3.0 to 3.1";

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setDefinition([
      ])
      ->setDescription($this->titleCommande)
      ->setHelp(<<<'EOF'
The <info>%command.name%</info> command to attachement domain with migrate 3.0 to 3.1

  <info>php %command.full_name%</info>
EOF
      )
    ;
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * @throws Exception
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output)
  {
    $entityManager = $this->container->get("austral.entity_manager");
    $domainsManagement = $this->container->get('austral.http.domains.management')->initialize();
    /** @var EntityMapping $entityMapping */
    foreach($this->container->get('austral.entity.mapping')->getEntitiesMapping() as $entityMapping)
    {
      /** @var DomainFilterMapping $domainFilterMapping */
      if($domainFilterMapping = $entityMapping->getEntityClassMapping(DomainFilterMapping::class))
      {
        if($domainFilterMapping->getAutoDomainId())
        {
          $objects = $entityManager->getRepository($entityMapping->entityClass)->createQueryBuilder("root")
            ->where("root.domainId IS NULL")
            ->getQuery()
            ->execute();

          if($objects)
          {
            /** @var Entity $object */
            foreach($objects as $object)
            {
              $object->setDomainId($domainsManagement->getDomainMaster()->getId());
              $entityManager->update($object, false);
            }
          }
        }
      }
      $entityManager->flush();
    }
  }

}
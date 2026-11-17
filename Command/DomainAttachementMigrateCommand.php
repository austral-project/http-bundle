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
use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\Contracts\Domain\FilterByDomainInterface;
use Austral\HttpBundle\Mapping\DomainFilterMapping;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\ToolsBundle\Command\Base\Command;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral Roles Command.
 * @template T of Entity
 *
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
  protected function configure(): void
  {
    $this
      ->setDefinition([
        new InputOption('--force', '-f', InputOption::VALUE_NONE, 'Add domain master for all objects'),
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
   * DomainAttachementMigrateCommand constructor
   *
   * @param EntityManager<T> $entityManager
   * @param Mapping<T> $mapping
   * @param DomainsManagement<T> $domainsManagement
   * @param string|null $name
   */
  public function __construct(
    #[Autowire(service: "austral.entity_manager")] protected EntityManager $entityManager,
    #[Autowire(service: "austral.entity.mapping")] protected Mapping $mapping,
    #[Autowire(service: "austral.http.domains.management")] protected DomainsManagement $domainsManagement,
    ?string $name = null
  )
  {
    parent::__construct($name);
  }


  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * @throws Exception
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output): void
  {
    $domainMaster = $this->domainsManagement->getDomainMaster();
    if($domainMaster) {
      /** @var EntityMapping<T> $entityMapping */
      foreach ($this->mapping->getEntitiesMapping() as $entityMapping) {
        /** @var DomainFilterMapping<T> $domainFilterMapping */
        if ($domainFilterMapping = $entityMapping->getEntityClassMapping(DomainFilterMapping::class))
        {
          $repository = $this->entityManager->getRepository($entityMapping->entityClass);
          if ($domainFilterMapping->getAutoDomainId() && $repository)
          {
            $queryBuilder = $repository->createQueryBuilder("root");
            if (!$input->getOption("force"))
            {
              $queryBuilder->where("root.domainId IS NULL");
            }
            $objects = $queryBuilder->getQuery()->execute();
            if ($objects)
            {
              /** @var EntityInterface<T> $object */
              foreach ($objects as $object)
              {
                if($object instanceof FilterByDomainInterface)
                {
                  $object->setDomainId($domainMaster->getId());
                  $this->entityManager->update($object, false);
                }
              }
            }
          }
        }
        $this->entityManager->flush();
      }
    }
  }

}
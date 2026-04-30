<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Admin;
use App\Entity\Austral\HttpBundle\Domain;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Repository\EntityRepository;
use Austral\FormBundle\Mapper\Fieldset;
use Austral\FormBundle\Mapper\GroupFields;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;

use Austral\AdminBundle\Admin\Admin;
use Austral\AdminBundle\Admin\AdminModuleInterface;
use Austral\AdminBundle\Admin\Event\FormAdminEvent;
use Austral\AdminBundle\Admin\Event\ListAdminEvent;

use Austral\FormBundle\Field as Field;
use Austral\ListBundle\Column as Column;
use Austral\ListBundle\DataHydrate\DataHydrateORM;

use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * Domain Admin.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class DomainAdmin extends Admin implements AdminModuleInterface
{

  /**
   * @return array
   */
  public function getEvents() : array
  {
    return array(
      FormAdminEvent::EVENT_UPDATE_BEFORE     =>  "formUpdateBefore"
    );
  }

  /**
   * @param ListAdminEvent $listAdminEvent
   */
  public function configureListMapper(ListAdminEvent $listAdminEvent)
  {
    $isGrantedRoot = $listAdminEvent->getAdminHandler()->isGranted("ROLE_ROOT");


    $listAdminEvent->getListMapper()
      ->buildDataHydrate(function(DataHydrateORM $dataHydrate) use($isGrantedRoot){

        $dataHydrate->addQueryBuilderCountAllClosure(function(QueryBuilder $queryBuilder) use($isGrantedRoot) {
          if(!$isGrantedRoot)
          {
            $queryBuilder->andWhere("root.isMaster = :isMaster")
              ->setParameter("isMaster", true);
          }
        });

        $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) use($isGrantedRoot) {
          if(!$isGrantedRoot)
          {
            $queryBuilder->andWhere("root.isMaster = :isMaster")
              ->setParameter("isMaster", true);
          }
          return $queryBuilder
            ->orderBy("root.position", "ASC")
            ->addOrderBy("root.name", "ASC");
        });
      })
      ->addColumn(new Column\Value("domain"))
      ->addColumn(new Column\Value("domainEnv"))
      ->addColumn(new Column\SwitchValue("isEnabled", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("isMaster", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("isVirtual", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\SwitchValue("onePage", null, 0, 1,
          $listAdminEvent->getCurrentModule()->generateUrl("change"),
          $listAdminEvent->getCurrentModule()->isGranted("edit")
        )
      )
      ->addColumn(new Column\Date("updated", null, "d/m/Y"));
  }

  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  public function configureFormMapper(FormAdminEvent $formAdminEvent)
  {
    $domainEnvs = array();
    foreach($this->container->get('austral.http.config')->get("env.list") as $env)
    {
      $domainEnvs[$env] = $env;
    }
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.dev.config")
        ->setCollapse(true)
        ->setIsView($this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT"))
        ->add(Field\TextField::create("keyname", array(
            "autoConstraints" => false,
            "isView" => $this->container->get("security.authorization_checker")->isGranted("ROLE_ROOT")
          )
        ))
        ->end()


      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->setViewName(false)
        ->add(Field\ChoiceField::create("isEnabled",
          array(
            "choices.status.no"     =>  array(
              "value"   =>  false,
              "styles"  =>  array(
                "--element-choice-current-background:var(color-main-20)",
                "--element-choice-current-color:var(--color-main-100)",
                "--element-choice-hover-color:var(--color-main-100)"
              )
            ),
            "choices.status.yes"     =>  array(
              "value"   =>  true,
              "styles"  =>  array(
                "--element-choice-current-background:var(--color-green-20)",
                "--element-choice-current-color:var(--color-green-100)",
                "--element-choice-hover-color:var(--color-green-100)"
              )
            )
          )

        )
        )
      ->end()
      ->addFieldset("fieldset.generalInformation")
        ->add(Field\TextField::create("name", array("entitled"=>"fields.nameDomain.entitled")))
        ->addGroup("domain")
          ->add(Field\SelectField::create('scheme', array(
                DomainInterface::SCHEME_HTTPS => DomainInterface::SCHEME_HTTPS,
                DomainInterface::SCHEME_HTTP  => DomainInterface::SCHEME_HTTP,
              ), array(
                'required' => true
              )
            )->setGroupSize(GroupFields::SIZE_COL_2)
          )
          ->add(Field\TextField::create("domain")->setGroupSize(GroupFields::SIZE_COL_8))

          ->add(Field\SelectField::create('domainEnv', $domainEnvs, array(
                'required' => true
              )
            )->setGroupSize(GroupFields::SIZE_COL_2)
          )
        ->end()

        ->addGroup("parameters")
          ->setDirection(GroupFields::DIRECTION_COLUMN)
          ->addGroup("parameters")
            ->setStyle(GroupFields::STYLE_BOOLEAN)
              ->add(Field\SwitchField::create("isMaster", array(
                  "helper"    =>  "fields.isMaster.information",
                )
              )
            )
            ->add(Field\SwitchField::create("onePage", array(
                  "helper"    =>  "fields.onePage.information",
                )
              )
            )
            ->add(Field\SwitchField::create("isVirtual",
                array(
                  "helper"    =>  "fields.isVirtual.information",
                  "container" =>  array('class'=>"view-element-by-choices-language domain-not-language"),
                  "attr"        =>  array(
                    "data-view-by-choices-parent"   =>  ".form-container",
                    "data-view-by-choices-children" =>  ".view-element-by-choices",
                    'data-view-by-choices' =>  json_encode(array(
                      true           =>  "domain-virtual",
                      false          =>  "domain-not-virtual",
                    ))
                  ),
                )
              )
            )
            ->add(Field\SwitchField::create("isTranslate",
                array(
                  "helper"    =>  "fields.isVirtual.information",
                  "container" =>  array('class'=>"view-element-by-choices domain-not-virtual"),
                  "attr"        =>  array(
                    "data-view-by-choices-parent"   =>  ".form-container",
                    "data-view-by-choices-children" =>  ".view-element-by-choices-language",
                    'data-view-by-choices' =>  json_encode(array(
                      true           =>  "domain-language",
                      false          =>  "domain-not-language",
                    ))
                  ),
                )
              )
            )
          ->end()
        ->end()



        ->add(Field\EntityField::create("master", Domain::class,
          array(
            'query_builder'     => function (EntityRepository $er) use($formAdminEvent) {
              $queryBuilder = $er->createQueryBuilder('root')
                ->where("root.isVirtual = :isVirtual")
                ->setParameter("isVirtual", false)
                ->addOrderBy('root.name', 'ASC');
              return $queryBuilder;
            },
            'entitled'  =>  "fields.domainMaster.entitled",
            "container" =>  array('class'=>"view-element-by-choices domain-virtual"),
            'choice_label' => 'name',
            "required"  =>  $formAdminEvent->getFormMapper()->getObject()->getIsVirtual()
          )
        ))
        ->add(Field\EntityField::create("master_language", Domain::class,
          array(
            "getter"  =>  function(DomainInterface $object) {
              return $object->getMaster();
            },
            "setter"  =>  function(DomainInterface $object, $value) {
              if($object->getIsTranslate())
              {
                $object->setMaster($value);
              }
            },
            'query_builder'     => function (EntityRepository $er) use($formAdminEvent) {
              $queryBuilder = $er->createQueryBuilder('root')
                ->where("root.isVirtual = :isVirtual")
                ->setParameter("isVirtual", false)
                ->addOrderBy('root.name', 'ASC');
              return $queryBuilder;
            },
            'entitled'  =>  "fields.domainMaster.entitled",
            "container" =>  array('class'=>"view-element-by-choices-language domain-language"),
            'choice_label' => 'name',
            "required"  =>  $formAdminEvent->getFormMapper()->getObject()->getIsVirtual()
          )
        ))
        ->add(Field\TextField::create("language"))
      ->end();

    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.domainLogo")
        ->addGroup("logo")
          ->setDirection(GroupFields::DIRECTION_COLUMN)
          ->addGroup("logo_master")
            ->add(Field\UploadField::create("logo",array(
              "entitled"  =>  "fields.logo.entitled"
            )))
            ->add(Field\UploadField::create("logoSecond",array(
              "entitled"  =>  "fields.logoSecond.entitled"
            )))
          ->end()
          ->addGroup("logo_second")
            ->add(Field\UploadField::create("favicon",array(
              "entitled"  =>  "fields.favicon.entitled"
            )))
            ->add(Field\UploadField::create("logoEmail",array(
              "entitled"  =>  "fields.logoEmail.entitled"
            )))
          ->end()
        ->end();

    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.domainConfig")
        ->addGroup("robots", "groups.robots")
        ->setDirection(GroupFields::DIRECTION_COLUMN)
          ->addGroup("robots")
            ->setStyle(GroupFields::STYLE_BOOLEAN)
            ->add(Field\SwitchField::create("isIndex", array(
                  "entitled"  =>  "fields.isIndex.entitled",
                  "helper"    =>  "fields.isIndexDomain.information",
                  "getter"  =>  function(DomainInterface $object) {
                    return (bool) $object->getConfigKey("isIndex", false);
                  },
                  "setter"  =>  function(DomainInterface $object, $value) {
                    return $object->setConfigKey("isIndex", $value);
                  },
                )
              )
            )
            ->add(Field\SwitchField::create("isFollow", array(
                  "entitled"  =>  "fields.isFollow.entitled",
                  "helper"    =>  "fields.isFollowDomain.information",
                  "getter"  =>  function(DomainInterface $object) {
                    return  (bool) $object->getConfigKey("isFollow", false);
                  },
                  "setter"  =>  function(DomainInterface $object, $value) {
                    return $object->setConfigKey("isFollow", $value);
                  },
                )
              )
            )
          ->end()
          ->addGroup("seo")
            ->add(Field\TextField::create("seoTitleSuffixHomepage", array(
              "entitled"  =>  "fields.seoTitleSuffixHomepage.entitled",
              "helper"    =>  "fields.seoTitleSuffixHomepage.information",
              "getter"  =>  function(DomainInterface $object) {
                return $object->getConfigKey("seoTitleSuffixHomepage", null);
              },
              "setter"  =>  function(DomainInterface $object, $value) {
                return $object->setConfigKey("seoTitleSuffixHomepage", $value);
              },
            )))
            ->add(Field\TextField::create("seoTitleSuffix", array(
              "entitled"  =>  "fields.seoTitleSuffix.entitled",
              "helper"    =>  "fields.seoTitleSuffix.information",
              "getter"  =>  function(DomainInterface $object) {
                return $object->getConfigKey("seoTitleSuffix", null);
              },
              "setter"  =>  function(DomainInterface $object, $value) {
                return $object->setConfigKey("seoTitleSuffix", $value);
              },
            )))
          ->end()
        ->end()
        ->addGroup("redirect", "groups.redirect")
          ->add(Field\TextField::create("redirectUrl"))
          ->add(Field\ChoiceField::create("redirectWithUri",
            array(
              "choices.status.no"         =>  false,
              "choices.status.yes"        =>  true,
            ))->setGroupSize(GroupFields::SIZE_COL_5)
          )
        ->end()
    ;





    $formAdminEvent->getFormMapper()
      ->addPopin("popup-editor-favicon", "favicon", array(
          "button"  =>  array(
            "entitled"            =>  "actions.picture.edit",
            "picto"               =>  "",
            "class"               =>  "button-action"
          ),
          "popin"  =>  array(
            "id"            =>  "upload",
            "template"      =>  "uploadEditor",
          )
        )
      )
      ->end()
      ->addPopin("popup-editor-logo", "logo", array(
          "button"  =>  array(
            "entitled"            =>  "actions.picture.edit",
            "picto"               =>  "",
            "class"               =>  "button-action"
          ),
          "popin"  =>  array(
            "id"            =>  "upload",
            "template"      =>  "uploadEditor",
          )
        )
      )
      ->end();
  }
  /**
   * @param FormAdminEvent $formAdminEvent
   *
   * @throws Exception
   */
  protected function formUpdateBefore(FormAdminEvent $formAdminEvent)
  {
    /** @var DomainInterface|EntityInterface $object */
    $object = $formAdminEvent->getFormMapper()->getObject();

    if(!$object->getKeyname()) {
      $object->setKeyname($object->getName());
    }
  }


}
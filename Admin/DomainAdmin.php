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
    $listAdminEvent->getListMapper()
      ->buildDataHydrate(function(DataHydrateORM $dataHydrate) {
        $dataHydrate->addQueryBuilderPaginatorClosure(function(QueryBuilder $queryBuilder) {
          return $queryBuilder
            ->orderBy("root.position", "ASC")
            ->addOrderBy("root.name", "ASC");
        });
      })
      ->addColumn(new Column\Value("domain"))
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
    $formAdminEvent->getFormMapper()
      ->addFieldset("fieldset.right")
        ->setPositionName(Fieldset::POSITION_RIGHT)
        ->setViewName(false)
        ->add(Field\ChoiceField::create("isEnabled",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("onePage",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("isMaster",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\ChoiceField::create("isVirtual",
            array(
              "choices.status.no"         =>  false,
              "choices.status.yes"        =>  true,
            ),  array(
            "attr"        =>  array(
              "data-view-by-choices-parent"   =>  ".form-container",
              "data-view-by-choices-children" =>  ".view-element-by-choices",
              'data-view-by-choices' =>  json_encode(array(
                true           =>  "domain-master",
              ))
            ),
          ))
        )
      ->end()
      ->addFieldset("fieldset.generalInformation")
        ->addGroup("domain")
          ->add(Field\SelectField::create('scheme', array(
                DomainInterface::SCHEME_HTTPS => DomainInterface::SCHEME_HTTPS,
                DomainInterface::SCHEME_HTTP  => DomainInterface::SCHEME_HTTP,
              ), array(
                'required' => true
              )
            )->setGroupSize(GroupFields::SIZE_COL_2)
          )
          ->add(Field\TextField::create("domain")->setGroupSize(GroupFields::SIZE_COL_10))
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
            "container" =>  array('class'=>"view-element-by-choices domain-master"),
            'choice_label' => 'name',
            "required"  =>  $formAdminEvent->getFormMapper()->getObject()->getIsVirtual()
          )
        ))


        ->add(Field\TextField::create("keyname"))
        ->add(Field\TextField::create("name", array("entitled"=>"fields.nameDomain.entitled")))
        ->add(Field\TextField::create("language"))
        ->add(Field\TextField::create("redirectUrl"))
        ->add(Field\ChoiceField::create("redirectWithUri",
          array(
            "choices.status.no"         =>  false,
            "choices.status.yes"        =>  true,
          ))
        )
        ->add(Field\UploadField::create("favicon"))
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
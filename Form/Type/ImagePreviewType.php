<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 01.08.14
 * Time: 13:23
 */

namespace ITM\ImagePreviewBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use ITM\Sonata\ImagePreviewBundle\Twig\Extension;
use Symfony\Component\Security\Acl\Exception\Exception;

class ImagePreviewType extends AbstractType
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $curEntity = $form->getParent()->getData();

        $config = $this->container->getParameter('ITMImagePreviewBundleConfiguration');
        $doctrine = $this->container->get('doctrine');
        $liipFilters = $this->container->getParameter('liip_imagine.filter_sets');

        // Получаем данные о фильтрах, накладываемых на изображение
        $filters = [];
        foreach( $config['entities'] as $bundleName => $bundle )
        {
            foreach( $bundle['bundle'] as $entityName => $entity )
            {
                $entityClass = get_class($curEntity);
                // Проверяем принадлежит ли сущность тому же бандлу и классу, что и описанная в конфигурации
                if( $entityClass == $doctrine->getAliasNamespace($bundleName).'\\'. $entityName)
                {
                    foreach( $entity['entity'] as $fieldName => $field )
                    {
                        if($fieldName != $form->getName()) continue;

                        foreach( $field['field']['formats'] as $format )
                        {
                            $filter = ['name' => $format['format']];
                            if(!isset($liipFilters[$filter['name']]))
                            {
                                throw new InvalidConfigurationException('Liip imagine filter "'.$filter['name'].'" not found');
                            }

                            foreach( $liipFilters[$filter['name']]['filters'] as $liipFilterName => $liipFilter )
                            {
                                if(!in_array($liipFilterName, ['thumbnail'])) throw new InvalidConfigurationException('ITMImagePreview support thumbnail liip filters only!');
                                $filter = array_merge($filter, $liipFilter);
                            }

                            $filters[] = $filter;
                        }

                        break(3);
                    }
                }
            }
        }

        $view->vars['filters'] = $filters;
    }

    public function getParent()
    {
        return FileType::class;
    }

    public function getBlockPrefix()
    {
        return 'itm_image_preview';
    }
} 
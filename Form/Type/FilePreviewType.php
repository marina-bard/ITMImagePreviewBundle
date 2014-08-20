<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 20.08.14
 * Time: 20:56
 */

namespace ITM\Sonata\ImagePreviewBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilePreviewType extends AbstractType
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
        $pathResolver = $this->container->get('itm.image.preview.path.resolver');

        $filePath = $pathResolver->getPath( $curEntity, $form->getName() );

        $view->vars['info'] = stat($filePath);
        $view->vars['info']['mime'] = mime_content_type($filePath);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'itm_file_preview';
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 01.08.14
 * Time: 13:23
 */

namespace ITM\Sonata\ImagePreviewBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImagePreviewType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

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
        return 'itm_image_preview';
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 01.08.14
 * Time: 17:09
 */

namespace ITM\ImagePreviewBundle\Twig\Extension;

use ITM\ImagePreviewBundle\Resolver\PathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImagePreviewExtension extends \Twig_Extension
{
    private static $pathResolver;
    private static $container;

    public function __construct(PathResolver $pathResolver, ContainerInterface $container)
    {
        self::$pathResolver = $pathResolver;
        self::$container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('itm_ipw_url', array($this, 'resolveUrl')),
            new \Twig_SimpleFilter('itm_ipw_path', array($this, 'resolvePath')),
            new \Twig_SimpleFilter('itm_ipw_exists', array($this, 'imageExists')),
        );
    }

    public static function resolveUrl( $entity, $field )
    {
        return self::$pathResolver->getUrl($entity, $field);
    }

    public static function resolvePath( $entity, $field )
    {
        return self::$pathResolver->getPath($entity, $field, true);
    }

    public static function imageExists( $entity, $field )
    {
        $fs = new Filesystem();
        return $fs->exists( self::$pathResolver->getPath($entity, $field));
    }

    public function getName()
    {
        return 'itm_image_preview_extension';
    }
}
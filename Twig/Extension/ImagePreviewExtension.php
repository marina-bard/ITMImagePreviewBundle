<?php
/**
 * Created by PhpStorm.
 * User: archer.developer
 * Date: 01.08.14
 * Time: 17:09
 */

namespace ITM\Sonata\ImagePreviewBundle\Twig\Extension;

use Symfony\Component\Filesystem\Filesystem;

class ImagePreviewExtension extends \Twig_Extension
{
    private static $pathResolver;
    private static $container;

    public function __construct($pathResolver, $container)
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

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('itmIPW_ListFilters', [$this, 'listFilters']),
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

    /**
     * Функция возвращает массив форматов (liip фильтров) для обработки
     *
     * @param $curEntity
     * @param $curField
     * @return array
     *
     * @todo Возможно, эту информацию стоило бы собрать в Type в BuildView,
     * @todo но тогда туда пришлось бы передавать контейнер при сборке формы
     */
    public static function listFilters( $curEntity, $curField )
    {
        $config = self::$container->getParameter('ITMImagePreviewBundleConfiguration');
        $doctrine = self::$container->get('doctrine');

        foreach( $config['entities'] as $bundleName => $bundle )
        {
            foreach( $bundle['bundle'] as $entityName => $entity )
            {
                $entityClass = get_class($curEntity);
                // Проверяем принадлежит ли сущность тому же бандлу и классу, что и описанная в конфигурации
                if( $entityClass == $doctrine->getAliasNamespace($bundleName).'\\'. $entityName)
                {
                    foreach( $entity['entity'] as $field )
                    {
                        $formats = [];
                        foreach( $field['field']['formats'] as $format )
                        {
                            $formats[] = $format['format'];
                        }

                        return $formats;
                    }
                }
            }
        }
    }

    public function getName()
    {
        return 'itm_image_preview_extension';
    }
}
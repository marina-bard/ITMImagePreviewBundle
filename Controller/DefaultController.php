<?php

namespace ITM\Sonata\ImagePreviewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $filterName = $request->get('name');
        $filePath = $request->get('filepath');
        $scale = $request->get('scale');
        $x = $request->get('x');
        $y = $request->get('y');

        $container = $this->container;

        $imagemanagerResponse = $container->get('liip_imagine.controller');
        $pathResolver = $container->get('liip_imagine.cache.resolver.default');
        $filterConfiguration = $container->get('liip_imagine.filter.configuration');

        $configuration = $filterConfiguration->get($filterName);

        $size = $configuration['filters']['thumbnail']['size'];

        unset($configuration['filters']['thumbnail']);
        $max = max($size);
        $side = ($size[0] > $size[1]) ? 'heighten' : 'widen';
        $configuration['filters']['relative_resize'][$side] = $max;

        $configuration['filters']['crop']['size'] = [$size[0]/$scale, $size[1]/$scale];
        $configuration['filters']['crop']['start'] = [$x, $y];
        $configuration['filters']['upscale']['min'] = $size;
        
        $filterConfiguration->set($filterName, $configuration);

        $pathResolver->remove([$filePath], [$filterName]);

        $imagemanagerResponse->filterAction($request, $filePath, $filterName);

        $thumbUrl = $pathResolver->resolve($filePath, $filterName);

        return new Response($thumbUrl);
    }
}

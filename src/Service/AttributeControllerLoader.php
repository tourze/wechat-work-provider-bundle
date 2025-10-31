<?php

namespace WechatWorkProviderBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;
use WechatWorkProviderBundle\Controller\CorpCallbackController;
use WechatWorkProviderBundle\Controller\ProviderCallbackController;
use WechatWorkProviderBundle\Controller\SuiteCallbackController;

#[AutoconfigureTag(name: 'routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();

        $collection->addCollection($this->controllerLoader->load(CorpCallbackController::class));
        $collection->addCollection($this->controllerLoader->load(ProviderCallbackController::class));
        $collection->addCollection($this->controllerLoader->load(SuiteCallbackController::class));

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }
}

<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class RequestMethodListener
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if ('OPTIONS' === $event->getRequest()->getMethod()) {
            $test = json_encode(['Allow' => $this->router->getRouteCollection()->get($event->getRequest()->get('_route'))->getMethods()]);
            $event->getResponse()->setContent($test);
        }
    }
}

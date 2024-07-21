<?php

namespace Drupal\login_guard\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AddNoIndexHeader implements EventSubscriberInterface
{

    // noindexをつける
    protected $configFactory;

    public function __construct(ConfigFactoryInterface $config_factory)
    {
        $this->configFactory = $config_factory;
    }


    public static function getSubscribedEvents()
    {
        $events[KernelEvents::RESPONSE][] = ['onRespond', -100];
        return $events;
    }

    public function onRespond(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) return;

        // 設定値を取得
        $config = $this->configFactory->get('login_guard.settings');
        $noindex = $config->get('noindex');

        if ($noindex) {
            $response = $event->getResponse();

            $routes = [
                'login' => 'user.login',
                'register' => 'user.register',
                'password' => 'user.pass',
            ];

            $route = \Drupal::routeMatch()->getRouteName();
            if (in_array($route, $routes)) {
                $key = array_search($route, $routes);
                if (!empty($key)) {
                    $response->headers->set('X-Robots-Tag', 'noindex');
                }
            }
        }
    }
}

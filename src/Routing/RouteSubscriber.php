<?php

namespace Drupal\login_guard\Routing;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase
{
    const CHANGE_PREFIX = "site";
    protected $configFactory;

    public function __construct(ConfigFactoryInterface $config_factory)
    {
        $this->configFactory = $config_factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('config.factory')
        );
    }

    /**
     * https://ibuildings.com/blog/2016/04/drupal-8-display-user-login-admin-theme/
     * 管理用テーマの表示チェックを匿名ユーザーでもチェックすると表示される
     * ユーザー関連のルートを管理用テーマの表示で表示するように設定します。
     *「admin/people/permissions」
     */
    protected function alterRoutes(RouteCollection $collection)
    {
        $config = $this->configFactory->get('login_guard.settings');
        $user_routing_change = $config->get('user_routing_change');

        foreach ($collection->all() as $route_name => $route) {
            if (strpos($route_name, 'user.') === 0) {
                $route->setOption('_admin_route', true);

                if ($user_routing_change) {
                    $original_path = $route->getPath();
                    $new_path = str_replace('/user/', '/' . self::CHANGE_PREFIX . '_user/', $original_path); // 'user/' を 'site_user/' に置き換え
                    $route->setPath($new_path);
                }
            }
        }
    }
}

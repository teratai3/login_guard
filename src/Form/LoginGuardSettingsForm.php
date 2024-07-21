<?php

namespace Drupal\login_guard\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\ProxyClass\Routing\RouteBuilder;
use Drupal\Core\Form\ConfigFormBase;


class LoginGuardSettingsForm extends ConfigFormBase
{

  const CONFIG_NAME = 'login_guard.settings';

  /**
   * @var \Drupal\Core\ProxyClass\Routing\RouteBuilder.
   */
  protected $routeBuilder;

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'login_guard_settings_form';
  }

  /**
   * Class constructor.
   */
  public function __construct(RouteBuilder $route_builder)
  {
    $this->routeBuilder = $route_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      self::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config(self::CONFIG_NAME);

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => '
      <p>このページでは、Login Guardモジュールの設定を変更できます。必要に応じて以下のオプションを有効または無効にしてください。</p>
      <p>また、「admin/people/permissions」の管理用テーマ権限を匿名ユーザーにもチェックすることで、ユーザーで管理用テーマを表示できるようになります。</p>
      ',
    ];

    $form['noindex'] = [
      '#type' => 'checkbox',
      '#title' => 'noindexを有効にする',
      '#default_value' => $config->get('noindex'),
    ];

    $form['recaptcha'] = [
      '#type' => 'checkbox',
      '#title' => 'reCAPTCHAを有効にする',
      '#default_value' => $config->get('recaptcha'),
    ];

    $form['user_routing_change'] = [
      '#type' => 'checkbox',
      '#title' => 'ユーザーのルーティングを変更する',
      '#default_value' => $config->get('user_routing_change'),
    ];

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->config(self::CONFIG_NAME)
      ->set('noindex', $form_state->getValue('noindex'))
      ->set('recaptcha', $form_state->getValue('recaptcha'))
      ->set('user_routing_change', $form_state->getValue('user_routing_change'))
      ->save();

    $this->routeBuilder->rebuild();
    parent::submitForm($form, $form_state);
  }
}

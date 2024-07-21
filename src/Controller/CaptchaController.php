<?php

namespace Drupal\login_guard\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Drupal\Core\Controller\ControllerBase;

class CaptchaController extends ControllerBase
{

  protected $session;

  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }

  public static function create($container)
  {
    return new static(
      $container->get('session')
    );
  }

  public function generateCaptcha()
  {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $code = substr(str_shuffle($chars), 0, 6);
    $this->session->set('captcha_code', md5($code));

    $width = 150;
    $height = 50;
    $image = imagecreate($width, $height);

    // 背景色
    imagecolorallocate($image, 255, 255, 255);
    // 文字色
    $text_color = imagecolorallocate($image, 0, 0, 0);
    // ノイズ色
    $noise_color = imagecolorallocate($image, 100, 120, 180);

    // ランダムなノイズを追加
    for ($i = 0; $i < 1000; $i++) {
      imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noise_color);
    }

    // ランダムな線を追加
    for ($i = 0; $i < 10; $i++) {
      imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
    }

    // 文字を追加
    for ($i = 0; $i < strlen($code); $i++) {
      $x = 20 + ($i * 20);
      $y = mt_rand(15, 35);
      imagestring($image, 5, $x, $y, $code[$i], $text_color);
    }

    // 画像を出力
    ob_start();
    imagepng($image);
    $image_data = ob_get_clean();

    imagedestroy($image);

    $response = new Response($image_data);
    $response->headers->set('Content-Type', 'image/png');
    return $response;
  }
  
}

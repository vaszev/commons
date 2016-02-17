<?php
namespace Vaszev\CommonsBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;

class TemplateExtension extends Twig_Extension {

  protected $container;



  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }



  public function getFilters() {
    return array(
        new \Twig_SimpleFilter('minutesTime', array($this, 'minutesTimeFilter')),
        new \Twig_SimpleFilter('dayName', array($this, 'dayNameFilter')),
        new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        new \Twig_SimpleFilter('imgSize', array($this, 'imgSizeFilter')),
        new \Twig_SimpleFilter('friendly', array($this, 'friendlyFilter')),
        new \Twig_SimpleFilter('entityCheck', array($this, 'entityCheck')),
    );
  }



  public function getFunctions() {
    return array(
        new \Twig_SimpleFunction('lorem', array($this, 'loremIpsum')),
    );
  }



  public function loremIpsum($words = 1, $onlyalpha = false, $html = false) {
    $sample = "Ea vix ornatus offendit delicatissimi, perfecto similique in has. Summo consetetur at vis. Vix an nulla malorum sapientem, nostrud voluptatum cum ex, an usu civibus accusam salutatus. Ex magna voluptaria his, has latine convenire assentior in, vel insolens pertinacia ut. Id justo ullum meliore sit, cu tempor nemore ius.";

    $tags = array('h2', 'h3', 'h4', 'strong', 'span', 'underline', 'p', 'em', 'a');
    $tmp = array();

    $arr = explode(" ", $sample);
    for ($w = 0; $w < ($words); $w++) {
      shuffle($arr);
      $first = $arr[0];
      if ($onlyalpha) {
        $tmp[] = preg_replace("/[^a-zA-Z]+/", "", $first);
      } else {
        $tmp[] = $first;
      }
      if ($html) {
        shuffle($tags);
        $tag = $tags[0];
        $tmp[] = '<' . ($tag == "a" ? 'a href="javascript:void(0);"' : $tag) . '>' . $first . '</' . $tag . '>';
      }
    }
    shuffle($tmp);

    return implode(" ", $tmp);
  }



  public function entityCheck($entity) {
    try {
      if (empty($entity)) {
        throw new \Exception("entity is empty");
      }
      if (!is_object($entity)) {
        throw new \Exception("entity is not an object");
      }
      $entity->getId();

      return $entity;
    } catch (\Exception $e) {
      return false;
    }
  }



  public function friendlyFilter($str) {
    $commons = $this->container->get('vaszev_commons.functions');

    return $commons->replaceNonAlphanumericChars($str);
  }



  public function minutesTimeFilter($number) {
    $hours = floor($number / 60);
    $minutes = $number % 60;
    $str = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);

    return $str;
  }



  public function dayNameFilter($number) {
    $days = [1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday',];

    return $days[$number];
  }



  public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',', $currency = '$') {
    $price = number_format($number, $decimals, $decPoint, $thousandsSep);
    $price = $currency . $price;

    return $price;
  }



  public function imgSizeFilter($path, $size = 'small', $crop = false) {
    $outer = strpos($path, 'http');
    if ($outer === false) {
      // not outer
    } else {
      // outer link, start with http
      return $path;
    }
    $rootDir = $this->container->get('kernel')->getRootDir();
    $commons = $this->container->get('vaszev_commons.functions');
    $docPath = trim($this->container->getParameter('vaszev_commons.docs'), '/');
    $defaultImage = __DIR__ . '/' . $this->container->getParameter('vaszev_commons.default_image');
    $defaultImageNewName = 'default-transparent.png';
    $defaultImageDestination = $rootDir . '/../web/' . $docPath . '/' . $defaultImageNewName;
    // copy default image if not exists
    if (!file_exists($defaultImageDestination)) {
      copy($defaultImage, $defaultImageDestination);
    }
    $unfold = explode('/', $path);
    $fileStr = end($unfold);
    $originalUrl = $docPath . '/' . $fileStr;
    $resizedUrl = $docPath . '/' . $size . ($crop ? '-cropped' : '') . '/' . $fileStr;
    // pre-check for image, get default is it fails
    $originalImageSize = @getimagesize($originalUrl);
    if (empty($originalImageSize)) {
      // not an image
      $originalUrl = $docPath . '/' . $defaultImageNewName;
      $resizedUrl = $docPath . '/' . $size . ($crop ? '-cropped' : '') . '/' . $defaultImageNewName;
    }
    // finally, get that image with correct size
    if (!file_exists($resizedUrl)) {
      dump($resizedUrl);
      $resizedUrl = $commons->getImageVariant($originalUrl, $size, $crop);
    } else {
      $resizedUrl = '/' . $resizedUrl;
    }

    return $resizedUrl;
  }



  public function getName() {
    return 'template_extension';
  }
}
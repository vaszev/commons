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
    );
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



  public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',', $currency='$') {
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
    $defaultImageDestination = $rootDir .'/../web/'. $docPath . '/' . $defaultImageNewName;
    $unfold = explode('/', $path);
    $fileStr = end($unfold);
    $oldUrl = $docPath . '/' . $fileStr;
    $newUrl = str_replace($docPath, ($docPath . '/' . $size), $fileStr);
    // pre-check for image, get default is it fails
    $imageSize = @getimagesize($oldUrl);
    if (empty($imageSize)) {
      $oldUrl = $docPath . '/' . $defaultImageNewName;
      $newUrl = str_replace($docPath, ($docPath . '/' . $size), $defaultImageNewName);
    }
    // copy default image if not exists
    if (!file_exists($defaultImageDestination)) {
      copy($defaultImage, $defaultImageDestination);
    }
    // finally, get that image with correct size
    if (!file_exists($newUrl)) {
      $newUrl = $commons->getImageVariant($oldUrl, $size, $crop);
    }

    return $newUrl;
  }



  public function getName() {
    return 'template_extension';
  }
}
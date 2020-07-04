<?php

namespace Vaszev\CommonsBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;
use Twig_Extension;

class TemplateExtension extends Twig_Extension {

  protected $container;



  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }



  public function getFilters() {
    return [
        new \Twig_SimpleFilter('joinByKey', [$this, 'joinByKey']),
        new \Twig_SimpleFilter('secret', [$this, 'secret']),
        new \Twig_SimpleFilter('minutesTime', [$this, 'minutesTimeFilter']),
        new \Twig_SimpleFilter('dayName', [$this, 'dayNameFilter']),
        new \Twig_SimpleFilter('price', [$this, 'priceFilter']),
        new \Twig_SimpleFilter('imgSize', [$this, 'imgSizeFilter']),
        new \Twig_SimpleFilter('imgSizeKept', [$this, 'imgSizeFilterKept']),
        new \Twig_SimpleFilter('friendly', [$this, 'friendlyFilter']),
        new \Twig_SimpleFilter('entityCheck', [$this, 'entityCheck']),
        new \Twig_SimpleFilter('metricToImperial', [$this, 'metricToImperial']),
        new \Twig_SimpleFilter('country', [$this, 'countryFilter']),
        new \Twig_SimpleFilter('ordinal', [$this, 'ordinal']),
        new \Twig_SimpleFilter('strPos', [$this, 'strPos']),
        new \Twig_SimpleFilter('strReplace', [$this, 'strReplace']),
        new \Twig_SimpleFilter('strPad', [$this, 'strPad']),
        new \Twig_SimpleFilter('verifiedDefault', [$this, 'verifiedDefault']),
        new \Twig_SimpleFilter('numberScale', [$this, 'numberScale']),
        new \Twig_SimpleFilter('autoPunctuation', [$this, 'autoPunctuation']),
        new \Twig_SimpleFilter('quotation', [$this, 'quotation'], ['is_safe' => ['html']]),
        new \Twig_SimpleFilter('resolution', [$this, 'resolution']),
        new \Twig_SimpleFilter('br2nl', [$this, 'br2nl']),
    ];
  }



  public function getFunctions() {
    return [
        new \Twig_SimpleFunction('lorem', [$this, 'loremIpsum']),
        new \Twig_SimpleFunction('rnd', [$this, 'rndGen']),
    ];
  }



  public function rndGen($start = 0, $end = 100, $float = false) {
    $tmp = [];
    $tmp[] = (integer)$start;
    $tmp[] = (integer)$end;
    sort($tmp, SORT_NUMERIC);
    $rnd = rand($tmp[0], $tmp[1]);
    if ($float) {
      $d = rand(1, 99) / 100;
      $rnd += $d;
    }

    return $rnd;
  }



  public function loremIpsum($words = 1, $onlyalpha = false, $html = false) {
    $sample = "Ea vix ornatus offendit delicatissimi, perfecto similique in has. Summo consetetur at vis. Vix an nulla malorum sapientem, nostrud voluptatum cum ex, an usu civibus accusam salutatus. Ex magna voluptaria his, has latine convenire assentior in, vel insolens pertinacia ut. Id justo ullum meliore sit, cu tempor nemore ius.";

    $tags = ['h2', 'h3', 'h4', 'strong', 'span', 'underline', 'p', 'em', 'a'];
    $tmp = [];

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



  public function friendlyFilter($str, $default = 'untitled', $joker = "-") {
    $commons = $this->container->get('vaszev_commons.functions');
    $str = trim($commons->friendlyFilter($str, $joker));
    $str = ($str ? $str : $default);

    return $str;
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
    // $defaultImage = __DIR__ . '/' . $this->container->getParameter('vaszev_commons.default_image');
    $defaultImage = $this->container->getParameter('vaszev_commons.default_image');
    $defaultImageNewName = 'default-transparent.png';
    $defaultImageDestination = $rootDir . '/../web/' . $docPath . '/' . $defaultImageNewName;
    // copy default image if not exists
    if (!file_exists($defaultImageDestination)) {
      copy($defaultImage, $defaultImageDestination);
    }
    $pathParts = explode('/', $path);
    $fileStr = array_pop($pathParts);
    $originalUrl = implode('/', $pathParts) . '/' . $fileStr;
    $resizedUrl = implode('/', $pathParts) . '/' . $size . ($crop ? '-cropped' : '') . '/' . $fileStr;
    // pre-check for image, get default is it fails
    $originalImageSize = @getimagesize($originalUrl);
    if (empty($originalImageSize)) {
      // not an image
      $originalUrl = $docPath . '/' . $defaultImageNewName;
      $resizedUrl = $docPath . '/' . $size . ($crop ? '-cropped' : '') . '/' . $defaultImageNewName;
    }
    // finally, get that image with correct size
    if (!file_exists($resizedUrl)) {
      $resizedUrl = $commons->getImageVariant($originalUrl, $size, $crop);
    } else {
      $resizedUrl = '/' . $resizedUrl;
    }

    return $resizedUrl;
  }



  public function imgSizeFilterKept($path, $size = 'small') {
    error_reporting(E_ERROR);
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
    // $defaultImage = __DIR__ . '/' . $this->container->getParameter('vaszev_commons.default_image');
    $defaultImage = $this->container->getParameter('vaszev_commons.default_image');
    $defaultImageNewName = 'default-transparent.png';
    $defaultImageDestination = $rootDir . '/../web/' . $docPath . '/' . $defaultImageNewName;
    // copy default image if not exists
    if (!file_exists($defaultImageDestination)) {
      copy($defaultImage, $defaultImageDestination);
    }
    $pathParts = explode('/', $path);
    $fileStr = end($pathParts);
    $originalUrl = implode('/', $pathParts) . '/' . $fileStr;
    $resizedUrl = implode('/', $pathParts) . '/' . $size . '-kept' . '/' . $fileStr;
    // pre-check for image, get default is it fails
    $originalImageSize = @getimagesize($originalUrl);
    if (empty($originalImageSize)) {
      // not an image
      $originalUrl = $docPath . '/' . $defaultImageNewName;
      $resizedUrl = $docPath . '/' . $size . '-kept' . '/' . $defaultImageNewName;
    }
    // finally, get that image with correct size
    if (!file_exists($resizedUrl)) {
      $resizedUrl = $commons->getImageVersion($originalUrl, $size);
    } else {
      $resizedUrl = '/' . $resizedUrl;
    }

    return $resizedUrl;

  }



  public function metricToImperial($cm = 0) {
    $commons = $this->container->get('vaszev_commons.functions');

    return $commons->metricToImperial($cm);
  }



  public function getName() {
    return 'template_extension';
  }



  public function countryFilter($countryCode) {
    $c = Intl::getRegionBundle()->getCountryName($countryCode);

    return ($c ? $c : $countryCode);
  }



  public function ordinal($number) {
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
      return $number . 'th';
    } else {
      return $number . $ends[$number % 10];
    }
  }



  public function strPos($string, $findMe) {
    if (empty($string) || empty($findMe)) {
      return null;
    }

    return stripos($string, $findMe);
  }



  public function strReplace($string, $findMe, $replace) {
    $ret = str_ireplace($findMe, $replace, $string);

    return $ret;
  }



  public function verifiedDefault($entity = null, $defaultValue = null) {
    try {
      if (empty($entity)) {
        throw new \Exception("entity is empty");
      }
      if (!is_object($entity)) {
        throw new \Exception("entity is not an object");
      }
      if (!empty($entity->getDeleted())) {
        throw new \Exception("entity was deleted");
      }
      $value = $entity->__toString();

      return $value;
    } catch (\Exception $e) {
      return $defaultValue;
    }
  }



  public function numberScale($number, $decimal = 1) {
    return $this->container->get('vaszev_commons.functions')->numberScale($number, $decimal);
  }



  public function autoPunctuation($string = null, $prefix = null, $postfix = null) {
    if (empty($string)) {
      return null;
    }
    $string = trim($string);
    $end = substr($string, -1);
    $chk = ctype_alnum($end);
    $txt = $prefix . ($chk ? $string . '.' : $string) . $postfix;

    return $txt;
  }



  public function quotation($string, $stripTags = true) {
    $string = html_entity_decode($string);
    $string = nl2br($string);
    if ($stripTags) {
      $string = strip_tags($string);
    }
    $string = html_entity_decode($string, ENT_QUOTES);
    $string = str_replace("\n", ' ', $string);
    $string = str_replace("\r", '', $string);
    $string = str_replace('"', "'", $string);

    return $string;
  }



  public function resolution($path, $type = 'width') {
    try {
      $info = @getimagesize($path);
      if (empty($info)) {
        throw new \Exception('invalid image');
      }
      if ($type == 'width') {
        return $info[0];
      } else {
        return $info[1];
      }
    } catch (\Exception $e) {
      return null;
    }
  }



  public function secret($str) {
    $str = str_rot13($str);
    $str = str_shuffle($str);

    return $str;
  }



  public function strPad($str, $padLength, $padString, $orient = STR_PAD_LEFT) {
    return str_pad($str, $padLength, $padString, $orient);
  }



  public function br2nl($str) {
    $breaks = ["<br />", "<br>", "<br/>"];
    $str = str_ireplace($breaks, "\r\n", $str);

    return $str;
  }



  public function joinByKey($arr, $glue = ',', $index = null) {
    if (!is_array($arr) || empty($index)) {
      return $arr;
    }
    $tmp = [];
    foreach ($arr as $item) {
      foreach ($item as $key => $val) {
        if ($key == $index) {
          $tmp[] = $val;
        }
      }
    }
    $ret = implode($glue, $tmp);

    return $ret;
  }

}


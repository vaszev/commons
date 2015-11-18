<?php

  namespace Vaszev\CommonsBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Vaszev\CommonsBundle\Service\Functions;

  class DefaultController extends Controller {
    /**
     * @Route("/test", name="vaszev-commons-test")
     * @Template()
     */
    public function indexAction() {
      $ret = [];

      /** @var  $echo Functions */
      $commons = $this->get('vaszev_commons.functions');
      $ret['randomTxt'] = $commons->loremIpsum(500, false, true);

      return $ret;
    }


  }

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
      $ret['friendly'] = $commons->removeAccents("Géwalteg d'Hiezer en aus, űm sëtzen beschté d'Bëscher den! Nő vill Well Fríémd get, Haüs zielen ás zúm, dat hü brét d'Pied! Dé deé stón blëtzen d'Blúmme! Lánd Dűerf sin ún.
 Nün Bénn Kaffi Stíeren ké, rem fű Haus d'Kanner. Mir an Húnn schlon klinzecht, et blo Heck Bass dúérch, dérbéi űechter fergiéss sin en. Néierens d'Lëtzebüergér um sou, en dén stét Fläiß Plett'len. Nei Riesén d'Bëscher wa, Feld Blűmmen aüs hu. Fü háűt d'Kamäíner dan! Dat d'Löft Säiten prächteg en!
 Hären welle Stieren dá öch, mä géi keng räich, Bass Hierz gewëss um ass! Do Gáas Zalot méi, ás déser d'Pied wellen vun. En Miér denkt Klarinett all. Ech d'Land genűch án, ze soubal Grénge get. Híé Híerz Hämmélsbrot ké, ké Mamm géwëss Hémecht wär? Űn sou Schíet Dohannen.
 Mönn schéí Gesträich am ech, ke d'Leít gemáacht däischter ech, gét d'Loft béssért jo! Haűs denkt Stret méi mä. Éiwég bereét uechtér ons si, wee vu Ierd keng beschéngt! An räich brómmt ech, get vill main zënter vu? Méi Biérég Keppchén wa, vu zënne d'Land verstoppen őft. Dach d'Land Pöufank gei ze, kille d'Wise ze wéi. Rëm grőúss lössen si!
      Rém et Hünn fond, dér Stréi Grénge génűch fu? Esoú Kirmesdág um wéi. Land d'wäiss Margréitchen jó dan, zënne heémléch get no, de vún Dúerf Gréngé. D'Pied Dóhannén gét no, gei séngt Dohannen mä. Et wéi stón álles? Fír dann welle si, Bass éiweg ons as, Wand botze hinnén hir hú.
      ");
      $ret['filename'] = $commons->repairFileName("Géwalteg d'Hiezer en aus, űm sëtzen beschté d'Bëscher den!.txt");

      return $ret;
    }


  }

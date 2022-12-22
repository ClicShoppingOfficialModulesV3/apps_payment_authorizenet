<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\AuthorizeNet\AuthorizeNet;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_AuthorizeNet = new AuthorizeNet();
      Registry::set('AuthorizeNet', $CLICSHOPPING_AuthorizeNet);

      $this->app = $CLICSHOPPING_AuthorizeNet;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/log');
    }
  }

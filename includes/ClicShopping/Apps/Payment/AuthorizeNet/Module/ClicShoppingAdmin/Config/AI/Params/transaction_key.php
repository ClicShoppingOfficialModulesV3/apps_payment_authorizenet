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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\AI\Params;

  class transaction_key extends \ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '';
    public $sort_order = 40;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_authorizenet_transaction_key_title');
      $this->description = $this->app->getDef('cfg_authorizenet_transaction_key_desc');
    }
  }

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

  use ClicShopping\OM\HTML;

  class server extends \ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'Test';
    public $sort_order = 30;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_authorizenet_server_title');
      $this->description = $this->app->getDef('cfg_authorizenet_server_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'Live', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_server_live') . ' ';
      $input .= HTML::radioField($this->key, 'Test', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_server_test');

      return $input;
    }
  }
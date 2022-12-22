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

  class transaction_method extends \ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'Authorization';
    public $sort_order = 50;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_authorizenet_transaction_method_title');
      $this->description = $this->app->getDef('cfg_authorizenet_transaction_method_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'Authorization', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_transaction_method_authorization') . ' ';
      $input .= HTML::radioField($this->key, 'Capture', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_transaction_method_capture');

      return $input;
    }
  }
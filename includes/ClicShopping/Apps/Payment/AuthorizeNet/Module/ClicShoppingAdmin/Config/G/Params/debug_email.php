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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\G\Params;

  use ClicShopping\OM\HTML;

  class debug_email extends \ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'True';
    public $sort_order = 100;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_authorizenet_status_debug_title');
      $this->description = $this->app->getDef('cfg_authorizenet_status_debug_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'True', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_status_debug_true') . ' ';
      $input .= HTML::radioField($this->key, 'False', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_authorizenet_status_debug_false');

      return $input;
    }
  }

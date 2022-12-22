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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Module\Hooks\Shop\CheckoutProcess;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\AuthorizeNet\AuthorizeNet as AuthorizeNetApp;

  class Process implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('AuthorizeNet')) {
        Registry::set('AuthorizeNet', new AuthorizeNetApp());
      }

      $this->app = Registry::get('AuthorizeNet');
    }

    private function saveOdoo()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      if ((defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE') && CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE == 'True') && (defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_ORDER_CATALOG') && CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_ORDER_CATALOG != 'none')) {
        $delivery_postcode = $CLICSHOPPING_Order->delivery['postcode'];
        $billing_postcode = $CLICSHOPPING_Order->billing['postcode'];

        if (defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_ORDER_CATALOG') && CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_ORDER_CATALOG == 'order') {
          Registry::get('Hooks')->call(CLICSHOPPING_APP_WEBSERVICE_ODOO_VERSION, 'CheckoutProcessInvoice', 'save');
        } else {
          Registry::get('Hooks')->call(CLICSHOPPING_APP_WEBSERVICE_ODOO_VERSION, 'CheckoutProcessOrder', 'save');
        }
      }

    }

    public function execute()
    {
      $this->saveOdoo();
    }
  }
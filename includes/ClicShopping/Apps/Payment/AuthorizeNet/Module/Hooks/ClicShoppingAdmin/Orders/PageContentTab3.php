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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Module\Hooks\ClicShoppingAdmin\Orders;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\AuthorizeNet\AuthorizeNet as AuthorizeNetApp;

  class PageContentTab3 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('AuthorizeNet')) {
        Registry::set('AuthorizeNet', new AuthorizeNetApp());
      }

      $this->app = Registry::get('AuthorizeNet');
    }

    public function display()
    {

      if (!defined('CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS')) {
        return false;
      }

      $this->app->loadDefinitions('hooks/ClicShoppingAdmin/orders/page_content_tab_3');

      $content = '<!-- boxtal start -->';
      $content .= '<div class="separator"></div>';
      $content .= '<div class="row" id="boxtalButton">';
      $content .= '<span class="col-md-2"><a href="https://account.authorize.net/" target="_blank" class="btn btn-primary" role="button">' . $this->app->getDef('text_authorizenet_manual_account') . '</a>';
      $content .= '</div>';
      $content .= '<!-- boxtal end -->';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start order tracking     -->
<!-- ######################## -->
<script>
$('#ErpOrder').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End order tracking      -->
<!-- ######################## -->
EOD;


      return $output;
    }

  }

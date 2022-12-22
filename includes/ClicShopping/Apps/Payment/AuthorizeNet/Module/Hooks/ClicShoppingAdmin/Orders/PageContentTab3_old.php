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

  class PageContentTab3Old implements \ClicShopping\OM\Modules\HooksInterface
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

      $this->app->loadDefinitions('hooks/ClicShoppingAdmin/orders/tab');

      $output = '';

      $Qc = $this->app->db->prepare('select *
                                     from :table_authorizenet_reference
                                     where order_id = :order_id
                                   ');
      $Qc->bindInt(':order_id', $_GET['oID']);
      $Qc->execute();

      if ($Qc->fetch() !== false) {
        /*
                if (CLICSHOPPING_APP_AUTHORIZENET_AI_SERVER == 'Test') {
                  $authorizenet_button = HTML::button($this->app->getDef('button_view_at_authorizenet'), null, 'https://www.authorizenetpaiement.fr/fr/test/identification/', 'info', ['newwindow' => 'blank']);
                } else {
                  $authorizenet_button = HTML::button($this->app->getDef('button_view_at_authorizenet'), null, 'https://www.authorizenetpaiement.fr/fr/identification/', 'info', ['newwindow' => 'blank']);
                }
        */
        $tab_title = addslashes($this->app->getDef('tab_title'));

        $content = '
          <div class="separator"></div>
            <div class="col-md-12">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <td>
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr class="dataTableHeadingRow">
                        <th>' . $this->app->getDef('table_heading_date') . '</th>
                        <th>' . $this->app->getDef('table_heading_ref_number') . '</th>
                        <th>' . $this->app->getDef('table_heading_order_id') . '&nbsp;</th>
                        <th>' . $this->app->getDef('table_heading_tpe') . '</th>
                        <th>' . $this->app->getDef('table_heading_montant') . '&nbsp;</th>
                        <th>' . $this->app->getDef('table_heading_code_retour') . '&nbsp;</th>
                        <th>' . $this->app->getDef('table_heading_retour_plus') . '</th>
                        <th>' . $this->app->getDef('table_heading_ip_customer') . '&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
                      <td>' . $Qc->value('date') . '</td>
                      <td>' . $Qc->value('ref_number') . '</td>
                      <td>' . $Qc->valueInt('order_id') . '</td>
                      <td>' . $Qc->value('TPE') . '</td>
                      <td>' . $Qc->value('montant') . '</td>
                      <td>' . $Qc->value('code_retour') . '</td>
                      <td>' . $Qc->value('retourPLUS') . '</td>
                      <td>' . $Qc->value('ipclient') . '</td>
                    </tbody>
                  </table>
                </td>
              </table>
            </div>
          </div>
         ';

        $output = <<<EOD
<div class="tab-pane" id="section_AuthorizeNetApp_content">
  <div class="mainTitle"></div>
  <div class="adminformTitle">
  <div class="separator"></div>
    {$authorizenet_button}
    {$content}
  </div>
</div>

<script>
$('#section_AuthorizeNetApp_content').appendTo('#orderTabs .tab-content');
$('#orderTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_AuthorizeNetApp_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
EOD;

      }
      return $output;
    }

  }

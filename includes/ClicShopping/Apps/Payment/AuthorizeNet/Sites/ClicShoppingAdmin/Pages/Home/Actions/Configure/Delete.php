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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Delete extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_AuthorizeNet = Registry::get('AuthorizeNet');
      $CLICSHOPPING_Composer = Registry::get('Composer');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('AuthorizeNetAdminConfig' . $current_module);
      $m->uninstall();

      static::removeMenu();
      $CLICSHOPPING_Composer->remove('authorizenet/authorizenet');

      Cache::clear('menu-administrator');

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_AuthorizeNet->getDef('alert_module_uninstall_success'), 'success', 'AuthorizeNet');

      $CLICSHOPPING_AuthorizeNet->redirect('Configure&module=' . $current_module);
    }

    private static function removeMenu()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_payment_authorizenet']);

      if ($Qcheck->fetch()) {

        $QMenuId = $CLICSHOPPING_Db->prepare('select id
                                              from :table_administrator_menu
                                              where app_code = :app_code
                                            ');

        $QMenuId->bindValue(':app_code', 'app_payment_authorizenet');
        $QMenuId->execute();

        $menu = $QMenuId->fetchAll();

        $menu1 = count($menu);

        for ($i = 0, $n = $menu1; $i < $n; $i++) {
          $CLICSHOPPING_Db->delete('administrator_menu_description', ['id' => (int)$menu[$i]['id']]);
        }

        $CLICSHOPPING_Db->delete('administrator_menu', ['app_code' => 'app_payment_authorizenet']);
      }
    }
  }
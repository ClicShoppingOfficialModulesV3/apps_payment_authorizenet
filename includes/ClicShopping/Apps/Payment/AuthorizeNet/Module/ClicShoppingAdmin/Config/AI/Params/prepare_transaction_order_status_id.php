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
  use ClicShopping\OM\Registry;

  class prepare_transaction_order_status_id extends \ClicShopping\Apps\Payment\AuthorizeNet\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0';
    public $sort_order = 13;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_authorizenet_prepare_transaction_order_status_id_title');
      $this->description = $this->app->getDef('cfg_authorizenet_prepare_transaction_order_status_id_desc');


      $this->default = $this->getParams();
    }

    public function getInputField()
    {
      $statuses_array = [
        [
          'id' => '0',
          'text' => $this->app->getDef('cfg_authorizenet_prepare_transaction_order_status_id_default')
        ]
      ];

      $Qstatuses = $this->app->db->get('orders_status', [
        'orders_status_id',
        'orders_status_name'
      ], [
        'language_id' => $this->app->lang->getId()
      ],
        'orders_status_name'
      );

      while ($Qstatuses->fetch()) {
        $statuses_array[] = [
          'id' => $Qstatuses->valueInt('orders_status_id'),
          'text' => $Qstatuses->value('orders_status_name')
        ];
      }

      $input = HTML::selectField($this->key, $statuses_array, $this->getInputValue());

      return $input;
    }


    public function getParams()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_ORDER_STATUS_ID')) {
        $QCheck = $this->app->db->prepare('select orders_status_id 
                                        from :table_orders_status
                                        where orders_status_name = :orders_status_name 
                                        limit 1
                                       ');
        $QCheck->bindValue(':orders_status_name', 'Authorize.net [Transactions]');
        $QCheck->execute();

        if ($QCheck->rowCount() < 1) {

          $Qstatus = $this->app->db->prepare('select max(orders_status_id) as status_id
                                            from :table_orders_status                                        
                                          ');
          $Qstatus->execute();

          $status_id = $Qstatus->valueInt('status_id') + 1;

          $languages = $CLICSHOPPING_Language->getLanguages();

          foreach ($languages as $lang) {
            $this->app->db->save('orders_status', [
                'orders_status_id' => $status_id,
                'language_id' => $lang['id'],
                'orders_status_name' => 'Authorize.net [Transactions]'
              ]
            );
          }

          $Qstatuses = $this->app->db->prepare('select orders_status_id 
                                              from :table_orders_status
                                              where orders_status_name = :orders_status_name 
                                              limit 1
                                             ');
          $Qstatuses->bindValue(':orders_status_name', 'Authorize.net [Transactions]');
          $Qstatuses->execute();

          $flags_query = $this->app->db->query('describe :table_orders_status public_flag');

          if ($flags_query->rowCount() == 1) {
            $Qupdate = $this->app->db->prepare('update :table_orders_status
                                              set public_flag = 0
                                              and downloads_flag = 0
                                              where orders_status_id = :orders_status_id 
                                              limit 1
                                             ');
            $Qupdate->bindInt(':orders_status_id', $status_id);
            $Qupdate->execute();
          }
        } else {
          $status_id = $QCheck->valueInt('orders_status_id');
        }
      } else {
        $status_id = MODULE_PAYMENT_AUTHORIZENET_CC_AIM_TRANSACTION_ORDER_STATUS_ID;
      }

      return $status_id;
    }
  }

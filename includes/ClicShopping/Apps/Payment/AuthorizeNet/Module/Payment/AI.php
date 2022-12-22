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

  namespace ClicShopping\Apps\Payment\AuthorizeNet\Module\Payment;

  use ClicShopping\Apps\Payment\AuthorizeNet\AuthorizeNet;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Payment\AuthorizeNet\AuthorizeNet as AuthorizeNetApp;

  use ClicShopping\Sites\Common\B2BCommon;

  use net\authorize\api\contract\v1 as AnetAPI;
  use net\authorize\api\controller as AnetController;

  class AI implements \ClicShopping\OM\Modules\PaymentInterface
  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public mixed $app;

    protected $lang;
    protected $signatureKey;
    protected $response;
    protected $merchantAuthentication;

    public function __construct()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (Registry::exists('Order')) {
        $CLICSHOPPING_Order = Registry::get('Order');
      }

      $this->lang = Registry::get('Language');

      if (!Registry::exists('AuthorizeNet')) {
        Registry::set('AuthorizeNet', new AuthorizeNetApp());
      }

      $this->app = Registry::get('AuthorizeNet');
      $this->app->loadDefinitions('Module/Shop/AI/AI');

      $this->signature = 'authorizenet|authorizenet_cc_aim|1.0|2.2';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'AI';
      $this->title = $this->app->getDef('module_ai_title');
      $this->public_title = $this->app->getDef('module_ai_public_title');

      if (defined('CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS')) {
        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
          if (B2BCommon::getPaymentUnallowed($this->code)) {
            if (CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS == 'True') {
              $this->enabled = true;
            } else {
              $this->enabled = false;
            }
          }
        } else {
          if (CLICSHOPPING_APP_AUTHORIZENET_AI_NO_AUTORIZE == 'True' && $CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
            if ($CLICSHOPPING_Customer->getCustomersGroupID() == 0) {
              if (CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS == 'True') {
                $this->enabled = true;
              } else {
                $this->enabled = false;
              }
            }
          }
        }

        if ((int)CLICSHOPPING_APP_AUTHORIZENET_AI_PREPARE_ORDER_STATUS_ID > 0) {
          $this->order_status = CLICSHOPPING_APP_AUTHORIZENET_AI_PREPARE_ORDER_STATUS_ID;
        }

        if ($this->enabled === true) {
          if ((empty(CLICSHOPPING_APP_AUTHORIZENET_AI_LOGIN_ID) || empty(CLICSHOPPING_APP_AUTHORIZENET_AI_TRANSACTION_KEY)) && defined('CLICSHOPPING_APP_AUTHORIZENET_AI_ERROR_ADMIN_CONFIGURATION')) {
            $this->description = '<div class="alert alert-warning">' . CLICSHOPPING_APP_AUTHORIZENET_AI_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

            $this->enabled = false;
          }
        }

        if ($this->enabled === true) {
          if (isset($CLICSHOPPING_Order) && is_object($CLICSHOPPING_Order)) {
            $this->update_status();
          }
        }

        $this->sort_order = defined('CLICSHOPPING_APP_AUTHORIZENET_AI_SORT_ORDER') ? CLICSHOPPING_APP_AUTHORIZENET_AI_SORT_ORDER : 0;


        if ($this->enabled === true) {
          if (defined('CLICSHOPPING_APP_AUTHORIZENET_AI_LOGIN_ID') & !empty(CLICSHOPPING_APP_AUTHORIZENET_AI_LOGIN_ID)) {
            $this->loginId = CLICSHOPPING_APP_AUTHORIZENET_AI_LOGIN_ID;
          } else {
            $this->enabled = false;
          }
        }

        if ($this->enabled === true) {
          $currency_code = isset($_SESSION['currency']) ?? DEFAULT_CURRENCY;

          if ($currency_code != 'CAD' || $currency_code != 'USD') {
            $this->enabled = false;
          }
        }

        if ($this->enabled === true) {
          if (defined('CLICSHOPPING_APP_AUTHORIZENET_AI_TRANSACTION_KEY') & !empty(CLICSHOPPING_APP_AUTHORIZENET_AI_TRANSACTION_KEY)) {
            $this->transactionKey = CLICSHOPPING_APP_AUTHORIZENET_AI_TRANSACTION_KEY;

            define("AUTHORIZENET_LOG_FILE", "phplog");
            $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $this->merchantAuthentication->setName($this->loginId);
            $this->merchantAuthentication->setTransactionKey($this->transactionKey);
          } else {
            $this->enabled = false;
          }
        }
      }
    }


    public function update_status()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      if (($this->enabled === true) && ((int)CLICSHOPPING_APP_AUTHORIZENET_AI_ZONE > 0)) {
        $check_flag = false;

        $Qcheck = $this->app->db->get('zones_to_geo_zones', 'zone_id', ['geo_zone_id' => CLICSHOPPING_APP_AUTHORIZENET_AI_ZONE,
          'zone_country_id' => $CLICSHOPPING_Order->delivery['country']['id']
        ],
          'zone_id'
        );

        while ($Qcheck->fetch()) {
          if (($Qcheck->valueInt('zone_id') < 1) || ($Qcheck->valueInt('zone_id') == $CLICSHOPPING_Order->delivery['zone_id'])) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag === false) {
          $this->enabled = false;
        }
      }
    }

    public function javascript_validation()
    {
      return false;
    }

    public function selection()
    {
      $CLICSHOPPING_Template = Registry::get('Template');

      if (CLICSHOPPING_APP_AUTHORIZENET_AI_LOGO) {
        $this->public_title = $this->title . '&nbsp;&nbsp;&nbsp;' . HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/' . CLICSHOPPING_APP_AUTHORIZENET_AI_LOGO);
      } else {
        $this->public_title = $this->title;
      }

      return ['id' => $this->app->vendor . '\\' . $this->app->code . '\\' . $this->code,
        'module' => $this->public_title
      ];
    }

    public function pre_confirmation_check()
    {
      return false;
    }

    public function confirmation()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      for ($i = 1; $i < 13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => sprintf('%02d', $i));
      }

      $today = getdate();


      for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
        $expires_year[] = array('id' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
      }

      $confirmation = ['fields' => [array('title' => $this->app->getDef('text_card_owner_firstname'),
        'field' => HTML::inputField('cc_owner_firstname', $CLICSHOPPING_Order->billing['firstname']), null, 'required aria-required="true"'),
        array('title' => $this->app->getDef('text_card_owner_lastname'),
          'field' => HTML::inputField('cc_owner_lastname', $CLICSHOPPING_Order->billing['lastname']), null, 'required aria-required="true"'),
        array('title' => $this->app->getDef('text_card_number'),
          'field' => HTML::inputField('cc_number_nh-dns'), null, 'required aria-required="true"'),
        array('title' => $this->app->getDef('text_card_expires'),
          'field' => HTML::selectMenu('cc_expires_month', $expires_month) . '&nbsp;' . HTML::selectMenu('cc_expires_year', $expires_year)),
        array('title' => $this->app->getDef('text_card_cvv'),
          'field' => HTML::inputField('cc_ccv_nh-dns', '', 'size="5" maxlength="4" required aria-required="true"'))
      ]
      ];

      return $confirmation;
    }

    public function process_button()
    {
      return false;
    }


    public function before_process()
    {
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

//      $first_name = HTML::sanitize($_POST['cc_owner_firstname']);
//      $lastname = HTML::sanitize($_POST['cc_owner_lastname']);
      $card_number = HTML::sanitize($_POST['cc_number_nh-dns']);
      $expire_month = HTML::sanitize($_POST['cc_expires_month']);
      $expire_year = HTML::sanitize($_POST['cc_expires_year']);
      $card_code = HTML::sanitize($_POST['cc_ccv_nh-dns']);
      $expiration_date = $expire_year . '-' . $expire_month;

      $total_amount = number_format($CLICSHOPPING_Order->info['total'] * $CLICSHOPPING_Currencies->getValue($CLICSHOPPING_Order->info['currency']), $CLICSHOPPING_Currencies->currencies[$CLICSHOPPING_Order->info['currency']]['decimal_places'], '.', '');

// Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber($card_number);
      $creditCard->setExpirationDate($expiration_date);
      $creditCard->setCardCode($card_code);

// Add the payment data to a paymentType object
      $paymentOne = new AnetAPI\PaymentType();
      $paymentOne->setCreditCard($creditCard);

// Set the customer's Bill To address
      $customerAddress = new AnetAPI\CustomerAddressType();
      $customerAddress->setFirstName($CLICSHOPPING_Order->billing['lastname']);
      $customerAddress->setLastName($CLICSHOPPING_Order->billing['lastname']);
      $customerAddress->setCompany($CLICSHOPPING_Order->billing['company']);
      $customerAddress->setAddress($CLICSHOPPING_Order->billing['lastname'] . ' ' . $CLICSHOPPING_Order->billing['suburb']);
      $customerAddress->setCity($CLICSHOPPING_Order->billing['city']);
      $customerAddress->setState($CLICSHOPPING_Order->billing['state']); //tx
      $customerAddress->setZip($CLICSHOPPING_Order->billing['postcode']);
      $customerAddress->setCountry($CLICSHOPPING_Order->billing['country']['iso_code_3']);

//Transaction request
      $transactionRequestType = new AnetAPI\TransactionRequestType();

      if (CLICSHOPPING_APP_AUTHORIZENET_AI_TRANSACTION_METHOD == 'Authorization') {
        $transactionRequestType->setTransactionType("authOnlyTransaction");
      } else {
        $transactionRequestType->setTransactionType("authCaptureTransaction");
      }

      $transactionRequestType->setAmount($total_amount);
      $transactionRequestType->setPayment($paymentOne);
      $transactionRequestType->setBillTo($customerAddress);

// Set the transaction's refId
      $refId = 'ref' . time();

// Assemble the complete transaction request
      $request = new AnetAPI\CreateTransactionRequest();
      $request->setMerchantAuthentication($this->merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest($transactionRequestType);

// Create the controller and get the response
      $controller = new AnetController\CreateTransactionController($request);

      if (CLICSHOPPING_APP_AUTHORIZENET_AI_SERVER == 'Test') {
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
      } else {
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
      }

      if (CLICSHOPPING_APP_AUTHORIZENET_DEBUG_EMAIL == 'True') {
        $this->sendDebugEmail($response);
      }

      if ($response != null) {
        if ($response->getMessages()->getResultCode() != 'Ok') {
          CLICSHOPPING::redirect(null, 'Checkout&Billing&payment_error=' . $this->code . '&error=' . $this->get_error(), true);
        }
      }
    }

    function after_process()
    {
      $CLICSHOPPING_Order = Registry::get('Order');

      $insert_id = $CLICSHOPPING_Order->getLastOrderId();

      $status = 'Transaction by AurthorizeNet';

      $sql_data_array = ['orders_id' => $insert_id,
        'orders_status_id' => CLICSHOPPING_APP_AUTHORIZENET_AI_PREPARE_TRANSACTION_ORDER_STATUS_ID,
        'date_added' => 'now()',
        'customer_notified' => '0',
        'comments' => $status
      ];

      $this->app->db->save('orders_status_history', $sql_data_array);
    }

    public function get_error()
    {
      $error = $this->app->getDef('error_transaction');

      return $error;
    }

    public function check()
    {
      return defined('CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS') && (trim(CLICSHOPPING_APP_AUTHORIZENET_AI_STATUS) != '');
    }

    public function install()
    {
      $this->app->redirect('Configure&Install&module=AI');
    }

    public function remove()
    {
      $this->app->redirect('Configure&Uninstall&module=AI');
    }

    public function keys()
    {
      return array('CLICSHOPPING_APP_AUTHORIZENET_AI_SORT_ORDER');
    }

    /**
     * @param $number
     * @param string $currency_code
     * @param string $currency_value
     * @return string
     */
    private function format_raw($number, $currency_code = '', $currency_value = '')
    {
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $_SESSION['currency'];
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $CLICSHOPPING_Currencies->currencies[$currency_code]['value'];
      }

      return number_format(round($number * $currency_value, $CLICSHOPPING_Currencies->currencies[$currency_code]['decimal_places']), $CLICSHOPPING_Currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    /**
     * Send information to the owner
     * @param $response
     */
    private function sendDebugEmail($response)
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');

      if (CLICSHOPPING_APP_AUTHORIZENET_DEBUG_EMAIL == 'True') {
        $email_body = '';

        if ($response != null) {
          // Check to see if the API request was successfully received and acted upon
          if ($response->getMessages()->getResultCode() == "Ok") {
            // Since the API request was successful, look for a transaction response
            // and parse it to display the results of authorizing the card
            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getMessages() != null) {
              $email_body .= " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
              $email_body .= " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
              $email_body .= " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
              $email_body .= " Auth Code: " . $tresponse->getAuthCode() . "\n";
              $email_body .= " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
            } else {
              echo "Transaction Failed \n";
              if ($tresponse->getErrors() != null) {
                $email_body .= " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                $email_body .= " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
              }
            }
            // Or, print errors if the API request wasn't successful
          } else {
            echo "Transaction Failed \n";
            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getErrors() != null) {
              $email_body .= " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
              $email_body .= " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
            } else {
              $email_body .= " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
              $email_body .= " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
            }
          }

        } else {
          $email_body .= 'No response returned';
        }

        $CLICSHOPPING_Mail->clicMail('', STORE_OWNER_EMAIL_ADDRESS, 'Authorize.net AIM Debug E-Mail', $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
    }
  }

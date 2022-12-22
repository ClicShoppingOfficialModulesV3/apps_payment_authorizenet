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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_AuthorizeNet = Registry::get('AuthorizeNet');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if ($CLICSHOPPING_MessageStack->exists('AuthorizeNet')) {
    echo $CLICSHOPPING_MessageStack->get('AuthorizeNet');
  }
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/modules_modules_checkout_payment.gif', $CLICSHOPPING_AuthorizeNet->getDef('authorizenet'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_AuthorizeNet->getDef('authorizenet') . ' v' . $CLICSHOPPING_AuthorizeNet->getVersion(); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-2">
            <?php echo HTML::button($CLICSHOPPING_AuthorizeNet->getDef('button_configure'), null, $CLICSHOPPING_AuthorizeNet->link('Configure'), 'warning'); ?>
          </span>
          <span class="col-md-10 text-end">
            <?php echo HTML::button($CLICSHOPPING_AuthorizeNet->getDef('button_sort_order'), null, CLICSHOPPING::link(null, 'A&Configuration\Modules&Modules&set=payment'), 'primary'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

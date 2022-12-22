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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;

  $CLICSHOPPING_AuthorizeNet = Registry::get('AuthorizeNet');

  require_once(__DIR__ . '/template_top.php');
?>
  <div class="contentBody">
    <div class="separator"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="col-md-12">
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('text_intro_cm'); ?>
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('return_url', ['return_authorizenet_url_cm' => HTTP::typeUrlDomain() . '/index.php?order&creditmutuel&checkout&cm']); ?>
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('text_intro_ob'); ?>
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('return_url_ob', ['return_authorizenet_url_ob' => HTTP::typeUrlDomain() . '/index.php?order&obc&checkout&ob']); ?>
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('text_intro_ho'); ?>
            <?php echo $CLICSHOPPING_AuthorizeNet->getDef('return_url_ho', ['return_authorizenet_url_ho' => HTTP::typeUrlDomain() . '/index.php?order&authorizenet&checkout&ho']); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  require_once(__DIR__ . '/template_bottom.php');

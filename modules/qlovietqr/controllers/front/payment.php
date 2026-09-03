<?php
class QloVietQrPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->nbProducts() == 0) {
            Tools::redirect('index.php?controller=order');
        }

        if ($cart->is_advance_payment) {
            $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
        } else {
            $total = $cart->getOrderTotal(true, Cart::BOTH);
        }

        $bankId = Configuration::get('VIETQR_BANK_ID');
        $accountNo = Configuration::get('VIETQR_ACCOUNT_NO');
        $accountName = Configuration::get('VIETQR_ACCOUNT_NAME');

        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'total' => $total,
            'total_formatted' => Tools::displayPrice($total, $this->context->currency, false),
            'bank_id' => $bankId,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'validation_url' => $this->context->link->getModuleLink('qlovietqr', 'validation', array(), true),
            'this_path' => $this->module->getPathUri(),
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}

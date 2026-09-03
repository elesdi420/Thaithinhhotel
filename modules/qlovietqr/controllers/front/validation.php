<?php
class QloVietQrValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        if ($cart->is_advance_payment) {
            $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
        } else {
            $total = $cart->getOrderTotal(true, Cart::BOTH);
        }

        $orderStateId = (int) Configuration::get('QLO_OS_VIETQR_PENDING');
        if (!$orderStateId) {
            $orderStateId = (int) Configuration::get('PS_OS_AWAITING_PAYMENT');
        }

        $mailVars = array(
            '{bank_id}' => Configuration::get('VIETQR_BANK_ID'),
            '{account_no}' => Configuration::get('VIETQR_ACCOUNT_NO'),
            '{account_name}' => Configuration::get('VIETQR_ACCOUNT_NAME')
        );

        $this->module->validateOrder(
            (int) $cart->id,
            $orderStateId,
            $total,
            $this->module->displayName,
            null,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=' . (int) $this->module->id . '&id_order=' . (int) $this->module->currentOrder . '&key=' . $customer->secure_key);
    }
}

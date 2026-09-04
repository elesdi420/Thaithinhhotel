<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class QloVietQr extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = array();

    public function __construct()
    {
        $this->name = 'qlovietqr';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'CMS Hotel';
        $this->controllers = array('payment', 'validation', 'webhook');
        $this->is_eu_compatible = 1;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Thanh toán Quét mã QR (VietQR)');
        $this->description = $this->l('Thanh toán chuyển khoản ngân hàng tự động qua mã QR động VietQR/Napas247.');
        $this->confirmUninstall = $this->l('Bạn có chắc muốn gỡ bỏ module VietQR?');
    }

    public function install()
    {
        if (!parent::install() 
            || !$this->registerHook('payment') 
            || !$this->registerHook('paymentReturn')
            || !$this->registerHook('displayPaymentEU')
        ) {
            return false;
        }

        // Set default configurations
        Configuration::updateValue('VIETQR_BANK_ID', 'MB');
        Configuration::updateValue('VIETQR_ACCOUNT_NO', '0901234567');
        Configuration::updateValue('VIETQR_ACCOUNT_NAME', 'CMS HOTEL PMS');
        Configuration::updateValue('VIETQR_TEMPLATE', 'compact2');
        // Deliberately left blank: a shipped default would be public in this repo and
        // therefore useless as a secret. Set VIETQR_WEBHOOK_SECRET in .env instead.
        Configuration::updateValue('VIETQR_WEBHOOK_SECRET', '');

        // Create custom Order States if not existing
        $this->initOrderStates();

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('VIETQR_BANK_ID');
        Configuration::deleteByName('VIETQR_ACCOUNT_NO');
        Configuration::deleteByName('VIETQR_ACCOUNT_NAME');
        Configuration::deleteByName('VIETQR_TEMPLATE');
        Configuration::deleteByName('VIETQR_WEBHOOK_SECRET');

        return parent::uninstall();
    }

    private function initOrderStates()
    {
        if (!Configuration::get('QLO_OS_VIETQR_PENDING')) {
            $orderState = new OrderState();
            $orderState->name = array();
            foreach (Language::getLanguages(false) as $language) {
                $orderState->name[$language['id_lang']] = 'Chờ thanh toán VietQR';
            }
            $orderState->send_email = false;
            $orderState->color = '#3498db';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;
            if ($orderState->add()) {
                Configuration::updateValue('QLO_OS_VIETQR_PENDING', (int) $orderState->id);
            }
        }
    }

    public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
            'vietqr_bank_id' => Configuration::get('VIETQR_BANK_ID'),
            'vietqr_account_no' => Configuration::get('VIETQR_ACCOUNT_NO'),
            'vietqr_account_name' => Configuration::get('VIETQR_ACCOUNT_NAME'),
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookDisplayPaymentEU($params)
    {
        if (!$this->active) {
            return;
        }

        return array(
            'cta_text' => $this->l('Thanh toán Quét mã QR (VietQR)'),
            'logo' => Media::getMediaPath(dirname(__FILE__) . '/views/img/vietqr.png'),
            'action' => $this->context->link->getModuleLink($this->name, 'payment', array(), true)
        );
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $objOrder = $params['objOrder'];
        $amount = (int) round($objOrder->total_paid);
        $orderRef = $objOrder->reference;
        $memo = 'HOTEL ' . $orderRef;

        $bankId = Configuration::get('VIETQR_BANK_ID');
        $accountNo = Configuration::get('VIETQR_ACCOUNT_NO');
        $accountName = Configuration::get('VIETQR_ACCOUNT_NAME');
        $template = Configuration::get('VIETQR_TEMPLATE');

        $qrUrl = self::getVietQrUrl($bankId, $accountNo, $template, $amount, $memo, $accountName);

        $this->smarty->assign(array(
            'status' => 'ok',
            'id_order' => $objOrder->id,
            'order_reference' => $orderRef,
            'total_to_pay' => Tools::displayPrice($objOrder->total_paid, $this->context->currency, false),
            'amount_raw' => $amount,
            'transfer_memo' => $memo,
            'bank_id' => $bankId,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'vietqr_image_url' => $qrUrl,
        ));

        return $this->display(__FILE__, 'payment_return.tpl');
    }

    /**
     * Resolve the webhook secret, environment first.
     *
     * The DB seed (docker/db/init.sql) is committed, so a secret stored only in
     * Configuration ends up published. The environment is read first so deployments
     * can hold the real value in .env, which is git-ignored.
     *
     * @return string Empty string when nothing is configured; callers must fail closed.
     */
    public static function getWebhookSecret()
    {
        $fromEnv = getenv('VIETQR_WEBHOOK_SECRET');
        if ($fromEnv !== false && trim($fromEnv) !== '') {
            return trim($fromEnv);
        }

        return trim((string) Configuration::get('VIETQR_WEBHOOK_SECRET'));
    }

    public static function getVietQrUrl($bankId, $accountNo, $template, $amount, $memo, $accountName)
    {
        $memoEncoded = urlencode(trim($memo));
        $nameEncoded = urlencode(trim($accountName));
        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$memoEncoded}&accountName={$nameEncoded}";
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('VIETQR_BANK_ID', Tools::getValue('VIETQR_BANK_ID'));
            Configuration::updateValue('VIETQR_ACCOUNT_NO', Tools::getValue('VIETQR_ACCOUNT_NO'));
            Configuration::updateValue('VIETQR_ACCOUNT_NAME', Tools::getValue('VIETQR_ACCOUNT_NAME'));
            Configuration::updateValue('VIETQR_TEMPLATE', Tools::getValue('VIETQR_TEMPLATE'));
            Configuration::updateValue('VIETQR_WEBHOOK_SECRET', Tools::getValue('VIETQR_WEBHOOK_SECRET'));
            $this->_html .= $this->displayConfirmation($this->l('Cập nhật cấu hình VietQR thành công!'));
        }

        return $this->_html . $this->renderForm();
    }

    public function renderForm()
    {
        // Show the secret actually in force, which may come from the environment
        // rather than from the value stored in this form.
        $webhookUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'index.php?fc=module&module=qlovietqr&controller=webhook&token=' . self::getWebhookSecret();

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Cấu hình VietQR & Webhook'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mã Ngân hàng (BIN/Code)'),
                        'name' => 'VIETQR_BANK_ID',
                        'desc' => $this->l('Ví dụ: MB, VCB, TCB, ACB, ICB'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Số tài khoản'),
                        'name' => 'VIETQR_ACCOUNT_NO',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tên chủ tài khoản'),
                        'name' => 'VIETQR_ACCOUNT_NAME',
                        'required' => true
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Mẫu mã QR'),
                        'name' => 'VIETQR_TEMPLATE',
                        'options' => array(
                            'query' => array(
                                array('id' => 'compact2', 'name' => 'Compact 2 (Gọn, đẹp)'),
                                array('id' => 'compact', 'name' => 'Compact'),
                                array('id' => 'qr_only', 'name' => 'Chỉ mã QR'),
                                array('id' => 'print', 'name' => 'Bản in đầy đủ'),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mã bí mật Webhook (Secret Token)'),
                        'name' => 'VIETQR_WEBHOOK_SECRET',
                        'desc' => $this->l('Dùng để xác thực webhook từ ngân hàng. Webhook URL: ') . '<code>' . $webhookUrl . '</code>',
                        'required' => false
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Lưu cấu hình'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'VIETQR_BANK_ID' => Configuration::get('VIETQR_BANK_ID'),
                'VIETQR_ACCOUNT_NO' => Configuration::get('VIETQR_ACCOUNT_NO'),
                'VIETQR_ACCOUNT_NAME' => Configuration::get('VIETQR_ACCOUNT_NAME'),
                'VIETQR_TEMPLATE' => Configuration::get('VIETQR_TEMPLATE'),
                // No fallback value: the form must not re-seed a known secret.
                'VIETQR_WEBHOOK_SECRET' => Configuration::get('VIETQR_WEBHOOK_SECRET'),
            ),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }
}

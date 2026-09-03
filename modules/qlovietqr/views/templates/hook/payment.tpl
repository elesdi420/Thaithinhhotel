<div class="row">
    <div class="col-xs-12">
        <p class="payment_module">
            <a class="qlovietqr" href="{$link->getModuleLink('qlovietqr', 'payment')|escape:'html':'UTF-8'}" title="{l s='Thanh toán Chuyển khoản QR (VietQR)' mod='qlovietqr'}" style="background: url('{$this_path}views/img/vietqr_icon.png') 15px 12px no-repeat #fbfbfb; background-size: 64px auto; padding-left: 95px; min-height: 70px; display: block; border: 1px solid #d6d4d4; border-radius: 4px; text-decoration: none;">
                <strong style="font-size: 16px; color: #333;">{l s='Thanh toán Quét mã QR (VietQR / Chuyển khoản 24/7)' mod='qlovietqr'}</strong>
                <br />
                <span style="color: #777; font-size: 13px;">{l s='Hỗ trợ tất cả app ngân hàng (MB, Vietcombank, Techcombank, Momo...) - Tự động xác nhận.' mod='qlovietqr'}</span>
            </a>
        </p>
    </div>
</div>

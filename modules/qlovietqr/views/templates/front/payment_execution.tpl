{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Quay lại phương thức thanh toán' mod='qlovietqr'}">{l s='Thanh toán' mod='qlovietqr'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Thanh toán VietQR' mod='qlovietqr'}
{/capture}

<h1 class="page-heading">
    {l s='Xác nhận đặt phòng qua VietQR' mod='qlovietqr'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="box cheque-box" style="padding: 25px; background: #fff; border: 1px solid #e1e1e1; border-radius: 6px;">
    <form action="{$validation_url}" method="post">
        <h3 class="page-subheading" style="border-bottom: 2px solid #0052cc; padding-bottom: 10px; color: #0052cc;">
            <i class="icon-qrcode"></i> {l s='Thanh toán Chuyển khoản Quét mã QR' mod='qlovietqr'}
        </h3>
        <p style="margin-top: 15px; font-size: 15px;">
            {l s='Bạn đã chọn thanh toán chuyển khoản qua mã QR VietQR.' mod='qlovietqr'}
        </p>
        <p style="font-size: 15px;">
            - {l s='Tổng số tiền cần thanh toán:' mod='qlovietqr'}
            <span id="amount" class="price" style="font-size: 20px; font-weight: bold; color: #e74c3c;">{$total_formatted}</span>
        </p>
        <p style="font-size: 14px; color: #555;">
            - {l s='Ngân hàng thụ hưởng:' mod='qlovietqr'} <strong>{$bank_id} ({$account_name})</strong>
            <br />
            - {l s='Số tài khoản:' mod='qlovietqr'} <strong>{$account_no}</strong>
        </p>
        <p style="margin-top: 15px; padding: 12px; background: #f0f7ff; border-left: 4px solid #0052cc; color: #333; font-size: 14px;">
            {l s='Sau khi bấm "Xác nhận đặt phòng", hệ thống sẽ hiển thị mã QR động chứa đúng số tiền và nội dung chuyển khoản để bạn quét mã thanh toán ngay trên ứng dụng ngân hàng.' mod='qlovietqr'}
        </p>
        <p class="cart_navigation clearfix" style="margin-top: 25px;">
            <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" style="padding: 10px 20px;">
                <i class="icon-chevron-left"></i> {l s='Chọn phương thức khác' mod='qlovietqr'}
            </a>
            <button class="button btn btn-primary pull-right" type="submit" style="padding: 10px 25px; font-size: 16px; background-color: #0052cc; border-color: #0052cc;">
                <span>{l s='Xác nhận đặt phòng & Lấy mã QR' mod='qlovietqr'} <i class="icon-chevron-right right"></i></span>
            </button>
        </p>
    </form>
</div>

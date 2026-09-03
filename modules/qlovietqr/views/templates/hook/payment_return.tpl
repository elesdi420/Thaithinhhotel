<div class="box" style="padding: 25px; background: #fff; border: 1px solid #e1e1e1; border-radius: 8px; margin-top: 20px;">
    <div class="alert alert-success" style="font-size: 16px; margin-bottom: 25px;">
        <i class="icon-check-circle"></i> <strong>{l s='Đơn đặt phòng đã được tạo thành công!' mod='qlovietqr'}</strong>
        {l s='Mã đơn hàng:' mod='qlovietqr'} <strong>#{$order_reference}</strong>
    </div>

    <div class="row">
        <!-- Cột hiển thị mã QR động -->
        <div class="col-xs-12 col-md-5 text-center" style="margin-bottom: 20px;">
            <div style="padding: 15px; border: 2px dashed #0052cc; border-radius: 12px; background: #fafbfc; display: inline-block; max-width: 100%;">
                <img src="{$vietqr_image_url}" alt="Mã QR Thanh toán VietQR" class="img-responsive" style="max-width: 320px; border-radius: 8px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
                <p style="margin-top: 12px; font-size: 13px; color: #555;">
                    <i class="icon-camera"></i> {l s='Mở App ngân hàng quét mã để thanh toán tự động' mod='qlovietqr'}
                </p>
            </div>
        </div>

        <!-- Cột thông tin chuyển khoản thủ công -->
        <div class="col-xs-12 col-md-7">
            <h4 style="color: #0052cc; font-weight: bold; margin-top: 0;">
                <i class="icon-info-circle"></i> {l s='Thông tin Chuyển khoản Ngân hàng' mod='qlovietqr'}
            </h4>
            <p style="color: #666; font-size: 14px;">
                {l s='Nếu không thể quét mã QR, quý khách vui lòng chuyển khoản theo thông tin dưới đây:' mod='qlovietqr'}
            </p>

            <table class="table table-bordered" style="margin-top: 15px; font-size: 14px;">
                <tbody>
                    <tr>
                        <td style="width: 40%; background: #f9f9f9;"><strong>{l s='Ngân hàng' mod='qlovietqr'}</strong></td>
                        <td><span class="label label-info" style="font-size: 13px;">{$bank_id}</span></td>
                    </tr>
                    <tr>
                        <td style="background: #f9f9f9;"><strong>{l s='Số tài khoản' mod='qlovietqr'}</strong></td>
                        <td><strong style="color: #0052cc; font-size: 16px;">{$account_no}</strong></td>
                    </tr>
                    <tr>
                        <td style="background: #f9f9f9;"><strong>{l s='Chủ tài khoản' mod='qlovietqr'}</strong></td>
                        <td><strong>{$account_name}</strong></td>
                    </tr>
                    <tr>
                        <td style="background: #f9f9f9;"><strong>{l s='Số tiền' mod='qlovietqr'}</strong></td>
                        <td><strong class="price" style="color: #e74c3c; font-size: 18px;">{$total_to_pay}</strong></td>
                    </tr>
                    <tr>
                        <td style="background: #f9f9f9;"><strong>{l s='Nội dung chuyển khoản' mod='qlovietqr'}</strong></td>
                        <td>
                            <code style="font-size: 16px; font-weight: bold; color: #c0392b; background: #fdf2e9; padding: 4px 8px; border-radius: 4px;">{$transfer_memo}</code>
                            <br /><small style="color: #e67e22;">*{l s='Quý khách vui lòng nhập chính xác nội dung để hệ thống tự động xác nhận' mod='qlovietqr'}</small>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="alert alert-info" style="font-size: 13px; margin-top: 15px;">
                <i class="icon-bell"></i> {l s='Hệ thống sẽ gửi email xác nhận đặt phòng ngay khi giao dịch chuyển khoản hoàn tất.' mod='qlovietqr'}
            </div>
        </div>
    </div>
</div>

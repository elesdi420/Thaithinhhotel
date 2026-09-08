<div class="row">
    {* KHỐI 1: XUẤT LỊCH SANG CÁC KÊNH OTA *}
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-calendar-check-o"></i> {l s='1. Chiều Xuất Lịch Sang OTA (Export iCal Feed)'}
            </div>
            <div class="alert alert-info" style="margin-bottom: 15px;">
                <i class="icon-info-circle"></i>
                {l s='Sao chép các đường dẫn iCal bên dưới và dán vào phần "Import Calendar" trên Airbnb, Agoda hoặc Booking.com. Mỗi khi có khách đặt phòng trên website này, các kênh OTA sẽ tự động cập nhật và khóa ngày tương ứng để tránh trùng phòng (Overbooking).'}
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="center" style="width: 60px;">{l s='ID'}</th>
                            <th>{l s='Tên Chi Nhánh'}</th>
                            <th>{l s='Khu Vực'}</th>
                            <th>{l s='Đường Dẫn Lịch iCal (.ics)'}</th>
                            <th class="center" style="width: 140px;">{l s='Thao Tác'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if isset($ota_export_feeds) && $ota_export_feeds}
                            {foreach from=$ota_export_feeds item=feed}
                                <tr>
                                    <td class="center">{$feed.id_hotel}</td>
                                    <td><strong>{$feed.hotel_name|escape:'html':'UTF-8'}</strong></td>
                                    <td>{$feed.city|escape:'html':'UTF-8'}</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{$feed.export_url|escape:'html':'UTF-8'}" id="export_url_{$feed.id_hotel}" readonly />
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" onclick="copyToClipboard('export_url_{$feed.id_hotel}')">
                                                    <i class="icon-copy"></i> {l s='Sao chép'}
                                                </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a href="{$feed.export_url|escape:'html':'UTF-8'}" target="_blank" class="btn btn-default btn-sm">
                                            <i class="icon-download"></i> {l s='Tải xem .ics'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="5" class="text-center text-muted">{l s='Chưa có chi nhánh nào được phân quyền.'}</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {* KHỐI 2: NHẬP LỊCH TỪ OTA VỀ HỆ THỐNG *}
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-exchange"></i> {l s='2. Chiều Nhập Lịch & Khóa Phòng Từ OTA (Import & Sync)'}
                <div class="panel-heading-action">
                    <form method="post" action="{$current_index}&token={$current_token}" style="display: inline-block;">
                        <button type="submit" name="submitSyncAllOta" class="btn btn-primary btn-sm">
                            <i class="icon-refresh"></i> {l s='Đồng bộ toàn bộ kênh ngay'}
                        </button>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning" style="margin-bottom: 20px;">
                <i class="icon-shield"></i>
                {l s='Dán đường dẫn iCal (.ics) do Airbnb, Agoda hoặc Booking.com cung cấp vào form bên dưới. Hệ thống sẽ tự động kéo danh sách ngày đã đặt và khóa trên lưới lịch của khách sạn để chống overbooking hoàn toàn.'}
            </div>

            {* Form thêm mới feed *}
            <form method="post" action="{$current_index}&token={$current_token}" class="form-horizontal well">
                <h4><i class="icon-plus-circle"></i> {l s='Kết Nối Thêm Kênh OTA Mới'}</h4>
                <div class="form-group">
                    <label class="control-label col-lg-2 required">{l s='Chi nhánh áp dụng'}</label>
                    <div class="col-lg-3">
                        <select name="id_hotel" class="form-control" required>
                            {foreach from=$ota_hotels item=h}
                                <option value="{$h.id}">{$h.hotel_name|escape:'html':'UTF-8'} ({$h.city|escape:'html':'UTF-8'})</option>
                            {/foreach}
                        </select>
                    </div>

                    <label class="control-label col-lg-2 required">{l s='Nền tảng OTA'}</label>
                    <div class="col-lg-2">
                        <select name="channel_name" class="form-control" required>
                            <option value="Airbnb">Airbnb</option>
                            <option value="Agoda">Agoda</option>
                            <option value="Booking.com">Booking.com</option>
                            <option value="Traveloka">Traveloka</option>
                            <option value="Khác">{l s='Khác / Google Calendar'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-2 required">{l s='Đường dẫn iCal (.ics)'}</label>
                    <div class="col-lg-7">
                        <input type="url" name="feed_url" class="form-control" placeholder="https://www.airbnb.com/calendar/ical/...ics" required />
                        <p class="help-block">{l s='Lấy trong phần Listing > Pricing and availability > Calendar sync > Export Calendar trên Airbnb/Agoda'}</p>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" name="submitAddOtaFeed" class="btn btn-success">
                            <i class="icon-plus"></i> {l s='Lưu & Đồng bộ ngay'}
                        </button>
                    </div>
                </div>
            </form>

            {* Bảng danh sách các kết nối hiện tại *}
            <h4 style="margin-top: 25px;"><i class="icon-list"></i> {l s='Danh Sách Các Kênh Đang Kết Nối'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="center" style="width: 50px;">{l s='ID'}</th>
                            <th style="width: 130px;">{l s='Kênh OTA'}</th>
                            <th>{l s='Chi Nhánh'}</th>
                            <th>{l s='Đường Dẫn Feed'}</th>
                            <th style="width: 150px;">{l s='Đồng Bộ Gần Nhất'}</th>
                            <th class="center" style="width: 120px;">{l s='Khoảng Ngày Đã Khóa'}</th>
                            <th>{l s='Trạng Thái'}</th>
                            <th class="center" style="width: 160px;">{l s='Thao Tác'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if isset($ota_imported_feeds) && $ota_imported_feeds}
                            {foreach from=$ota_imported_feeds item=f}
                                <tr>
                                    <td class="center">{$f.id_feed}</td>
                                    <td>
                                        <span class="label {if $f.channel_name == 'Airbnb'}label-danger{elseif $f.channel_name == 'Booking.com'}label-info{elseif $f.channel_name == 'Agoda'}label-success{else}label-default{/if}">
                                            {$f.channel_name|escape:'html':'UTF-8'}
                                        </span>
                                    </td>
                                    <td><strong>{$f.hotel_name|escape:'html':'UTF-8'}</strong></td>
                                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <a href="{$f.feed_url|escape:'html':'UTF-8'}" target="_blank">{$f.feed_url|escape:'html':'UTF-8'}</a>
                                    </td>
                                    <td>{if $f.last_sync_at}{$f.last_sync_at}{else}<span class="text-muted">{l s='Chưa chạy'}</span>{/if}</td>
                                    <td class="center"><span class="badge badge-success">{$f.last_sync_count} {l s='đợt'}</span></td>
                                    <td>
                                        <small>{$f.last_sync_status|escape:'html':'UTF-8'}</small>
                                    </td>
                                    <td class="center">
                                        <a href="{$current_index}&token={$current_token}&sync_feed={$f.id_feed}" class="btn btn-default btn-xs" title="{l s='Đồng bộ lại'}">
                                            <i class="icon-refresh"></i> {l s='Đồng bộ'}
                                        </a>
                                        <a href="{$current_index}&token={$current_token}&delete_feed={$f.id_feed}" class="btn btn-danger btn-xs" onclick="return confirm('{l s='Bạn có chắc muốn xóa kênh kết nối này và mở lại các ngày phòng đã khóa?'}')" title="{l s='Xóa'}">
                                            <i class="icon-trash"></i> {l s='Xóa'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding: 20px;">
                                    {l s='Chưa có kênh OTA nào được kết nối. Hãy thêm link iCal từ Airbnb/Agoda ở phía trên.'}
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    showSuccessMessage("{l s='Đã sao chép đường dẫn iCal vào bộ nhớ tạm!'}");
}
</script>

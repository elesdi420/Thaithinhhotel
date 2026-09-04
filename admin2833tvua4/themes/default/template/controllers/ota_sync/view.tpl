{*
 * MOCKUP - GIAO DIỆN TĨNH, CHƯA ĐẤU NỐI OTA
 * Form này không gửi đi đâu, không lưu gì. Xem AdminOtaSyncController.php.
 *}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-exchange"></i> Đồng bộ OTA (Agoda/Airbnb)
	</div>

	<div class="alert alert-info" style="margin-bottom:20px">
		<p style="margin:0">
			<strong>Tính năng sẵn sàng đấu nối.</strong>
			Yêu cầu nhập API Key của khách sạn để kích hoạt đồng bộ 2 chiều.
		</p>
	</div>

	<div class="alert alert-warning" style="margin-bottom:20px">
		<p style="margin:0">
			<i class="icon-info-circle"></i>
			<strong>Bản demo giao diện.</strong>
			Trang này chưa kết nối tới OTA nào. Ô nhập bên dưới chưa được lưu và nút bấm chưa gửi dữ liệu đi.
			Việc đấu nối thật nằm ở giai đoạn v2 theo PRD.
		</p>
	</div>

	<form class="form-horizontal" onsubmit="return false;">
		{foreach from=$ota_channels item=channel}
			<div class="form-group">
				<label class="control-label col-lg-3">
					API Key {$channel.label|escape:'html':'UTF-8'}
				</label>
				<div class="col-lg-6">
					<input type="text" class="form-control"
						name="ota_api_key_{$channel.code|escape:'html':'UTF-8'}"
						placeholder="Dán API Key {$channel.label|escape:'html':'UTF-8'} vào đây"
						autocomplete="off"/>
				</div>
				<div class="col-lg-3">
					<span class="badge badge-warning" style="margin-top:8px">Chưa kết nối</span>
				</div>
			</div>
		{/foreach}

		<div class="form-group">
			<label class="control-label col-lg-3">Cơ sở áp dụng</label>
			<div class="col-lg-6">
				<select class="form-control" name="ota_hotel" disabled="disabled">
					<option>Tất cả cơ sở</option>
					{foreach from=$ota_hotels item=hotelName}
						<option>{$hotelName|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
				<p class="help-block" style="margin-top:6px">
					Sẽ mở khoá sau khi có API Key hợp lệ.
				</p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3">Chiều đồng bộ</label>
			<div class="col-lg-6">
				<div class="checkbox">
					<label><input type="checkbox" disabled="disabled" checked="checked"/> Đẩy tồn phòng và giá lên OTA</label>
				</div>
				<div class="checkbox">
					<label><input type="checkbox" disabled="disabled" checked="checked"/> Nhận đơn đặt phòng từ OTA về hệ thống</label>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" disabled="disabled">
				<i class="process-icon-save"></i> Lưu và kiểm tra kết nối
			</button>
		</div>
	</form>
</div>

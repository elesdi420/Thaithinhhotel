<?php
/**
 * MOCKUP - GIAO DIỆN TĨNH, CHƯA ĐẤU NỐI OTA
 *
 * Vỏ giao diện cho FR-9 (Channel Manager / đồng bộ OTA), phục vụ demo cho khách.
 * Trang này KHÔNG lưu gì, KHÔNG gọi API, KHÔNG đồng bộ phòng hay giá với Agoda,
 * Airbnb hay bất kỳ kênh nào. Ô nhập API Key chỉ là ô trống để xem giao diện -
 * bấm nút không gửi đi đâu cả.
 *
 * PRD đã hoãn FR-9 sang v2 (xem _bmad-output/planning-artifacts/prds). Khi làm
 * thật, thay toàn bộ file này; nhớ gỡ luôn dòng cảnh báo mockup bên dưới.
 */
class AdminOtaSyncControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();
    }

    /**
     * Không có bản ghi để thêm hay xoá, nên bỏ hết nút trên thanh công cụ.
     */
    public function initToolbar()
    {
        $this->toolbar_btn = array();
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Đồng bộ OTA (Agoda/Airbnb)');
        $this->page_header_toolbar_btn = array();

        parent::initPageHeaderToolbar();
    }

    /**
     * @return string HTML tĩnh của trang mockup
     */
    public function renderView()
    {
        $channels = array(
            array('code' => 'agoda',     'label' => 'Agoda'),
            array('code' => 'airbnb',    'label' => 'Airbnb'),
            array('code' => 'booking',   'label' => 'Booking.com'),
            array('code' => 'traveloka', 'label' => 'Traveloka'),
        );

        // Tên cơ sở nằm ở category_lang chứ không nằm trong htl_branch_info,
        // nên đọc thẳng bằng một truy vấn đọc. Danh sách chỉ để xem cho giống thật,
        // ô chọn vẫn bị khoá.
        $hotels = array();
        $rows = Db::getInstance()->executeS(
            'SELECT cl.`name`
             FROM `'._DB_PREFIX_.'htl_branch_info` hbi
             INNER JOIN `'._DB_PREFIX_.'category_lang` cl
                ON cl.`id_category` = hbi.`id_category`
                AND cl.`id_lang` = '.(int) $this->context->language->id.'
             WHERE hbi.`active` = 1
             ORDER BY hbi.`id`'
        );
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $hotels[] = $row['name'];
            }
        }

        $this->context->smarty->assign(array(
            'ota_channels' => $channels,
            'ota_hotels' => $hotels,
        ));

        return $this->context->smarty->fetch(
            _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.'default'
            .DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'controllers'
            .DIRECTORY_SEPARATOR.'ota_sync'.DIRECTORY_SEPARATOR.'view.tpl'
        );
    }
}

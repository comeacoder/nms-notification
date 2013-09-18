<?php
class Tinmoi extends CI_Controller {
    function __construct() {
        parent::__construct();
    }

    function about() {
        $about = array(
            'text' => 'Tinmoi.vn cung cấp thông tin toàn diện về chính trị xã hội, đời sống, kinh tế, thể thao, giải trí cùng các tiện ích tra cứu, tư vấn pháp luật, hỏi đáp. Với lượng dữ liệu khổng lồ, thông tin được cập nhật liên tục 24h/7, hiện tinmoi.vn thuộc nhóm 10 website hàng đầu Việt Nam chuyên cung cấp thông tin trong nước.',
            'image' => site_url('/assets/images/gioithieu_tinmoi.png')
        );
        echo json_encode($about);
    }

    function android_cf() {
        $config = array(
            'service_host'      => 'http://api.tinmoi.vn/index.php/',
            'push_host'         => 'http://services.meme.vn/apps/',
            'version'           => '1.0',
            'update_required'   => 'no',
            'turn_ads'          => 'yes',
            'update_intro_app'              =>  '20130910',
            'update_category_tinmoi'        =>  '0',
            'update_category_thethao247'    =>  '0',
            'update_ads'                    =>  '0'
        );
        echo json_encode($config);
    }

    function iphone_cf() {
        $config = array(
            'service_host'      => 'http://api.tinmoi.vn/index.php/',
            'push_host'         => 'http://services.meme.vn/apps/',
            'version'           => '1.0',
            'update_required'   => 'no',
            'turn_ads'          => 'yes',
        );
        echo json_encode($config);
    }

}
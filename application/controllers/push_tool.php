<?php
class Push_tool extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $theUrl = $this->input->get('theUrl');
        if($theUrl) {
            exit($theUrl);
            $output = file_get_contents($theUrl);
        } else {
            $this->load->view('push/index');
        }
    }

}
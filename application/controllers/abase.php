<?php
class Abase extends CI_Controller {

    protected $_result;

    function __construct() {
        parent::__construct();
    }

    protected function _response_success() {
        $this->_result['status'] = 0;
        if(!isset($this->_result['message'])) {
            $this->_result['message'] = 'success';
        }
        header('Content-Type: application/json');
        echo json_encode($this->_result);
        exit();
    }

    protected function _response_error($message) {
        $this->_result['status'] = 1;
        $this->_result['message'] = $message;
        header('Content-Type: application/json');
        echo json_encode($this->_result);
        exit();
    }

    public function pushAndroidMessage($registrationId = null, $collapseKey = null, $type = '', $id = null, $data = null, $apikey)
    {

        $messageUrl = "https://android.googleapis.com/gcm/send";

        $_data = array("notify_type" => $type, "notify_id" => $id, "notify_data" => $data); //The content of the message

        $json['data'] = $_data;
        $json['registration_ids'] = array($registrationId);
        $json['collapse_key'] = $collapseKey;
        $json['delay_while_idle'] = false;
        $json['time_to_live'] = 7 * 2 * 24 * 60 * 60; // 2 week

        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $apikey
        );


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $messageUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($json));
        $response = curl_exec($curl);
        //$info = curl_getinfo($curl);
        curl_close($curl);

        //var_dump($response); var_dump($info); var_dump(json_encode($json)); die();

        if (empty ($response)) return null;

        $result = json_decode($response);
        if ($result->success) return json_encode($json);
        else return null;

    }

    function genKey() { return md5(time()); }

}
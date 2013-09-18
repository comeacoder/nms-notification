<?php
require_once('abase.php');
class Notify_tinmoi extends Abase
{
    private $prefix = "tinmoi_";

    function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        set_time_limit(999999);
    }

    function add_device()
    {
        # Validate device type
        $type = $this->uri->segment(3);
        if (!in_array($type, array('ios', 'iphone', 'android'))) {
            $this->_response_error("Invalid request: Device type is not defined");
        }

        if ($type == 'android') {
            $device_id = $this->input->get('device_id');
            $token = $this->input->get('token');

            # Validate device info
            if ($device_id == '' || $token == '') {
                $this->_response_error("Missing device info");
            }

            # Case add device
            $rs = $this->db->where(array('device_id' => $device_id, 'device_type' => 'android'))->get($this->prefix . 'devices');
            if ($rs->num_rows() > 0) {
                $this->db->where(array('device_id' => $device_id, 'device_type' => 'android'))->set('token', $token)->update($this->prefix . 'devices');
                $this->_result['message'] = 'Update device success';
                $this->_result['device_info'] = array(
                    'device_id' => $device_id,
                    'token' => $token
                );
                $this->_response_success();
            } else {
                $this->db->insert($this->prefix . 'devices', array(
                    'device_id' => $device_id,
                    'token' => $token,
                    'device_type' => 'android',
                ));
                $this->_result['message'] = 'Add device success';
                $this->_result['device_info'] = array(
                    'device_id' => $device_id,
                    'token' => $token,
                    'device_type' => 'android'
                );
                $this->_response_success();
            }
        } else {
            # IPHONE CASE
            $device_id = $this->input->get('device_id');
            $token = $this->input->get('token');
            $old_token = $this->input->get('old_token');

            # Validate device info
            if (empty($device_id) || empty($token)) {
                $this->_response_error("Missing device info");
            }

            # Update device
            $now = date('Y-m-d H:i:s');
            if (!empty($old_token)) {
                $this->db->where(array('token' => $old_token, 'device_type' => 'iphone'))->set(array(
                    'token' => $token,
                    'device_id' => $device_id,
                    'last_activate' => $now
                ))->update($this->prefix . 'devices');
                $this->_result['message'] = 'Update device success';
                $this->_result['device_info'] = array(
                    'device_id' => $device_id,
                    'token' => $token,
                    'last_activate' => $now
                );
                $this->_response_success();
            }


            # Add device
            $rs = $this->db->where(array('token' => $token))->get($this->prefix . 'devices');
            if ($rs->num_rows() > 0) {
                $this->_response_error('Warning: This device has been added');
            } else {
                $this->db->insert($this->prefix . 'devices', array(
                    'device_id' => $device_id,
                    'token' => $token,
                    'device_type' => 'iphone',
                ));
                $this->_result['message'] = 'Add device success';
                $this->_result['device_info'] = array(
                    'device_id' => $device_id,
                    'token' => $token,
                    'device_type' => 'iphone'
                );
                $this->_response_success();
            }
        }
    }

    function remove_device()
    {
        # Validate device type
        $type = $this->uri->segment(3);
        if (!in_array($type, array('ios', 'iphone', 'android'))) {
            $this->_response_error("Invalid request: Device type is not defined");
        }

        $device_id = $this->input->get('device_id');
        $token = $this->input->get('token');

        # Validate device info
        if (($type == 'android' && empty($device_id)) || ($type == 'iphone' && empty($token))) {
            $this->_response_error("Missing device info");
        }

        if ($type == 'android') {
            $device = $this->db->where('device_id', $device_id)->get($this->prefix . 'devices');
        } else if ($type == 'iphone') {
            $device = $this->db->where('token', $token)->get($this->prefix . 'devices');
        }

        if ($device->num_rows() <= 0) {
            $this->_response_error("Device \"$device_id\" is not exist");
        }

        if ($type == 'android') {
            $this->db->where(array('device_id' => $device_id, 'device_type' => $type))->delete($this->prefix . 'devices');
        } else if ($type == 'iphone') {
            $this->db->where(array('token' => $token, 'device_type' => $type))->delete($this->prefix . 'devices');
        }
        $this->_result['message'] = "Remove device \"$device_id\" success";
        $this->_response_success();
    }

    function list_devices()
    {
        # Validate device type
        $type = $this->uri->segment(3);
        if (!in_array($type, array('ios', 'iphone', 'android'))) {
            $this->_response_error("Invalid request: Device type is not defined");
        }

        $devices = $this->db->where('device_type', $type)->get($this->prefix . 'devices');
        if ($devices->num_rows() > 0) {
            $devices = $devices->result();
            $header = "";
            $rows = "";
            foreach ($devices[0] as $k => $v) {
                $header .= "<th align='left'>$k</th>";
            }
            foreach ($devices as $d) {
                $row = "<tr>";
                $d = (array)$d;
                foreach ($d as $k => $v) {
                    $row .= "<td>$v</td>";
                }
                $row .= "</tr>";
                $rows .= $row;
            }
            $table = "<table border='1px' style='width: 100%'>
                    <tr>$header</tr>
                        $rows
                    </table>";
            echo $table;
        }
    }

    /*Push data for iphone devices*/
    function push_iphone()
    {
        $msg = $this->input->get('message');
        $type = $this->input->get('type');
        $nid = $this->input->get('nid');
        $cid = $this->input->get('cid');
        $branch = $this->input->get('branch');
        $scope = $this->input->get('scope');

        if(empty($scope)) {
            $scope = 'all';
        }

        if (empty($type)) {
            $type = 'message';
        }

        # Validate message info
        if (empty($msg)) {
            $this->_response_error("Missing message");
        }

        if($scope == 'dev') {
            $this->db->where('isdev', 'yes');
        }

        if($scope == 'one') {
            $focus_token = $this->input->get('token');
            $this->db->where('token', $focus_token);
        }
        $this->db->where('device_type', 'iphone');

        $devices = $this->db->get($this->prefix . 'devices');

        require_once(APPPATH . '/libraries/ApnsPHP/Autoload.php');

        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            './assets/setificates/tinmoi_server_certificates_bundle_production.pem'
        );

        if ($devices->num_rows() > 0) {
            $devices = $devices->result();
            $push->connect();
            foreach ($devices as $device) {
                try {
                    $message = new ApnsPHP_Message($device->token);
                    $message->setCustomIdentifier("Notification from Tin moi");
                    $message->setBadge(3);
                    $message->setText($msg);
                    $message->setSound();
                    $mdata = array();
                    if (empty($nid)) {
                        $nid = '0';
                    }

//                    if (empty($cid)) {
//                        $cid = '0';
//                    }

                    if ($type == 'news') {
                        $mdata['nid'] = $nid;
//                        $mdata['cid'] = $cid;
                    }

                    if ($type == 'message') {
                        $mdata['nid'] = '0';
//                        $mdata['cid'] = '0';
                    }

                    if (!empty($branch)) {
                        $mdata['branch'] = $branch;
                    }

                    if ($type == 'update') {
                        $mdata['link_update'] = "https://itunes.apple.com/us/app/tin-moi-tin-tuc-doc-bao-moi/id689919492?l=vi&ls=1&mt=8";
                    }

                    $message->setCustomProperty('data', $mdata);
                    $message->setExpiry(30);
                    $push->add($message);
                    $push->send();
                    $aErrorQueue = $push->getErrors();
                } catch (ApnsPHP_Exception $ex) {
                    $ex->getMessage();
                }
            }
            $push->disconnect();
        }
    }

    function push_android()
    {
        $nid = $this->input->get('nid');
//        $cid = $this->input->get('cid');
        $msg = $this->input->get('message');
        $type = $this->input->get('type');
        $branch = $this->input->get('branch');

        if(!in_array($type, array('message', 'news', 'update'))) {
            $type = 'message';
        } else {
        }

        $devices = $this->db->where('device_type', 'android')->get($this->prefix . 'devices');

        if ($devices->num_rows() > 0) {
            $data = array(
                'status' => '0',
                'data' => array('nid' => $nid, 'title' => $msg),
                'message' => 'message',
                'total' => 1
            );

            if ($type == 'news') {
                $data['data']['nid'] = $nid;
//                $data['data']['cid'] = $cid;
            }

            if ($type == 'message') {
                $data['data']['nid'] = 'none';
//                $data['data']['cid'] = 'none';
            }

            $data['data']['push_type'] = $type;

            if (!empty($branch)) {
                $data['data']['branch'] = $branch;
            } else {
                $data['data']['branch'] = 'tinmoi';
            }

            foreach ($devices->result() as $device) {
                $this->pushAndroidMessage($device->token, json_encode(time()), 'alert', 1, $data, GOOGLE_KEY_TINMOI);
            }
        }
    }

    function test()
    {
        require_once(APPPATH . '/libraries/ApnsPHP/Autoload.php');

        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            './assets/setificates/tinmoi_server_certificates_bundle_production.pem'
        );
        $push->connect();
        try {
            $message = new ApnsPHP_Message('12ec7253c5c61044f5df003e2d3da5809a4d7d47ba4686511d9f6e584fa820bf');
            $message->setCustomIdentifier("Notification from Tin moi");
            $message->setBadge(3);
            $message->setText('test message');
            $message->setSound();
            $mdata = array();
            $mdata['nid'] = 'none';
            $mdata['cid'] = 'none';

            $message->setCustomProperty('data', $mdata);
            $message->setExpiry(30);
            $push->add($message);
            $push->send();
            $aErrorQueue = $push->getErrors();
        } catch (ApnsPHP_Exception $ex) {
            $ex->getMessage();
        }
        $push->disconnect();
    }

    function test2() {
        $data = array(
            'status' => '0',
            'data' => array('nid' => 'xxxxxxxxx', 'title' => 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyy'),
            'message' => 'message',
            'total' => 1
        );
//
//        $result = $this->pushAndroidMessage('APA91bGcyxwbFZidIn9WJL4MWb-VoHdiBv8wyZHDan9ZAdug84mJ_ssldcBbrtglPrdAqFNNEhnVhdizFVtvm_ErW35xIuw5AKVNa2NcYfKd4m3udCbsWh_Nned_c3-Rx1C3NLRAK51S0gFVhv_Qw-G1l-b7sx5atg', json_encode(time()), 'alert', 1, $data, GOOGLE_KEY_TINMOI);
        $result = $this->pushAndroidMessage('APA91bEnD_9VjKq8fE3B_sLB0wBGiSJc4W2ydtVgHpzAhg-Md5T35p7VbcZFDHF7_F_nEm9VfLcnVZfMkc8jS5lI6Xx9PfzmBGtNEGHMJkKe7sakt7QLB8ji43D1BzoWgcZvSAX9X2o1JY5p5soRKv49jk4z4eh0cw', json_encode(time()), 'alert', 1, $data, GOOGLE_KEY_TINMOI);
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
}
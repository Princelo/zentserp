<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alipay_Notify extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MOrder', 'MOrder');
        $this->db = $this->load->database('default', true);
    }


    public function index()
    {
        $alipay_config = alipay_config();
        require_once("application/third_party/alipay/lib/alipay_notify.class.php");
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        $result = false;

        if($verify_result) {
            $out_trade_no = $_POST['out_trade_no'];
            $trade_no = $_POST['trade_no'];
            $trade_status = $_POST['trade_status'];
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                $result = $this->MOrder->updatePaymentStatus($out_trade_no);
                if(!$result)
                    logResult('update payment error:'.$out_trade_no . "\\n");
                logResult($out_trade_no . " " . $trade_no . " " . $trade_status . "\\n");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $result = $this->MOrder->updatePaymentStatus($out_trade_no);
                if(!$result)
                    logResult('update payment error:'.$out_trade_no . "\\n");
                logResult($out_trade_no . " " . $trade_no . " " . $trade_status . "\\n");
            }
            if($result)
                echo "success";		//请不要修改或删除
            else
                echo 'fail';
        }
        else {
            echo "fail";
            logResult("notify fail out_trade_no:".$_POST['out_trade_no'].' trade_no:'.$_POST['trade_no']. ' trade_status:'.$_POST['trade_status']);
            //if($_POST['trade_status'] == 'TRADE_SUCCESS')
            //    logPayError();
        }
    }


}


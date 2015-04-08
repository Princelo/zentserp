<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Trial_Order extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('login');
        if($this->session->userdata('level') == 0 && $this->session->userdata('role') != 'admin')
            exit('You are not a member.');
        $this->load->model('MTrialProduct', 'MTrialProduct');
        $this->load->model('MTrialOrder', 'MTrialOrder');
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }


    public function listpage_admin($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  'Search Keyword',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'uid',
                'label' =>  'User Id',
                'rules' =>  'trim|xss_clean|numeric'
            ),
            /*array(
                'field' =>  'is_finish',
                'label' =>  'Is Finish',
                'rules' =>  'trim|xss_clean|boolean'
            ),*/
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('uid', true) != '' ||
            $this->input->get('is_finish', true) != '' ||
            $this->input->get('date_from', true) != '' ||
            $this->input->get('date_to', true) != '' ||
            $this->input->get('hour', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $uid = $this->input->get('uid', true);
            $is_finish = $this->input->get('is_finish', true);
            $date_from = $this->input->get('date_from', true);
            $date_to = $this->input->get('date_to', true);
            $hour = $this->input->get('hour', true);
            $data = array();
            $config['base_url'] = base_url()."trial_order/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish, $date_from, $date_to, $hour);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('trial_order/listpage_admin', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) ';
            $config['base_url'] = base_url()."trial_order/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('trial_order/listpage_admin', $data);
        }
    }

    public function listpage($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  'Search Keyword',
                'rules' =>  'trim|xss_clean'
            ),
            /*array(
                'field' =>  'uid',
                'label' =>  'User Id',
                'rules' =>  'trim|xss_clean|numeric'
            ),*/
            /*array(
                'field' =>  'is_finish',
                'label' =>  'Is Finish',
                'rules' =>  'trim|xss_clean|boolean'
            ),*/
        );
        $uid = $this->session->userdata('current_user_id');
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            //$this->input->get('uid', true) != '' ||
            $this->input->get('is_finish', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            //$uid = $this->input->get('uid', true);
            $is_finish = $this->input->get('is_finish', true);
            $data = array();
            $config['base_url'] = base_url()."trial_order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('trial_order/listpage', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) and o.user_id = '. $uid;
            $config['base_url'] = base_url()."trial_order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('trial_order/listpage', $data);
        }
    }

    public function listpage_sub($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $pid = $this->session->userdata('current_user_id');
        $current_user_id = $this->input->get('user');
        if(!is_numeric($current_user_id))
            exit('User Id Error');
        if(!$this->MUser->isParent($pid, $current_user_id))
            exit('You are not the Superior of this user');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  'Search Keyword',
                'rules' =>  'trim|xss_clean'
            ),
            /*array(
                'field' =>  'uid',
                'label' =>  'User Id',
                'rules' =>  'trim|xss_clean|numeric'
            ),*/
            array(
                'field' =>  'is_finish',
                'label' =>  'Is Finish',
                'rules' =>  'trim|xss_clean|boolean'
            ),
        );
        //$uid = $this->session->userdata('current_user_id');
        $uid = $current_user_id;
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            //$this->input->get('uid', true) != '' ||
            $this->input->get('is_finsh', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            //$uid = $this->input->get('uid', true);
            $is_finish = $this->input->get('is_finish', true);
            $data = array();
            $config['base_url'] = base_url()."trial_order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('trial_order/listpage', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) and o.user_id = '. $uid;
            $config['base_url'] = base_url()."trial_order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MTrialOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MTrialOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('trial_order/listpage', $data);
        }

    }

    public function details($order_id)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $data = array();
        $data['v'] = $this->MTrialOrder->objGetOrderInfo($order_id);
        $this->load->view('templates/header', $data);
        $this->load->view('trial_order/details', $data);
    }

    public function details_admin($order_id)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $data = array();
        $data['v'] = $this->MTrialOrder->objGetOrderInfo($order_id);
        if($this->input->post('finish', true) != '')
        {
            if($this->input->post('finish') == 'finish_with_pay')
            {
                if($data['v']->is_pay == 't' && $data['v']->is_correct == 't')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单已完成');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线上付款类，不能插入新付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if(money($data['v']->pay_amt) > 0)
                {
                    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                $result = $this->MTrialOrder->finish_with_pay($order_id,
                                                         bcmul(money($data['v']->unit_price), $data['v']->quantity, 4 ),
                                                         $data['v']->uid,
                                                         $data['v']->parent_user_id,
                                                         $data['v']->is_root,
                                                         bcadd(bcmul(money($data['v']->unit_price), $data['v']->quantity, 4 ), money($data['v']->post_fee), 4),
                                                         $data['v']->is_first);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                    redirect('trial_order/details_admin/'.$order_id);
                }
            }
            if($this->input->post('finish') == 'finish_without_pay')
            {
                if($data['v']->is_pay == 't' && $data['v']->is_correct == 't')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单已完成');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 'f')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线下付款类，必须插入付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay == 'f' && $data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单未支付金额，且属于线上交易类，未能完成订单');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if(
                (money($data['v']->pay_amt) <
                    bcadd(money($data['v']->post_fee), bcmul(money($data['v']->unit_price), $data['v']->count, 4), 4)
                ) &&
                $data['v']->is_pay_online == 't'
                )
                {
                    $this->session->set_flashdata('flashdata', '该订单支付金额不足，未能完成订单');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                $result = $this->MTrialOrder->finish_without_pay($order_id);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                }

            }
            if($this->input->post('finish') == 'unfinish_rollback')
            {
                if($data['v']->is_pay == 'f' || $data['v']->is_correct == 'f')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单未完成');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线上付款类，禁止清除付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                $result = $this->MTrialOrder->unfinish_rollback($order_id);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                }
            }
            if($this->input->post('finish') == 'unfinish')
            {
                if($data['v']->is_pay == 'f' || $data['v']->is_correct == 'f')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单未完成');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 'f')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线下付款类，要执行该操作，必须清除付款纪录');
                    redirect('trial_order/details_admin/'.$order_id);
                }
                $result = $this->MTrialOrder->unfinish($order_id);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                }
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view('trial_order/details_admin', $data);
    }

    public function add($product_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') == 0)
            exit('You are not a member.');
        $data = array();
        $data['error'] = '';
        $config = array(
            /*array(
                'field'   => 'product_id',
                'label'   => 'Product Id',
                'rules'   => 'required|integer|xss_clean'
            ),*/
            array(
                'field'   => 'is_post',
                'label'   => 'Stock Type',
                'rules'   => 'boolean|xss_clean'
            ),
            array(
                'field'   => 'contact',
                'label'   => 'Linkman',
                'rules'   => 'trim|xss_clean|required'
            ),
            array(
                'field'   => 'mobile',
                'label'   => 'Mobile no',
                'rules'   => 'trim|xss_clean|required'
            ),
            array(
                'field'   => 'remark',
                'label'   => 'Remark',
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field'   => 'count',
                'label'   => 'Purchase quantity',
                'rules'   => 'trim|xss_clean|is_natural|greater_than[0]'
            ),
            array(
                'field'   => 'pay_method',
                'label'   => 'Pay method',
                'rules'   => 'trim|xss_clean|required'
            ),
        );
        if($this->input->post('is_post') === true)
        {
            array_merge($config,
                array(
                    'field'   => 'province_id',
                    'label'   => 'Province',
                    //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                    'rules'   => 'trim|xss_clean|integer'
                )
            );
            array_merge($config,
                array(
                    'field'   => 'city',
                    'label'   => 'City',
                    'rules'   => 'trim|xss_clean|integer'
                )
            );
            array_merge($config,
                array(
                    'field'   => 'address_info',
                    'label'   => 'Address Info',
                    'rules'   => 'trim|xss_clean'
                )
            );
        }


        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            $this->__extra_verify();
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header_user', $data);
                $this->load->view('trial_order/add', $data);
            }else{
                $address_info = array(
                    'province_id' => $this->input->post('province_id'),
                    'city_id' => $this->input->post('city_id'),
                    'address_info' => $this->input->post('address_info'),
                    'contact' => $this->input->post('contact'),
                    'mobile'  => $this->input->post('mobile'),
                    'remark'  => $this->input->post('remark'),
                );
                $main_data = array(
                    //'product_id' => $this->input->post('product_id'),
                    'product_id' => $product_id,
                    'count' => $this->input->post('count'),
                    'level' => $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id')),
                    'is_post' => $this->input->post('is_post'),
                    'pay_method' => $this->input->post('pay_method'),
                    'post_fee' => 0,
                );
                $main_data['post_fee'] = $this->calcPostFee($main_data, $address_info);
                $result_id = $this->MTrialOrder->intAddReturnOrderId($main_data, $address_info);
                if($result_id != 0){
                    $this->session->set_flashdata('flashdata', '订单添加成功');
                    if($this->input->post('pay_method') == 'alipay')
                        redirect('trial_order/pay_method/'.$result_id);
                    else
                        redirect('trial_order/add/'.$product_id);
                }
                else{
                    $this->session->set_flashdata('flashdata', '订单添加失败');
                    redirect('trial_order/add'.$product_id);
                }
            }
        }else{
            $data['product_name'] = $this->MTrialProduct->strGetProductTitle($product_id);
            $data['product_id'] = $product_id;
            $this->load->view('templates/header_user', $data);
            $this->load->view('trial_order/add', $data);
        }

    }
    public function pay_method($order_id)
    {
        $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
        $data = array();
        $data = $this->MTrialOrder->getOrderPrice($order_id);
        $data->token = $this->session->userdata('token');
        $data->order_id = $order_id;
        $this->load->view('templates/header_user', $data);
        $this->load->view('trial_order/pay_method', $data);
    }

    public function pay($order_id)
    {
        if(!$this->__validate_token())
            exit('your operation is expired!');
        $this->MOrder->is_paid($order_id);
        require_once("application/third_party/alipay/lib/alipay_submit.class.php");
        $alipay_config = alipay_config();
        $alipaySubmit = new AlipaySubmit($alipay_config);


        $payment_type = "1";
        $notify_url = base_url()."alipay_notify?alipay=sb";
        //there's a bug that alipay api will filter out the first para of the url return.
        //fixed it by insert a para in the url.

        $return_url = base_url()."order/return_alipay?alipay=sb";

        $out_trade_no = $this->session->userdata('user') . date('YmdHis') . random_string('numeric', 4);

        $is_update_out_trade_no_success = $this->MTrialOrder->updateOrderTradeNo($out_trade_no, $order_id);
        if(!$is_update_out_trade_no_success)
            exit('error!\nPlease try again later');

        $subject = $this->session->userdata('user') . "_-_ERP_no.Trial".$order_id;

        $data = $this->MTrialOrder->getOrderPrice($order_id);
        $total_fee = $data->total;


        //$anti_phishing_key = $alipaySubmit->query_timestamp();
        $anti_phishing_key = "";

        $exter_invoke_ip = get_client_ip();

        $body = "";
        $show_url = "";

        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "show_url"	=> $show_url,
            "anti_phishing_key"	=> $anti_phishing_key,
            "exter_invoke_ip"	=> $exter_invoke_ip,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"></head>";
        echo "<div style=\"display:none;\">".$html_text."</div></html>";

    }


    private function __get_search_str($search = '', $uid = '', $is_finish = null, $date_from = null, $date_to = null, $hour = null)
    {
        $where = '';
        if($search != '')
        {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' ) ";
        }
        if($uid != '')
        {
            $where .= " and o.user_id = {$uid} ";
        }
        if($is_finish != null)
        {
            if($is_finish == '1')
                $where .= " and o.is_pay = true and o.is_correct = true ";
            elseif($is_finish == '0')
                $where .= " and (o.is_pay = false or o.is_correct = false) ";
        }
        if($date_from != null && $date_to == null && $hour == null)
        {
            $where = " and o.create_time between '{$date_from} 00:00:00' and now() ";
        }
        if($date_to != null && $date_from == null && $hour == null)
        {
            $where = " and o.create_time between '2014-12-30 00:00:00' and '{$date_to} 23:59:59' ";
        }
        if($date_from != null && $date_to != null && $hour == null)
        {
            $where = " and o.create_time between '{$date_from} 00:00:00' and '{$date_to} 23:59:59' ";
        }
        if($hour != null)
        {
            $where = " and o.create_time + interval '{$hour} hour' >= now() ";
        }
        return $where;
    }

    private function _finish($order_id)
    {

    }

    private function  __validate_token($token = 'token'){
        if(isset($_POST[$token]) && $_POST[$token] != $this->session->userdata($token)){
            $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
            return false;
        }else if(!isset($_POST[$token])){
            $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
            return false;
        }else if($_POST[$token] == $this->session->userdata($token)){
            return true;
        }
    }


    private function __extra_verify()
    {
        if($this->input->post('pay_method') != 'alipay'
            && $this->input->post('pay_method') != 'offline')
            exit('pay_method error!');
    }

    private function calcPostFee($data, $address_info)
    {
        if($data['is_post'] == 1) {
            $this->db->select('first_pay')
                ->select('additional_pay')
                ->select('first_weight')
                ->select('additional_weight')
                ->from('post_rules')
                ->where(
                    array(
                        'province_id' => $address_info['province_id'],
                        'city_id' => $address_info['city_id']
                    )
                );
            $query = $this->db->get()->result();
            if(empty($query)) {
                $this->db->select('first_pay')
                    ->select('additional_pay')
                    ->select('first_weight')
                    ->select('additional_weight')
                    ->from('post_rules')
                    ->where(
                        array(
                            'province_id' => $address_info['province_id'],
                            'city_id' => 0,
                        )
                    );
                $query = $this->db->get()->result();
            }
            if(empty($query)) {
                $this->db->select('first_pay')
                    ->select('additional_pay')
                    ->select('first_weight')
                    ->select('additional_weight')
                    ->from('post_rules')
                    ->where(
                        array(
                            'province_id' => 0,
                            'city_id' => 0,
                        )
                    );
                $query = $this->db->get()->result();
            }

            $query = $query[0];
            $first_pay = money($query->first_pay);
            $additional_pay = money($query->additional_pay);
            $first_weight = $query->first_weight;
            $additional_weight = $query->additional_weight;
            $quantity = $data['count'];
            $product_id = $data['product_id'];
            $total_weight = $this->db->select('weight')
                ->from('trial_products')
                ->where(array('id' => $product_id))
                ->get()
                ->result()[0]->weight;
            $total_weight = $total_weight * $quantity;

            if($total_weight < $first_weight) {
                return $first_pay;
            } else {
                $additional_total_weight = $total_weight - $first_weight;
                $additional_count = ceil( bcdiv($additional_total_weight, $additional_weight, 4) );
                return bcadd( $first_pay, bcmul($additional_pay, $additional_count));
            }
        } else {
            return 0;
        }
    }
}

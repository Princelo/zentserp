<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Order extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('login');
        $this->load->model('MProduct', 'MProduct');
        $this->load->model('MOrder', 'MOrder');
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function index_sub($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $current_user_id = $this->session->userdata('current_user_id');
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  '用戶名',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'level',
                'label' =>  'Level',
                'rules' =>  'trim|xss_clean|is_natural'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('level', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $level = $this->input->get('level', true);
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."order/index_sub/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            //$where .= ' and p.is_valid = true ';
            $where = "";
            $where .= $this->__get_search_str($search, $level);
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            //$where = ' and is_admin = false ';
            $order = '';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/index_sub', $data);
        }else{
            $data = array();
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['base_url'] = base_url()."order/index_sub/";
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $iwhere = " and p.id = {$current_user_id} ";
            $where = "";
            $config['total_rows'] = $this->MUser->intGetSubUsersCount($where, $iwhere);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = ' and p.is_valid = true ';
            $order = '';
            $data['users'] = $this->MUser->objGetSubUserList($where, $iwhere, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/index_sub', $data);
        }
    }

    public function query_sub($id)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $pid = $this->session->userdata('current_user_id');
        if(!is_numeric($id))
            exit('ERROR');
        if(!$this->MUser->isParent($pid, $id))
            exit('You are not the Superior of this user');
        $data['id'] = $id;
        $this->load->view('templates/header_user');
        $this->load->view('order/query_sub', $data);
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
            $config['base_url'] = base_url()."order/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish, $date_from, $date_to, $hour);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('order/listpage_admin', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) ';
            $config['base_url'] = base_url()."order/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('order/listpage_admin', $data);
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
            $config['base_url'] = base_url()."order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/listpage', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) and o.user_id = '. $uid;
            $config['base_url'] = base_url()."order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/listpage', $data);
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
            $config['base_url'] = base_url()."order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            //$where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $uid, $is_finish);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = ' order by o.create_time desc ';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/listpage', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) and o.user_id = '. $uid;
            $config['base_url'] = base_url()."order/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MOrder->intGetOrdersCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = ' order by o.create_time desc';
            $data['orders'] = $this->MOrder->objGetOrderList($where, $order, $limit);
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/listpage', $data);
        }

    }

    public function details($order_id)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $data = array();
        $data['v'] = $this->MOrder->objGetOrderInfo($order_id);
        $this->load->view('templates/header_user', $data);
        $this->load->view('order/details', $data);
    }
    public function details_admin($order_id)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $data = array();
        $data['v'] = $this->MOrder->objGetOrderInfo($order_id);
        if($this->input->post('post_info') != '')
        {
            $this->db->query("update orders set post_info = '{$this->input->post('post_info')}' where id = {$order_id}");
            if($this->input->post('finsh') == '')
                redirect('order/details_admin/'.$order_id);
        }
        if($this->input->post('finish', true) != '')
        {
            if($this->input->post('finish') == 'finish_with_pay')
            {
                if($data['v']->is_pay == 't' && $data['v']->is_correct == 't')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单已完成');
                    redirect('order/details_admin/'.$order_id);
                }
                //if($data['v']->is_pay_online == 't')
                //{
                    //$this->session->set_flashdata('flashdata', '该订单属于线上付款类，不能插入新付款纪录');
                    //redirect('order/details_admin/'.$order_id);
                //}
                //if($data['v']->is_pay == 't')
                //{
                //    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                //    redirect('order/details_admin/'.$order_id);
                //}
                //if(money($data['v']->pay_amt) > 0)
                //{
                //    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                //    redirect('order/details_admin/'.$order_id);
                //}
                $result = $this->MOrder->finish_with_pay($order_id,
                                                         bcadd(money($data['v']->amount), money($data['v']->post_fee),4),
                                                         $data['v']->uid,
                                                         $data['v']->parent_user_id,
                                                         $data['v']->is_root,
                                                         money($data['v']->amount),
                                                         $data['v']->post_fee,
                                                         money($data['v']->amount),
                                                         $data['v']->is_first);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                    redirect('order/details_admin/'.$order_id);
                }
            }
            if($this->input->post('finish') == 'finish_without_pay')
            {
                if($data['v']->is_pay == 't' && $data['v']->is_correct == 't')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单已完成');
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 'f')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线下付款类，必须插入付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay == 'f' && $data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单未支付金额，且属于线上交易类，未能完成订单');
                    redirect('order/details_admin/'.$order_id);
                }
                if(
                (money($data['v']->pay_amt) <
                    bcadd(money($data['v']->post_fee), bcmul(money($data['v']->unit_price), $data['v']->count, 4), 4)
                ) &&
                $data['v']->is_pay_online == 't'
                )
                {
                    $this->session->set_flashdata('flashdata', '该订单支付金额不足，未能完成订单');
                    redirect('order/details_admin/'.$order_id);
                }
                $result = $this->MOrder->finish_without_pay($order_id);
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
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线上付款类，禁止清除付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                $result = $this->MOrder->unfinish_rollback($order_id);
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
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 'f')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线下付款类，要执行该操作，必须清除付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                $result = $this->MOrder->unfinish($order_id);
                if($result === true)
                {
                    $this->session->set_flashdata('flashdata', '订单更改成功');
                }
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view('order/details_admin', $data);
    }

    public function order_product($order_id)
    {
        if($this->session->userdata('role') != 'admin')
        {
            if(!$this->MOrder->checkIsOwn($this->session->userdata('current_user_id'), $order_id))
            {
                exit('The order is not yours');
            }
        }
        $data = array();
        $data['products'] = $this->MOrder->getOrderProducts($order_id);
        if($this->session->userdata('role') != 'admin')
        {
            $this->load->view('templates/header_user');
            $this->load->view('order/order_product', $data);
        }
        else
        {
            $this->load->view('templates/header');
            $this->load->view('order/order_product_admin', $data);
        }
    }

    public function add()
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        //if($this->session->userdata('level') == 0)
        //    redirect('order/add_non_member/');
        if($this->input->post('token') != $this->session->userdata('token')){
            redirect('order/listpage');
        }
        $data = array();
        $data['error'] = '';
        $products = $this->getProducts($this->input->post('cart_info'));
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
                'field'   => 'pay_method',
                'label'   => 'Pay method',
                'rules'   => 'trim|xss_clean|required'
            ),
        );
        if($this->input->post('is_post') == 1)
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
                redirect('order/cart');
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
                    'level' => $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id')),
                    'pay_method' => $this->input->post('pay_method'),
                    'is_post' => $this->input->post('is_post'),
                    'post_fee' => 0,
                    'products' => $products,
                );
                $main_data['post_fee'] = $this->calcPostFee($main_data, $address_info);
                $result_id = $this->MOrder->intAddReturnOrderId($main_data, $address_info);
                if($result_id != 0){
                    $this->session->set_flashdata('flashdata', '订单添加成功');
                    if($this->input->post('pay_method') == 'alipay')
                        redirect('order/pay_method/'.$result_id);
                    else
                        redirect('order/listpage/');
                }
                else{
                    $this->session->set_flashdata('flashdata', '订单添加失败');
                    redirect('order/cart');
                }
            }
        }/*else{
            $data['product_name'] = $this->MProduct->strGetProductTitle($product_id);
            $data['product_id'] = $product_id;
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/add', $data);
        }*/

    }

    private function addbak($product_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') == 0)
            redirect('order/add_non_member/'.$product_id);
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
        if($this->input->post('is_post') == 1)
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
                $this->load->view('order/add', $data);
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
                    'pay_method' => $this->input->post('pay_method'),
                    'is_post' => $this->input->post('is_post'),
                    'post_fee' => 0,
                );
                $main_data['post_fee'] = $this->calcPostFee($main_data, $address_info);
                $result_id = $this->MOrder->intAddReturnOrderId($main_data, $address_info);
                if($result_id != 0){
                    $this->session->set_flashdata('flashdata', '订单添加成功');
                    if($this->input->post('pay_method') == 'alipay')
                        redirect('order/pay_method/'.$result_id);
                    else
                        redirect('order/add/'.$product_id);
                }
                else{
                    $this->session->set_flashdata('flashdata', '订单添加失败');
                    redirect('order/add'.$product_id);
                }
            }
        }else{
            $data['product_name'] = $this->MProduct->strGetProductTitle($product_id);
            $data['product_id'] = $product_id;
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/add', $data);
        }

    }

    public function add_non_member($product_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') != 0)
            exit('You are a member');
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
        if($this->input->post('is_post') == 1)
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
                $this->load->view('order/add_non_member', $data);
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
                    'is_valid' => false,
                );
                $main_data['post_fee'] = $this->calcPostFee($main_data, $address_info);
                $result_id = $this->MOrder->intAddNonMemberReturnOrderId($main_data, $address_info);
                if($result_id != 0){
                    $this->session->set_flashdata('flashdata', '加入购物车成功');
                    //if($this->input->post('is_post') === true)
                        //redirect('order/pay/'.$result_id);
                    //else
                        redirect('order/add_non_member/'.$product_id);
                }
                else{
                    $this->session->set_flashdata('flashdata', '加入购物车失败');
                    redirect('order/add_non_member/'.$product_id);
                }
            }
        }else{
            $data['product_name'] = $this->MProduct->strGetProductTitle($product_id);
            $data['product_info'] = $this->MProduct->objGetProductInfo($product_id);
            $data['cart'] = $this->MProduct->objGetCart($this->session->userdata('current_user_id'));
            /*if(isset($data['cart'][0])){
                if($data['cart'][0]->level == 1)
                    $data['target'] = 19800;
                if($data['cart'][0]->level == 2)
                    $data['target'] = 3980;
                if($data['cart'][0]->level == 3)
                    $data['target'] = 1980;
            }else{
                $assign_level = $this->MUser->objGetUserInfo($this->session->userdata('current_user_id'));
                $assign_level = $assign_level->assign_level;
                if($assign_level == 1)
                    $data['target'] = 19800;
                if($assign_level == 2)
                    $data['target'] = 3980;
                if($assign_level == 3)
                    $data['target'] = 1980;
            }*/
            $data['target'] = 1980;
            $data['product_id'] = $product_id;
            $this->load->view('templates/header_user', $data);
            $this->load->view('order/add_non_member', $data);
        }

    }

    public function addtocart()
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        $config = array(
            array(
                'field'   => 'product_id',
                'label'   => 'Product Id',
                'rules'   => 'required|is_natural|xss_clean|required'
            ),
            array(
                'field'   => 'is_trial',
                'label'   => 'Is Trial',
                'rules'   => 'required|xss_clean|required'
            ),
            array(
                'field'   => 'quantity',
                'label'   => 'Quantity',
                'rules'   => 'required|is_natural|xss_clean|required|greater_than[0]'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE)
        {
            echo "{'info':'未知錯誤'}";
            return false;
        }
        $check = $this->db->select('id')
            ->from('cart_product')
            ->where(
                array(
                    'product_id' => $this->input->post('product_id'),
                    'user_id' => $this->session->userdata('current_user_id'),
                    'is_trial' => $this->input->post('is_trial'),
                    'is_finished' => 'false',
                )
            );
        $check = $this->db->get()->result();
        if(!empty($check)){
            $return_data = array();
            $return_data['info'] = '错误:您的购物车存在此产品';
            exit(json_encode($return_data));
        }
        $result = $this->MOrder->addToCart($this->session->userdata('current_user_id'),
            $this->input->post('product_id'),
            $this->input->post('quantity'),
            $this->input->post('is_trial')
        );
        $return_data = array();
        if($result)
            $return_data['info'] = "成功添加至购物车！";
        else
            $return_data['info'] = "未知錯誤！";
        echo json_encode($return_data);
        return false;
    }

    public function remove_from_cart($product_id)
    {
        $result = $this->db->from('cart_product')->where(array('product_id'=> $product_id))->delete();
        if($result === true)
        {
            $this->session->set_flashdata('flashdata', '刪除成功');
            redirect('order/cart');
        }else
        {
            $this->session->set_flashdata('flashdata', '刪除失败');
            redirect('order/cart');
        }

    }

    public function enableCart()
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') != 0)
            exit('You are a member');
        $data = $this->MProduct->objGetCart($this->session->userdata('current_user_id'));
        /*if($data[0]->level == 1)
            $target = 19800;
        if($data[0]->level == 2)
            $target = 3980;
        if($data[0]->level == 3)
            $target = 1980;
        */
        $target = 1980;
        $total = 0;
        foreach($data as $k => $v)
        {
            $total = bcadd($total, bcmul(money($v->amount), $v->count, 2), 2);
        }

        if($total < $target)
        {
            exit('not enough');
        }
        $result = $this->MOrder->enableCart($this->session->userdata('current_user_id'));
        if($result) {
            $this->session->set_flashdata('flashdata', '结算成功');
            $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
            redirect('order/pay_method_non_member');
        } else {
            $this->session->set_flashdata('flashdata', '结算失败');
            redirect('product/listpage');
        }
    }

    public function cart()
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        $user_id = $this->session->userdata('current_user_id');
        $level = $this->session->userdata('level');
        $data = array();
        $data['products'] = $this->MOrder->getCartInfo($user_id, $level);
        $data['level'] = $this->session->userdata('level');


        $this->load->view('templates/header_user', $data);
        $this->load->view('order/cart', $data);
    }

    public function add_by_cart()
    {
        if(!isset($_POST) || empty($_POST))
        {
            redirect('order/cart');
        }
        $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
        $config = array(
            array(
                'field'   => 'items',
                'label'   => 'items',
                'rules'   => 'required|xss_clean'
            ),
        );

        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == FALSE)
        {
            redirect('order/cart');
        }
        $products = $this->getProducts($this->input->post('items'));
        $data = array();
        $data['products_quantity'] = $products;
        $user_id = $this->session->userdata('current_user_id');
        $level = $this->session->userdata('level');
        $data['products'] = $this->MOrder->getCartInfo($user_id, $level);
        if(empty($data['products']))
        {
            redirect('order/listpage');
        }
        $data['str'] = $this->input->post('items');
        $data['token'] = $this->session->userdata('token');
        $this->load->view('templates/header_user', $data);
        $this->load->view('order/add_by_cart', $data);

    }

    private function getProducts($str)
    {
        $str = explode("|", $str);
        $arr = array();
        foreach($str as $k => $v)
        {
            $value = substr($v, strpos($v, ",") + 1);
            $kvalue = substr($v, 0, strpos($v, ","));
            $arr[$kvalue] = $value;
        }
        return $arr;
    }

    public function pay_method($order_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        /*if($this->session->userdata('level') == 0 )
            exit('You are not a member');*/
        $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
        $data = array();
        $data = $this->MOrder->getOrderPrice($order_id);
        $data->token = $this->session->userdata('token');
        $data->order_id = $order_id;
        $this->load->view('templates/header_user', $data);
        $this->load->view('order/pay_method', $data);
    }

    public function pay_method_trial($order_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') == 0 )
            exit('You are not a member');
        $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
        $data = array();
        $data = $this->MOrder->getOrderPriceTrial($order_id);
        $data->token = $this->session->userdata('token');
        $data->order_id = $order_id;
        $this->load->view('templates/header_user', $data);
        $this->load->view('trial_order/pay_method', $data);
    }

    public function pay_method_non_member()
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') != 0)
            exit('You are a member');
        $this->session->set_userdata('token', md5(date('YmdHis').rand(0, 32000)) );
        $data = array();
        $data = $this->MOrder->getNonMemberCartTotal();
        $data->token = $this->session->userdata('token');
        $this->load->view('templates/header_user', $data);
        $this->load->view('order/pay_method_non_member', $data);
    }

    public function pay($order_id)
    {
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
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

        $is_update_out_trade_no_success = $this->MOrder->updateOrderTradeNo($out_trade_no, $order_id);
        if(!$is_update_out_trade_no_success)
            exit('error!\nPlease try again later');

        $subject = $this->session->userdata('user') . "_-_ERP_no.".$order_id;

        $data = $this->MOrder->getOrderPrice($order_id);
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

    public function delete($order_id, $product_id){
        if($this->session->userdata('role') == 'admin')
            exit('You are the admin.');
        if($this->session->userdata('level') != 0)
            exit('You are a member');
        if(!$this->MOrder->checkIsOwn($this->session->userdata('current_user_id'), $order_id))
        {
            exit('This order is not yours');
        }
        $result = $this->MOrder->delete($order_id);
        if($result)
            $this->session->set_flashdata('flashdata', '移除成功');
        else
            $this->session->set_flashdata('flashdata', '移除失败');
        redirect('order/add_non_member/'.$product_id);
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
            //$quantity = $data['count'];
            //$product_id = $data['product_id'];
            $total_weight = 0;
            foreach($data['products'] as $k => $v)
                $total_weight += $this->db->select('weight')
                    ->from('products')
                    ->where(array('id' => $k))
                    ->get()
                    ->result()[0]->weight * $v;

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


    public function return_alipay()
    {
        require_once("application/third_party/alipay/lib/alipay_notify.class.php");
        $alipay_config = alipay_config();
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号

            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];


            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"></head>";
                echo "验证成功<br />";
                echo "<script>alert('支付成功！请等待管理员审核完成实物交易。');</script>";
                echo "<script>window.location.href=\"".base_url()."order/listpage\";</script>";
                echo "</html>";
            }
            else {
                echo "trade_status=".$_GET['trade_status'];
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\"></head>";
            echo "<script>alert('你的支付信息将同步到系统！请等待管理员审核完成实物交易。');</script>";
            echo "<script>window.location.href=\"".base_url()."order/listpage\";</script>";
            echo "验证失败";
            echo "</html>";
        }
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


}


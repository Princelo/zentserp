<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MProduct', 'MProduct');
        $this->load->model('MOrder', 'MOrder');
        $this->load->model('MUser', 'MUser');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function listpage_admin($offset = 0)
    {
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
            array(
                'field' =>  'is_finish',
                'label' =>  'Is Finish',
                'rules' =>  'trim|xss_clean|boolean'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('uid', true) != '' ||
            $this->input->get('is_finsh', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $uid = $this->input->get('uid', true);
            $is_finish = $this->input->get('is_finish', true);
            $data = array();
            $config['base_url'] = base_url()."order/listpage_admin/";
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
            $this->load->view('templates/header', $data);
            $this->load->view('order/listpage_admin', $data);
        }else{
            $data = array();
            $where = ' and ( o.is_pay = true or o.is_correct = false ) ';
            $config['base_url'] = base_url()."order/listpage_admin/";
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

    public function details_admin($order_id)
    {
        /*if($this->session->userdata('admin') == ""){
            redirect('forecast/index', 'refresh');
        }*/
        $data = array();
        $data['v'] = $this->MOrder->objGetOrderInfo($order_id);
        if($this->input->post('finish', true) != '')
        {
            if($this->input->post('finish') == 'finish_with_pay')
            {
                if($data['v']->is_pay == 't' && $data['v']->is_correct == 't')
                {
                    $this->session->set_flashdata('flashdata', '操作有误: 订单已完成');
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay_online == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单属于线上付款类，不能插入新付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                if($data['v']->is_pay == 't')
                {
                    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                if(money($data['v']->pay_amt) > 0)
                {
                    $this->session->set_flashdata('flashdata', '该订单已支付金额，不能插入新付款纪录');
                    redirect('order/details_admin/'.$order_id);
                }
                $result = $this->MOrder->finish_with_pay($order_id,
                                                         bcmul(money($data['v']->unit_price), $data['v']->quantity, 4 ),
                                                         $data['v']->uid,
                                                         $data['v']->parent_user_id,
                                                         $data['v']->is_root,
                                                         bcadd(bcmul(money($data['v']->unit_price), $data['v']->quantity, 4 ), money($data['v']->post_fee), 4),
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

    public function add($product_id)
    {
        /*if(!isset($_SESSION['admin'])){
            redirect('login', 'refresh');
        }*/
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
                'rules'   => 'trim|xss_clean|integer'
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
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('order/add', $data);
            }else{
                $main_data = array(
                    //'product_id' => $this->input->post('product_id'),
                    'product_id' => $product_id,
                    'count' => $this->input->post('count'),
                    'level' => $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id')),
                    'is_post' => $this->input->post('is_post'),
                );
                $address_info = array(
                    'province_id' => $this->input->post('province_id'),
                    'city_id' => $this->input->post('city_id'),
                    'address_info' => $this->input->post('address_info'),
                    'contact' => $this->input->post('contact'),
                    'mobile'  => $this->input->post('mobile'),
                    'remark'  => $this->input->post('remark'),
                );
                $result_id = $this->MOrder->intAddReturnOrderId($main_data, $address_info);
                if($result_id != 0){
                    $this->session->set_flashdata('flashdata', '订单添加成功');
                    if($this->input->post('is_post') === true)
                        redirect('order/pay/'.$result_id);
                    else
                        redirect('order/add');
                }
                else{
                    $this->session->set_flashdata('flashdata', '订单添加失败');
                    redirect('order/add');
                }
            }
        }else{
            $data['product_name'] = $this->MProduct->strGetProductTitle($product_id);
            $data['product_id'] = $product_id;
            $this->load->view('templates/header', $data);
            $this->load->view('order/add', $data);
        }

    }

    private function __get_search_str($search = '', $uid = '', $is_finish = null)
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
            $where .= " and o.is_pay = true and o.is_correct = true ";
        }
        return $where;
    }

    private function _finish($order_id)
    {

    }
}


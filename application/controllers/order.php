<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MProduct', 'MProduct');
        $this->load->model('MOrder', 'MOrder');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    /*public function listpage($offset = 0)
    {
        $get_config = array(
            array(
                'field' =>  'search',
                'label' =>  '关键词',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'price_low',
                'label' =>  '价格区间(低)',
                'rules' =>  'trim|xss_clean|numeric'
            ),
            array(
                'field' =>  'price_high',
                'label' =>  '价格区间(高)',
                'rules' =>  'trim|xss_clean|numeric'
            ),
        );
        $this->form_validation->set_rules($get_config);
        if($this->input->get('search', true) != '' ||
            $this->input->get('price_low', true) != '' ||
            $this->input->get('price_high', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $price_low = $this->input->get('price_low', true);
            $price_high = $this->input->get('price_high', true);
            $data = array();
            $config['base_url'] = base_url()."product/listpage/";
            $where = '';
            $where .= ' and p.is_valid = true ';
            $where .= $this->__get_search_str($search, $price_low, $price_high);
            $config['total_rows'] = $this->MProduct->intGetProductsCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = '';
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('product/listpage', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."product/listpage/";
            $config['total_rows'] = $this->MProduct->intGetProductsCount(' and p.is_valid = true ');
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = ' and p.is_valid = true ';
            $order = '';
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $this->load->view('templates/header', $data);
            $this->load->view('product/listpage', $data);
        }
    }*/

    public function add($error = '')
    {
        /*if(!isset($_SESSION['admin'])){
            redirect('login', 'refresh');
        }*/
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'product_id',
                'label'   => 'Product Id',
                'rules'   => 'required|integer|xss_clean'
            ),
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
                    'product_id' => $this->input->post('product_id'),
                    'count' => $this->input->post('count'),
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
            $this->load->view('templates/header', $data);
            $this->load->view('order/add', $data);
        }

    }

    /*private function __get_search_str($search = '', $price_low = '', $price_high = '')
    {
        $where = '';
        if($search != '' && $price_low != '' && $price_high != '')
        {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            (cast(pr{$this->level}.price as numeric) between {$price_low} and {$price_high} )
                            ) ";
        }elseif($search != '' && $price_low == '' && $price_high == '')
        {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%') ";
        }elseif($search != '' && $price_low != '' && $price_high == '')
        {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            (cast(pr{$this->level}.price as numeric) > {$price_low} )
                            ) ";
        }elseif($search != '' && $price_low == '' && $price_high != '')
        {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            (cast(pr{$this->level}.price as numeric) < {$price_high} )
                            ) ";
        }elseif($search == '' && $price_low != '' && $price_high != '')
        {
            $where .= " and (cast(pr{$this->level}.price as numeric) between {$price_low} and {$price_high}) ";
        }elseif($search == '' && $price_low != '' && $price_high == '')
        {
            $where .= " and (cast(pr{$this->level}.price as numeric) > {$price_low} )";
        }elseif($search == '' && $price_low == '' && $price_high != '')
        {
            $where .= " and (cast(pr{$this->level}.price as numeric) < {$price_high} )";
        }

        return $where;
    }*/
}


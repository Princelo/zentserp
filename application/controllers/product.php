<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MProduct', 'MProduct');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->db = $this->load->database('default', true);
        $this->level = 1;
    }

    public function listpage($offset = 0)
    {
        /*$data = array();
        $data['products'] = $this->MProduct->objGetProductList();
        $this->load->view('templates/header', $data);
        $this->load->view('product/listpage', $data);*/
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
    }

    public function add($error = '')
    {
        /*if(!isset($_SESSION['admin'])){
            redirect('login', 'refresh');
        }*/
        $data = array();
        $data['error'] = $error;
        $config = array(
            array(
                'field'   => 'title',
                'label'   => '产品名称',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'price_special',
                'label'   => '特约代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field'   => 'price_last_2',
                'label'   => '一级代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field'   => 'price_last_3',
                'label'   => '二级代理价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('product/add', $data);
            }else{
                $price_list = array(
                    1 => $this->input->post('price_special'),
                    2 => $this->input->post('price_last_2'),
                    3 => $this->input->post('price_last_3'),
                );
                $main_data = array(
                    'title' => $this->input->post('title'),
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    'img' => $this->input->post('img'),
                    'is_valid' => $this->input->post('is_valid'),
                );
                $result = $this->MProduct->add($main_data, $price_list);
                if($result){
                    $this->session->set_flashdata('flashdata', '产品添加成功');
                    redirect('product/add');
                }
                else{
                    $this->session->set_flashdata('flashdata', '产品添加失败');
                    redirect('product/add');
                }
            }
        }else{
            $this->load->view('templates/header', $data);
            $this->load->view('product/add', $data);
        }

    }

    private function __get_search_str($search = '', $price_low = '', $price_high = '')
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
    }
}


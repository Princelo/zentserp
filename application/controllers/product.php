<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Product extends MY_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin' && $this->session->userdata('role') != 'user')
            redirect('login');
        $this->load->model('MProduct', 'MProduct');
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
        $is_trial = false;
        $trial_type = 0;
        if(@$_GET['is_trial'] == 'true') {
            $is_trial = true;
            if(isset($_GET['trial_type']) && intval($_GET['trial_type'] > 0))
                $trial_type = intval($_GET['trial_type']);
            if(isset($_POST['trial_type']) && intval($_POST['trial_type'] > 0))
                $trial_type = intval($_POST['trial_type']);
        }
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
            $this->input->get('price_high', true) != '' ||
            $this->input->get('category', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $price_low = $this->input->get('price_low', true);
            $price_high = $this->input->get('price_high', true);
            $category = $this->input->get('category', true);
            $data = array();
            $config['base_url'] = base_url()."product/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            $where .= ' and p.is_valid = true ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $where .= $this->__get_search_str($search, $price_low, $price_high, $category, $is_trial, $trial_type);
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
            if($is_trial)
                $this->load->view('trial_product/listpage_admin', $data);
            else
                $this->load->view('product/listpage_admin', $data);
        }else{
            $data = array();
            $config['base_url'] = base_url()."product/listpage_admin/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            $where .= ' and p.is_valid = true ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $config['total_rows'] = $this->MProduct->intGetProductsCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $order = '';
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $data['level'] = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->load->view('templates/header', $data);
            if($is_trial)
                $this->load->view('trial_product/listpage_admin', $data);
            else
                $this->load->view('product/listpage_admin', $data);
        }
    }

    public function listpage_admin_invalid($offset = 0)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $is_trial = false;
        $trial_type = 0;
        if(@$_GET['is_trial'] == 'true') {
            $is_trial = true;
            if(isset($_GET['trial_type']) && intval($_GET['trial_type'] > 0))
                $trial_type = intval($_GET['trial_type']);
            if(isset($_POST['trial_type']) && intval($_POST['trial_type'] > 0))
                $trial_type = intval($_POST['trial_type']);
        }
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
            $this->input->get('price_high', true) != '' ||
            $this->input->get('category', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $price_low = $this->input->get('price_low', true);
            $price_high = $this->input->get('price_high', true);
            $category = $this->input->get('category', true);
            $data = array();
            $config['base_url'] = base_url()."product/listpage_admin_invalid/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            $where .= ' and p.is_valid = false ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $where .= $this->__get_search_str($search, $price_low, $price_high, $category, $is_trial, $trial_type);
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
            if($is_trial)
                $this->load->view('trial_product/listpage_admin', $data);
            else
                $this->load->view('product/listpage_admin', $data);
        }else{
            $data = array();
            $where = ' and p.is_valid = false ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $order = '';
            $config['base_url'] = base_url()."product/listpage_admin_invalid/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MProduct->intGetProductsCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $data['level'] = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->load->view('templates/header', $data);
            if($is_trial)
                $this->load->view('trial_product/listpage_admin', $data);
            else
                $this->load->view('product/listpage_admin', $data);
        }
    }

    public function listpage($offset = 0)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are the admin.');
        $is_trial = false;
        $trial_type = 0;
        if(@$_GET['is_trial'] == 'true') {
            $is_trial = true;
            if(isset($_GET['trial_type']) && intval($_GET['trial_type'] > 0))
                $trial_type = intval($_GET['trial_type']);
            if(isset($_POST['trial_type']) && intval($_POST['trial_type'] > 0))
                $trial_type = intval($_POST['trial_type']);
        }
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
            $this->input->get('price_high', true) != '' ||
            $this->input->get('category', true) != ''
        )
        {
            $search = $this->input->get('search', true);
            $search = $this->db->escape_like_str($search);
            $price_low = $this->input->get('price_low', true);
            $price_high = $this->input->get('price_high', true);
            $category = $this->input->get('category', true);
            $data = array();
            $config['base_url'] = base_url()."product/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $where = '';
            $where .= ' and p.is_valid = true ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $where .= $this->__get_search_str($search, $price_low, $price_high, $category, $is_trial, $trial_type);
            $config['total_rows'] = $this->MProduct->intGetProductsCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            //$where = '';
            $order = '';
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $data['level'] = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->load->view('templates/header_user', $data);
            if($is_trial)
                $this->load->view('trial_product/listpage', $data);
            else
                $this->load->view('product/listpage', $data);
        }else{
            $data = array();
            $where = ' and p.is_valid = true ';
            if($is_trial) {
                $where .= ' and p.is_trial = true ';
                $where .= " and trial_type = {$trial_type} ";
            } else {
                $where .= ' and p.is_trial = false';
            }
            $order = '';
            $config['base_url'] = base_url()."product/listpage/";
            if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
            $config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
            $config['total_rows'] = $this->MProduct->intGetProductsCount($where);
            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $data['products'] = $this->MProduct->objGetProductList($where, $order, $limit);
            $data['level'] = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->load->view('templates/header_user', $data);
            if($is_trial)
                $this->load->view('trial_product/listpage', $data);
            else
                $this->load->view('product/listpage', $data);
        }
    }

    public function details_admin($product_id)
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $is_trial = false;
        $trial_type = 0;
        if(@$_GET['is_trial'] == 'true') {
            $is_trial = true;
            if(isset($_GET['trial_type']) && intval($_GET['trial_type'] > 0))
                $trial_type = intval($_GET['trial_type']);
            if(isset($_POST['trial_type']) && intval($_POST['trial_type'] > 0))
                $trial_type = intval($_POST['trial_type']);
        }
        $data = array();
        $data['v'] = $this->MProduct->objGetProductInfo($product_id);
        $config = array(
            array(
                'field'   => 'title',
                'label'   => '产品名称',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'category',
                'label' => '所属分类',
                'rules'   => 'trim|required|xss_clean|is_natural|less_than[5]',
            ),
            array(
                'field' => 'weight',
                'label' => '总重量',
                'rules'   => 'trim|required|xss_clean|is_natural',
            ),
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                if($is_trial)
                    $this->load->view('trial_product/details_admin/'.$product_id, $data);
                else
                    $this->load->view('product/details_admin/'.$product_id, $data);
            }else{
                /*if($this->input->post('is_valid') == '1' && $data['v']->is_valid == 't')
                {
                    $this->session->set_flashdata('flashdata', '非法操作: 产品本已上架');
                    redirect('product/details_admin/'.$product_id);
                }
                if($this->input->post('is_valid') == '0' && $data['v']->is_valid == 'f')
                {
                    $this->session->set_flashdata('flashdata', '非法操作: 产品本已下架');
                    redirect('product/details_admin/'.$product_id);
                }*/
                $main_data = array();
                $main_data = array(
                    'title' => $this->input->post('title'),
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    'category' => $this->input->post('category'),
                    'weight' => $this->input->post('weight'),
                    'is_valid' => false,
                );
                $main_data['is_valid'] = $this->input->post('is_valid')=='1'?'true':'false';
                if($this->MProduct->update($main_data, $product_id))
                {
                    $this->session->set_flashdata('flashdata', '产品更改成功');
                }else{
                    $this->session->set_flashdata('flashdata', '产品更改失败');
                }
                if($is_trial)
                    $get = "?is_trial=true";
                redirect('product/details_admin/'.$product_id.$get);
                /*if($this->input->post('is_valid') == '1')
                {
                    if($this->MProduct->enable($product_id))
                        $this->session->set_flashdata('flashdata', '产品上架成功');
                    else
                        $this->session->set_flashdata('flashdata', '产品上架失败');
                    redirect('product/details_admin/'.$product_id);
                }
                if($this->input->post('is_valid') == '0')
                {
                    if($this->MProduct->disable($product_id))
                        $this->session->set_flashdata('flashdata', '产品下架成功');
                    else
                        $this->session->set_flashdata('flashdata', '产品下架失败');
                    redirect('product/details_admin/'.$product_id);
                }*/
            }

        }
        $this->load->view('templates/header', $data);
        if($is_trial)
            $this->load->view('trial_product/details_admin', $data);
        else
            $this->load->view('product/details_admin', $data);
    }

    public function details($product_id)
    {
        if($this->session->userdata('role') != 'user')
            exit('You are not admin.');
        $is_trial = false;
        if(@$_GET['is_trial'] == 'true')
            $is_trial = true;
        $data = array();
        $data['v'] = $this->MProduct->objGetProductInfo($product_id);
        if($data['v']->is_valid == 'f')
            exit('The product is invalid');
        $this->load->view('templates/header_user', $data);
        if($is_trial)
            $this->load->view('trial_product/details', $data);
        else
            $this->load->view('product/details', $data);
    }

    public function add($error = '')
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $is_trial = false;
        $trial_type = 0;
        if(@$_GET['is_trial'] == 'true') {
            $is_trial = true;
            if(isset($_GET['trial_type']) && intval($_GET['trial_type'] > 0))
                $trial_type = intval($_GET['trial_type']);
            if(isset($_POST['trial_type']) && intval($_POST['trial_type'] > 0))
                $trial_type = intval($_POST['trial_type']);
        }
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
            array(
                'field'  => 'price_normal',
                'label'  => '零售价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field' => 'weight',
                'label' => '总重量',
                'rules'   => 'trim|required|xss_clean|is_natural',
            ),
        );

        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                if($is_trial)
                    $this->load->view('trial_product/add', $data);
                else
                    $this->load->view('product/add', $data);
            }else{
                if(!($this->input->post('price_special') < $this->input->post('price_last_2')
                    && $this->input->post('price_last_2') < $this->input->post('price_last_3')
                    && $this->input->post('price_last_3') < $this->input->post('price_normal'))
                )
                {
                    exit('the price you set are not decreasing');
                }
                $config['upload_path'] = './uploads/';
                $config['file_name'] = uniqid();
                $config['allowed_types'] = 'jpg';
                $config['max_size']	= '500000';

                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('img'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    echo $error['error'];
                    return false;
                }
                else
                {
                    $upload_data = array('upload_data' => $this->upload->data());
                    //$data['avatardir'] = $upload_data['upload_data']['full_path'];
                    $path = $upload_data['upload_data']['file_path'];
                    $fname = $upload_data['upload_data']['file_name'];
                    $this->createThumbs($path, $fname, 100);
                }
                $price_list = array(
                    0 => $this->input->post('price_normal'),
                    1 => $this->input->post('price_special'),
                    2 => $this->input->post('price_last_2'),
                    3 => $this->input->post('price_last_3'),
                );
                $main_data = array(
                    'title' => $this->input->post('title'),
                    'category' => $this->input->post('category'),
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    //'img' => $this->input->post('img'),
                    'img' => $fname,
                    'weight' => $this->input->post('weight'),
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

    public function trial_add($error = '')
    {
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin.');
        $data = array();
        $data['error'] = $error;
        $trial_type = 0;
        if(isset($_GET['trial_type']) && intval($_GET['trial_type']) > 0)
            $trial_type = intval($_GET['trial_type']);
        if(isset($_POST['trial_type']) && intval($_POST['trial_type']) > 0)
            $trial_type = intval($_POST['trial_type']);
        $config = array(
            array(
                'field'   => 'title',
                'label'   => '产品名称',
                //'rules'   => 'trim|required|xss_clean|is_unique[products.title]'
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'price',
                'label'   => '单价',
                'rules'   => 'trim|xss_clean|numeric|required'
            ),
            array(
                'field' => 'weight',
                'label' => '总重量',
                'rules'   => 'trim|required|xss_clean|is_natural',
            ),
        );
        $this->form_validation->set_rules($config);
        if(isset($_POST) && !empty($_POST))
        {
            if ($this->form_validation->run() == FALSE)
            {
                $this->load->view('templates/header', $data);
                $this->load->view('trial_product/add', $data);
            }else{
                $config['upload_path'] = './uploads/';
                $config['file_name'] = uniqid();
                $config['allowed_types'] = 'jpg';
                $config['max_size']	= '500000';
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('img'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    echo $error['error'];
                    return false;
                }
                else
                {
                    $upload_data = array('upload_data' => $this->upload->data());
                    //$data['avatardir'] = $upload_data['upload_data']['full_path'];
                    $path = $upload_data['upload_data']['file_path'];
                    $fname = $upload_data['upload_data']['file_name'];
                    $this->createThumbs($path, $fname, 100);
                }
                $main_data = array(
                    'title' => $this->input->post('title'),
                    'category' => $this->input->post('category'),
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    //'img' => $this->input->post('img'),
                    'img' => $fname,
                    'weight' => $this->input->post('weight'),
                    'is_valid' => $this->input->post('is_valid'),
                    'price' => $this->input->post('price'),
                    'trial_type' => $trial_type,
                );
                $result = $this->MProduct->trial_add($main_data);
                if($result){
                    $this->session->set_flashdata('flashdata', '产品添加成功');
                    redirect('product/trial_add?trial_type='.$trial_type);
                }
                else{
                    $this->session->set_flashdata('flashdata', '产品添加失败');
                    redirect('product/trial_add?trial_type='.$trial_type);
                }
            }
        }else{
            $this->load->view('templates/header', $data);
            $this->load->view('trial_product/add', $data);
        }
    }

    private function __get_search_str($search = '', $price_low = '', $price_high = '', $category = null, $is_trial = false, $trial_type = 0)
    {
        if($is_trial) {
            $where = ' and p.is_trial = true';
            $where .= " and p.trial_type = {$trial_type} ";
        } else {
            $where = ' and p.is_trial = false';
        }
        if($search != '' && $price_low != '' && $price_high != '' && $is_trial == false) {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            pr{$this->level}.price::decimal between {$price_low} and {$price_high} )
                            ) ";
        } elseif($search != '' && $price_low == '' && $price_high == '') {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%') ";
        } elseif($search != '' && $price_low != '' && $price_high == '' && $is_trial == false) {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            (cast(pr{$this->level}.price as decimal) > {$price_low} )
                            ) ";
        } elseif($search != '' && $price_low == '' && $price_high != '' && $is_trial == false) {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            (cast(pr{$this->level}.price as decimal) < {$price_high} )
                            ) ";
        } elseif($search == '' && $price_low != '' && $price_high != '' && $is_trial == false) {
            $where .= " and (cast(pr{$this->level}.price as decimal) between {$price_low} and {$price_high}) ";
        } elseif($search == '' && $price_low != '' && $price_high == '' && $is_trial == false) {
            $where .= " and (cast(pr{$this->level}.price as decimal) > {$price_low} )";
        } elseif($search == '' && $price_low == '' && $price_high != '' && $is_trial == false) {
            $where .= " and (cast(pr{$this->level}.price as decimal) < {$price_high} )";
        } elseif($search != '' && $price_low != '' && $price_high != '' && $is_trial == true) {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            trial_price::decimal between {$price_low} and {$price_high} )
                            ) ";
        } elseif($search != '' && $price_low != '' && $price_high == '' && $is_trial == true) {
            $where .= " and (p.title like '%{$search}%' or p.feature like '%{$search}%' or
                            trial_price::decimal > {$price_low} )
                            ) ";
        } elseif($search == '' && $price_low != '' && $price_high != '' && $is_trial == true) {
            $where .= " and trial_price::decimal between {$price_low} and {$price_high}) ";
        } elseif($search == '' && $price_low != '' && $price_high == '' && $is_trial == true) {
            $where .= " and trial_price::decimal > {$price_low} )";
        } elseif($search == '' && $price_low == '' && $price_high != '' && $is_trial == true) {
            $where .= " and (cast(trial_price as decimal) < {$price_high} )";
        }

        if($category != null) {
            $where .= " and p.category = {$category} ";
        }

        return $where;
    }


    function createThumbs( $path, $fname, $thumbHeight )
    {
        $info = pathinfo($path . $fname);
        // continue only if this is a JPEG image
        if ( strtolower($info['extension']) == 'jpg' ||  strtolower($info['extension']) == 'jpeg' )
        {
            //echo "Creating thumbnail for {$fname} <br />";

            // load image and get image size
            $img = imagecreatefromjpeg( "{$path}{$fname}" );
            $width = imagesx( $img );
            $height = imagesy( $img );

            // calculate thumbnail size
            //$new_width = $thumbWidth;
            //$new_height = floor( $height * ( $thumbWidth / $width ) );
            $new_height = $thumbHeight;
            $new_width = floor($width * ($thumbHeight / $height));

            // create a new temporary image
            $tmp_img = imagecreatetruecolor( $new_width, $new_height );

            // copy and resize old image into new image
            imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

            // save thumbnail into a file
            //imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
            $nname = substr($fname, 0, strpos($fname, ".")) . "_thumb.png";
            imagepng( $tmp_img, "{$path}{$nname}" );
        }
    }
}


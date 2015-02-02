<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {

    public $db;
    public function __construct(){
        parent::__construct();
        $this->load->model('MProduct', 'MProduct');
        $this->load->model('MProfit', 'MProfit');
        $this->load->model('MUser', 'MUser');
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
                'field' =>  'profit_type',
                'label' =>  'Profit Type',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'date_from',
                'label' =>  'Date From',
                'rules' =>  'trim|xss_clean'
            ),
            array(
                'field' =>  'date_to',
                'label' =>  'Date To',
                'rules' =>  'trim|xss_clean'
            ),
        );
        $this->form_validation->set_rules($get_config);
        $profit_type = $this->input->get('profit_type', true);
        $date_from = strtotime($this->input->get('date_from', true));
        $date_to = strtotime($this->input->get('date_to', true));
        $interval = date_diff(new \DateTime($date_from), new \DateTime($date_to), true)->days;
        switch($profit_type)
        {
            case "day":
                $profit_type = "day";
                $config['total_rows'] = $interval->days + 1;
                break;
            case "month":
                $profit_type = "month";
                $config['total_rows'] = $interval->y*12 + $interval->m + 1;
                break;
            case "year":
                $profit_type = "year";
                $config['total_rows'] = $interval->y + 1;;
                break;
            default:
                $profit_type = "";
                break;
        }
        if($this->input->get('profit_type', true) != '' &&
            $date_from != '' && $date_to != ''
        )
        {
            //$search = $this->db->escape_like_str($search);
            $data = array();
            $config['base_url'] = base_url()."profit/listpage/";
            //$where = '';
            //$where .= ' and p.is_valid = true ';
            //$where .= $this->__get_search_str($search, $price_low, $price_high);
            //$config['total_rows'] = $this->MProduct->intGetProductsCount($where);

            $config['per_page'] = 30;
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            $limit = '';
            $limit .= " limit {$config['per_page']} offset {$offset} ";
            $where = '';
            $order = '';
            switch($profit_type)
            {
                case 'day':
                    $data['profits'] = $this->MProfit->objGetProfitListOfDay($where, $order, $limit);
                    break;
                case 'month':
                    $data['profits'] = $this->MProfit->objGetProfitListOfMonth($where, $order, $limit);
                    break;
                case 'year':
                    $data['profits'] = $this->MProfit->objGetProfitListOfYear($where, $order, $limit);
                    break;

                default:
                    break;
            }
            $this->load->view('templates/header', $data);
            $this->load->view('profit/listpage', $data);
        }else{
            $this->session->set_flashdata('flashdata', '参数错误');
            redirect('profit/index');
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
            array(
                'field'  => 'price_normal',
                'label'  => '零售价',
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
                    'properties' => $this->input->post('properties'),
                    'feature' => $this->input->post('feature'),
                    'usage_method' => $this->input->post('usage_method'),
                    'ingredient' => $this->input->post('ingredient'),
                    //'img' => $this->input->post('img'),
                    'img' => $fname,
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


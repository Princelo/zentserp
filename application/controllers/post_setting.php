<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include('application/libraries/MY_Controller.php');
class Post_Setting extends MY_Controller {

    public function __construct(){
        parent::__construct();
        if($this->session->userdata('role') != 'admin')
            exit('You are not the admin');
        $this->load->model('MPost', 'MPost');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data = array();
        $data['rules'] = $this->MPost->objGetRules();

        $this->load->view('templates/header', $data);

        $this->load->view('post_setting/index', $data);
    }

    public function add()
    {
        $config = array(
            array(
                'field'   => 'province_id',
                'label'   => 'Province Id',
                'rules'   => 'trim|xss_clean|required|integer'
            ),
            array(
                'field'   => 'city_id',
                'label'   => 'City Id',
                'rules'   => 'trim|xss_clean|required|integer'
            ),
            array(
                'field'   => 'first_weight',
                'label'   => 'First Weight',
                'rules'   => 'trim|xss_clean|is_natural|required'
            ),
            array(
                'field'   => 'additional_weight',
                'label'   => 'Additional Weight',
                'rules'   => 'trim|xss_clean|is_natural|required'
            ),
            array(
                'field'   => 'first_pay',
                'label'   => 'First Pay',
                'rules'   => 'trim|xss_clean|decimal|required'
            ),
            array(
                'field'   => 'additional_pay',
                'label'   => 'Additional Pay',
                'rules'   => 'trim|xss_clean|decimal|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if($_POST && $_POST != '')
        {
            $main_data = array(
                'province_id' => $this->input->post('province_id'),
                'city_id' => $this->input->post('city_id'),
                'first_weight' => $this->input->post('first_weight'),
                'additional_weight' => $this->input->post('additional_weight'),
                'first_pay' => $this->input->post('first_pay'),
                'additional_pay' => $this->input->post('additional_pay'),
            );
            if($this->MPost->checkIsDuplicate($main_data)) {
                exit('The rule you edited Is Duplicate Or Conflict with another rule exists');
            }
            if($this->MPost->add($main_data))
            {
                $this->session->set_flashdata('flashdata', '修改成功');
                redirect('post_setting/index');
            } else {
                $this->session->set_flashdata('flashdata', '修改失败');
                redirect('post_setting/index');
            }
        }

        $this->load->view('templates/header');

        $this->load->view('post_setting/add');
    }

    public function edit($id)
    {
        $config = array(
            array(
                'field'   => 'province_id',
                'label'   => 'Province Id',
                'rules'   => 'trim|xss_clean|required|integer'
            ),
            array(
                'field'   => 'city_id',
                'label'   => 'City Id',
                'rules'   => 'trim|xss_clean|required|integer'
            ),
            array(
                'field'   => 'first_weight',
                'label'   => 'First Weight',
                'rules'   => 'trim|xss_clean|is_natural|required'
            ),
            array(
                'field'   => 'additional_weight',
                'label'   => 'Additional Weight',
                'rules'   => 'trim|xss_clean|is_natural|required'
            ),
            array(
                'field'   => 'first_pay',
                'label'   => 'First Pay',
                'rules'   => 'trim|xss_clean|decimal|required'
            ),
            array(
                'field'   => 'additional_pay',
                'label'   => 'Additional Pay',
                'rules'   => 'trim|xss_clean|decimal|required'
            ),
        );

        $this->form_validation->set_rules($config);
        if($_POST && $_POST != '')
        {
            $main_data = array(
                'province_id' => $this->input->post('province_id'),
                'city_id' => $this->input->post('city_id'),
                'first_weight' => $this->input->post('first_weight'),
                'additional_weight' => $this->input->post('additional_weight'),
                'first_pay' => $this->input->post('first_pay'),
                'additional_pay' => $this->input->post('additional_pay'),
            );
            if($this->MPost->checkWhetherConflict($main_data, $id)) {
                exit('The rule you edited Is Duplicate Or Conflict with another rule exists');
            }
            if($this->MPost->update($main_data, $id))
            {
                $this->session->set_flashdata('flashdata', '修改成功');
                redirect('post_setting/index');
            }
        }
        $data = $this->MPost->getRuleInfo($id);
        $this->load->view('templates/header', $data);

        $this->load->view('post_setting/edit', $data);
    }

}


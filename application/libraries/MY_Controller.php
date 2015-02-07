<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('MUser', 'MUser');
        if($this->session->userdata('current_user_id') != null && $this->session->userdata('role') == 'user')
        {
            $level = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->session->set_userdata('level', $level);

        }
    }
}

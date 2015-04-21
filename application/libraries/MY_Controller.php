<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        $this->load->model('MUser', 'MUser');
        if($this->session->userdata('current_user_id') != null && $this->session->userdata('role') == 'user')
        {
            $level = $this->MUser->intGetCurrentUserLevel($this->session->userdata('current_user_id'));
            $this->session->set_userdata('level', $level);
        }
    }
}

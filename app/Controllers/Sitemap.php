<?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 class Sitemap extends CI_Controller {
     public function index() {     
           $this->load->database();     
          $query = $this->db->get("locations");     
          $data['pages'] = $query->result();     
          $this->load->view('sitemap', $data); }
     }
}

<?php
class Profile extends CI_Controller {

    function __construct() {
    parent::__construct();
		$this->load->model('user_model');
		$this->load->library('googleplus');
    }

    public function complete(){
      if($this->user_model->is_registered($this->session->email,"Y") == TRUE OR $this->session->email == NULL) {
            redirect(base_url("dashboard"));
      }
        $data['title'] = ucfirst('Complete Profile');
        $this->load->view('dashboard/complete',$data);
        $user = $this->input->post();
        $user = $this->security->xss_clean($user);
          if( $this->input->post('branch') != NULL && $this->input->post('phone') != NULL ){
            // $temp['verify_user'] = $this->user_model->get_user_single_verify($this->session->email);
              // if($this->input->post('accsess_code')==$temp['verify_user']['admission_number']){
                  $user['branch'] = $this->input->post('branch');
                  $user['phone'] = $this->input->post('phone');
                  $user['fullname'] = $this->input->post('fullname');
                  $user['course_duration_from'] = $this->input->post('course_duration_from');
                  $user['course_duration_to'] = $this->input->post('course_duration_to');
                  $user['whyiedc'] = $this->input->post('whyiedc');
                  // $user['admission_number'] = $this->input->post('admission_number');
                  $user['profile_completed'] = '1';
                  $this->user_model->complete_signin($user);
              // }
              // else{
                // $this->session->set_flashdata('fail', 'Your registration is Successfull!!');
                // echo "flag";exit;
                // redirect(base_url("dashboard/complete"));
              // }
              if(isset($_SESSION['back_url']) && strpos($_SESSION['back_url'], 'ico') == false){
                  $link=$_SESSION['back_url'];
                  unset($_SESSION['back_url']);
                  redirect($link);
              }else{
                  $this->session->set_flashdata('success', 'Your registration is Successfull!!');
                  redirect(base_url("dashboard"));
              }

          }
    }

    public function user_profile(){
        if(isset($_SESSION['email'])){
            $data['userinfo']=$this->user_model->get_user_single($this->session->email);
            $data['profile_pic'] = $this->session->profile_pic;
            $data['link'] = $this->session->link;
            $data['loginURL'] = $this->googleplus->loginURL();
            $data['verify_user'] = $this->user_model->get_user_single_verify($this->session->email);
              if($data['verify_user']['profile_completed']==1){
                $this->session->set_flashdata('success', 'Login Successfull!!');
                // $this->load->view('dashboard/myprofile',$data);
                $this->load->view('dashboard/sidebar',$data);
                $this->load->view('dashboard/header',$data);
                $this->load->view('dashboard/home',$data);
                $this->load->view('dashboard/footer',$data);

              }
              else{
                $data['title'] = ucfirst('Complete Profile');
                $this->load->view('dashboard/complete',$data);
              }
          }
        else{
            // set the expiration date to one hour ago
            setcookie("redir", "myprofile", time() + 3600);
            $data['loginURL']=$this->googleplus->loginURL();
            header('Location: '.$data['loginURL']);
            exit('');
        }
    }


}

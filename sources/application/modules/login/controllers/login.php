<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        initialize();
    }

    public function index()
    {
        if(is_logged(false)) redirect(base_url());
        set_theme('title','Login');
        set_theme('content', load_module('login','login'));
        set_theme('bodyClass','login bg-login printable');
        set_theme('pluginsJS',load_javascript(array('user-pages','initialize-login')),false);
        load_template();
    }

    public function enter()
    {
        $this->form_validation->set_rules('loginemail', 'Login / E-mail', 'required|min_length[4]');
        $this->form_validation->set_rules('senha', 'Senha', 'required');
        if($this->form_validation->run())
        {
            $login = $this->input->post('loginemail',true);
            $senha = md5($this->input->post('senha',true));
            $result = $this->login_mdl->login($login,$senha);
            if($result == 'loggedLogin')
            {
                $query = $this->login_mdl->get_user($login)->row();
                $data = array(
                    'user_id'     => $query->id,
                    'user_name'   => $query->nome,
                    'user_login'  => $query->login,
                    'user_email'  => $query->email,
                    'user_admin'  => $query->admin,
                    'user_status' => 'logged'
                );
                $this->session->set_userdata($data);
                echo $this->session->userdata('user_status');
            }
            else
            {
                if($result == 'loggedEmail')
                {
                    $query = $this->login_mdl->get_user($login,'email')->row();
                    $data = array(
                        'user_id'     => $query->id,
                        'user_name'   => $query->nome,
                        'user_login'  => $query->login,
                        'user_email'  => $query->email,
                        'user_admin'  => $query->admin,
                        'user_status' => 'logged'
                    );
                    $this->session->set_userdata($data);
                    echo $this->session->userdata('user_status');
                }else{
                    $this->session->sess_destroy();
                    echo $result;
                }
            }
        }
        else
        {
            $this->session->sess_destroy();
            echo 'error';
        }
    }

    public function recovery()
    {
        $this->form_validation->set_rules('forgot-email','E-mail','trim|required|valid_email|strtolower');
        if($this->form_validation->run() == true)
        {
            $email = $this->input->post('email');
            $query = $this->login_mdl->get_user($email,'email');
            if($query->num_rows() == 1)
            {
                $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvxwyz123456789'),6);
                $mensagem = '<p>Você solicitou uma nova senha no Sistema Vortex.</p><p>Su nova senha de acesso é: '.. $new_password. '</p><p>Qualquer dúvida entre em contato através de nosso canais de comunicação.</p>';
                if($this->vortex->send_mail($email,'Nova Senha de Acesso',$mensagem))
                {

                }else
                {

                }
            }
            else
            {
                echo false;
            }
        }
    }

}

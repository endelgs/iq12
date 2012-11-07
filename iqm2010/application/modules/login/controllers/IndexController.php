<?php

require_once(APPLICATION_PATH . '/models/Users.php');
require_once(APPLICATION_PATH . '/../library/componentes/Mail.php');

class Login_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_table::getDefaultAdapter());

        //Se o usuario já estiver logado e for para a tela de login, será redirecionado
        //o zend_auth só tem uma instancia por sessão então pego a instância dessa sessão
        //e vejo se ela já tem uma identidade, se o cara ja tá logado

        if ($this->getRequest()->isPost()) {

            if ($_REQUEST['login'] != "" && $_REQUEST['pass'] != "") {
                $password = sha1($_REQUEST['pass']);
                $authAdapter = $this->getAuthAdapter();

                $authAdapter
                        ->setIdentity($_REQUEST['login'])
                        ->setCredential($password)
                ;

                //Instance of UNIQUE auth -> is authenticated by adaptaer
                if (Zend_Auth::getInstance()->authenticate($authAdapter)->isValid()) {
                    //Instance of UNIQUE auth -> get session writer to record authentication rowset
                    Zend_Auth::getInstance()->getStorage()->write($authAdapter->getResultRowObject());
                    $clsUser = new Application_Models_Users();
                    $arrUser = $clsUser->CRUDreadByUser($_REQUEST['login']);
                    $_SESSION['tipo'] = $arrUser[0]['acesso'];
                    $this->_redirect('dashboard');
                } else {
                    $this->view->errorMessage = array('text' => 'Login ou Senha inválidos.', 'level' => 1);
                    $this->view->errorMessage = '<div class="notice">Login ou Senha inválidos.</div>';
                }
            } else {
                if ($_REQUEST['login'] != "" && $_REQUEST['email'] != "") {
                    $this->esqueciMinhaSenha($_REQUEST['login'], $_REQUEST['email']);
                } else {
                    $this->view->errorMessage = array('text' => 'Ambos os campo devem ser preenchidos.', 'level' => 1);
                    $this->view->errorMessage = '<div class="notice">Ambos os campo devem ser preenchidos.</div>';
                }
            }
        }
    }

    public function logoutAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            Zend_Auth::getInstance()->clearIdentity();
            $this->_redirect('login');
        }
    }

    protected function getAuthAdapter() {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_table::getDefaultAdapter());
        $authAdapter->setTableName('v_users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');


        return $authAdapter;
    }

    protected function esqueciMinhaSenha($login, $email) {
        if ($login != "" && $email != "") {
            $clsUser = new Application_Models_Users();
            $arrUser = $clsUser->CRUDreadByUser($login);
            $user = $login;


            if ($deletado == 0) {

                if ($user == $arrUser[0]['username']) {
                    if ($email == $arrUser[0]['email']) {
                        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),0,6);
                        $paraSalvar = md5($random);
                        $clsUser->CRUDupdate(array('password'=>$paraSalvar), $arrUser[0]['id']);
                        $this->enviaEmail($email, $user, $random);
                    } else {
                        $this->view->errorMessage = array('text' => 'E-mail Incorreto.', 'level' => 1);
                        $this->view->errorMessage = '<div class="notice">Email Incorreto.</div>';
                    }
                } else {
                    $this->view->errorMessage = array('text' => 'Usuário Incorreto.', 'level' => 1);
                    $this->view->errorMessage = '<div class="notice">Usuário Incorreto.</div>';
                }
            } else {
                $this->view->errorMessage = array('text' => 'Ambos os campo devem ser preenchidos.', 'level' => 1);
                $this->view->errorMessage = '<div class="notice">Ambos os campo devem ser preenchidos.</div>';
            }
        } else {
            $this->view->errorMessage = array('text' => 'Ambos os campo devem ser preenchidos.', 'level' => 1);
            $this->view->errorMessage = '<div class="notice">Ambos os campo devem ser preenchidos.</div>';
        }
    }

    protected function enviaEmail($email, $user, $senha) {

        $to = $email;
        $subject = "Recuperação de senha";
        $html = "<html>
		   <body>
         
                        Seu nome de usuário é: $user.
                              <br/>
                        Sua nova senha é: $senha.
                     
		  </body>
                </html>";
//        $headers = "Content-type: text/html; charset=iso-8859-1rn";

//        if (mail($to, $subject, $html, $headers)) {
//            echo "Email enviado com sucesso !";
//        } else {
//            echo "Ocorreu um erro durante o envio do email.";
//        }
        
        Mail::enviaEmail($to, $html, $subject);
    }

}
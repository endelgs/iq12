<?php
require_once(APPLICATION_PATH.'/models/Users.php');
class Gerenciar_UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{


		$user= Zend_Auth::getInstance()->getIdentity();
		$usuarioAtual=$user->username;


		$data = new Application_Models_Users();
			
		// UPDATE
		$editar='admin';

		if($_REQUEST['editar']=='nao')
		$editar='user';
			

		//var_dump($usuarioAtual);


		if ($this->getRequest()->isPost() && $this->_request->getParam("id") != 0) {

			//caso a senha seja <> vazio e os dois campos =s
			if (($this->_request->getParam("senha")!="")
			&&($this->_request->getParam("senha2")!="")){
					
					
				if ($this->_request->getParam("senha")==$this->_request->getParam("senha2"))
				{

					$params = array(
              		'password' =>  $this->_request->getParam("senha"),
              		'email' => $this->_request->getParam("email"),
					'acesso'=> $editar
					);

					if($usuarioAtual == $this->_request->getParam("username"))
					{

						$params = array(
              		'password' =>  $this->_request->getParam("senha"),
              		'email' => $this->_request->getParam("email"),
						);
						$alert = ' Mas você não pode editar suas proprias permissões.';
					}




					$data->CRUDupdate($params, $this->_request->getParam("id"));
					$this->view->alert = 'Alterações gravadas com sucesso!'.$alert;
				}
				else
				$this->view->alert = 'Senhas estão vazias ou não estão iguais.';
			}
			else //senao
			{
				$params = array(
              		'email' => $this->_request->getParam("email"),
					'acesso'=> $editar
					
				);

					

				if($usuarioAtual == $this->_request->getParam("username"))
				{

					$params = array(
              		 'email' => $this->_request->getParam("email"),
					);
					$alert = ' Mas você não pode editar suas proprias permissões.';
				}

				$data->CRUDupdate($params, $this->_request->getParam("id"));
				$this->view->alert = 'Dados alterado com sucesso. '.$alert;
			}


		}
		else if($this->getRequest()->isPost())
		{

			$espaco=substr_count($this->_request->getParam("user"),' ');

			if($espaco>0)
			{
				$this->view->alert = 'Não use espaços para nome de usuário.';
			}else
			// CREATE caso as duas seja <> vazio e iguais
			if (($this->_request->getParam("senha")!="")
			&& ($this->_request->getParam("senha2")!="")){

				if ($this->_request->getParam("senha")==$this->_request->getParam("senha2"))
				{
					$params = array(
              'username'    => $this->_request->getParam("user"),
              'password' =>  $this->_request->getParam("senha"),
              'email' => $this->_request->getParam("email"),
					'acesso'=> $editar
					);

					if($data->CRUDcreate($params))
					$this->view->alert = 'Usuário adicionado com sucesso.';
					else
					$this->view->alert = 'Este usuário já existe.';
				}
				else
				$this->view->alert = 'Senhas estão vazias ou não conferem.';
			}

		}
			
		// READ
		$this->view->data = $data->CRUDread();
	}
	function deleteAction()
	{

		$data = new Application_Models_Users();
		
		$user= Zend_Auth::getInstance()->getIdentity();
		$usuarioAtual=$user->username;
		
		if($usuarioAtual != $this->_request->getParam("username"))
		{
			$data->CRUDdelete($this->_request->getParam("id"));
			$this->_redirect('/gerenciar/user');
		}else {
			
			$this->_redirect('/gerenciar/user');
		}
	}
}
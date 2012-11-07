<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	//public function _initMyInit()

	//	public function _initMyInit()
	//	{
	//
	//
	//
	//	}

	public function __construct($application)
	{
		parent::__construct($application);
		Zend_Session::start();

		Zend_Loader::loadClass('Zend_Controller_Front');
		Zend_Loader::loadClass('Zend_Controller_Action');
		Zend_Loader::loadClass('Zend_Config_Ini');
		Zend_Loader::loadClass('Zend_Registry');
		Zend_Loader::loadClass('Zend_Db');
		Zend_Loader::loadClass('Zend_Db_Table');
		Zend_Loader::loadClass('Zend_Form');
		Zend_Loader::loadClass('Zend_Session');
		Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
		Zend_Loader::loadClass('Zend_Auth');
		Zend_Loader::loadClass('Zend_View');
		Zend_Loader::loadClass('Zend_Controller_Action_Helper_ViewRenderer');
		Zend_Loader::loadClass('Zend_View_Helper_Abstract');
		Zend_Loader::loadClass('Zend_Controller_Action_HelperBroker');
		Zend_Loader::loadClass('Zend_Layout');

		Zend_loader::loadClass('Application_Models_Users', APPLICATION_PATH);
		Zend_loader::loadClass('Application_Models_LogsDeAcesso', APPLICATION_PATH);
		Zend_loader::loadClass('Zend_Controller_Request_Http');
		Zend_loader::loadClass('Application_Models_Pessoas', APPLICATION_PATH);
		Zend_loader::loadClass('Application_Models_Graduacoes', APPLICATION_PATH);
		Zend_loader::loadClass('Acesso', APPLICATION_PATH);



		$this->verificaPermissao();
	}


	public function verificaPermissao()
	{
		$zendAuth=Zend_Auth::getInstance()->getIdentity();
		$user=$_SESSION['tipo'];
			
		if($zendAuth!="")
		{

			if($user!='admin')
			{

				if($_POST)
				{
					$_POST=array();
				}
			}

			elseif($user=='admin')
			{
				$acesso = new Acesso();
				$acesso->logs();
			}
		}

		$baseurl=$_SERVER['PHP_SELF'];
		$baseurl=str_replace('/index.php','', $baseurl);
		$url=$_SERVER['REQUEST_URI'];
		$final=str_replace($baseurl,'', $url);
		$final=explode('/',$final);
		$_modules=$final[1];
		$_controller=$final[2];
		$_action=$final[3];
		$tipoDeUser=$_SESSION['tipo'];

		if($tipoDeUser=='user'  && ($_modules!="procurar" &&
		$_modules!="materias" && $_modules!="relatorios" && $_modules!="login"))
		{
			$_POST=array();
			unset($_POST);
		}
	}





	protected static function setupAcl()
	{
		//self::$frontController->registerPlugin(new GSD_Controller_Plugin_Acl());

		$acl = new Zend_Acl();

		$acl->addRole(new Zend_Acl_Role('user'));
		$acl->addRole(new Zend_Acl_Role('member'), 'user');
		$acl->addRole(new Zend_Acl_Role('admin'), 'member');

		$acl->add(new Zend_Acl_Resource('membro'));
		$acl->add(new Zend_Acl_Resource('index'));

		//	   $acl->add(new Zend_Acl_Resource('busca'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('register'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('manutencao'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('exibedados'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('exibeform'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('save-pdf'), 'membro');
		//	   $acl->add(new Zend_Acl_Resource('form-mail'), 'index');
		//
		//	   $acl->allow('admin', array('membro', 'index'));
		//	   $acl->allow('member', 'membro', array('busca', 'exibedados', 'exibeform'));
		//	   $acl->allow('member', 'index');
		//	   $acl->allow('user', 'membro', array('busca', 'exibedados', 'exibeform'));
		//
		//	   $acl->deny('member', 'membro', array('manutencao', 'register'));
		//	   $acl->deny('user', 'membro', array('manutencao', 'register', 'save-pdf'));
		//	   $acl->deny('user', 'index');
		//
		//	   /** Registra a variável $acl */
		Zend_Registry::set('acl', $acl);
	}
}
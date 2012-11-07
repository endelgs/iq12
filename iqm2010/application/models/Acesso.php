<?php
DEFINE('CONFIGURACOES','application.ini');
class Acesso
{
    
	protected $_controller;
	protected $_modules;
	protected $_action;

	protected $nomePessoa ;
	protected $tipo;

	public function __construct()
	{
		$url=$_SERVER['REQUEST_URI'];
		$baseurl=$_SERVER['PHP_SELF'];
		$baseurl=str_replace('/index.php','', $baseurl);

		$pos = strpos($url, '?');

		if($pos>0)
		$url=substr($url, 0,$pos);


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

		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/'.CONFIGURACOES);
		$title  = $config->appName;
		$x=$config->toArray();
		$y=$x['development']['resources']['db']['params'];
		//$DB = new Zend_Db_Adapter_Pdo_Mysql($x);
		$adapter=$x['development']['resources']['db']['adapter'];
		
		$DB = Zend_Db::factory($adapter, $y);
		$DB->setFetchMode(Zend_Db::FETCH_OBJ);

		Zend_Registry::set('db',$DB);

		Zend_loader::loadClass('Application_Models_Pessoas', APPLICATION_PATH);
		Zend_loader::loadClass('Application_Models_Graduacoes', APPLICATION_PATH);

		$final=str_replace($baseurl,'', $url);

		$final=explode('/',$final);


		$this->_modules=$final[1];
		$this->_controller=$final[2];
		$this->_action=$final[3];
		$params=array();
		forEach($final as $index=>$f)
		{
			if($index>=4 && $index%2==0)
			$params[$f]=$final[$index+1];

		}

		$pessoa = new Application_Models_Pessoas('db');
		$pos= new Application_Models_Graduacoes('db');

		$id_pessoa=$params['id_pessoa'];

		if($params['id_pos_graduacao']!="" && $id_pessoa=="") {
			$pos=$pos->getPessoaByPos($params['id_pos_graduacao']);
			$id_pessoa=$pos[0]['id_pessoa'];
		}

		if($id_pessoa!="") {
			$pessoa=$pessoa->getPessoa($id_pessoa);
			$this->nomePessoa = ' de '.$pessoa['nome'];
		}

		if($params['tipo']!="")
		{
			if($params['tipo']==5)
			$this->tipo=' em Doutorado';
			elseif($params['tipo']==6)
			$this->tipo=' em Docente';
			else
			$this->tipo=' em Mestrado';
		}

	}


	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		$this->getResponse()->appendBody("<p>postDispatch() called</p>\n");
	}

	public function salvaLogs()
	{
		$user= Zend_Auth::getInstance()->getIdentity();
		$usuarioAtual=$user->username;

		if($this->_modules!="procurar")
		{
			$logs = new Application_Models_LogsDeAcesso('db');
			$parans=array('data'=>date('Y-m-d H:i:s'),
			      'user'=>$usuarioAtual,
			    'descricao'=>$this->descricao()
			);


			$logs->CRUDcreate($parans);
		}

	}

	public function logs()
	{

		if($_POST || $this->_action=='delete')
		{

			$this->salvaLogs();
		}
	}


	public function descricao()
	{
		$descricao = array ( 'pessoas'=>array('geral'=>'Dados Pessoais '.$this->nomePessoa ),
					 'posgraduacao'=>array('inscricoes'=>'Inscrição '.$this->nomePessoa .$this->tipo,
										   'exames'=>'Exames'.$this->nomePessoa .$this->tipo,
											'ingressos'=>'Ingresso '.$this->nomePessoa .$this->tipo,
											'atividades'=>'Atividades '.$this->nomePessoa .$this->tipo,
											'qualificacoes'=>'Dados de Qualificação '.$this->nomePessoa .$this->tipo,
											'defesas'=>'Dados de Defesa '.$this->nomePessoa .$this->tipo,
											'disciplinas'=>'Disciplinasa '.$this->nomePessoa .$this->tipo,
											'linguas'=>'Linguas '.$this->nomePessoa .$this->tipo,
											'publicacoes'=>'Publicações '.$this->nomePessoa .$this->tipo,
											'premios'=>'Prêmios '.$this->nomePessoa .$this->tipo,
											'frequencia'=>'Frequência '.$this->nomePessoa .$this->tipo,
											 'bolsas'=>'Bolsas '.$this->nomePessoa .$this->tipo,
		),
					   'docente'=>array('index'=>'Dados de Docente '.$this->nomePessoa ,
										'bolsa'=>'Bolsas '.$this->nomePessoa .' em docentes'
										),
					   'gerenciar'=>array('areas-de-concentracao' => 'Gerenciamento de área de concentração',
										  'cidades-da-prova' => 'Gerenciamento de cidades de prova',
										  'cursos' => 'Gerenciamento de cursos',
										  'departamentos' => 'Gerenciamento de departamentos',
										  'instituicoes' => 'Gerenciamento de Instituições',
										  'linhas-de-pesquisa' => 'Gerenciamento de Linhas de Pesquisa',
										  'periodos' => 'Gerenciamento de Períodos',
										  'user' => 'Gerenciamento de Usuários',
										  'atividades' => 'Gerenciamento de Atividades',
										  'detalhes-do-desligado' => 'Gerenciamento de Detalhes do desligamento',
						    			  'disciplinas' => 'Gerenciamento de disciplinas',	
										   'linguas' => 'Gerenciamento de Línguas',		
										   'oferecimentosDisciplinas' => 'Gerenciamento de Oferecimento de Disciplinas',
										  'publicacoes' => 'Gerenciamento de Tipo de publicação',
										  'tiposProjetos' => 'Gerenciamento de Tipos de Projetos de Dissertação',
										  'tipos-de-trancamento' => 'Gerenciamento de Tipos de Trancamento',
										   'aposentadorias' => 'Gerenciamento de tipo aposentadorias de docentes',
										  'atribuicoes' => 'Gerenciamento de tipo Atribuições de Banca',
										  'tipodocente' => 'Gerenciamento de tipo docente',		
										  'agencias' => 'Gerenciamento de Agências',
										  'motivos-de-cancelamento' => 'Gerenciamento de Motivos de Cancelamento',	
										  'motivo-suspensao-bolsa' => 'Gerenciamento de Motivos de Suspensão de Bolsa',		
										  'nivelbolsa' => 'Gerenciamento de Nível de bolsa produtividade',				
										),
			 'materias'=>array('inserir'  => 'Inserção de turma', 
							   'procurar' => 'Edição de turma'),
										);



										$msg=$descricao[$this->_modules][$this->_controller];
										if($msg=="")
										$msg="Ação em ".$this->_modules." / ".$this->_controller;


										if($this->_action=='delete')
										{
											$msg='Ação de apagar em '.$msg;
										}elseif($this->nomePessoa=="" && ($this->_modules=="pessoas" || $this->_modules=="posgraduacao"|| $this->_modules=="docente"))
										{
											$msg='Inserção em '.$msg;
										}

										if ($this->_modules=="login")
										{
											$msg= 'Logou-se';
										}
											

										return $msg;
											
	}

}
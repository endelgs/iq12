<?php
class Application_Models_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';

	function CRUDcreate($array)
	{
		if(!$this->userExist($array['username']))
		{
			$array['password']=$this->encrypt($array['password']);

			//		var_dump($array);
			//		exit;
			try{
				return($this->insert($array));
			}catch (Exception $e){

				if($e->getCode()=="23000")
				return false;

				return true;

			}
		}
		return false;

	}

	public function userExist($username)
	{
		$usuarios=$this->fetchAll($this->select()->where("deletado=0 AND username='$username'")->order('username'))->toArray();

		if(count($usuarios)>0)
		{
			return true;
		}

		return false;
	}

	function encrypt($string)
	{

		return sha1($string);
			
	}

	function CRUDread()
	{
		return($this->fetchAll($this->select()->where('deletado=0')->order('username'))->toArray());
	}

	function CRUDupdate($array, $id)
	{
		if(!empty($array['password']))
		$array['password']=$this->encrypt($array['password']);

		$this->update($array, 'id='.$id);
	}

	function CRUDdelete ($id)
	{
		$this->update(array('deletado'=>'1'), 'id='.$id);
	}
	
	function CRUDreadByUser($username)
	{
		return($this->fetchAll($this->select()->where(" deletado=0 AND username='$username' "))->toArray());
	}
	
	
	
}
?>
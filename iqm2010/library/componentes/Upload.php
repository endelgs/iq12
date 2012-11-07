<?php
// o form tem que ter enctype="multipart/form-data"
class Upload
{
	public $name;
	public $id;
	public $local;

	public function __construct($name="file",$local="", $id="file")
	{
		$this->name=$name;

		if(($name!="file") && ($id=="file"))
		$this->id=$name;
		else
		$this->id=$id;

		$this->local=$local;
	}

	public function getInputFile()
	{
		$html='<input type="file" name="'.$this->name.'" id="'.$this->id.'" />';

		return $html;
	}

	public function criaArquivo($FILES, $novonome, $local="")
	{
		if ($local=="")
		$local=$this->local;

		$target_path=$local;
		//$target_path = "uploads/";

		//$target_path = $target_path . basename( $_FILES[$this->name]['name']);

		$ext=explode('.',basename( $FILES[$this->name]['name']));
		$ext=$ext[count($ext)-1];

		$target_path.=$novonome.'.'.$ext;

		if(move_uploaded_file($FILES[$this->name]['tmp_name'], $target_path)) {
			echo "O upload do ".  basename( $FILES[$this->name]['name']).
				" foi bem sucedido.";
		} else{
			echo "Houve algum erro, tente novamente!";
		}

		return $novonome.'.'.$ext;
	}
}
?>
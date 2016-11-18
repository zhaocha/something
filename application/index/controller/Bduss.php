<?php
namespace app\index\controller;

use think\Controller;

class Bduss extends Controller
{
	public function index()
	{
		return $this->fetch('index');
	}

	public function update()
	{
		return $this->fetch('update');
	}

}
?>
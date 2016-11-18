<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
	public function index()
	{
		return $this->fetch();
	}

	public function doPost()
	{
		$username = htmlspecialchars(input('post.username'));
		$times = intval(input('post.times'));
		$res = User::base()->checkUserExists($username);
		if(!isset($res[0]))
		{
			return $data = [
				'errcode' => 1,
				'msg' => '用户不存在'
			];
		}else if(!($times === -1 || $times === 10 || $times === 30 ))
		{
			return $data = [
				'errcode' => 1,
				'msg' => $times
			];
		}
		$res = $this->selectGoods($username, $times);
		return $res;
		
	}

	private function selectGoods($username = '', $times = 10)
	{
		$res = User::base()->selectGoods($username, $times);
		return $res;
	}
}
?>
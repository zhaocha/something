<?php
namespace app\index\controller;

use think\Controller;
use my\Curl;

class User extends Controller
{
	function index()
	{
		return $this->fetch();
	}

	// 实例化Curl类
	private static function objCurl()
	{
		$obj = new Curl();
		return $obj;
	}

	public function doPost()
	{
		$bduss = htmlspecialchars(input('post.bduss'));
		$method = htmlspecialchars(input('post.type'));
		if(!empty($bduss))
		{
			$res = $this->getUserInfo($bduss);
			if($res['errcode'] === 0)
			{
				$username = htmlspecialchars($res['username']);
				if($method === 'add') return $this->addBduss($username, $bduss);
				else if($method === 'update') return $this->updateBduss($username, $bduss);
				else return $data = [
							'errcode' => 2,
							'msg' => '操作有误'
						];
			}else return $data = [
							'errcode' => 1,
							'msg' => '操作失败，请输入正确的BDUSS'
						];
		}else return $data = [
							'errcode' => 1,
							'msg' => 'invaild bduss'
						];
	}

	// 实例化Base类
	public static function base()
	{
		$obj = \think\Loader::model('Base');
		return $obj;
	}

	public function addBduss($username = '', $bduss = '')
	{
		$res = $this->checkUserExists($username);
		if($res === false)
		{
			return $data = [
					'errcode' => 1,
					'msg' => "用户 【{$username}】已经存在"
				];
			// return false;
		}else 
		{
			$this->addUserInfo($username, $bduss);
			return $data = [
					'errcode' => 0,
					'msg' => '用户：【'.$username.'】添加成功'
				];
			// return true;
		}
	}

	public function updateBduss($username = '', $bduss = '')
	{
		$res = $this->checkUserExists($username);
		if($res === true)
		{
			return $data = [
					'errcode' => 1,
					'msg' => "用户 【{$username}】还未添加，请先添加用户"
				];
		}else 
		{
			$this->updateBduss($bduss);
			return $data = [
					'errcode' => 0,
					'msg' => '用户：【'.$username.'】BDUSS更新成功'
				];
		}
	}

	public function selectUserInfo()
	{
		$res = self::base()->selectUserInfo();
		dump($res);
	}

	public function checkUserExists($username = '')
	{
		$res = self::base()->checkUserExists($username);
		if(empty($res))
		{
			return true;
		}else return false;
		
	}

	private function addUserInfo($username = '', $bduss = '')
	{
		$res = self::base()->addUserInfo($username, $bduss);
	}

	private function updateUserInfo($username, $bduss = '')
	{
		self::base()->updateUserInfo($username, $bduss);
	}
	// 获取用户信息
	private function getUserInfo($bduss = '')
	{
		$cookie = 'Cookie:'.$bduss;
		$header = array($cookie);
		$content =  $this->objCurl()->send(config('__lotteryUrl__'), $header);
		if(empty($content))
		{
			return $data = [
					'errcode' => 2,
					'message' => 'fail to get usernfo'
				];
		}
		$pattern = '/user-name\">(.+?)</';
		preg_match_all($pattern, $content, $res);
		if(empty($res[1][0]))
		{
			return $data = [
					'errcode' => 3,
					'message' => 'fail to preg_match userinfo'
				];
		}
		$username = $res[1][0];
		$username = mb_convert_encoding($username, 'utf-8', 'gbk');
		return $data = [
				'errcode' => 0,
				'username' => $username
			];
	}

}
?>
<?php
namespace app\index\model;

use think\Model;

class Base extends Model
{
	public function selectUserInfo()
	{
		$sql = "SELECT `username`,`bduss` FROM `baidu_userinfo` ";
		//$sql = "select $values from $dbTable";
		//$link->query($sql);
		$res = $this->query($sql);
		return $res;
	}

	function checkUserExists($username = '')
	{
		$sql = "SELECT `id` FROM `baidu_userinfo` WHERE `username`= ? LIMIT 1";
		$res = $this->query($sql, [$username]);
		// $row = $res->num_rows; 原生代码时用来检查数据是否存在
		return $res;
	}

	function addUserInfo($username = '', $bduss = '')
	{
		$sql = "INSERT INTO baidu_userinfo(`username`,`bduss`) VALUES(?,?)";
		$this -> query($sql, [$username, $bduss]);
	}

	function updateUserInfo($username = '', $bduss = '')
	{
		$sql = "UPDATE baidu_userinfo SET `bduss` = ? WHERE `username` = ?";
		$this->query($sql, [$bduss, $username]);
	}

	function addLotteryGoods($username = '', $goodsName = '')
	{
		$sql = "INSERT INTO zhidao_lotterygoods(`username`,`goodsname`) VALUES(?,?)";
		$this->query($sql, [$username, $goodsname]);
	}

	function selectGoods($username = '', $times = 10)
	{
		$sql = "SELECT `goodsname`,`lotterytime` FROM `zhidao_lotterygoods` WHERE `username`=? ORDER BY `lotterytime` DESC LIMIT 0,? ";
		$res = $this->query($sql, [$username, $times]);
		return $res;
	}

}

?>
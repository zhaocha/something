<?php
namespace app\index\controller;

use think\Controller;
use my\Curl;

class Zhidaolottery extends Controller
{
	// const __lotteryUrl__ = 'https://zhidao.baidu.com/shop/lottery';

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

	// 执行抽奖
	public function freeLottery()
	{
		$userInfo = User::base()->selectUserInfo();
		$userNum = count($userInfo);
		for($i = 0; $i < $userNum; $i++)
		{
			$username = $userInfo[$i]['username'];
			$url = $this->getUrl($userInfo[$i]['bduss']);
			if(empty($url)){
				die('bduss 已过期');
			}
			$r = $this->lottery($url, $userInfo[$i]['bduss'], $i % 4);
			$goodsName = $r['goodsName'];
			if(!empty($goodsName))
			{
				User::base()->addLotteryGoods($username, $goodsName);
			}
			// echo "用户：{$username}    奖品：{$goodsName}</br>";
			sleep(2);
			flush();
		}
	}

	// 得到抽奖url
	private function getUrl($bduss = '')
	{
		$luckyToken = $this->getLuckyToken($bduss);
		if(empty($luckyToken))
		{
			return;
		}
		$url = 'https://zhidao.baidu.com/shop/submit/lottery?type=0&token='.$luckyToken;
		return $url;
	}

	// 抽奖并获取结果
	private function lottery($url = '', $bduss = '', $i = 0)
	{
		$agent = [
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393',
			'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36',
			'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0',
			'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36'
		];
		
		$header = array(
			'X-Requested-With:XMLHttpRequest',
			'X-ik-ssl:1',
			'User-Agent:'.$agent[$i],
			'Referer:https://zhidao.baidu.com/shop/lottery',
			'Cookie:'.$bduss
		);

		$content = $this->objCurl()->send($url, $header);
		$content = json_decode($content, true);
		if(!empty($content['errmsg']) && $content['errmsg'] === '不能免费抽奖')
		{
			$finished = true;
			return $finished;
		}else if(!empty($content['data']['userInfo']['freeChance']) && !empty($content['data']['prizeList'][0]['goodsName']))
		{
			$user = [ 
				'freeChance' => $content['data']['userInfo']['freeChance'],
				'goodsName' => $content['data']['prizeList'][0]['goodsName']
			];
			return $user;
		}
	}

	// 获取LuckyToken
	private function getLuckyToken($bduss = ''){
		$cookie = 'Cookie:'.$bduss;
		$header = array($cookie);
		$content = $this->objCurl()->send(config('__lotteryUrl__'), $header);
		if(empty($content)){
			return $data = [
					'error' => 2,
					'msg' => 'fail to get LuckyToken'
				];
		}
		$pattern = '/\'luckyToken\', \'(\w+)\'/';
		preg_match_all($pattern, $content, $res);
		if(empty($res[1][0])){
			return $data = [
					'error' => 2,
					'msg' => 'fail to get LuckyToken'
				];
		}
		$luckyToken = $res[1][0];
		return $luckyToken;
	}

	
}
?>
<?php

require '../../php/scpos.php';

function getArr($sign = false)
{
	return array(
		'via' => isset($_SERVER['HTTP_VIA']) ? $_SERVER['HTTP_VIA'] : '',
		'fact' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
		'poss' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '',
		'info' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
		'sign' => $sign
	);
}
if (isset($_GET['p'][1])) {
	$sign = substr($get = $_GET['p'], 1);
	if ($get[0] === '0') {
		$hdr = isset($_GET['u']) ? $_GET['u'] : 'https://api.ghser.com/random/api.php';
		$hdr = substr_compare($hdr, 'http', 0, 4) === 0 ? 'Location: ' . $hdr : 'Location: http://' . $hdr;
		header($hdr);
		ScpoPHP::sql_insert(getArr($sign), 'sc_chaip_data');
		exit();
	}
	if (empty(ScpoPHP::sql_select("`name`='$sign'", '*', 'sc_chaip_sign'))) {
		$aim = 'nohok';
		goto ghtml_start;
	}
	$sign = md5($sign);
	$data = ScpoPHP::sql_select("`sign`='$sign'", '*', 'sc_chaip_data');
	$aim = 'check';
	goto ghtml_start;
} else if ($qry = $_SERVER['QUERY_STRING']) {
	header('Content-type: application/json');
	if (substr_compare($qry, 'self', 0, 4) === 0) {
		if (substr_compare($qry, 'intro', -5, 5) === 0) {
			$url = "http://{$_SERVER['HTTP_HOST']}/chaip/";
			$addr = array(true, "{$url}self/", "{$url}?self");
			$desc = '
				通过此API，你可以简单快速的获取到自己的公网IP、代理IP、真实IP和客户端信息。
				<br />这是获取服务器外网IP的另一种更方便的解决方法。
			';
			$frmt = array(
				'code' => array('@' => '@', '状态代码', 0),
				'info' => array('@' => '@', '状态信息', 'success'),
				'time' => array('@' => '@', '请求时间', '1687-04-19 10:05:49'),
				'data' => array(
					'via' => array('@' => '@', '可能的代理IP', ''),
					'poss' => array('@' => '@', '可能的真实IP', ''),
					'fact' => array('@' => '@', '实际连接的IP', '127.0.0.1'),
					'info' => array('@' => '@', '客户端信息', 'Mozilla/5.0 (Windows NT'
						. ' 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0')
				)
			);
			$data = array(
				'addr' => $url,
				'desc' => $desc,
				'mtci' => 'GET',
				'type' => 'application/json',
				'frmt' => $frmt
			);
		} else $data = getArr();
		ScpoPHP::api_pack($data);
	}
	if (substr_compare($qry, 'check', 0, 4) === 0) die('{"code": -1}');
	header('Location: ../../../../chaip/');
	exit();
} else if (isset($_POST['create_gouz'])) {
	if (isset($_COOKIE['alreadyHadGouz'])) {
		$aim = 'noadd';
		goto ghtml_start;
	}
	$hispos = isset($_COOKIE['historyGouz']) ? count($_COOKIE['historyGouz']) : 0;
	$name = "historyGouz[$hispos]";
	$id = ScpoPHP::sql_insert(array(), 'sc_chaip_sign', true);
	$salt = md5(mt_rand(-999999, 999999));
	$salt = substr($salt, mt_rand(0, 15), 16);
	$hash = md5($sheet_id . $salt);
	ScpoPHP::sql_update(array('name' => $hash, 'salt' => $salt));
	$time = time();
	setcookie('alreadyHadGouz', $hash, $time + 3600 * 2);
	$time = $time + 3600 * 24 * 365 * 5;
	setcookie("$name[hash]", $hash, $time);
	setcookie("$name[look]", substr_replace($hash, '<br />', 16, 0), $time);
	setcookie("$name[time]", str_replace(' ', '<br />', date('Y-m-d H:i:s')), $time);
	header("Location: ./p=1$hash");
	exit();
} else {
	$aim = 'index';
	goto ghtml_start;
}
exit();
ghtml_start:
function getHtml($title, $style = false, $script = false, $crf = false)
{
?>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>
			<?= $title ? $title . ' - |简·陋|钓IP工具' : '|简·陋|钓IP工具' ?>
		</title>
		<?php
		if ($style) {
		?>
			<style>
				* {
					padding: 0;
					margin: 0;
					text-shadow: #fff 1px 0 0, #fff 0 1px 0, #fff -1px 0 0, #fff 0 -1px 0;
				}

				.land {
					margin: 0.3cm;
					padding: 0.2cm;
					background: rgba(255, 255, 255, 0.65);
				}

				b {
					font-size: 0.6cm;
					word-break: break-all;
				}

				#btn {
					color: #fff;
					position: relative;
					font-size: 1.5cm;
					padding: 0.1cm;
					padding-top: 0.3cm;
					padding-bottom: 0.3cm;
					box-shadow: 7px 7px #000;
					text-shadow: #000 1px 0 0, #000 0 1px 0, #000 -1px 0 0, #000 0 -1px 0;
					margin-right: 5px;
				}

				#btn:hover {
					left: 2px;
					top: 2px;
					box-shadow: 5px 5px #000;
				}

				#btn:active {
					left: 5px;
					top: 5px;
					box-shadow: 2px 2px #000;
				}
			</style>
		<?php
		}
		if ($script) {
		?>
			<script type="text/javascript">
				window.onload = function() {
					var boxNode = bgp.parentNode;
					var picStyle = bgp.style;
					var picNode = bgp;

					function cleanBGP() {
						var x = boxNode.clientWidth,
							y = boxNode.clientHeight,
							yFact = y,
							xFact = x;
						x / y < scale ? x = y * scale : y = x / scale;
						picStyle.width = (x += 2) + "px";
						picStyle.height = (y += 2) + "px";
						picStyle.left = (xFact - x) / 2 + "px";
						picStyle.top = (yFact - y) / 2 + "px";
					};
					scale = picNode.width / picNode.height;
					cleanBGP();
					window.onresize = cleanBGP;
				}
			</script>
		<?php
		}
		?>
		<?= $crf ? '<script type="text/javascript" src="http://js.seventop.top/crf.js"></script>' : '' ?>
	</head>
<?php
}
switch ($aim) {
	case 'noadd':
		goto noadd_start;
	case 'nohok':
		goto nohok_start;
}
function disHistoryList()
{
?>
	<button onclick="hist.style.display = 'inline-block'">
		显示历史钩子
	</button>
	<br />
	<div id='hist' style="display: none;padding: 1em;" class="land">
		<table border="1" style="text-align:center;word-break: break-all;">
			<tr>
				<th>编号</th>
				<th>网址</th>
				<th>代码</th>
				<th>时间</th>
			</tr>
			<?php
			$his = $_COOKIE['historyGouz'];
			$len = count($his);
			$hash = 'hash';
			for ($i = 0; $i < $len; $i++) {
			?>
				<tr>
					<td>
						<?= $i ?>
					</td>
					<td>
						<button onclick="window.location.href='./p=1<?= $his[$i][$hash] ?>'">
							访问
						</button>
					</td>
					<td>
						<?= $his[$i]['hash'] ?>
					</td>
					<td>
						<?= $his[$i]['time'] ?>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
	</div>
<?php
}
switch ($aim) {
	case 'index':
		goto index_start;
	case 'check':
		goto check_start;
}
index_start:
?>
<!DOCTYPE html>

<?php getHtml(false, true, true, true); ?>

<body>
	<style>
		body,
		html {
			text-align: center;
		}

		.land {
			display: inline-block;
		}

		button:not(.land button) {
			font-size: 0.6cm;
		}
	</style>
	<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;z-index: -1000">
		<img src="https://api.ghser.com/random/api.php" id="bgp" style="position: relative;" />
	</div>
	<form style="display: none;" action="./" method="POST">
		<input type="submit" id="sub" />
		<input name="create_gouz" value="1" />
	</form>
	<div style="padding: 0.5cm;">
		<button id="btn" onclick="sub.click();">生成钩子</button>
		<script>
			change_defaultGraColor.color_weight_changer.fgp.multi = 0.01;
			change_defaultGraColor.transition_weight_changer.fgp.multi = 0.01;
			change_defaultGraColor.axis_changer.fgp.multi = 0.02;
			change_defaultGraColor.color_changer.fgp.multi = 0.01;
			change_eeg(btn);
			change_ccstart(btn);
		</script>
		<br /><br />
		<?php if (isset($_COOKIE['historyGouz'])) disHistoryList(); ?>
	</div>
	<div class="land">
		<h1>
			「你信不信我查你IP地址?!」
		</h1>
		<br />
		<a>
			大IP时代已然到来！<br />
			在这动不动就查IP报家门的乱世，<br />
			只有真正的强者才能不惧IP之挑战！！<br /><br />
		</a>
		<a>
			使用<b>|简·陋|钓IP工具</b>，<br />
			您可以通过给对方发一个网址让其访问，<br />
			来轻松获取对方的IP地址！<br /><br />
			点击“生成钩子”即可生成钓IP的网址。<br /><br />
		</a>
		<a>
			鉴于对方可能会对不明网址抱有抵触情绪，<br />
			我们提供访问后自动跳转到其他页面的功能。<br />
			您可以通过制作短链接，<br />
			设置跳转的目标页面等手段，<br />
			作为饵料掩盖鱼钩，<br />
			降低对方的警戒心。
		</a>
		<br /><br /><br />
		<hr style="width: 80%;display: inline-block;" /><br /><br />
		本站额外提供 1 项API服务。<br /><br />
		<script type="text/javascript">
			function goAPI(n) {
				var arr = window.location.href.split('/')[2];
				arr = arr.split('.');
				arr.shift();
				window.location.href = 'http://api.' + arr.join('.') + '/sc/chaip/' + n + '/intro/';
			}
		</script>
		<ul>
			<li><a href="javascript: goAPI('self');">获取本机IP信息</a></li>
		</ul>
		<br /><br />
	</div>
</body>

</html>
<?php
exit();
check_start:
?>
<!DOCTYPE html>
<html lang="ch">

<?php getHtml("查看访问记录", true, true); ?>

<body>
	<style>
		td {
			white-space: pre;
			text-align: center;
		}
	</style>
	<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;z-index: -1000">
		<img src="https://api.ghser.com/random/api.php" id="bgp" style="position: relative;" />
	</div>
	<div class="land">
		<h2>鱼钩网址</h2>
		<b id="gouza"></b>
		<script type="text/javascript">
			gouzURL = window.location.href.split("?")[0] + "?p=0<?= $sign ?>&u=";
			gouza.innerText = gouzURL;
		</script>
		<br />
		<br />
		将上方的鱼钩网址发给您的好友，若ta访问了这个网址，您正在浏览的这个网页就能显示ta的IP地址。
		<br />
		<br />
		修改访问鱼钩后跳转到的网址：<input oninput="gouza.innerText = gouzURL + this.value" />
		<br />
		（默认跳转到随机一张二次元图片。api提供者<a href="https://ghser.com/">一叶三秋</a>，欢迎支持）
	</div>
	<hr />
	<div class="land">
		<h2>当前网址</h2>
		<b id="urla"></b>
		<script type="text/javascript">
			urla.innerText = window.location.href;
		</script>
		<br />
		<br />
		您只能通过此网址查看鱼钩访问情况，请妥善保存当前网址。
		<br />
		<br />
	</div>
	<hr />
	<div class="land" style="overflow-x: auto;">
		<h2>访问情况</h2>
		<a>
			"代理地址"就是VPN的IP地址，如果不为空，则对方使用了VPN。<br />
			使用了VPN，"连接地址"就不是对方真实的IP，"真实地址"有可能是对方的真实IP。<br />
			只有连接IP和访问时间是完全可信的，其他访问信息都可以人为更改，仅供参考<br /><br />
			<button onclick="window.location.href = window.location.href;" style="font-size: 0.5cm;">刷新页面</button>
		</a>
		<br />
		<table border="1">
			<tr>
				<th>ID</th>
				<th>时间</th>
				<th>连接<br />地址</th>
				<th>代理<br />地址</th>
				<th>真实<br />地址</th>
				<th>信息</th>
			</tr>
			<?php
			$model = [
				'time' => null,
				'fact' => null,
				'via' => null,
				'poss' => null,
			];
			for ($i = 0; $i < count($data); $i++) {
				echo '<tr>';
				echo '<td>' . $i + 1 . '</td>';
				foreach ($model as $key => $val) {
					echo '<td class="' . $key . 'td">' . str_replace(' ', '<br />', $data[$i][$key]) . '</td>';
				}
			?>
				<td style="white-space: nowrap;">
					<textarea style="display: none;"><?= $data[$i]['info'] ?></textarea>
					<button onclick="alert('浏览器信息：\n' + this.parentNode.getElementsByTagName('textarea')[0].value);"> 查看
					</button>
				</td>
			<?php
				echo '</tr>';
			}
			?>
		</table>
	</div>
</body>

</html>
<?php
exit();
noadd_start:
?>
<!DOCTYPE html>
<html lang="ch">

<?php getHtml('操作太频繁'); ?>

<body>
	<h1>错误！</h1>
	您在两个小时之内已经投放过一个钩子了<br /><br />
	<a href='./p=1<?= $_COOKIE[' alreadyHadGouz'] ?>'>
		您的网址
	</a><br /><br />
	<?php if (isset($_COOKIE['historyGouz'])) disHistoryList(); ?>
</body>

</html>
<?php
exit();
nohok_start:
?>
<!DOCTYPE html>
<html lang="ch">

<?php getHtml('钩子不见了'); ?>

<body>
	<h1>错误！</h1>
	未找到钩子
	<b>
		<?= $sign ?>
	</b><br />
	请检查网址是否正确<br /><br />
	<a href="./">返回主页</a>
</body>

</html>
<?php
exit();

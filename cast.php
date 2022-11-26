<?php

namespace SC\Chaip;

require '../../../scpo-php/db.php';
$time = time();

\ScpoPHP\Db::delete('`datetime`<"' . date('Y-m-d H:i:s', $time) . '"', 'sc_chaip_cool');
if (\ScpoPHP\Db::select("`ip`='{$_SERVER['REMOTE_ADDR']}'")) die(header('Location: ./toofast.html'));

\ScpoPHP\Db::insert(['datetime' => date('Y-m-d H:i:s', $time + 3600 * 2), 'ip' => $_SERVER['REMOTE_ADDR']]);
$salt = \ScpoPHP\Db::insert([], 'sc_chaip_mark', true);
\ScpoPHP\Db::delete();
$mark = md5(bin2hex(mt_rand(100000, 999999) . (strlen((string)$salt) % 2 ? "F$salt" : $salt)));

$time = $time + 3600 * 24 * 365 * 20;
$hook_id = isset($_COOKIE['hook_history']) ? count($_COOKIE['hook_history']) : 0;
setcookie("hook_history[$hook_id][mark]", $mark, $time);
setcookie("hook_history[$hook_id][time]", date('Y-m-d H:i:s'), $time);

header("Location: ./bucket.html?$mark");

<?php
header("content-Type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ob_start();

//********************
//		设置区开始
//********************
//认证 KEY
$zKey = "test";

//	检测开关
/**
 * 0为不检测,1为检测
**/
//检测网卡
$zNW = "1";

/**
 * IPV4
**/
//检测TCP连接
//连接数统计
$zTCP4N = "1";
//随机现实5个连接IP信息
$zTCP4IP = "1";
//连接状态统计
$zTCP4ST = "1";

//检测UDP连接
//统计连接数
$zUDP4N = "1";
//随机现实5个连接IP信息
$zUDP4IP = "1";
//连接状态统计
$zUDP4ST = "1";

/**
 * IPV6
**/
//检测TCP连接
//连接数统计
$zTCP6N = "1";
//随机现实5个连接IP信息
$zTCP6IP = "1";
//连接状态统计
$zTCP6ST = "1";

//检测UDP连接
//连接数统计
$zUDP6N = "1";
//随机现实5个连接IP信息
$zUDP6IP = "1";
//连接状态统计
$zUDP6ST = "1";

//检测MySQL
$zSQL = "0";

//MySQL信息
if ($zSQL == "1"){
	$zServerName = "localhost";//MySQL服务器地址
	$zUserName = "root";//MySQL用户名
	$zPassWord = "123";//MySQL密码
	//MySQL链接
	$zConn = mysql_connect($zServerName, $zUserName, $zPassWord);
	if($zConn){$zSQL_Status = "连接成功";}else{$zSQL_Status = "连接失败";}
	mysql_close($zConn);
}

//系统资源预警值
//只允许使用纯数字(正数)
//一分钟负载
$zOL = "50";
//五分钟负载
$zFL = "50";
//十五分钟负载
$zFtL = "50";
//磁盘使用率
$zDP = "99.9";
//物理内存使用率
$zMRP = "99";
//SWAP内存使用率
$zSP = "50";


//********************
//		设置区结束
//********************

//********************
//		服务器信息
//********************
$zSysInfo = zSys();
$zServ = $_GET['num']+1;
//服务器IP
$zIP = $_SERVER["SERVER_ADDR"];
//客户端IP
$zCIP = $_GET['ip'];
//服务器时间
$zSysTime = date("Y-n-j H:i:s");
//北京时间
$BJTime = gmdate("Y-n-j H:i:s",time()+8*3600);
//服务器在线时间
$zUpTime = $zSysInfo['uptime'];
//服务器空闲时间
$zFreeTime = $zSysInfo['freetime'];
//服务器开机时间
$zBTime = $zSysInfo['btime'];
//服务器磁盘信息
if ((@disk_total_space(".") !== null)&&(@disk_total_space(".") != 0)){
	$zSpace = "1";
	$df = zFS(round(@disk_free_space("."),4));
	$dt = zFS(round(@disk_total_space("."),4));
	$dp = round(((@disk_total_space(".")-@disk_free_space("."))/@disk_total_space("."))*100,4);
}else{$zSpace = "0";}

//服务器物理内存信息
if (($zSysInfo['memTotal'] !== null)&&($zSysInfo['memTotal'] != 0)){
	$zMem = "1";
	$mt = zFSS($zSysInfo['memTotal']);
	$mf = zFSS($zSysInfo['memFree']);
	$mc = zFSS($zSysInfo['memCached']);
	$mu = zFSS($zSysInfo['memUsed']);
	//$mr = round(($zSysInfo['memUsed']/$zSysInfo['memTotal'])*100,4);
	$mp = $zSysInfo['memPercent'];
	$mru = zFSS($zSysInfo['memRealUsed']);
	$mrp = $zSysInfo['memRealPercent'];
}else{$zMem = "0";}

//服务器SWAP内存信息
if (($zSysInfo['swapTotal'] !== null)&&($zSysInfo['swapTotal'] != 0)){
	$zSwap = "1";
	$st = zFSS($zSysInfo['swapTotal']);
	$su = zFSS($zSysInfo['swapUsed']);
	$sf = zFSS($zSysInfo['swapFree']);
	//$sr = round(($zSysInfo['swapUsed']/$zSysInfo['swapTotal'])*100,4);
	$sp = $zSysInfo['swapPercent'];
}else{$zSwap = "0";}

//服务器负载信息
if (empty($zSysInfo['LoadAvg'])){$zLoad = "0";}else{
	$zLoad = "1";
	$zLoad_1 = $zSysInfo['LoadAvg_1'];
	$zLoad_5 = $zSysInfo['LoadAvg_5'];
	$zLoad_15 = $zSysInfo['LoadAvg_15'];
	$zLoad_p = $zSysInfo['LoadAvg_p'];
}

//服务器网卡信息
if ($zNW === "1")$zNetNum = $zSysInfo['NetNum'];
if (empty($zNetNum)&&($zNetNum <= "0"))$zNetNum = 0;

//服务器TCP-IPV4信息
if ($zTCP4N === "1")$zT4Num = $zSysInfo['T4Num'];
if ($zTCP4IP === "1")$zT4IP = $zSysInfo['T4IP'];

//服务器UDP-IPV4信息
if ($zUDP4N === "1")$zU4Num = $zSysInfo['U4Num'];
if ($zUDP4IP === "1")$zU4IP = $zSysInfo['U4IP'];

//服务器TCP-IPV6信息
if ($zTCP6N === "1")$zT6Num = $zSysInfo['T6Num'];
if ($zTCP6IP === "1")$zT6IP = $zSysInfo['T6IP'];

//服务器UDP-IPV6信息
if ($zUDP6N === "1")$zU6Num = $zSysInfo['U6Num'];
if ($zUDP6IP === "1")$zU6IP = $zSysInfo['U6IP'];

//********************
//		JSON数据构建
//********************
$zArr = Array(
	'num' => "$zServ",
	'key' => "$zKey",
	'ServerIP' => "$zIP",
	'ServerloadAvg' => Array ( "$zLoad_1","$zLoad_5","$zLoad_15","$zLoad_p","$zLoad" ),
	'ServerSpace' => Array ( 'Space' => "$zSpace",'FreeSpace' => "$df",'TotalSpace' => "$dt",'PercentSpace' => "$dp" ),
	'ServerMemory' => Array ( 'Mem' => "$zMem",'TotalMemory' => "$mt",'FreeMemory' => "$mf",'UsedMemory' => "$mu",'CachedMemory' => "$mc",'PercentMemory' => "$mp",'RealUsedMemory' => "$mru",'RealPercentMemory' => "$mrp" ),
	'ServerSwap' => Array ( 'Swap' => "$zSwap",'TotalSwap' => "$st",'FreeSwap' => "$sf",'UsedSwap' => "$su",'PercentSwap' => "$sp" ),
	'ServerTime' => Array ( 'UpTime' => "$zUpTime",'FreeTime' => "$zFreeTime",'BTime' => "$zBTime",'BJTime' => "$BJTime",'SysTime' => "$zSysTime" ),
	'ServerStatus' => Array ( 'SQL' => "$zSQL",'Net' => "$zNW",'T4N' => "$zTCP4N",'T4IP' => "$zTCP4IP",'T4ST' => "$zTCP4ST",'U4N' => "$zUDP4N",'U4IP' => "$zUDP4IP",'U4ST' => "$zUDP4ST",'T6N' => "$zTCP6N",'T6IP' => "$zTCP6IP",'T6ST' => "$zTCP6ST",'U6N' => "$zUDP6N",'U6IP' => "$zUDP6IP",'U6ST' => "$zUDP6ST" ),
	'ServerMysql' => Array ( 'SQLStatus' => "$zSQL_Status" ),
	'ServerNetWorks' => 'NetWorks',
	'ServerCONNNum' => Array ( 'TCP4Num' => "$zT4Num",'UDP4Num' => "$zU4Num",'TCP6Num' => "$zT6Num",'UDP6Num' => "$zU6Num" ),
	'ServerTCP4conn' => Array ( "$zT4IP[0]","$zT4IP[1]","$zT4IP[2]","$zT4IP[3]","$zT4IP[4]" ),
	'ServerUDP4conn' => Array ( "$zU4IP[0]","$zU4IP[1]","$zU4IP[2]","$zU4IP[3]","$zU4IP[4]" ),
	'ServerTCP6conn' => Array ( "$zT6IP[0]","$zT6IP[1]","$zT6IP[2]","$zT6IP[3]","$zT6IP[4]" ),
	'ServerUDP6conn' => Array ( "$zU6IP[0]","$zU6IP[1]","$zU6IP[2]","$zU6IP[3]","$zU6IP[4]" ),
	'ServerTCP4Status' => 'TCP4Status',
	'ServerUDP4Status' => 'UDP4Status',
	'ServerTCP6Status' => 'TCP6Status',
	'ServerUDP6Status' => 'UDP6Status',
);
$zJarr=json_encode($zArr);
/**
 * 判断是否替换
**/
//网卡信息
if ($zNW === "1")
{
	$zNetWork = $zSysInfo['NetWork'];
	$zJarr=str_replace("\"ServerNetWorks\":\"NetWorks\"",$zNetWork,$zJarr);
}
//TCP-IP4连接状态统计
if ($zTCP4ST === "1")
{
	$zT4STN = $zSysInfo['T4STN'];
	if ($zT4STN > "0")
	{
		$zT4ST = $zSysInfo['T4ST'];
		$zJarr = str_replace("\"ServerTCP4Status\":\"TCP4Status\"",$zT4ST,$zJarr);
	}
}
//UDP-IP4连接状态统计
if ($zUDP4ST === "1")
{
	$zU4STN = $zSysInfo['U4STN'];
	if ($zU4STN > "0")
	{
		$zU4ST = $zSysInfo['U4ST'];
		$zJarr = str_replace("\"ServerUDP4Status\":\"UDP4Status\"",$zU4ST,$zJarr);
	}
}
//TCP-IP6连接状态统计
if ($zTCP6ST === "1")
{
	$zT6STN = $zSysInfo['T6STN'];
	if ($zT6STN > "0")
	{
		$zT6ST = $zSysInfo['T6ST'];
		$zJarr = str_replace("\"ServerTCP6Status\":\"TCP6Status\"",$zT6ST,$zJarr);
	}
}
//UDP-IP6连接状态统计
if ($zUDP6ST === "1")
{
	$zU6STN = $zSysInfo['U6STN'];
	if ($zU6STN > "0")
	{
		$zU6ST = $zSysInfo['U6ST'];
		$zJarr = str_replace("\"ServerUDP6Status\":\"UDP6Status\"",$zU6ST,$zJarr);
	}
}

//********************
//		Auth
//********************
$zKey = md5($zKey);
$zKey = md5(getenv("SERVER_PROTOCOL")."+".$zCIP."+".$_SERVER["SERVER_PORT"]."+".getenv("REQUEST_METHOD")."+".$zKey);

//********************
//		ECHO
//********************
if ($_GET['key'] === "key"){echo $zKey;exit;}
if ($_GET['st'] === "status"){echo $zSQL,'|',$zNW,'|',$zNetNum,'|',$zTCP4N,'|',$zTCP4IP,'|',$zTCP4ST,'|',$zUDP4N,'|',$zUDP4IP,'|',$zUDP4ST,'|',$zTCP6N,'|',$zTCP6IP,'|',$zTCP6ST,'|',$zUDP6N,'|',$zUDP6IP,'|',$zUDP6ST,'|',$zOL,'|',$zFL,'|',$zFtL,'|',$zDP,'|',$zMRP,'|',$zSP,'|';exit;}
if ($_GET['act'] === $zKey){echo $_GET['callback'],'(',$zJarr,')';exit;}

//********************
//		功能函数
//********************
//系统信息
function zSys()
{
	// UPTIME
	if (false === ($zStr = @file("/proc/uptime"))) return false;
	$zStr = explode(" ", implode("", $zStr));
	$zUp = trim($zStr['0']);
	$sec = $zUp;
	$min = $zUp / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	$sec = floor($sec - ($days * 60 * 60 * 24) - ($hours * 60 * 60) - ($min *60));
	if ($days !== 0) $zRes['uptime'] = $days." Day ";
	if ($hours !== 0) $zRes['uptime'] .= $hours." Hour ";
	if ($min !== 0) $zRes['uptime'] .= $min." Min ";
	if ($sec < 60) $zRes['uptime'] .= $sec." Sec ";
	// FREETIME
	$zFree = trim($zStr['1']);
	$sec = $zFree;
	$min = $zFree / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	$sec = floor($sec - ($days * 60 * 60 * 24) - ($hours * 60 * 60) - ($min *60));
	if ($days !== 0) $zRes['freetime'] = $days." Day ";
	if ($hours !== 0) $zRes['freetime'] .= $hours." Hour ";
	if ($min !== 0) $zRes['freetime'] .= $min." Min ";
	if ($sec < 60) $zRes['freetime'] .= $sec." Sec ";
	// BootTime
	if (false === ($zStr = @file("/proc/stat"))) return false;
	$zStr = implode("", $zStr);
	preg_match_all("/btime\s{0,}([\d\.]+)/s", $zStr, $zBTI);
	$zRes['btime'] = date("Y-m-d H:i:s",$zBTI['1']['0']);
	// MEMORY
	if (false === ($zStr = @file("/proc/meminfo"))) return false;
	$zStr = implode("", $zStr);
	preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $zStr, $zMemI);
	$zRes['memTotal'] = $zMemI['1']['0'];
	$zRes['memFree'] = $zMemI['2']['0'];
	$zRes['memCached'] = $zMemI['3']['0'];
	$zRes['memUsed'] = $zRes['memTotal']-$zRes['memFree'];
	$zRes['memPercent'] = (floatval($zRes['memTotal'])!=0)?round($zRes['memUsed']/$zRes['memTotal']*100,4):0;
	$zRes['memRealUsed'] = $zRes['memTotal'] - $zRes['memFree'] - $zRes['memCached'];
	$zRes['memRealPercent'] = (floatval($zRes['memTotal'])!=0)?round($zRes['memRealUsed']/$zRes['memTotal']*100,4):0;
	$zRes['swapTotal'] = $zMemI['4']['0'];
	$zRes['swapFree'] = $zMemI['5']['0'];
	$zRes['swapUsed'] = $zRes['swapTotal']-$zRes['swapFree'];
	$zRes['swapPercent'] = (floatval($zRes['swapTotal'])!=0)?round($zRes['swapUsed']/$zRes['swapTotal']*100,4):0;
	// LOAD AVG
	if (false === ($zStr = @file("/proc/loadavg"))) return false;
	$zStr = explode(" ", implode("", $zStr));
	$zStr = array_chunk($zStr, 4);
	$zRes['LoadAvg'] = implode(" ", $zStr['0']);
	$zRes['LoadAvg_1'] = $zStr['0']['0'];
	$zRes['LoadAvg_5'] = $zStr['0']['1'];
	$zRes['LoadAvg_15'] = $zStr['0']['2'];
	$zRes['LoadAvg_p'] = $zStr['0']['3'];
	// NetWork
	if (false === ($zStr = @file("/proc/net/dev"))) return false;
	$zNetNums = count($zStr)-2;
	$NetWorks = "\"ServerNetWorks\":{\"NetWorkNum\":\"".$zNetNums."\"";
	for ($i=2; $i<count($zStr); ++$i)
	{
		preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $zStr[$i], $zNetI);
		$zNetName[$i] = $zNetI[1][0];
		$zNetInput[$i] = zFS($zNetI[2][0]);
		$zNetOut[$i] = zFS($zNetI[10][0]);
		$x=$i-2;
		$NetWorks .= ",\"NetName".$x."\":\"".$zNetName[$i]."\",";
		$NetWorks .= "\"NetInput".$x."\":\"".$zNetInput[$i]."\",";
		$NetWorks .= "\"NetOut".$x."\":\"".$zNetOut[$i]."\"";
	}
	$NetWorks .= "}";
	$zRes['NetNum'] = $zNetNums;
	$zRes['NetWork'] = $NetWorks;
	//连接状态
	$zCONNCode = array("ERROR_STATUS","ESTABLISHED","SYN_SENT","SYN_RECV","FIN_WAIT1","FIN_WAIT2","TIME_WAIT","CLOSE","CLOSE_WAIT","LAST_ACK","LISTEN","CLOSING");
	// TCP_IPV4
	if (false === ($zStr = @file("/proc/net/tcp"))) return false;
	$zT4Nums = count($zStr)-1;
	$zT4IPs = "[";$zT4STs = "[";
	for ($i=1; $i<count($zStr); ++$i)
	{
		preg_match_all("/([\d]+):\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+)/",$zStr[$i],$zTcp4);
		$zT4Rem[$i] = base_convert(substr($zTcp4['3']['0'],6,2),16,10).".".base_convert(substr($zTcp4['3']['0'],4,2),16,10).".".base_convert(substr($zTcp4['3']['0'],2,2),16,10).".".base_convert(substr($zTcp4['3']['0'],0,2),16,10);
		$zTcp4ST[$i] = base_convert($zTcp4['4']['0'],16,10);
		$zT4IPs .= "\"".$zT4Rem[$i]."\",";
		$zT4STs .= "\"".$zCONNCode[$zTcp4ST[$i]]."\",";
	}
	$zT4IPs .= "]";$zT4STs .= "]";
	$zT4IPNs = count(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/[,]{0,}\"0.0.0.0\"[,]{0,}/","",$zT4IPs)))));
	$zT4IPs = ($zT4IPNs > 0 ? explode(",",preg_replace("/$/"," 个连接",str_replace(","," 个连接 ,",str_replace(":"," 共有 ",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/[,]{0,}\"0.0.0.0\"[,]{0,}/","",$zT4IPs))))))))))) : null);
	$zT4STNs = count(json_decode(str_replace("\",]","\"]",$zT4STs)));
	$zT4STs = "\"ServerTCP4Status\":".str_replace("]","}",str_replace("[","{",str_replace(":","\":\"",json_encode(explode(",",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace("\",]","\"]",$zT4STs))))))))));
	$zRes['T4Num'] = $zT4Nums;
	$zRes['T4IPN'] = $zT4IPNs;
	$zRes['T4IP'] = $zT4IPs;
	$zRes['T4STN'] = $zT4STNs;
	$zRes['T4ST'] = $zT4STs;
	// UDP_IPV4
	if (false === ($zStr = @file("/proc/net/udp"))) return false;
	$zU4Nums = count($zStr)-1;
	$zU4IPs = "[";$zU4STs = "[";
	for ($i=1; $i<count($zStr); ++$i)
	{
		preg_match_all("/([\d]+):\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+)/",$zStr[$i],$zUdp4);
		$zU4Rem[$i] = base_convert(substr($zUdp4['3']['0'],6,2),16,10).".".base_convert(substr($zUdp4['3']['0'],4,2),16,10).".".base_convert(substr($zUdp4['3']['0'],2,2),16,10).".".base_convert(substr($zUdp4['3']['0'],0,2),16,10);
		$zUdp4ST[$i] = base_convert($zUdp4['4']['0'],16,10);
		$zU4IPs .= "\"".$zU4Rem[$i]."\",";
		$zU4STs .= "\"".$zCONNCode[$zUdp4ST[$i]]."\",";
	}
	$zU4IPs .= "]";$zU4STs .= "]";
	$zU4IPNs = count(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/[,]{0,}\"0.0.0.0\"[,]{0,}/","",$zU4IPs)))));
	$zU4IPs = ($zU4IPNs > 0 ? explode(",",preg_replace("/$/"," 个连接",str_replace(","," 个连接 ,",str_replace(":"," 共有 ",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/[,]{0,}\"0.0.0.0\"[,]{0,}/","",$zU4IPs))))))))))) : null);
	$zU4STNs = count(json_decode(str_replace("\",]","\"]",$zU4STs)));
	$zU4STs = "\"ServerUDP4Status\":".str_replace("]","}",str_replace("[","{",str_replace(":","\":\"",json_encode(explode(",",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace("\",]","\"]",$zU4STs))))))))));
	$zRes['U4Num'] = $zU4Nums;
	$zRes['U4IPN'] = $zU4IPNs;
	$zRes['U4IP'] = $zU4IPs;
	$zRes['U4STN'] = $zU4STNs;
	$zRes['U4ST'] = $zU4STs;
	// TCP_IPV6
	if (false === ($zStr = @file("/proc/net/tcp6"))) return false;
	$zT6Nums = count($zStr)-1;
	$zT6IPs = "[";$zT6STs = "[";
	for ($i=1; $i<count($zStr); ++$i)
	{
		preg_match_all("/([\d]+):\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+)/",$zStr[$i],$zTcp6);
		$zT6Rem[$i] = substr($zTcp6['3']['0'],6,2).substr($zTcp6['3']['0'],4,2)."-".substr($zTcp6['3']['0'],2,2).substr($zTcp6['3']['0'],0,2)."-".substr($zTcp6['3']['0'],14,2).substr($zTcp6['3']['0'],12,2)."-".substr($zTcp6['3']['0'],10,2).substr($zTcp6['3']['0'],8,2)."-".substr($zTcp6['3']['0'],22,2).substr($zTcp6['3']['0'],20,2)."-".substr($zTcp6['3']['0'],18,2).substr($zTcp6['3']['0'],16,2)."-".substr($zTcp6['3']['0'],30,2).substr($zTcp6['3']['0'],28,2)."-".substr($zTcp6['3']['0'],26,2).substr($zTcp6['3']['0'],24,2);
		$zTcp6ST[$i] = base_convert($zTcp6['4']['0'],16,10);
		$zT6Rem6[$i] = substr($zTcp6['3']['0'],6,2).substr($zTcp6['3']['0'],4,2)."-".substr($zTcp6['3']['0'],2,2).substr($zTcp6['3']['0'],0,2)."-".substr($zTcp6['3']['0'],14,2).substr($zTcp6['3']['0'],12,2)."-".substr($zTcp6['3']['0'],10,2).substr($zTcp6['3']['0'],8,2)."-".substr($zTcp6['3']['0'],22,2).substr($zTcp6['3']['0'],20,2)."-".substr($zTcp6['3']['0'],18,2).substr($zTcp6['3']['0'],16,2);
		if (($zT6Rem6[$i] == "0000-0000-0000-0000-0000-0000")&&($zT6Rem[$i] !== "0000-0000-0000-0000-0000-0000-0000-0000"))
		{
			$zT6Rem[$i] = "0-0-0-0-0-0".base_convert(substr($zTcp6['3']['0'],30,2),16,10).".".base_convert(substr($zTcp6['3']['0'],28,2),16,10).".".base_convert(substr($zTcp6['3']['0'],26,2),16,10).".".base_convert(substr($zTcp6['3']['0'],24,2),16,10);
		}
		$zT6IPs .= "\"".$zT6Rem[$i]."\",";
		$zT6STs .= "\"".$zCONNCode[$zTcp6ST[$i]]."\",";
	}
	$zT6IPs .= "]";$zT6STs .= "]";
	$zT6IPNs = count(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/\"0-+/","\"--",preg_replace("/([^\d\"]+)(0-){3,}/","--",preg_replace("/0{4,}/","0",preg_replace("/-0{1,3}/","-",preg_replace("/[,]{0,}\"[0-]+\"[,]{0,}/","",$zT6IPs)))))))));
	$zT6IPs = ($zT6IPNs > 0 ? explode(",",str_replace("-",":",preg_replace("/$/"," 个连接",str_replace(","," 个连接 ,",str_replace(":"," 共有 ",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/\"0-+/","\"--",preg_replace("/([^\d\"]+)(0-){3,}/","--",preg_replace("/0{4,}/","0",preg_replace("/-0{1,3}/","-",preg_replace("/[,]{0,}\"[0-]+\"[,]{0,}/","",$zT6IPs)))))))))))))))) : null);
	$zT6STNs = count(json_decode(str_replace("\",]","\"]",$zT6STs)));
	$zT6STs = "\"ServerTCP6Status\":".str_replace("]","}",str_replace("[","{",str_replace(":","\":\"",json_encode(explode(",",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace("\",]","\"]",$zT6STs))))))))));
	$zRes['T6Num'] = $zT6Nums;
	$zRes['T6IPN'] = $zT6IPNs;
	$zRes['T6IP'] = $zT6IPs;
	$zRes['T6STN'] = $zT6STNs;
	$zRes['T6ST'] = $zT6STs;
	// UDP_IPV6
	if (false === ($zStr = @file("/proc/net/udp6"))) return false;
	$zU6Nums = count($zStr)-1;
	$zU6IPs = "[";$zU6STs = "[";
	for ($i=1; $i<count($zStr); ++$i)
	{
		preg_match_all("/([\d]+):\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+):[\dA-F]+\s+([\dA-F]+)/",$zStr[$i],$zUdp6);
		$zU6Rem[$i] = substr($zUdp6['3']['0'],6,2).substr($zUdp6['3']['0'],4,2)."-".substr($zUdp6['3']['0'],2,2).substr($zUdp6['3']['0'],0,2)."-".substr($zUdp6['3']['0'],14,2).substr($zUdp6['3']['0'],12,2)."-".substr($zUdp6['3']['0'],10,2).substr($zUdp6['3']['0'],8,2)."-".substr($zTcp6['3']['0'],22,2).substr($zUdp6['3']['0'],20,2)."-".substr($zUdp6['3']['0'],18,2).substr($zUdp6['3']['0'],16,2)."-".substr($zUdp6['3']['0'],30,2).substr($zUdp6['3']['0'],28,2)."-".substr($zUdp6['3']['0'],26,2).substr($zUdp6['3']['0'],24,2);
		$zUdp6ST[$i] = base_convert($zUdp6['4']['0'],16,10);
		$zU6Rem6[$i] = substr($zUdp6['3']['0'],6,2).substr($zUdp6['3']['0'],4,2)."-".substr($zUdp6['3']['0'],2,2).substr($zUdp6['3']['0'],0,2)."-".substr($zUdp6['3']['0'],14,2).substr($zUdp6['3']['0'],12,2)."-".substr($zUdp6['3']['0'],10,2).substr($zUdp6['3']['0'],8,2)."-".substr($zTcp6['3']['0'],22,2).substr($zUdp6['3']['0'],20,2)."-".substr($zUdp6['3']['0'],18,2).substr($zUdp6['3']['0'],16,2);
		if (($zU6Rem6[$i] == "0000-0000-0000-0000-0000-0000")&&($zUdRem[$i] !== "0000-0000-0000-0000-0000-0000-0000-0000"))
		{
			$zU6Rem[$i] = "0-0-0-0-0-0".base_convert(substr($zUdp6['3']['0'],30,2),16,10).".".base_convert(substr($zTcp6['3']['0'],28,2),16,10).".".base_convert(substr($zTcp6['3']['0'],26,2),16,10).".".base_convert(substr($zTcp6['3']['0'],24,2),16,10);
		}
		$zU6IPs .= "\"".$zU6Rem[$i]."\",";
		$zU6STs .= "\"".$zCONNCode[$zUdp6ST[$i]]."\",";
	}
	$zU6IPs .= "]";$zU6STs .= "]";
	$zU6IPNs = count(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/\"0-+/","\"--",preg_replace("/([^\d\"]+)(0-){3,}/","--",preg_replace("/0{4,}/","0",preg_replace("/-0{1,3}/","-",preg_replace("/[,]{0,}\"[0-]+\"[,]{0,}/","",$zU6IPs)))))))));
	$zU6IPs = ($zU6IPNs > 0 ? explode(",",str_replace("-",":",preg_replace("/$/"," 个连接",str_replace(","," 个连接 ,",str_replace(":"," 共有 ",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace(",]","]",str_replace("[,","[",preg_replace("/\"0-+/","\"--",preg_replace("/([^\d\"]+)(0-){3,}/","--",preg_replace("/0{4,}/","0",preg_replace("/-0{1,3}/","-",preg_replace("/[,]{0,}\"[0-]+\"[,]{0,}/","",$zU6IPs)))))))))))))))) : null);
	$zU6STNs = count(json_decode(str_replace("\",]","\"]",$zU6STs)));
	$zU6STs = "\"ServerUDP6Status\":".str_replace("]","}",str_replace("[","{",str_replace(":","\":\"",json_encode(explode(",",preg_replace("/[\"\{\}]{0,}/","",json_encode(array_count_values(json_decode(str_replace("\",]","\"]",$zU6STs))))))))));
	$zRes['U6Num'] = $zU6Nums;
	$zRes['U6IPN'] = $zU6IPNs;
	$zRes['U6IP'] = $zU6IPs;
	$zRes['U6STN'] = $zU6STNs;
	$zRes['U6ST'] = $zU6STs;
	return $zRes;
}
//字节转换
function zFS($fileSize)
{
	$unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
	$i = 0;
	//由于计算机做乘法比做除法快
	$inv = 1 / 1024;
	while($fileSize >= 1024 && $i < 8)
	{
		$fileSize *= $inv;
		++$i;
	}
	$fileSizeTmp = sprintf("%.2f", $fileSize);
	//以下代码在99.99%的情况下结果会是正确的，除非你使用了"超超大数"。：）
	return ($fileSizeTmp - (int)$fileSizeTmp ? $fileSizeTmp : $fileSize) . $unit[$i];
}
function zFSS($fileSize)
{
	$unit = array(' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
	$i = 0;
	//由于计算机做乘法比做除法快
	$inv = 1 / 1024;
	while($fileSize >= 1024 && $i < 8)
	{
		$fileSize *= $inv;
		++$i;
	}
	$fileSizeTmp = sprintf("%.2f", $fileSize);
	//以下代码在99.99%的情况下结果会是正确的，除非你使用了"超超大数"。：）
	return ($fileSizeTmp - (int)$fileSizeTmp ? $fileSizeTmp : $fileSize) . $unit[$i];
}
?>
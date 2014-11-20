<?php
    date_default_timezone_set('Europe/Zurich'); // adjust to your timezone to avoid PHP compiler warnings
    
	/*
	    Example Taiwan train schedule search query looks like this:
	    
		http://twtraffic.tra.gov.tw/twrail/SearchResult.aspx?searchtype=0&searchdate=2012/08/29&fromstation=1619&tostation=1715&trainclass='1100','1101','1102','1110','1120'&fromtime=0000&totime=2359&language=eng 
	*/
	class tairail {
		var $stations;
		var $maxDaysInFuture;
		
		function __construct() {
		    $this->stationsPathJson = './stations.json';
		    
		    $this->stationsRaw = $this->getStations();
		    $this->stations = $this->getStationsFiltered();
		    
		    $this->maxDaysInFuture = $this->getMaxDaysInFuture();
		}
		
		function getStations() {
		    $json = file_get_contents($this->stationsPathJson);
		    
		    if($json === false) {
		        throw new Exception('Could not open file "' . $this->stationsPathJson . '"');
		    }
		    
		    $stations = json_decode($json,true); // true returns array instead of object
		    
		    if($json === null) {
		        throw new Exception('Could not parse JSON from file "' . $this->stationsPathJson . '"');
		    }
		    
		    if(!is_array($stations)) {
		        throw new Exception('Expected $stations to be array, but is of type "' . gettype($stations) . '"');
		    }
		    
		    return $stations;
		}
		
		function getStationsFiltered() {
		    $tmp = array();
		    foreach($this->stationsRaw as $key=>$attribs) {
		        if($attribs['display'] === false) {
		            // Not displaying this station; adjust $this->stationsPathJson and an items ['display'] attribute to display a specific station
		            continue;
		        }
		        
		        $id = $attribs['id'];
		        $station = $attribs['station'];
		        
		        $tmp[$id] = $station;
		    }
		    
		    return $tmp;
		}
		
		function getMaxDaysInFuture() {
		    // Back when I wrote this script this was the last date in the future which was accepted by the server; any further and you got an error message telling you the date chosen is too far into the future ...
		    $diff = strtotime('2012-10-09') - strtotime('2012-08-26');
			$maxDaysInFuture = floor($diff/60/60/24);
			
			return $maxDaysInFuture;
		}
		
		function makeStationDropdown($name = null, $preselected = null) {
			if($preselected == null) {
				$preselected = 1008;
			}
			
			$tmp = array();
			foreach($this->stations as $id=>$station) {
				if($id === $preselected) {
					$selected = " selected";
				}
				else {
					$selected = null;
				}
				
				$tmp[$station] = '<option value="' . $id . '"' . $selected . '>' . $station . '</input>';
			}
			//sort($tmp);
			ksort($tmp);
			
			$out = '<select name="' . $name . '">' . implode(null,$tmp) . '</select>';
			
			return $out;
		}
		
		function makeDateDropdown($name = null) {
			$out = null;
			
			$today = strtotime(date('Y-m-d 00:00:00'));
			$max = $today + $this->maxDaysInFuture*24*60*60 + 1;
			
			for($i=$today; $i<=$max; $i+=24*60*60) {
				$selected = null;
				
				$out .= '<option value="' . date('Y/m/d',$i) . '" ' . $selected . '>' . date('D, Y-m-d',$i) . '</input>';
			}
			
			$out = '<select name="' . $name . '">' . $out . '</select>';
			
			return $out;
		}
		
		/*
		    Run this function only if you want to overwrite $this->stationPathJson, purging all your own display settings
		*/
		function makeStationsJSON($write = false) {
		    // Comment if you want to regenerate $this->stationPathJson
		    return;
		    
		    $stations = array(
				1810=>'Fulong',
				1809=>'Gongliao',
				1808=>'Shuangxi',
				1807=>'Mudan',
				1806=>'Sandiaoling',
				1805=>'Houtong',
				1804=>'Ruifang',
				1803=>'Sijiaoting',
				1802=>'Nuannuan',
				1001=>'Keelung',
				1029=>'Sankeng',
				1002=>'Badu',
				1003=>'Qidu',
				1030=>'Baifu',
				1004=>'Wudu',
				1005=>'Xizhi',
				1031=>'Xike',
				1006=>'Nangang',
				1007=>'Songshan',
				1008=>'Taipei',
				1009=>'Wanhua',
				1011=>'Banqiao',
				1032=>'Fuzhou',
				1012=>'Shulin',
				1013=>'Shanjia',
				1014=>'Yingge',
				1015=>'Taoyuan',
				1016=>'Neili',
				1017=>'Zhongli',
				1018=>'Puxin',
				1019=>'Yangmei',
				1020=>'Fugang',
				1231=>'Dahu',
				1232=>'Luzhu',
				1233=>'Gangshan',
				1234=>'Qiaotou',
				1235=>'Nanzi',
				1242=>'Xinzuoying',
				1236=>'Zuoying',
				1238=>'Kaohsiung',
				1402=>'Fengshan',
				1403=>'Houzhuang',
				1404=>'Jiuqutang',
				1405=>'Liukuaicuo',
				1406=>'Pingtung',
				1407=>'Guilai',
				1408=>'Linluo',
				1409=>'Xishi',
				1410=>'Zhutian',
				1411=>'Chaozhou',
				1412=>'Kanding',
				1413=>'Nanzhou',
				1414=>'Zhenan',
				1415=>'Linbian',
				1416=>'Jiadong',
				1417=>'Donghai',
				1418=>'Fangliao',
				1502=>'Jialu',
				1503=>'Neishi',
				1504=>'Fangshan',
				1507=>'Guzhuang',
				1508=>'Dawu',
				1510=>'Longxi',
				1512=>'Jinlun',
				1514=>'Taimali',
				1516=>'Zhiben',
				1517=>'Kangle',
				1632=>'Taitung',
				1631=>'Shanli',
				1630=>'Luye',
				1629=>'Ruiyuan',
				1628=>'Ruihe',
				1627=>'Yuemei',
				1626=>'Guanshan',
				1625=>'Haiduan',
				1624=>'Chishang',
				1623=>'Fuli',
				1622=>'Dongzhu',
				1621=>'Dongli',
				1619=>'Yuli',
				1617=>'Sanmin',
				1616=>'Ruisui',
				1614=>'Fuyuan',
				1613=>'Dafu',
				1612=>'Guangfu',
				1611=>'Wanrong',
				1610=>'Fenglin',
				1609=>'Nanping',
				1608=>'Xikou',
				1607=>'Fengtian',
				1606=>'Shoufeng',
				1605=>'Pinghe',
				1604=>'Zhixue',
				1602=>'Jian',
				1715=>'Hualien',
				1714=>'Beipu',
				1713=>'Jingmei',
				1712=>'Xincheng',
				1711=>'Chongde',
				1710=>'Heren',
				1709=>'Heping',
				1708=>'Hanben',
				1706=>'Wuta',
				1705=>'Nanao',
				1704=>'Dongao',
				1703=>'Yongle',
				1827=>'Suao',
				1826=>'Suaoxin',
				1825=>'Xinma',
				1824=>'Dongshan',
				1823=>'Luodong',
				1822=>'Zhongli',
				1821=>'Erjie',
				1820=>'Yilan',
				1819=>'Sicheng',
				1818=>'Jiaoxi',
				1817=>'Dingpu',
				1816=>'Toucheng',
				1815=>'Waiao',
				1814=>'Guishan',
				1813=>'Daxi',
				1812=>'Dali',
				1811=>'Shicheng',
				1908=>'Jingtong',
				1907=>'Pingxi',
				1906=>'Lingjiao',
				1905=>'Wanggu',
				1904=>'Shifen',
				1903=>'Dahua',
				2212=>'Shibo (Qianjia)',
				2213=>'Zhuke (Xinzhuang)',
				2203=>'Zhuzhong',
				2214=>'Liujia',
				2204=>'Shangyuan',
				2211=>'Ronghua',
				2205=>'Zhudong',
				2206=>'Hengshan',
				2207=>'Jiuzantou',
				2208=>'Hexing',
				2209=>'Fugui',
				2210=>'Neiwan',
				2702=>'Yuanquan',
				2703=>'Zhuoshui',
				2704=>'Longquan',
				2705=>'Jiji',
				2706=>'Shuili',
				2707=>'Checheng',
				5101=>'Chang Jung Christian University',
				5102=>'Shalun',
				1021=>'Hukou',
				1022=>'Xinfeng',
				1023=>'Zhubei',
				1024=>'North Hsinchu',
				1025=>'Hsinchu',
				1026=>'Xiangshan',
				1103=>'Tanwannan',
				1237=>'Gushan',
				1119=>'na',
				1633=>'na',
				1634=>'na',
				1635=>'Wuhe',
				1505=>'Fangye',
				1506=>'na',
				2302=>'Taichung Port',
				2402=>'Longjing',
				2502=>'Shangang',
				2802=>'Nandiao',
				2902=>'Kaohsiung Port',
				3102=>'na',
				3202=>'hualien Port',
				3302=>'na',
				3402=>'na',
				3902=>'na',
				4102=>'ShuDiao',
				4202=>'na',
				4302=>'na',
				2002=>'na',
				2102=>'na',
				2103=>'Linkou',
				2104=>'na',
				2105=>'na',
				2106=>'na',
				2107=>'na',
				2108=>'Hengshan',
				1511=>'na',
				1620=>'Antung',
				12430=>'Rende',
				1027=>'Qiding',
				1028=>'Zhunan',
				1102=>'Tanwen',
				1104=>'Dashan',
				1105=>'Houlong',
				1106=>'Longgang',
				1107=>'Baishatun',
				1108=>'Xinpu',
				1109=>'Tongxiao',
				1110=>'Yuanli',
				1302=>'Zaoqiao',
				1304=>'Fengfu',
				1305=>'Miaoli',
				1307=>'Nanshi',
				1308=>'Tongluo',
				1310=>'Sanyi',
				1111=>'Rinan',
				1112=>'Dajia',
				1113=>'Taichung Port',
				1114=>'Qingshui',
				1115=>'Shalu',
				1116=>'Longjing',
				1117=>'Dadu',
				1118=>'Zhuifen',
				1314=>'Taian',
				1315=>'Houli',
				1317=>'Fengyuan',
				1318=>'Tanzi',
				1323=>'Taiyuan',
				1319=>'Taichung',
				1322=>'Daqing',
				1320=>'Wuri',
				1324=>'Xinwuri',
				1321=>'Chenggong',
				1120=>'Changhua',
				1202=>'Huatan',
				1240=>'Dacun',
				1203=>'Yuanlin',
				1204=>'Yongjing',
				1205=>'Shetou',
				1206=>'Tianzhong',
				1207=>'Ershui',
				1208=>'Linnei',
				1209=>'Shiliu',
				1210=>'Douliu',
				1211=>'Dounan',
				1212=>'Shigui',
				1213=>'Dalin',
				1214=>'Minxiong',
				1241=>'Jiabei',
				1215=>'Chiayi',
				1217=>'Shuishang',
				1218=>'Nanjing',
				1219=>'Houbi',
				1220=>'Xinying',
				1221=>'Liuying',
				1222=>'Linfengying',
				1223=>'Longtian',
				1224=>'Balin',
				1225=>'Shanhua',
				1244=>'Nanke',
				1226=>'Xinshi',
				1227=>'Yongkang',
				1239=>'Daqiao',
				1228=>'Tainan',
				1229=>'Baoan',
				1230=>'Zhongzhou',
			);
		    
		    $idsToDisplay = array(
		        1008,//=>'Taipei',
			    1238,//=>'Kaohsiung',
			    1418,//=>'Fangliao',
			    1619,//=>'Yuli',
			    1612,//=>'Guangfu',
			    1715,//=>'Hualien',
			    1712,//=>'Xincheng',
			    1319,//=>'Taichung',
			    1228,//=>'Tainan'
			);
		    
		    $json = array();
		    foreach($stations as $id=>$station) {
		        $display = (in_array($id,$idsToDisplay) ? true : false);
		        
		        $json[] = array(
                    'id' => (int)$id,
                    'station' => trim($station),
                    'display' => $display,
		        );
		    }
		    
		    $json = json_encode($json);
		    $res = file_put_contents($this->stationsPathJson,$json);
		    
		    if($res === false) {
		        throw new Exception('Could not write ' . strlen($json) . ' bytes to file "' . $this->stationsPathJson . '"');
		    }
		    
		    return true;
		}
	}
	
	$t = new tairail();
?>
<html>
<head>
	<title>Taiwan Rail Timetable</title>
	
	<style>
		table 				{border-collapse:collapse;}
		table tr			{}
		table tr td			{padding:8px;border:1px solid #000000;}
	</style>
</head>
<body>

<form action="http://twtraffic.tra.gov.tw/twrail/SearchResult.aspx" method="get">
<input type="hidden" name="language" value="eng">
<input type="hidden" name="searchtype" value="0">

<input type="hidden" name="fromtime" value="0000">
<input type="hidden" name="totime" value="2359">

<table>
	<tr>
		<td>From</td>
		<td><?=$t->makeStationDropdown('fromstation'); ?></td>
	</tr>
	<tr>
		<td>To</td>
		<td><?=$t->makeStationDropdown('tostation'); ?></td>
	</tr>
	<tr>
		<td>Train Class</td>
		<td>
			<input type="radio" name="trainclass" value="'1100','1101','1102','1110','1120'" checked> Express<br>
			<input type="radio" name="trainclass" value="'1131','1132','1140'"> Ordinary<br>
			<input type="radio" name="trainclass" value="2"> All<br>
		</td>
	<tr>
	<tr>
		<td>Date</td>
		<td><?=$t->makeDateDropdown('searchdate'); ?></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="search"></td>
	</tr>
</table>

</form>

</body>
</html>
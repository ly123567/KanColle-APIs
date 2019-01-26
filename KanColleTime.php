<?php
$shipName = $_GET['name'];
if ($shipName == "") die("Error!");
$url = "https://zh.kcwiki.org/wiki/";

$timeWord = array("〇", "一", "二", "三", "四", "五", "六", "七", "八", "九");
$shipName = urlencode($shipName);
$data = array();

//获取网页内容
$websiteContent = file_get_contents($url . $shipName);

for ($i = 0; $i < 24; ++$i) {
    $data[$i]["hour"] = $i;
    $hour = strval($i);
    $hour = str_split(sprintf("%02d", $hour));

    //解析台词
    $word = $timeWord[intval($hour[0])] . $timeWord[intval($hour[1])] . "〇〇时报";
    $posStart = strpos($websiteContent, $word) + strlen($word);
    if ($i < 23) $posEnd = strpos($websiteContent, "<tr>\n<td rowspan", $posStart);
    else $posEnd = strpos($websiteContent, "</table>", $posStart);
    $timeStr = trim(substr($websiteContent, $posStart, $posEnd - $posStart));
    $timeStr = preg_replace("#<tr[^>]*>(.*?)</tr>#is", "$1", $timeStr);
    ParseTimeStr($timeStr, $i);

    //解析语音URL
    $findWord = "data-filesrc=";
    $posStart = strrpos($websiteContent, $findWord, 0 - strlen($websiteContent) + $posStart) + strlen($findWord);
    $posEnd = strpos($websiteContent, ">", $posStart);
    $audioUrl = substr($websiteContent, $posStart, $posEnd - $posStart);
    $audioUrl = trim($audioUrl, "\"");
    $data[$i]["audio"] = $audioUrl;
    $data[$i]["filename"] = basename($audioUrl);
}

echo json_encode($data);

function ParseTimeStr($str, $time) {
    global $data;

    //解析日语
    $word = '<td lang="ja">';
    $start = strpos($str, $word) + strlen($word);
    $end = strpos($str, '</td>', $start);
    $content = trim(substr($str, $start, $end - $start));
    $data[$time]["jp"] = $content;

    //解析中文
    $word = '<td>';
    $start = strpos($str, $word, $end) + strlen($word);
    $end = strpos($str, '</td>', $start);
    $content = trim(substr($str, $start, $end - $start));
    $data[$time]["cn"] = $content;
}
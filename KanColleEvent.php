<?php
    define("PAGE_DOMAIN", "https://zh.kcwiki.org/wiki/");
    define("EVENT_NAME", "%E6%94%BB%E7%95%A5:2018%E5%B9%B4%E7%A7%8B%E5%AD%A3%E6%B4%BB%E5%8A%A8%E6%94%BB%E7%95%A5");

    switch($_GET['type']) {
        case 'reward':
            GetReward();
            break;
        case 'one_sentence':
            GetOneSentence((int)$_GET['mission']);
            break;
        case 'blackboard':
            GetBlackboard((int)$_GET['mission']);
            break;
        case 'range':
            GetRangePic((int)$_GET['mission']);
            break;
    }

    function GetHtmlText($url, $start_text, $end_text) {
        $html = file_get_contents($url);

        $start_pos_temp = stripos($html, $start_text);
        if ($start_pos_temp === false) {
            if (strstr($url, "E5") && strstr($start_text, "一句话攻略")) die("未找到内容或内容过于复杂，请前往原网站查看!");
            die("没有找到此内容,可能原作者未更新!");
        }
        $start_pos = $start_pos_temp + strlen($start_text);
        $end_pos = stripos($html, $end_text, $start_pos);
		$str = substr($html, $start_pos, $end_pos - $start_pos);
		
		while(stripos($html, $start_text, $end_pos) !== false) {
			$str = $str . "--------------------------";
			$start_pos_temp = stripos($html, $start_text, $end_pos);
			$start_pos = $start_pos_temp + strlen($start_text);
			$end_pos = stripos($html, $end_text, $start_pos);
			$str = $str . substr($html, $start_pos, $end_pos - $start_pos);
		}
        
        $str = preg_replace("#<a[^>]*>(.*?)</a>#is", "$1", $str);
        $str = preg_replace("#<span[^>]*>(.*?)</span>#is", "$1", $str);
        $str = preg_replace("#<b[^>]*>(.*?)</b>#is", "【$1】", $str);
        $str = str_replace("3>", "", $str);
		$str = str_replace("2>", "", $str);
		$str = str_replace("1>", "", $str);
        return htmlspecialchars_decode($str);
    }

    function ParseHtmlTable($str) {
        $str = str_replace("<ul>", "", $str);
        $str = str_replace("</ul>", "", $str);
        $str = str_replace("</li>", "", $str);
        $str = str_replace("<li>", "", $str);
        $str = preg_replace("#<div[^>]*>(.*?)</div>#is", "$1", $str);
        $str = preg_replace("#<p[^>]*>(.*?)</p>#is", "$1", $str);
        $str = preg_replace("#<style[^>]*>(.*?)</style>#is", "", $str);
        return $str;
    }

    function GetReward() {
        $url = PAGE_DOMAIN . EVENT_NAME;
        $start_text = '<span class="mw-headline" id=".E9.80.9A.E5.85.B3.E5.A5.96.E5.8A.B1">通关奖励</span></h';
        $end_text = '<h';

        $str = GetHtmlText($url, $start_text, $end_text);
        $str = str_replace("<ul>", "", $str);
        $str = str_replace("</ul>", "", $str);
        $str = str_replace("</li>", "", $str);
        $str = str_replace("<li>", "-", $str);
        $str = str_replace("-E", "E", $str);
        $str = str_replace("、 ", "、", $str);
        $str = str_replace("、 ", "、", $str);
        $str = ParseHtmlTable($str);

        echo trim($str);
    }
    
    function GetOneSentence($mission) {
        $url = PAGE_DOMAIN . EVENT_NAME . "/E" . $mission;
        $start_text = '一句话攻略</span></h';
        $end_text = '<h';

        $str = GetHtmlText($url, $start_text, $end_text);
        $str = ParseHtmlTable($str);
        echo "[E" . $mission . "一句话攻略]\n" . trim($str) . "\n\n[信息来源] Kcwiki [原作者] 极冰之焰";
    }

    function GetBlackboard($mission) {
        $url = PAGE_DOMAIN . EVENT_NAME . "/E" . $mission;
        $start_text = '<div class="poem">';
        $end_text = '</div>';

        $str = GetHtmlText($url, $start_text, $end_text);
        $str = ParseHtmlTable($str);

        $str = str_replace("<br />", "", $str);
        echo trim($str) . "\n\n[信息来源] Kcwiki [原作者] 极冰之焰";
    }

    function GetRangePic($mission) {
        $url = PAGE_DOMAIN . EVENT_NAME . "/E" . $mission;
        $start_text = '航程图</span></h';
        $end_text = '</a>';

        $str = GetHtmlText($url, $start_text, $end_text);
        $start = 'data-url="'; $end = '"';
        $pos_start = stripos($str, $start) + strlen($start);
        $pos_end = stripos($str, $end, $pos_start);
        $pic_url = substr($str, $pos_start, $pos_end - $pos_start);

        header('Content-Type:image/png');
        echo file_get_contents($pic_url);
    }
<?php
    $wday = (int)date("w");

	$file_time = filemtime("img.png");
	$file_wday = (int)date("w", $file_time);
	if (is_file("img.png") && $file_wday == $wday) {
		header("Content-Type:image/png;");
		die(file_get_contents("img.png", true));
	}
	
    $json = file_get_contents('https://kcwikizh.github.io/kcdata/slotitem/poi_improve.json');
    $value = json_decode($json);
	$word = array("日", "一", "二", "三", "四", "五", "六");
    $output = "今日改修(周" . $word[$wday] . "): (除每日固定改修及不常用装备)\n";
	$line = 1;
    
    for ($i = 0; $i < count($value); $i++) {
		$miss = false;
		switch((int)$value[$i]->id) {
			case 1: case 2: case 294: case 266: case 267: case 280: case 282: case 284: case 91: 
			case 4: case 6: case 161:
			case 174: case 15: case 283: 
			case 19: case 109: 
			case 34: case 145: case 72: case 73: case 203: case 204:
			case 44:
			$miss = true;
			break;
		}
		if ($miss) continue;
		
		$improvement = $value[$i]->improvement;
        for ($a = 0; $a < count($improvement); $a++) {
            $req = $improvement[$a]->req;
            for ($b = 0; $b < count($req); $b++) {
                if ((bool)$req[$b]->day[$wday]) {
                    $output .= $value[$i]->type . " " . $value[$i]->name . " (" . $req[$b]->secretary[0] . ")\n";
					$line++;
                    break 2;
                }
            }
        } 
    }
	
	$size = 14; //字体大小
	$font = "c:/windows/fonts/SIMHEI.TTF"; //字体类型，这里为黑体，具体请在windows/fonts文件夹中，找相应的font文件
	$img = imagecreate(460, $line * 24);
	imagecolorallocate($img, 0xff, 0xff, 0xff);
	$black = imagecolorallocate($img, 0, 0, 0); //设置字体颜色
	
	add_ship_pic("Amatsukaze.jpg", 80, 30);
	add_ship_pic("Kasumi.jpg", 80, 730);
	add_ship_pic("Asashio.jpg", 80, 1350);
	imagettftext($img, $size, 0, 0, 16, $black, $font, $output);//将ttf文字写到图片中
	
	imagepng($img, ".\img.png");
	header("Content-Type:image/png;");
	echo file_get_contents("img.png", true);
	
	function add_ship_pic($ship_path, $x, $y) {
		global $img;
		$ship = imagecreatefromjpeg($ship_path);
		list($ship_w, $ship_h) = getimagesize($ship_path);
		imagecopymerge($img, $ship, $x, $y, 0, 0, $ship_w, $ship_h, 30);
	}
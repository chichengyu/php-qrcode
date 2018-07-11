<?php 

// 1. 生成原始的二维码(生成图片文件)
/**
 * 生成二维码
 * @param    url $url       二维码链接
 * @param    string $qrDir  二维码保存路径(根目录)
 * @param    string $qrName 二维码图片名称
 * @return   path 返回生成二维码路径
 */
function scerweima($url='',$qrDir,$qrName){
	is_dir($qrDir) || mkdir($qrDir,0755,true);
	$value = $url;			//二维码内容	
	$errorCorrectionLevel = 'L';	//容错级别 
	$matrixPointSize = 60;		//生成图片大小 
	$margin = 1;			//控制生成二维码的空白区域大小

	Vendor('Phpqrcode.phpqrcode');
    	//生成临时二维码图片
    	$filename = $qrDir.'/'.time().'.png';
	//生成二维码图片
	QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, $margin);  

	$QR = $filename;				//已经生成的原始二维码图片文件  
 
	$QR = imagecreatefromstring(file_get_contents($QR)); 

  	//删除临时二维码图片
  	@unlink($filename);

	//输出图片  
  	$path = $qrDir.'/'.$qrName.'.png';
	imagepng($QR, $path);
	imagedestroy($QR);
	return $path;
}


//2. 在生成的二维码中加上logo(生成图片文件)
/**
 * 生成二维码
 * @param    url $url 二维码链接
 * @param    string $qrDir 二维码保存路径(根目录)
 * @param    string $qrName 二维码图片名称
 * @param    string $logo 二维码中间logo路径
 * @return   source 返回图片流资源
 */
function scerweima1($url,$qrDir,$qrName,$logo){
    	Vendor('Phpqrcode.phpqrcode');

    	is_dir($qrDir)||mkdir($qrDir,0755);
	$value = $url;					//二维码内容  
	$errorCorrectionLevel = 'H';	//容错级别  
	$matrixPointSize = 60;			//生成图片大小  
	$margin = 1;					//控制生成二维码的空白区域大小	
	//生成临时二维码图片
	// $filename = $_SERVER['DOCUMENT_ROOT'].'/Public/qrcode_logo/'.time().'.png';
    	$filename = $qrDir.'/'.time().'.png';
	//生成二维码图片
	QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, $margin);  
    	//$logo = $_SERVER['DOCUMENT_ROOT'].'/Public/logo/logo.png';   //准备好的logo图片  
	$QR = $filename;			//已经生成的原始二维码图  
 
	if (file_exists($logo)) {
		$QR = imagecreatefromstring(file_get_contents($QR));   		//目标图象连接资源。
		$logo = imagecreatefromstring(file_get_contents($logo));   	//源图象连接资源。
		$QR_width = imagesx($QR);			//二维码图片宽度   
		$QR_height = imagesy($QR);			//二维码图片高度   
		$logo_width = imagesx($logo);		//logo图片宽度   
		$logo_height = imagesy($logo);		//logo图片高度   
		$logo_qr_width = $QR_width / 4;   	//组合之后logo的宽度(占二维码的1/5)
		$scale = $logo_width/$logo_qr_width;   	//logo的宽度缩放比(本身宽度/组合后的宽度)
		$logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
		$from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
		//重新组合图片并调整大小
		/*
		 *	imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
		 */
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
		//删除临时二维码图片
  		@unlink($filename);
	}
    	//输出图片 
    	ob_start();
    	imagepng($QR);
    	$ob_img = ob_get_contents();
    	ob_end_clean();
    	imagedestroy($QR);
    	imagedestroy($logo);
    return $ob_img;
}

//3. 生成原始的二维码(不生成图片文件)
function scerweima2($url=''){
	Vendor('Phpqrcode.phpqrcode');
	
	$value = $url;					//二维码内容
	$errorCorrectionLevel = 'L';	//容错级别 
	$matrixPointSize = 5;			//生成图片大小  
	$margin = 2;					//控制生成二维码的空白区域大小
	//生成二维码图片
	$QR = QRcode::png($value,false,$errorCorrectionLevel, $matrixPointSize, $margin);
}


// 图片合成需要的二维码
/**
 * 生成二维码 
 * @param    array(
 *     'url'      => 二维码链接,
 *     'qrDir'    => 二维码保存路径(根目录),
 *     'qrName'   => 二维码图片名称,
 *     'logo'     => 二维码中间logo路径,
 *     'top'      => 顶部文字,
 *     'botton'   => 底部文字,
 * ) 
 * @return   void
 */
function qrcode($arr){
    $qrDir = str_replace('\\','/',$arr['qrDir']);
    $qrDir = $_SERVER['DOCUMENT_ROOT'].$qrDir.'/'.date('Y-m');
    if (!$arr['logo']) {
        $arr['logo'] = $_SERVER['DOCUMENT_ROOT'].'/Public/logo/logo.png';
    }
    $img_source = scerweima1($arr['url'],$qrDir,$arr['qrName'],$arr['logo']);
    //var_dump($qrDir.'/'.$arr['qrName']);die;

	// 创建画布
	$image = imagecreatetruecolor(440,610);
	$bgwhite = imagecolorallocate($image,255,255,255);
    imagefill($image, 0, 0, $bgwhite);
	
    // 合成的二维码上下左右居中
    $img = imagecreatefromstring($img_source);
    $bg_width = imagesx($image);//背景图片宽度   
    $bg_height = imagesy($image);//背景图片高度   
    $code_width = imagesx($img);//用户分享码宽度   
    $code_height = imagesy($img);//用户分享码高度   
    $code_qr_width = $bg_width / 1;   
    $scale = $code_width/$code_qr_width;   
    $code_qr_height = $code_height/$scale;
    $from_width = ($bg_width - $code_qr_width) / 2;   
    //重新组合图片并调整大小   
    $copy = imagecopyresampled($image, $img, $from_width, $from_width+130, 0, 0, $code_qr_width,$code_qr_height, $code_width, $code_height);     

    //底部文字
	$color = imagecolorallocate( $image, 0, 0, 0);
    $font = $_SERVER['DOCUMENT_ROOT'].'/public/font/myfont.TTF'; // 字体文件
    $fontsize = 12;
    $fontwidth = imagettfbbox( $fontsize, 0, $font, $arr['botton'] ); //获取文字的宽度 
    $textBottonW = ceil( ($bg_width - $fontBox[0]) / 2 );//计算文字的x坐标
    $textBottonH = $bg_height - $fontBox[3] - 30;//计算文字的y坐标
	imagettftext( $image, $fontsize, 0, $textBottonW, $textBottonH, $color, $font, $arr['botton']); // 创建文字

    //顶部文字
	$red = imagecolorallocate( $image, 48, 184, 69 );//创建一个颜色，以供使用
	imagefilledrectangle( $image, 0, 0, imagesx($image), 130, $red );//画一个矩形。参数说明：30,30表示矩形左上角坐标；240,140表示矩形右下角坐标；$red表示颜色
    $color = imagecolorallocate( $image, 255, 255, 255 );
    $fontsize = 50;
    $fontBox = imagettfbbox( $fontsize, 0, $font, $arr['top'] );//文字水平居中实质
    $textTopW = ceil( ($bg_width - $fontBox[2] ) / 2 );//计算文字的x坐标
    $textTopH = ( 130 - $fontBox[5] ) / 2;//计算文字的y坐标
    imagettftext ( $image, $fontsize, 0, $textTopW, $textTopH, $color, $font, $arr['top'] );
    // $arr = array(
    //     'fontBox' => $fontBox,
    //     'bg_width' => $bg_width,
    // );
    // file_put_contents('filename.txt', json_encode($arr));

    if ( strpos($arr['qrName'], '.png') === false ) {
        $arr['qrName'] = $arr['qrName'].'.png';
    }
    header('Content-Type:image/png');
    //$paths = $_SERVER['DOCUMENT_ROOT'].'/Public/qrcode_logo/logo_01.png';
    $path = $qrDir.'/'.$arr['qrName'];
	imagepng( $image, $path );
	imagedestroy( $image );
    radius_img( $path );
}


/**
 * 处理圆角图片
 * @param  string  $imgpath 源图片路径
 * @param  integer $radius  圆角半径长度默认为15,处理成圆型
 * @return [type]           [description]
 */
function radius_img($imgpath, $radius = 15) {
    $ext     = pathinfo($imgpath);
    $src_img = null;
    $index = 0;
    switch ($ext['extension']) {
    case 'jpg':
        $src_img = imagecreatefromjpeg($imgpath);
        $index = 0;
        break;
    case 'png':
        $src_img = imagecreatefrompng($imgpath);
        $index = 1;
        break;
    }
    $wh = getimagesize($imgpath);
    $w  = $wh[0];
    $h  = $wh[1];
    // $radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
    $img = imagecreatetruecolor($w, $h);
    //这一句一定要有
    imagesavealpha($img, true);
    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $bg);
    $r = $radius; //圆 角半径
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgbColor = imagecolorat($src_img, $x, $y);
            if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                //不在四角的范围内,直接画
                imagesetpixel($img, $x, $y, $rgbColor);
            } else {
                //在四角的范围内选择画
                //上左
                $y_x = $r; //圆心X坐标
                $y_y = $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //上右
                $y_x = $w - $r; //圆心X坐标
                $y_y = $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //下左
                $y_x = $r; //圆心X坐标
                $y_y = $h - $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //下右
                $y_x = $w - $r; //圆心X坐标
                $y_y = $h - $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
    }
    // $prifex = strrchr($imgpath,'.');
    // $fileName = substr($imgpath,0,-3).'_th'.$prifex;
    
    if ($index === 0) {
        header('Content-Type:image/jpeg');
        imagejpeg($img,$imgpath);
    }else{
        header("content-type:image/png");
        imagepng($img,$imgpath);
    }
    imagedestroy($img);
    //@unlink($imgpath);
    //return true;
}

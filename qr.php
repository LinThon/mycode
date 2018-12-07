<?php    

include 'phpqrcode.php';    
   

function ImageAddBoard($ImgUrl, $px = 2,$width=0,$height=0,$color=array(255,255,255,10)) {
    $aPathInfo = pathinfo ( $ImgUrl );
    $sExtension = $aPathInfo ['extension'];
    $imginfo = getimagesize ( $ImgUrl );
    $img_w = $width == 0 ? 300 : $width;
    $img_h = $height == 0 ? $img_w/$imginfo[0] *$imginfo[1] :$height;
    $r = $color[0];
    $g = $color[1];
    $b = $color[2];
    $a = $color[3];
    // 读取图片
    if (strtolower ( $sExtension ) == 'png') {
        $resource = imagecreatefrompng ( $ImgUrl );
    } elseif (strtolower ( $sExtension ) == 'jpg' || strtolower ( $sExtension ) == 'jpeg') {
        $resource = imagecreatefromjpeg ( $ImgUrl );
    }
    $im = @imagecreatetruecolor ( ($img_w), ($img_h) ) or die ( "Cannot Initialize new GD image stream" );
    $color = imagecolorallocatealpha  ( $im, $r, $g, $b ,$a );

    $logo_width = $imginfo[0];//logo图片宽度   
    $logo_height = $imginfo[1];//logo图片高度   
    
    $logo_qr_width = $img_w -$px;   
    $scale = $logo_qr_width/$logo_width;   
    $logo_qr_height = $logo_height*$scale;   
    $from_width = ($img_w - $logo_qr_width) / 2;   
    $from_height = ($img_h - $logo_qr_height) / 2;   


    imagefill ( $im, 0, 0, $color );
    imagecopyresampled($im, $resource, $from_width, $from_height, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height); 
    imagedestroy($resource);
    return $im; 
}

$value = isset($_REQUEST["url"]) ? $_REQUEST["url"] : "";
$errorCorrectionLevel = isset($_REQUEST["l"]) ? $_REQUEST["l"] : "H";
$matrixPointSize = isset($_REQUEST["s"]) ? $_REQUEST["s"] : "6";
$logo = isset($_REQUEST["logo"]) ? $_REQUEST["logo"] : FALSE;
 
$QR = QRcode::png($value, false , $errorCorrectionLevel, $matrixPointSize, 1);
if ($logo !== FALSE && !empty($logo)) {   
    $logo = ImageAddBoard($logo,30,300,300);   
    $QR_width = imagesx($QR);//二维码图片宽度   
    $QR_height = imagesy($QR);//二维码图片高度   
    $logo_width = imagesx($logo);//logo图片宽度   
    $logo_height = imagesy($logo);//logo图片高度   
    
    $logo_qr_width = $QR_width / 5;   
    $scale = $logo_qr_width/$logo_width;   
    $logo_qr_height = $logo_height*$scale;   
    $from_width = ($QR_width - $logo_qr_width) / 2;   
    $from_height = ($QR_height - $logo_qr_height) / 2;   
    //重新组合图片并调整大小   
    imagecopyresampled($QR, $logo, $from_width, $from_height, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);   
}   
//输出图片  
Header("Content-type: image/png"); 
imagepng($QR);   


?>
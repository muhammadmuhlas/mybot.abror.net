<?php
$size = $_GET['size'];
$text = $_GET['text'];

function createImage($text, $x, $y){

    header('Content-type: image/png');
    $img=imagecreatetruecolor($x, $y);
    $text_color=imagecolorallocate($img, 200, 200, 200);
    imagestring($img, 10000, 10, 10, $text, $text_color);
    imagepng($img);
    imagedestroy($img);
}

if ($size == "s"){

    createImage($text, 240, 240);
}

if ($size == "l"){

    createImage($text, 1024, 1024);
}

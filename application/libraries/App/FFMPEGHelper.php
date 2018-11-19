<?php

namespace App;

use App\OSHelper;

class FFMPEGHelper {

    public static function isVideoFile($pathToVideo) {
        $tmp = strrchr($pathToVideo, '.');
        $ext = substr($tmp, 1, strlen($tmp) - 1);
        return true;
    }

    /** extract image from video
     * 
     * @param string $pathToVideo path to video
     * 
     * @return string path to image
     */
    public static function extractImage($pathToVideo, $pathToImage = '') {

        $ffmpeg = '';

        if (OSHelper::isWin()) {
            $ffmpeg = WEB_ROOT . '/vendor/ffmpeg/ffmpeg.exe';
            if (!file_exists($ffmpeg)) {
                throw new Exception('FFMPEG is not found');
            }
        } else {
            $ffmpeg = 'fmpeg';
        }

        $time = strtotime(date('Y-m-d H:i:s'));

        $video = $pathToVideo;

        if (!self::isVideoFile($pathToVideo)) {
            echo 'Invalid Video File';
            return false;
        }

        if (!$pathToImage) {

            $ext = strrchr($pathToVideo, '.');
            $pathToImage = str_replace($ext, '.jpg', $pathToVideo);
        }

        $image = $pathToImage;

        $second = 1;

        $cmd = "$ffmpeg -i $video 2>&1";

        if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
            $total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
            $second = rand(1, ($total - 1));
        }

        $cmd = "$ffmpeg -i $video -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
        exec($cmd);
    }

}


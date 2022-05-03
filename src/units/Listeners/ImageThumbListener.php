<?php

namespace Idopin\ApiSupport\Listeners;

use Idopin\ApiSupport\Events\FileUploadedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class ImageThumbListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param FileUploadedEvent $event
     * @return void
     */
    public function handle(FileUploadedEvent $event)
    {
        $this->__thumb($event->fileInfo, $event->folder, $event->maxSize, $event->suffix);
    }

    private function __thumb(array $fileInfo, string $folder, int $maxSize, string $suffix)
    {
        $src = $fileInfo['pathName'];

        $mime = $fileInfo['mime'];

        $image_size = getimagesize($src);

        $im = null;
        $src_width = $image_size[0];  // 源宽度
        $src_height = $image_size[1]; // 源高度

        // 判断是否要缩放
        if ($src_height > $maxSize || $src_width >  $maxSize) {
            // 计算图片最终的尺寸大小
            $maxVal = max($src_width, $src_height);
            $ratio = $maxVal / $maxSize;

            $des_width = round($src_width / $ratio);
            $des_height = round($src_height / $ratio);

            switch ($mime) {
                case "image/jpeg": {
                        $im = imagecreatefromjpeg($src);
                        break;
                    }
                case "image/png": {
                        $im = imagecreatefrompng($src);
                        break;
                    }
                case "image/gif": {
                        $im = imagecreatefromgif($src);
                        break;
                    }
                default: {
                        return "error";
                    }
            }

            $des_im = imagecreatetruecolor($des_width, $des_height);

            imagecopyresampled($des_im, $im, 0, 0, 0, 0, $des_width, $des_height, $src_width, $src_height);

            $des_file = $folder . '/thumbs/' . preg_replace('/\./', "_${suffix}.", $fileInfo['hashName']);

            switch ($mime) {
                case "image/jpeg": {
                        $stream = $this->__image_stream($des_im, 'imagejpeg');
                        Storage::put($des_file, $stream);
                        break;
                    }
                case "image/png": {
                        $stream = $this->__image_stream($des_im, 'imagepng');
                        Storage::put($des_file, $stream);
                        break;
                    }
                case "image/gif": {
                        $stream = $this->__image_stream($des_im, 'imagegif');
                        Storage::put($des_file, $stream);
                        break;
                    }
                default: {
                        return "error";
                    }
            }
        }
    }

    private function __image_stream($im, callable $image_method)
    {
        ob_start();

        $image_method($im);

        $string_data = ob_get_contents(); // read from buffer

        ob_end_clean(); // delete buffer

        return $string_data;
    }
}

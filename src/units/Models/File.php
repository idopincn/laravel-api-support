<?php

namespace Idopin\ApiSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'size',
        'extension',
        'mime',
        'md5'
    ];

    public function path(null|string $scale = '')
    {
        $dirname = dirname($this->path) . '/thumbs/';
        $basename = basename($this->path);

        if (in_array($scale, ['small', 'medium', 'large'])) {
            $path = $dirname . preg_replace('/\./', "_${scale}.", $basename);
            return Storage::exists($path) ? $path : $this->path;
        } else {
            return $this->path;
        }
    }
}
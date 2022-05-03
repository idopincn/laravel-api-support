<?php

namespace Idopin\ApiSupport\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileUploadedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public array $fileInfo;
    public string $folder;
    public int $maxSize;
    public string $suffix;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $fileInfo, string $folder, int $maxSize, string $suffix)
    {
        //
        $this->fileInfo = $fileInfo;
        $this->folder = $folder;
        $this->maxSize = $maxSize;
        $this->suffix = $suffix;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

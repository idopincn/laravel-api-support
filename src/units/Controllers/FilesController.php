<?php

namespace Idopin\ApiSupport\Controllers;

use Idopin\ApiSupport\Controller;
use Idopin\ApiSupport\Enums\ApiCode;
use Idopin\ApiSupport\Events\FileUploadedEvent;
use Idopin\ApiSupport\Models\File;
use Idopin\ApiSupport\Models\UserFile;
use Idopin\ApiSupport\Requests\FileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class FilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'index',
                'show'
            ]
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function check(Request $request)
    {
        $md5 = strtolower($request->md5);

        if ($file = File::where('md5', $md5)->first()) {
            return $this->response(ApiCode::RESOURCE_EXIST, $file->toArray());
        } else {
            return $this->response(ApiCode::OK, null, '文件不存在于服务器');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileRequest $request)
    {
        $file = $request->file('file');

        $isPublic = (int)$request->input('public', 0);

        $file_md5 = md5_file($file);

        // 根据 md5 判断文件是否存在
        // 如果文件存在则直接返回文件的信息

        if ($res = File::where('md5', $file_md5)->first()) {

            //
            $this->__storeToUserFile($res->id);
            return $this->response(ApiCode::OK, $res->toArray());
        }

        // 日期目录名
        $folder = 'files/' . now()->format('Ymd');

        // 存在文件并得到相对路径

        $path = $isPublic ?  $file->storePublicly($folder) : $file->store($folder);

        if ($path === false) {
            return $this->response(ApiCode::RESOURCE_CREATE_FAILED);
        }

        // 默认磁盘
        $defaultDisk = config('filesystems.default');

        $fileInfo = [
            'pathName' => config("filesystems.disks.{$defaultDisk}.root") . '/' . $path,  // 文件的真实地址
            'mime' => strtolower($file->getClientMimeType()),
            'hashName' => $file->hashName()
        ];

        // 把文件信息保存到数据库
        $data = [
            'path' => $path,
            'size' => $file->getSize(),
            'extension' => $file->extension(),
            'mime' => $fileInfo['mime'],
            'md5' => $file_md5
        ];


        $createdFile = File::create($data);

        if (strpos($fileInfo['mime'], 'image/') !== false) {
            FileUploadedEvent::dispatch($fileInfo, $folder, 400, 'small');
            FileUploadedEvent::dispatch($fileInfo, $folder, 800, 'medium');
            FileUploadedEvent::dispatch($fileInfo, $folder, 1200, 'large');
        }

        // 保存到用户文件数据表
        $this->__storeToUserFile($createdFile->id);

        return $this->response(ApiCode::RESOURCE_CREATED, $createdFile->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file, Request $request)
    {
        $scale = $request->query('scale');

        return  Storage::url($file->path($scale));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }

    private function __storeToUserFile(int $file_id)
    {
        $user_id =  auth('api')->id();
        if (UserFile::where('user_id', $user_id)->where('file_id', $file_id)->doesntExist()) {
            UserFile::create([
                'user_id' =>  $user_id,
                'file_id' => $file_id
            ]);
        }
    }
}

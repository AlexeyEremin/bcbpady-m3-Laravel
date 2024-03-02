<?php
/** и нажать ENTER
 * перед методом, а после не большой свой комментарий сверху опиши
 */

namespace App\Http\Controllers;

use App\Http\Requests\AddFileRequest;
use App\Http\Resources\AccessResource;
use App\Http\Resources\FileNotLoadedResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\GetFileResource;
use App\Http\Resources\SharedResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AccessRequest;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * А что этот не дописал метод?
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile(File $file)
    {
        $path = Storage::disk('public')->path($file->path);

        return response()->download($path, basename($path));
    }

    /**
     * А что этот не дописал метод?
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteFile(File $file)
    {
        $successSecurity = $file->access()->where(['user_id' => auth()->id(), 'author' => 1])->first();

        if (!$successSecurity) {
            return response([
                "message" => "Forbidden for you",
            ], 403);
        }

        $file->delete();

        return response([
            'success' => true,
            'message' => 'File already deleted'
        ]);
    }
    /**
     * Добавления файлов
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    /*
     * Смотри я добавил новый Request, посмотри его логику валидации
     */
    public function addFiles(AddFileRequest $request)
    {
        $user = auth()->user();
        $files = $request->file('files');

        foreach ($files as $file) {
            $name = $file->getClientOriginalName();

            $fileName = pathinfo($name, PATHINFO_FILENAME);
            $fileExtension = pathinfo($name, PATHINFO_EXTENSION);

            $existFile = $user->files()->where([
                ['name', 'LIKE', $fileName],
                ['type', 'LIKE', $fileExtension],
            ])->orderByDesc('version')->first();
            $version = $existFile ? ['version' => $existFile->version + 1] : [];

            $path = $file->store('public');
            if ($path) {
                # Убираем под папки
                $path = explode('/', $path);
                # Получаем последние значение это название нашего файла
                $path = $path[count($path) - 1];

                $createFile = File::create(
                    [
                        'path' => $path,
                        'name' => $existFile->name ?? $fileName,
                        'type' => $existFile->type ?? $fileExtension,
                    ] + $version
                );

                $createFile->access()->create(['author' => true, 'user_id' => $user->id]);

                $res[] = new FileResource([
                    "fileName" => $createFile->nameFile(),
                    "url" => $path,
                    "file_id" => $createFile->id,
                ]);
            } else {
                $res[] = new FileNotLoadedResource([
                    'fileName' => $file->getClientOriginalName(),
                ]);
            }
        }

        return response($res);
    }

    /**
     * Выдаем права на файл
     * @param  File           $file
     * @param  AccessRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addAccess(File $file, AccessRequest $request)
    {
        $userCoAuthor = User::where('email', $request->email)->first();
        $file->access()->updateOrCreate([
            'user_id' => $userCoAuthor->id,
        ]);

        return response(AccessResource::collection($file->access));
    }

    public function deleteAccess(File $file, AccessRequest $request)
    {
        $coAuthor = User::where('email', $request->email)->first();
        $access = $file->access()->where(['user_id' => $coAuthor->id, 'author' => 0])->first();

        if (!$access) {
            return response([
                "message" => "Not found",
            ], 404);
        }
        $access->delete();

        return response(AccessResource::collection($file->access));
    }

    public function getDisk()
    {
        $fileAccess = auth()->user()->access;

        return response(GetFileResource::collection($fileAccess));
    }

    public function getShared()
    {
        $fileNotAuthor = auth()->user()->access()->where('author', 0)->get();

        return response(SharedResource::collection($fileNotAuthor));
    }
}

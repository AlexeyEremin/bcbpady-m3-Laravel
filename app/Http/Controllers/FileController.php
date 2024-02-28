<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccessResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\GetFileResource;
use App\Models\Access;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AccessRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
  public function addFiles(Request $request)
  {
    $user = auth()->user();
    $files = $request->allFiles();

    foreach ($files as $file) {
      $name = $file->getClientOriginalName();
      $fileSize = $file->getSize();

      $fileName = pathinfo($name, PATHINFO_FILENAME);
      $fileExtension = pathinfo($name, PATHINFO_EXTENSION);

      if (
        $fileSize > 1024 * 2
        && ($fileExtension == "doc"
          || $fileExtension == "pdf"
          || $fileExtension == "docx"
          || $fileExtension == "zip"
          || $fileExtension == "jpeg"
          || $fileExtension == "jpg"
          || $fileExtension == "png"
        )
      ) {
        $originalFileName = $fileName;
        for ($i = 1; ; $i++) {
          $fileСompare = File::all()
            ->where('name', ($fileName . '.' . $fileExtension))
            ->where('user_id', $user->id)
            ->first();
          if ($fileСompare) {
            $fileName = $originalFileName . " ($i)";
          } else {
            break;
          }
        }

        for (; ; ) {
          $randomName = Str::random(10);
          $fileСompare = File::all()
            ->where('pash', ("file/" . $randomName . '.' . $fileExtension))
            ->first();
          if (!$fileСompare) {
            break;
          }
        }

        $path = $file->storeAs('file', $randomName . '.' . $fileExtension);

        $file = new File();
        $file->user_id = $user->id;
        $file->path = $path;
        $file->name = ($fileName . '.' . $fileExtension);
        $file->save();

        $res[] = new FileResource([
          "success" => true,
          'file' => $file
        ]);

      } else {
        $file = [];
        $file['name'] = $name;
        $res[] = new FileResource([
          "success" => false,
          'file' => $file
        ]);
      }
    }
    return response($res);
  }

  public function addAccess($file_id, AccessRequest $request)
  {
    $user = auth()->user();
    $file = File::where('path', 'like', ("file/" . $file_id . "%"))
      ->first();
    if (!$file) {
      return response([
        "message" => "Not found"
      ], 404);
    }
    if ($file->user_id != $user->id) {
      return response([
        "message" => "Forbidden for you"
      ], 403);
    }
    if ($request->email != $user->email) {
      $coAuthor = User::all()
        ->where('email', $request->email)
        ->first();
      if (!$coAuthor) {
        return response([
          "message" => "Not found"
        ], 404);
      }
      $access = Access::where('user_id', $coAuthor->id)
        ->where('file_id', $file->id)
        ->first();
      if (!$access) {
        $access = new Access();
        $access->user_id = $coAuthor->id;
        $access->file_id = $file->id;
        $access->save();
      }
    }
    $accesses = Access::where('file_id', $file->id)
      ->with('user')
      ->with('file')
      ->get();
    $res = new AccessResource([
      'accesses' => $accesses,
      'user' => $user,
    ]);
    return response($res);
  }

  public
    function deleteAccess(
    $file_id,
    AccessRequest $request
  ) {
    $user = auth()->user();
    $file = File::where('path', 'like', ("file/" . $file_id . "%"))
      ->first();
    if (!$file) {
      return response([
        "message" => "Not found"
      ], 404);
    }
    if ($request->email == $user->email) {
      return response([
        "message" => "Forbidden for you"
      ], 403);
    }
    if ($file->user_id != $user->id) {
      return response([
        "message" => "Forbidden for you"
      ], 403);
    }
    $coAuthor = User::all()
      ->where('email', $request->email)
      ->first();
    if (!$coAuthor) {
      return response([
        "message" => "Not found"
      ], 404);
    }
    $access = Access::where('user_id', $coAuthor->id)
      ->where('file_id', $file->id)
      ->first();
    if (!$access) {
      return response([
        "message" => "Not found"
      ], 404);
    }
    $access->delete();
    $accesses = Access::where('file_id', $file->id)
      ->with('user')
      ->with('file')
      ->get();
    $res = new AccessResource([
      'accesses' => $accesses,
      'user' => $user,
    ]);
    return response($res);
  }

  public function getDisk(Request $request)
  {
    $res = [];
    $user = auth()->user();
    $files = File::where('user_id', $user->id)
      ->get();
    foreach ($files as $file) {
      $accesses = Access::where('file_id', $file->id)
        ->with('user')
        ->with('file')
        ->get();
      $res[] = new GetFileResource([
        'file' => $file,
        'accesses' => $accesses,
        'user' => $user,
      ]);
    }
    return response($res);
  }

  public function getShared(Request $request)
  {
    $res = [];
    $user = auth()->user();
    $accesses = Access::where('user_id', $user->id)
      ->get();
    foreach ($accesses as $access) {
      $file = File::where('id', $access->file_id)
        ->first();
      $res[] = new GetFileResource([
        'file' => $file,
      ]);
    }
    return response($res);
  }
}

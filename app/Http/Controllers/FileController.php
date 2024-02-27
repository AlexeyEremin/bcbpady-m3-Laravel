<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AccessRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
  public function getUser($request)
  {
    $token = $request->header('Authorization');
    $token = str_replace('Bearer ', '', $token);
    $user = User::where('api_token', $token)
      ->first();
    if (!$user) {
      return response([
        "success" => false,
        "message" => "Login failed",
      ], 401);
    }
    return $user;
  }

  public function addFiles(Request $request)
  {
    $user = $this->getUser($request);
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

        for (; ;) {
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

        $res[] = [
          "success" => true,
          "message" => "Success",
          "name" => ($fileName . '.' . $fileExtension),
          "url" => env('APP_URL') . "files/" . $name,
          "file_id" => $randomName,
        ];
      } else {
        $res[] = [
          "success" => false,
          "message" => "File not loaded",
          "name" => $name,
          "url" => env('APP_URL') . "files/" . $name
        ];
      }
    }
    return response($res);
  }

  public function addAccess($file_id, AccessRequest $request)
  {
    $user = $this->getUser($request);
    $res[] = [
      "fullname" => ($user->first_name . " " . $user->last_name),
      "email" => $user->email,
      "type" => "author"
    ];
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
      ->get();

    foreach ($accesses as $access) {
      $coAuthor = User::all()
        ->where('id', $access->user_id)
        ->first();
      $res[] = [
        "fullname" => ($coAuthor->first_name . " " . $coAuthor->last_name),
        "email" => $coAuthor->email,
        "type" => "co-author"
      ];
    }
    return response($res);
  }

  public
  function deleteAccess($file_id, AccessRequest $request)
  {
    $user = $this->getUser($request);
    $res[] = [
      "fullname" => ($user->first_name . " " . $user->last_name),
      "email" => $user->email,
      "type" => "author"
    ];
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
      ->get();

    foreach ($accesses as $access) {
      $coAuthor = User::all()
        ->where('id', $access->user_id)
        ->first();
      $res[] = [
        "fullname" => ($coAuthor->first_name . " " . $coAuthor->last_name),
        "email" => $coAuthor->email,
        "type" => "co-author"
      ];
    }
    return response($res);
  }

  public
  function getDisk(Request $request)
  {
    $res = [];
    $user = $this->getUser($request);
    $files = File::where('user_id', $user->id)
      ->get();
    foreach ($files as $file) {
      $accesses = Access::where('file_id', $file->id)
        ->get();
      $coAuthors = [];
      foreach ($accesses as $access) {
        $coAuthor = User::all()
          ->where('id', $access->user_id)
          ->first();
        $coAuthors[] = [
          "fullname" => ($coAuthor->first_name . " " . $coAuthor->last_name),
          "email" => $coAuthor->email,
          "type" => "co-author"
        ];
      }
      $file_id = pathinfo($file->path, PATHINFO_FILENAME);
      $res[] = [
        "file_id" => $file_id,
        "name" => $file->name,
        "url" => (env('APP_URL') . 'files/' . $file_id),
        "accesses" => $coAuthors,
      ];
    }

    return response($res);
  }
}

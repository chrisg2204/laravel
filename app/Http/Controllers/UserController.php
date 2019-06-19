<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Libs.
use Validator;
use Mail;
use DB;

// Models
use App\User;

class UserController extends Controller
{
    private $success = 200;
    private $bad = 400;
    private $notFound = 404;
    private $notAuthorized = 401;
    private $servErr = 500;

    /**
     * [__construct description]
     */
    public function __construct() {
        // 
    }

    /**
     * [create Método para crear un usuario]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    public function create(Request $req) {
        $validator = Validator::make($req->all(), [
            "name" => "required|regex:/^[\pL\s\-]+$/u",
            "email" => "required|email",
            "phone" => "required|numeric",
            "password" => "required",
            "c_password" => "required|same:password"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], $this->bad);
        }

        $body = $req->all();

        $findUser = User::where('id', $body['email'])->first();

        if (!$findUser) {
            $user = new User;
            $user->name = $body['name'];
            $user->email = $body['email'];
            $user->phone = $body['phone'];
            $user->status = $body['status'];
            $user->password = bcrypt($body['password']);
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->name;

            $userSaved = $user->save();
            if (!$userSaved) {
                return response()->json([
                    "success" => false,
                    "content" => "Error al registar Usuario ".$body["email"]
                ], $this->$servErr);
            } else {
                return response()->json([
                    "success" => true,
                    "content" => "Registro exitoso"
                ], $this->success);
            }
        } else {
            return response()->json([
                "success" => false,
                "content" => "Email ".$body["email"]." ya existe"
            ], $this->bad);
        }
    }

    /**
     * [find Método para encontrar un usuario por id]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    public function find(Request $req) {
        $userId = $req->route("id");

        $find = User::where("id", $userId)->first();

        if (!$find) {
            return response()->json([
                "success" => false,
                "content" => "Usuario ".$userId." no encontrado."
            ], $this->notFound);
        } else {
            return response()->json([
                "rows" => $find
            ], $this->success);
        }
    }

    /**
     * [findAll Método que encuentra todos los usuarios]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    public function findAll(Request $req) {
        $offset = ($req->offset !== null) ? $req->offset : 0;
        $limit = ($req->limit !== null) ? $req->limit : 10;
        $searchType = ($req->searchType !== null) ? $req->searchType : "all";
        $verifyArr = ['limit' => $limit, 'offset' => $offset];

        $validator = Validator::make($verifyArr, [
            'limit' => 'numeric',
            'offset' => 'numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], $this->bad);
        }

        $findAll = User::select([DB::raw("
            users.id AS id,
            users.name AS name,
            users.email AS email,
            users.phone AS phone,
            users.status AS status,
            users.create_date AS create_date
        ")])
        ->orderBy("users.id", "ASC")
        ->take($limit)
        ->skip($offset)
        ->get();

        return response()->json([
            "rows" => $findAll
        ], $this->success);
    }

    /**
     * [delete Método que elimina un usuario por id]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    public function delete(Request $req) {
        $userId = $req->route("id");

        $find = User::where("id", $userId)->first();

        if (!$find) {
            return response()->json([
                "success" => false,
                "content" => "Usuario ".$userId." no encontrado"
            ], $this->notFound);
        } else {
            $delete = $find->delete();

            if ($delete) {
                return response()->json([
                    "success" => true,
                    "content" => "Usuario eliminado con exito"
                ], $this->success);
            }
        }
    }

    /**
     * [update Método actualiaza un usuario por id]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    public function update(Request $req) {
        $userId = $req->route("id");

        $validator = Validator::make($req->all(), [
            "name" => "sometimes|required|regex:/^[\pL\s\-]+$/u",
            "email" => "sometimes|required|email",
            "phone" => "sometimes|required|numeric",
            "password" => "sometimes|required",
            "c_password" => "sometimes|required|same:password"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], $this->bad);
        }

        $find = User::where("id", $userId)->first();

        if (!$find) {
            return response()->json([
                "success" => false,
                "content" => "Usuario ".$userId." no encontrado"
            ], $this->notFound);
        } else {
            $body = $req->all();

            if (array_key_exists('name', $body)) {
                $find->name = $body['name'];

            }
            if (array_key_exists('status', $body)) {
                $find->status = $body['status'];

            }
            if (array_key_exists('email', $body)) {
                $find->email = $body['email'];

            }
            if (array_key_exists('phone', $body)) {
                $find->phone = $body['phone'];

            }
            if (array_key_exists('password', $body)) {
                $find->password = bcrypt($body['password']);

            }

            $userUpdated = $find->save();
            if (!$userUpdated) {
                return response()->json([
                    "success" => false,
                    "content" => "Error al actualizar Usuario ".$userId
                ], $this->serverErr);
            } else {
                return response()->json([
                    "success" => true,
                    "content" => "Usuairo Actualizado exitosamente."
                ], $this->success);
            }
        }
    }

}

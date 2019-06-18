<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Libs.
use Validator;
use Mail;
use DB;

class PassportController extends Controller
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
	 * [login Método para iniciar sesión]
	 * @param  Request $req [description]
	 * @return [type]       [description]
	 */
	public function login(Request $req) {
		$validator = Validator::make($req->all(), [
			'email' => 'required|email',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
				'error' => $validator->errors()
			], $this->bad);
		}

		$body = $req->all();

		if (Auth::attempt([
			'email' => $body['email'],
			'password' => $body['password']
		])) {
			$userAuth = Auth::user();
			$token = $userAuth->createToken('MyApp')->accessToken;

			return response()->json([
				'user' => $userAuth,
				'token' => $token
			], $this->success);
		} else {

			return response()->json([
				'success' => false,
				'content' => 'Usuario o contraseña invalidos.'
			], $this->notAuthorized);
		}
	}

	/**
	 * [logout Método para finalizar sesión]
	 * @param  Request $req [description]
	 * @return [type]       [description]
	 */
	public function logout(Request $req) {
		if (Auth::check()) {
			$req->user()->token()->delete();

			return response()->json(['success' => true, 'content' => 'Sesión finalizada.'], $this->success);
		}
	}

}

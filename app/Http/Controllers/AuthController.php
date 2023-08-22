<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints relacionados a autenticação do user",
 * )
 */
class AuthController extends Controller
{

    /**
     * @OA\Schema(
     *     schema="User",
     *     title="User",
     *     description="Objeto de usuário",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="john@example.com"),
     *     @OA\Property(property="type", type="string", example="atendente"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar Usuário",
     *     description="Autentica o usuário e gera um token de acesso.",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\Header(header="Authorization", description="Bearer token", @OA\Schema(type="string")),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Bem-vindo, ' . $user->name . '! Você foi autenticado com sucesso.',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ])->withCookie(cookie('token', $token, 60 * 24 * 30));

    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar Novo Usuário",
     *     description="Registra um novo usuário.",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name","email","password","type"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="type", type="string", enum={"atendente","gerente","suporte"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="authorisation", type="object", 
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="type", type="string", example="bearer"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *     ),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'type' => ['required', 'string', 'max:255', 'in:atendente,gerente,suporte'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ])->withCookie(cookie('token', $token, 60 * 24 * 30));
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout",
     *     description="Realiza o logout do usuário.",
     *     tags={"Autenticação"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ])->withCookie(cookie()->forget('token'));
    }


    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Atualizar Token",
     *     description="Atualiza o token de acesso.",
     *     tags={"Autenticação"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="authorisation", type="object", 
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="type", type="string", example="bearer"),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Obter Dados de um Determinado Usuario",
     *     description="Retorna os dados de um determinado usuario pelo ID .",
     *     tags={"Autenticação"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Somente o proprio usuario pode ver seu perfil",
     *     ),
     * 
     * )
     */
    public function show($id)
    {
        if (Auth::id() == $id) {
            $user = Auth::user();
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 403);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/userinfo",
     *     summary="Obter dados do usuario logado",
     *     description="Retorna os dados do usuario que está atualmente logado",
     *     tags={"Autenticação"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Somente o proprio usuario pode ver seu perfil",
     *     ),
     * 
     * )
     */
    public function getId()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}

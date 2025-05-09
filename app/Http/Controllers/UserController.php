<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AssignRoleRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * UserController constructor
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Kullanıcıları listeler
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    /**
     * Kullanıcı detayını getirir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    /**
     * Kullanıcıya rol veya rolleri atar
     *
     * @param AssignRoleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function assignRole(AssignRoleRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userService->assignMultipleRoles($id, $validated['role_ids']);

        return response()->json([
            'status' => true,
            'message' => count($validated['role_ids']) > 1 ? 'Roller başarıyla atandı' : 'Rol başarıyla atandı',
            'data' => [
                'user' => $user,
                'roles' => $user->roles
            ]
        ]);
    }

    /**
     * Kullanıcının rollerini getirir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getUserRoles(int $id): JsonResponse
    {
        $roles = $this->userService->getUserRoles($id);

        return response()->json([
            'status' => true,
            'data' => $roles
        ]);
    }

    /**
     * Kullanıcıdan rol kaldırır
     *
     * @param int $id
     * @param int|string $roleId
     * @return JsonResponse
     */
    public function removeRole(int $id, $roleId): JsonResponse
    {
        $user = $this->userService->removeRole($id, $roleId);

        return response()->json([
            'status' => true,
            'message' => 'Rol başarıyla kaldırıldı',
            'data' => [
                'user' => $user,
                'roles' => $user->roles
            ]
        ]);
    }
}

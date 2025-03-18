<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\IndexRoleRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Rol listesini getirir
     */
    public function index(IndexRoleRequest $request): JsonResponse
    {
        $roles = $this->roleService->getRoles($request->getPerPage());
        
        return response()->json([
            'status' => true,
            'data' => $roles
        ]);
    }

    /**
     * Yeni rol oluşturur
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Rol başarıyla oluşturuldu',
            'data' => $role
        ], 201);
    }

    /**
     * Rol detayını getirir
     */
    public function show(int $id): JsonResponse
    {
        $role = $this->roleService->getRole($id);
        
        return response()->json([
            'status' => true,
            'data' => $role
        ]);
    }

    /**
     * Rolü günceller
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = $this->roleService->updateRole($id, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Rol başarıyla güncellendi',
            'data' => $role
        ]);
    }

    /**
     * Rolü siler
     */
    public function destroy(int $id): JsonResponse
    {
        $this->roleService->deleteRole($id);

        return response()->json([
            'status' => true,
            'message' => 'Rol başarıyla silindi'
        ]);
    }
} 
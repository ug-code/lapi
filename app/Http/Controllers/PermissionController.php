<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\CreateRequest;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Tüm permission'ları listeler
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $permissions = $this->permissionService->getAll();

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission listesi başarıyla getirildi',
            'data'    => $permissions
        ], 200);
    }

    /**
     * Yeni bir permission oluşturur
     *
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(CreateRequest $request): JsonResponse
    {

        $permission = $this->permissionService->create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission başarıyla oluşturuldu',
            'data'    => $permission
        ], 201);

    }
}

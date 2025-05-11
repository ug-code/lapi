<?php

namespace App\Http\Controllers;

use App\Http\Requests\AiTool\StoreAiToolRequest;
use App\Http\Requests\AiTool\UpdateAiToolRequest;
use App\Services\AiToolService;
use Illuminate\Http\JsonResponse;

class AiToolController extends Controller
{
    /**
     * @var AiToolService
     */
    protected AiToolService $aiToolService;

    /**
     * AiToolController constructor
     *
     * @param AiToolService $aiToolService
     */
    public function __construct(AiToolService $aiToolService)
    {
        $this->aiToolService = $aiToolService;
    }

    /**
     * Listeleme
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $perPage = request()->query('per_page', 15);
        $tools = $this->aiToolService->getAllTools((int)$perPage);
        return response()->json($tools);
    }

    /**
     * Ekleme (Save)
     *
     * @param StoreAiToolRequest $request
     * @return JsonResponse
     */
    public function store(StoreAiToolRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tool = $this->aiToolService->createTool($validated);

        return response()->json([
            'message' => 'AI Tool created successfully',
            'data' => $tool,
        ], 201);
    }

    /**
     * Tekil Gösterme
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $tool = $this->aiToolService->getToolById($id);
        return response()->json($tool);
    }

    /**
     * Güncelleme (Update)
     *
     * @param UpdateAiToolRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateAiToolRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();

        $tool = $this->aiToolService->updateTool($id, $validated);

        return response()->json([
            'message' => 'AI Tool updated successfully',
            'data' => $tool,
        ]);
    }

    /**
     * Silme (Delete)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->aiToolService->deleteTool($id);

        return response()->json([
            'message' => 'AI Tool deleted successfully',
        ]);
    }
}

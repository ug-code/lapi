<?php

namespace App\Http\Controllers;

use App\Models\AiTool;
use App\Http\Requests\AiTool\StoreAiToolRequest;
use App\Http\Requests\AiTool\UpdateAiToolRequest;
use Illuminate\Http\JsonResponse;

class AiToolController extends Controller
{
    // Listeleme
    public function index(): JsonResponse
    {
        $tools = AiTool::all();
        return response()->json($tools);
    }

    // Ekleme (Save)
    public function store(StoreAiToolRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tool = AiTool::create($validated);

        return response()->json([
            'message' => 'AI Tool created successfully',
            'data' => $tool,
        ], 201);
    }

    // Tekil Gösterme
    public function show($id): JsonResponse
    {
        $tool = AiTool::findOrFail($id);
        return response()->json($tool);
    }

    // Güncelleme (Update)
    public function update(UpdateAiToolRequest $request, $id): JsonResponse
    {
        $tool = AiTool::findOrFail($id);

        $validated = $request->validated();

        $tool->update($validated);

        return response()->json([
            'message' => 'AI Tool updated successfully',
            'data' => $tool,
        ]);
    }

    // Silme (Delete)
    public function destroy($id): JsonResponse
    {
        $tool = AiTool::findOrFail($id);
        $tool->delete();

        return response()->json([
            'message' => 'AI Tool deleted successfully',
        ]);
    }
}

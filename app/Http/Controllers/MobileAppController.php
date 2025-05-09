<?php

namespace App\Http\Controllers;


use App\Http\Requests\MobileApp\CreateCategoryRequest;
use App\Http\Requests\MobileApp\CreateKeywordRequest;
use App\Http\Requests\MobileApp\SetLearnKeywordRequest;
use App\Services\MobileAppService;
use Illuminate\Http\JsonResponse;

class MobileAppController extends Controller
{
    /**
     * @var MobileAppService
     */
    protected MobileAppService $mobileAppService;

    /**
     * MobileAppController constructor
     *
     * @param MobileAppService $mobileAppService
     */
    public function __construct(MobileAppService $mobileAppService)
    {
        $this->mobileAppService = $mobileAppService;
    }

    /**
     * Kelime oluşturur
     *
     * @param CreateKeywordRequest $request
     * @return JsonResponse
     */
    public function createKeyword(CreateKeywordRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = 1; // Şimdilik sabit kullanıcı ID

        $result = $this->mobileAppService->createKeyword($validatedData);

        return response()->json([
            'message' => $result['message'],
            'data'    => $result['data']
        ]);
    }

    /**
     * Kelime listesini getirir
     *
     * @return JsonResponse
     */
    public function getKeywordList(): JsonResponse
    {
        $data = $this->mobileAppService->getKeywordList();

        return response()->json([
            'message' => 'Başarılı bir şekilde getirildi.',
            'data'    => $data
        ]);
    }

    /**
     * Kelime öğrenilme durumunu günceller
     *
     * @param SetLearnKeywordRequest $request
     * @return JsonResponse
     */
    public function setLearnKeyword(SetLearnKeywordRequest $request): JsonResponse
    {
        $request->validated();

        $data = $this->mobileAppService->setLearnKeyword($request->id, $request->isLearned);

        return response()->json([
            'message' => 'Başarılı bir şekilde güncellendi.',
            'data'    => $data
        ]);
    }

    /**
     * Kelime çevirisi yapar
     *
     * @param string $keyword
     * @return JsonResponse
     */
    public function translate($keyword)
    {
        $data = $this->mobileAppService->translate($keyword);

        return response()->json([
            'message' => 'Çeviri başarıyla tamamlandı.',
            'data'    => $data
        ]);
    }

    /**
     * ID'ye göre kelime detayını getirir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getKeyword($id): JsonResponse
    {
        $data = $this->mobileAppService->getKeywordWithTranslation($id);

        return response()->json([
            'message' => 'Kelime başarıyla getirildi.',
            'data'    => $data
        ]);
    }

    /**
     * Kullanıcının kelime sayısını getirir
     *
     * @return JsonResponse
     */
    public function myKeywordCount(): JsonResponse
    {
        $data = $this->mobileAppService->getKeywordCount(1); // Şimdilik sabit kullanıcı ID

        return response()->json([
            'message' => 'Kelime sayısı başarıyla getirildi.',
            'data'    => $data
        ]);
    }

    /**
     * Kategori oluşturur
     *
     * @param CreateCategoryRequest $request
     * @return JsonResponse
     */
    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {
        $request->validated();

        $result = $this->mobileAppService->createCategory($request->description, 1); // Şimdilik sabit kullanıcı ID

        return response()->json([
            'message' => $result['message'],
            'data'    => $result['data']
        ]);
    }

    /**
     * Kategori listesini getirir
     *
     * @return JsonResponse
     */
    public function getCategory(): JsonResponse
    {
        $category = $this->mobileAppService->getCategories(1); // Şimdilik sabit kullanıcı ID

        return response()->json([
            'message' => "Kategoriler başarıyla getirildi",
            'data'    => $category
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\BannerService;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannersController extends Controller
{
    public function __construct(private readonly BannerService $banner_service)
    {
    }

    public function obterBanner(Request $request): JsonResponse
    {
        $banner = $this->banner_service->obterBanner($request->id);
        
        return $this->getResponse('success', [
            'banner' => $banner
        ]);
    }

    public function saveBanner(Request $request): JsonResponse
    {
        // $request->validate([
        //     'imgBanner' => 'file',
        // ]);

        $banner = $this->banner_service->save(
            $request->id,
            $request->cidades,
            $request->title,
            $request->subtitle,
            $request->link,
            $request->type,
            $request->format,
            Auth::id(),
            $request->imgBanner,
            $request->starts_at ? new DateTime($request->starts_at) : null
        );

        return $this->getResponse('success', [
            'message' => 'Banner salvo com sucesso!',
            'banner' => $banner
        ]);
    }

    public function deleteBanner(Request $request): JsonResponse
    {
        $this->banner_service->delete($request->id);

        return $this->getResponse('success', [
            'message' => 'Banner excluído com sucesso!',
        ]);
    }

    public function duplicateBanner(Request $request): JsonResponse
    {
        $this->banner_service->duplicate($request->id);

        return $this->getResponse('success', [
            'message' => 'Banner duplicado com sucesso!',
        ]);
    }

    public function getBanners(Request $request): JsonResponse
    {
        $banners = $this->banner_service->getBanners($request->id_cidade);

        return $this->getResponse('success', [
            'banners' => $banners
        ]);
    }

    public function getBannersByLocation(Request $request): JsonResponse
    {
        $banners = $this->banner_service->getBannersByLocation(
            $request->location,
            $request->city_id,
            $request->state_id
        );

        return $this->getResponse('success', [
            'banners' => $banners
        ]);
    }

    public function getBannersByType(Request $request): JsonResponse
    {
        $banners = $this->banner_service->getBannersByType(
            $request->type,
            $request->format,
            $request->city,
            $request->state,
        );

        return $this->getResponse('success', [
            'banners' => $banners
        ]);
    }
}

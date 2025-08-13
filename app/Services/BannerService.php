<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Banner;
use App\Models\BannerLocation;
use App\Models\Cidade;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    public function getBannersByLocation(string $location, ?int $city_id, ?int $state_id): array
    {
        $banners = Banner::where(function ($query) use ($city_id, $state_id, $location) {
            if ($city_id) {
                $query->whereHas('cities', function ($query) use ($city_id) {
                    $query->where('city_id', $city_id);
                });
            }

            if ($state_id) {
                $query->whereHas('states', function ($query) use ($state_id) {
                    $query->where('state_id', $state_id);
                });
            }

            $query->whereHas('locations', function ($query) use ($location) {
                $query->whereIn('location_key', [$location, 'all']);
            });
            $query->where('starts_at', '<=', now());
        })
        ->inRandomOrder()
        ->get();

        return $banners->toArray();
    }

    public function getBannersByType(string $type, ?string $format, ?string $city, ?string $state): array
    {
        // Log::info('Geolocalização: ' . $city . ' - ' . $state);
        // Log::info('format: ' . $format);
        if(!$format) $format = "D";

        $cidadeAtual = Cidade::whereRelation('estado', 'nome', 'like', '%'.$state.'%')->where('nome', $city)->first();
        
        $banners = Banner::where('type', $type)->where('format', $format);
        if($cidadeAtual) $banners->whereRelation('cities', 'city_id', '=', $cidadeAtual->id);
        $banners = $banners->whereNotNull('cdn_url')
            ->inRandomOrder()
            ->get();

        $entrouNoIf = false;
        if(!$banners || count($banners) == 0){
            $entrouNoIf = true;
            $banners = Banner::where('type', $type)->where('format', $format)
                ->whereNotNull('cdn_url')
                ->inRandomOrder()
                ->get();
        }

        // Log::info('type');
        // Log::info($type);
        // Log::info($entrouNoIf);
        return $banners->map(function ($banner){
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'link' => $banner->link,
                'url_imagem' => $banner->url_imagem,
            ];
        })->toArray();
    }

    public function obterBanner($id): array
    {
        $banner = Banner::where('id', $id)->with('cities.estado')->first();
        $bannerDTO = [
            'id' => $banner->id,
            'title' => $banner->title,
            'subtitle' => $banner->subtitle,
            'link' => $banner->link,
            'type' => $banner->type,
            'format' => $banner->format,
            'url_imagem' => $banner->url_imagem,
        ];

        if($banner->cities){
            $bannerDTO['cidades'] = $banner->cities->map(function($city){
                return [
                    'value' => $city->id,
                    'label' => $city->nome . ' (' . $city->estado->sigla . ')',
                ];
            });
        }

        return $bannerDTO;
    }

    public function getBanners($idCidade): array
    {
        // Log::info('idCidade =' . $idCidade);
        $query = Banner::query();

        if (!empty($idCidade)) {
            $query->whereHas('cities', function ($q) use ($idCidade) {
                $q->where('cidades.id', $idCidade);
            });
        }

        $banners = $query->orderBy('banner.id', 'desc')->get()->toArray();
        return $banners;
    }

    public function save(
        ?int $id,
        ?array $cidades,
        ?string $title,
        ?string $subtitle,
        ?string $link,
        ?string $type,
        ?string $format,
        int $created_by_user,
        ?UploadedFile $file,
        ?\DateTime $starts_at
    ): array {
        $url = null;
        $banner = null;

        // Verifica se está atualizando (tem ID)
        if ($id) {
            $banner = Banner::find($id);
        }

        // Se tem novo arquivo E já existe um banner com imagem anterior
        if ($file && $banner && $banner->filename) {
            $oldPath = self::getBucketFilePath() . '/' . $banner->filename;

            if (Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }

        if($file){
            $bucketFilename = self::getBucketFilename($file);
            $bucketBasePath = self::getBucketFilePath();
            Storage::makeDirectory($bucketBasePath, 0775, true);
            $filePath = Storage::putFileAs(
                $bucketBasePath,
                $file,
                $bucketFilename,
            );
            $url = Storage::url($filePath);
        }

        $updateOrCreateArray = [
            'title' => $title,
            'subtitle' => $subtitle,
            'link' => $link,
            'type' => $type,
            'format' => $format,
            'created_by_user' => $created_by_user,
            'starts_at' => $starts_at ?: now(),
        ];

        if($url) $updateOrCreateArray['cdn_url'] = $url;
        if($file) $updateOrCreateArray['filename'] = $bucketFilename;
        if($file) $updateOrCreateArray['original_filename'] = $file->getClientOriginalName();

        $banner = Banner::updateOrCreate(
            ['id' => $id],
            $updateOrCreateArray,
        );
        // Log::info($banner);

        $banner->load('cities');
        // Log::info($cidades);
        if($cidades){
            $cities = Cidade::whereIn('id', array_map(function ($city){return $city['value'];}, $cidades))->get();
            // Log::info($cities);
            $banner->cities()->sync($cities);
        } else {
            $banner->cities()->detach();
        }

        return $banner->toArray();
    }

    public function delete(int $id): void
    {
        $banner = Banner::findOrFail($id);

        $bucketOldFilePath = self::getBucketFilePath($banner->filename);
        Storage::delete($bucketOldFilePath);

        $banner->cities()->detach();
        $banner->delete();
    }

    protected static function getBucketFilePath(?string $filename = null): string
    {
        $filepath = sprintf(
            'uploads%sbanners',
            DIRECTORY_SEPARATOR,
        );

        if (!empty($filename)) {
            $filepath = sprintf(
                '%s%s%s',
                $filepath,
                DIRECTORY_SEPARATOR,
                $filename,
            );
        }

        return $filepath;
    }

    protected static function getBucketFilename(UploadedFile $file): string
    {
        return sprintf('%s.%s', Str::ulid(), $file->getClientOriginalExtension());
    }

    public function duplicate(int $id): void
    {
        $original = Banner::where('id', $id)->with('cities')->first();

        $copy = $original->replicate();

        if ($original->filename) {
            $bucketBasePath = self::getBucketFilePath();
            $originalPath = $bucketBasePath . '/' . $original->filename;
        
            if (Storage::exists($originalPath)) {
                // Gerar novo nome para o arquivo
                $newFilename = 'copy_' . time() . '_' . $original->filename;
        
                // Novo caminho completo
                $newPath = $bucketBasePath . '/' . $newFilename;
        
                // Copiar o arquivo
                Storage::copy($originalPath, $newPath);
        
                // Atualizar atributos da cópia
                $copy->filename = $newFilename;
                $copy->cdn_url = Storage::url($newPath);
                $copy->original_filename = $original->original_filename; // opcional
            }
        }

        $copy->save();

        // Copiar cidades (relação many-to-many)
        $cityIds = $original->cities->pluck('id');
        $copy->cities()->sync($cityIds);
    }
}

<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MobileSettings extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [ 'company_id', 'logo_path', 'color_palette', 'dark_color_palette', 'vertical_menu', 'horizontal_menu', 'dashboard', 'firebase_token_notification'];

    protected $cast = ['vertical_menu' => 'array', 'horizontal_menu' => 'array', 'dashboard' => 'array' ];

    public static function createMobileSetting($request)
    {
        $company_id = Auth::user()->company_id;
        $data = $request->validated();
        $data['company_id'] = $company_id;
        $mobileSetting = self::select('*')->where('company_id', $company_id)->get();
        //Log::debug('----');
        //Log::debug($mobileSetting);

        if (sizeof($mobileSetting) > 0) {

            $mobileSetting = $mobileSetting[0];
            $mobileSetting->update($request->validated());

            if ($request->hasFile('logo')) {
                $mobileSetting->clearMediaCollection('logo-mobile');
            }

        } else {
            $mobileSetting = self::create($data);
        }

        if ($request->hasFile('logo')) {
            /* //Log::debug('tiene logo');
            //Log::debug(gettype($mobileSetting));
            //Log::debug($mobileSetting); */
            $mobileSetting->addMedia($request->logo)->toMediaCollection('logo-mobile', 'local');
            $media = $mobileSetting->getMedia('logo-mobile');
            foreach ($media as $file) {
                $mobileSetting->logo_path = url($file->getUrl());
            }

        }
        $mobileSetting->save();

        return $mobileSetting;
    }
}

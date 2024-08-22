<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CompanyRequest;
use Crater\Http\Requests\ProfileRequest;
use Crater\Models\BwConfig;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Modules;
use Crater\Models\Setting;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;

class CompanyController extends Controller
{
    public function getUser(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "getUser");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $user = Auth::user();

        $user->load([
            'addresses',
            'addresses.country',
            'company',
            'company.address',
            'company.address.country',
        ]);

        $setting = Setting::where(
            'option',
            'company_page_title',
            'primary_color',
        )->first();

        $settingsCompany = CompanySetting::getSettings([
            'enable_credit_customer',
            'enable_invoice_customer',
            'enable_make_customer',
            'enable_payment_customer',
            'enable_paymentaccount_customer',
            'enable_pbxservice_customer',
            'enable_quotes_customer',
            'enable_report_customer',
            'enable_service_customer',
            'enable_tickets_customer',

        ], $user->company_id);

        $bandwidthModule = Modules::where('company_id', $user->company_id)->where('name', 'Bandwidth')->first();
        if ($bandwidthModule) {
            $bwConfigActive = BwConfig::where('is_default', 1)->first();
            if ($bwConfigActive) {
                $bandwidth = true;
            } else {
                $bandwidth = false;
            }
        } else {
            $bandwidth = false;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
        ], "message" => "getUser"];
        LogsDev::finishLog($log, $res, $time, 'D', "getUser");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Company", "View", "admin/settings/user-profile", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User admin: ".$user->name);

        return response()->json([
            'user' => $user,
            'setting' => $setting,
            'settingsCompany' => $settingsCompany,
            'modules' => [
                'bandwidth' => $bandwidth,
            ],
        ]);
    }

    /**
     * Update the Admin profile.
     * Includes name, email and (or) password
     *
     * @param  \Crater\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(ProfileRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "updateProfile");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $user = Auth::user();

        $user->update($request->validated());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "updateProfile"];
        LogsDev::finishLog($log, $res, $time, 'D', "updateProfile");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Company", "Update", "admin/settings/user-profile", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User admin: ".$user->name);

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    /**
     * Update Admin Company Details
     * @param \Crater\Http\Requests\CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(CompanyRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "updateCompany");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Auth::user()->company;

        $company->update($request->only('name', 'company_identifier', 'title_header', 'subtitle_header', 'footer_text_value', 'footer_url_value', 'footer_url_name', 'current_year', 'url_wallpaper_login', 'hide_title'));

        $company->address()->updateOrCreate(['company_id' => $company->id], $request->except(['name']));
        $direccion = $company->address()->first();
        $direccion->state_id = $request->billing_state["id"];
        $direccion->save();

        $settings = [];
        $settings['header_color'] = $request->header_color;
        $settings['primary_color'] = $request->primary_color;
        $settings['color_invoice'] = $request->color_invoice;
        $settings['footer_text_value'] = $request->footer_text_value;
        $settings['footer_url_value'] = $request->footer_url_value;
        $settings['footer_url_name'] = $request->footer_url_name;
        $settings['current_year'] = $request->current_year;
        $settings['hide_title'] = $request->hide_title;
        $settings['company_report_info'] = $request->company_report_info != '' ? $request->company_report_info : '';
        CompanySetting::setSettings($settings, $company->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'company' => $company,
            'success' => true,
        ], "message" => "updateCompany"];
        LogsDev::finishLog($log, $res, $time, 'D', "updateCompany");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Company", "Update", "admin/settings/company-info", $company->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Company: ".$company->name);

        return response()->json([
            'company' => $company,
            'success' => true,
        ]);
    }

    /**
     * Upload the company logo to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyLogo(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "uploadCompanyLogo");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = json_decode($request->company_logo);

        if ($data) {
            $company = Company::find($request->header('company'));

            if ($company) {
                // Logs por modulo
                LogsModule::createLog("Company", "Update", "admin/settings/company-info", $company->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Company update logo: ".$company->name);

                $company->clearMediaCollection('logo');

                $company->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('logo');

                $company->logo = $data->data;
                $company->save();
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadCompanyLogo"];
        LogsDev::finishLog($log, $res, $time, 'D', "uploadCompanyLogo");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    // uploadWallpaper
    public function uploadWallpaper(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "uploadWallpaper");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = json_decode($request->url_wallpaper_login);

        if ($data) {
            $company = Company::find($request->header('company'));

            if ($company) {
                // Logs por modulo
                LogsModule::createLog("Company", "Update", "admin/settings/company-info", $company->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Company update wallpaper: ".$company->name);

                $company->clearMediaCollection('wallpaperLogin');

                $company->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('wallpaperLogin');

                $company->url_wallpaper_login = $data->data;
                $company->save();
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadWallpaper"];
        LogsDev::finishLog($log, $res, $time, 'D', "uploadWallpaper");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Upload the Admin Avatar to public storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "uploadAvatar");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = json_decode($request->admin_avatar);

        if ($data) {
            $user = auth()->user();
            $user = User::find($user->id);
            if ($user) {

                // Logs por modulo
                LogsModule::createLog("Company", "Update", "admin/settings/user-profile", $user->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "User admin upload avatar: ".$user->name);

                $user->clearMediaCollection('admin_avatar');

                $user->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('admin_avatar');

                $user->avatar = $data->data;
                $user->save();
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'user' => $user,
            'success' => true,
        ], "message" => "uploadAvatar"];
        LogsDev::finishLog($log, $res, $time, 'D', "uploadAvatar");
        /////////////////////////////////////////

        return response()->json([
            'user' => $user,
            'success' => true,
        ]);
    }

    public function getCompanyLogo(Request $request)
    {
        try {

            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
            $time = microtime(true);
            $log = LogsDev::initLog($request, "", "D", "CompanyController", "getCompanyLogo");
            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

            $user = User::first();
            $user->load([
                'addresses',
                'addresses.country',
                'company',
                'company.address',
                'company.address.country',
            ]);
            $primary_color = CompanySetting::getSettings(['primary_color'], $user->company->id);
            if ($primary_color) {
                $user->company['primary_color'] = $primary_color['primary_color'];
            }

            $company = $user->company;

            if (is_null($company)) {
                return [];
            }

            $company->success = true;

            // verify if the exists image, added new object for validate in the front
            $mediaImage = $user->company->media->where('collection_name', 'wallpaperLogin')->first();
            Log::debug('media image', [$mediaImage]);
            if ($mediaImage != null) {
                if (Storage::disk('public')->exists($mediaImage->id.'/'.$mediaImage->file_name)) {
                    $company->wallpaper_login_exists = true;
                } else {
                    $company->wallpaper_login_exists = false;
                }
            } else {
                $company->wallpaper_login_exists = false;
            }

            return $company;

            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [
                'user' => $user,
            ], "message" => "getCompanyLogo"];
            LogsDev::finishLog($log, $res, $time, 'D', "getCompanyLogo");
            /////////////////////////////////////////

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Upload the company page title to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyPageTitle(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "uploadCompanyPageTitle");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $set = Setting::where('option', 'company_page_title')->first();

        if ($set) {
            $set->option = 'company_page_title';
            $set->value = $request->input('company_page_title');
            $set->save();
        } else {
            $set = new Setting();
            $set->option = 'company_page_title';
            $set->value = $request->input('company_page_title');
            $set->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadCompanyPageTitle"];
        LogsDev::finishLog($log, $res, $time, 'D', "uploadCompanyPageTitle");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Upload the company favicon to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCompanyFavicon(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CompanyController", "uploadCompanyFavicon");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = json_decode($request->company_favicon);

        if ($data) {
            $company = Company::find($request->header('company'));

            if ($company) {
                // Logs por modulo
                LogsModule::createLog("Company", "Update", "admin/settings/company-info", $company->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Company update logo: ".$company->name);

                $company->clearMediaCollection('favicon');

                $company->addMediaFromBase64($data->data)
                    ->usingFileName($data->name)
                    ->toMediaCollection('favicon');
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "uploadCompanyFavicon"];
        LogsDev::finishLog($log, $res, $time, 'D', "uploadCompanyFavicon");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }
}

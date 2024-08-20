<?php

namespace Crater\Http\Controllers\V1\Provider;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteProviderRequest;
use Crater\Http\Requests\ProviderRequest;
use Crater\Models\Country;
use Crater\Models\Expense;
use Crater\Models\LogsDev;
use Crater\Models\Provider;
use Crater\Models\State;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Inicio del registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "index");

        // Obtener el límite de la paginación
        $limit = $request->input('limit', 10);

        // Aplicar filtros y obtener proveedores
        $providerQuery = Provider::applyFilters($request->only([
            'search',
            'providers_number',
            'title',
            'phone',
            'email',
            'orderByField',
            'orderBy',
            'status',
        ]))
            ->leftJoin('countries', 'countries.id', '=', 'providers.country_id')
            ->leftJoin('states', 'states.id', '=', 'providers.state_id')
            ->select('providers.*', 'countries.name as country_name', 'states.name as state_name')
            ->latest();

        // Contar el total de proveedores activos una sola vez
        $providerTotalCount = Provider::whereStatus('A')->count();

        // Preparar la respuesta
        $responseData = [
            'providers' => $providerQuery->paginateData($limit),
            'providerTotalCount' => $providerTotalCount,
        ];

        // Registro de log
        $res = ["success" => true, "response" => ["datamesage" => $responseData, "message" => "Provider Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider Index");

        // Devolver la respuesta JSON
        return response()->json($responseData);
    }

    /**
     * Método exclusivo para cargar selectores de formularios con información de proveedores.
     * Este método se utiliza para poblar selectores en la interfaz de usuario, permitiendo la selección de proveedores.
     *
     * @param Request $request Datos de la solicitud que pueden incluir parámetros como 'limit'.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con los proveedores para el selector.
     */
    public function indexselectprovider(Request $request)
    {
        // Iniciar el registro de log para seguimiento de desarrollo.
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "index");

        // Definir el límite de proveedores por página, con un valor predeterminado de 10 si no se especifica en la solicitud.
        $limit = $request->input('limit', 10);

        // Construir la consulta para obtener proveedores, seleccionando solo los campos 'id' y 'title'.
        // La consulta se ordena por los más recientes y se aplica la paginación según el límite definido.
        $providerQuery = Provider::where("status", "A")->select('providers.id', 'providers.title')->latest();

        // Preparar los datos de respuesta con los proveedores obtenidos.
        $responseData = [
            'providers' => $providerQuery->paginateData($limit),
        ];

        // Finalizar el registro de log con los datos de respuesta y el tiempo transcurrido.
        $res = ["success" => true, "response" => ["datamesage" => $responseData, "message" => "Provider Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider Index");

        // Devolver la respuesta en formato JSON con los datos de los proveedores.
        return response()->json($responseData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProviderRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "store");
        ////////////////

        $provider = Provider::createProvider($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'provider' => $provider,
        ], "message" => "Provider store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'providers' => $provider,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "show");
        ////////////////

        $providers = Provider::where('providers.id', $id)
            ->leftJoin('countries', 'countries.id', 'providers.country_id')
            ->leftJoin('states', 'states.id', 'providers.state_id')
            ->select('providers.*', 'countries.id as country_id', 'countries.name as country', 'states.id as state_id', 'states.name as state')
            ->first();

        if ($providers['country_id'] != null) {
            $country = Country::where('id', $providers['country_id'])->first();
            $providers['countries'] = $country;
        }

        if ($providers['state_id'] != null) {
            $state = State::where('id', $providers['state_id'])->first();
            $providers['states'] = $state;
        }

        switch ($providers['status']) {
            case 'A':
                $providers['status'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $providers['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            default:
                break;
        }

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'providers' => $providers,
        ], "message" => "Provider show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'providers' => $providers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(ProviderRequest $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "update");
        ////////////////

        $providers = Provider::find($id);

        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['creator_id'] = Auth::user()->id;
        $providers->update($data);
        $providers->title = $data['title'];
        $providers->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'providers' => $providers,
        ], "message" => "Provider update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'providers' => $providers,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteProviderRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "destroy");
        //////////////////

        // Validate isExist provider associated
        $isExist = Expense::whereIn("providers_id", $request["ids"])->get();
        if ($isExist->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'The provider is associated with an expense',
            ]);
        }
        // Delete provider
        Provider::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Provider deleted successfully',
        ], "message" => "Provider deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Provider deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => true,
            'message' => 'Provider deleted successfully',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function setPrefix(Request $request)
    {
        //
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProviderController", "setPrefix");
        ///////////////////////////////////////

        $prefix = $request->prov_prefix;
        $providerlist = Provider::all();

        if (! isset($prefix) || trim($prefix) === '') {

            $providerlist->each(function ($pro) use ($prefix) {
                $up = Provider::find($pro['id']);
                $up->providers_number = $prefix.'-000'.$pro['id'];
                $up->save();

            });

        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "setPrefix"]];
        LogsDev::finishLog($log, $res, $time, 'D', "setPrefix");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);

    }
}

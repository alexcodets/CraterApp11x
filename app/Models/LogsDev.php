<?php

namespace Crater\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Log;

class LogsDev extends Model
{
    use HasFactory;

    protected $table = 'corebill_logs_dev';

    protected $fillable = [
        'ip',
        'auth',
        'request',
        'controller',
        'method',
        'data_in',
        'data_out',
        'message',
        'time',
        'type',
        'date_reg'
    ];

    public $timestamps = false;

    public static function isEnable(): bool
    {
        return CompanySetting::getSetting('dev_log_activation ', auth()->user()->company_id ?? null) ?? false;
    }

    /**
     * Metodo para inicializar un registro de log
     * @param Request $request
     * @param bool $message opcional, si se envía se agrega un mesanje al log
     * @param string $type opcional, sirve para marcar si el log es un error(E) o solo debug(D)
     * @param bool $controller opcional, si se envía se agrega el controlador al log
     * @param bool $method opcional, si se envía se agrega el método al log
     *
     * @return LogsDev|null
     */
    public static function initLog(Request $request, $message = false, $type = 'D', $controller = false, $method = false)
    {

        if (! self::isEnable()) {
            return null;
        }

        $log = new LogsDev();

        $log->ip = $request->ip();
        $log->request = $request->fullUrl();
        $log->data_in = (string)json_encode($request->input());
        $log->headers = (string)json_encode($request->header());

        if (! empty($request->bearerToken())) {
            $log->auth = $request->bearerToken();
        }

        if (! empty($request->header('company'))) {
            $log->company = $request->header('company');
        }

        if ($message) {
            $log->message = $message;
        }

        if ($controller) {
            $log->controller = $controller;
        }

        if ($method) {
            $log->method = $method;
        }

        $log->type = $type;
        $log->date_reg = date('Y-m-d H:i:s');
        $log->save();

        return $log;
    }

    /**
     * Metodo para finalizar un registro de log
     * @param Crater\Models\Log $log objeto devuelto por el metodo initLog
     * @param Array $response opcional, si se envía debe ser un array asociativo que se guardara como data enviada
     * @param Int $time opcional, si se envia se registra el tiempo que tardo el request en optener una respuesta
     * @param Char $type opcional, sirve para marcar si el log es un error(E) o solo debug(D)
     * @param String $message opcional, si se envía se agrega un mesanje al log
     *
     * @return Crater\Models\Log
     */
    public static function finishLog(LogsDev $log = null, $response = [], $time = 0, $type = 'D', $message = false)
    {

        if (! self::isEnable()) {
            return null;
        }

        if ($time > 0) {
            $log->time = round((microtime(true) - $time), 2);
        }

        if ($message) {
            $log->message = ! empty($log->message) ? ($log->message.' '.$message) : $message;
        }

        $log->type = $type;

        $log->data_out = (string)json_encode($response);
        $log->save();

        return $log;
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }
}

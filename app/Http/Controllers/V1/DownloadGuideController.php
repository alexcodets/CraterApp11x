<?php

namespace Crater\Http\Controllers\V1;

use Illuminate\Support\Facades\Response;

class DownloadGuideController
{
    //

    public function downloadGuide()
    {
        $filePath = public_path('assets/pdf/invoice_import_guide.pdf');

        // Verificar si el archivo existe
        if (file_exists($filePath)) {

            chmod($filePath, 0644);
            chmod(dirname($filePath), 0755);

            // Descargar el archivo
            return Response::download($filePath, 'invoice_import_guide.pdf');
        } else {
            // Si el archivo no existe, devolver un error 404
            abort(404);
        }
    }

    public function downloadCsv()
    {
        $filePath = public_path('assets/csv/CsvTestExample.csv');

        // Verificar si el archivo existe
        if (file_exists($filePath)) {

            chmod($filePath, 0644);
            chmod(dirname($filePath), 0755);

            // Descargar el archivo
            return Response::download($filePath, 'CsvTestExample.csv');
        } else {
            // Si el archivo no existe, devolver un error 404
            abort(404);
        }
    }
}

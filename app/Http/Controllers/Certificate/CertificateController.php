<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificateController extends Controller
{
    private string $templatePath;
    private string $tempDir;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/cert_template.docx');
        $this->tempDir      = storage_path('app/temp');
    }

    public function index()
    {
        return view('certificate', ['pdfAvailable' => PHP_OS_FAMILY !== 'Windows']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'cert_number'  => ['required'],
            'zavod_number' => ['required'],
            'make_year'    => ['required'],
            'fio_address'  => ['required'],
            'water_data'   => ['required', 'numeric'],
            'class'        => ['required'],
            'check_date'   => ['required', 'date_format:d.m.Y'],
            'plomb_number'   => ['required'],
        ], [
            'check_date.date_format' => 'Дата поверки: формат ДД.ММ.ГГГГ',
            'water_data.numeric'     => 'Показания счётчика должны быть числом',
        ]);
        $data['final_date'] = Carbon::createFromFormat('d.m.Y', $data['check_date'])
            ->addYears(5)
            ->format('d.m.Y');

        return $data;
    }

    private function buildDocx(array $data): string
    {
        abort_unless(file_exists($this->templatePath), 500, 'Шаблон не найден: app/templates/cert_template.docx');

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $processor = new TemplateProcessor($this->templatePath);
        $processor->setValue('cert_number',  $data['cert_number']);
        $processor->setValue('zavod_number', $data['zavod_number']);
        $processor->setValue('make_year',    $data['make_year']);
        $processor->setValue('fio_address',  $data['fio_address']);
        $processor->setValue('water_data',   $data['water_data']);
        $processor->setValue('class',        $data['class']);
        $processor->setValue('check_date',   $data['check_date']);
        $processor->setValue('final_date',   $data['final_date']);
        $processor->setValue('plomb_number', $data['plomb_number']);

        $path = $this->tempDir . DIRECTORY_SEPARATOR . 'cert_' . Str::uuid() . '.docx';
        $processor->saveAs($path);

        return $path;
    }

    public function downloadWord(Request $request): BinaryFileResponse
    {
        $data     = $this->validated($request);

        $docxPath = $this->buildDocx($data);

        $filename = sprintf(
            'cert_%s_%s.docx',
            $data['zavod_number'],
            str_replace('.', '', $data['check_date'])
        );

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    public function downloadPdf(Request $request)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return response()->json([
                'error' => 'Конвертация в PDF недоступна на Windows. Скачайте файл в формате WORD.',
            ], 501);
        }

        $debug = [];

        try {
            $debug['os']           = PHP_OS_FAMILY;
            $debug['template']     = $this->templatePath;
            $debug['template_exists'] = file_exists($this->templatePath);
            $debug['temp_dir']     = $this->tempDir;
            $debug['temp_writable'] = is_writable(dirname($this->tempDir)) || is_writable($this->tempDir);

            $data     = $this->validated($request);
            $docxPath = $this->buildDocx($data);
            $pdfDir   = dirname($docxPath);

            $debug['docx_path']   = $docxPath;
            $debug['docx_exists'] = file_exists($docxPath);

            $libreoffice = trim((string) shell_exec('which libreoffice 2>&1'));
            $debug['libreoffice_path'] = $libreoffice ?: 'NOT FOUND';

            $command = sprintf(
                'HOME=/tmp libreoffice --headless --norestore --nofirststartwizard --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg($pdfDir),
                escapeshellarg($docxPath)
            );
            $debug['command'] = $command;

            $cmdOutput = shell_exec($command);
            $debug['cmd_output'] = $cmdOutput;

            @unlink($docxPath);

            $pdfPath = substr($docxPath, 0, -5) . '.pdf';
            $debug['pdf_path']   = $pdfPath;
            $debug['pdf_exists'] = file_exists($pdfPath);

            if (!file_exists($pdfPath)) {
                return response()->json(['debug' => $debug], 500);
            }

            $filename = sprintf(
                'cert_%s_%s.pdf',
                $data['zavod_number'],
                str_replace('.', '', $data['check_date'])
            );

            return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            $debug['exception'] = $e->getMessage();
            $debug['file']      = $e->getFile() . ':' . $e->getLine();
            $debug['trace']     = $e->getTraceAsString();

            return response()->json(['debug' => $debug], 500);
        }
    }
}

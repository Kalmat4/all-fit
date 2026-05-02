<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificateController extends Controller
{
    private string $templatePath;
    private string $scriptPath;
    private string $tempDir;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/cert_temp.pdf');
        $this->scriptPath   = base_path('scripts/generate_cert.py');
        $this->tempDir      = storage_path('app/temp');
    }

    public function index()
    {
        return view('certificate');
    }

    public function generate(Request $request): BinaryFileResponse
    {
        $data = $request->validate([
            'cert_num'  => ['required', 'string', 'max:50'],
            'serial'    => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'mfg_year'  => ['required', 'string', 'max:10'],
            'user'      => ['required', 'string', 'max:200'],
            'acc_class' => ['required', 'string', 'max:5'],
            'date_from' => ['required', 'date_format:d.m.Y'],
        ], [
            'serial.regex'          => 'Заводской номер: только цифры',
            'date_from.date_format' => 'Дата поверки: формат ДД.ММ.ГГГГ',
        ]);

        abort_unless(file_exists($this->templatePath), 500, 'Шаблон PDF не найден');
        abort_unless(file_exists($this->scriptPath),   500, 'Скрипт генерации не найден');

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $outputFilename = 'cert_' . Str::uuid() . '.pdf';
        $outputPath     = $this->tempDir . DIRECTORY_SEPARATOR . $outputFilename;

        // Слеши всегда forward — Python их понимает и на Windows и на Linux
        $payload = json_encode([
            'cert_num'  => $data['cert_num'],
            'serial'    => $data['serial'],
            'mfg_year'  => $data['mfg_year'],
            'user'      => $data['user'],
            'acc_class' => $data['acc_class'],
            'date_from' => $data['date_from'],
            'template'  => str_replace('\\', '/', $this->templatePath),
            'output'    => str_replace('\\', '/', $outputPath),
        ], JSON_UNESCAPED_UNICODE);

        // На Windows python3 может не существовать — пробуем оба варианта
        $python  = $this->resolvePython();
        $script  = str_replace('\\', '/', $this->scriptPath);
        $command = sprintf('%s %s %s 2>&1',
            $python,
            escapeshellarg($script),
            escapeshellarg($payload)
        );

        $scriptOutput = shell_exec($command);

        $realOutput = str_replace('/', DIRECTORY_SEPARATOR, $outputPath);

        if (!file_exists($realOutput)) {
            logger()->error('Certificate generation failed', [
                'python'   => $python,
                'command'  => $command,
                'output'   => $scriptOutput,
            ]);
            abort(500, 'Ошибка генерации PDF: ' . $scriptOutput);
        }

        $downloadName = sprintf('cert_%s_%s.pdf',
            $data['serial'],
            str_replace('.', '', $data['date_from'])
        );

        return response()
            ->download($realOutput, $downloadName)
            ->deleteFileAfterSend(true);
    }

    /**
     * Найти исполняемый файл Python в системе
     */
    private function resolvePython(): string
    {
        // Сначала смотрим .env: PYTHON_BIN=C:/Python311/python.exe
        if ($bin = env('PYTHON_BIN')) {
            return escapeshellarg($bin);
        }

        // Пробуем стандартные имена
        foreach (['python3', 'python'] as $candidate) {
            $test = shell_exec(sprintf('%s --version 2>&1', $candidate));
            if ($test && str_contains($test, 'Python')) {
                return $candidate;
            }
        }

        // Типичный путь на Windows если Python установлен из Microsoft Store
        $windowsFallback = 'C:/Users/' . get_current_user() . '/AppData/Local/Programs/Python/Python311/python.exe';
        if (file_exists($windowsFallback)) {
            return escapeshellarg($windowsFallback);
        }

        abort(500, 'Python не найден. Укажи PYTHON_BIN в .env');
    }
}
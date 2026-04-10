<?php

use App\Services\Document\DocxReader;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('docx:read {path : Chemin vers un fichier .docx}', function (string $path) {
    $candidatePath = $path;

    $isWindowsAbsolute = preg_match('/^[A-Za-z]:\\\\/', $path) === 1;
    $isUnixAbsolute = str_starts_with($path, '/');

    if (! $isWindowsAbsolute && ! $isUnixAbsolute) {
        $candidatePath = base_path($path);
    }

    try {
        $text = app(DocxReader::class)->readTextFromPath($candidatePath);
    } catch (\Throwable $exception) {
        $this->error($exception->getMessage());

        return self::FAILURE;
    }

    if ($text === '') {
        $this->warn('Document lu, mais aucun texte detecte.');

        return self::SUCCESS;
    }

    $this->line($text);

    return self::SUCCESS;
})->purpose('Lit un fichier .docx et affiche son texte en console');

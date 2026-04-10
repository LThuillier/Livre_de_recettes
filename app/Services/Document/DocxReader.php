<?php

namespace App\Services\Document;

use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;
use ZipArchive;

class DocxReader
{
    public function readTextFromPath(string $path): string
    {
        if (! is_file($path)) {
            throw new InvalidArgumentException("Fichier introuvable: {$path}");
        }

        if (strtolower((string) pathinfo($path, PATHINFO_EXTENSION)) !== 'docx') {
            throw new InvalidArgumentException('Le fichier doit etre au format .docx');
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($path);

        if ($openResult !== true) {
            throw new RuntimeException('Impossible d\'ouvrir le fichier .docx');
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($documentXml === false) {
            throw new RuntimeException('Le fichier .docx ne contient pas word/document.xml');
        }

        return $this->extractPlainText($documentXml);
    }

    private function extractPlainText(string $documentXml): string
    {
        $dom = new DOMDocument();
        $loaded = $dom->loadXML($documentXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);

        if (! $loaded) {
            throw new RuntimeException('Impossible de lire le contenu XML du .docx');
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraphs = $xpath->query('//w:body/w:p');
        if ($paragraphs === false) {
            return '';
        }

        $lines = [];

        foreach ($paragraphs as $paragraph) {
            $tokens = $xpath->query('.//w:t | .//w:tab | .//w:br | .//w:cr', $paragraph);
            if ($tokens === false) {
                continue;
            }

            $line = '';

            foreach ($tokens as $token) {
                $nodeName = $token->nodeName;

                if ($nodeName === 'w:tab') {
                    $line .= "\t";
                    continue;
                }

                if ($nodeName === 'w:br' || $nodeName === 'w:cr') {
                    $line .= PHP_EOL;
                    continue;
                }

                $line .= $token->textContent;
            }

            $normalized = preg_replace('/[ \t]+/u', ' ', $line);
            $normalized = is_string($normalized) ? trim($normalized) : '';

            if ($normalized !== '') {
                $lines[] = $normalized;
            }
        }

        return trim(implode(PHP_EOL, $lines));
    }
}


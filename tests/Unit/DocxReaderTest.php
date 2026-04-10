<?php

namespace Tests\Unit;

use App\Services\Document\DocxReader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use ZipArchive;

class DocxReaderTest extends TestCase
{
    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_extrait_le_texte_depuis_un_docx_valide(): void
    {
        $path = $this->createDocxWithXml(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body>'
            . '<w:p><w:r><w:t>Bonjour</w:t></w:r></w:p>'
            . '<w:p><w:r><w:t>Monde</w:t></w:r></w:p>'
            . '</w:body>'
            . '</w:document>'
        );

        $reader = new DocxReader();
        $text = $reader->readTextFromPath($path);

        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        $this->assertSame("Bonjour\nMonde", $normalized);
    }

    public function test_le_lecteur_refuse_un_fichier_non_docx(): void
    {
        $path = $this->createTempFile('not-a-docx', 'fichier texte');

        $reader = new DocxReader();

        $this->expectException(InvalidArgumentException::class);
        $reader->readTextFromPath($path);
    }

    public function test_le_lecteur_refuse_un_docx_sans_document_xml(): void
    {
        $path = $this->createEmptyDocx();

        $reader = new DocxReader();

        $this->expectException(RuntimeException::class);
        $reader->readTextFromPath($path);
    }

    private function createDocxWithXml(string $documentXml): string
    {
        $path = $this->createTempFile('.docx');

        $zip = new ZipArchive();
        $openResult = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            $this->fail('Impossible de creer le fichier .docx de test');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString(
            '_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>'
        );
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        return $path;
    }

    private function createEmptyDocx(): string
    {
        $path = $this->createTempFile('.docx');

        $zip = new ZipArchive();
        $openResult = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            $this->fail('Impossible de creer le fichier .docx de test');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->close();

        return $path;
    }

    private function createTempFile(string $suffix, string $content = ''): string
    {
        $path = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . 'docx-reader-'
            . uniqid('', true)
            . $suffix;

        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return $path;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '</Types>';
    }
}

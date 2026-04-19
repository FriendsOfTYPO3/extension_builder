<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\Tests\Unit\Service;

use EBT\ExtensionBuilder\Service\ClassBuilder;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\LocalizationService;
use EBT\ExtensionBuilder\Service\Printer;
use EBT\ExtensionBuilder\Service\RoundTrip;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use ReflectionClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FileGeneratorXlfTest extends UnitTestCase
{
    private FileGenerator $fileGenerator;
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup('root');

        $this->fileGenerator = new FileGenerator(
            $this->createMock(ClassBuilder::class),
            $this->createMock(RoundTrip::class),
            $this->createMock(Printer::class),
            $this->createMock(LocalizationService::class),
            $this->createMock(ViewFactoryInterface::class),
            $this->createMock(ExtensionConfiguration::class),
        );
    }

    private function callXlfContentIsUnchanged(string $targetFile, string $newContent): bool
    {
        $ref = new ReflectionClass($this->fileGenerator);
        $method = $ref->getMethod('xlfContentIsUnchanged');
        return $method->invoke($this->fileGenerator, $targetFile, $newContent);
    }

    private function xlf(string $date, string $label = 'Hello'): string
    {
        return <<<XML
            <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
            <xliff version="1.0">
                <file source-language="en" datatype="plaintext" original="locallang.xlf" date="{$date}" product-name="my_ext">
                    <header/>
                    <body>
                        <trans-unit id="general.yes" resname="general.yes">
                            <source>{$label}</source>
                        </trans-unit>
                    </body>
                </file>
            </xliff>
            XML;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returnsFalseWhenFileDoesNotExist(): void
    {
        $path = vfsStream::url('root/locallang.xlf');
        self::assertFalse($this->callXlfContentIsUnchanged($path, $this->xlf('2025-01-01T00:00:00Z')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returnsTrueWhenOnlyDateAttributeDiffers(): void
    {
        $path = vfsStream::url('root/locallang.xlf');
        vfsStream::newFile('locallang.xlf')
            ->withContent($this->xlf('2020-01-01T00:00:00Z'))
            ->at($this->vfsRoot);

        self::assertTrue($this->callXlfContentIsUnchanged($path, $this->xlf('2026-04-11T12:00:00Z')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returnsFalseWhenLabelChanged(): void
    {
        $path = vfsStream::url('root/locallang.xlf');
        vfsStream::newFile('locallang.xlf')
            ->withContent($this->xlf('2020-01-01T00:00:00Z', 'Hello'))
            ->at($this->vfsRoot);

        self::assertFalse($this->callXlfContentIsUnchanged($path, $this->xlf('2026-04-11T12:00:00Z', 'Goodbye')));
    }
}

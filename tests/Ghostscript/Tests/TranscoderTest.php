<?php

namespace Ghostscript\Tests;

use Ghostscript\Transcoder;

class TranscoderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transcoder */
    protected $object;

    protected function setUp()
    {
        $this->object = Transcoder::create();
    }

    public function testTranscodeToPdf()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.pdf';
        $this->object->toPDF(__DIR__ . '/../../files/test.pdf', $dest, 1, 1);

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(0, filesize($dest));

        unlink($dest);
    }

    public function testTranscodeMergeMultiplePdfsToPdf()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.pdf';

        $inputFile1 = __DIR__ . '/../../files/test2.pdf';
        $inputFile2 = __DIR__ . '/../../files/test3.pdf';

        $this->object->toPDF(array($inputFile1, $inputFile2), $dest, 1, 1);

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(max(filesize($inputFile1), filesize($inputFile2)), filesize($dest));

        unlink($dest);
    }

    public function testTranscodeAIToImage()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.jpg';
        $this->object->toImage(__DIR__ . '/../../files/test.pdf', $dest);

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(0, filesize($dest));

        unlink($dest);
    }
}

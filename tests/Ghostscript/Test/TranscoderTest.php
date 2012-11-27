<?php

namespace Ghostscript\Test;

use Ghostscript\Transcoder;

class TranscoderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = Transcoder::load();
    }

    public function testTranscodeToPdf()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.pdf';
        $this->object->open(__DIR__ . '/../../files/test.pdf')
            ->toPDF($dest, 1, 1)
            ->close();

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(0, filesize($dest));

        unlink($dest);
    }

    public function testTranscodeAIToImage()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.jpg';
        $this->object->open(__DIR__ . '/../../files/plane.ai')
            ->toImage($dest)
            ->close();

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(0, filesize($dest));

        unlink($dest);
    }
}

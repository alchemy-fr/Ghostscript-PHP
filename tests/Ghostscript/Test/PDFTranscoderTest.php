<?php

namespace Ghostscript\Test;

use Ghostscript\PDFTranscoder;

class PDFTranscoderTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = PDFTranscoder::load();
    }

    public function testTranscode()
    {
        $dest = tempnam(sys_get_temp_dir(), 'gs_temp') . '.pdf';
        $this->object->open(__DIR__ . '/../../files/test.pdf')
            ->transcode($dest, 1, 1)
            ->close();

        $this->assertTrue(file_exists($dest));
        $this->assertGreaterThan(0, filesize($dest));

        unlink($dest);
    }
}

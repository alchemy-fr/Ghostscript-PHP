<?php

namespace Ghostscript\Test;

use Ghostscript\GhostscriptServiceProvider;
use Silex\Application;

class GhostscriptServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application;
        $app->register(new GhostscriptServiceProvider());

        $this->assertInstanceOf('\\Ghostscript\\Transcoder', $app['ghostscript.transcoder']);
    }
}


<?php

namespace Ghostscript;

use Silex\ServiceProviderInterface;
use Silex\Application;

class GhostscriptServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['ghostscript.binary'] = null;
        $app['ghostscript.logger'] = null;
        $app['ghostscript.timeout'] = 0;

        $app['ghostscript.transcoder'] = $app->share(function(Application $app) {

            if (null !== $app['ghostscript.logger']) {
                $logger = $app['ghostscript.logger'];
            } else {
                $logger = null;
            }

            if (null === $app['ghostscript.binary']) {
                return Transcoder::create($logger, array('timeout' => $app['ghostscript.timeout']));
            } else {
                return Transcoder::load($app['ghostscript.binary'], $logger, array('timeout' => $app['ghostscript.timeout']));
            }
        });
    }

    public function boot(Application $app)
    {
    }
}

<?php

namespace Ghostscript;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Silex\ServiceProviderInterface;
use Silex\Application;

class GhostscriptServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['ghostscript.binary'] = null;
        $app['ghostscript.logger'] = null;

        if (isset($app['monolog'])) {
            $app['ghostscript.logger'] = function() use ($app) {
                return $app['monolog'];
            };
        }

        $app['ghostscript.transcoder'] = $app->share(function(Application $app) {

            if ($app['ghostscript.logger']) {
                $logger = $app['ghostscript.logger'];
            } elseif (isset($app['monolog'])) {
                $logger = $app['monolog'];
            } else {
                $logger = new Logger('unoconv');
                $logger->pushHandler(new NullHandler());
            }

            if (!$app['ghostscript.binary']) {
                return Transcoder::load($logger);
            } else {
                return new PDFTranscoder($app['ghostscript.binary'], $logger);
            }
        });
    }

    public function boot(Application $app)
    {
    }
}

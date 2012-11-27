<?php

namespace Ghostscript;

use Ghostscript\Exception\RuntimeException;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

class Transcoder
{
    private $binary;
    private $logger;
    private $file;

    public function __construct($gs_binary, Logger $logger)
    {
        $this->binary = $gs_binary;
        $this->logger = $logger;
    }

    public function open($file)
    {
        $this->file = $file;

        return $this;
    }

    public function close()
    {
        $this->file = null;

        return $this;
    }

    public function toImage($destination)
    {
        $builder = ProcessBuilder::create(array(
                $this->binary,
                '-sDEVICE=jpeg',
                '-dNOPAUSE',
                '-dBATCH',
                '-dSAFER',
                '-sOutputFile=' . $destination,
                $this->file,
            ));

        $process = $builder->getProcess();
        $this->logger->addInfo(sprintf('Ghostscript about to run %s', $process->getCommandLine()));
        $process->run();
        
        if (!$process->isSuccessful() || !file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to Image');
        }

        return $this;
    }

    public function toPDF($destination, $pageStart, $pageQuantity)
    {
        $builder = ProcessBuilder::create(array(
                $this->binary,
                '-sDEVICE=pdfwrite',
                '-dNOPAUSE',
                '-dBATCH',
                '-dSAFER',
                sprintf('-dFirstPage=%d', $pageStart),
                sprintf('-dLastPage=%d', ($pageStart + $pageQuantity - 1)),
                '-sOutputFile=' . $destination,
                $this->file,
            ));

        $process = $builder->getProcess();
        $this->logger->addInfo(sprintf('Ghostscript about to run %s', $process->getCommandLine()));
        $process->run();

        if (!$process->isSuccessful() || !file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to PDF');
        }

        return $this;
    }

    public static function load(Logger $logger = null)
    {
        if (!$logger) {
            $logger = new Logger('Ghostscript logger');
            $logger->pushHandler(new NullHandler());
        }

        $finder = new ExecutableFinder();
        if (null === $gs = $finder->find('gs')) {
            throw new RuntimeException('gs does not seems present on this install');
        }

        return new static($gs, $logger);
    }
}

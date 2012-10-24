<?php

namespace Ghostscript;

use Ghostscript\Exception\RuntimeException;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

class PDFTranscoder
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

    public function transcode($destination, $start, $quantity)
    {
        $builder = ProcessBuilder::create(array(
                $this->binary,
                '-sDEVICE=pdfwrite',
                '-dNOPAUSE',
                '-dBATCH',
                '-dSAFER',
                sprintf('-dFirstPage=%d', $start),
                sprintf('-dLastPage=%d', ($start + $quantity - 1)),
                '-sOutputFile=' . $destination,
                $this->file,
            ));

        $process = $builder->getProcess();
        $this->logger->addInfo(sprintf('Ghostscript about to run %s', $process->getCommandLine()));
        $process->run();

        if (!$process->isSuccessful() || !file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode your PDF');
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

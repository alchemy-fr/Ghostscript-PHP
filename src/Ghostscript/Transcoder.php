<?php

namespace Ghostscript;

use Alchemy\BinaryDriver\AbstractBinary;
use Psr\Log\LoggerInterface;
use Ghostscript\Exception\RuntimeException;

class Transcoder extends AbstractBinary
{
    public function toImage($input, $destination)
    {
        $process = $this->factory->create(array(
            '-sDEVICE=jpeg',
            '-dNOPAUSE',
            '-dBATCH',
            '-dSAFER',
            '-sOutputFile=' . $destination,
            $input,
        ));

        $this->logger->addInfo(sprintf('Ghostscript about to run %s', $process->getCommandLine()));
        $process->run();

        if (!$process->isSuccessful() || !file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to Image');
        }

        return $this;
    }

    public function toPDF($input, $destination, $pageStart, $pageQuantity)
    {
        $process = $this->factory->create(array(
            '-sDEVICE=pdfwrite',
            '-dNOPAUSE',
            '-dBATCH',
            '-dSAFER',
            sprintf('-dFirstPage=%d', $pageStart),
            sprintf('-dLastPage=%d', ($pageStart + $pageQuantity - 1)),
            '-sOutputFile=' . $destination,
            $input,
        ));

        $this->logger->addInfo(sprintf('Ghostscript about to run %s', $process->getCommandLine()));
        $process->run();

        if (!$process->isSuccessful() || !file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to PDF');
        }

        return $this;
    }

    public static function create(LoggerInterface $logger = null, $configuration = array())
    {
        return static::load('gs', $logger, $configuration);
    }
}

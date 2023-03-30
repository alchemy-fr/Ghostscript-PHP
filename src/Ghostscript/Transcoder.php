<?php

namespace Ghostscript;

use Alchemy\BinaryDriver\AbstractBinary;
use Psr\Log\LoggerInterface;
use Ghostscript\Exception\RuntimeException;
use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ConfigurationInterface;
use Alchemy\BinaryDriver\Exception\ExecutionFailureException;

class Transcoder extends AbstractBinary
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ghostscript-transcoder';
    }

    /**
     * Transcode a PDF to an image.
     *
     * @param string  $input        The path to the input file.
     * @param string  $destination  The path to the output file.
     * @param integer $pageStart    The number of the first page.
     * @param integer $pageQuantity The number of page to include.
     *
     * @return Transcoder
     *
     * @throws RuntimeException In case of failure
     */
    public function toImage($input, $destination, $pageStart = 1, $pageQuantity = 1)
    {
        try {
            $this->command(array(
                '-sDEVICE=jpeg',
                '-dNOPAUSE',
                '-dBATCH',
                '-dSAFER',
                sprintf('-dFirstPage=%d', $pageStart),
                sprintf('-dLastPage=%d', ($pageStart + $pageQuantity - 1)),
                '-sOutputFile=' . $destination,
                $input,
            ));
        } catch (ExecutionFailureException $e) {
            throw new RuntimeException('Ghostscript was unable to transcode to Image', $e->getCode(), $e);
        }

        if (!file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to Image');
        }

        return $this;
    }

    /**
     * Transcode a PDF to another PDF
     *
     * @param string|array $input   The path to the input file(s).
     * @param string  $destination  The path to the output file.
     * @param integer $pageStart    The number of the first page.
     * @param integer $pageQuantity The number of page to include.
     *
     * @return Transcoder
     *
     * @throws RuntimeException In case of failure
     */
    public function toPDF($input, $destination, $pageStart, $pageQuantity)
    {
        try {
            $this->command(
                array_merge(
                    array(
                        '-sDEVICE=pdfwrite',
                        '-dNOPAUSE',
                        '-dBATCH',
                        '-dSAFER',
                        sprintf('-dFirstPage=%d', $pageStart),
                        sprintf('-dLastPage=%d', ($pageStart + $pageQuantity - 1)),
                        '-sOutputFile=' . $destination,
                    ),
                    (is_array($input) ? $input : array($input))
                )
            );
        } catch (ExecutionFailureException $e) {
            throw new RuntimeException('Ghostscript was unable to transcode to PDF', $e->getCode(), $e);
        }

        if (!file_exists($destination)) {
            throw new RuntimeException('Ghostscript was unable to transcode to PDF');
        }

        return $this;
    }

    /**
     * Creates a Transcoder.
     *
     * @param array|ConfigurationInterface $configuration
     * @param LoggerInterface              $logger
     *
     * @return Transcoder
     */
    public static function create($configuration = array(), LoggerInterface $logger = null)
    {
        if (!$configuration instanceof ConfigurationInterface) {
            $configuration = new Configuration($configuration);
        }

        $binaries = $configuration->get('gs.binaries', array('gs'));

        return static::load($binaries, $logger, $configuration);
    }
}

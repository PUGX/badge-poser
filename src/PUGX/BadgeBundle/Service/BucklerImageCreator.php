<?php
namespace PUGX\BadgeBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Process\ProcessBuilder;
use Imagine\Image\ImageInterface;
use PUGX\BadgeBundle\BucklerImage;

class BucklerImageCreator implements ImageCreatorInterface
{
    private static $definedColors = array(
        self::DOWNLOADS => 'blue',
        self::STABLE => '28a3df',
        self::UNSTABLE => 'e68718',
        self::ERROR => 'red',
        self::LICENSE => '428F7E'
    );
    private $logger;
    private $bucklerBinary;
    private $colors;

    public function __construct(Logger $logger, $bucklerBinary, $colors = array())
    {
        $this->bucklerBinary = $bucklerBinary;
        $this->logger = $logger;
        $this->colors = array_merge($colors, self::$definedColors);
    }

    private function createProcess(ImageInterface $image)
    {
        $array = $image->get('array');
        $builder = new ProcessBuilder(array($this->bucklerBinary, '-v', $array['vendor'], "-s", $array['status'], "-c", $array['color'], '-'));
        $builder->setTimeout(3600);

        return $builder->getProcess();
    }

    /**
     * Stream the output.
     *
     * @param  ImageInterface $image
     * @return bool|string
     *
     * @throws \RuntimeException
     */
    public function streamRawImageData(ImageInterface $image)
    {
        $process = $this->createProcess($image);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->critical('streamRawImageData:'  . $process->getErrorOutput());
            throw new \RuntimeException($process->getErrorOutput());
        }

        echo $process->getOutput();
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createDownloadsImage($value)
    {
        return new BucklerImage(self::DOWNLOADS, $value, $this->colors[self::DOWNLOADS]);
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value)
    {
        return new BucklerImage(self::STABLE, $value, $this->colors[self::STABLE]);
    }

    /**
     * Create the 'stable:no release' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return ImageInterface
     */
    public function createStableNoImage($value)
    {
        return $this->createImage(self::STABLE, $value, $this->colors[self::STABLE]);
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev')
    {
        return $this->createImage(self::UNSTABLE, $value, $this->colors[self::UNSTABLE]);
    }

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return ImageInterface
     */
    public function createErrorImage($value)
    {
        return $this->createImage(self::ERROR, $value, $this->colors[self::ERROR]);
    }

    /**
     * Create a License Image
     *
     * @param $value
     *
     * @return ImageInterface
     */
    public function createLicenseImage($value)
    {
        return new BucklerImage(self::LICENSE, $value, $this->colors[self::LICENSE]);
    }

    /**
     * Create a standard Image
     *
     * @param $vendor
     * @param $value
     * @param $color
     *
     * @return ImageInterface
     */
    private function createImage($vendor, $value, $color)
    {
        return new BucklerImage($vendor, $value, $color);
    }
}

<?php

namespace Jced;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class LockService
 */
class LockService
{
    /** @var Filesystem */
    private $fileSystem;

    /** @var string */
    private $lockPath;

    /**
     * LockService constructor.
     * @param string $lockPath
     */
    public function __construct($lockPath = null)
    {
        $this->fileSystem = new Filesystem();
        $this->setLockPath($lockPath);
    }

    /**
     * @param $path
     */
    public function setLockPath($path)
    {
        if (is_null($path)) {
            $this->lockPath = sys_get_temp_dir();
        } else {
            $this->lockPath = rtrim($path, DIRECTORY_SEPARATOR);
        }
        if (!is_dir($this->lockPath)) {
            $this->fileSystem->mkdir($this->lockPath);
        }
        if (!is_writable($this->lockPath)) {
            throw new IOException(sprintf('The directory "%s" is not writable.', $this->lockPath), 0, null, $this->lockPath);
        }
    }

    /**
     * @param null|string $name
     * @return string
     */
    private function getFilePath($name)
    {
        return sprintf(
            '%s%s%s.%s.lock',
            $this->lockPath,
            DIRECTORY_SEPARATOR,
            preg_replace('/[^a-z0-9\._-]+/i', '-', $name),
            hash('sha256', $name)
        );
    }

    /**
     * @param string $name
     * @return bool Return false if already locked
     */
    public function lock($name)
    {
        $file = $this->getFilePath($name);
        $handler = @fopen($file, 'xb');
        if (!$handler) {
            return false;
        }
        fclose($handler);
        return true;
    }

    /**
     * @param null|string $name
     */
    public function release($name)
    {
        $file = $this->getFilePath($name);
        return $this->fileSystem->remove($file);
    }
}

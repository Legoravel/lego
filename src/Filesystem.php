<?php

namespace Lego;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

trait Filesystem
{
    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Create a file at the given path with the given contents.
     *
     * @param string $path
     * @param string $contents
     * @param bool $lock
     * @return bool
     */
    public function createFile(string $path, string $contents = '', bool $lock = false): bool
    {
        $this->createDirectory(dirname($path));

        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Create a directory.
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return bool
     */
    public function createDirectory(string $path, int $mode = 0755, bool $recursive = true, bool $force = true): bool
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * Delete an existing file or directory at the given path.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void
    {
        $filesystem = new SymfonyFilesystem();

        $filesystem->remove($path);
    }

    public function rename(string $path, string $name): void
    {
        $filesystem = new SymfonyFilesystem();

        $filesystem->rename($path, $name);
    }
}

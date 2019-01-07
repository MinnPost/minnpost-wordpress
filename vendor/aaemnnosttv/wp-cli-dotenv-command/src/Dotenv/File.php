<?php

namespace WP_CLI_Dotenv\Dotenv;

use WP_CLI_Dotenv\Dotenv\Exception\FilePermissionsException;
use WP_CLI_Dotenv\Dotenv\Exception\NonExistentFileException;

/**
 * Class File
 * @package WP_CLI_Dotenv_Command
 */
class File
{
    /**
     * Absolute path to the file
     * @var string
     */
    protected $path;

    /**
     * Lines collection
     * @var FileLines
     */
    protected $lines;

    /**
     * File constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Get a new instance, and ensure the file is readable.
     *
     * @param $path
     *
     * @throws NonExistentFileException
     * @throws FilePermissionsException
     *
     * @return static
     */
    public static function at($path)
    {
        $file = new static($path);

        if (! $file->exists()) {
            throw new NonExistentFileException("File does not exist at $path");
        }

        if (! $file->isReadable()) {
            throw new FilePermissionsException("File not readable at $path");
        }

        return $file;
    }

    /**
     * Get a new instance, and ensure the file is writable.
     *
     * @param $path
     *
     * @throws FilePermissionsException
     *
     * @return static
     */
    public static function writable($path)
    {
        $file = static::at($path);

        if (! is_writable($path)) {
            throw new FilePermissionsException("File not writable at $path");
        }

        return $file;
    }


    /**
     * Create a new instance, including the file and parent directories.
     *
     * @param $path
     *
     * @return static
     */
    public static function create($path)
    {
        $file = new static($path);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (! $file->exists()) {
            touch($path);
        }

        return $file;
    }

    /**
     * Whether the file exists and is readable
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_readable($this->path);
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return is_writable($this->path);
    }

    /**
     * @return $this
     */
    public function load()
    {
        $this->lines = FileLines::load($this->path);

        return $this;
    }

    /**
     * Get the full path to the file.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Write the lines to the file.
     *
     * @return int
     */
    public function save()
    {
        return file_put_contents($this->path, $this->lines->toString());
    }

    /**
     * Check if the file exists
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->path);
    }

    /**
     * @return int
     */
    public function lineCount()
    {
        return $this->lines->count();
    }

    /**
     * Get the value for a key
     *
     * Ex using our format:
     * KEY='VALUE'
     *
     * @param $key
     *
     * @return null|string          string value,
     *                              null if no match was found
     */
    public function get($key)
    {
        return $this->lines->getDefinition($key);
    }

    /**
     * Set a variable definition.
     *
     * @param        $key
     * @param        $value
     * @param string $quote
     *
     * @return $this
     */
    public function set($key, $value, $quote = '')
    {
        $this->lines->updateOrAdd(new KeyValue($key, $value, $quote));

        return $this;
    }

    /**
     * Remove a variable definition.
     *
     * @param $key
     *
     * @return int Lines removed
     */
    public function remove($key)
    {
        $linesBefore = $this->lineCount();

        $this->lines->removeDefinition($key);

        return $linesBefore - $this->lineCount();
    }

    /**
     * Whether or not the file defines the given key
     *
     * @param $key
     *
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->lines->hasDefinition($key);
    }

    /**
     * Get the lines as key => value.
     *
     * @return Collection
     */
    public function dictionary()
    {
        return $this->lines->toDictionary();
    }

    /**
     * Get the lines as key => value pairs, where the keys match the given glob-style patterns.
     *
     * @param $patterns
     *
     * @return Collection
     */
    public function dictionaryWithKeysMatching($patterns)
    {
        return $this->lines->whereKeysLike($patterns)->toDictionary();
    }
}

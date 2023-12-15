<?php

namespace Lego;

use Illuminate\Support\Str as LaravelStr;

class Str
{
    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        return LaravelStr::studly($value);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        return LaravelStr::snake($value, $delimiter);
    }

    /**
     * Determine the real name of the given name,
     * excluding the given pattern.
     *    i.e. the name: "CreateArticleFeature.php" with pattern '/Feature.php'
     *        will result in "Create Article".
     *
     * @param string $name
     * @param string $pattern
     * @return string
     */
    public static function realName(string $name, string $pattern = '//'): string
    {
        $name = preg_replace($pattern, '', $name);

        return implode(' ', preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get the given name formatted as a feature.
     *
     *    i.e. "Create Post Feature", "CreatePostFeature.php", "createPost", "create"
     *    and many other forms will be transformed to "CreatePostFeature" which is
     *    the standard feature class name.
     *
     * @param string $name
     * @return string
     */
    public static function feature(string $name): string
    {
        $parts = array_map(static function ($part) {
            return self::studly($part);
        }, explode('/', $name));
        $feature = self::studly(preg_replace('/Feature(\.php)?$/', '', array_pop($parts)) . 'Feature');

        $parts[] = $feature;

        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Get the given name formatted as a job.
     *
     *    i.e. "Create Post Feature", "CreatePostJob.php", "createPost",
     *    and many other forms will be transformed to "CreatePostJob" which is
     *    the standard job class name.
     *
     * @param string $name
     * @return string
     */
    public static function job(string $name): string
    {
        return self::studly(preg_replace('/Job(\.php)?$/', '', $name) . 'Job');
    }

    /**
     * Get the given name formatted as an operation.
     *
     *  i.e. "Create Post Operation", "CreatePostOperation.php", "createPost",
     *  and many other forms will be transformed to "CreatePostOperation" which is
     *  the standard operation class name.
     *
     * @param string $name
     * @return string
     */
    public static function operation(string $name): string
    {
        return self::studly(preg_replace('/Operation(\.php)?$/', '', $name) . 'Operation');
    }

    /**
     * Get the given name formatted as a domain.
     *
     * Domain names are just CamelCase
     *
     * @param string $name
     * @return string
     */
    public static function domain(string $name): string
    {
        return self::studly($name);
    }

    /**
     * Get the given name formatted as a service name.
     *
     * @param string $name
     * @return string
     */
    public static function service(string $name): string
    {
        return self::studly($name);
    }

    /**
     * Get the given name formatted as a controller name.
     *
     * @param string $name
     * @return string
     */
    public static function controller(string $name): string
    {
        return self::studly(preg_replace('/Controller(\.php)?$/', '', $name) . 'Controller');
    }

    /**
     * Get the given name formatted as a model.
     *
     * Model names are just CamelCase
     *
     * @param string $name
     * @return string
     */
    public static function model(string $name): string
    {
        return self::studly($name);
    }

    /**
     * Get the given name formatted as a policy.
     *
     * @param $name
     * @return string
     */
    public static function policy($name): string
    {
        return self::studly(preg_replace('/Policy(\.php)?$/', '', $name) . 'Policy');
    }

    /**
     * Get the given name formatted as a request.
     *
     * @param $name
     * @return string
     */
    public static function request($name): string
    {
        return self::studly($name);
    }
}

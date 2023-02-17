<?php

namespace Okapi\Path;

/**
 * # Path Helper
 *
 * This class contains helper methods for path manipulation.
 */
class Path
{
    /**
     * Resolve a path.
     *
     * @param string|string[] $path
     * @param bool            $shouldCheckExistence
     *
     * @return string|false|(string|false)[]
     *
     * @see https://github.com/nodejs/node/blob/main/lib/path.js#L162
     */
    public static function resolve(string|array $path, bool $shouldCheckExistence = false): string|array|false
    {
        // Ignore empty paths
        if (!$path) {
            return $path;
        }

        // Resolve array of paths
        if (is_array($path)) {
            return array_map(fn($path) => self::resolve($path), $path);
        }

        // Get scheme name and path
        $components = explode('://', $path, 2);
        [$pathScheme, $resolvedPath] = isset($components[1]) ? $components : [null, $components[0]];

        // Bypass complex logic for simple paths
        if (!$pathScheme && ($fastPath = stream_resolve_include_path($path))) {
            return $fastPath;
        }

        // Resolve relative path
        $isRelative = $resolvedPath[0] !== '/' && $resolvedPath[1] !== ':';
        if ($isRelative) {
            $resolvedPath = getcwd() . DIRECTORY_SEPARATOR . $resolvedPath;
        }

        // Resolve path parts
        $resolvedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $resolvedPath);
        if (str_contains($resolvedPath, '.')) {
            $parts = explode(DIRECTORY_SEPARATOR, $resolvedPath);
            $resolvedParts = [];
            foreach ($parts as $part) {
                if ($part === '.') {
                    continue;
                }
                if ($part === '..') {
                    array_pop($resolvedParts);
                    continue;
                }
                $resolvedParts[] = $part;
            }
            $resolvedPath = implode(DIRECTORY_SEPARATOR, $resolvedParts);
        }

        // Append scheme name
        if ($pathScheme) {
            $resolvedPath = "$pathScheme://$resolvedPath";
        }

        // Check existence
        if ($shouldCheckExistence && !file_exists($resolvedPath)) {
            return false;
        }

        return $resolvedPath;
    }

    /**
     * Join paths.
     *
     * @param string[] $paths
     *
     * @return string
     *
     * @see https://github.com/nodejs/node/blob/main/lib/path.js#L425
     */
    public static function join(string ...$paths): string
    {
        // Ignore no paths
        if (count($paths) === 0) {
            return '.';
        }

        foreach ($paths as &$path) {
            if ($path === '') {
                $path = '.';
            }
        }

        $joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
        /** @noinspection RegExpRedundantEscape */
        return preg_replace(
            '/\\' . DIRECTORY_SEPARATOR . '+/',
            DIRECTORY_SEPARATOR,
            $joinedPath
        );
    }
}

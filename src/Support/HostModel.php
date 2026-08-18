<?php

namespace ComposerRumus\Support;

use RuntimeException;

/**
 * Resolves the host application classes and relation names named in
 * config/composer-rumus.php, and explains what to change when one is missing.
 */
class HostModel
{
    /** Returns the configured model class for a key such as `payment`. */
    public static function resolve(string $key): string
    {
        $class = config("composer-rumus.models.{$key}");

        if (! is_string($class) || $class === '') {
            throw new RuntimeException(static::message($key, '(not set)'));
        }

        if (! class_exists($class)) {
            throw new RuntimeException(static::message($key, $class));
        }

        return $class;
    }

    /**
     * Returns a configured relation name, or null when the host application
     * does not have that relation and the value is set to null or false.
     */
    public static function relation(string $key): ?string
    {
        $relation = config("composer-rumus.relations.{$key}");

        return is_string($relation) && $relation !== '' ? $relation : null;
    }

    /** Returns the usable relation names from a configured list, skipping disabled ones. */
    public static function relationList(string $key, array $default = []): array
    {
        $relations = config("composer-rumus.relations.{$key}", $default);

        if (! is_array($relations)) {
            $relations = $default;
        }

        return array_values(array_filter($relations, fn ($relation) => is_string($relation) && $relation !== ''));
    }

    /** Filters a set of relation keys down to the ones the host application provides. */
    public static function eagerLoad(string ...$keys): array
    {
        return array_values(array_filter(array_map(
            fn (string $key) => static::relation($key),
            $keys
        )));
    }

    private static function message(string $key, string $class): string
    {
        return sprintf(
            'composer-rumus: the "%s" model is configured as "%s", which does not exist in this application. '
            .'Open config/composer-rumus.php, set models.%s to the matching model class '
            .'(run `php artisan vendor:publish --tag=composer-rumus-config` first if that file is missing), '
            .'then run `php artisan optimize:clear`.',
            $key,
            $class,
            $key
        );
    }
}

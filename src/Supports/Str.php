<?php

namespace Huangang\YansongdaPayV2\Supports;

use Exception;

/**
 * modify from Illuminate\Support.
 */
class Str
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Return the remainder of a string after a given value.
     */
    public static function after(string $subject, string $search): string
    {
        return '' === $search ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     */
    public static function ascii(string $value, string $language = 'en'): string
    {
        $languageSpecific = static::languageSpecificCharsArray($language);

        if (!is_null($languageSpecific)) {
            $value = str_replace($languageSpecific[0], $languageSpecific[1], $value);
        }

        foreach (static::charsArray() as $key => $val) {
            $value = str_replace($val, $key, $value);
        }

        return preg_replace('/[^\x20-\x7E]/u', '', $value);
    }

    /**
     * Get the portion of a string before a given value.
     */
    public static function before(string $subject, string $search): string
    {
        return '' === $search ? $subject : explode($search, $subject)[0];
    }

    /**
     * Convert a value to camel case.
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string|array $needles
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string|array $needles
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cap a string with a single instance of a given value.
     */
    public static function finish(string $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|array $pattern
     */
    public static function is($pattern, string $value): bool
    {
        $patterns = is_array($pattern) ? $pattern : (array) $pattern;

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern == $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (1 === preg_match('#^'.$pattern.'\z#u', $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a string to kebab case.
     */
    public static function kebab(string $value): string
    {
        return static::snake($value, '-');
    }

    /**
     * Return the length of the given string.
     *
     * @param string $encoding
     */
    public static function length(string $value, ?string $encoding = null): int
    {
        if (null !== $encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * Limit the number of characters in a string.
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }

    /**
     * Convert the given string to lower-case.
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Limit the number of words in a string.
     */
    public static function words(string $value, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    /**
     * Parse a Class.
     */
    public static function parseCallback(string $callback, ?string $default = null): array
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @throws Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = function_exists('random_bytes') ? random_bytes($size) : mt_rand();

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Replace a given value in the string sequentially with an array.
     */
    public static function replaceArray(string $search, array $replace, string $subject): string
    {
        foreach ($replace as $value) {
            $subject = static::replaceFirst($search, $value, $subject);
        }

        return $subject;
    }

    /**
     * Replace the first occurrence of a given value in the string.
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ('' == $search) {
            return $subject;
        }

        $position = strpos($subject, $search);

        if (false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     */
    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        $position = strrpos($subject, $search);

        if (false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Begin a string with a single instance of a given value.
     */
    public static function start(string $value, string $prefix): string
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }

    /**
     * Convert the given string to upper-case.
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to title case.
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     */
    public static function slug(string $title, string $separator = '-', string $language = 'en'): string
    {
        $title = static::ascii($title, $language);

        // Convert all dashes/underscores into separator
        $flip = '-' == $separator ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator.'at'.$separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert a string to snake case.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string|array $needles
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Make a string's first character uppercase.
     */
    public static function ucfirst(string $string): string
    {
        return static::upper(static::substr($string, 0, 1)).static::substr($string, 1);
    }

    /**
     * Convert string's encoding.
     *
     * @author yansongda <me@yansonga.cn>
     */
    public static function encoding(string $string, string $to = 'utf-8', string $from = 'gb2312'): string
    {
        return mb_convert_encoding($string, $to, $from);
    }

    /**
     * Returns the replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     */
    protected static function charsArray(): array
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            '0' => ['??', '???', '??', '???'],
            '1' => ['??', '???', '??', '???'],
            '2' => ['??', '???', '??', '???'],
            '3' => ['??', '???', '??', '???'],
            '4' => ['???', '???', '??', '??', '???'],
            '5' => ['???', '???', '??', '??', '???'],
            '6' => ['???', '???', '??', '??', '???'],
            '7' => ['???', '???', '??', '???'],
            '8' => ['???', '???', '??', '???'],
            '9' => ['???', '???', '??', '???'],
            'a' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '???', '???', '???', '??', '??', '??', '???', '???', '??', '???', '??'],
            'b' => ['??', '??', '??', '???', '???', '???'],
            'c' => ['??', '??', '??', '??', '??', '???'],
            'd' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '???', '???', '???', '???'],
            'e' => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '??', '??', '???'],
            'f' => ['??', '??', '??', '??', '???', '???'],
            'g' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???'],
            'h' => ['??', '??', '??', '??', '??', '??', '???', '???', '???', '???'],
            'i' => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '??', '???', '???', '??', '??', '??', '???', '???', '???', '??????', '??', '???', '???', '??', '???'],
            'j' => ['??', '??', '??', '???', '??', '???'],
            'k' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '???'],
            'l' => ['??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'm' => ['??', '??', '??', '???', '???', '???'],
            'n' => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'o' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??????', '??', '??', '??', '???', '???', '???', '??'],
            'p' => ['??', '??', '???', '???', '??', '???'],
            'q' => ['???', '???'],
            'r' => ['??', '??', '??', '??', '??', '??', '???', '???'],
            's' => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '???', '???'],
            't' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???', '???', '???'],
            'u' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??'],
            'v' => ['??', '???', '??', '???'],
            'w' => ['??', '??', '??', '???', '???', '???'],
            'x' => ['??', '??', '???'],
            'y' => ['??', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'z' => ['??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'aa' => ['??', '???', '??'],
            'ae' => ['??', '??'],
            'ai' => ['???'],
            'ch' => ['??', '???', '???', '??'],
            'dj' => ['??', '??'],
            'dz' => ['??', '???'],
            'ei' => ['???'],
            'gh' => ['??', '???'],
            'ii' => ['???'],
            'ij' => ['??'],
            'kh' => ['??', '??', '???'],
            'lj' => ['??'],
            'nj' => ['??'],
            'oe' => ['??', '??', '??'],
            'oi' => ['???'],
            'oii' => ['???'],
            'ps' => ['??'],
            'sh' => ['??', '???', '??'],
            'shch' => ['??'],
            'ss' => ['??'],
            'sx' => ['??'],
            'th' => ['??', '??', '??', '??', '??'],
            'ts' => ['??', '???', '???'],
            'ue' => ['??'],
            'uu' => ['???'],
            'ya' => ['??'],
            'yu' => ['??'],
            'zh' => ['??', '???', '??'],
            '(c)' => ['??'],
            'A' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '???', '??'],
            'B' => ['??', '??', '???', '???'],
            'C' => ['??', '??', '??', '??', '??', '???'],
            'D' => ['??', '??', '??', '??', '??', '??', '???', '???', '??', '??', '???'],
            'E' => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '??', '??', '???'],
            'F' => ['??', '??', '???'],
            'G' => ['??', '??', '??', '??', '??', '??', '???'],
            'H' => ['??', '??', '??', '???'],
            'I' => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'J' => ['???'],
            'K' => ['??', '??', '???'],
            'L' => ['??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'M' => ['??', '??', '???'],
            'N' => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'O' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '??'],
            'P' => ['??', '??', '???'],
            'Q' => ['???'],
            'R' => ['??', '??', '??', '??', '??', '???'],
            'S' => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'T' => ['??', '??', '??', '??', '??', '??', '???'],
            'U' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '??'],
            'V' => ['??', '???'],
            'W' => ['??', '??', '??', '???'],
            'X' => ['??', '??', '???'],
            'Y' => ['??', '???', '???', '???', '???', '??', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'Z' => ['??', '??', '??', '??', '??', '???'],
            'AE' => ['??', '??'],
            'Ch' => ['??'],
            'Dj' => ['??'],
            'Dz' => ['??'],
            'Gx' => ['??'],
            'Hx' => ['??'],
            'Ij' => ['??'],
            'Jx' => ['??'],
            'Kh' => ['??'],
            'Lj' => ['??'],
            'Nj' => ['??'],
            'Oe' => ['??'],
            'Ps' => ['??'],
            'Sh' => ['??'],
            'Shch' => ['??'],
            'Ss' => ['???'],
            'Th' => ['??'],
            'Ts' => ['??'],
            'Ya' => ['??'],
            'Yu' => ['??'],
            'Zh' => ['??'],
            ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", "\xEF\xBE\xA0"],
        ];
    }

    /**
     * Returns the language specific replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     */
    protected static function languageSpecificCharsArray(string $language): ?array
    {
        static $languageSpecific;
        if (!isset($languageSpecific)) {
            $languageSpecific = [
                'bg' => [
                    ['??', '??', '??', '??', '??', '??', '??', '??'],
                    ['h', 'H', 'sht', 'SHT', 'a', '??', 'y', 'Y'],
                ],
                'de' => [
                    ['??',  '??',  '??',  '??',  '??',  '??'],
                    ['ae', 'oe', 'ue', 'AE', 'OE', 'UE'],
                ],
            ];
        }

        return isset($languageSpecific[$language]) ? $languageSpecific[$language] : null;
    }
}

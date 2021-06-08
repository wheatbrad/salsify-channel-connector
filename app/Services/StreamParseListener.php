<?php declare(strict_types=1);

namespace App\Services;

use JsonStreamingParser\Listener\ListenerInterface;

/**
 * Service to parse JSON streams. 
 */
class StreamParseListener extends ListenerInterface
{
    /**
     * @var string[]
     */
    private array $keys;

    public function startDocument(): void
    {
        $this->keys = [];
    }

    public function endDocument(): void
    {

    }

    public function startObject(): void
    {

    }

    public function endObject(): void
    {

    }

    public function startArray(): void
    {

    }

    public function endArray(): void
    {

    }

    public function key(string $key): void
    {
        $this->keys[] = $key;
    }

    /**
     * @param mixed $value the value as read from the parser, it may be a string, integer, boolean, etc
     */
    public function value($value)
    {

    }

    public function whitespace(string $whitespace): void
    {

    }
}
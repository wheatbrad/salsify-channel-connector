<?php declare(strict_types=1);

namespace App\Services;

use JsonStreamingParser\Listener\ListenerInterface;

/**
 * Service to parse JSON streams. 
 */
class StreamParseListener implements ListenerInterface
{

    /**
     * StreamParseListener implementation
     * test URL: https://api.ocozzio.com/_testing/data.json
     * 
     * Need to determine which level in the dataset we are at, and 
     * write the whole level into a table. (levels: header, attributes,
     * attribute_values, digital_assets, products)
     * 
     * Write that whole level into a table with a transaction because
     * we'll be overwriting data
     */


    /**
     * @var string[]
     */
    public array $keys;

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
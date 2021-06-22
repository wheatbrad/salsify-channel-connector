<?php declare(strict_types=1);

namespace App\Services;

use App\Model\RefreshModel;
use JsonStreamingParser\Listener\ListenerInterface;

/**
 * ObjectListener parses objects from Salsify data stream
 */
class ObjectListener implements ListenerInterface
{
    protected const LEVEL_ROOT = 1;
    protected const LEVEL_SECTION = 2;
    protected const LEVEL_OBJECT = 3;
    protected const LEVEL_ATTRIBUTE = 4;
    protected const LEVEL_ENUMERATED = 5;

    protected RefreshModel $refreshModel;
    
    /**
     * @var int|null will hold the current nesting level
     */
    protected int $level;

    /**
     * @var string|null will hold current section heading
     */
    protected string $currentSectionHeading;

    /**
     * @var array will hold the current section
     */
    protected $currentSection = [];

    /**
     * @var array will hold current object
     */
    protected $currentObject = [];

    /**
     * @var array will hold enumerated values
     */
    protected $enumeratedValues = [];

    /**
     * @var string|null will hold current key
     */
    protected string $currentKey;

    protected float $startTime;

    public function __construct(RefreshModel $refreshModel)
    {
        $this->refreshModel = $refreshModel;
    }

    public function startDocument(): void
    {
        $this->startTime = microtime(true);
        $this->level = 0;
        echo 'Document parsing started...'.PHP_EOL;
    }

    public function endDocument(): void
    {
        $this->level = 0;
        $timeToComplete = microtime(true) - $this->startTime;
        echo "Document parsed in: $timeToComplete seconds";
    }

    public function startObject(): void
    {
        $this->increaseLevel();

        if ($this->level === self::LEVEL_ATTRIBUTE) {
            // need to start writing 
        }
    }
    
    public function endObject(): void
    {
        $this->decreaseLevel();

        if ($this->level === self::LEVEL_OBJECT) {
            // closing an object need to push into currentSection
            $this->currentSection[] = $this->currentObject;
        }
    }
    
    public function startArray(): void
    {
        $this->increaseLevel();
    }
    
    public function endArray(): void
    {
        $this->decreaseLevel();

        if ($this->level === self::LEVEL_SECTION) {
            switch ($this->currentSectionHeading) {
                case 'attributes':
                    $this->refreshModel->setAttributesTable($this->currentSection);
                    break;
                
                case 'attribute_values':
                    $this->refreshModel->setAttributeValuesTable($this->currentSection);
                    break;
                
                case 'digital_assets':
                    $this->refreshModel->setDigitalAssetsTable($this->currentSection);
                    break;
                
                case 'products':
                    $this->refreshModel->setProductsTable($this->currentSection);
                    break;
            }

            $this->reset();
        }

        // if ($this->level === self::LEVEL_ATTRIBUTE) {
        //     // we've just closed up an enumerated value
        //     $this->currentObject[$this->currentKey] = implode(',', $this->enumeratedValues);
        // }
    }

    public function key(string $key): void
    {
        if ($this->level === self::LEVEL_SECTION) {
            $this->currentSectionHeading = $key;
        } else {
            $this->currentKey = $key;
        }
    }

    public function value($value): void
    {
        if ($this->level === self::LEVEL_ATTRIBUTE) {
            $this->currentObject[$this->currentKey] = $value;
        }
        if ($this->level === self::LEVEL_ENUMERATED) {
            // push into prop then when we detect close array implode
            $this->enumeratedValues[] = $value;
        }
    }

    public function whitespace(string $whitespace): void
    {
        // ingnoring whitespace chars
    }

    protected function increaseLevel(): void
    {
        $this->level = ++$this->level;
    }

    protected function decreaseLevel(): void
    {
        $this->level = --$this->level;
    }

    /**
     * Reset all the compound values to default.
     */
    protected function reset(): void
    {
        $this->currentSection = [];
        $this->currentObject = [];
    }
}

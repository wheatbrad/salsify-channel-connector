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
    
    protected int $level;
    protected string $currentSectionHeading;
    protected array $currentSection = [];
    protected array $currentObject = [];
    protected array $enumeratedValues = [];
    protected string $currentKey;

    public function __construct(RefreshModel $refreshModel)
    {
        $this->refreshModel = $refreshModel;
    }

    public function startDocument(): void
    {
        $this->level = 0;
    }

    public function endDocument(): void
    {
        $this->level = 0;
    }

    public function startObject(): void
    {
        $this->increaseLevel();
    }
    
    public function endObject(): void
    {
        $this->decreaseLevel();

        if ($this->level === self::LEVEL_OBJECT) {
            // closing an object need to push into current section
            $this->currentSection[] = $this->currentObject;
            // reset current object after pushing into section
            $this->currentObject = [];
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

            // reset respective class props
            $this->currentSection = [];
            $this->currentObject = [];
        }

        if ($this->level === self::LEVEL_ATTRIBUTE) {
            // we've just closed up an enumerated value
            $this->currentObject[$this->currentKey] = implode(',', $this->enumeratedValues);
            // reset enumerated values prop
            $this->enumeratedValues = [];
        }
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
            // these values joined into string @`this->endArray()` once all values collected
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
}

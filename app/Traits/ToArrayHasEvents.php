<?php
namespace App\Traits;

use Carbon\Carbon;

trait ToArrayHasEvents
{
    /**
     * Add toArray method added calling events 'array_converting' and 'array_converted'.
     */
    public function toArray()
    {
        $result = null;
        if (isset($this->dispatchesEvents) && isset($this->dispatchesEvents['array_converting'])) {
            $eventResult = event(new $this->dispatchesEvents['array_converting']($this));
            // If result of array_converting event has array from any listener, using it as result.
            if (is_array($eventResult)) {
                foreach ($eventResult as $listenerResult) {
                    if (is_array($listenerResult)) {
                        $result = $listenerResult;
                        break;
                    }
                }
            }
        }

        if (!$result) {
            $result = parent::toArray();
        }

        $result = $this->toArrayConvertDates($result);

        if (isset($this->dispatchesEvents) && isset($this->dispatchesEvents['array_converted'])) {
            event(new $this->dispatchesEvents['array_converted']($this, $result));
        }
        return $result;
    }

    /**
     * Convert dates to string.
     *
     * @param array $result
     * @return array
     */
    private function toArrayConvertDates(array $result): array
    {
        if (isset($this->dates) && !empty($this->dates)) {
            foreach ($this->dates as $dateAttribute) {
                if (isset($result[ $dateAttribute ]) && is_a($result[ $dateAttribute ], Carbon::class)) {
                    $result[ $dateAttribute ] = $result[ $dateAttribute ]->toString();
                }
            }
        }
        return $result;
    }
}

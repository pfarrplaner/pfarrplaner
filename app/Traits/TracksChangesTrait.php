<?php


namespace App\Traits;


use App\Service;
use Illuminate\Support\Collection;

trait TracksChangesTrait
{

    /** @var array $dataBeforeChanges Snapshot with original data */
    public $dataBeforeChanges = [];
    public $changed = [];

    public function createSnapshot()
    {
        $snapshot = $this->removeTimestamps(json_decode(json_encode($this), true));
        if (property_exists($this, 'appendsToTracking') && is_array($this->appendsToTracking)) {
            foreach ($this->appendsToTracking as $attribute) {
                if (!isset($snapshot[$attribute])) $snapshot[$attribute] = $this->$attribute;
            }
        }
        return $snapshot;
    }

    /**
     * Start tracking changed
     * @return void
     */
    public function trackChanges()
    {
        if (isset($this->forceTracking)) {
            $this->load($this->forceTracking);
        }
        $this->dataBeforeChanges = $this->createSnapshot();
    }

    protected function removeTimestamps($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) $array[$key] = $this->removeTimestamps($value);
            elseif ($key == 'updated_at') unset($array[$key]);
            elseif ($key == 'created_at') unset($array[$key]);
        }
        return $array;
    }


    /**
     * Return all changed attributes and relations
     * @return array
     */
    public function diff()
    {
        $this->refresh();

        $diff = [];
        $snapshot = $this->createSnapshot();
        $original = $this->dataBeforeChanges;

        // set previously unset keys
        foreach ($snapshot as $key => $value) {
            if (!isset($original[$key])) $original[$key] = '';
        }

        foreach ($original as $key => $value) {
            // reset unset keys
            if (!isset($snapshot[$key])) $snapshot[$key] = '';
            if (print_r($value, 1) != print_r($snapshot[$key], 1)) {
                $diff[$key] = [
                    'original' => $original[$key],
                    'changed' => $snapshot[$key],
                ];
            }
        }

        return $diff;
    }

    public function isChanged()
    {
        $snapshot = $this->createSnapshot();
        $original = $this->dataBeforeChanges;
        unset($original['updated_at']);
        unset($snapshot['updated_at']);
        return (json_encode($snapshot) != json_encode($original));
    }

    public function storeDiff() {
        $this->changed = $this->diff();
    }

}

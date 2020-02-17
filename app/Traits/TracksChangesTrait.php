<?php


namespace App\Traits;


use App\Service;
use Illuminate\Support\Collection;

trait TracksChangesTrait
{

    /** @var array $dataBeforeChanges Snapshot with original data */
    public $dataBeforeChanges = [];

    public function createSnapshot()
    {
        if (isset($this->forceTracking)) $this->load($this->forceTracking);
        $data = $this->toArray();
        $snapshot = [];
        foreach ($data as $key => $val) {
            if (is_array($data[$key]) && method_exists($this, $key)) {
                if (is_a($val, \Illuminate\Database\Eloquent\Collection::class)) {
                    $snapshot['relations'][$key] = collect($this->$key);
                } else {
                    $snapshot['relations'][$key] = $this->$key;
                }
            } else {
                $snapshot['attributes'][$key] = $val;
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
        $this->dataBeforeChanges = $this->createSnapshot();
    }

    /**
     * Return all changed attributes and relations
     * @return array
     */
    public function diff()
    {
        $data = $this->createSnapshot();

        $class = get_class($this);
        $originalObj = new $class();
        $changedObj = new $class();

        // unset updated_at, which will always be changed
        unset($data['attributes']['updated_at']);
        if(!isset($this->dataBeforeChanges['attributes'])) $this->dataBeforeChanges['attributes'] = [];
        if(!isset($this->dataBeforeChanges['relations'])) $this->dataBeforeChanges['relations'] = [];

        $diff = ['attributes' => [], 'relations' => []];
        foreach ($data['attributes'] as $attribute => $value) {
            if (!isset($this->dataBeforeChanges['attributes'][$attribute])) $this->dataBeforeChanges['attributes'][$attribute] = null;
            if ($value != $this->dataBeforeChanges['attributes'][$attribute]) {
                $diff['attributes'][$attribute] = ['original' =>  ($this->dataBeforeChanges['attributes'][$attribute] ?? ''), 'changed' => $value ?? ''];
            }
            $originalObj->$attribute = $this->dataBeforeChanges['attributes'][$attribute] ?? null;
            $changedObj->$attribute = $value ?: null;
        }
        foreach ($data['relations'] as $relation => $value) {
            if (isset($this->dataBeforeChanges['relations'][$relation])) {
                if (is_a($value, Collection::class)) {
                    $changed = ($value->pluck('id')->join(',') != $this->dataBeforeChanges['relations'][$relation]->pluck('id')->join(','));
                } else {
                    $changed = ($value != $this->dataBeforeChanges['relations'][$relation]);
                }
                if ($changed) $diff['relations'][$relation] = ['original' => $this->dataBeforeChanges['relations'][$relation], 'changed' => $value];
            }
        }

        /** @var Service $originalObj */
        $originalObj->setRelations($this->dataBeforeChanges['relations']);
        /** @var Service $changedObj */
        $changedObj->setRelations($data['relations']);
        $diff['original'] = $originalObj;
        $diff['changed'] = $changedObj;
        $diff['field_names'] = $this->revisionFormattedFieldNames ?? [];
        return $diff;
    }

    public function isChanged() {
        $diff = $this->diff();
        return ((count($diff['attributes']) + count($diff['relations'])) != 0);
    }



}

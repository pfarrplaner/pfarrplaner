<?php


namespace App\Traits;


use App\Service;
use Illuminate\Support\Collection;

trait TracksChangesTrait
{

    /** @var array $dataBeforeChanges Snapshot with original data */
    public $dataBeforeChanges = [];
    public $originalObject = null;
    public $changedFields = [];
    public $originalRelations = [];
    public $originalAttributes = [];
    public $originalAppendedAttributes = [];

    public function createSnapshot()
    {
        $snapshot = $this->attributes;
        foreach ($this->getRelations() as $key => $relation) {
            if (is_a($relation, \Illuminate\Database\Eloquent\Collection::class)) {
                $snapshot[$key] = $relation->pluck('id')->join(',');
            } else {
                if (is_object($relation) && property_exists($relation, 'id')) {
                    $snapshot[$key] = $relation->id;
                }
            }
        }
        return $snapshot;
    }

    public function restoreOriginal($attributes = null, $relations = null)
    {
        $class = get_called_class();
        $object = new $class();
        $object->setRawAttributes(($attributes ?? $this->originalAttributes));
        foreach (($relations ?? $this->originalRelations) as $key => $relation) {
            $object->$key = $relation;
        }
        return $object;
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
        if (is_object($this)) $this->originalObject = clone($this);
        $this->originalAttributes = $this->attributes;

        foreach ($this->getRelations() as $relationKey => $relation) {
            if (is_a($relation, \Illuminate\Database\Eloquent\Collection::class)) {
                $collection = new Collection();
                foreach ($relation as $key => $item) {
                    $collection->put($key, $item);
                }
                $this->originalObject->$relation = $collection;
                $this->originalRelations[$relationKey] = $collection;
            } else {
                if (is_object($relation)) {
                    $this->originalObject->$relation = $this->originalRelations[$relationKey] = clone($relation);
                }
            }
        }
        $this->dataBeforeChanges = $this->createSnapshot();
        $this->originalAppendedAttributes = $this->getAppendedAttributes();
    }

    protected function unPivot($array) {
        if (!is_array($array)) return $array;
        if (isset($array['pivot'])) unset($array['pivot']);
        foreach ($array as $k => $v) {
            $array[$k] = $this->unPivot($v);
        }
        return $array;
    }

    protected function getAppendedAttributes()
    {
        $attrs = [];
        if (property_exists($this, 'appendsToTracking')) {
            foreach ($this->appendsToTracking as $key) {
                $value = $this->$key;
                $attrs[$key] = $this->unPivot(json_decode(json_encode($value), true));
            }
        }
        return $attrs;
    }


    /**
     * Return all changed attributes and relations
     * @return array
     */
    public function diff()
    {
        $this->refresh();
        $dataNow = $this->createSnapshot();
        $dataBefore = $this->dataBeforeChanges;

        $diff = [];
        $diff['difference'] = array_diff($dataBefore, $dataNow);

        // check appended attributes
        foreach ($this->originalAppendedAttributes as $attribute => $value) {
            if (!(serialize($this->unPivot(json_decode(json_encode($this->$attribute), true))) == serialize($value))) {
                $diff['difference'][$attribute] = $this->$attribute;
            }
        }

        $this->changedFields = $diff['difference'];

        $diff['original'] = $this->originalObject;
        $diff['changed'] = $this;
        $diff['field_names'] = $this->revisionFormattedFieldNames ?? [];
        return $diff;
    }

    public function isChanged()
    {
        $diff = $this->diff();
        if (isset($diff['difference']['updated_at'])) {
            unset($diff['difference']['updated_at']);
        }
        return (count($diff['difference']) != 0);
    }


}

<?php


namespace App\Calendars;


use Carbon\Carbon;

class AbstractCalendarItem
{

    protected $ID;

    /** @var Carbon Start date */
    protected $startDate = null;

    /** @var Carbon End date */
    protected $endDate = null;

    /** @var string Title */
    protected $title = '';

    /** @var string Description */
    protected $description = '';

    /** @var string Location */
    protected $location = '';

    protected $calendar = null;

    protected $dates = ['startDate', 'endDate'];


    public function __construct(array $data = [], $calendar = null)
    {
        if (null !== $calendar) $this->setCalendar($calendar);
        $this->setPropertiesFromArray($data);
    }

    protected function setPropertiesFromArray(array $data)
    {
        foreach ($data as $key => $val) {
            if (in_array($key, $this->dates)) $val = new Carbon($val);
            if (property_exists($this, $key)) {
                $setter = 'set'.ucfirst($key);
                if (method_exists($this, $setter)) {
                    $this->$setter($val);
                } else {
                    $this->$key = $val;
                }
            }
        }
    }



    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @param Carbon $startDate
     */
    public function setStartDate(Carbon $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }

    /**
     * @param Carbon $endDate
     */
    public function setEndDate(Carbon $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return null
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param null $calendar
     */
    public function setCalendar($calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param mixed $ID
     */
    public function setID($ID): void
    {
        $this->ID = $ID;
    }

    public function isDateField($field): bool {
        return in_array($field, $this->dates);
    }

}

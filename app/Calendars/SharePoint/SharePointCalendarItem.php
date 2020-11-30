<?php


namespace App\Calendars\SharePoint;


use Carbon\Carbon;
use App\Calendars\AbstractCalendarItem;
use Thybag\SharePointAPI;

class SharePointCalendarItem extends AbstractCalendarItem
{
    /** @var SharePointCalendar */
    protected $calendar = null;

    protected function setPropertiesFromArray($data) {
        parent::setPropertiesFromArray($data);
        if (isset($data['id'])) {
            $this->setID($data['id']);
        }
    }

    protected function setProperty($property, $datum)
    {
        if (!in_array($property, $this->dates)) {
            $this->$property = $datum;
        } else {
            $this->$property = new Carbon($datum);
        }
    }

    public static function fromSharepointListItem($data, $calendar = null) {
        $myData = [
            'startDate' => $data['eventdate'],
            'endDate' => $data['enddate'],
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'location' => $data['location'] ?? '',
            'ID' => $data['id'] ?? null,
        ];
        return new self($myData, $calendar);
    }

    public function toArray()
    {
        return  [
            'EventDate' => $this->getStartDate()->format('Y-m-d H:i:s'),
            'EndDate' => $this->getEndDate()->format('Y-m-d H:i:s'),
            'Title' => $this->getTitle(),
            'Description' => $this->getDescription(),
            'Location' => $this->getLocation(),
        ];
    }

    public function update(array$data) {
        $this->setPropertiesFromArray($data);
        if (null !== $this->calendar) return $this->calendar->update($this);
        return $this;
    }

    public function delete() {
        if (null !== $this->calendar) $this->calendar->delete($this);
    }

}

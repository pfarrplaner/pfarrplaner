<?php


namespace App\Calendars\Exchange;


use Carbon\Carbon;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfItemChangeDescriptionsType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Type\BodyContentType;
use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\ItemChangeType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\SetItemFieldType;
use App\Calendars\AbstractCalendarItem;
use App\Calendars\Exchange\Exceptions\CalendarNotSetException;
use jamesiarmes\PhpEws\Type\TimeZoneDefinitionType;
use jamesiarmes\PhpEws\Type\TimeZoneType;

class ExchangeCalendarItem extends AbstractCalendarItem
{
    protected $changeKey = '';

    /** @var ExchangeCalendar */
    protected $calendar = null;

    protected $fieldURI = [
        'startDate' => UnindexedFieldURIType::CALENDAR_START,
        'endDate' => UnindexedFieldURIType::CALENDAR_END,
        'title' => UnindexedFieldURIType::ITEM_SUBJECT,
        'location' => UnindexedFieldURIType::CALENDAR_LOCATION,
        'description' => UnindexedFieldURIType::ITEM_BODY,
    ];

    public function __construct($data = [], ExchangeCalendar $calendar = null)
    {
        if (null !== $calendar) {
            $this->setCalendar($calendar);
        }
        $this->setPropertiesFromArray($data);
    }

    public function toExchangeItem()
    {
        $event = new CalendarItemType();
        $event->Start = $this->startDate->format('c');
        $event->End = $this->endDate->format('c');
        $event->Subject = $this->title ?: '';
        $event->Body = new BodyType();
        $event->Body->BodyType = BodyTypeType::HTML;
        $event->Body->_ = $this->description ?: '';
        $event->Location = $this->location;

        // do not set reminder for past items!
        if ($this->startDate <= Carbon::now()) {
            $event->ReminderIsSet = false;
        }

        return $event;
    }

    public static function fromExchangeItem(CalendarItemType $item, $calendar = null): ExchangeCalendarItem
    {
        $data = [
            'startDate' => new Carbon($item->Start),
            'endDate' => new Carbon($item->End),
            'title' => $item->Subject ?? '',
            'location' => $item->Location ?? '',
            'description' => $item->Body->_ ?? '',
            'ID' => $item->ItemId->Id,
            'changeKey' => $item->ItemId->ChangeKey,
        ];
        return new self($data, $calendar);
    }

    public function delete()
    {
        if (null === $this->calendar) {
            throw new CalendarNotSetException();
        }
        return $this->calendar->delete($this->getID());
    }

    public function update($data)
    {
        if (null === $this->calendar) {
            throw new CalendarNotSetException();
        }
        return $this->calendar->update($this->getID(), $data);
    }

    public function getItemChangeType($data)
    {
        $change = new ItemChangeType();
        $change->ItemId = new ItemIdType();
        $change->ItemId->Id = $this->getID();
        $change->ItemId->ChangeKey = $this->getChangeKey();
        $change->Updates = new NonEmptyArrayOfItemChangeDescriptionsType();

        foreach ($data as $key => $value) {
            if ($this->isDateField($key)) {
                $value = (new Carbon($value))->format('c');
            }
            $field = new SetItemFieldType();
            $field->FieldURI = new PathToUnindexedFieldType();
            $field->FieldURI->FieldURI = $this->fieldURI[$key];
            $field->CalendarItem = new CalendarItemType();

            switch ($key) {
                case 'startDate':
                    $field->CalendarItem->Start = is_object($value) ? $value->format('c') : $value;
                    break;
                case 'endDate':
                    $field->CalendarItem->End = is_object($value) ? $value->format('c') : $value;
                    break;
                case 'title':
                    $field->CalendarItem->Subject = $value ?? '';
                    break;
                case 'description':
                    if (!$field->CalendarItem->Body) $field->CalendarItem->Body = new BodyType();
                    $field->CalendarItem->Body->BodyType = BodyTypeType::HTML;
                    $field->CalendarItem->Body->_ = $value ?? '';
                    break;
                case 'location':
                    $field->CalendarItem->Location = $value ?? '';
                    break;
            }
            $change->Updates->SetItemField[] = $field;
        }
        return $change;
    }

    /**
     * @return string
     */
    public function getChangeKey(): string
    {
        return $this->changeKey;
    }

    /**
     * @param string $changeKey
     */
    public function setChangeKey(string $changeKey): void
    {
        $this->changeKey = $changeKey;
    }


}

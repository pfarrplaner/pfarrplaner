<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App;

use App\Helpers\YoutubeHelper;
use App\Seating\AbstractSeatFinder;
use App\Seating\MaximumBasedSeatFinder;
use App\Seating\RowBasedSeatFinder;
use App\Tools\StringTool;
use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use App\Traits\IncludesPermissionAttributes;
use App\Traits\TracksChangesTrait;
use Carbon\Carbon;
use Exception;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_VideoSnippet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * Class Service
 * @package App
 */
class Service extends Model
{
    use RevisionableTrait;
    use HasCommentsTrait;
    use TracksChangesTrait;
    use HasAttachmentsTrait;
    use IncludesPermissionAttributes;

    /**
     * @var array
     */
    public $liturgy = [];

    /**
     * @var string[]
     */
    protected $with = [
        'participants',
        'day',
        'location',
        'city',
        'pastors',
        'organists',
        'sacristans',
        'otherParticipants',
        'participantsWithMinistry',
        'funerals',
        'baptisms',
        'weddings'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'need_predicant' => 'boolean',
        'cc' => 'boolean',
        'baptism' => 'boolean',
        'eucharist' => 'boolean',
    ];

    /**
     * @var bool
     */
    protected $revisionEnabled = true;
    /**
     * @var string[]
     */
    protected $revisionFormattedFieldNames = array(
        'day_id' => 'Tag',
        'location_id' => 'Ort',
        'time' => 'Uhrzeit',
        'description' => 'Besonderheiten',
        'city_id' => 'Kirchengemeinde',
        'special_location' => 'Ort (Freitext)',
        'need_predicant' => 'Prädikant benötigt',
        'baptism' => 'Taufe',
        'eucharist' => 'Abendmahl',
        'offerings_counter1' => 'Opferzähler 1',
        'offerings_counter2' => 'Opferzähler 2',
        'offering_goal' => 'Opferzweck',
        'offering_description' => 'Anmerkungen zum Opfer',
        'offering_type' => 'Opfertyp',
        'others' => 'Weitere Beteiligte',
        'cc' => 'Kinderkirche findet statt',
        'cc_location' => 'Ort der Kinderkirche',
        'cc_lesson' => 'Lektion für die Kinderkirche',
        'cc_staff' => 'Mitarbeiter in der Kinderkirche',
        'cc_alt_time' => 'Alternative Uhrzeit für die Kinderkirche',
        'internal_remarks' => 'Interne Anmerkungen',
        'offering_amount' => 'Opferbetrag',
        'title' => 'Titel',
        'hidden' => 'Versteckt',
    );

    /**
     * @var string[]
     */
    protected $fillable = [
        'day_id',
        'location_id',
        'time',
        'description',
        'city_id',
        'special_location',
        'need_predicant',
        'baptism',
        'eucharist',
        'offerings_counter1',
        'offerings_counter2',
        'offering_goal',
        'offering_description',
        'offering_type',
        'others',
        'cc',
        'cc_alt_time',
        'cc_location',
        'cc_lesson',
        'cc_staff',
        'internal_remarks',
        'offering_amount',
        'title',
        'youtube_url',
        'cc_streaming_url',
        'offerings_url',
        'meeting_url',
        'recording_url',
        'songsheet',
        'external_url',
        'sermon_title',
        'sermon_reference',
        'sermon_image',
        'sermon_description',
        'konfiapp_event_type',
        'konfiapp_event_qr',
        'hidden',
        'needs_reservations',
        'exclude_sections',
        'registration_active',
        'exclude_places',
        'registration_phone',
        'registration_online_start',
        'registration_online_end',
        'registration_max',
        'reserved_places',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'pastors',
        'organists',
        'sacristans',
        'otherParticipants',
        'descriptionText',
        'locationText',
        'dateText',
        'timeText',
        'baptismsText',
        'descriptionText',
        'liturgy',
        'ministriesByCategory',
        'isShowable',
        'isEditable',
        'isDeletable',
        'isMine',
        'titleText',
        'liveDashboardUrl',
    ];

    /**
     * @var string[]
     */
    protected $appendsToTracking = [
        'ministriesByCategory',
    ];

    /**
     * @var string[]
     */
    protected $attributes = [
        'offering_type' => 'eO',
    ];

    protected $dates = [
        'registration_online_start',
        'registration_online_end',
    ];
    /** @var AbstractSeatFinder */
    protected $seatFinder = null;
    /**
     * @var array
     */
    private $auditData = [];

// ACCESSORS

    /**
     * @return array
     */
    public function getMinistriesByCategoryAttribute()
    {
        return $this->ministries();
    }

    /**
     * @return array
     */
    public function ministries()
    {
        $ministries = [];
        foreach ($this->participantsWithMinistry as $participant) {
            if (!isset($ministries[$participant->pivot->category])) {
                $ministries[$participant->pivot->category] = new Collection();
            }
            $ministries[$participant->pivot->category]->push($participant);
        }
        return $ministries;
    }

    /**
     * @return mixed
     */
    public function getDateTextAttribute()
    {
        return $this->dateText();
    }

    /**
     * @param string $format
     * @return mixed
     */
    public function dateText($format = '%d.%m.%Y')
    {
        return $this->day->date->formatLocalized($format);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPastorsAttribute()
    {
        return $this->pastors()->get();
    }

    /**
     * @return BelongsToMany
     */
    public function pastors()
    {
        return $this->belongsToMany(User::class)->wherePivot('category', 'P')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganistsAttribute()
    {
        return $this->organists()->get();
    }

    /**
     * @return BelongsToMany
     */
    public function organists()
    {
        return $this->belongsToMany(User::class)->wherePivot('category', 'O')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSacristansAttribute()
    {
        return $this->sacristans()->get();
    }
//

    /**
     * @return BelongsToMany
     */
    public function sacristans()
    {
        return $this->belongsToMany(User::class)->wherePivot('category', 'M')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOtherParticipantsAttribute()
    {
        return $this->otherParticipants()->get();
    }

    /**
     * @return BelongsToMany
     */
    public function otherParticipants()
    {
        return $this->belongsToMany(User::class)->wherePivot('category', 'A')->withTimestamps();
    }

    /**
     * @return string
     */
    public function getDescriptionTextAttribute()
    {
        return $this->descriptionText();
    }

    /**
     * @return string
     */
    public function descriptionText()
    {
        $desc = [];
        if ($this->needs_reservations) {
            $desc[] = ($this->registration_online_end ? 'Anmeldung nötig bis ' . $this->registration_online_end->format(
                    'd.m.Y, H:i'
                ) . ' Uhr' : 'Anmeldung nötig');
        }
        if ($this->baptism) {
            $desc[] = 'mit Taufen';
        }
        if ($this->eucharist) {
            $desc[] = 'mit Abendmahl';
        }
        if ($this->getAttribute('description') != '') {
            $desc[] = $this->getAttribute('description');
        }
        return join('; ', $desc);
    }

    /**
     * @return mixed|string
     */
    public function getLocationTextAttribute()
    {
        return $this->locationText();
    }

    /**
     * @return mixed|string
     */
    public function locationText()
    {
        return $this->special_location ?: (is_object($this->location) ? $this->location->name : '');
    }

    /**
     * @return string
     */
    public function getTimeTextAttribute()
    {
        return $this->timeText();
    }

    /**
     * @param bool $uhr
     * @param string $separator
     * @param bool $skipMinutes
     * @param bool $nbsp
     * @param bool $leadingZero
     * @return string
     */
    public function timeText($uhr = true, $separator = ':', $skipMinutes = false, $nbsp = false, $leadingZero = false)
    {
        return StringTool::timeText($this->time, $uhr, $separator, $skipMinutes, $nbsp, $leadingZero);
    }

    /**
     * @return string
     */
    public function getBaptismsTextAttribute()
    {
        return $this->baptismsText(true);
    }

    /**
     * @param bool $includeCount
     * @param bool $includeText
     * @return string
     */
    public function baptismsText($includeCount = false, $includeText = false)
    {
        $baptisms = [];
        foreach ($this->baptisms as $baptism) {
            $baptisms[] = $baptism->candidate_name;
        }
        return
            ($includeText ? ($this->baptisms()->count() == 1 ? 'Taufe ' : 'Taufen ') . 'von ' : '')
            . ($includeCount ? $this->baptisms()->count() . ' ' . ($this->baptisms()->count(
                ) == 1 ? 'Taufe ' : 'Taufen ') : '')
            . join('; ', $baptisms);
    }

    /**
     * @return HasMany
     */
    public function baptisms()
    {
        return $this->hasMany(Baptism::class);
    }

    /**
     * @return false|string
     */
    public function getTimeAttribute()
    {
        return isset($this->attributes['time']) ? substr($this->attributes['time'], 0, 5) : '';
    }

    /**
     * @return array
     */
    public function getLiturgyAttribute()
    {
        return $this->liturgy;
    }

    /**
     * @return mixed|string
     */
    public function getBroadcastTitleAttribute()
    {
        $liturgy = Liturgy::getDayInfo($this->day);
        return ($this->title ?: (isset($liturgy['title']) ? $liturgy['title']
            . ' (' . $this->day->date->format('d.m.Y') . ')'
            : 'Gottesdienst mit ' . $this->participantsText('P', true)));
    }

    /**
     * @param $category
     * @param bool $fullName
     * @param bool $withTitle
     * @param string $glue
     * @return string
     */
    public function participantsText($category, $fullName = false, $withTitle = true, $glue = ', ')
    {
        $participants = $this->participantsByCategory($category);
        $names = [];
        foreach ($participants as $participant) {
            $names[] = ($fullName ? $participant->fullName($withTitle) : $participant->lastName($withTitle));
        }
        return join($glue, $names);
    }

    /**
     * @param $category
     * @return Collection|mixed
     */
    public function participantsByCategory($category)
    {
        switch ($category) {
            case 'P':
                return $this->pastors;
            case 'O':
                return $this->organists;
            case 'M':
                return $this->sacristans;
            case 'A':
                return $this->otherParticipants;
            default:
                return $this->ministries()[$category] ?? collect([]);
        }
    }

    /**
     * @return array|string
     * @throws \Throwable
     */
    public function getBroadcastDescriptionAttribute()
    {
        return (view(
            'services.youtube.snippet.description',
            ['service' => $this, 'liturgy' => Liturgy::getDayInfo($this->day)]
        )->render());
    }

    /**
     * @return string
     */
    public function getVideoTimeStringAttribute()
    {
        $thisTime = Carbon::createFromTimeString(
            $this->day->date->format('Y-m-d') . ' ' . $this->time,
            'Europe/Berlin'
        );
        return $thisTime->setTimezone('UTC')->format('Y-m-d\TH:i:s') . '.000Z';
    }

    /**
     * @return string
     */
    public function getSongsheetUrlAttribute()
    {
        return route(
            'storage',
            [
                'path' => pathinfo($this->songsheet, PATHINFO_FILENAME),
                'prettyName' => $this->day->date->format('Ymd') . '-Liedblatt.' . pathinfo(
                        $this->songsheet,
                        PATHINFO_EXTENSION
                    )
            ]
        );
    }

    /**
     * @return string
     */
    public function getOfferingsUrlAttribute()
    {
        return $this->attributes['offerings_url'] ?: $this->city->default_offering_url;
    }
// END ACCESSORS

// MUTATORS
    /**
     * @param $time
     */
    public function setTimeAttribute($time)
    {
        $this->attributes['time'] = substr($time, 0, 5);
    }

// SCOPES

    /**
     * @param Builder $query
     * @param $ministries
     * @return Builder
     */
    public function scopeHavingOpenMinistries(Builder $query, $ministries)
    {
        return $query->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereDoesntHave(
                'participants',
                function ($query) use ($ministries) {
                    $query->whereIn('service_user.category', $ministries);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC');
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeStartingFrom(Builder $query, Carbon $date)
    {
        return $query->whereHas(
            'day',
            function ($query) use ($date) {
                $query->where('date', '>=', $date);
            }
        );
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeEndingAtFrom(Builder $query, Carbon $date)
    {
        return $query->whereHas(
            'day',
            function ($query) use ($date) {
                $query->where('date', '<=', $date);
            }
        );
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeBetween(Builder $query, Carbon $start, Carbon $end)
    {
        return $query->whereHas(
            'day',
            function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            }
        );
    }

    /**
     * @param Builder $query
     * @param int $year Year
     * @param int $month Month
     * @return Builder
     */
    public function scopeInMonth(Builder $query, $year, $month) {
        $monthStart = Carbon::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-01 0:00:00');
        $monthEnd = (clone $monthStart)->addMonth(1)->subSecond(1);
        return $query->between($monthStart, $monthEnd);
    }

    /**
     * @param Builder $query
     * @param City $city
     * @return Builder
     */
    public function scopeRegularForCity(Builder $query, City $city)
    {
        return $query->where('city_id', $city->id)
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('weddings');
    }

    /**
     * @param Builder $query
     * @param $city
     * @return Builder
     */
    public function scopeInCity(Builder $query, $city)
    {
        return $query->where('city_id', $city->id);
    }

    /**
     * @param Builder $query
     * @param Carbon $start
     * @param Carbon $end
     * @return Builder
     */
    public function scopeDateRange(Builder $query, Carbon $start, Carbon $end)
    {
        return $query->whereHas(
            'day',
            function ($query2) use ($start, $end) {
                $query2->where('date', '>=', $start)
                    ->where('date', '<=', $end);
            }
        );
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query)
    {
        return $query->select('services.*')
            ->join('days', 'services.day_id', 'days.id')
            ->orderBy('days.date')
            ->orderBy('time');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeHidden(Builder $query)
    {
        return $query->where('hidden', 1);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotHidden(Builder $query)
    {
        return $query->where('hidden', 0);
    }

// SETTERS

    /**
     * Mix a collection of services into an array of events
     * @param $events
     * @param $services
     * @param Carbon $start
     * @param Carbon $end
     * @return mixed
     */
    public static function mix($events, $services, Carbon $start, Carbon $end)
    {
        foreach ($services as $service) {
            if (is_object($service->day)) {
                if (($service->day->date <= $end) && ($service->day->date >= $start)) {
                    $events[$service->trueDate()->format('YmdHis')][] = $service;
                }
            }
        }

        ksort($events);
        return $events;
    }

    /**
     * Return services with empty entries for specific ministries
     * @param $ministries array
     * @return array
     */
    public static function withOpenMinistries($ministries)
    {
        $missing2 = [];

        foreach ($ministries as $ministry) {
            $missing2[$ministry] = Service::with(['location', 'day'])
                ->whereIn('city_id', Auth::user()->writableCities->pluck('id'))
                ->havingOpenMinistries([$ministry])
                ->startingFrom(Carbon::now())
                ->get();
        }

        $missing = [];
        foreach ($missing2 as $ministry => $services) {
            foreach ($services as $service) {
                if (isset($missing[$service->id])) {
                    $missing[$service->id]['missing'][] = $ministry;
                } else {
                    $missing[$service->id] = [
                        'missing' => [Ministry::title($ministry)],
                        'service' => $service,
                    ];
                }
            }
        }
        return $missing;
    }

    /**
     * @param $field
     * @param $originalRecords
     * @param $newIds
     */
    public function audit($field, $originalRecords, $newIds)
    {
        $originalIds = $originalRecords->pluck('id')->toArray();
    }

    /**
     * @return BelongsTo
     */
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * @param null $format
     * @return mixed
     */
    public function date($format = null)
    {
        if (is_null($format)) {
            return $this->day->date;
        } else {
            return $this->day->date->format($format);
        }
    }

    /**
     * @return BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return HasMany
     */
    public function funerals()
    {
        return $this->hasMany(Funeral::class);
    }

    /**
     * @return HasMany
     */
    public function weddings()
    {
        return $this->hasMany(Wedding::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @param $ministry
     * @return BelongsToMany
     */
    public function ministryParticipants($ministry)
    {
        return $this->belongsToMany(User::class)->wherePivot('category', $ministry)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function serviceGroups()
    {
        return $this->belongsToMany(ServiceGroup::class);
    }

    /**
     * @return BelongsToMany
     */
    public function participantsWithMinistry()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('category')
            ->wherePivotIn('category', ['P', 'O', 'M', 'A'], 'and', 'NotIn')
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @param $category
     * @param $participantIds
     * @return array
     */
    public function syncParticipantsByCategory($category, $participantIds)
    {
        $participants = [];
        foreach ($participantIds as $participantId) {
            $participants[$participantId] = ['category' => $category];
        }
        switch ($category) {
            case 'P':
                return $this->pastors()->sync($participants);
            case 'O':
                return $this->organists()->sync($participants);
            case 'M':
                return $this->sacristans()->sync($participants);
            case 'A':
                return $this->otherParticipants()->sync($participants);
        }
    }

    /**
     * @return string
     */
    public function offeringText()
    {
        return $this->offering_goal . ($this->offering_type ? ' (' . $this->offering_type . ')' : '');
    }

    /**
     * @return string
     */
    public function titleAndDescriptionCombinedText()
    {
        $description = [];
        $x = $this->titleText(false, false);
        if ($x != 'Gottesdienst') {
            $description[] = $x;
        }
        $x = $this->descriptionText();
        if ($x != '') {
            $description[] = $x;
        }
        return join('; ', $description);
    }

    /**
     * @param bool $short
     * @param bool $skipRites
     * @return string
     */
    public function titleText($short = true, $skipRites = false)
    {
        $elements = [];
        if ($this->title != '') {
            $elements[] = $x = $this->title;
        }
        if (!$skipRites) {
            if ($this->weddingsText() != '') {
                $elements[] = $x = $this->weddingsText();
            }
            if ($this->funeralsText() != '') {
                $elements[] = $x = $this->funeralsText();
            }
            if ($this->baptismsText() != '') {
                $elements[] = $x = 'Taufe(n)';
            }
        }
        if ((count($elements) == 1) && ($x != '') && ($x != $this->title)) {
            $elements[0] = ($short ? 'GD' : 'Gottesdienst') . ' mit ' . $elements[0];
        }
        return join(' / ', $elements) ?: ($short ? 'GD' : 'Gottesdienst');
    }

    public function getTitleTextAttribute()
    {
        return $this->titleText(true, true);
    }

    /**
     * @return string
     */
    public function weddingsText()
    {
        $weddings = [];
        /** @var Wedding $wedding */
        foreach ($this->weddings as $wedding) {
            $weddings[] = 'Trauung von ' . $wedding->spouse1_name
                . ($wedding->spouse1_birth_name ? ' (' . $wedding->spouse1_birth_name . ')' : '')
                . ' und ' . $wedding->spouse2_name
                . ($wedding->spouse2_birth_name ? ' (' . $wedding->spouse2_birth_name . ')' : '');
        }
        return (join('; ', $weddings));
    }

    /**
     * @return string
     */
    public function funeralsText()
    {
        $funerals = [];
        foreach ($this->funerals as $funeral) {
            $funerals[] = $funeral->type . ' von ' . $funeral->buried_name;
        }
        return (join('; ', $funerals));
    }

    /**
     * @param bool $emptyIfNotSet
     * @param bool $uhr
     * @param string $separator
     * @param bool $skipMinutes
     * @param bool $nbsp
     * @param bool $leadingZero
     * @return string
     */
    public function ccTimeText(
        $emptyIfNotSet = false,
        $uhr = true,
        $separator = ':',
        $skipMinutes = false,
        $nbsp = false,
        $leadingZero = false
    ) {
        if (null === $this->ccTime($emptyIfNotSet)) {
            return '';
        }
        return StringTool::timeText($this->ccTime(false), $uhr, $separator, $skipMinutes, $nbsp, $leadingZero);
    }

    /**
     * @param bool $emptyIfNotSet
     * @return Carbon|null
     * @throws Exception
     */
    public function ccTime($emptyIfNotSet = false)
    {
        if ($this->cc_alt_time == '00:00:00') {
            $this->cc_alt_time = null;
        }
        if (null === $this->cc_alt_time) {
            if ($emptyIfNotSet) {
                return null;
            }
            $t = $this->time;
        } else {
            $t = $this->cc_alt_time;
        }
        return new Carbon($this->day->date->format('Y-m-d') . ' ' . $t);
    }

    /**
     * Check if service description contains a specific text (case-insensitive!)
     * @param string $text Search for this text
     * @return bool True if text is in description
     */
    public function hasDescription(string $text): bool
    {
        return (false !== strpos(strtolower($this->descriptionText()), strtolower($text)));
    }

    /**
     * @param User $author
     * @param $text
     */
    public function notifyOfCreation(User $author, $text)
    {
        $mailText = sprintf($text, $author->name . ' (' . $author->email . ')') . "\r\n\r\n"
            . "Gottesdienst:\r\n=============\r\n";

        foreach ($this->revisionFormattedFieldNames as $key => $name) {
            $attribute = $this->getAttribute($key);
            if ($key == 'time') {
                $attribute = strftime('%H:%M', strtotime($attribute));
            }
            if ($key == 'day_id') {
                $attribute = strftime('%A, %d. %B %Y', $this->day->date->getTimestamp());
            }
            if ($key == 'city_id') {
                $attribute = $this->city->name;
            }
            if ($key == 'location_id') {
                if (is_object($this->location)) {
                    $attribute = $this->location->name;
                } else {
                    $attribute = $this->special_location;
                }
            }
            if ($key != 'special_location') {
                $mailText .= 'Feld "' . $name . '": "' . $attribute . '"' . "\r\n";
            }
        }

        $mailText .= "\r\n\r\nDiese Benachrichtigung wurde automatisch erzeugt.";

        $this->notify($mailText);
    }

    /**
     * @param $text
     */
    protected function notify($text)
    {
        $users = User::whereHas(
            'cities',
            function ($query) {
                $query->where('city_id', $this->city_id);
            }
        )->where('notifications', 1)
            ->get();

        foreach ($users as $user) {
            mail(
                $user->email,
                'Gottesdienst am ' . $this->day->date->format('d.m.Y')
                . ', ' . strftime('%H:%M Uhr', strtotime($this->time))
                . ', ' . ($this->special_location ?: $this->location->name) . "\r\n\r\n",
                utf8_decode($text),
                'From: no-reply@tailfingen.de'
            );
        }
    }

    /**
     * @return bool
     */
    function hasNonStandardCCLocation()
    {
        if ($this->special_location) {
            return true;
        } else {
            return ($this->cc_location != $this->location->cc_default_location);
        }
    }

    /**
     * @return mixed|string
     */
    function ccLocationText()
    {
        return ($this->cc_location ?: $this->locationText());
    }

    /**
     * @return Carbon
     */
    public function trueDate()
    {
        return Carbon::createFromTimeString($this->day->date->format('Y-m-d') . ' ' . $this->time);
    }

    /**
     * @return string
     */
    public function atText()
    {
        return is_object($this->location) ? $this->location->atText() : '(' . $this->locationText() . ')';
    }

    public function setDefaultOfferingValues()
    {
        if ($this->offering_goal == '') {
            if ((count($this->funerals) > 0) && $this->city->default_funeral_offering_goal != '') {
                $this->offering_goal = $this->city->default_funeral_offering_goal;
                $this->offering_description = $this->city->default_funeral_offering_description;
                return;
            }
            if ((count($this->weddings) > 0) && $this->city->default_wedding_offering_goal != '') {
                $this->offering_goal = $this->city->default_wedding_offering_goal;
                $this->offering_description = $this->city->default_wedding_offering_description;
                return;
            }
            if ($this->city->default_offering_goal != '') {
                $this->offering_goal = $this->city->default_offering_goal;
                $this->offering_description = $this->city->default_offering_description;
            }
        }
    }

    /**
     * @param $request
     * @param Service $service
     * @return array
     */
    public function associateParticipants($request, Service $service)
    {
        $participants = [];
        foreach (($request->get('participants') ?: []) as $category => $participantList) {
            foreach ($participantList as $participant) {
                $participant = User::createIfNotExists($participant);
                $participants[$category][$participant]['category'] = $category;
            }
        }

        $ministries = $request->get('ministries') ?: [];
        foreach ($ministries as $ministry) {
            if (isset($ministry['people'])) {
                foreach ($ministry['people'] as $participant) {
                    $participant = User::createIfNotExists($participant);
                    $participants[$ministry['description']][$participant]['category'] = $ministry['description'];
                }
            }
        }
        $this->participants()->sync([]);
        if (count($participants)) {
            foreach ($participants as $category => $participant) {
                $this->participants()->attach($participant);
            }
        }
        return $participants;
    }

    /**
     * @return BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('category');
    }

    public function checkIfPredicantNeeded()
    {
        if (count($this->pastors)) {
            $this->need_predicant = false;
            $this->save();
        }
    }

    /**
     * Get an instance of SeatFinder for this service
     * @return AbstractSeatFinder
     */
    public function getSeatFinder()
    {
        if ($this->seatFinder) {
            return $this->seatFinder;
        }
        if (is_object($this->location) && (count($this->location->seatingSections) > 0)) {
            return new RowBasedSeatFinder($this);
        }
        return new MaximumBasedSeatFinder($this);
    }

    /**
     * @param $s
     * @return mixed
     */
    public function formatTime($s)
    {
        return (false !== strpos($s, '%')) ? $this->dateTime()->formatLocalized($s) : $this->dateTime()->format($s);
    }

    /**
     * @return mixed
     */
    public function dateTime()
    {
        list ($hour, $minute) = explode(':', $this->time);
        return $this->day->date->copy()->setTime($hour, $minute, 0);
    }

    public function getBroadcastSnippet(): Google_Service_YouTube_LiveBroadcastSnippet
    {
        $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
        $broadcastSnippet->setTitle($this->broadcastTitle);
        $broadcastSnippet->setDescription($this->broadcastDescription);
        $broadcastSnippet->setScheduledStartTime($this->videoTimeString);
        return $broadcastSnippet;
    }

    public function getVideoSnippet(): Google_Service_YouTube_VideoSnippet
    {
        $videoSnippet = new \Google_Service_YouTube_VideoSnippet();
        $videoSnippet->setTitle($this->broadcastTitle);
        $videoSnippet->setDescription($this->broadcastDescription);
        $videoSnippet->setCategoryId(24);
        return $videoSnippet;
    }



    public function scopeInMonth(Builder $query, Carbon $date) {
        return $query->whereHas('day', function($query2) use ($date) {
            return $query2->inMonth($date);
        });
    }

    public function scopeInCities(Builder $query, $cities)
    {
        if ((!is_array($cities)) && (is_object($cities->first()))) $cities = $cities->pluck('id');
        return $query->whereIn('city_id', $cities);
    }


    public function getIsMineAttribute() {
        return $this->participants->contains(Auth::user());
    }

    public function getLiveDashboardUrlAttribute()
    {
        return $this->city->youtube_channel_url ? YoutubeHelper::getLiveDashboardUrl($this->city, $this->youtube_url) : '';
    }
}

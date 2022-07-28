<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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
use App\Liturgy\Block;
use App\Seating\AbstractSeatFinder;
use App\Seating\MaximumBasedSeatFinder;
use App\Seating\RowBasedSeatFinder;
use App\Services\NameService;
use App\Tools\StringTool;
use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use App\Traits\HasLiturgicalInfo;
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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
    use HasLiturgicalInfo;

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
        'weddings',
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
        'youtube_prefix_description',
        'youtube_postfix_description',
        'sermon_id',
        'announcements',
        'offering_text',
        'communiapp_id',
        'communiapp_listing_start',
        'slug',
        'controlled_access',
        'alt_liturgy_date',
        'date',
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
        'datetime',
        'liturgicalInfoDate',
        'liturgicalInfo',
        'keyDate',
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
        'date',
        'registration_online_start',
        'registration_online_end',
        'communiapp_listing_start',
        'alt_liturgy_date',
    ];
    /** @var AbstractSeatFinder */
    protected $seatFinder = null;
    /**
     * @var array
     */
    private $auditData = [];

// ACCESSORS
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
            $baptisms[] = NameService::fromName($baptism->candidate_name)->format(NameService::FIRST_LAST);
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
     * @return array|string
     * @throws \Throwable
     */
    public function getBroadcastDescriptionAttribute()
    {
        return (view(
            'services.youtube.snippet.description',
            ['service' => $this, 'liturgy' => Liturgy::getDayInfo($this->date->format('Y-m-d'))]
        )->render());
    }

    /**
     * @return mixed|string
     */
    public function getBroadcastTitleAttribute()
    {
        $liturgy = Liturgy::getDayInfo($this->date->format('Y-m-d'));
        return ($this->title ?: (isset($liturgy['title']) ? $liturgy['title']
            . ' (' . $this->date->format('d.m.Y') . ')'
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
     * @return array
     */
    public function ministries()
    {
        return $this->participantsWithMinistry->groupBy('pivot.category');
    }

    public function getCreditsAttribute()
    {
        $credits = [
            'Liturgie' => 'Liturgie: ' . $this->participantsText('P', true),
            'Orgel' => 'Orgel: ' . $this->participantsText('O', true)
        ];
        foreach ($this->ministries() as $ministry => $people) {
            $credits[$ministry] = $ministry . ': ' . $this->participantsText($ministry, true, true);
        }
        $credits['Mesner*in'] = 'Mesnerdienst: ' . $this->participantsText('M', true, true);
        $separator = utf8_encode(' ' . chr(183) . ' ');
        return join($separator, $credits);
    }
//

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
        return $this->date->formatLocalized($format);
    }

    public function getDatetimeAttribute()
    {
        return $this->date->copy();
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
    public function descriptionText($exclude = [])
    {
        $desc = [];
        if ($this->needs_reservations) {
            $desc['needs_reservations'] = ($this->registration_online_end ? 'Anmeldung nötig bis ' . $this->registration_online_end->format(
                    'd.m.Y, H:i'
                ) . ' Uhr' : 'Anmeldung nötig');
        }
        if ($this->baptism) {
            $desc['baptism'] = 'mit Taufen';
        }
        if ($this->eucharist) {
            $desc['eucharist'] = 'mit Abendmahl';
        }
        if ($this->getAttribute('description') != '') {
            $desc['description'] = $this->getAttribute('description');
        }
        foreach ($exclude as $key) {
            if (isset($desc[$key])) {
                unset($desc[$key]);
            }
        }
        return join('; ', $desc);
    }

    public function getFreeSeatsTextAttribute()
    {
        return $this->getSeatFinder()->freeSeatsText();
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
     * @return bool
     */
    public function getHasSeatsAttribute() {
        return $this->getSeatFinder()->hasSeats;
    }

    public function getIsMineAttribute()
    {
        return $this->participants->contains(Auth::user());
    }

    /**
     * @return array
     */
    public function getLiturgyAttribute()
    {
        return $this->liturgy;
    }

    public function getLiveDashboardUrlAttribute()
    {
        if (null === $this->city) {
            return '';
        }
        return $this->city->youtube_channel_url ? YoutubeHelper::getLiveDashboardUrl(
            $this->city,
            $this->youtube_url
        ) : '';
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

    public function getMaximumCapacityAttribute()
    {
        return $this->getSeatFinder()->maximumCapacity();
    }

    /**
     * @return array
     */
    public function getMinistriesByCategoryAttribute()
    {
        return $this->ministries();
    }

    /**
     * @return string
     */
    public function getOfferingsUrlAttribute()
    {
        return $this->attributes['offerings_url'] ?: ($this->city ? $this->city->default_offering_url : '');
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
     * Get all preachers for this service
     * @return Collection
     */
    public function getPreachersAttribute()
    {
        $sermonItem = null;
        foreach ($this->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if ($item->data_type == 'sermon') {
                    $sermonItem = $item;
                    continue;
                }
            }
            if (null !== $sermonItem) {
                continue;
            }
        }
        if (null === $sermonItem) {
            return collect();
        }
        return $sermonItem->recipients();
    }

    /**
     * @return int|mixed|void
     */
    public function getRemainingCapacityAttribute()
    {
        return $this->getSeatFinder()->remainingCapacity();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSacristansAttribute()
    {
        return $this->sacristans()->get();
    }

    /**
     * @return BelongsToMany
     */
    public function sacristans()
    {
        return $this->belongsToMany(User::class)->wherePivot('category', 'M')->withTimestamps();
    }

    /**
     * Get seating for this service
     * @return array
     */
    public function getSeatingAttribute()
    {
        return $this->getSeatFinder()->getSeatingTable();
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
                'prettyName' => $this->date->format('Ymd') . '-Liedblatt.' . pathinfo(
                        $this->songsheet,
                        PATHINFO_EXTENSION
                    )
            ]
        );
    }

    /**
     * @return false|string
     */
    public function getTimeAttribute()
    {
        return isset($this->attributes['time']) ? substr($this->attributes['time'], 0, 5) : '';
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
        return $this->date->copy()->setTimeZone('Europe/Berlin')
                ->format(($leadingZero ? 'H' : 'G').($skipMinutes ?  '' : $separator.'i'))
            .($uhr ? ($nbsp ? '&nbsp;' : ' ') . 'Uhr' : '');
    }

    public function getTitleTextAttribute()
    {
        return $this->titleText(false, false);
    }

    /**
     * @param bool $short
     * @param bool $skipRites
     * @return string
     */
    public function titleText($short = true, $skipRites = false)
    {
        $includeDefaultServiceTitle = true;
        $elements = [];
        if ($this->title != '') {
            $elements[] = $x = $this->title;
        }
        if (!$skipRites) {
            if ($this->weddingsText() != '') {
                $elements[] = $x = $this->weddingsText();
                $includeDefaultServiceTitle = false;
            }
            if ($this->funeralsText() != '') {
                $elements[] = $x = $this->funeralsText();
                $includeDefaultServiceTitle = false;
            }
            if ($this->baptismsText() != '') {
                $elements[] = $x = 'Taufe(n)';
            }
        }
        if ($includeDefaultServiceTitle && (count($elements) == 1) && ($x != '') && ($x != $this->title)) {
            $elements[0] = ($short ? 'GD' : 'Gottesdienst') . ' mit ' . $elements[0];
        }
        return join(' / ', $elements) ?: ($short ? 'GD' : 'Gottesdienst');
    }

    /**
     * @return string
     */
    public function weddingsText()
    {
        $weddings = [];
        /** @var Wedding $wedding */
        foreach ($this->weddings as $wedding) {
            $weddings[] = 'Trauung von ' . NameService::fromName($wedding->spouse1_name)->format(NameService::FIRST_LAST)
                . ($wedding->spouse1_birth_name ? ' (' . $wedding->spouse1_birth_name . ')' : '')
                . ' und ' . NameService::fromName($wedding->spouse2_name)->format(NameService::FIRST_LAST)
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
            $funerals[] = ($funeral->type  ?: 'Bestattung'). ' von ' . NameService::fromName($funeral->buried_name)->format(NameService::FIRST_LAST);
        }
        return (join('; ', $funerals));
    }

    /**
     * @return string
     */
    public function getVideoTimeStringAttribute()
    {
        $thisTime = Carbon::createFromTimeString(
            $this->date->format('Y-m-d') . ' ' . $this->time,
            'Europe/Berlin'
        );
        return $thisTime->setTimezone('UTC')->format('Y-m-d\TH:i:s') . '.000Z';
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
     * @param $date
     * @return Builder
     */
    public function scopeAtDate(Builder $query, $date) {
        return $query->whereDate('date', $date);
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeBetween(Builder $query, Carbon $start, Carbon $end)
    {
        $start = $start->copy()->setTime(0,0,0);
        $end = $end->copy()->setTime(23,59,59);
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * @param Builder $query
     * @param Carbon $start
     * @param Carbon $end
     * @return Builder
     */
    public function scopeDateRange(Builder $query, Carbon $start, Carbon $end)
    {
        $start = $start->copy()->setTime(0,0,0);
        $end = $end->copy()->setTime(23,59,59);
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * @param Builder $query
     * @param Carbon $date
     * @return Builder
     */
    public function scopeEndingAt(Builder $query, Carbon $date)
    {
        return $query->whereDate('date', '<=', $date);
    }

    /**
     * @param Builder $query
     * @param $ministries
     * @return Builder
     */
    public function scopeHavingOpenMinistries(Builder $query, $ministries)
    {
        return $query->whereDoesntHave(
                'participants',
                function ($query) use ($ministries) {
                    $query->whereIn('service_user.category', $ministries);
                }
            )
            ->orderBy('date', 'ASC');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeHidden(Builder $query)
    {
        return $query->where('hidden', 1);
    }

    public function scopeInCities(Builder $query, $cities)
    {
        if ((!is_array($cities)) && (is_object($cities->first()))) {
            $cities = $cities->pluck('id');
        }
        return $query->where(function($q) use ($cities) {
            $q->whereIn('city_id', $cities);
            $q->orWhereHas('relatedCities', function ($q2) use ($cities) {
                $q2->whereIn('cities.id', $cities);
            });
        });


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
     * @param int $year Year
     * @param int $month Month
     * @return Builder
     */
    public function scopeInMonth(Builder $query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeInMonthByDate(Builder $query, Carbon $date)
    {
        return $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
    }

    /**
     * Get services used as agenda
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsAgenda(Builder $query)
    {
        return $query->whereDate('date', '1978-03-05');
    }

    /**
     * Get services not used as agenda
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsNotAgenda(Builder $query)
    {
        return $query->whereDate('date', '!=', '1978-03-05');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotHidden(Builder $query)
    {
        return $query->where('hidden', 0);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrdered(Builder $query, $direction = 'ASC')
    {
        return $query->orderBy('date', $direction);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderedDesc(Builder $query)
    {
        return $query->ordered('DESC');
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
     * @param Carbon $date
     * @return Builder
     */
    public function scopeStartingFrom(Builder $query, Carbon $date)
    {
        return $query->whereDate('date', '>=', $date);
    }

    /**
     * Get services where a specific user is a participant
     * @param Builder $query
     * @param User $user
     * @param string $category
     * @return Builder
     */
    public function scopeUserParticipates(Builder $query, User $user, $category = null)
    {
        return $query->whereHas('participants', function($query2) use ($user, $category) {
            $query2->where('user_id', $user->id);
            if ($category) $query2->where('category', $category);
        });
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWritable(Builder $query)
    {
        return $query->whereIn('city_id', Auth::user()->writableCities->pluck('id'));
    }

// SETTERS
// SETTERS
// SETTERS
// SETTERS
// SETTERS
// SETTERS
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
            if (($service->date <= $end) && ($service->date >= $start)) {
                $events[$service->date->format('YmdHis')][] = $service;
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
     * Boot method
     * -> register creating/updating handlers to ensure slug is always up to date
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function($service) {
            $service->slug = $service->createSlug();
        });

        static::updating(function($service) {
            $service->slug = $service->createSlug();
        });
    }

    /**
     * @param $request
     * @param Service $service
     * @return array
     */
    public function associateParticipants($request, Service $service)
    {
        if (!$request->has('participants')) return;

        $participants = [];
        foreach (($request->get('participants') ?: []) as $category => $participantList) {
            foreach ($participantList as $participant) {
                if ($participant) {
                    $participant = User::createIfNotExists($participant);
                    $participants[$category][$participant]['category'] = $category;
                }
            }
        }

        $ministries = $request->get('ministries') ?: [];
        foreach ($ministries as $ministry) {
            if (isset($ministry['people'])) {
                foreach ($ministry['people'] as $participant) {
                    if ($participant) {
                        $participant = User::createIfNotExists($participant);
                        $participants[$ministry['description']][$participant]['category'] = $ministry['description'];
                    }
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

    /**
     * @return string
     */
    public function atText()
    {
        return is_object($this->location) ? $this->location->atText() : '(' . $this->locationText() . ')';
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
     * @return HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return mixed|string
     */
    function ccLocationText()
    {
        return ($this->cc_location ?: $this->locationText());
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
        return new Carbon($this->date->format('Y-m-d') . ' ' . $t);
    }

    public function checkIfPredicantNeeded()
    {
        if (count($this->pastors)) {
            $this->need_predicant = false;
            $this->save();
        }
    }

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function createSlug()
    {
        return $this->date->format('Ymd-Hi').'-'.$this->id
            .($this->city ? '-'.Str::slug($this->city->name) : '');
    }

    /**
     * @return mixed
     */
    public function dateTime()
    {
        if (false === strpos($this->time, ':')) return new Carbon();
        list ($hour, $minute) = explode(':', $this->time);
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date->format('Y-m-d').' '.$this->time.':00', 'Europe/Berlin');
    }

    /**
     * @return BelongsTo
     */
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Get total number of participants + bookings
     */
    public function estimatePeoplePresent()
    {
        $number = $this->participants->count();
        foreach ($this->bookings as $booking) {
            $number += $booking->number;
        }
        return $number;
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
     * @return HasMany
     */
    public function funerals()
    {
        return $this->hasMany(Funeral::class);
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

    public function liturgyBlocks()
    {
        return $this->hasMany(Block::class);
    }

    /**
     * @return BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
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
     * @return string
     */
    public function offeringText()
    {
        return $this->offering_goal . ($this->offering_type ? ' (' . $this->offering_type . ')' : '');
    }

    public function oneLiner($title = false)
    {
        return ($title ? $this->titleText(false) . ', ' : '') . $this->date->format(
                'd.m.Y'
            ) . ', ' . $this->timeText() . ', ' . $this->locationText();
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

    public function sermon()
    {
        return $this->belongsTo(Sermon::class);
    }

    /**
     * @return BelongsToMany
     */
    public function serviceGroups()
    {
        return $this->belongsToMany(ServiceGroup::class);
    }

    public function setDefaultOfferingValues()
    {
        if (null === $this->city) {
            return;
        }
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
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
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
     * Get title plus some participants (for calendar entries)
     * @param string[] $categories
     * @return string
     */
    public function titleTextWithParticipants($categories = ['P', 'O', 'M'])
    {
        $participants = [];
        foreach ($categories as $category) {
            $participants[] = $category.': '.$this->participantsText($category);
        }
        return trim($this->titleText().' '.join(' ', $participants));
    }

    /**
     * @return Carbon
     */
    public function trueDate()
    {
        return Carbon::createFromTimeString($this->date->format('Y-m-d') . ' ' . $this->time);
    }

    public function unsetAdditionalFields() {
        $this->appends = [];
    }

    public function updateRelatedCitiesFromRequest(Request $request)
    {
        $cityIds = $request->get('related_cities', []);
        if (!is_array($cityIds)) return;
        $existing = $this->relatedCities->pluck('id');
        $result = $existing->filter(function ($item) use ($cityIds) {
            if (!Auth::user()->cities->pluck('id')->contains($item)) return true;
            if (in_array($item, $cityIds)) return true;
        });
        foreach ($cityIds as $cityId) {
            if (!$result->contains($cityId)) $result->push($cityId);
        }
        $this->relatedCities()->sync($result);
    }

    /**
     * @return BelongsToMany
     */
    public function relatedCities()
    {
        return $this->belongsToMany(City::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function weddings()
    {
        return $this->hasMany(Wedding::class);
    }

    public function getKeyDateAttribute()
    {
        return $this->date->format('Y-m-d');
    }
}

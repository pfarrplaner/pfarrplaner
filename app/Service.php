<?php

namespace App;

use App\Http\Requests\StoreServiceRequest;
use App\Mail\ServiceUpdated;
use App\Tools\StringTool;
use App\Traits\HasAttachmentsTrait;
use App\Traits\HasCommentsTrait;
use App\Traits\TracksChangesTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Venturecraft\Revisionable\RevisionableTrait;

class Service extends Model
{
    use RevisionableTrait;
    use HasCommentsTrait;
    use TracksChangesTrait;
    use HasAttachmentsTrait;

    public $liturgy = [];

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

    protected $revisionEnabled = true;
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
    );

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
    ];

    protected $appends = [
        'pastors',
        'organists',
        'sacristans',
        'otherParticipants',
        'descriptionText',
        'locationText',
        'timeText',
        'baptismsText',
        'liturgy',
        'ministriesByCategory',
    ];

    protected $attributes = [
        'offering_type' => 'eO',
    ];

    private $auditData = [];

    public static function boot()
    {
        parent::boot();
    }

    public function audit($field, $originalRecords, $newIds) {
        $originalIds = $originalRecords->pluck('id')->toArray();
    }

    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function date($format = null) {
        if (is_null($format)) return $this->day->date; else return $this->day->date->format($format);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function baptisms() {
        return $this->hasMany(Baptism::class);
    }

    public function funerals() {
        return $this->hasMany(Funeral::class);
    }

    public function weddings() {
        return $this->hasMany(Wedding::class);
    }

    public function participants() {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('category');
    }

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function pastors() {
        return $this->belongsToMany(User::class)->wherePivot('category', 'P')->withTimestamps();
    }

    public function organists() {
        return $this->belongsToMany(User::class)->wherePivot('category', 'O')->withTimestamps();
    }

    public function sacristans() {
        return $this->belongsToMany(User::class)->wherePivot('category', 'M')->withTimestamps();
    }


    public function otherParticipants() {
        return $this->belongsToMany(User::class)->wherePivot('category', 'A')->withTimestamps();
    }

    public function ministryParticipants($ministry) {
        return $this->belongsToMany(User::class)->wherePivot('category', $ministry)->withTimestamps();
    }

    public function tags() {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function serviceGroups() {
        return $this->belongsToMany(ServiceGroup::class);
    }

    public function participantsByCategory($category) {
        switch ($category) {
            case 'P': return $this->pastors;
            case 'O': return $this->organists;
            case 'M': return $this->sacristans;
            case 'A': return $this->otherParticipants;
            default:
                return $this->ministries()[$category] ?? collect([]);
        }

    }

    public function participantsWithMinistry() {
        return $this->belongsToMany(User::class)
            ->withPivot('category')
            ->wherePivotIn('category', ['P', 'O', 'M', 'A'], 'and', 'NotIn')
            ->withTimestamps();
    }

    public function ministries() {
        $ministries = [];
        foreach ($this->participantsWithMinistry as $participant) {
            if (!isset($ministries[$participant->pivot->category])) $ministries[$participant->pivot->category] = new Collection();
            $ministries[$participant->pivot->category]->push($participant);
        }
        return $ministries;
    }

    public function getMinistriesByCategoryAttribute() {
        return $this->ministries();
    }


    public function participantsText($category, $fullName = false, $withTitle = true, $glue = ', ') {
        $participants = $this->participantsByCategory($category);
        $names = [];
        foreach ($participants as $participant) {
            $names[] = ($fullName ? $participant->fullName($withTitle) : $participant->lastName($withTitle));
        }
        return join($glue, $names);
    }

    public function syncParticipantsByCategory ($category, $participantIds) {
        $participants = [];
        foreach ($participantIds as $participantId) {
            $participants[$participantId] = ['category' => $category];
        }
        switch ($category) {
            case 'P': return $this->pastors()->sync($participants);
            case 'O': return $this->organists()->sync($participants);
            case 'M': return $this->sacristans()->sync($participants);
            case 'A': return $this->otherParticipants()->sync($participants);
        }
    }

    public function locationText() {
        return $this->special_location ?: (is_object( $this->location) ? $this->location->name : '');
    }

    public function descriptionText() {
        $desc = [];
        if ($this->baptism) $desc[] = 'mit Taufen';
        if ($this->eucharist) $desc[] = 'mit Abendmahl';
        if ($this->getAttribute('description') != '') $desc[] = $this->getAttribute('description');
        return join('; ', $desc);
    }

    public function timeText($uhr = true, $separator=':', $skipMinutes = false, $nbsp = false, $leadingZero = false)  {
        return StringTool::timeText($this->time, $uhr, $separator, $skipMinutes, $nbsp, $leadingZero);
    }

    public function offeringText() {
        return $this->offering_goal.($this->offering_type ? ' ('.$this->offering_type.')' : '');
    }

    public function baptismsText($includeCount = false, $includeText = false) {
        $baptisms = [];
        foreach ($this->baptisms as $baptism) {
            $baptisms[] = $baptism->candidate_name;
        }
        return
            ($includeText ? ($this->baptisms()->count() == 1 ? 'Taufe ' : 'Taufen ').'von ' : '')
            .($includeCount ? $this->baptisms()->count().' '.($this->baptisms()->count() == 1 ? 'Taufe ' : 'Taufen ') : '')
            .join('; ', $baptisms);
    }

    public function funeralsText() {
        $funerals = [];
        foreach ($this->funerals as $funeral) {
            $funerals[] = $funeral->type.' von '.$funeral->buried_name;
        }
        return (join('; ', $funerals));
    }

    public function weddingsText() {
        $weddings = [];
        /** @var Wedding $wedding */
        foreach ($this->weddings as $wedding) {
            $weddings[] = 'Trauung von '.$wedding->spouse1_name
                .($wedding->spouse1_birth_name ? ' ('.$wedding->spouse1_birth_name.')' : '')
                .' und '.$wedding->spouse2_name
                .($wedding->spouse2_birth_name ? ' ('.$wedding->spouse2_birth_name.')' : '');
        }
        return (join('; ', $weddings));
    }

    public function titleText($short = true, $skipRites = false) {
        $elements = [];
        if ($x = $this->title) $elements[] = $x;
        if (!$skipRites) {
            if ($x = $this->weddingsText()) $elements[] = $x;
            if ($x = $this->funeralsText()) $elements[] = $x;
            if ($x = $this->baptismsText()) $elements[] = 'Taufe(n)';
        }
        if ((count($elements) == 1) && ($x != '') && ($x != $this->title)) {
            $elements[0] = ($short ? 'GD' : 'Gottesdienst').' mit '.$elements[0];
        }
        return join(' / ', $elements) ?: ($short ? 'GD' : 'Gottesdienst');
    }


    public function titleAndDescriptionCombinedText()
    {
        $description = [];
        if (($x = $this->titleText(false, false)) != 'Gottesdienst') $description[] = $x;
        if ($x = $this->descriptionText) $description[] = $x;
        return join('; ', $description);
    }

    public function ccTime($emptyIfNotSet = false) {
        if ($this->cc_alt_time == '00:00:00') $this->cc_alt_time = null;
        if (null === $this->cc_alt_time) {
            if ($emptyIfNotSet) return null;
            $t = $this->time;
        } else {
            $t = $this->cc_alt_time;
        }
        return new Carbon($this->day->date->format('Y-m-d').' '.$t);
    }

    public function ccTimeText($emptyIfNotSet = false, $uhr = true, $separator=':', $skipMinutes = false, $nbsp = false, $leadingZero = false)  {
        if (null === $this->ccTime($emptyIfNotSet)) return '';
        return StringTool::timeText($this->ccTime(false), $uhr, $separator, $skipMinutes, $nbsp, $leadingZero);
    }

    /**
     * Check if service description contains a specific text (case-insensitive!)
     * @param string $text Search for this text
     * @return bool True if text is in description
     */
    public function hasDescription(string $text): bool {
        return (false !== strpos(strtolower($this->descriptionText()), strtolower($text)));
    }

    public function notifyOfCreation(User $author, $text) {

        $mailText = sprintf($text, $author->name.' ('.$author->email.')')."\r\n\r\n"
            ."Gottesdienst:\r\n=============\r\n";

        foreach ($this->revisionFormattedFieldNames as $key => $name) {
            $attribute = $this->getAttribute($key);
            if ($key == 'time') $attribute = strftime('%H:%M', strtotime($attribute));
            if ($key == 'day_id') $attribute = strftime('%A, %d. %B %Y', $this->day->date->getTimestamp());
            if ($key == 'city_id') $attribute = $this->city->name;
            if ($key == 'location_id') {
                if (is_object($this->location)) {
                    $attribute = $this->location->name;
                } else {
                    $attribute = $this->special_location;
                }
            }
            if ($key != 'special_location') $mailText .= 'Feld "'.$name.'": "'.$attribute.'"'."\r\n";
        }

        $mailText .= "\r\n\r\nDiese Benachrichtigung wurde automatisch erzeugt.";

        $this->notify($mailText);
    }


    protected function notify ($text) {
        $users = User::whereHas('cities', function($query){
            $query->where('city_id', $this->city_id);
        })->where('notifications', 1)
            ->get();

        foreach ($users as $user) {
            mail($user->email, 'Gottesdienst am '        .$this->day->date->format('d.m.Y')
                .', '.strftime('%H:%M Uhr', strtotime($this->time))
                .', '.($this->special_location ?: $this->location->name)."\r\n\r\n",
                utf8_decode($text), 'From: no-reply@tailfingen.de');
        }
    }

    function hasNonStandardCCLocation() {
        if ($this->special_location) {
            return true;
        } else {
            return ($this->cc_location != $this->location->cc_default_location);
        }
    }

    function ccLocationText() {
        return ($this->cc_location ?: $this->locationText());
    }


    public function getPastorsAttribute() {
        return $this->pastors()->get();
    }

    public function getOrganistsAttribute() {
        return $this->organists()->get();
    }

    public function getSacristansAttribute() {
        return $this->sacristans()->get();
    }

    public function getOtherParticipantsAttribute() {
        return $this->otherParticipants()->get();
    }

    public function getDescriptionTextAttribute() {
        return $this->descriptionText();
    }

    public function getLocationTextAttribute() {
        return $this->locationText();
    }

    public function getTimeTextAttribute() {
        return $this->timeText();
    }

    public function getBaptismsTextAttribute() {
        return $this->baptismsText(true);
    }

    public function getTimeAttribute() {
        return isset($this->attributes['time']) ? substr($this->attributes['time'], 0, 5) : '';
    }

    public function setTimeAttribute($time) {
        $this->attributes['time'] = substr($time, 0, 5);
    }


    public function scopeRegularForCity(Builder $query, City $city) {
        return $query->where('city_id', $city->id)
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('weddings');
    }

    public function trueDate() {
        return Carbon::createFromTimeString($this->day->date->format('Y-m-d').' '.$this->time);
    }

    public function atText() {
        return is_object($this->location) ? $this->location->atText() : '('.$this->locationText().')';
    }

    public function scopeInCity(Builder $query, $city) {
        return $query->where('city_id', $city->id);
    }

    public function scopeDateRange(Builder $query, Carbon $start, Carbon $end) {
        return $query->whereHas('day', function ($query2) use ($start, $end) {
            $query2->where('date', '>=', $start)
                ->where('date', '<=', $end);
        });
    }

    public function scopeOrdered(Builder $query) {
        return $query->select('services.*')
            ->join('days', 'services.day_id', 'days.id')
            ->orderBy('days.date')
            ->orderBy('time');
    }


    public function setDefaultOfferingValues() {
        if ($this->offering_goal == '') {
            if ((count($this->funerals) >0) && $this->city->default_funeral_offering_goal != '') {
                $this->offering_goal = $this->city->default_funeral_offering_goal;
                $this->offering_description = $this->city->default_funeral_offering_description;
                return;
            }
            if ((count($this->weddings) >0) && $this->city->default_wedding_offering_goal != '') {
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


    public function associateParticipants($request, Service $service) {
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
        if (count($participants)) {
            $this->participants()->sync([]);
            foreach ($participants as $category => $participant) {
                $this->participants()->attach($participant);
            }
        }
        return $participants;
    }

    public function checkIfPredicantNeeded() {
        if (count($this->pastors)) {
            $this->need_predicant = false;
            $this->save();
        }
    }



    /**
     * Mix a collection of services into an array of events
     * @param $events
     * @param $services
     * @param Carbon $start
     * @param Carbon $end
     * @return mixed
     */
    public static function mix($events, $services, Carbon $start, Carbon $end) {

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

    public function getLiturgyAttribute() {
        return $this->liturgy;
    }


    /**
     * Return services with empty entries for specific ministries
     * @param $ministries array
     * @return array
     */
    public static function withOpenMinistries($ministries) {
        $missing2 = [];

        foreach ($ministries as $ministry) {
            $missing2[$ministry] = Service::with(['location', 'day'])
                ->whereDoesntHave(
                    'participants',
                    function ($query) use ($ministry) {
                        $query->where('service_user.category', $ministry);
                    }
                )
                ->whereIn('city_id', Auth::user()->writableCities->pluck('id'))
                ->whereHas(
                    'day',
                    function ($query)  {
                        $query->where('date', '>=', now());
                    }
                )
                ->select(['services.*', 'days.date'])
                ->join('days', 'days.id', '=', 'day_id')
                ->orderBy('days.date', 'ASC')
                ->orderBy('time', 'ASC')
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
}

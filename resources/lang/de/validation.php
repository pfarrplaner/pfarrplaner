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

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => ':attribute muss akzeptiert werden.',
    'active_url' => ':attribute ist keine gültige Internet-Adresse.',
    'after' => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach dem :date oder gleich dem :date sein.',
    'alpha' => ':attribute darf nur aus Buchstaben bestehen.',
    'alpha_dash' => ':attribute darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen.',
    'alpha_num' => ':attribute darf nur aus Buchstaben und Zahlen bestehen.',
    'array' => ':attribute muss ein Array sein.',
    'before' => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor dem :date oder gleich dem :date sein.',
    'between' => [
        'numeric' => ':attribute muss zwischen :min & :max liegen.',
        'file' => ':attribute muss zwischen :min & :max Kilobytes groß sein.',
        'string' => ':attribute muss zwischen :min & :max Zeichen lang sein.',
        'array' => ':attribute muss zwischen :min & :max Elemente haben.',
    ],
    'boolean' => ":attribute muss entweder 'true' oder 'false' sein.",
    'confirmed' => ':attribute stimmt nicht mit der Bestätigung überein.',
    'date' => ':attribute muss ein gültiges Datum sein.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute entspricht nicht dem gültigen Format für :format.',
    'different' => ':attribute und :other müssen sich unterscheiden.',
    'digits' => ':attribute muss :digits Stellen haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Stellen haben.',
    'dimensions' => ':attribute hat ungültige Bildabmessungen.',
    'distinct' => ':attribute beinhaltet einen bereits vorhandenen Wert.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => ':attribute muss ausgefüllt sein.',
    'gt' => [
        'numeric' => ':attribute muss mindestens :value sein.',
        'file' => ':attribute muss mindestens :value Kilobytes groß sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
        'array' => ':attribute muss mindestens :value Elemente haben.',
    ],
    'gte' => [
        'numeric' => ':attribute muss größer oder gleich :value sein.',
        'file' => ':attribute muss größer oder gleich :value Kilobytes sein.',
        'string' => ':attribute muss größer oder gleich :value Zeichen lang sein.',
        'array' => ':attribute muss größer oder gleich :value Elemente haben.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'in_array' => 'Der gewählte Wert für :attribute kommt nicht in :other vor.',
    'integer' => ':attribute muss eine ganze Zahl sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':attribute muss ein gültiger JSON-String sein.',
    'lt' => [
        'numeric' => ':attribute muss kleiner :value sein.',
        'file' => ':attribute muss kleiner :value Kilobytes groß sein.',
        'string' => ':attribute muss kleiner :value Zeichen lang sein.',
        'array' => ':attribute muss kleiner :value Elemente haben.',
    ],
    'lte' => [
        'numeric' => ':attribute muss kleiner oder gleich :value sein.',
        'file' => ':attribute muss kleiner oder gleich :value Kilobytes sein.',
        'string' => ':attribute muss kleiner oder gleich :value Zeichen lang sein.',
        'array' => ':attribute muss kleiner oder gleich :value Elemente haben.',
    ],
    'max' => [
        'numeric' => ':attribute darf maximal :max sein.',
        'file' => ':attribute darf maximal :max Kilobytes groß sein.',
        'string' => ':attribute darf maximal :max Zeichen haben.',
        'array' => ':attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes' => ':attribute muss den Dateityp :values haben.',
    'mimetypes' => ':attribute muss den Dateityp :values haben.',
    'min' => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
        'array' => ':attribute muss mindestens :min Elemente haben.',
    ],
    'not_in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'not_regex' => ':attribute hat ein ungültiges Format.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => ':attribute entspricht nicht deinem aktuellen Passwort.',
    'present' => ':attribute muss vorhanden sein.',
    'phone_number' => ':attribute muss eine gültige Telefonnummer sein.',
    'regex' => ':attribute Format ist ungültig.',
    'required' => 'Das Feld ":attribute" muss ausgefüllt sein.',
    'required_if' => ':attribute muss ausgefüllt sein, wenn :other :value ist.',
    'required_unless' => ':attribute muss ausgefüllt sein, wenn :other nicht :values ist.',
    'required_with' => ':attribute muss angegeben werden, wenn :values ausgefüllt wurde.',
    'required_with_all' => ':attribute muss angegeben werden, wenn :values ausgefüllt wurde.',
    'required_without' => ':attribute muss angegeben werden, wenn :values nicht ausgefüllt wurde.',
    'required_without_all' => ':attribute muss angegeben werden, wenn keines der Felder :values ausgefüllt wurde.',
    'same' => ':attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => ':attribute muss gleich :size sein.',
        'file' => ':attribute muss :size Kilobyte groß sein.',
        'string' => ':attribute muss :size Zeichen lang sein.',
        'array' => ':attribute muss genau :size Elemente haben.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => ':attribute muss ein String sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => ':attribute ist schon vergeben.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'url' => ':attribute muss eine URL sein.',
    'uuid' => ':attribute muss ein UUID sein.',
    'zip' => ':attribute muss eine gültige Postleitzahl sein.',

    // seatable validators
    'seatable' => 'Die gewünschte Anzahl zusammenhängender Plätze (:input) ist in diesem Gottesdienst nicht verfügbar.',
    'seatable_fixed' => 'Der gewünschte Platz :input ist in diesem Gottesdienst nicht verfügbar.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'Name',
        'username' => 'Benutzername',
        'email' => 'E-Mail-Adresse',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort-Bestätigung',
        'city' => 'Stadt',
        'country' => 'Land',
        'address' => 'Adresse',
        'phone' => 'Telefonnummer',
        'mobile' => 'Handynummer',
        'age' => 'Alter',
        'sex' => 'Geschlecht',
        'gender' => 'Geschlecht',
        'day' => 'Tag',
        'month' => 'Monat',
        'year' => 'Jahr',
        'hour' => 'Stunde',
        'minute' => 'Minute',
        'second' => 'Sekunde',
        'title' => 'Titel',
        'content' => 'Inhalt',
        'description' => 'Beschreibung',
        'excerpt' => 'Auszug',
        'date' => 'Datum',
        'time' => 'Uhrzeit',
        'available' => 'verfügbar',
        'size' => 'Größe',
        'city_id' => 'Kirchengemeinde',
        'offering_goal' => 'Opferzweck',
        'lastService' => 'Datum des letzten Gottesdienstes',
        'offerings' => 'Opfersumme',
        'offering_text' => 'Text zum Opfer',
        'candidate_name' => 'Name des Täuflings',
        'candidate_address' => 'Adresse',
        'candidate_zip' => 'Postleitzahl',
        'candidate_city' => 'Ort',
        'candidate_phone' => 'Telefon',
        'candidate_email' => 'E-Mail',
        'first_contact_on' => 'Datum des Erstkontakts',
        'appointment' => 'Gesprächstermin',
        'registration_document' => 'Anmeldedokument',
        'buried_name' => 'Name des/der Verstorbenen',
        'buried_address' => 'Adresse',
        'buried_zip' => 'Postleitzahl',
        'buried_city' => 'Ort',
        'buried_phone' => 'Telefon',
        'buried_email' => 'E-Mail',
        'relative_name' => 'Name des/der Angehörigen',
        'relative_address' => 'Adresse',
        'relative_zip' => 'Postleitzahl',
        'relative_city' => 'Ort',
        'relative_phone' => 'Telefon',
        'relative_email' => 'E-Mail',
        'spouse1_name' => 'Name des 1. Ehepartners',
        'spouse1_address' => 'Adresse',
        'spouse1_zip' => 'Postleitzahl',
        'spouse1_city' => 'Ort',
        'spouse1_phone' => 'Telefon',
        'spouse1_email' => 'E-Mail',
        'spouse2_name' => 'Name des 2. Ehepartners',
        'spouse2_address' => 'Adresse',
        'spouse2_zip' => 'Postleitzahl',
        'spouse2_city' => 'Ort',
        'spouse2_phone' => 'Telefon',
        'spouse2_email' => 'E-Mail',
        'current_password' => 'Aktuelles Passwort',
        'new_password' => 'Neues Passwort',
        'new_password_confirmation' => 'Passwortbestätigung',

        'absence_id' => 'absence_id',
        'user_id' => 'user_id',
        'from' => 'von',
        'to' => 'bis',
        'replacement' => 'Vertretung',
        'reason' => 'Beschreibung',
        'status' => 'Status',
        'replacement_notes' => 'Notizen für die Vertretung',
        'file' => 'Datei',
        'attachable_id' => 'attachable_id',
        'attachable_type' => 'attachable_type',
        'service_id' => 'service_id',
        'first_contact_with' => 'Erstkontakt mit',
        'registered' => 'Anmeldung erhalten',
        'signed' => 'Anmeldung unterzeichnet',
        'docs_ready' => 'docs_ready',
        'docs_where' => 'docs_where',
        'done' => 'done',
        'code' => 'code',
        'contact' => 'contact',
        'number' => 'number',
        'public_events_calendar_url' => 'public_events_calendar_url',
        'default_offering_goal' => 'default_offering_goal',
        'default_offering_description' => 'default_offering_description',
        'default_funeral_offering_goal' => 'default_funeral_offering_goal',
        'default_funeral_offering_description' => 'default_funeral_offering_description',
        'default_wedding_offering_goal' => 'default_wedding_offering_goal',
        'default_wedding_offering_description' => 'default_wedding_offering_description',
        'op_domain' => 'op_domain',
        'op_customer_key' => 'op_customer_key',
        'op_customer_token' => 'op_customer_token',
        'podcast_title' => 'podcast_title',
        'podcast_logo' => 'podcast_logo',
        'sermon_default_image' => 'sermon_default_image',
        'homepage' => 'homepage',
        'podcast_owner_name' => 'podcast_owner_name',
        'podcast_owner_email' => 'podcast_owner_email',
        'google_auth_code' => 'google_auth_code',
        'google_access_token' => 'google_access_token',
        'google_refresh_token' => 'google_refresh_token',
        'youtube_channel_url' => 'youtube_channel_url',
        'konfiapp_apikey' => 'konfiapp_apikey',
        'day_id' => 'day_id',
        'permission' => 'permission',
        'sorting' => 'sorting',
        'body' => 'body',
        'private' => 'private',
        'commentable_id' => 'commentable_id',
        'commentable_type' => 'commentable_type',
        'day_type' => 'day_type',
        'connection' => 'connection',
        'queue' => 'queue',
        'payload' => 'payload',
        'exception' => 'exception',
        'failed_at' => 'failed_at',
        'text' => 'text',
        'announcement' => 'announcement',
        'type' => 'type',
        'wake' => 'wake',
        'relative_contact_data' => 'relative_contact_data',
        'wake_location' => 'wake_location',
        'dob' => 'dob',
        'dod' => 'dod',
        'sortable' => 'sortable',
        'programmable_id' => 'programmable_id',
        'programmable_type' => 'programmable_type',
        'default_time' => 'default_time',
        'cc_default_location' => 'cc_default_location',
        'alternate_location_id' => 'alternate_location_id',
        'general_location_name' => 'general_location_name',
        'at_text' => 'at_text',
        'migration' => 'migration',
        'batch' => 'batch',
        'permission_id' => 'permission_id',
        'model_type' => 'model_type',
        'model_id' => 'model_id',
        'role_id' => 'role_id',
        'parish_id' => 'parish_id',
        'zip' => 'zip',
        'congregation_name' => 'congregation_name',
        'congregation_url' => 'congregation_url',
        'token' => 'token',
        'guard_name' => 'guard_name',
        'replacement_id' => 'replacement_id',
        'revisionable_type' => 'revisionable_type',
        'revisionable_id' => 'revisionable_id',
        'key' => 'key',
        'old_value' => 'old_value',
        'new_value' => 'new_value',
        'seating_section_id' => 'seating_section_id',
        'seats' => 'seats',
        'divides_into' => 'divides_into',
        'spacing' => 'spacing',
        'location_id' => 'location_id',
        'seating_model' => 'seating_model',
        'service_group_id' => 'service_group_id',
        'tag_id' => 'tag_id',
        'category' => 'category',
        'special_location' => 'special_location',
        'need_predicant' => 'need_predicant',
        'baptism' => 'baptism',
        'eucharist' => 'eucharist',
        'offerings_counter1' => 'offerings_counter1',
        'offerings_counter2' => 'offerings_counter2',
        'offering_description' => 'offering_description',
        'offering_type' => 'offering_type',
        'others' => 'others',
        'cc' => 'cc',
        'cc_location' => 'cc_location',
        'cc_lesson' => 'cc_lesson',
        'cc_staff' => 'cc_staff',
        'location_description' => 'location_description',
        'internal_remarks' => 'internal_remarks',
        'offering_amount' => 'offering_amount',
        'cc_alt_time' => 'cc_alt_time',
        'youtube_url' => 'youtube_url',
        'cc_streaming_url' => 'cc_streaming_url',
        'offerings_url' => 'offerings_url',
        'meeting_url' => 'meeting_url',
        'recording_url' => 'recording_url',
        'songsheet' => 'songsheet',
        'external_url' => 'external_url',
        'sermon_title' => 'sermon_title',
        'sermon_reference' => 'sermon_reference',
        'sermon_image' => 'sermon_image',
        'sermon_description' => 'sermon_description',
        'konfiapp_event_type' => 'konfiapp_event_type',
        'konfiapp_event_qr' => 'konfiapp_event_qr',
        'hidden' => 'hidden',
        'odd_start' => 'odd_start',
        'odd_end' => 'odd_end',
        'even_start' => 'even_start',
        'even_end' => 'even_end',
        'subscription_type' => 'subscription_type',
        'approver_id' => 'approver_id',
        'value' => 'value',
        'email_verified_at' => 'email_verified_at',
        'api_token' => 'api_token',
        'remember_token' => 'remember_token',
        'notifications' => 'notifications',
        'office' => 'office',
        'preference_cities' => 'preference_cities',
        'new_features' => 'new_features',
        'manage_absences' => 'manage_absences',
        'image' => 'image',
        'method' => 'method',
        'request' => 'request',
        'url' => 'url',
        'referer' => 'referer',
        'languages' => 'languages',
        'useragent' => 'useragent',
        'headers' => 'headers',
        'device' => 'device',
        'platform' => 'platform',
        'browser' => 'browser',
        'ip' => 'ip',
        'visitable_type' => 'visitable_type',
        'visitable_id' => 'visitable_id',
        'visitor_type' => 'visitor_type',
        'visitor_id' => 'visitor_id',
        'spouse1_birth_name' => 'spouse1_birth_name',
        'spouse2_birth_name' => 'spouse2_birth_name',
        'contact' => 'Kontaktdaten'


//===AUTO_INSERTED_ATTRIBUTES===//
    ],
];

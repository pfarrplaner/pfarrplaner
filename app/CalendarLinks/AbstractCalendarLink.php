<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 13:29
 */

namespace App\CalendarLinks;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AbstractCalendarLink
{
    protected $title = '';
    protected $description = '';

    protected $data = [];
    protected $viewName = 'ical';

    /** @var User $user  */
    protected $user = null;


    public function __construct()
    {
        $this->data['key'] = $this->getKey();
        if (null !== Auth::user()) {
            $this->user = Auth::user();
            $this->data['token'] = Auth::user()->getToken();
            $this->data['user'] = Auth::user()->id;
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    public function getKey(): string {
        return lcfirst(strtr(get_class($this), ['CalendarLink' => '', 'App\\s\\' => '', 'App\\CalendarLinks\\' => '']));
    }

    public function setupRoute() {
        return route('ical.setup', ['key' => $this->getKey()]);
    }

    public function setupData() {
        return [];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }


    public function setDataFromRequest(Request $request) {

    }

    public function getSetupViewName() {
        return 'ical.'.$this->getKey().'.setup';
    }

    public function setupView() {
        $data = $this->setupData();
        $data['calendarLink'] = $this;
        return view($this->getSetupViewName(), $data);
    }

    public function getLink() {
        return route('ical.export', $this->data);
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->data['user'] = $this->user;
        $this->data['token'] = $this->user->getToken();
    }



    public function getRenderData(Request $request, User $user) {
        return [];
    }

    public function export(Request $request, User $user) {
        $this->setDataFromRequest($request);
        $this->setUser($user);
        $data = $this->getRenderData($request, $user);
        $calendarLink = $this;
        $raw = View::make('ical.export.'.$this->viewName, compact('calendarLink', 'data'));
        $s = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', str_replace(' ,', ',', $raw)))));
        return preg_replace('/^(\s*)/m', '', $s);
    }


}
<?php


namespace App\Calendars\SharePoint;


use Thybag\SharePointAPI;

class SharePointCalendar extends \App\Calendars\AbstractCalendar
{

    /** @var SharePointAPI Internal Sharepoint API */
    protected $api = null;

    /** @var string Url */
    protected $url = '';

    /** @var string User name */
    protected $user = '';

    /** @var string Password */
    protected $password = '';

    /** @var string Cached WSDL file */
    protected $wsdlFile = '';

    /** @var string List name */
    protected $listName = '';

    public function __construct($url, $user = '', $password = '')
    {
        $this->setUrl($url);
        $this->setUser($user);
        $this->setPassword($password);
        $this->loadWsdl();
        $this->setApi(new SharePointAPI($user, $password, $this->wsdlFile, 'NTLM'));
    }

    public function __destruct()
    {
        if (file_exists($this->wsdlFile)) unlink ($this->wsdlFile);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return SharePointAPI
     */
    public function getApi(): SharePointAPI
    {
        return $this->api;
    }

    /**
     * @param SharePointAPI $api
     */
    public function setApi(SharePointAPI $api): void
    {
        $this->api = $api;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    protected function getWsdlUrl()
    {
        return str_replace('/default.aspx',  '/_vti_bin/Lists.asmx?WSDL', $this->getUrl());
    }

    public function getCalendars() {
        $calendars = [];
        foreach($this->api->getLists() as $list) {
            if ($list['servertemplate'] == 106) $calendars[] = $list['title'];
        }
        return $calendars;
    }

    protected function loadWsdl() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getWsdlUrl());
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getUser().':'.$this->getPassword());
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CAINFO, "./cacert.pem");
        $return = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);


        $tmpFile = tempnam(sys_get_temp_dir(), 'wsdl');
        file_put_contents($tmpFile, $return);
        $this->wsdlFile = $tmpFile;
        return $tmpFile;
    }

    /**
     * @return string
     */
    public function getWsdlFile(): string
    {
        return $this->wsdlFile;
    }

    /**
     * @param string $wsdlFile
     */
    public function setWsdlFile(string $wsdlFile): void
    {
        $this->wsdlFile = $wsdlFile;
    }


    public function query() {
        return $this->api->query($this->getListName());
    }

    public function all() {
        $results = $this->query()->get();
        if (isset($results[0])) {
            $events = [];
            foreach ($results as $result) {
                $events[] = SharePointCalendarItem::fromSharepointListItem($result, $this);
            }
            return $events;
        } else return null;
    }

    public function find($id) {
        $result = $this->query()->where('ID', '=', $id)->get();
        return isset($result[0]) ? SharePointCalendarItem::fromSharepointListItem($result[0], $this) : null;
    }

    public function create($data) {
        $event = new SharePointCalendarItem($data);
        $result = $this->api->write($this->getListName(), $event->toArray());
        return isset($result[0]) ? SharePointCalendarItem::fromSharepointListItem($result[0], $this) : null;
    }

    public function update(SharePointCalendarItem $event) {
        $result = $this->api->update($this->getListName(), $event->getID(), $event->toArray());
        return isset($result[0]) ? SharePointCalendarItem::fromSharepointListItem($result[0], $this) : null;
    }

    public function delete(SharePointCalendarItem $event) {
        $this->api->delete($this->getListName(), $event->getID());
    }

    public function getMetaData() {
        return $this->api->readListMeta($this->getListName());
    }

    public function getColumns() {
        $columns = [];
        foreach ($this->api->readListMeta($this->getListName(), true, false) as $data) {
            $columns[] = $data['name'];
        }
        return $columns;
    }

    /**
     * @return string
     */
    public function getListName(): string
    {
        return $this->listName;
    }

    /**
     * @param string $listName
     */
    public function setListName(string $listName): void
    {
        $this->listName = $listName;
    }



}

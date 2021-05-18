<?php

namespace App\Service;

class GoogleCalendarService
{
    /**
     * @var string
     */
    protected $applicationName = 'resavo';

    /**
     * @var string
     */
    protected $credentialsPath;

    /**
     * @var string
     */
    protected $clientSecretPath;

    /**
     * @var string
     */
    protected $scopes;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var bool
     */
    protected $fromFile = true;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $approvalPrompt;

    /**
     * construct
     */
    public function __construct($credentialsPath, $client_secretPath)
    {
        $this->credentialsPath = $credentialsPath;
        $this->type = 'offline';
        $this->approvalPrompt = 'force';
        $this->scopes = implode(' ', [\Google_Service_Calendar::CALENDAR]);
        $this->clientSecretPath = $client_secretPath;
    }

    /**
     * @param $scope
     */
    public function addScope($scope)
    {
        $this->scopes .= ' ' . $scope;
    }

    /**
     * @param $scope
     */
    public function removeScope($scope)
    {
        $scopes = explode(' ', $this->scopes);
        if (($key = array_search($scope, $scopes)) !== false) {
            unset($scopes[$key]);
        }
        $this->scopes = implode(' ', $scopes);
    }

    /**
     * Add contact scope
     */
    public function addScopeContact()
    {
        $this->addScope(\Google_Service_Script::WWW_GOOGLE_COM_M8_FEEDS);
    }

    /**
     * Remove contact scope
     */
    public function removeScopeContact()
    {
        $this->removeScope(\Google_Service_Script::WWW_GOOGLE_COM_M8_FEEDS);
    }

    /**
     * Add calendar scope
     */
    public function addScopeCalendar()
    {
        $this->addScope(\Google_Service_Calendar::CALENDAR);
    }

    /**
     * Remove calendar scope
     */
    public function removeScopeCalendar()
    {
        $this->removeScope(\Google_Service_Calendar::CALENDAR);
    }

    /**
     * Add offline scope
     */
    public function addScopeOffline()
    {
        $this->type = 'offline';
        $this->approvalPrompt = 'force';
    }

    /**
     * Remove offline scope
     */
    public function removeScopeOffline()
    {
        $this->type = 'online';
        $this->approvalPrompt = 'auto';
    }

    /**
     * Add userinfo scope
     */
    public function addScopeUserInfos()
    {
        $this->addScope(\Google_Service_Oauth2::USERINFO_PROFILE);
        $this->addScope(\Google_Service_Oauth2::USERINFO_EMAIL);
    }

    /**
     * Remove userinfo scope
     */
    public function removeScopeUserInfos()
    {
        $this->removeScope(\Google_Service_Oauth2::USERINFO_PROFILE);
        $this->removeScope(\Google_Service_Oauth2::USERINFO_EMAIL);
    }

    /**
     * @param $applicationName
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
    }

    /**
     * @param $credentialsPath
     */
    public function setCredentialsPath($credentialsPath)
    {
        $this->credentialsPath = $credentialsPath;
    }

    /**
     * @param $clientSecretPath
     */
    public function setClientSecretPath($clientSecretPath)
    {
        $this->clientSecretPath = $clientSecretPath;
    }

    /**
     * @param $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        if ($accessToken != "") {
            $this->accessToken = $accessToken;
        }
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        if ($refreshToken != "") {
            $this->refreshToken = $refreshToken;
        }
    }

    /**
     * clear tokens
     */
    public function clearTokens()
    {
        $this->accessToken = "";
        $this->refreshToken = "";
    }

    /**
     * @param $inputStr
     *
     * @return string
     */
    public static function base64UrlEncode($inputStr)
    {
        return strtr(base64_encode($inputStr), '+/=', '-_,');
    }

    /**
     * @param $inputStr
     *
     * @return string
     */
    public static function base64UrlDecode($inputStr)
    {
        return base64_decode(strtr($inputStr, '-_,', '+/='));
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param null      $authCode
     * @param bool|true $fromFile
     *
     * @return \Google_Client|string
     */
    public function getClient($authCode = null, $fromFile = true)
    {
        $this->fromFile = $fromFile;

        $client = new \Google_Client();
        $client->setApplicationName($this->applicationName);
        $client->setScopes($this->scopes);
        $client->setAuthConfig($this->clientSecretPath);
        $client->setAccessType($this->type);
        $client->setApprovalPrompt($this->approvalPrompt);
        $client->setState($this->base64UrlEncode(json_encode($this->parameters)));

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->credentialsPath;
        if ($fromFile) {
            if (file_exists($credentialsPath)) {
                $accessToken = json_decode(file_get_contents($credentialsPath), true);
            } else {
                // Request authorization from the user.
                if ($this->redirectUri) {
                    $client->setRedirectUri($this->redirectUri);
                }

                if ($authCode != null) {
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

                    if (!file_exists(dirname($credentialsPath))) {
                        mkdir(dirname($credentialsPath), 0700, true);
                    }
                    file_put_contents($credentialsPath, json_encode($accessToken));
                } else {
                    return $client->createAuthUrl();
                }
            }
        } else {
            if ($this->accessToken != null) {
                $accessToken = json_decode($this->accessToken, true);
            } else {
                // Request authorization from the user.
                if ($this->redirectUri) {
                    $client->setRedirectUri($this->redirectUri);
                }

                if ($authCode != null) {
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $this->accessToken = json_encode($accessToken);
                } else {
                    return $client->createAuthUrl();
                }
            }
        }
        $client->setAccessToken($accessToken);

        if ($client->getRefreshToken()) {
            $this->refreshToken = $client->getRefreshToken();
        }

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            if ($this->refreshToken) {
                $refreshToken = $this->refreshToken;
            } else {
                $refreshToken = $client->getRefreshToken();
            }

            if ($refreshToken) {
                $res = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                if (!isset($res['access_token'])) {
                    return $client->createAuthUrl();
                }
                if ($fromFile) {
                    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
                } else {
                    $this->accessToken = json_encode($client->getAccessToken());
                }
            } else {
                if ($fromFile) {
                    unlink($credentialsPath);
                } else {
                    $this->accessToken = null;
                }

                return $client->createAuthUrl();
            }
        }

        return $client;
    }

    /**
     * Add an Event to the specified calendar
     *
     * @param string       $calendarId
     * @param \DateTime    $eventStart
     * @param \DateTime    $eventEnd
     * @param string       $eventSummary
     * @param string       $eventDescription
     * @param string|array $eventAttendee
     * @param string       $location
     * @param array        $optionalParams
     * @param boolean      $allDay
     *
     * @return \Google_Service_Calendar_Event
     */
    public function addEvent(
        $calendarId,
        \DateTime $eventStart,
        \DateTime $eventEnd,
        $eventSummary,
        $eventDescription,
        $eventAttendee = "",
        $location = "",
        $optionalParams = [],
        $allDay = false
    ) {
        // Your new GoogleEvent object
        $event = new \Google_Service_Calendar_Event();
        // Set the title
        $event->setSummary($eventSummary);

        $start = new \Google_Service_Calendar_EventDateTime();
        $end = new \Google_Service_Calendar_EventDateTime();
        if ($allDay) {
            $formattedStart = $eventStart->format('Y-m-d');
            $formattedEnd = $eventEnd->format('Y-m-d');
            $start->setDate($formattedStart);
            $end->setDate($formattedEnd);
        } else {
            $formattedStart = $eventStart->format(\DateTime::RFC3339);
            $formattedEnd = $eventEnd->format(\DateTime::RFC3339);
            $start->setDateTime($formattedStart);
            $end->setDateTime($formattedEnd);
        }
        $event->setStart($start);
        $event->setEnd($end);
        // Default status for newly created event
        $event->setStatus('tentative');
        // Set event's description
        $event->setDescription($eventDescription);
        // Attendees - permit to manage the event's status
        if (!is_array($eventAttendee)) {
            $eventAttendee = explode(';', $eventAttendee);
        }
        $attendees = [];
        if (count($eventAttendee)) {
            foreach ($eventAttendee as $ea) {
                if ($ea != "") {
                    $attendee = new \Google_Service_Calendar_EventAttendee();
                    $attendee->setEmail($ea);
                    $attendees[] = $attendee;
                }
            }

            $event->attendees = $attendees;
        }
        if ($location != "") {
            $event->setLocation($location);
        }

        // Event insert
        return $this->getCalendarService()->events->insert($calendarId, $event, $optionalParams);
    }

    /**
     * Retrieve modified events from a Google push notification
     *
     * @param string $calendarId
     * @param string $syncToken Synchronised Token to retrieve last changes
     *
     * @return object
     */
    public function getEvents($calendarId, $syncToken)
    {
        // Option array
        $optParams = [];

        return $this->getCalendarService()->events->listEvents($calendarId, $optParams);
    }

    /**
     * Init a full list of events
     *
     * @param string $calendarId
     *
     * @return object
     */
    public function initEventsList($calendarId)
    {
        $eventsList = $this->getCalendarService()->events->listEvents($calendarId);

        return $eventsList->getItems();
    }

    /**
     * Delete an event
     *
     * @param string $calendarId
     * @param string $eventId
     */
    public function deleteEvent($calendarId, $eventId)
    {
        $this->getCalendarService()->events->delete($calendarId, $eventId);
    }

    /**
     * @param           $calendarId
     * @param           $eventId
     * @param \DateTime $eventStart
     * @param \DateTime $eventEnd
     * @param           $eventSummary
     * @param           $eventDescription
     * @param string    $eventAttendee
     * @param string    $location
     * @param array     $optionalParams
     * @param bool      $allDay
     *
     * @return \Google_Service_Calendar_Event
     */
    public function updateEvent(
        $calendarId,
        $eventId,
        \DateTime $eventStart,
        \DateTime $eventEnd,
        $eventSummary,
        $eventDescription,
        $eventAttendee = "",
        $location = "",
        $optionalParams = [],
        $allDay = false
    ) {
        // Your GoogleEvent object
        $event = $this->getEvent($calendarId, $eventId);

        // Set the title
        $event->setSummary($eventSummary);

        $start = new \Google_Service_Calendar_EventDateTime();
        $end = new \Google_Service_Calendar_EventDateTime();
        if ($allDay) {
            $formattedStart = $eventStart->format('Y-m-d');
            $formattedEnd = $eventEnd->format('Y-m-d');
            $start->setDate($formattedStart);
            $end->setDate($formattedEnd);
        } else {
            $formattedStart = $eventStart->format(\DateTime::RFC3339);
            $formattedEnd = $eventEnd->format(\DateTime::RFC3339);
            $start->setDateTime($formattedStart);
            $end->setDateTime($formattedEnd);
        }
        $event->setStart($start);
        $event->setEnd($end);
        // Default status for newly created event
        $event->setStatus('tentative');
        // Set event's description
        $event->setDescription($eventDescription);
        // Attendees - permit to manage the event's status
        if (!is_array($eventAttendee)) {
            $eventAttendee = explode(';', $eventAttendee);
        }
        if (count($eventAttendee)) {
            $attendees = [];

            foreach ($eventAttendee as $ea) {
                if ($ea != "") {
                    $attendee = new \Google_Service_Calendar_EventAttendee();
                    $attendee->setEmail($ea);
                    $attendees[] = $attendee;
                }
            }

            $event->attendees = $attendees;
        }
        if ($location != "") {
            $event->setLocation($location);
        }

        // Event insert
        return $this->getCalendarService()->events->update($calendarId, $event->getId(), $event);
    }

    /**
     * Get an event
     *
     * @param       $calendarId
     * @param       $eventId
     * @param array $optParams
     *
     * @return \Google_Service_Calendar_Event
     */
    public function getEvent($calendarId, $eventId, $optParams = [])
    {
        return $this->getCalendarService()->events->get($calendarId, $eventId, $optParams);
    }

    /**
     * @param \DateTime|null $updatedAt
     * @param int            $max
     *
     * @return array
     */
    public function listContacts(\DateTime $updatedAt = null, $max = 2000)
    {
        $accessToken = json_decode($this->getAccessToken(), 1)['access_token'];
        if (!is_null($updatedAt)) {
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max . '&updated-min=' . $updatedAt->format('Y-m-d\TH:i:s') . '&alt=json&v=3.0&oauth_token=' . $accessToken;
        } else {
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max . '&alt=json&v=3.0&oauth_token=' . $accessToken;
        }
        $xmlresponse = $this->curl($url);
        $contacts = json_decode($xmlresponse, true);

        $return = [];
        if (!empty($contacts['feed']['entry'])) {
            foreach ($contacts['feed']['entry'] as $contact) {
                if (isset($contact['gd$email'])) {
                    //retrieve Name and email address
                    $return[] = [
                        'name'      => $contact['title']['$t'],
                        'firstname' => isset($contact['gd$name']) && isset($contact['gd$name']['gd$givenName']) && isset($contact['gd$name']['gd$givenName']['$t']) ? $contact['gd$name']['gd$givenName']['$t'] : '',
                        'lastname'  => isset($contact['gd$name']) && isset($contact['gd$name']['gd$familyName']) && isset($contact['gd$name']['gd$familyName']['$t']) ? $contact['gd$name']['gd$familyName']['$t'] : '',
                        'email'     => $contact['gd$email'][0]['address'],
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * List shared and available calendars
     *
     * @return object
     */
    public function listCalendars()
    {
        return $this->getCalendarService()->calendarList->listCalendarList();
    }

    /**
     * Retrieve Google events on a date range
     *
     * @param string    $calendarId
     * @param \DateTime $start Range start
     * @param \DateTime $end   Range end
     *
     * @return object
     */
    public function getEventsOnRange($calendarId, \Datetime $start, \Datetime $end)
    {
        $service = $this->getCalendarService();

        $timeMin = $start->format(\DateTime::RFC3339);
        $timeMax = $end->format(\DateTime::RFC3339);

        // Params to send to Google
        $eventOptions = [
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax
        ];
        $eventList = $service->events->listEvents($calendarId, $eventOptions);

        return $eventList;
    }

    /**
     * @return \Google_Service_Oauth2_Userinfoplus
     */
    public function getUserInfos()
    {
        $oauth2 = $this->getOauth2Service();
        $userInfo = $oauth2->userinfo->get();

        return $userInfo;
    }

    /**
     * Retrieve Google events for a date
     *
     * @param           $calendarId
     * @param \Datetime $date
     *
     * @return \Google_Service_Calendar_Events
     */
    public function getEventsForDate($calendarId, \Datetime $date)
    {
        $service = $this->getCalendarService();

        $start = clone $date;
        $start->setTime(0, 0, 0);
        $end = clone $date;
        $end->setTime(23, 59, 29);
        $timeMin = $start->format(\DateTime::RFC3339);
        $timeMax = $end->format(\DateTime::RFC3339);

        // Params to send to Google
        $eventOptions = [
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax
        ];
        $eventList = $service->events->listEvents($calendarId, $eventOptions);

        return $eventList;
    }

    /**
     * Retrieve Google events filtered by parameters
     *
     * @param string $calendarId
     * @param array  $eventOptions
     *
     * @return object
     */
    public function getEventsByParams($calendarId, $eventOptions)
    {
        $service = $this->getCalendarService();
        foreach (['timeMin', 'timeMax', 'updatedMin'] as $opt) {
            if (isset($eventOptions[$opt])) $eventOptions[$opt] = $eventOptions[$opt]->format(\DateTime::RFC3339);
        }
        $eventList = $service->events->listEvents($calendarId, $eventOptions);

        return $eventList;
    }

    /**
     * @return \Google_Service_Calendar|null
     */
    public function getCalendarService()
    {
        $client = $this->getClient(null, $this->fromFile);
        if (!is_string($client)) {
            return new \Google_Service_Calendar($client);
        }

        return null;
    }

    /**
     * @return \Google_Service_Oauth2|null
     */
    public function getOauth2Service()
    {
        $client = $this->getClient(null, $this->fromFile);
        if (!is_string($client)) {
            return new \Google_Service_Oauth2($client);
        }

        return null;
    }

    /**
     * @param        $url
     * @param string $post
     *
     * @return mixed
     */
    public function curl($url, $post = "")
    {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
        curl_setopt($curl, CURLOPT_URL, $url);
        //The URL to fetch. This can also be set when initializing a session with curl_init().
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        //The number of seconds to wait while trying to connect.
        if ($post != "") {
            curl_setopt($curl, CURLOPT_POST, 5);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        //The contents of the "User-Agent: " header to be used in a HTTP request.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        //To follow any "Location: " header that the server sends as part of the HTTP header.
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        //To automatically set the Referer: field in requests where it follows a Location: redirect.
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //To stop cURL from verifying the peer's certificate.
        $contents = curl_exec($curl);
        curl_close($curl);

        return $contents;
    }
}

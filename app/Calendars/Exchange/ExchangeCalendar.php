<?php


namespace App\Calendars\Exchange;


use App\Calendars\Exchange\Exceptions\RequestFailedException;
use Carbon\Carbon;
use Closure;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Enumeration\CalendarItemCreateOrDeleteOperationType;
use jamesiarmes\PhpEws\Enumeration\ConflictResolutionType;
use jamesiarmes\PhpEws\Enumeration\ContainmentComparisonType;
use jamesiarmes\PhpEws\Enumeration\ContainmentModeType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\DisposalType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\FolderQueryTraversalType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Request\DeleteItemType;
use jamesiarmes\PhpEws\Request\FindFolderType;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Request\UpdateItemType;
use jamesiarmes\PhpEws\Response;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\CalendarViewType;
use jamesiarmes\PhpEws\Type\ConstantValueType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\FolderIdType;
use jamesiarmes\PhpEws\Type\FolderResponseShapeType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\RestrictionType;

class ExchangeCalendar extends \App\Calendars\AbstractCalendar
{

    protected $server = '';
    protected $username = '';
    protected $password = '';
    protected $folderName = '';
    protected $serverVersion = '';


    protected $client = null;

    /** @var \jamesiarmes\PhpEws\Type\CalendarFolderType */
    protected $folder = null;

    public function __construct($server, $username, $password, $serverVersion, $folder)
    {
        $this->setServer($server);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setFolderName($folder);
        $this->setServerVersion($serverVersion);

        $this->client = new Client($server, $username, $password, 'Exchange' . $serverVersion);
        if ($folder) {
            $this->folder = $this->findFolder($folder);
        }
    }

    public function getAllCalendars() {
        $request = new FindFolderType();
        $request->FolderShape = new FolderResponseShapeType();
        $request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

        // Search recursively.
        $request->Traversal = FolderQueryTraversalType::DEEP;

        // Search within the root folder. Combined with the traversal set above, this
        // should search through all folders in the user's mailbox.
        $parent = new DistinguishedFolderIdType();
        $parent->Id = DistinguishedFolderIdNameType::ROOT;
        $request->ParentFolderIds->DistinguishedFolderId[] = $parent;

        $response = $this->client->FindFolder($request);
        $response_messages = $response->ResponseMessages->FindFolderResponseMessage;
        $calendars = [];
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass == ResponseClassType::SUCCESS) {
                $id = 0;
                foreach($response_message->RootFolder->Folders->CalendarFolder as $calendar) {
                    $calendars[] = ['id' => $calendar->DisplayName, 'name' => $calendar->DisplayName];
                }
            }
        }
        return $calendars;
    }

    public function getAllEventsForRange(Carbon $start, Carbon $end)
    {
        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

        $request->ParentFolderIds->FolderId = new FolderIdType();
        $request->ParentFolderIds->FolderId->Id = $this->folder->FolderId->Id;

        $request->CalendarView = new CalendarViewType();
        $request->CalendarView->StartDate = $start->format('c');
        $request->CalendarView->EndDate = $end->format('c');

        $response = $this->client->FindItem($request);
        $response_messages = $response->ResponseMessages->FindItemResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(STDERR, "Failed to find folders with \"$code: $message\"\n");
                return null;
            }
            return $response_message->RootFolder->Items->CalendarItem;
        }
    }

    public function findFolder()
    {
        $request = new FindFolderType();
        $request->FolderShape = new FolderResponseShapeType();
        $request->FolderShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        $request->Restriction = new RestrictionType();

        // Search recursively.
        $request->Traversal = FolderQueryTraversalType::DEEP;

        // Search within the root folder. Combined with the traversal set above, this
        // should search through all folders in the user's mailbox.
        $parent = new DistinguishedFolderIdType();
        $parent->Id = DistinguishedFolderIdNameType::ROOT;
        $request->ParentFolderIds->DistinguishedFolderId[] = $parent;

        // Build the restriction that will search for folders containing "Cal".
        $contains = new \jamesiarmes\PhpEws\Type\ContainsExpressionType();
        $contains->FieldURI = new PathToUnindexedFieldType();
        $contains->FieldURI->FieldURI = UnindexedFieldURIType::FOLDER_DISPLAY_NAME;
        $contains->Constant = new ConstantValueType();
        $contains->Constant->Value = $this->getFolderName();
        $contains->ContainmentComparison = ContainmentComparisonType::EXACT;
        $contains->ContainmentMode = ContainmentModeType::SUBSTRING;
        $request->Restriction->Contains = $contains;

        $response = $this->client->FindFolder($request);
        $response_messages = $response->ResponseMessages->FindFolderResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(STDERR, "Failed to find folders with \"$code: $message\"\n");
                return null;
            }
            return $response_message->RootFolder->Folders->CalendarFolder[0];
        }
    }

    public function find($id)
    {
        $request = new GetItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $item = new ItemIdType();
        $item->Id = $id;
        $request->ItemIds->ItemId[] = $item;

        $response = $this->client->GetItem($request);
        return $this->handleResponse(
            $response->ResponseMessages->GetItemResponseMessage,
            null,
            function (CalendarItemType $item) {
                return ExchangeCalendarItem::fromExchangeItem($item, $this);
            },
            false
        );
    }

    public function create(array $data = [])
    {
        $event = new ExchangeCalendarItem($data, $this);
        $item = $event->toExchangeItem();

        $request = new CreateItemType();
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;
        $request->Items = new NonEmptyArrayOfAllItemsType();
        $request->Items->CalendarItem[] = $item;
        $request->SavedItemFolderId = $this->folder;

        $response = $this->client->CreateItem($request);

        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                return null;
            }

            // Iterate over the created events, printing the id for each.
            foreach ($response_message->Items->CalendarItem as $item) {
                return $this->find($item->ItemId->Id);
            }
        }
    }

    public function update($id, $data)
    {
        /** @var ExchangeCalendarItem $item */
        $item = $this->find($id);

        $request = new UpdateItemType();
        $request->ConflictResolution = ConflictResolutionType::ALWAYS_OVERWRITE;
        $request->SendMeetingInvitationsOrCancellations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;

        $change = $item->getItemChangeType($data);
        $request->ItemChanges[] = $change;

        $response = $this->client->UpdateItem($request);
        return $this->handleResponse(
            $response->ResponseMessages->UpdateItemResponseMessage,
            false,
            function (CalendarItemType $item) {
                return $this->find($item->ItemId->Id);
            }
        );
    }

    /**
     * @param Response $response The response object
     * @param mixed $returnOnFail The value to be returned if the request failed (often null or false)
     * @param Closure $processSuccess A function to be called with the first returned item
     * @return mixed
     * @throws RequestFailedException
     */
    protected function handleResponse(
        array $responseMessages,
        $returnOnFail,
        Closure $processSuccess,
        $throwException = true
    ) {
        foreach ($responseMessages as $responseMessage) {
            // Make sure the request succeeded.
            if ($responseMessage->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $responseMessage->ResponseCode;
                $message = $responseMessage->MessageText;
                if ($throwException) {
                    throw new RequestFailedException("Request failed with \"$code: $message\"");
                }
                return $returnOnFail;
            }

            // Iterate over the returned events, and call the success function
            foreach ($responseMessage->Items->CalendarItem as $item) {
                return $processSuccess($item);
            }
        }
    }

    public function delete($id)
    {
        $request = new DeleteItemType();
        $request->DeleteType = DisposalType::HARD_DELETE;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->SendMeetingCancellations = CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;
        $item = new ItemIdType();
        $item->Id = $id;
        $request->ItemIds->ItemId[] = $item;
        $response = $this->client->DeleteItem($request);

        $response_messages = $response->ResponseMessages->GetItemResponseMessage;

        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $message = $response_message->ResponseCode;
                echo "Failed to delete event with \"$message\"\n";
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer(string $server): void
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
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

    /**
     * @return string
     */
    public function getFolderName(): string
    {
        return $this->folderName;
    }

    /**
     * @param string $folderName
     */
    public function setFolderName(string $folderName): void
    {
        $this->folderName = $folderName;
    }

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param null $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getServerVersion(): string
    {
        return $this->serverVersion;
    }

    /**
     * @param string $serverVersion
     */
    public function setServerVersion(string $serverVersion): void
    {
        $this->serverVersion = $serverVersion;
    }


}

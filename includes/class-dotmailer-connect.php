<?php

class N2CF7_DotMailerConnect
{
    public $request_url = 'http://apiconnector.com/api.asmx?WSDL';
    public $username;
    public $password;
    public $client;
    public $params;
    public $auth;
    public $header;

    function __construct($username, $password)
    {
        try {
            $this->username = $username;
            $this->password = $password;
            $this->params = array('username' => $this->username, 'password' => $this->password);
            $this->client = new SoapClient($this->request_url, array("trace" => 1, "exceptions" => 1));
            $this->getClient();
        } catch (Exception $e) {
            echo $e->getFile();
        }
    }

    function getClient()
    {
        return $this->client;
    }

    function listAddressBooks()
    {
        try {
            $result = $this->client->ListAddressBooks($this->params);
            return $result->ListAddressBooksResult->APIAddressBook;
        } Catch (SoapFault $ex) {

        }
    }

    function listDataFields()
    {
        try {
            $result = $this->client->ListContactDataLabels($this->params);
            return $result->ListContactDataLabelsResult->ContactDataLabel;
        } Catch (Exception $ex) {
            return false;
        }
    }

    function AddDataField($name, $type)
    {
        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'fieldname' => $name,
            'datatype' => $type
        );

        try {
            $result = $this->client->CreateDataField($params);
            return $result;
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    function AddContactToAddressBook($email, $addressBookId, $datafields = "")
    {
        $AudienceType = "B2C";
        $OptInType = "Double";
        $EmailType = "Html";

        $contact = array(
            "Email" => $email,
            "AudienceType" => $AudienceType,
            "OptInType" => $OptInType,
            "EmailType" => $EmailType,
            "ID" => -1,
            "DataFields" => $datafields
        );

        $params = array(
            'username' => $this->username,
            'password' => $this->password,
            'contact' => $contact,
            'addressbookId' => $addressBookId
        );

        try {
            $result = $this->client->AddContactToAddressBook($params);
            return $result;
        } catch (SoapFault $e) {
            if ($e->faultcode == "soap:Server") {
                wp_mail(
                    get_option('admin_email'),
                    $e->faultcode,
                    $e->xdebug_message . 'Error email for '. $email . '. The customer is trying to resubscribe.' 
                );
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    function GetContactByEmail($email)
    {
        $params = array('username' => $this->username, 'password' => $this->password, 'email' => $email);

        try {
            $result = $this->client->GetContactByEmail($params);
            return $result;
        } Catch (Exception $ex) {
            return false;
        }
    }

    function getStatusByEmail($email)
    {
        $params = array('username' => $this->username, 'password' => $this->password, 'email' => $email);
        try {
            $result = $this->client->GetContactStatusByEmail($params);
            return $result;
        } Catch (Exception $ex) {
            return false;
        }
    }

    function reSubscribeContact($email, $addressBookId, $datafields = "")
    {
        $AudienceType = "B2C";
        $OptInType = "Double";
        $EmailType = "Html";

        $contact = array('Email' => $email, "AudienceType" => $AudienceType, "OptInType" => $OptInType,
            'EmailType' => $EmailType, "ID" => -1, "DataFields" => $datafields);

        $params = array('username' => $this->username,
            'password' => $this->password,
            'contact' => $contact,
            'addressBookId' => $addressBookId,
            'preferredLocale' => 'en-GB',
            'returnUrlToUseIfChallenged' => ''
        );

        try {
            $result = $this->client->ResubscribeContact($params);
            return $result;
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    function getAccountDetails()
    {
        try {
            $result = $this->client->GetCurrentAccountInfo($this->params);
            return $result->GetCurrentAccountInfoResult->Properties->APIAccountProperty;
        } Catch (SoapFault $ex) {

            return false;
        }
    }

    function ApiCampaignSend($userName, $password, $campaignID, $contactID)
    {
        try {
            $newTime = date("c", strtotime(date("Y-m-d H:i:s") . " -60 minutes"));
            $params = array('username' => $userName, 'password' => $password, 'campaignId' => $campaignID, 'contactid' => $contactID, 'sendDate' => $newTime);
            $result = $this->client->SendCampaignToContact($params);
            return $result;
        } catch (SoapFault $e) {
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
}
?>
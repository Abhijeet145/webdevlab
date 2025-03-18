<?php
if (isset($_POST['submit'])) {
    $empId = $_POST['empId'];
    $fullName = $_POST['fullName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $complaintType = $_POST['complaintType'];
    $description = $_POST['description'];
    
    $token = "TKN" . rand(100000, 999999);

    $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Complaints></Complaints>");
    $xml->addChild('EmployeeId',  $empId);
    $xml->addChild('FullName', $fullName);
    $xml->addChild('PhoneNumber', $phone);
    $xml->addChild('Email', $email);
    $xml->addChild('ComplaintType', $complaintType);
    $xml->addChild('Description', $description);
    $xml->addChild('Token', $token);

    $xmlFile = 'complaints/' . $token . '.xml';
    // $xmlFile = 'complaints/comp.xml';
    $xml->asXML($xmlFile);
    // echo "Complaint submitted successfully with Token: $token";
    function libxml_display_error($error)
    {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .=    " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    function libxml_display_errors() {
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            print libxml_display_error($error);
        }
        libxml_clear_errors();
    }

    // Enable user error handling
    libxml_use_internal_errors(true);

    $doc = new DOMDocument(); 
    $doc->load($xmlFile); 

    if (!$doc->schemaValidate('complaint_schema.xsd')) {
        print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
        libxml_display_errors();
    }else{
        echo "Complaint submitted successfully with Token: $token";
    }

}

?> 


<?php

function getContacts() {
    $url = 'https://b24-51h20a.bitrix24.ru/rest/10/4gaqadob9uqh56my/crm.contact.list';
    $data = file_get_contents($url);
    return json_decode($data, true)['result'];
}

function updateContact($contact) {
    $url = 'https://b24-51h20a.bitrix24.ru/rest/10/4gaqadob9uqh56my/crm.contact.update';
    $params = http_build_query(array(
        'id' => $contact['ID'],
        'fields' => array(
            'NAME' => $contact['NAME'],
            'SECOND_NAME' => $contact['SECOND_NAME'],
            'LAST_NAME' => $contact['LAST_NAME']
        )
    ));
    
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $params
        )
    );
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

function normalizeContacts($contacts) {
    foreach ($contacts as &$contact) {
        if (empty($contact['SECOND_NAME'])) {
            $names = explode(' ', $contact['NAME']);
            if (count($names) == 2) {
                $contact['NAME'] = $names[0];
                $contact['SECOND_NAME'] = $names[1];
            }
        }
    }
    return $contacts;
}

$contacts = getContacts();
$normalizedContacts = normalizeContacts($contacts);

foreach ($normalizedContacts as $contact) {
    updateContact($contact);
}

echo 'Данные были нормализованы';

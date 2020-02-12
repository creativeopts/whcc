<?php

function GetMaxNewsItem()
{
    $tURL="https://hacker-news.firebaseio.com/v0/maxitem.json?print=pretty";
    $tParms="";
    $tReturn=0;
            
    $mId=GetInfo ($tParms,$tURL, $tReturn);
    $mId=json_decode(trim($mId), TRUE);
    return $mId;
            
}
function GetNewsItem($inItem)
{
    $tURL="https://hacker-news.firebaseio.com/v0/item/".$inItem.".json?print=pretty";
    $tParms="";
    $tReturn=1;
            
    $mReturn=GetInfo ($tParms,$tURL, $tReturn);
    
    return $mReturn;
            
}


    
function GetInfo ($in_parms,$in_url, $return_array){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $in_url);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($curl, CURLOPT_POST, 0);
//    curl_setopt($curl, CURLOPT_POSTFIELDS, $in_parms);

    $cwnresult = curl_exec ($curl);
    curl_close ($curl);
    
    if ($return_array==1)
    {
        $cwnresult = json_decode(trim($cwnresult), TRUE);
    }

    return $cwnresult;
}    
?>

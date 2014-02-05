<?php
// load the API Client library
//include "../Eventbrite.php"; 
include "eventbrite.php"; 

// Initialize the API client
//  Eventbrite API / Application key (REQUIRED)
//   http://www.eventbrite.com/api/key/
//  Eventbrite user_key (OPTIONAL, only needed for reading/writing private user data)
//   http://www.eventbrite.com/userkeyapi
$authentication_tokens = array('app_key'  => 'HJRPHMRZZ7HPCASJJT',
                               'user_key' => '131723199220817796590');
$eb_client = new Eventbrite( $authentication_tokens );

try{
// For more information about the functions that are available through the Eventbrite API, see http://developer.eventbrite.com/doc/
    $attendees = $eb_client->event_list_attendees( array('id'=>'9789407381') );
} catch ( Exception $e ) {
    // Be sure to plan for potential error cases
    // so that your application can respond appropriately

    //var_dump($e);
    $attendees = array();
}

function attendee_to_html( $attendee ){
    if($attendee->first_name){ 
        return "<div class='eb_attendee_list_item'>"
        .$attendee->first_name.' '.$attendee->last_name.' '
        .$attendee->answers[2]->answer->answer_text.' ' 
        
        ."</div>\n";
    }else{
        return '';
    }
}

function sort_attendees_by_created_date( $x, $y ){
    if($x->attendee->created == $y->attendee->created ){
        return 0;
    }
    return ( $x->attendee->created > $y->attendee->created ) ? -1 : 1;
}

function valid_badge( $attendee )
{
    /*
    Early bird Innovation Badges        (Innovation Early Bird Badge)       22361343
    Advance Innovation Badges
    Regular Innovation Badges           (Innovation Regular Badge)          22361345
    Early bird Premium Badges           (Premium Early Bird Badge)          22361347
    Advance Premium Badges
    Regular Premium Badges              (Premium Regular Badge)             22361753
    */

    if( ($attendee->ticket_id) 
        && ($attendee->ticket_id == "22361343"
            || $attendee->ticket_id == "22361345"
            || $attendee->ticket_id == "22361347"            
            || $attendee->ticket_id == "22361753"))
    {
        return true;
    }else{
        return false;
    }
}

function attendee_list_to_html( $attendees ){
    $attendee_list_html = "<div class='eb_attendee_list'>\n";
    if( isset($attendees->attendees) ){ 
        //sort the attendee list?
        usort( $attendees->attendees, "sort_attendees_by_created_date");
        //render the attendee as HTML
        foreach( $attendees->attendees as $attendee ){
            if( valid_badge( $attendee->attendee ) ){
                $attendee_list_html .= attendee_to_html( $attendee->attendee );
            }
        }
    }else{
        $attendee_list_html .= '<div class="eb_attendee_list_item">You can be the first to register for this event!</div>';
    }   
    return $attendee_list_html . "</div>\n";
}

function paged(){
    return ' PAGE: '.$_GET["p"];
}

?>

<?= attendee_list_to_html( $attendees ); ?>

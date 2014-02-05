<?php
// load the API Client library
include "eventbrite.php"; 

//   http://www.eventbrite.com/api/key/
//  Eventbrite user_key (OPTIONAL, only needed for reading/writing private user data)
//   http://www.eventbrite.com/userkeyapi
$authentication_tokens = array('app_key'  => 'HJRPHMRZZ7HPCASJJT',
                               'user_key' => '131723199220817796590');
$eb_client = new Eventbrite( $authentication_tokens );

define("INNOVATION_EARLY_BIRD_BADGE", 22361343);
define("INNOVATION_REGULAR_BADGE", 22361345);
define("PREMIUM_EARLY_BIRD_BADGE", 22361347);
define("PREMIUM_REGULAR_BADGE", 22361753);
/*
    Advance Innovation Badges
    Advance Premium Badges
*/

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
    if( ($attendee->ticket_id) 
        && ($attendee->ticket_id == INNOVATION_EARLY_BIRD_BADGE
            || $attendee->ticket_id == INNOVATION_REGULAR_BADGE
            || $attendee->ticket_id == PREMIUM_EARLY_BIRD_BADGE         
            || $attendee->ticket_id == PREMIUM_REGULAR_BADGE))
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
    return ($_GET["p"] > 1 ? $_GET["p"] + 1 : 2);
}

//mark-up your attendee list
// render in html - ?>
<style type="text/css">
.eb_attendee_list_item{
  padding-bottom: 8px;
}
.eb_attendee_list{
  margin-left: 20px;
}
</style>

<h1>Event Attendee List:</h1>
<div id="content">

<?= attendee_list_to_html( $attendees ); ?>

</div>


<a id="next" href="index.php?p=<?= paged(); ?>">next page</a>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="jquery.infinitescroll.min.js"></script>
<script>
$('#content').infinitescroll({
    navSelector     : "#next:last",
    nextSelector    : "a#next:last",
    itemSelector    : ".eb_attendee_list",
    debug           : false,
    dataType        : 'html',
    maxPage         : 100,
    //prefill         : false,
//      path: ["http://nuvique/infinite-scroll/test/index", ".html"]
    path: function(index) {
        //window.console && console.log('path: ',this);
        return "index.php?p=" + index;
    }
    // behavior     : 'twitter',
    // appendCallback   : false, // USE FOR PREPENDING
    // pathParse        : function( pathStr, nextPage ){ return pathStr.replace('2', nextPage ); }
}, 
    function(newElements, data, url){
    //USE FOR PREPENDING
    // $(newElements).css('background-color','#ffef00');
    // $(this).prepend(newElements);
    //
    //END OF PREPENDING

      //window.console && console.log('context: ',this);
      //window.console && console.log('returned: ', newElements);

      $(this).append(newElements);
    
});

/*
$.ajax({
  url: "index2.php",
  cache: false
})
  .done(function( html ) {
    $( "#content" ).append( html );
  });
*/
</script>
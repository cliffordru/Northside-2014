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
define("NO_MORE_ATTENDEES", "eb_attendee_list_item_none");
define("API_COUNT",2);
/*
    Advance Innovation Badges
    Advance Premium Badges
*/

try{
// For more information about the functions that are available through the Eventbrite API, see http://developer.eventbrite.com/doc/
    $attendees = $eb_client->event_list_attendees( array('id'=>'9789407381' , 'count' => API_COUNT, 'page' => page()) );
} catch ( Exception $e ) {
    // Be sure to plan for potential error cases
    // so that your application can respond appropriately

    //var_dump($e);
    $attendees = array();
}

function is_twitter_handle_question( $attendee, $question_index ){
    return $attendee->answers[$question_index]->answer->question == "Twitter handle";
}

function question_answer( $attendee, $question_index ){
    return $attendee->answers[$question_index]->answer->answer_text;
}

function attendee_twitter_handle( $attendee )
{
    $twitter_handle = "";
    $length = count($attendee->answers);

    for ($i = 0; $i < $length; $i++) {
        if(is_twitter_handle_question($attendee, $i)){
            $twitter_handle = question_answer($attendee, $i);
            break;
        }
    }
    
    return $twitter_handle;
}

function attendee_to_html( $attendee ){    
    if($attendee->first_name){ 
        $twitter_handle = attendee_twitter_handle($attendee);
        
        return "<div class='img' align='center'>
                    <div class='desc'>
                        <span class='textdesc'><strong>"
                            .$attendee->first_name." ".$attendee->last_name."</strong></span><span class='subdesc'><br>
                        <a href='https://twitter.com/".twitter_user_strip_at($twitter_handle)
                            ."' target='_blank'>".twitter_user_prefix_at($twitter_handle)."</a><br> "
                        .$attendee->job_title."<br><em>".$attendee->company."</em><br>
                        </span>
                    </div>
                </div>";
    }
    return '';
}

/**
* strips @ from @user
*/
function twitter_user_strip_at($user)
{
    return ltrim($user,'@');    
}

/**
* prefix @ to user
*/
function twitter_user_prefix_at($user)
{
    if(IsNullOrEmptyString($user)){
        return $user;
    }
    return '@'.twitter_user_strip_at($user);
}

function IsNullOrEmptyString($question){
    return (!isset($question) || trim($question)==='');
}

function sort_attendees_by_created_date( $x, $y ){
    if($x->attendee->created == $y->attendee->created ){
        return 0;
    }
    return ( $x->attendee->created > $y->attendee->created ) ? -1 : 1;
}

function is_valid_badge( $attendee )
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
            if( is_valid_badge( $attendee->attendee ) ){
                $attendee_list_html .= attendee_to_html( $attendee->attendee );
            }
        }
    }else{
        $attendee_list_html .= '<div class="'.NO_MORE_ATTENDEES.'"></div>';
    }   
    return $attendee_list_html . "</div>\n";
}

function page(){
    return ($_GET["p"] > 1 ? $_GET["p"] + 1 : 2);
}

// render in html - ?>
<style type="text/css"><!--
                        
.textdesc{  font-size:18px !important; }  div.img {  margin:5px; padding:20px 10px 10px 10px; border-bottom:2px solid; border-bottom-color:#CCCCCC; height:150px; width:180px; float:left; }  div.img a:hover img { border:0px; } div.desc { text-align:center; font-weight:normal; width:180px; margin:0px; }  div.intro { font-size:18px !important; } div.intro img { border:0px !important; margin:0px !important; padding:0px 0px 30px 0px !important; } div.section{ border-bottom:1px solid; border-top:1px solid; }

                        /* Added for infinite scroll */
                        #colophon {
                            padding-top: 300px;
                        }
                        --></style>
                        <div class="intro">
                            <img src="http://northsidefestival.com/wp-content/uploads/2014/06/intro_04.jpg" alt="intro_04" width="850" height="420" class="aligncenter size-full wp-image-2242">
<br >                            
<strong>
    Our NYC technologists, hackers, thinkers and creators have become the most exciting innovators in the world. NORTHSIDE INNOVATION is the annual conference &amp; trade show that showcases the best of this community as it continues to grow and revolutionize society!
    <br />
To purchase CONFERENCE BADGES, <a href="http://www.eventbrite.com/e/northside-festival-tickets-9789407381">click here.</a> To purchase PREMIUM BADGES, <a href="http://www.eventbrite.com/e/northside-festival-tickets-9789407381">click here.</a>
</strong>
<p></p>
<div class="section">2014 CONFERENCE ATTENDEES</div>                        
</div>
<div id="content_list">
    <?= attendee_list_to_html( $attendees ); ?>
</div>

<a id="next" href="index.php?p=<?= page(); ?>"></a>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="jquery.infinitescroll.min.js"></script>
<script>
$('#content_list').infinitescroll({
    navSelector     : "#next:last",
    nextSelector    : "a#next:last",
    itemSelector    : ".img",
    debug           : false,
    dataType        : 'html',
    maxPage         : 100,
    donetext        : "",
    currPage         :0,
    //prefill         : false,
//      path: ["http://nuvique/infinite-scroll/test/index", ".html"]
    loading: {
        msgText     : "Loading more attendees...",
        finishedMsg : ""
    },
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
      if(newElements[0].innerHTML.indexOf("<?= NO_MORE_ATTENDEES ?>") != -1)
      {
        //console.log(data);
        data.maxPage = 1;
        data.state.isDone = true;
      }  
      else{    
        $(this).append(newElements);
      }
    
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
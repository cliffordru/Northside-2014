<?php
/**
 * Template Name: Attendees Template
 *
 */
?>
<?php get_header();?>


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
define("API_COUNT",4);
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

    var_dump($e);
    $attendees = array();
}

function attendee_to_html( $attendee ){
    if($attendee->first_name){ 
        return "<div class='img' align='center'>
                    <div class='desc'>
                        <span class='textdesc'><span class='names'><strong>"
                            .$attendee->first_name." ".$attendee->last_name."</strong></span>
                        <a href='https://twitter.com/".twitter_user_strip_at($attendee->answers[1]->answer->answer_text)
                            ."' target='_blank'>".twitter_user_prefix_at($attendee->answers[1]->answer->answer_text)."</a> "
                        .$attendee->job_title." <em>".$attendee->company."</em>
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
    //return ($_GET["p"] > 1 ? $_GET["p"] + 1 : 2);

	$uri = $_SERVER['REQUEST_URI'];
	$tmp = explode('/', $uri);
	$param = $tmp[2];

	//return count($tmp);
	//return $param > 1 ? $param + 1 : 1;
	return $param;

/*
    $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];//$_SERVER['PATH_INFO'];
    $keys = parse_url($url); // parse the url
    $path = explode("/", $keys['path']); // splitting the path
    return end($path) > 1 ? end($path) + 1 : 2; // get the value of the last element 
    */

/*
    if(preg_match("/\/(\d+)$/",$_SERVER['PATH_INFO'],$matches))
	{
		echo $matches[1];
	  return $matches[1];
	}
	return "";
	*/
}

//mark-up your attendee list
// render in html - ?>
<style type="text/css"><!--
.textdesc{ 
    text-align:center;
    font-weight:lighter;
} 
div.img { 
    margin:5px;
    padding:20px 10px 10px 10px;
    border-bottom:1px solid;
    border-bottom-color:#CCCCCC;
    height:170px;
    width:130px;
    float:left;
} 
div.img a:hover img {
    border:0px;
}

div.desc {
    text-align:center;
    font-weight:normal;
    width:150px;
    margin:0px;
} 
div.intro img {
    border:0px !important;
    margin:0px !important;
    padding:0px 0px 30px 0px !important;
}

#colophon {
	padding-top: 300px;
}
--></style>

<div class="intro">
    <img class="aligncenter size-full wp-image-2232" alt="intro_image" 
        src="http://northsidefestival.com/wp-content/uploads/2014/06/intro01.jpg" width="850" height="420" />
</div>
<strong>
    Northside Innovation is filled with great programming and great networking opportunities.
    To see who else has bought a badge see our conference attendees below:
</strong>
To purchase CONFERENCE BADGES, 
    <a href="http://www.eventbrite.com/e/northside-festival-tickets-9789407381">click here.</a>
To purchase PREMIUM BADGES, 
    <a href="http://www.eventbrite.com/e/northside-festival-tickets-9789407381">click here.</a>
<hr />
<center><strong>2014 CONFERENCE ATTENDEES</strong></center>
<hr />

<div id="content">
    <?= attendee_list_to_html( $attendees ); ?>
</div>

<a id="next" href="<?php the_permalink() ?><?= page(); ?>/"></a>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/jquery.infinitescroll.min.js"></script>
<script>
$('#content').infinitescroll({
    navSelector     : "#next:last",
    nextSelector    : "a#next:last",
    itemSelector    : ".img",
    debug           : false,
    dataType        : 'html',
    maxPage         : 1000,
    donetext        : "",
    currPage        : 0,
    bufferPx     	: 800,
    //prefill         : false,
//      path: ["http://nuvique/infinite-scroll/test/index", ".html"]
    loading: {
        msgText     : "Loading more attendees...",
        finishedMsg : ""
    },
    path: function(index) {
        //window.console && console.log('path: ',this);
        return "<?php the_permalink() ?>" + index + "/";        

        
        //return "<?php add_query_arg('p', page(), get_permalink()) ?>";
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


<?php get_footer();?>			
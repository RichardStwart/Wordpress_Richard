<?php

function the_permalink() {
	echo get_permalink();
}

function permalink_link() { // For backwards compatibility
	echo get_permalink();
}

function permalink_anchor($mode = 'id') {
    global $id, $post;
    switch(strtolower($mode)) {
        case 'title':
            $title = sanitize_title($post->post_title) . '-' . $id;
            echo '<a id="'.$title.'"></a>';
            break;
        case 'id':
        default:
            echo '<a id="post-'.$id.'"></a>';
            break;
    }
}

function permalink_single_rss($file = '') {
    echo get_permalink();
}

function get_permalink($id=false) {
    global $post, $wpdb;
    global $querystring_start, $querystring_equal;

    $rewritecode = array(
        '%year%',
        '%monthnum%',
        '%day%',
		'%hour%',
		'%minute%',
		'%second%',
        '%postname%',
        '%post_id%',
        '%category%'
    );

    if ($id) {
        $idpost = $wpdb->get_row("SELECT ID, post_date, post_name FROM $wpdb->posts WHERE ID = $id");
    } else {
        $idpost = $post;
    }
       
    if ('' != get_settings('permalink_structure')) {
	    $unixtime = strtotime($idpost->post_date);

        $cats = get_the_category($idpost->ID);
        $category = $cats[0]->category_nicename;

        $rewritereplace = array(
                                date('Y', $unixtime),
                                date('m', $unixtime),
                                date('d', $unixtime),
                                date('H', $unixtime),
                                date('i', $unixtime),
                                date('s', $unixtime),
                                $idpost->post_name,
                                $idpost->ID,
                                $category
                                );
        return get_settings('home') . str_replace($rewritecode, $rewritereplace, get_settings('permalink_structure'));
    } else { // if they're not using the fancy permalink option
        return get_settings('home') . '/' . get_settings('blogfilename').$querystring_start.'p'.$querystring_equal.$idpost->ID;
    }
}

function get_month_link($year, $month) {
    global $querystring_start, $querystring_equal;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
    if ('' != get_settings('permalink_structure')) {
        $permalink = get_settings('permalink_structure');

        // If the permalink structure does not contain year and month, make
        // one that does.
        if (! (strstr($permalink, '%year%') && strstr($permalink, '%monthnum%'))
            || preg_match('/%category%.*(%year%|%monthnum%|%day%)/', $permalink)) {
            $front = substr($permalink, 0, strpos($permalink, '%'));
            $permalink = $front . '%year%/%monthnum%/';
        }

        $off = strpos($permalink, '%monthnum%');
        $offset = $off + 11;
        $monthlink = substr($permalink, 0, $offset);
        if ('/' != substr($monthlink, -1)) $monthlink = substr($monthlink, 0, -1);
        $monthlink = str_replace('%year%', $year, $monthlink);
        $monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
        $monthlink = str_replace('%post_id%', '', $monthlink);
        $monthlink = str_replace('%category%', '', $monthlink);
        return get_settings('home') . $monthlink;
    } else {
        return get_settings('home') .'/'. get_settings('blogfilename') .$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2);
    }
}

function get_day_link($year, $month, $day) {
    global $querystring_start, $querystring_equal;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
    if (!$day) $day = gmdate('j', time()+(get_settings('gmt_offset') * 3600));
    if ('' != get_settings('permalink_structure')) {
        $permalink = get_settings('permalink_structure');

        // If the permalink structure does not contain year, month, and day,
        // make one that does.
        if (! (strstr($permalink, '%year%') && strstr($permalink, '%monthnum%')&& strstr($permalink, '%day%'))
            || preg_match('/%category%.*(%year%|%monthnum%|%day%)/', $permalink)) {
            $front = substr($permalink, 0, strpos($permalink, '%'));
            $permalink = $front . '%year%/%monthnum%/%day%/';
        }

        $off = strpos($permalink, '%day%');
        $offset = $off + 6;
        $daylink = substr($permalink, 0, $offset);
        if ('/' != substr($daylink, -1)) $daylink = substr($daylink, 0, -1);
        $daylink = str_replace('%year%', $year, $daylink);
        $daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
        $daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
        $daylink = str_replace('%post_id%', '', $daylink);
        $daylink = str_replace('%category%', '', $daylink);
        return get_settings('home') . $daylink;
    } else {
        return get_settings('home') .'/'. get_settings('blogfilename') .$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2).zeroise($day, 2);
    }
}

function get_feed_link($feed='rss2') {
    $do_perma = 0;
    $feed_url = get_settings('siteurl');
    $comment_feed_url = $feed_url;

    $permalink = get_settings('permalink_structure');
    if ('' != $permalink) {
        $do_perma = 1;
        $feed_url = get_settings('home');
        $index = get_settings('blogfilename');
        $prefix = '';
        if (preg_match('#^/*' . $index . '#', $permalink)) {
            $feed_url .= '/' . $index;
        }

        $comment_feed_url = $feed_url;
        $feed_url .= '/feed';
        $comment_feed_url .= '/comments/feed';
    }

    switch($feed) {
        case 'rdf':
            $output = $feed_url .'/wp-rdf.php';
            if ($do_perma) {
                $output = $feed_url . '/rdf/';
            }
            break;
        case 'rss':
            $output = $feed_url . '/wp-rss.php';
            if ($do_perma) {
                $output = $feed_url . '/rss/';
            }
            break;
        case 'atom':
            $output = $feed_url .'/wp-atom.php';
            if ($do_perma) {
                $output = $feed_url . '/atom/';
            }
            break;        
        case 'comments_rss2':
            $output = $feed_url .'/wp-commentsrss2.php';
            if ($do_perma) {
                $output = $comment_feed_url . '/rss2/';
            }
            break;
        case 'rss2':
        default:
            $output = $feed_url .'/wp-rss2.php';
            if ($do_perma) {
                $output = $feed_url . '/rss2/';
            }
            break;
    }

    return $output;
}

function edit_post_link($link = 'Edit This', $before = '', $after = '') {
    global $user_level, $post;

    get_currentuserinfo();

    if ($user_level > 0) {
        $authordata = get_userdata($post->post_author);
        if ($user_level < $authordata->user_level) {
            return;
        }
    } else {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=edit&amp;post=$post->ID";
    echo "$before <a href=\"$location\">$link</a> $after";
}

function edit_comment_link($link = 'Edit This', $before = '', $after = '') {
    global $user_level, $post, $comment;

    get_currentuserinfo();

    if ($user_level > 0) {
        $authordata = get_userdata($post->post_author);
        if ($user_level < $authordata->user_level) {
            return;
        }
    } else {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=editcomment&amp;comment=$comment->comment_ID";
    echo "$before <a href='$location'>$link</a> $after";
}

// Navigation links

function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
    global $id, $post, $wpdb;
    global $p, $posts, $posts_per_page, $s, $single;
    global $querystring_start, $querystring_equal, $querystring_separator;

    if(($p) || ($posts_per_page == 1) || 1 == $single) {

        $current_post_date = $post->post_date;
        $current_category = $post->post_category;

        $sqlcat = '';
        if ($in_same_cat != 'no') {
            $sqlcat = " AND post_category = '$current_category' ";
        }

        $sql_exclude_cats = '';
        if (!empty($excluded_categories)) {
            $blah = explode('and', $excluded_categories);
            foreach($blah as $category) {
                $category = intval($category);
                $sql_exclude_cats .= " AND post_category != $category";
            }
        }

        $limitprev--;
        $lastpost = @$wpdb->get_row("SELECT ID, post_title FROM $wpdb->posts WHERE post_date < '$current_post_date' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT $limitprev, 1");
        if ($lastpost) {
            $string = '<a href="'.get_permalink($lastpost->ID).'">'.$previous;
            if ($title == 'yes') {
                $string .= wptexturize(stripslashes($lastpost->post_title));
            }
            $string .= '</a>';
            $format = str_replace('%', $string, $format);
            echo $format;
        }
    }
}

function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
    global $posts_per_page, $post, $wpdb, $single;
    if(1 == $posts_per_page || 1 == $single) {

        $current_post_date = $post->post_date;
        $current_category = $post->post_category;

        $sqlcat = '';
        if ($in_same_cat != 'no') {
            $sqlcat = " AND post_category='$current_category' ";
        }

        $sql_exclude_cats = '';
        if (!empty($excluded_categories)) {
            $blah = explode('and', $excluded_categories);
            foreach($blah as $category) {
                $category = intval($category);
                $sql_exclude_cats .= " AND post_category != $category";
            }
        }

        $now = current_time('mysql');

        $limitnext--;

        $nextpost = @$wpdb->get_row("SELECT ID,post_title FROM $wpdb->posts WHERE post_date > '$current_post_date' AND post_date < '$now' AND post_status = 'publish' $sqlcat $sql_exclude_cats AND ID != $post->ID ORDER BY post_date ASC LIMIT $limitnext,1");
        if ($nextpost) {
            $string = '<a href="'.get_permalink($nextpost->ID).'">'.$next;
            if ($title=='yes') {
                $string .= wptexturize(stripslashes($nextpost->post_title));
            }
            $string .= '</a>';
            $format = str_replace('%', $string, $format);
            echo $format;
        }
    }
}

function get_pagenum_link($pagenum = 1){
   $qstr = $_SERVER['REQUEST_URI'];

   $page_querystring = "paged"; 
   $page_modstring = "page/";
   $page_modregex = "page/?";

   // if we already have a QUERY style page string
   if( stristr( $qstr, $page_querystring ) ) {
       $replacement = "$page_querystring=$pagenum";
      $qstr = preg_replace("/".$page_querystring."[^\d]+\d+/", $replacement, $qstr);
   // if we already have a mod_rewrite style page string
   } elseif ( preg_match( '|'.$page_modregex.'\d+|', $qstr ) ){
      $qstr = preg_replace('|'.$page_modregex.'\d+|',"$page_modstring$pagenum",$qstr);

   // if we don't have a page string at all ...
   // lets see what sort of URL we have...
   } else {
      // we need to know the way queries are being written
      global $querystring_start, $querystring_equal, $querystring_separator;
      // if there's a querystring_start (a "?" usually), it's deffinitely not mod_rewritten
      if ( stristr( $qstr, $querystring_start ) ){
         // so append the query string (using &, since we already have ?)
         $qstr .=  $querystring_separator.$page_querystring.$querystring_equal.$pagenum;
         // otherwise, it could be rewritten, OR just the default index ...
      } elseif( '' != get_settings('permalink_structure')) {
         $qstr = preg_replace('|(.*)/[^/]*|', '$1/', $qstr).$page_modstring.$pagenum;
      } else {
         $qstr = get_settings('blogfilename') . $querystring_start.$page_querystring.$querystring_equal.$pagenum;
      }
   }

   $home_root = str_replace('http://', '', trim(get_settings('home')));
   $home_root = preg_replace('|/+|i', '/', $home_root);
   $qstr = str_replace($home_root . '/', '', $qstr);
   $qstr = preg_replace('|^/+|', '', $qstr);
   return preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', trailingslashit( get_settings('home') ) . trailingslashit($qstr) );
}

function next_posts($max_page = 0) { // original by cfactor at cooltux.org
    global $p, $paged, $what_to_show, $pagenow;
    global $querystring_start, $querystring_equal, $querystring_separator;

     if (empty($p) && ($what_to_show == 'paged')) {
         if (!$paged) $paged = 1;
         $nextpage = intval($paged) + 1;
         if (!$max_page || $max_page >= $nextpage) {
             echo get_pagenum_link($nextpage);
         }         
     }
}

function next_posts_link($label='Next Page &raquo;', $max_page=0) {
    global $p, $paged, $result, $request, $posts_per_page, $what_to_show, $wpdb;
    if ($what_to_show == 'paged') {
        if (!$max_page) {
            $nxt_request = $request;
            //if the query includes a limit clause, call it again without that
            //limit clause!
            if ($pos = strpos(strtoupper($request), 'LIMIT')) {
                $nxt_request = substr($request, 0, $pos);
            }
            $nxt_result = $wpdb->query($nxt_request);
            $numposts = $wpdb->num_rows;
            $max_page = ceil($numposts / $posts_per_page);
        }
        if (!$paged)
            $paged = 1;
        $nextpage = intval($paged) + 1;
        if (empty($p) && (empty($paged) || $nextpage <= $max_page)) {
            echo '<a href="';
            next_posts($max_page);
            echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
        }
    }
}


function previous_posts() { // original by cfactor at cooltux.org
    global $_SERVER, $p, $paged, $what_to_show, $pagenow;
    global $querystring_start, $querystring_equal, $querystring_separator;

     if (empty($p) && ($what_to_show == 'paged')) {
         $nextpage = intval($paged) - 1;
         if ($nextpage < 1) $nextpage = 1;
         echo get_pagenum_link($nextpage);
     }
}

function previous_posts_link($label='&laquo; Previous Page') {
    global $p, $paged, $what_to_show;
    if (empty($p)  && ($paged > 1) && ($what_to_show == 'paged')) {
        echo '<a href="';
        previous_posts();
        echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
    }
}

function posts_nav_link($sep=' &#8212; ', $prelabel='&laquo; Previous Page', $nxtlabel='Next Page &raquo;') {
    global $p, $what_to_show, $request, $posts_per_page, $wpdb;
    if (empty($p) && ($what_to_show == 'paged')) {
        $nxt_request = $request;
        if ($pos = strpos(strtoupper($request), 'LIMIT')) {
            $nxt_request = substr($request, 0, $pos);
        }
        $nxt_result = $wpdb->query($nxt_request);
        $numposts = $wpdb->num_rows;
        $max_page = ceil($numposts / $posts_per_page);
        if ($max_page > 1) {
            previous_posts_link($prelabel);
            echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
            next_posts_link($nxtlabel, $max_page);
        }
    }
}


?>
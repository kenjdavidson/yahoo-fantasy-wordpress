<?php

/**
 * Provide an Admin About page describing the Admin pages.  This documents
 * how to configure and then use the short code to display details about the 
 * users Yahoo Fantasy Sports account.
 *
 * @since      1.1.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/admin/partials
 */

?>

<div class="wrap about">
    <h1>About Yahoo Fantasy Sports Plugin</h1>
    <p>
        The Yahoo Fantasy Sports plugin allows you to include details about
        your Yahoo Fanasty teams and leagues, within your pages and posts.  Using
        the [yahoofantasysports] shortcode, you can add the following details:
        
        <ul class="about-intro">
            <li>
                Games, a listing of games in which you have been registered
                over a number of seasons.  Games in Yahoo Fantasy terms are the
                sports in which your teams exist: Footbal, Baseball, etc.
            </li>
            <li>
                Leagues, a listing of the leagues for each game (sport).  You
                may contain one or many teams within leagues for a specific
                game (sport).
            </li>
            <li>
                Teams, a listing of each of your teams; displaying information
                such as: image, name, position/standing, score, etc.  Teams 
                are displayed as badges, for easy access.
            </li>
            <li>
                Standings, a table of your leagues standings.  Standings show 
                all teams within the league and which position they are 
                currently in.  If the league is finished, the top three teams
                are displayed with their medal colors.
            </li>
            <li>
                Matchups, a listing of the leagues current matchups.  Matchups
                will only be displayed when the season is in progress.  When
                the season is over this will default to the Teams display.
            </li>
        </ul>
    
    </p>
    
    <h1>Yahoo OAuth Configuration</h1>
    <p>
        Yahoo allows connection to their services through a security method called
        OAuth.  This just means that you need to login to their service and tell
        them that you want to allow this Plugin access to their data.  To do this
        there are a number of things that need to happen:
        <ul>
            <li>
                First, you need to register for a developer account with Yahoo.
                You need to do this because PHP is open source and having the
                author use their own Consumer/Secret key just isn't a good idea.
                In order to get a developer account and the Consumer/Secret needed
                go to this <a href="https://developer.yahoo.com/">link</a> and 
                signup.
            </li>
            <li>
                Once you have your Consumer/Secret keys, you can continue to the
                Yahoo OAuth Config page and start the process.  Follow the 
                directions on the Yahoo OAuth page to complete the login and
                test your connection.
            </li>
        </ul>
    </p>
    
    <h1>Yahoo Fantasy Sports Shortcode</h1>
    <p>
        To display Yahoo Fantasy content on your pages and posts, the shortcode
        <b>[yahoofantasysports]</b> is used.  The shortcode allows for content
        to be placed within it, this content is displayed after the title, before
        the details. The following list of options provides you with a number of 
        different display options (usage examples show default values):
        <ul>
            <li>
                Wrap / Wrap-Class <br/>
                [yahoofantasysports wrap="div" wrap-class="fantasy-sports"] are 
                used to wrap the Yahoo Fantasy content within a specific HTML
                element and then provide specific class(es) to that element.
            </li>
            <li>
                Title <br/>
                [yahoofantasysports title="Yahoo! Fantasy"] allows you to set the 
                title to display at the top of the Yahoo Fantasy content.
            </li>
            <li>
                Title-Wrap / Title-Class <br/>
                [yahoofantasysports title-wrap="h4" title-class="title"] is used
                to wrap the title String a specific HTML Element, as well as 
                add custom class(es) to that element.
            </li>
            <li>
                Type <br/>
                [yahoofantasysports type="standings"] is used to display the 
                specific Yahoo Fantasy content you wish to be displayed.  The
                options available are listed above.                 
            </li>
            <li>
                Seasons <br/>
                [yahoofantasysports type="standings" seasons="2016"] allows you
                you to choose a specific year or years to display within this
                specific shortcodes content.  Multiple seasons can be added 
                with a comma [... seasons="2015,2016"]
            </li>
        </ul>
    </p>
</div>

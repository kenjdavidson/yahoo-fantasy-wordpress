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
        the shortcode described below, you can display specific information
        from your Yahoo! sports account.
    </p>
    <p>
        The quickest shortcode to use is [yahoofantasysports type="TYPE"] where
        TYPE is replaced with one of the following:
        
        <ul class="about-intro">
            <li>
                <h5>Games</h5>
                Displays and unordered list of the games which the user is/was
                registered to.  This is the highest level info that can be 
                displayed.
            </li>
            <li>
                <h5>Leagues</h5>
                Displays a table for each game the user is/was registered to.  
                Each table consists of the leagues within that game that were
                active during the specific season.  Columns in the tables are:
                League name, Start Date, End Date, Scoring Type and Number of
                Teams.
            </li>
            <li>
                <h5>Teams</h5> 
                Displays a quick peak of the teams in which a user is registered.
                The badge displays a general info about the team, plus a quick
                peak at the top players on the team.
            </li>
            <li>
                <h5>Standings</h5>
                Displays Atable of your leagues standings.  Standings show 
                all teams within the league and which position they are 
                currently in.  If the league is finished, the top three teams
                are displayed with their medal colors.
            </li>
            <li>
                <h5>Matchups</h5> 
                Displays a listing of the leagues current match-ups.  Match-ups
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
    
    <h1>Yahoo Fantasy Sports Options</h1>
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
    
    <h1>Yahoo Displayer Customization</h1>
    <p>
        There are a number of ways to customize the way that the Yahoo!
        Sports shortcodes are displpayed:
        <ul>
            <li>
                <h5>Style Sheets</h5>
            </li>
            <li>
                <h5>Custom YahooPublicDisplayer</h5>
            </li>
            <li>
                <h5>Wordpress Filters</h5>
            </li>
        </ul>
    </p>
</div>

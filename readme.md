# Web Site Design

ALiVE has two web front ends, the official ALiVE mod web page and the War Room site.

## URLs

Current site: www.alivemod.com (arma3live.com redirects here)
Current WarRoom: www.alivemod.com/war-room

## Tasks Status

* **Cleanup Operations Page, fix AAR**
* Add stats to Operations Page (see Operations Detail Page below)
* Admin page that allows group admins to delete mission data, web admins to prune databases
* Allow upload of icons/pics
* Update tables to link to players, groups, operations and AOs
* Replace missing icons with text
* Add vehicle/weapon Display Name to icons when mouse focus/rollover
* Twitch page integration
* Add Twitch streams to Operations Detail Page
* Add Twitch stream to Players Page
* Add Twitch streams to Groups Page
* Blink on global map, groups that are live - link to live op
* Steam OpenID Auth
* Design/Create - Edit Operation page
* Add favourite/current loadout to Player page
* Design/Create - Edit Loadout subpage
* Design/Create - live map page
* Orbat diagram page showing groups and parents etc
* About Us page
* Create AO page - Tup
* AO Map with list of operations and totals stats - Tup

## Stack Ranked User Stories

P0
- ~~As a member of a clan/group I want to review a past/current inactive operation and see a static(zoomable etc) map with all the events listed in time order~~, overall operation stats, operation map markers, after action reports, current player unit locations, current player unit loadouts, current player side vehicles and loadout, current sector status and list of tasks and status

P1
- ~~As a player I want a player page with pretty icons of my favourite loadout, -a picture of my character(!)~~
- As a player I want to edit my loadout if the group admin allows it.
- As a mission maker, I want to be able to preview units/weapons/vehicles and classnames to make it easier to build out missions.
- ~~As a server admin I want to see ASM data for my server(s)- and an overall average so I can track server performance~~

P2
* As a player I want to watch a live "active" operation, that should include a page with a live map (tracking player units), any associated twitch streams and a feed of events happening in game along with any pertinent stats
* As a server/group admin/group cmdr I want to CRUD markers
* As a server/group admin/group cmdr I want to CRUD AARs
* As a server/group admin/group cmdr I want to CRUD an operation report including text, pictures and links to videos
* As a server/group admin/group cmdr I want to update player location 
* As a server/group admin/group cmdr I want to CRUD a players loadout
* As a server/group admin/group cmdr I want to CRUD a vehicles loadout
* As a server/group admin/group cmdr I want to CRUD tasks within an operation
* ~~As a group admin I want be able to create my own official seal/insignia as a group avatar~~ (http://www.says-it.com/sample_generators.zip)

P3
* As a group admin/group cmdr I want to display my members in an ORBAT diagram http://www.theriflesgu.co.uk/orbat.html
* As a group admin/ group cmdr I want to define the ORBAT structure
* As a group officer I want to be able to move my members into different sub units in the ORBAT
* As an group officer I want to assign customisable flags to my members in order to track training progress, rewards, medals or badges
* As a group cmdr I want to be able to track my members attendance on operations and view a graph of attendance trends
* As a group member I want to manage my squad XML in my profile
* As a group admin I want my group data to be viewable to my group only

## Design Concepts

Mood boards for official addon site - http://www.battlefield.com/ http://www.arma3.com/  http://www.australianarmedforces.org/
Mood boards for War Room site
http://battlelog.battlefield.com/bf4/geoleaderboard/
https://players.planetside2.com/#!/leaderboards
http://www.callofduty.com/ghosts/features/mp/app
http://www.wallpaper4me.com/images/wallpapers/taskforce141-864985.jpeg
http://vimeo.com/38194135
http://i.imgur.com/NgwzzbC.jpg

## Platform

Laravel - An open source, open standards approach to the site. LAMP implementation using a PHP framework for Content Management, using HTML5, CSS and jQuery. We are using CouchDB for game data. 

## Installation

### Laravel 4 Site

*1)* Install some kind of WAMP stack eg:
http://www.wampserver.com/

*2)* Get composer working:
Windows installer on this page:
http://getcomposer.org/download/

*3)* Make sure you have php.exe on the windows path

*4)* Copy the website directory from the repo into webroot.

*5)* Open a command line at the website directory in your webroot and run:

composer install

*6)* Open phpmyadmin via wamp
create a new database:
alive_db
collation:
utf8_unicode_ci

make sure you set your database credentials to 
user: root
password: root
alternatively edit app/config/database.php and enter your DB credentials 

*7)* Make sure you have mod_rewrite or rewrite module enabled on apache

*8)* Run
php artisan migrate --package=cartalyst/sentry

*9)* Run
php artisan migrate

*10)* Once complete run 

php artisan db:seed

11) To get emails working edit 
app/config/mail.php
and add your SMTP outgoing server and replace the details in there

You should now be up and running.
*To login as admin use:*

email: arjaydev@gmail.com
password: cheese

## Site Map

alivemod.com
> Home
> About Us
> Login / Register
> > Download
> FAQ
> War Room

arma3live.com / war-room.alivemod.com
> Login
> > Home  - Global Map with links to different AOs (gets stats on a per AO  basis)
> > Personnel Records
> > > Personnel Detail
> > > > Edit Details (Player linked with character only)
> > Operations
> > > Operation Detail
> > > > Edit Details (Server/group admin for operation)
> > ORBAT
> > > Group Detail
> > > > Edit Details (Server/group admin for group)
> > Geo / AO (just menu header with list of AOs when rolled over)
> > > AO Detail - Altis, Stratis, Takistan, Shapur, Chernarus, Utes (desert? Proving grounds?) Full gunny map, with satellite effect mask?
> > Live!
> > > Select Active Mission

### Page Descriptions

* Login

Login page looks like a terminal login, simple page with alive background, electronic version of the NATO Joint Task Force Alpha insignia, with username input, password input and a button to login and a button to signup. Check box to remember the user. Button to reset password if forgotten. Fake military illegal access disclaimer/warning.

* All Pages

Header includes a dynamic welcome message made up of either the NATO JTF insignia (small) with the words NATO Joint Task Force Alpha or ALiVE War Room logo. Underneath is text "Battlefield Information System". Insignia will change once logged in and user is a member of group (switch to group insignia). Header includes menus for the Home, Personnel, Operations, ORBAT, Geo/AO, Live . Header also includes a logout button. Data feed items include a "NTWK" data item listing the user's IP, Geo location based on IP ("here":http://stackoverflow.com/questions/7766978/geo-location-based-on-ip-address-php), number of active units, number of combat hours, number of operations, total EKIA, total losses, ammunition spent. 

Footer includes a fake military disclaimer

* Home Page - http://i.imgur.com/NgwzzbC.jpg

Background: Global Map presented full screen ("see":http://imgur.com/ZcMmB0x,AsOvebH,X7Rssmt,sCBfnnm,7hvaAr5,CoJ7NB8,mTRVrTD#0) with a header and footer overlaid. 

Middle: Callouts for each AO overlaid on global map, each callout should have AO name, forces deployed (number of personnel, units deployed (number of groups), Losses, EKIA and Number of Operations as data points. If possible, estimated enemy strength (based on average number of EN profiles stored for all ops in AO). Clicking on a callout takes you to the AO Map Page with for that AO.

Left Middle Top: Recent 15 Operations
Left Middle Bottom: Tier 1 Operators
Right Middle: Twitter like feed of live events

Bottom: Casualties, OPFOR Losses, BLUFOR Losses
Bottom: a small (100% width) operational tempo graph for all operations. 

* Personnel Page

Background: ALiVE Background

Middle: Player List - avatar, name, operations, kills etc

Left Middle Top: Tier 1 Operators
Left Middle Bottom: Tier 1 Marksmen
Right Middle Top: Top Pilots/Gunners
Right Middle Bottom: Combat Medics

Bottom: Personnel Performance Analysis (4 boxes) top 5 for each

Clicking on any player will take you to Personnel Detail Page

* Personnel Detail Page

Background: ALiVE Background

Upper Section

Left Top: Player Details (3 column list of attribute:value boxes) include pic/avatar/class pic, group insignia, last logon

Middle Top: Overview stats, Combat Experience
Middle Middle: Rankings (operators, medic, pilot, gunner, ops, hours)
Middle Bottom: Recent Operations

Right: Recent events for player

Middle Section (Current Loadout if stored)

Left: Class Pic
Middle: Eye wear, ear phones, Helmet, primary weapon, secondary weapon, vest, vest contents, uniform contents
Right: items, Ruck and contents
Edit button exists if logged in user is player - can edit loadout.

Lower Section
Fav Weapon, Fav Vehicle, Vehicle Experience, Weapons Experience, Field Experience

* Operations Page

List of Operations conducted with table of stats, click on operation and links to operation details page

* Operations Detail Page

Background: Operation Map

Left Top: Map Controls
Left Middle: Table of players - kills, injuries/deaths, time played

Middle Top: Scrollable set of archived twitch streams from op
Middle Bottom: AARs playback controls (forward and rewind of player movement for time period X mins)

Right Top: List of tasks/objectives and status (based on time period represented, paginated, load tasks/objective markers to map as icons)
Right Bottom: List of operation events (based on time period represented, paginated, load events to map as icons)

* ORBAT Page

Background: ALiVE Background graphic

Group List with search and pagination - small insignia, group name, operations, kills, losses (ranked by operations)

* Group Detail Page

Left
Group Name,
Group Avatar
Group overview stats
Combat Experience

Middle
- Leaders
- Officers
- Soldiers
Most Recent Ops

Right
Battle Feed

Lower Left
Tier 1 Operators
Vehicle Usage
Mounted Weapon Kills

Lower Middle
Top Guns
Weapon Usage
Weapons Effectiveness

Lower Right
Top Medics
Units Deployed
Unit Effectiveness

* AO Detail Page

Same as Home Page, however map = AO map, data = filtered for that AO. 

Map should be 100% width/height of browser, with header, footer overlaid. Map navigation should exist top left (below header though). Map should have satellite effect applied.

Callouts show groups deployed in AO, position based on last known position of commander (or first group member found if commander not found). Callout for group should include number of units (players), operations, losses, EKIA (same as AO callout really). Clicking on a callout takes you to the Group page for that group. Callout should represent type of group and size in NATO standard icon.

# Creating Map Tiles

Ensure you have the *ALiVEClient.dll* in the root of your @ALiVE folder.

Launch Arma 3 with the map mod enabled. Go into game.

Get the map EMF by:

* Hold [Left Shift] down and then press the [Numpad - (minus)] button. Then let go and type TOPOGRAPHY. Nothing is displayed as you type however the key strokes are being recorded.

* Generates a map in EMF vector format. The file is (not) always created at the root directory of the C: drive (Windows 7 with UAC might put the file in Virtualstore, "C:\Users\<username>\AppData\Local\Virtualstore" ). The output file is generated when the map is next viewed in game. German Users: Enter TOPOGRAPHZ instead. 

Open debug console:

* Type in the following  in the console (get the user and password from an admin)<pre>["user","password",false] call ALiVE_fnc_exportMapWarRoom;</pre>
* Hit *Local Exec*

Wait for the image to finish uploading

Exit the game

Go to https://alivemod.com/war-room and login in as an admin

Select *AO*

Hit *Create New Area of Operation* button

Your map image should be visible on the page

Fill in all the necessary details for the map

Hit *Create* button

Your AO is now created along with all its tiles.

## Maps Done

* Altis - Gunny
* Bootcamp_ACR - ARJ
* Bornholm
* Celle - ARJ
* Chernarus - Gunny
* Chernarus Summer - Tup
* Clafghan - ARJ
* Desert - Tup
* Fallujah - Gunny
* Fata - Gunny
* FDF_Isle1_a - ARJ
* Gorgona - Tup
* hellskitchen - ARJ
* Helvantis - Tup
* IsolaDiCapraia - ARJ
* Kunduz - Friznit
* MCN_HazarKot - Gunny
* mbg_celle2 - ARJ
* MCN_Aliabad - ARJ
* Mountains ACR - Tup
* N'Ziwasogo (pja305) - Gunny
* namalsk - ARJ
* Porto - Tup
* praa_av - ARJ
* Proving Grounds - Gunny
* Rahmadi - Tup
* Reshmaan - ARJ
* Sahrani (A2) - Gunny
* Sara - Tup (Sahrani A1)
* Sara_dbe1 - Tup (United Sahrani)
* Saralite - Tup (Southern Sahrani)
* Shapur - Tup
* Stratis - Gunny
* Takistan - Gunny
* Thirsk - ARJ
* ThirskW - ARJ
* Tigeria - ARJ
* Utes - Gunny
* Tora Bora - ARJ
* TUP_Qom - ARJ
* VR - Tup
* Woodland_ACR - ARJ
* Zargabad - Gunny

## Maps to be Done

* Caribou
* Koplic
* Vostok
* Sturko
* pja306 (Kalu Khan)
* Everon 2014
* Isla Duala A3
* Imrali
* Panthera A3
* PJA307
* PJA310
* Staszow
* Tavi
* Sahrani A3
* XCam Prototype
* hellskitchens

## Archived Task Status

* ~~Setup PHP Framework - ARJ~~
* ~~Create alivemod.com home page for official site - ARJ~~
* ~~Setup mySQL database - ARJ~~
* ~~Setup Auth - ARJ~~
* ~~Setup user/group management system - ARJ~~
* ~~Server Admin registration page - ARJ~~
* ~~Setup Server download (can download once registered) - ARJ~~
* ~~Web Design Document - Tupolov~~
* ~~Design War Room Home Page- Tup http://i.imgur.com/NgwzzbC.jpg~~
* ~~Create War Room Home Page - Tup/ARJ~~ 
* ~~Style data boxes - ARJ~~
* ~~Create AO callouts on global map~~
* ~~Design Player Page - Tup~~
* ~~Create Player Page - Tup/ARJ~~
* ~~Design ORBAT Page - Tup~~
* ~~Create ORBAT Page - Tup~~
* ~~Update home page to show groups deployed- - Tup~~
* ~~Add server perf data to server page- - Tup~~
* ~~Streamline group registration per HH's notes (http://pastebin.com/XTx3FJqh) - ARJ/Tup~~
* ~~Paginate Members/Officers on group page- - Tup~~
* ~~General CSS/design tidy up - ARJ~~
* ~~Correct download with subfolder- Tup~~
* ~~Server perf chart should match host name or IP- Tup~~
* ~~all db calls via api- Tup~~
* ~~switch to require valid user on db- Tup~~
* ~~List of last 20 ops for group on group page- - Tup~~
* ~~List of medics/pilots on group page- - Tup~~
* ~~Add Donate button- Tup~~
* ~~Admin nav bar collapse? (clashes with left hand nav bar)- ARJ~~
* ~~Create AO Map tiles- -Altis-, Stratis, Takistan, Chernarus, Shapur - Gunny~~
* ~~Design Operations Page for AAR- Tup~~
* ~~Create Operations Page for AAR- - Gunny~~
* ~~Operations AAR Map with reviewable timeline of all events in the mission- - Gunny~~

## Archived User Stories

P0
* ~~As a user I want to find out about ALiVE mod (About Us page, FAQ page, Latest News Page, link to/embed social media, link to War Room) on an official looking site~~
* ~~As a server admin I want to register to download the ALiVE non-mod server components (Arma2Net, extensions, config file) - that registration should work for War Room site too~~
* ~~As a player I want to register so I can access the War Room site~~
* ~~As a registered user I want to edit my account details in case my email address or password needs to change~~
* ~~As a user I want to be able to donate to the cause!~~
* ~~As an ALiVE admin I want to register so that I can manage users/groups/AOs/clans and view ASM web data~~

P1
* ~~As a server admin I want to add servers that are authorized to post data to the War room~~
* ~~As a player I want to see a list of events updated "live" like a twitter feed for all active operations on the home page~~
* ~~As a player I want to see a global map with all active AOs~~
* ~~As a player I want to see a list of recent operations by group~~
* ~~As a player I want to see player page where I can see statistic league tables for Kills, Best Shots, Gunnery Kills, Medical heal/revive, recent operations, global kill/ammo/play time/units count~~
* ~~As a player I want to see operation statistics league table for number of units played, units killed, deaths, tasks completed, vehicle destroyed, vehicles lost, vehicle time, flight time, operation time, map~~
* ~~As a player I want to see a player page with all my statistics listed~~
* ~~As a member of a clan/group I want to review a past/current inactive operation and see a static(zoomable etc) map with all the events listed in time order~~

P2
* ~~As a server admin I want to register a clan/group so I can maintain a group web profile~~
* ~~As a server/group admin I want to edit clan/group details~~
* ~~As a player I want to join a clan/group so I can be listed in the groups~~
* ~~As a member of a clan/group I want to see clan details that include ORBAT, cummulative stats and a list of operations~~
* ~~As a server admin I want to add/authorize/remove group admins from my group~~
* ~~As a server/group admin I want to add/authorize/remove group commanders from my group~~
* ~~As a server/group admin I want to add/authorize/remove players from my group~~
* ~~As a spectator I want to register so I can access the War Room site~~

h1. OLD Map Tiles Process

Get the map EMF by:

* Hold [Left Shift] down and then press the [Numpad - (minus)] button. Then let go and type TOPOGRAPHY. Nothing is displayed as you type however the key strokes are being recorded.

* Generates a map in EMF vector format. The file is (not) always created at the root directory of the C: drive (Windows 7 with UAC might put the file in Virtualstore, "C:\Users\<username>\AppData\Local\Virtualstore" ). The output file is generated when the map is next viewed in game. German Users: Enter TOPOGRAPHZ instead. 

Grab the following files from the internet

* EmfToPng.exe
* maptiler-1.0-beta2-setup.exe

Install Map Tiler by running *maptiler-1.0-beta2-setup.exe*

Convert the map EMF File using *EmfToPng.exe* , by putting the emf file in the same directory as the EXE.

Open a CMD Window and change directory to where the above files a located.

Run the following command:

EmfToPng.exe mapname.emf *(where mapname is the name of the emf file you want to convert)*

After sometime you will now have a png file of the map in the directory.

Open this png in Photoshop or Gimp and resize it to 32768 x 32768.

Save as Tiff.

Open Maptiler.

On the first screen select the bottom option *Image Based Tiles (Raster)*. Click continue

On the *Source Data Files* page, add the previously created Tiff file. Click Continue

Click continue on the *Spatial Reference System* page.

On the *Details about the tile Pyramid page*, check that the *Maximum Zoom Level* is set to *7*. Click Continue

The Destination folder should be the same name as the Map. Click Continue.

Leave Openlayers as the viewer. Click Continue.

Leave the Details as Default. Click Continue.

Then click *Render*.

Map Tiler will then create all teh required Tiles, this will take some time.

Once finished FTP the folder to the following directory on the web host:

*/public_html/maps*
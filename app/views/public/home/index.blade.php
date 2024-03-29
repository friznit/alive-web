@extends('public.layouts.default')

{{-- Content --}}
@section('content')

<div class="jumbotron alive-background-panel" id="Welcome">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img id="welcome_logo" src="{{ URL::to('/') }}/img/home_logo.png" class="img-responsive"/>
                <p id="welcome_text">ALIVE is the next generation dynamic persistent mission addon for Arma 3. Developed by Arma community veterans, the easy to use modular mission framework provides everything that players and mission makers need to set up and run realistic military operations in almost any scenario up to Company level, including command, combat support, service support and logistics.</p>
            </div>
            <div id="welcome_image" class="col-md-5 col-md-offset-1">
                <img src="{{ URL::to('/') }}/img/action3.jpg" class="img-responsive light-blue-border" />
            </div>
        </div>
    </div>
</div>

<div class="jumbotron light-blue-panel" id="Gameplay">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Gameplay</h2>
                <p>ALiVE is a dynamic campaign mission framework. The editor placed modules are designed to be intuitive but highly flexible so you can create a huge range of different scenarios by simply placing a few modules and markers. The AI Commanders, including an option for insurgency style tactics, have an overall mission and a prioritised list of objectives that they will work through autonomously.  Players can choose to tag along with the AI and join the fight, take your own squad of AI or other players and tackle your own objectives or just sit back and watch it all unfold.</p>
                <p>Mission makers may wish to experiment by synchronizing different modules to each other, or  using standalone ALiVE modules as a backdrop for dynamic missions and campaigns, enhancing scenarios created with traditional editing techniques.  ALiVE can significantly reduce the effort required to make a complex mission by adding ambience, support and persistence at the drop of a module.</p>
            </div>
            <div class="col-md-5 col-md-offset-1">
                <img src="{{ URL::to('/') }}/img/action4.jpg" class="img-responsive dark-border" />
            </div>
        </div>
    </div>
</div>

<div class="jumbotron black-panel" id="Trailer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/lsztsBqNbFc" autohide="3" frameborder="0" showinfo="0" modestbranding="1" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron white-panel" id="Features">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Features</h2>
                <ul class="fa-ul white-list">                
                    <li><i class="fa-li fa fa-caret-right"></i>Easy to use Arma 3 editor modules for rapid scenario generation</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Automatic map-wide strategic placement of enemy AI for large scale scenarios</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Virtual AI system allowing for simulation of thousands of AI for large scale scenarios including land, air and sea forces.</li>                    
                    <li><i class="fa-li fa fa-caret-right"></i>AI commanders that use different strategies to occupy/invade/terrorize objectives</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Military logistics system allowing AI commanders to reinforce and resupply</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Complete insurgency simulation based on civilian interaction, recruitment and asymmetric warfare</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Combat air support, transport, artillery and logistics systems allowing for company level combined arms operations</li>
                    <li><i class="fa-li fa fa-caret-right"></i>C2ISTAR system for task/mission generation and management, intel via map overlays and SITREP systems</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Player logistics system for lift/shift of objects, towing and more</li>                    
                    <li><i class="fa-li fa fa-caret-right"></i>Advanced persistent map markers and tools with SITREP integration</li>                    
                    <li><i class="fa-li fa fa-caret-right"></i>Ambient urban combat simulation for close quarters battle</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Ambient IED, VBIED and suicide bomber threat</li>                   
                    <li><i class="fa-li fa fa-caret-right"></i>MP player support systems such as admin actions, player tags, view distance control, player gear persistence, multi-respawn, crew info and more</li>
                    <li><i class="fa-li fa fa-caret-right"></i>In-game faction creation tool (ORBATRON) making it very simple to create new factions and groups.</li>          
 				</ul>
            </div>
            <div class="col-md-5 col-md-offset-1">
            <br />
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                        <iframe width="420" height="500" src="//www.youtube.com/embed/mJXdgyTej3I?controls=0" frameborder="0" allowfullscreen></iframe>
                    </div>
                <h2>Server Features</h2>                    
                  <ul class="fa-ul white-list">  
                    <li><i class="fa-li fa fa-caret-right"></i>Complete mission persistence for multi-session operations - without the need for a DB</li>                     
                    <li><i class="fa-li fa fa-caret-right"></i>Web based War Room to track player, group and operations statistics including a web based AAR system</li>
                    <li><i class="fa-li fa fa-caret-right"></i>Web based performance monitoring solution (based on ASM) for Arma 3 dedicated servers.</li>                    
 				</ul>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron dark-panel" id="ALiVEWarRoom">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2><img src="{{ URL::to('/') }}/img/alive_warroom_sm.png" class="img-responsive" /></h2>

                <p>ALiVE mod introduces revolutionary web services integration by streaming Arma 3 in game data to our ALiVE War Room web platform. War Room allows players and groups to review current and past operations as well keep track of individual and group performance statistics.</p>
                <p>War Room offers groups membership to a "virtual" task force operating across the various AO's offered by the Arma 3 engine. War Room exposes task force wins, losses and leaderboards for performance. The platform will provide live streaming capabilities for BLUFOR tracking in a web browser as well as Twitch integration for live helmet cam views and the much awaited ALiVE xStream functionality.</p>
                <p>Beside events, statistics and streaming, War Room provides the platform for persisting Multiplayer Campaigns. This allows groups to run "multi-session operations" by storing game state to a cloud based database. Group admins can update campaign data via the War Room, such as adding map markers, objectives, editing loadouts or adding vehicles and units to the campaign - all via the web platform.</p>
                <p>We believe ALiVE War Room is a revolutionary step in Arma gaming - sign up today!</p>
            </div>
            <div class="col-md-5 col-md-offset-1">
                    <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                        <iframe width="420" height="500" src="//www.youtube.com/embed/StKbdxL6LnA" frameborder="0" allowfullscreen></iframe>
                    </div>
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Join the ALiVE War Room</h3>
                    </div>
                    <div class="panel-body">
                        <a href="{{ URL::to('user/register') }}" class="btn btn-yellow">Sign Up Now!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron white-panel" id="Donate">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h2>Visit our hosts and sponsors!</h2>
                <p><a href="https://armahosts.com"><img src="https://armahosts.com/img/logo.png" alt="ArmaHosts" width="652" height="171" /></a></p>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>
</div>

<div class="jumbotron dark-panel" id="Johari">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>ALiVE Soundtrack</h2>
                <p id="welcome_text">We are pleased to present a collaboration with the band Johari.</p>
                <img src="http://static.wixstatic.com/media/99a3d7_15a6e8f6bb9d4484ada5eb5401668c0a.png_srz_620_544_85_22_0.50_1.20_0.00_png_srz" width="300" height="300" class="img-responsive" />
                <p><b>ALiVE includes the awesome 'This is War' (Metal cover) and 'Everest' by Johari, in game! </b></p>

            </div>
            <div class="col-md-5 col-md-offset-1">
                    <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                        <iframe width="420" height="500" src="//www.youtube.com/embed/Fx70rXFRxfQ?controls=0" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <br />
                    <p><a class="btn btn-yellow btn-med"href="http://www.johariofficial.com"><i class="fa fa-thumbs-o-up"></i> Visit the Official Johari website</a><br/></p>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron white-panel" id="Download">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h2>Download</h2>
                <table class="table">
                    <tr>
                        <th>Version</th>
                        <th>Compatible With</th>
                        <th>Download</th>
                    </tr>
                    <tr class="success">
                        <td>1.12.1.2002131</td>
                        <td>Arma 3 Stable</td>
                        <td><a class="btn btn-primary btn-lg pull-right" href="https://github.com/ALiVEOS/ALiVE.OS/releases"><i class="fa fa-download"></i> Download</a></td>
                    </tr>                   
                </table>
            </div>
             <div class="col-md-1">
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/alive_box.jpg" class="img-responsive" />
            </div>
        </div>
    </div>
</div>

<div class="jumbotron dark-panel" id="Installation">
    <div class="container">
        <div class="row">
            <div class="col-md-7">        
                <h2>Installation</h2>
                <p>Dependencies:  ALiVE Requires <b>CBA_A3</b><br/><br/>
                As with any other mod, extract @ALiVE into Steamapps/Common/ArmA 3 or My Documents/ArmA3.<br/><br/>
                Then either add @ALiVE to your shortcut -mod line, or enable it using the in game Expansions menu or use a launcher like Play with Six or ArmA3Sync.<br/><br/>
                Further instructions for installing mods are in this handy guide on <a target="_blank" href="http://www.armaholic.com/forums.php?m=posts&q=20866]ArmAholic">Armaholic</a>
                </p>  
            </div>    
        </div>
    </div>
</div>

<div class="jumbotron white-panel" id="Media">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Media</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <p>The ALiVE 0.70 release trailer</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/5_UittAx5W8" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>Various deaths, destruction and some vision of the improvements to the multispawn module (with respawn heli insert, among others) the new C2ISTAR module with Player Tasking, SITREP and PATROLREP functions.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/mZraXerMeO8" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-2">
                <p>In this video a peaceful tour of Takistan with the new CAF Aggressors civilian middle east faction, and the almost ready for release civilian module for ALiVE.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/rdGtaFjrF3M" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>In this video the war torn side of Takistan with the new CAF Aggressors civilian middle east faction, and the almost ready for release civilian module for ALiVE.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/5MyMoY8PBOs" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-2">
                <p>The release trailer. Captured entirely using the xStream module.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/88iDovAvk80" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>In the first techdemo video a brief glimpse under the hood of the Advanced Light Infantry Virtual Environment modification.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/jjeD0a7MdoU" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-2">
                <p>ALiVE logistics and reinforcement system test. A hectic 3 faction battle in a small valley on the western side of Altis.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/IjaO8Gm3jHQ" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>The mechanics behind the valley of JPD. In this debug test the Profile System, OPCOM, and an alpha version of the Logistics / Re-enforcement module send waves of units and competing factions against each other.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/xbjVG8IFFLk" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron dark-panel" id="Editors">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Editors</h2>
                <p>The following resources will help you get started with using the ALiVE mod's modules.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>ALiVE User Manual</h4>
                <p>You can access the wiki based user manual <a href="{{ URL::to('wiki/index.php?title=Main_Page') }}">here.</a></p>
            </div>
        </div>
        <div class="row top-margin">
             <div class="col-md-6">
                <h4>Sample Missions</h4>
                 <p>Sample missions have moved <a href="{{ URL::to('missions/') }}">here.</a></p>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>0.8.0 Tutorials</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <p>In this tutorial a look at the new C2ISTAR module</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/tMS6riykGfI" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>This tutorial runs through the improved multispawn module.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/R0NIBfDlyh0" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-6">
                <br/><h4>0.7.0 Tutorials</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <p>In this tutorial a look at the new Player Resupply module in the 0.70 release</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/1AymQUfawP4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>This tutorial runs through the new and improved military logistics module.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/MMwMqpUIr1o" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="row top-margin">
            <div class="col-md-2">
                <p>Tutorial for the new and much requested Custom Military Placement module.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/YKvVOCHS0D4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-2">
                <p>In this tutorial a look at the new player logistics system in the 0.70 release.</p>
            </div>
            <div class="col-md-4">
                <div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">
                    <iframe width="420" height="500" src="//www.youtube.com/embed/2ntP6RLif6U" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="jumbotron white-panel" id="FAQ">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>FAQ's</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <p><i class="fa fa-comment-o"></i> <b>Who's behind this?</b></p>
                <p><i class="fa fa-comment"></i> The guys that spent 2 years developing the Multi-Session Operations persistent mission framework teamed up with other Arma community veterans to bring a new brand of addon to Arma 3. We have currently serving and ex-forces guys as well as long serving Arma modders and clan members. The addon is aimed squarely at the COOP community that want full map, realistic company level operations.</p>
                <hr/>            
                <p><i class="fa fa-comment-o"></i> <b>Is ALiVE for Arma2, Operation Arrowhead or Arma3?</b></p>
                <p><i class="fa fa-comment"></i> It's for Arma 3. We technically could back-port most of it to Arma 2 OA, but we didn't think that supported our fresh start on a shiny new gaming platform.</p>
                <hr/>              
                <p><i class="fa fa-comment-o"></i> <b>Will ALiVE support Headless Client (HC)?</b></p>
                <p><i class="fa fa-comment"></i> Server performance with Profiled Virtual AI is already sufficient, so there's been no driver to use HC yet. However, we will provide an option in Profile System to spawn on HC in the first release!</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>ALIVE Object Oriented SQF - what is this wizardry, something you guys designed in-house?</b></p>
                <p><i class="fa fa-comment"></i> Wolffy first wrote OOSQF about in Sept 2012 on the MSO Developers Blog. Its a bit clunky - yes,. But it serves its purpose in the way we code cleaner and more reliably. And puts us in good stead when/if BIS release Java support for ARMA.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Will it be possible to set up the AI skill with your stand-alone admin actions?</b></p>
                <p><i class="fa fa-comment"></i> Yes, we have a module that allows you to customise AI skill for the mission.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>How's the performance in multiplayer when there are players mixed at two or more different objective areas on the map? Does it noticeably take a steep dive in performance?</b></p>
                <p><i class="fa fa-comment"></i> We are working on a limiter to the profile system, where the mission maker can set a hard top limit on the amount of 'active' profiles that can be in play at any one time. This will of course require testing on your hardware, and configured for the types of ops you are looking to support. I think it will be a reasonable compromise, as we all know Arma can only run X number of AI for any given system with Y number of players.</p>
            </div>
            <div class="col-md-4">
                <p><i class="fa fa-comment-o"></i> <b>How do I get access to the test version?</b></p>
                <p><i class="fa fa-comment"></i> We will not be extending the closed testing of ALiVE to any other groups at this stage. However, we will be releasing a public alpha for you all to test in due course.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Do you have any general performance concerns?</b></p>
                <p><i class="fa fa-comment"></i> Profiling or not, there is clearly a limit to what servers can handle with 'Visual' AI - lots of players in lots of locations across the map spawning lots of AI into the Visual World will of course have an impact and BIS is the only one that can do anything about that. However, note that ALiVE Placement (the system that analyses maps for objectives and automatically places AI groups) is completely customisable - for example, you can limit it to only a Platoon strength group and set a small Tactical Area of Responsibility (TAOR). Then place one for the other faction and watch them fight it out. Synch a Logistics module with unlimited supplies too if you want a perpetual micro battle. This would take about 5 minutes in the editor.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Can it be used on all maps?</b></p>
                <p><i class="fa fa-comment"></i> Our military placement and objectives modules, automatically scan maps for military, civilian infrastructure etc. We have it already working for a number of BIS and community made maps.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Also are you guys wizards by any chance?</b></p>
                <p><i class="fa fa-comment"></i> No</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Can I create my own pbo that changes the way ALiVE works, such as replacing functions or features to work with my own addons?</b></p>
                <p><i class="fa fa-comment"></i> No, users and the community do not have permission to edit, change, replace ALiVE content through the use of new addons. Clearly any changes required with ALiVE should be submitted to the dev team for inclusion in the official ALiVE build for all to enjoy.</p>
            </div>
            <div class="col-md-4">
                <p><i class="fa fa-comment-o"></i> <b>When will you be releasing ALiVE?</b></p>
                <p><i class="fa fa-comment"></i> We are following the well-known release schedule used by all addon makers in the community, and will release it "when it's ready" which will be "soon™"</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Will you have "Patrol Ops" like side missions or tasks?</b></p>
                <p><i class="fa fa-comment"></i> Yes, we will be including an optional Player Task Generator for those who want more directed content within the ALiVEsandbox. This is in early stages of development but the intent is for OPCOM to provide relevant but specialist tasks for the player that will enhance the overall war effort, rather than just random side missions.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Is it customizable so you can choose the enemies that spawn?</b></p>
                <p><i class="fa fa-comment"></i> Yes, you can choose the enemy force by faction and size (battalion, company, & platoon). The modules are configurable in the editor, so you can choose sides or factions, custom ones too. You can also select things such as size of the enemy force and posture. Combining modules and settings you can create dynamic and credible invasions or insurgency campaigns. For those of you familiar with MSO we will be introducing modules such as Terror Cells, IED/Suicide Bomber, Roadblocks, AAA sites etc.</p>
                <hr/>
                <p><i class="fa fa-comment-o"></i> <b>Can I depbo, edit and update any of the ALiVE modules for my own use?</b></p>
                <p><i class="fa fa-comment"></i> No, users do not have permission to edit and distribute ALiVE - as stated by the license. Users and the community are encouraged to submit code updates/fixes/new features to the development team for inclusion in the ALiVE addon.</p>
            </div>
        </div>
    </div>
</div>
<!--
<div class="jumbotron white-panel" id="Helpout">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h2>Help out</h2>
            </div>
            <div class="col-md-5">
                <a class="coinbase-button" data-code="d8ebf42741de6073065e8c00ceaf6223" data-button-style="donation_large" href="https://coinbase.com/checkouts/d8ebf42741de6073065e8c00ceaf6223">Donate Bitcoins</a><script src="https://coinbase.com/assets/button.js" type="text/javascript"></script>
            </div>
        </div>
    </div>
</div>
-->
@stop

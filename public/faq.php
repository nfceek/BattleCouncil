<?php
$pageTitle = "FAQ";
$pageClass = "page-static";

// 🔥 KEY FLAGS
$requiresAuth = false;
$loadAppJS    = false;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container bc-page">
    <div class="bc-row pad-row">
        <div class="bc-col-12">
            <div class="bc-card pad-card">
                <div class="bc-card-header" style="padding:8px;">
					<h1>FAQ and Info Page</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="bc-row pad-row">
        <div class="bc-col-12">
            <div class="bc-card pad-card">     
                <div class="bc-card-info-body">
                    <p style="text-align: left;">
                        <div class="col-md-2"></div>				
                    </div>
                        <!-- QA 1 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                <!--A: Nothing, it's just a site that I've had for LOOONG time waiting for the right project.--> 
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        <!-- QA 2 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: 1?</strong>
                        </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: thats complicated
                                <div class="faq-list">
                                    <ul>
                                        <li>1)</li>
                                        <li>2)
                                            <ul>
                                                <li>a)</li>
                                                <li>b)</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>				
                        </div>		
                        <!-- QA 4 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: 2</strong>
                        </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: Yes &amp; No, 
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        <!-- QA 5 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: 3</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: ask the Ogre in the bar
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        <!-- QA 6 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: </strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: Good for you. Keep it up. We hate them.
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        <!-- QA 7 -->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Is it free?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: The short answer is NO. 
                                <br />We are a SAAS cloud based system built to leverage fan data that you can't get anywhere else. 
                                We offer a number of very user friendly tools to make Total Battle a more enjoyable experience. And for that you will pay.
                        </div>
                        <!--
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        QA 8 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: What cool Premium Extras can I pay for?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: We are still getting ideas together
                            </div>				
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                         QA 9 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: What can I post?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: Standard rules apply. Most crap is fair game, no slander, racial slurs and the like.
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>
                         QA 11 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Can I write to your API?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: Seriously, we are an app using the Twitter API. We don't have our own API.
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>
                        QA 12 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Are you a website or an application?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: YES.<br />We are a website that behaves like an app. We have optimized the site to be responsive to different devices. But, that 
                                doesn't mean that it will always look perfect on every device.<br />
                                Using our app on a smartphone is kinda difficult because you can't easily cut and paste. And, there isn't any touch screen ability 
                                for a smarphone screen (again, this is a beta we are working on it). So, your mileage may vary by device.
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                         QA 13 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Where is your mobile application?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: There isn't one. The  system is built for business and high volume sellers. Some things still require a 
                                desktop system for best results.<br />
                                Building a listing takes only a few minutes and input it is not optimized for a mobile device.
                                <br />Use a full computer system to build a listing, use a mobile device to view it in your Twitter account timeline.
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>				
                         QA 14 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Can I post 1 item or many?</strong>
                            </div>
                        </div>				
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: YES.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>
                       QA 15 
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>		
                            <div class="col-md-8" style="text-align:left">	
                                <strong>Q: Can I schedule a Post?</strong>
                            </div>				
                        </div>
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8" style="text-align:left">	
                                A: YES.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>				
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                        </div>			
                        -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
$pageTitle = "About";
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
                    <h2>About BattleCouncil</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="bc-row pad-row">
        <div class="bc-col-12">
            <div class="bc-card pad-card">   
                <div class="bc-card-info-body">
                    <p style="text-align: left;">
                        BattleCouncil is a fan-built companion platform designed to enhance the experience for players of the online MMOG game: Total Battle. 

                        <br /><br />

                        It serves as a centralized hub for strategy, tools, and community-driven insights that help players make better decisions in-game. Whether you're optimizing attacks, organizing your clan, or just trying to understand mechanics more clearly, the goal is simple: give players a sharper edge with practical, usable tools.
                        At its core, the project focuses on translating complex game mechanics into something accessible and actionable. 

                        <br /><br />

                        Many systems in *Total Battle* are layered and not always transparent, which creates friction for players trying to improve. BattleCouncil bridges that gap by turning trial-and-error gameplay into informed strategy—reducing guesswork and helping players move faster with confidence.
                        A major component of the platform is its growing suite of calculators. 

                        <br /><br />

                        These include tools for monster hunting optimization, layered combat planning, and specialized utilities like the Citadel Cracker calculator. Each calculator is designed to go beyond basic stat math and instead reflect real in-game scenarios, allowing players to simulate outcomes and refine their approach before committing resources.

                        <br /><br />

                        In addition to calculators, the platform includes an evolving world mapping system. Players can view and track kingdoms, clans, and key locations using coordinate-based mapping. This helps visualize territory, identify patterns, and better understand the broader game environment. The clan mapping feature also allows players to document leadership, language, rules of engagement, and positioning—turning scattered information into a structured, shared resource.
                        BattleCouncil also introduces interactive elements like the Tavern and Talking Heads system. This feature adds personality and engagement to the platform by allowing users to generate spoken dialogue through characters, blending utility with entertainment. While still evolving, it opens the door for community-driven content, shared insights, and a more immersive way to communicate strategies or ideas.

                        <br /><br />

                        The long-term vision of the project is to expand into a collaborative knowledge base where players can contribute tips, strategies, and observations. Features like message boards, voting systems, and shared data inputs are planned to help surface the most useful information. The platform is being built with flexibility in mind, allowing it to grow alongside the needs of the player community.
                        All images and visual assets originating from the *Total Battle* game remain the property of their respective copyright holders. BattleCouncil does not use proprietary assets to create or deliver its core functionality, and any references are used solely for contextual or illustrative purposes.

                        <br /><br />

                        BattleCouncil is an independent fan project and is not affiliated with, endorsed by, or connected to *Total Battle* or its developer, Scorewarrior.
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
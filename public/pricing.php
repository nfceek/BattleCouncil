<?php
$pageTitle = "BattleCouncil Pricing";
$pageCss = "pricing";
include '../includes/header.php';
?>
    <section class="battlecouncil-intro">

    <div class="intro-inner">
        <h1>Command Smarter Battles</h1>
        <p>
        Stop guessing outcomes. Build precision-driven attacks using real matchup logic, creature synergy, and survivability modeling.
        Every decision becomes calculated — not improvised.

        From monster hunts to formation testing and scaling kingdom efficiency, this is your strategic layer above the game itself.
        </p>

        <ul>
        <li>Creature vs monster optimization with real impact weighting</li>
        <li>Attack formation builder for structured deployment planning</li>
        <li>Loss prediction + efficiency calculations before engagement</li>
        <li>Expanding toolkit for scalable battle strategy systems</li>
        </ul>

        <div class="intro-cta">
        <span>Choose your rank below to unlock system access</span>
        </div>

    </div>

    </section>
    <section class="pricing-wrap">

    <div class="pricing-header">
        <h2>Choose Your Rank</h2>
        <p>Power is layered. Access determines outcome.</p>
    </div>

    <div class="pricing-grid">

        <!-- SOLDIERS -->
        <div class="pricing-card soldier">
        <h3>Soldiers</h3>
        <div class="price">Free</div>
        <p class="tagline">Scout the battlefield</p>

        <ul>
            <li>Drive-by access</li>
            <li>Basic calculations</li>
            <li>Limited visibility</li>
            <li>No saved data</li>
        </ul>

        <button>Join as Soldier</button>
        </div>

        <!-- OFFICER -->
        <div class="pricing-card officer featured">
        <div class="badge">Most Popular</div>

        <h3>Officer</h3>
        <div class="price">$5.99<span>/mo</span></div>
        <p class="tagline">Command small-scale strategy</p>

        <ul>
            <li>Advanced calculations</li>
            <li>Kingdom data access</li>
            <li>Monster research tools</li>
            <li>Bounty system access</li>
            <li>Clan reward bonuses</li>
        </ul>

        <button>Become Officer</button>
        </div>

        <!-- SUPERIOR -->
        <div class="pricing-card superior">
        <h3>Superior</h3>
        <div class="price">$11.99<span>/mo</span></div>
        <p class="tagline">Full command authority</p>

        <ul>
            <li>All calculation systems</li>
            <li>Full kingdom + clan data</li>
            <li>Saved user data + autofill</li>
            <li>Enhanced bounty rewards</li>
            <li>Priority reward scaling</li>
        </ul>

        <button>Ascend to Superior</button>
        </div>

    </div>
    </section>

    </body>
<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/../includes/footer.php';        
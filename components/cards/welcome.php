
        <style>



        /* Layout Wrapper */
        .bc-container {
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        /* Single Card Wrapper */
        .bc-grid {
            width: 100%;
            max-width: 800px;   /* ✅ half the previous max width */
        }

        /* Card */
        .bc-card {
            width: 100%;
            background: #1b2130;
            border-radius: 18px;
            overflow: hidden;
            color: #fff;
            box-shadow: 0 8px 28px rgba(0,0,0,.6);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .bc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 34px rgba(0,0,0,.75);
        }

        /* Video */
        .bc-img video {
            width: 100%;
            height: 280px;      /* ✅ taller video */
            object-fit: cover;
        }

        /* Content */
        .bc-content {
            padding: 20px 24px;
        }

        .bc-content h2 {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 1.2;
        }

        .bc-content p {
            font-size: 15px;
            line-height: 1.6;
            color: #cfcfcf;
            margin-bottom: 12px;
        }

        .bc-content ul {
            margin-top: 12px;
            padding-left: 18px;
            font-size: 14px;
            color: #aaa;
        }

        /* Tablet */
        @media (min-width: 768px) {
            .bc-img video {
                height: 340px;
            }
            .bc-content h2 {
                font-size: 28px;
            }
            .bc-content p {
                font-size: 16px;
            }
        }
        /* Desktop */
        @media (min-width: 1024px) {
            .bc-grid {
                max-width: 800px;   /* maintain half width */
            }
            .bc-img video {
                height: 420px;
            }
            .bc-content h2 {
                font-size: 32px;
            }
            .bc-content p {
                font-size: 17px;
            }
        }
        </style>


                    <video src="/images/trent/Trent_the_Elder_generated.mp4" 
                    alt="Battle Council Video"
                    controls
                    autoplay
                    muted
                    loop
                    style="border-radius:8px;">
                    Your browser does not support the video tag.
                </video>
            </div>

            <div class="bc-content">
                <h2>Command the Hunt. Control the Outcome.</h2>

                <p>
                    Battle Council is built for players who don’t guess — they calculate. 
                    Every hunt, every squad, every creature choice matters. This is where you turn scattered data into clean, repeatable wins.
                </p>

                <p>
                    Plan smarter attacks using real matchup logic, creature bonuses, and survivability math. 
                    Instead of over-sending or guessing losses, you’ll know exactly what to deploy — and why it works.
                </p>

                <p>
                    Whether you’re optimizing monster hunts, testing formations, or scaling your efficiency, 
                    this is your command layer above the game.
                </p>

                <ul>
                    <li>Creature vs monster optimization</li>
                    <li>Attack formation builder</li>
                    <li>Loss + efficiency calculations</li>
                    <li>Scalable strategy tools (in progress)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

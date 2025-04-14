<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop4Life🩸
    </title>
    <link rel="stylesheet" href="./homepage/styles.css">
    <link rel="stylesheet" href="./login/alertstyle.css">

</head>

<body>
    <!-- <BODy></BODy> -->
    <header>
        <div class="logo-container">

            <h1>Drop4Life🩸</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#importance">Importance</a></li>
                <li><a href="#donate">Donate</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section id="hero">
        <div class="hero-content">
            <h2>Save Lives, Donate Blood</h2>
            <p>Your blood donation can make a significant impact on lives. Discover the importance of donating blood and how you can help.</p>
            <a href="#donate" class="btn">Learn More</a>
        </div>
        <img src="media/hero-image.png" alt="Blood Donation" class="hero-image">
    </section>

    <!-- ABOURT -->
    <section id="about">
        <h2>About Us</h2>
        <p>Drop4Life is a non-profit organization dedicated to spreading awareness about the critical need for blood donation. Our mission is to ensure that everyone understands the importance of donating blood and how it can save lives.</p>
        <div class="about-images">
            <img src="https://bjsindia.org/Blood-Donation-camp/images/gallery6.jpg" alt="About Us 1" class="about-image">
            <img src="https://bjsindia.org/Blood-Donation-camp/images/gallery7.jpg" alt="About Us 2" class="about-image">
            <img src="https://bjsindia.org/Blood-Donation-camp/images/gallery8.jpg" alt="About Us 3" class="about-image">
        </div>
    </section>



    <!-- 
IMPORTANCE -->


    <section id="importance">
        <h2>Why Blood Donation Matters</h2>
        <div class="info-container">
            <div class="info-box">
                <img src="media/emergency-icon.png" alt="Emergency" class="info-icon">
                <h3>Emergency Situations</h3>
                <p>During emergencies, such as accidents or natural disasters, the need for blood is crucial. Your donation can be a lifesaver in critical situations.</p>
            </div>
            <div class="info-box">
                <img src="media/chronic-icon.png" alt="Chronic Conditions" class="info-icon">
                <h3>Chronic Conditions</h3>
                <p>Patients with chronic conditions like anemia or cancer often require regular blood transfusions. Your donation helps them live better lives.</p>
            </div>
            <div class="info-box">
                <img src="media/surgery_icon.png" alt="Surgery" class="info-icon">
                <h3>Surgery Support</h3>
                <p>Blood transfusions are often necessary during surgeries, both major and minor. Donating blood ensures that hospitals have the resources they need.</p>
            </div>
        </div>
    </section>
    <!-- DONATE -->
    <section id="donate">
        <h2>How to Donate</h2>
        <p>Donating blood is a simple process that can make a huge difference. Find out the steps to donate and locate a blood donation center near you.</p>
        <div class="donate-images">

            <div class="card">
                <div class="img-content">
                    <img src="media/Medical-Checkup.png" alt="Step 2" class="donate-image">
                </div>
                <div class="content">
                    <p class="heading">Step 1</p>
                    <p>Undergo a brief medical screening to ensure you are fit to donate.</p>
                </div>
            </div>

            <div class="card">
                <div class="img-content">
                    <img src="media/step_first.jpg" alt="Step 1" class="donate-image">
                </div>
                <div class="content">
                    <p class="heading">Step 2</p>
                    <p>Register at the donation center and fill out the necessary forms.</p>
                </div>
            </div>

            <div class="card">
                <div class="img-content">
                    <img src="media/blood-donation.jpg" alt="Step 3" class="donate-image">
                </div>
                <div class="content">
                    <p class="heading">Step 3</p>
                    <p>Donate blood, which usually takes around 10 minutes.</p>
                </div>
            </div>
        </div>
        <div class="donate-btn">
            <a href="./login/" class="btn">Donate Now</a>
        </div>
    </section>

    <!-- CONTACT -->

    <section id="contact">
        <h2>Contact Us</h2>
        <p>If you have any questions or need more information, feel free to reach out to us. We're here to help you with any inquiries you may have.</p>
        <ul class="contact-info">
            <li><img src="media/email.png" alt="Email Icon"><a href="mailto:info@drop4life.org">info@drop4life.org |</a></li>
            <li><img src="media/phone.png" alt="Phone Icon"><a href="tel:+123456789">+1 234 567 89 |</a></li>
            <li><img src="media/address.png" alt="Address Icon">123 Blood Drive Lane, Cityville, ST 12345</li>
        </ul>

        <form class="contact-form" action="./homepage/submit_form.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit" class="btn">Send Message</button>
        </form>
    </section>
    <!-- ALERT -->

    <div id="alert-container">
        <?php if (isset($_GET['type']) && isset($_GET['message'])) : ?>
            <div class="info <?php echo $_GET['type']; ?>" id="infoBox">
                <div class="info__icon">
                    <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1.5c-5.79844 0-10.5 4.70156-10.5 10.5 0 5.7984 4.70156 10.5 10.5 10.5 5.7984 0 10.5-4.7016 10.5-10.5 0-5.79844-4.7016-10.5-10.5-10.5zm.75 15.5625c0 .1031-.0844.1875-.1875.1875h-1.125c-.1031 0-.1875-.0844-.1875-.1875v-6.375c0-.1031.0844-.1875.1875-.1875h1.125c.1031 0 .1875.0844.1875.1875zm-.75-8.0625c-.2944-.00601-.5747-.12718-.7808-.3375-.206-.21032-.3215-.49305-.3215-.7875s.1155-.57718.3215-.7875c.2061-.21032.4864-.33149.7808-.3375.2944.00601.5747.12718.7808.3375.206.21032.3215.49305.3215.7875s-.1155.57718-.3215.7875c-.2061.21032-.4864.33149-.7808.3375z" fill="#393a37"></path>
                    </svg>
                </div>
                <div class="info__title">
                    <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
                </div>
                <div class="info__close" onclick="closeInfoBox()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" height="20">
                        <path fill="#393a37" d="M15.8333 5.34166l-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z"></path>
                    </svg>
                </div>
            </div>
            <script>
                function closeInfoBox() {
                    var infoBox = document.getElementById('infoBox');
                    infoBox.style.opacity = '0';
                    setTimeout(function() {
                        infoBox.style.display = 'none';
                    }, 500); // Match the transition time
                }

                window.onload = function() {
                    var infoBox = document.getElementById('infoBox');
                    if (infoBox) {
                        infoBox.style.opacity = '1';
                        setTimeout(closeInfoBox, 2000); // Show for 2 seconds
                    }
                };
            </script>
        <?php endif; ?>

    </div>
    <!-- FOOTER -->
    <footer>
        <p>&copy; 2025 Drop4Life. All rights reserved.</p>
        <ul class="social-media">
            <li><a href="#"><img src="media/facebookicon_icon.png" alt="Facebook"></a></li>
            <li><a href="#"><img src="media/twitter_icon.png" alt="Twitter"></a></li>
            <li><a href="#"><img src="media/instagram_icon.png" alt="Instagram"></a></li>
        </ul>
    </footer>

    <script src="./homepage/scripts.js"></script>

</body>

</html>
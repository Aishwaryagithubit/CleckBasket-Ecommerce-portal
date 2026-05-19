<?php /* Contact Us Page */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - CleckBasket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/contactus.css" />
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="contact-page">

        <!-- ===== PAGE HEADER ===== -->
        <div class="contact-page-header">
            <h1>CONTACT US</h1>
            <p>get in touch with us for any inquires or visit our central pickup point.</p>
        </div>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="contact-main">

            <!-- LEFT: FORM CARD -->
            <div class="contact-form-card">
                <h2 class="form-card-title">Send us a message</h2>

                <div id="contactAlert" style="display:none;margin-bottom:18px;padding:14px 18px;border-radius:12px;font-size:15px;font-weight:500;"></div>

                <form class="contact-form" id="contactForm" action="#" method="POST">

                    <div class="form-field">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" placeholder="Julianne" />
                    </div>
                    <div class="form-field">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" placeholder="Moore" />
                    </div>

                    <div class="form-field">
                        <label for="emailAddress">Email Address</label>
                        <input type="email" id="emailAddress" name="email" placeholder="julianne@example.com" />
                    </div>
                    <div class="form-field">
                        <label for="contact-number">Contact Number</label>
                        <input type="tel" id="contact-number" name="contact_number" placeholder="+977 (01) 4455-667" />
                    </div>

                    <div class="form-field">
                        <label for="subject">Subject</label>
                        <div class="select-wrap">
                            <select id="subject" name="subject">
                                <option value="general">General Inquiry</option>
                                <option value="order">Order Issue</option>
                                <option value="partnership">Partnership</option>
                                <option value="feedback">Feedback</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="How can our curators help you today?"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Submit Message</button>

                </form>
            </div>

            <!-- RIGHT: VISUAL / MAP PANEL -->
            <div class="contact-visual-panel">

                <div class="roots-badge">
                    <span>Our Roots</span>
                </div>

                <h2 class="pickup-heading">Central Pickup Point</h2>

                <p class="pickup-description">Visit our flagship garden curator space for order pickups and fresh farm-to-table workshops.</p>

                <!-- Map -->
               <div class="map-card">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9479.123456789!2d-1.7167!3d53.7253!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487be0b1234567%3A0xabcdef!2sCleckheaton%2C+West+Yorkshire%2C+UK!5e0!3m2!1sen!2snp!4v1234567890"
        width="100%"
        height="100%"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="CleckBasket Pickup Location">
    </iframe>
</div>

                <!-- Details Grid -->
                <div class="details-grid">
                    <div class="detail-block">
                        <div class="detail-label">Address</div>
                        <div class="detail-value">
    Cleckheaton Town Centre,<br>
    West Yorkshire, BD19, UK
</div>
                    </div>
                    <div class="detail-block">
                        <div class="detail-label">Connect</div>
                        <div class="detail-value">
                            +977 (01) 4455-667<br>
                            hello@cleckbasket.com
                        </div>
                    </div>
                </div>

                <!-- Pickup Hours -->
                <div class="pickup-hours">
                    <div class="pickup-hours-label">Pickup Hours</div>
                    <div class="hours-row">
                        <span class="hours-day">Mon — Fri</span>
                        <span class="hours-time">09:00 AM – 06:00 PM</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-day">Sat — Sun</span>
                        <span class="hours-time">10:00 AM – 04:00 PM</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <?php include '../footer.php'; ?>

    <script>
    document.getElementById('contactForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form    = this;
        const alert   = document.getElementById('contactAlert');
        const btn     = form.querySelector('.btn-submit');
        const data    = {
            first_name:     form.querySelector('[name="first_name"]').value.trim(),
            last_name:      form.querySelector('[name="last_name"]').value.trim(),
            email:          form.querySelector('[name="email"]').value.trim(),
            contact_number: form.querySelector('[name="contact_number"]').value.trim(),
            subject:        form.querySelector('[name="subject"]').value,
            message:        form.querySelector('[name="message"]').value.trim(),
        };

        btn.disabled    = true;
        btn.textContent = 'Sending…';
        alert.style.display = 'none';

        try {
            const res  = await fetch('/cleckbasket/backend/submit_contact.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(data),
            });
            const json = await res.json();

            alert.textContent   = json.message;
            alert.style.display = 'block';
            if (json.success) {
                alert.style.background = '#e8f5e9';
                alert.style.color      = '#2e7d32';
                form.reset();
            } else {
                alert.style.background = '#ffebee';
                alert.style.color      = '#c62828';
            }
        } catch (err) {
            alert.textContent      = 'Network error. Please try again.';
            alert.style.display    = 'block';
            alert.style.background = '#ffebee';
            alert.style.color      = '#c62828';
        } finally {
            btn.disabled    = false;
            btn.textContent = 'Submit Message';
        }
    });
    </script>
</body>
</html>

<?php
ob_start();
?>

<!-- Page Header (Plain H1 + One Line) -->
<section class="hero section-pad pb-4">
    <div class="container">
        <h1>Contact Admissions &amp; Corporate Desk</h1>
        <hr class="gold-rule">
        <p class="lead mb-0">Have questions regarding candidate onboarding, upcoming training batches, or corporate sales hiring? Our admissions team in Gurgaon is here to assist.</p>
    </div>
</section>

<!-- Contact Form & Details (2 columns on desktop) -->
<section class="section-pad pt-2">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column: Contact Form -->
            <div class="col-lg-7">
                <div class="p-4 p-md-5 bg-white border" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important; overflow: hidden;">
                    <h2 class="h3 mb-2">Send a Message</h2>
                    <hr class="gold-rule">
                    <p class="small text-muted mb-4">Fill out the form below and an admissions counselor will reach out within 2 business hours.</p>

                    <form action="<?= url('/contact/send') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                        <div class="mb-3">
                            <label class="form-label" for="contact_name">Full Name *</label>
                            <input type="text" id="contact_name" name="name" class="form-control" placeholder="e.g. Kunal Sharma" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="contact_email">Email Address *</label>
                                <input type="email" id="contact_email" name="email" class="form-control" placeholder="kunal@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contact_phone">Mobile Number *</label>
                                <input type="tel" id="contact_phone" name="phone" class="form-control" placeholder="9876543210" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="contact_subject">Interest Area</label>
                            <select id="contact_subject" name="subject" class="form-select">
                                <option value="Candidate Training & Placement">Candidate Training &amp; Placement</option>
                                <option value="Corporate Sales Hiring">Corporate Real Estate Hiring</option>
                                <option value="Channel Partner Alliance">Channel Partner Alliance</option>
                                <option value="General Inquiry">General Inquiry</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="contact_message">Your Message *</label>
                            <textarea id="contact_message" name="message" class="form-control" rows="4" placeholder="How can we help you?" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Office Address & Contacts -->
            <div class="col-lg-5">
                <div class="p-4 p-md-5 bg-white border mb-4" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important; overflow: hidden;">
                    <h2 class="h3 mb-2">Headquarters &amp; Training Campus</h2>
                    <hr class="gold-rule">

                    <div class="mb-4 pb-3 border-bottom" style="border-color: var(--line) !important;">
                        <div class="small fw-bold text-dark mb-1">Campus Address</div>
                        <div class="small text-muted">
                            Golf Course Extension Road, Sector 65, Gurgaon, Haryana 122001, India
                        </div>
                    </div>

                    <div class="mb-4 pb-3 border-bottom" style="border-color: var(--line) !important;">
                        <div class="small fw-bold text-dark mb-1">Phone Helpline</div>
                        <div class="small text-muted">
                            +91 9999 888 777 / +91 124 456 7890
                        </div>
                    </div>

                    <div class="mb-4 pb-3 border-bottom" style="border-color: var(--line) !important;">
                        <div class="small fw-bold text-dark mb-1">Email Contacts</div>
                        <div class="small text-muted">
                            Admissions: admissions@realtysmartzpathshala.in<br>
                            HR Desk: hr@realtysmartzpathshala.in
                        </div>
                    </div>

                    <div>
                        <div class="small fw-bold text-dark mb-1">Office Hours</div>
                        <div class="small text-muted">
                            Monday &ndash; Saturday: 9:30 AM &ndash; 6:30 PM (Sunday Closed)
                        </div>
                    </div>
                </div>

                <!-- Clean Map Block -->
                <div class="border overflow-hidden bg-white" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important; height: 200px;">
                    <iframe
                        title="Realty Smartz Pathshala Campus Location"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14041.564757134375!2d77.0594!3d28.4069!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d228f092787d7%3A0xb5b79e274a2aa2c!2sGolf%20Course%20Ext%20Rd%2C%20Sector%2065%2C%20Gurugram%2C%20Haryana!5e0!3m2!1sen!2sin!4v1700000000000">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "Contact Us | Realty Smartz Pathshala";
$pageDescription = "Connect with Realty Smartz Pathshala admissions and corporate hiring teams in Gurgaon.";
require APP_PATH . '/views/layouts/main.php';
?>

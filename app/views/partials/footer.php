<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Col 1: Logo + one line -->
            <div class="col-lg-3 col-md-6">
                <a href="<?= url('/') ?>" class="d-inline-block mb-3">
                    <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" height="40" width="auto">
                </a>
                <p class="small text-muted mb-0">
                    Empowering businesses with skilled professionals — we build high-performing sales teams.
                </p>
            </div>

            <!-- Col 2: Pages -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-col-title">Pages</div>
                <ul class="footer-links">
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/projects') ?>">Projects</a></li>
                    <li><a href="<?= url('/about') ?>">About</a></li>
                    <li><a href="<?= url('/blog') ?>">Blog</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact</a></li>
                    <li><a href="<?= url('/register') ?>">Candidate Onboarding</a></li>
                </ul>
            </div>

            <!-- Col 3: Social Media with Original Brand Icons -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-col-title">Social Media</div>
                <ul class="footer-links footer-social-list">
                    <li>
                        <a href="https://instagram.com/realtysmartz" target="_blank" rel="noopener" class="social-footer-link" aria-label="Follow Realty Smartz on Instagram">
                            <span class="social-icon-badge" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="igBrandGrad" x1="0%" y1="100%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#f09433"/>
                                            <stop offset="25%" stop-color="#e6683c"/>
                                            <stop offset="50%" stop-color="#dc2743"/>
                                            <stop offset="75%" stop-color="#cc2366"/>
                                            <stop offset="100%" stop-color="#bc1888"/>
                                        </linearGradient>
                                    </defs>
                                    <rect width="24" height="24" rx="6" fill="url(#igBrandGrad)"/>
                                    <rect x="5.5" y="5.5" width="13" height="13" rx="3.5" stroke="#FFFFFF" stroke-width="1.6" fill="none"/>
                                    <circle cx="12" cy="12" r="3.2" stroke="#FFFFFF" stroke-width="1.6" fill="none"/>
                                    <circle cx="15.8" cy="8.2" r="0.9" fill="#FFFFFF"/>
                                </svg>
                            </span>
                            <span class="social-link-label">Instagram <span class="social-handle">(@realtysmartz)</span></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://linkedin.com/company/realtysmartz" target="_blank" rel="noopener" class="social-footer-link" aria-label="Connect with Realty Smartz on LinkedIn">
                            <span class="social-icon-badge" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="5" fill="#0A66C2"/>
                                    <path d="M7 9.5H4.8V17H7V9.5ZM5.9 6.2C5.1 6.2 4.5 6.8 4.5 7.5C4.5 8.2 5.1 8.8 5.9 8.8C6.7 8.8 7.3 8.2 7.3 7.5C7.3 6.8 6.7 6.2 5.9 6.2ZM17.5 12.8C17.5 10.6 16.3 9.5 14.8 9.5C13.6 9.5 13 10.2 12.7 10.7V9.5H10.5V17H12.7V13.3C12.7 12.3 12.9 11.4 14.1 11.4C15.3 11.4 15.3 12.4 15.3 13.4V17H17.5V12.8Z" fill="#FFFFFF"/>
                                </svg>
                            </span>
                            <span class="social-link-label">LinkedIn <span class="social-handle">(Realty Smartz)</span></span>
                        </a>
                    </li>
                    <li>
                        <a href="https://youtube.com/@realtysmartz" target="_blank" rel="noopener" class="social-footer-link" aria-label="Subscribe to Realty Smartz on YouTube">
                            <span class="social-icon-badge" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="5" fill="#FF0000"/>
                                    <polygon points="10,8.2 15.8,12 10,15.8" fill="#FFFFFF"/>
                                </svg>
                            </span>
                            <span class="social-link-label">YouTube <span class="social-handle">(@realtysmartz)</span></span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Contact -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-col-title">Contact</div>
                <p class="small text-muted mb-2">
                    Spaze Plazo, Sector 69, Gurugram, Haryana 122018
                </p>
                <p class="small text-muted mb-2">
                    info@realtysmartzpathshala.in
                </p>
                <p class="small text-muted mb-0">
                    +91 9999 888 777
                </p>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <div>
                &copy; <?= date('Y') ?> Realty Smartz Pathshala. All rights reserved.
            </div>
            <div>
                <a href="<?= url('/contact') ?>" class="text-muted text-decoration-none small">Support &amp; Grievance</a>
            </div>
        </div>
    </div>
</footer>

<style>
.site-footer {
    border-top: 1px solid var(--line);
    padding: 60px 0 32px;
    background: var(--surface);
    font-family: var(--font-family);
}
.footer-col-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text);
    margin-bottom: 18px;
}
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.footer-links a {
    color: var(--muted);
    text-decoration: none;
    font-size: 14px;
    transition: color 0.15s ease, transform 0.15s ease;
    display: inline-block;
}
.footer-links a:hover {
    color: var(--brand);
}
.footer-social-list {
    gap: 14px;
}
.social-footer-link {
    display: inline-flex !important;
    align-items: center;
    gap: 12px;
    color: var(--text) !important;
    font-weight: 500;
}
.social-footer-link:hover {
    color: var(--brand) !important;
    transform: translateX(3px);
}
.social-icon-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    flex-shrink: 0;
    transition: transform 0.2s ease, filter 0.2s ease;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.social-footer-link:hover .social-icon-badge {
    transform: scale(1.1);
    filter: brightness(1.05);
}
.social-link-label {
    font-size: 14px;
    color: var(--text);
    line-height: 1.3;
}
.social-handle {
    color: var(--muted);
    font-size: 13px;
}
.social-footer-link:hover .social-handle {
    color: var(--brand);
}
.footer-bottom {
    border-top: 1px solid var(--line);
    margin-top: 48px;
    padding-top: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 13px;
    color: var(--muted);
}
</style>

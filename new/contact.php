<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Templates.uz — Create Your Website Fast and Easy!</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="./src/css/variables.css" />
    <link rel="stylesheet" href="./src/css/header.css" />
    <link rel="stylesheet" href="./src/css/contact.css" />
    <link rel="stylesheet" href="./src/css/footer.css">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="./">
                <i class="bi bi-layers"></i> Templates.uz
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="./templates.php">Templates</a></li>
                    <li class="nav-item"><a class="nav-link" href="./pricing.php">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="./services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="./contact.php">Contact</a></li>

                    <li class="nav-item dropdown" id="languageDropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown" id="languageToggle">
                            <img src="./src/images/flags/gb.svg" alt="English" class="flag-icon"
                                style="width: 20px; border-radius: 3px; margin-right: 6px;" />
                            <span>Language</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageToggle">
                            <li>
                                <a class="dropdown-item" href="?lang=en" data-lang="en" data-name="English"
                                    data-flag="./src/images/flags/gb.svg">
                                    <img src="./src/images/flags/gb.svg" alt="English Flag" class="flag-icon"
                                        style="width: 20px; margin-right: 6px;" />
                                    English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="?lang=ru" data-lang="ru" data-name="Russian"
                                    data-flag="./src/images/flags/ru.svg">
                                    <img src="./src/images/flags/ru.svg" alt="Russian Flag" class="flag-icon"
                                        style="width: 20px; margin-right: 6px;" />
                                    Russian
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="?lang=uz" data-lang="uz" data-name="Uzbek"
                                    data-flag="./src/images/flags/uz.svg">
                                    <img src="./src/images/flags/uz.svg" alt="Uzbek Flag" class="flag-icon"
                                        style="width: 20px; margin-right: 6px;" />
                                    Uzbek
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="?lang=tj" data-lang="tj" data-name="Tajik"
                                    data-flag="./src/images/flags/tj.svg">
                                    <img src="./src/images/flags/tj.svg" alt="Tajik Flag" class="flag-icon"
                                        style="width: 20px; margin-right: 6px;" />
                                    Tajik
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <div class="ms-lg-3 mt-3 mt-lg-0 d-flex">
                    <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                    <a href="/register" class="btn btn-primary-custom">Register</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- NAVBAR END-->

    <!--  -->
    <section id="contact" class="contact-section">
        <div class="container contact-container">
            <div class="text-center mb-5">
                <h2 class="section-title">Let’s Build Your Website</h2>
                <p class="section-subtitle">
                    Have questions about our developer services or ready to start your project? Contact us today!
                </p>
            </div>
            <div class="contact-form-card fade-in">
                <form action="/submit-contact" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter your name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" placeholder="your.email@example.com"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="service" class="form-label">Interested Service</label>
                            <select class="form-control" id="service" required>
                                <option value="" disabled selected>Select a service</option>
                                <option value="domain-registration">Domain Registration ($10/year)</option>
                                <option value="website-creation">Website Creation ($99/one-time)</option>
                                <option value="website-updates">Website Updates ($50/month)</option>
                                <option value="website-launch">Website Launch ($75/one-time)</option>
                                <option value="backend-writing">Website Backend Writing ($200/one-time)</option>
                                <option value="personal-website">Personal Website Creation ($150/one-time)</option>
                                <option value="business-website">Business Website Creation ($499/one-time)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" placeholder="How can we help?">
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" rows="6"
                                placeholder="Tell us about your project or ask a question" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-envelope-fill"></i> Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="contact-info-card fade-in delay-1">
                <h3>Contact Us</h3>
                <div class="contact-item">
                    <i class="bi bi-geo-alt-fill contact-icon"></i>
                    <div class="contact-text">
                        <h5>Our Office</h5>
                        <p>Firdavsiy Street 51, Samarkand, Uzbekistan</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-envelope-fill contact-icon"></i>
                    <div class="contact-text">
                        <h5>Email Us</h5>
                        <p><a href="mailto:support@templates.uz">support@templates.uz</a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-telephone-fill contact-icon"></i>
                    <div class="contact-text">
                        <h5>Call Us</h5>
                        <p><a href="tel:+998331237790">+998 33 123 77 90</a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-clock-fill contact-icon"></i>
                    <div class="contact-text">
                        <h5>Working Hours</h5>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
                <div class="social-links">
                    <a href="https://facebook.com/templatesuz" class="social-link" target="_blank"><i
                            class="bi bi-facebook"></i></a>
                    <a href="https://x.com/templatesuz" class="social-link" target="_blank"><i class="bi bi-x"></i></a>
                    <a href="https://instagram.com/templatesuz" class="social-link" target="_blank"><i
                            class="bi bi-instagram"></i></a>
                    <a href="https://linkedin.com/company/templatesuz" class="social-link" target="_blank"><i
                            class="bi bi-linkedin"></i></a>
                    <a href="https://t.me/templatesuz" class="social-link" target="_blank"><i
                            class="bi bi-telegram"></i></a>
                </div>
                <div class="map-container fade-in delay-2">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3073.541047378342!2d66.9598153153626!3d39.65030097946344!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d191960077df7%3A0x487636d9d13f2e57!2sFirdavsiy%20Street%2051%2C%20Samarkand%2C%20Uzbekistan!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <div class="text-center mt-5 fade-in delay-2">
                <p class="text-muted fs-5">Launch your dream website today! <a href="#contact"
                        class="text-decoration-none fw-bold" style="color: var(--primary);">Contact us now</a> – limited
                    project slots available!</p>
            </div>
        </div>
    </section>
    <!--  -->

    <!-- FOOTER END -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5>Templates.uz</h5>
                    <p class="text-muted">Create a professional website for yourself with our platform. Fast, easy, and
                        reliable!</p>
                </div>
                <div class="col-md-2">
                    <div class="footer-links">
                        <h5>Links</h5>
                        <a href="#">Home</a>
                        <a href="#features">Features</a>
                        <a href="#templates">Templates</a>
                        <a href="#pricing">Pricing</a>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="footer-links">
                        <h5>Useful</h5>
                        <a href="#">Help Center</a>
                        <a href="#">Terms of Use</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Blog</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-links">
                        <h5>Contact Us</h5>
                        <p class="text-muted mb-2"><i class="bi bi-envelope me-2"></i>support@templates.uz</p>
                        <p class="text-muted mb-2"><i class="bi bi-telephone me-2"></i>+998 90 123 45 67</p>
                        <div class="social-icons mt-3 d-flex gap-3">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <p class="text-muted small">&copy; 2025 Templates.uz. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- FOOTER END -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link =>
                link.addEventListener('click', () => {
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    link.classList.add('active');
                })
            );

            const langParam = new URLSearchParams(window.location.search).get('lang') || 'en';
            const dropdown = document.getElementById('languageDropdown');
            const toggleLink = dropdown.querySelector('#languageToggle');
            const dropdownItems = dropdown.querySelectorAll('.dropdown-item');

            let currentLangItem = Array.from(dropdownItems).find(item => item.dataset.lang === langParam);
            if (!currentLangItem) currentLangItem = dropdownItems[dropdownItems.length - 1];

            toggleLink.innerHTML = `
                <img src="${currentLangItem.dataset.flag}" alt="${currentLangItem.dataset.name}" class="flag-icon" style="width: 20px; border-radius: 3px; margin-right: 6px;" />
                <span>${currentLangItem.dataset.name}</span>
            `;
        });
    </script>
</body>

</html>
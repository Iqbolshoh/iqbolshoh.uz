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
    <link rel="stylesheet" href="./src/css/developer-services.css" />

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

    <section id="developer-services" class="services-section">
        <div class="container">
            <h2 class="section-title">Our <span>Developer Services</span></h2>
            <p class="section-subtitle">Launch your dream website with our tailored solutions, from domains to premium
                business sites.</p>
            <div class="row g-4">
                <!-- Recurring Services -->
                <div class="col-md-6 col-lg-4 fade-in">
                    <div class="service-card">
                        <h3>Domain Registration</h3>
                        <div class="price">$10<span>/year</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Custom domain (e.g., yoursite.com)</li>
                            <li><i class="bi bi-check-circle-fill"></i> Domain management tools</li>
                            <li><i class="bi bi-check-circle-fill"></i> WHOIS privacy protection</li>
                            <li><i class="bi bi-check-circle-fill"></i> 24/7 support</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-in delay-1">
                    <div class="service-card">
                        <h3>Website Updates</h3>
                        <div class="price">$50<span>/month</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Content updates (up to 10 changes)</li>
                            <li><i class="bi bi-check-circle-fill"></i> Security patches</li>
                            <li><i class="bi bi-check-circle-fill"></i> Performance optimization</li>
                            <li><i class="bi bi-check-circle-fill"></i> Monthly backups</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <!-- One-Time Services -->
                <div class="col-md-6 col-lg-4 fade-in delay-2">
                    <div class="service-card">
                        <h3>Website Launch</h3>
                        <div class="price">$75<span>/one-time</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Hosting setup</li>
                            <li><i class="bi bi-check-circle-fill"></i> SSL certificate installation</li>
                            <li><i class="bi bi-check-circle-fill"></i> DNS configuration</li>
                            <li><i class="bi bi-check-circle-fill"></i> Launch testing</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-in">
                    <div class="service-card">
                        <h3>Website Creation</h3>
                        <div class="price">$99<span>/one-time</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Custom design with up to 5 pages</li>
                            <li><i class="bi bi-check-circle-fill"></i> Responsive layout</li>
                            <li><i class="bi bi-check-circle-fill"></i> Basic SEO setup</li>
                            <li><i class="bi bi-check-circle-fill"></i> 1 month free support</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-in delay-1">
                    <div class="service-card">
                        <h3>Personal Website Creation</h3>
                        <div class="price">$150<span>/one-time</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Personalized design (3 pages)</li>
                            <li><i class="bi bi-check-circle-fill"></i> Contact form integration</li>
                            <li><i class="bi bi-check-circle-fill"></i> Social media links</li>
                            <li><i class="bi bi-check-circle-fill"></i> 1 month free support</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 fade-in delay-2">
                    <div class="service-card">
                        <h3>Website Backend Writing</h3>
                        <div class="price">$200<span>/one-time</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Custom backend development</li>
                            <li><i class="bi bi-check-circle-fill"></i> Database integration</li>
                            <li><i class="bi bi-check-circle-fill"></i> API connections</li>
                            <li><i class="bi bi-check-circle-fill"></i> Admin panel setup</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Get Started</a>
                    </div>
                </div>
                <div class="col-12 fade-in delay-3">
                    <div class="service-card highlight-card">
                        <h3>Business Website Creation</h3>
                        <div class="price">$499<span>/one-time</span></div>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Premium design with up to 10 pages</li>
                            <li><i class="bi bi-check-circle-fill"></i> Advanced SEO optimization</li>
                            <li><i class="bi bi-check-circle-fill"></i> E-commerce or booking system</li>
                            <li><i class="bi bi-check-circle-fill"></i> Full admin panel</li>
                            <li><i class="bi bi-check-circle-fill"></i> Social media integration</li>
                            <li><i class="bi bi-check-circle-fill"></i> 3 months free support</li>
                            <li><i class="bi bi-check-circle-fill"></i> Dedicated hosting setup</li>
                        </ul>
                        <a href="#contact" class="btn btn-primary-custom">Contact Us Now</a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted fs-6">Need a custom solution? <a href="#contact"
                        class="text-decoration-none fw-bold" style="color: var(--primary-color);">Reach out now</a> –
                    limited slots available!</p>
            </div>
        </div>
    </section>


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
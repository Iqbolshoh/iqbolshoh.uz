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
    <link rel="stylesheet" href="./src/css/pricing-section.css" />
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

    <section id="pricing" class="pricing-section">
        <div class="container py-5">
            <h2 class="section-title">Pricing Plans</h2>
            <p class="section-subtitle">
                Flexible plans tailored to fit any budget
            </p>
            <div class="row g-4">
                <div class="col-md-4 fade-in">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h4>Free</h4>
                            <p>Perfect for beginners</p>
                        </div>
                        <div class="card-body">
                            <h3>$0<span>/month</span></h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i> 1 Website</li>
                                <li><i class="bi bi-check text-success me-2"></i> 5 MB Storage</li>
                                <li><i class="bi bi-check text-success me-2"></i> Basic Features: Templates, Slider,
                                    Form, Video, Images, Editor</li>
                                <li><i class="bi bi-check text-success me-2"></i> Subdomain: sitename.templates.uz</li>
                                <li><i class="bi bi-x text-danger me-2"></i> E-commerce</li>
                                <li><i class="bi bi-x text-danger me-2"></i> Premium Support</li>
                            </ul>
                            <a href="#" class="btn btn-primary-custom">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-1">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h4>Starter</h4>
                            <p>Ideal for small projects</p>
                        </div>
                        <div class="card-body">
                            <h3>$3<span>/month</span></h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i> 3 Websites</li>
                                <li><i class="bi bi-check text-success me-2"></i> 100 MB Storage</li>
                                <li><i class="bi bi-check text-success me-2"></i> All Basic Features</li>
                                <li><i class="bi bi-check text-success me-2"></i> Telegram Bot Editing</li>
                                <li><i class="bi bi-check text-success me-2"></i> Subdomain + Favicon</li>
                                <li><i class="bi bi-x text-danger me-2"></i> E-commerce</li>
                            </ul>
                            <a href="#" class="btn btn-primary-custom">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-2">
                    <div class="pricing-card h-100 position-relative">
                        <span class="pricing-popular">Most Popular</span>
                        <div class="pricing-header">
                            <h4>Standard</h4>
                            <p>Great for growing businesses</p>
                        </div>
                        <div class="card-body">
                            <h3>$8<span>/month</span></h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i> 5 Websites</li>
                                <li><i class="bi bi-check text-success me-2"></i> 500 MB Storage</li>
                                <li><i class="bi bi-check text-success me-2"></i> Custom Domain (mysite.uz)</li>
                                <li><i class="bi bi-check text-success me-2"></i> Basic Admin Panel</li>
                                <li><i class="bi bi-check text-success me-2"></i> All Basic Features</li>
                                <li><i class="bi bi-x text-danger me-2"></i> Advanced Analytics</li>
                            </ul>
                            <a href="#" class="btn btn-primary-custom">Choose Plan</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 fade-in">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h4>Pro</h4>
                            <p>Advanced features for professionals</p>
                        </div>
                        <div class="card-body">
                            <h3>$15<span>/month</span></h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i> 10 Websites</li>
                                <li><i class="bi bi-check text-success me-2"></i> 1 GB Storage</li>
                                <li><i class="bi bi-check text-success me-2"></i> Full Admin Panel</li>
                                <li><i class="bi bi-check text-success me-2"></i> SEO & Analytics</li>
                                <li><i class="bi bi-check text-success me-2"></i> User Registration</li>
                                <li><i class="bi bi-check text-success me-2"></i> All Basic Features</li>
                            </ul>
                            <a href="#" class="btn btn-primary-custom">Choose Plan</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-1">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h4>Premium</h4>
                            <p>Ultimate plan for large projects</p>
                        </div>
                        <div class="card-body">
                            <h3>$29<span>/month</span></h3>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i> Unlimited Websites</li>
                                <li><i class="bi bi-check text-success me-2"></i> 3 GB Storage</li>
                                <li><i class="bi bi-check text-success me-2"></i> Priority Support</li>
                                <li><i class="bi bi-check text-success me-2"></i> Multilingual Sites</li>
                                <li><i class="bi bi-check text-success me-2"></i> Currency Conversion</li>
                                <li><i class="bi bi-check text-success me-2"></i> Custom Integrations</li>
                                <li><i class="bi bi-check text-success me-2"></i> Exclusive Templates</li>
                            </ul>
                            <a href="#" class="btn btn-primary-custom">Choose Plan</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <p class="text-muted">Save 2 months with annual billing! <a href="#" class="text-decoration-none">Learn
                        More</a></p>
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
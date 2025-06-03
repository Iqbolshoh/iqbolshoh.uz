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
    <link rel="stylesheet" href="./src/css/hero-section.css" />
    <link rel="stylesheet" href="./src/css/features-section.css" />
    <link rel="stylesheet" href="./src/css/works-section.css" />
    <link rel="stylesheet" href="./src/css/faq-section.css" />
    <link rel="stylesheet" href="./src/css/testimonials-section.css" />
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

    <!-- HERO SECTION -->
    <section class="hero-section text-center" id="home">
        <div class="container">
            <h1 class="hero-title">Create Your Website Fast and Easy</h1>
            <p class="hero-subtitle">
                Build a stunning, customized website with Templates.uz in no time!
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
                <a href="create.html" class="btn btn-primary-custom btn-lg">
                    <i class="bi bi-rocket"></i> Create New Website
                </a>
                <a href="#options" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-eye"></i> Explore Options
                </a>
            </div>
            <div class="creation-options-wrapper" id="options">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="option-box">
                            <i class="bi bi-mouse2-fill option-icon"></i>
                            <h5>Drag & Drop Builder</h5>
                            <p>Drag and drop site blocks to see your design come to life instantly. No coding required!
                            </p>
                            <a href="create.html?mode=drag" class="btn btn-sm btn-outline-primary">Get Started</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="option-box">
                            <i class="bi bi-layout-text-window-reverse option-icon"></i>
                            <h5>Template-Based Creation</h5>
                            <p>Choose a ready-made template and customize it to fit your needs.</p>
                            <a href="create.html?mode=template" class="btn btn-sm btn-outline-primary">Get Started</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="option-box">
                            <i class="bi bi-code-slash option-icon"></i>
                            <h5>Developer Mode</h5>
                            <p>Take full control! Build your site with HTML, CSS, and JavaScript.</p>
                            <a href="create.html?mode=developer" class="btn btn-sm btn-outline-primary">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- HERO SECTION END-->

    <!-- FEATURES -->
    <section id="features" class="features-section">
        <div class="container py-5">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">
                Templates.uz offers the best website creation experience with powerful tools and flexibility.
            </p>
            <div class="row g-4">
                <div class="col-md-4 fade-in">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-magic"></i>
                        </div>
                        <h3>Drag & Drop Builder</h3>
                        <p>Customize your site without writing code. Easily add and arrange elements with our intuitive
                            builder.</p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-1">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-collection"></i>
                        </div>
                        <h3>Ready-Made Templates</h3>
                        <p>Choose from over 150 professionally designed templates and customize them to fit your vision.
                        </p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-2">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <h3>Multilingual & SEO</h3>
                        <p>Make your site globally accessible with multilingual support and SEO optimization to boost
                            your Google rankings.</p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4 fade-in">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h3>Mobile Responsive</h3>
                        <p>Create websites that look perfect on any device. Our templates automatically adapt to all
                            screen sizes.</p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-1">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h3>Lightning Fast</h3>
                        <p>Optimized code and CDN support ensure up to 90% faster load times for an exceptional user
                            experience.</p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-2">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3>24/7 Support</h3>
                        <p>Our dedicated support team is always ready to assist you via email, chat, or phone.</p>
                        <a href="#">Learn More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FEATURES END-->

    <!--  -->
    <section id="how-it-works" class="how-it-works-section">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title text-start">How It Works</h2>
                    <p class="section-subtitle">Create a professional website in just 3 simple steps</p>
                    <div class="step-item fade-in">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h5>Choose a Template</h5>
                            <p>Select a template from our collection or request a custom design from our team.</p>
                        </div>
                    </div>
                    <div class="step-item fade-in delay-1">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h5>Add Your Content</h5>
                            <p>Upload your text, images, and videos using our intuitive interface for easy
                                customization.</p>
                        </div>
                    </div>
                    <div class="step-item fade-in delay-2">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h5>Publish</h5>
                            <p>Launch your website online with a single click. No need to worry about domain or hosting
                                issues!</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 fade-in delay-1">
                    <video class="how-it-works-video img-fluid" autoplay muted loop playsinline>
                        <source
                            src="https://cdn.dribbble.com/userupload/43553017/file/original-c3c0ecf65625e47bb4b3ea80690dd9e1.mp4"
                            type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </section>
    <!--  -->

    <!--  -->
    <section id="faq" class="faq-section">
        <div class="container py-5">
            <h2 class="section-title">Developer Services FAQ</h2>
            <p class="section-subtitle">
                Find answers to common questions about our developer services below
            </p>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item fade-in">
                    <h2 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                            What does the Domain Registration service include?
                        </button>
                    </h2>
                    <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our Domain Registration service, priced at $10/year, provides a custom domain (e.g.,
                            yoursite.uz), WHOIS privacy protection, domain management tools, and 24/7 support to secure
                            your online identity effortlessly.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in delay-1">
                    <h2 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            How long does Website Creation take?
                        </button>
                    </h2>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our Website Creation service ($99/one-time) typically takes 5-7 business days, depending on
                            the complexity of your design. It includes up to 5 pages, a responsive layout, basic SEO
                            setup, and 1 month of free support.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in delay-2">
                    <h2 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            What kind of updates are covered in the Website Updates service?
                        </button>
                    </h2>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            The Website Updates service ($50/month) covers up to 10 content changes per month, security
                            patches, performance optimization, and monthly backups to keep your site fresh and secure.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in delay-3">
                    <h2 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            What is involved in the Website Launch process?
                        </button>
                    </h2>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our Website Launch service ($75/one-time) ensures a seamless go-live process, including
                            hosting setup, SSL certificate installation, DNS configuration, and thorough launch testing
                            to guarantee your site is ready for visitors.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in">
                    <h2 class="accordion-header" id="faqHeading5">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                            What technologies are used in Website Backend Writing?
                        </button>
                    </h2>
                    <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            For our Website Backend Writing service ($200/one-time), we use modern technologies like
                            Node.js, Python, or PHP, with database integration (e.g., MySQL, MongoDB), API connections,
                            and a custom admin panel tailored to your needs.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in delay-1">
                    <h2 class="accordion-header" id="faqHeading6">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                            What makes the Personal Website Creation service unique?
                        </button>
                    </h2>
                    <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            The Personal Website Creation service ($150/one-time) is designed to showcase your portfolio
                            or personal brand with a 3-page custom design, contact form, social media links, and 1 month
                            of free support, perfect for individuals.
                        </div>
                    </div>
                </div>
                <div class="accordion-item fade-in delay-2">
                    <h2 class="accordion-header" id="faqHeading7">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqCollapse7" aria-expanded="false" aria-controls="faqCollapse7">
                            Why choose the Business Website Creation service?
                        </button>
                    </h2>
                    <div id="faqCollapse7" class="accordion-collapse collapse" aria-labelledby="faqHeading7"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our Business Website Creation service ($499/one-time) is ideal for businesses seeking a
                            premium online presence. It includes up to 10 pages, advanced SEO, e-commerce or booking
                            systems, a full admin panel, social media integration, 3 months of free support, and
                            dedicated hosting setup.
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="#contact" class="btn btn-primary-custom">
                    <i class="bi bi-envelope-fill"></i> Still have questions? Contact Us
                </a>
            </div>
        </div>
    </section>
    <!--  -->

    <!--  -->
    <section id="testimonials" class="testimonials-section">
        <div class="container py-5">
            <h2 class="section-title">What Our Clients Say</h2>
            <p class="section-subtitle">
                Hear from our satisfied clients about their experience with Templates.uz
            </p>
            <div class="row g-4">
                <div class="col-md-4 fade-in">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>"With Templates.uz, we built an amazing website for our small business. Our customers love
                            it, and our sales increased by 40%!"</p>
                        <h5>Dilfuza Rahimova</h5>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-1">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>"I'm not a developer, but I created a professional portfolio website in just a few hours with
                            Templates.uz. Fantastic platform!"</p>
                        <h5>Javlonbek Ismoilov</h5>
                    </div>
                </div>
                <div class="col-md-4 fade-in delay-2">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <p>"We created a stunning website for our restaurant. The online ordering system saved us time
                            and attracted more customers. Great support team!"</p>
                        <h5>Zarina Qodirova</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  -->

    <!-- FOOTER -->
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
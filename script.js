const elements = {
    selector: document.getElementById("languageSelector"),
    content: {
        pageTitle: document.querySelector('title'),
        title: document.getElementById("title"),
        description: document.getElementById("description"),
        features: Array.from({ length: 3 }, (_, i) => ({
            title: document.getElementById(`feature${i + 1}-title`),
            desc: document.getElementById(`feature${i + 1}-desc`)
        })),
        footer: document.getElementById("footer-text")
    },
    progress: {
        percent: document.getElementById("progress-percent"),
        fill: document.querySelector(".progress-fill"),
        text: document.querySelector('[data-i18n="progress"]')
    },
    timer: {
        days: document.getElementById("days"),
        hours: document.getElementById("hours"),
        minutes: document.getElementById("minutes"),
        seconds: document.getElementById("seconds")
    },
    i18n: document.querySelectorAll('[data-i18n]')
};

async function loadLanguageData() {
    try {
        const response = await fetch('lang.json');
        return response.ok ? await response.json() : {};
    } catch (error) {
        console.error('Error loading language data:', error);
        return {};
    }
}

function updateContent(lang, content) {
    const data = content[lang] || content.en;
    const { content: el } = elements;

    el.pageTitle.textContent = "iqbolshoh.uz | " + data.title;
    el.title.textContent = data.title;
    el.description.textContent = data.description;
    el.features.forEach((f, i) => {
        f.title.textContent = data[`feature${i + 1}-title`];
        f.desc.textContent = data[`feature${i + 1}-desc`];
    });
    el.footer.textContent = data["footer-text"];

    elements.i18n.forEach(el => {
        const key = el.getAttribute('data-i18n');
        el.textContent = data[key] || key;
    });
}

function updateCountdown() {
    const now = new Date();
    const launchDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
    const diff = (launchDate - now) / 1e3;
    const total = (launchDate - new Date(now.getFullYear(), now.getMonth(), 1)) / 1e3;

    elements.timer.days.textContent = Math.floor(diff / 86400).toString().padStart(2, '0');
    elements.timer.hours.textContent = Math.floor((diff % 86400) / 3600).toString().padStart(2, '0');
    elements.timer.minutes.textContent = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
    elements.timer.seconds.textContent = Math.floor(diff % 60).toString().padStart(2, '0');

    const percent = ((1 - diff / total) * 100).toFixed(2);
    elements.progress.percent.textContent = `${percent}%`;
    elements.progress.fill.style.width = `${percent}%`;

    requestAnimationFrame(updateCountdown);
}

async function init() {
    const content = await loadLanguageData();
    const lang = localStorage.getItem('language') || 'uz';

    elements.selector.value = lang;
    updateContent(lang, content);
    updateCountdown();

    elements.selector.addEventListener('change', () => {
        const newLang = elements.selector.value;
        localStorage.setItem('language', newLang);
        updateContent(newLang, content);
    });
}

init();

function createParticles() {
    const particles = 30;
    for (let i = 0; i < particles; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');

        const size = Math.random() * 5 + 2;
        const posX = Math.random() * window.innerWidth;
        const posY = Math.random() * window.innerHeight;
        const duration = Math.random() * 20 + 10;
        const delay = Math.random() * 5;

        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}px`;
        particle.style.top = `${posY}px`;
        particle.style.animation = `float ${duration}s ${delay}s infinite linear`;
        particle.style.opacity = Math.random() * 0.5 + 0.1;

        document.body.appendChild(particle);
    }
}

document.addEventListener('DOMContentLoaded', createParticles);
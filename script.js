const elements = {
    selector: document.getElementById("languageSelector"),
    content: {
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
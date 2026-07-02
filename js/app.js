const BASE_URL = window.APP_BASE_URL || '';
const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#site-nav');
const themeToggle = document.querySelector('.theme-toggle');

const setTheme = theme => {
  const isDark = theme === 'dark';
  document.body.classList.toggle('dark-mode', isDark);
  if (themeToggle) {
    themeToggle.textContent = isDark ? '☀️' : '🌙';
    themeToggle.setAttribute('aria-pressed', String(isDark));
    themeToggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
  }
};

const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
setTheme(savedTheme);

themeToggle?.addEventListener('click', () => {
  const nextTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
  setTheme(nextTheme);
  localStorage.setItem('theme', nextTheme);
});

toggle?.addEventListener('click', () => {
  const open = nav.classList.toggle('open');
  toggle.setAttribute('aria-expanded', String(open));
  toggle.textContent = open ? 'Tutup' : 'Menu';
});

nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
  nav.classList.remove('open');
  toggle?.setAttribute('aria-expanded', 'false');
  if (toggle) toggle.textContent = 'Menu';
}));

const chatButton = document.querySelector('.chat-button');
const chatPicker = document.querySelector('.chat-picker');
chatButton?.addEventListener('click', () => {
  const isOpen = chatPicker?.classList.toggle('open');
  if (chatPicker) {
    chatPicker.setAttribute('aria-hidden', String(!isOpen));
  }
  if (chatButton) {
    chatButton.setAttribute('aria-expanded', String(isOpen));
  }
});

document.addEventListener('click', event => {
  if (chatPicker && chatButton && !chatPicker.contains(event.target) && !chatButton.contains(event.target)) {
    chatPicker.classList.remove('open');
    chatPicker.setAttribute('aria-hidden', 'true');
    chatButton.setAttribute('aria-expanded', 'false');
  }
});

const observer = new IntersectionObserver(entries => entries.forEach(entry => {
  if (entry.isIntersecting) {
    entry.target.classList.add('visible');
    observer.unobserve(entry.target);
  }
}), { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((element, index) => {
  element.style.transitionDelay = `${Math.min(index % 4, 3) * 70}ms`;
  observer.observe(element);
});

const escapeHtml = (value = '') => String(value).replace(/[&<>"']/g, character => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[character]));

async function hydrateContent() {
  try {
    const responses = await Promise.all([fetch(`${BASE_URL}/api/settings`), fetch(`${BASE_URL}/api/portfolio`), fetch(`${BASE_URL}/api/services`)]);
    if (responses.some(response => !response.ok)) throw new Error('API konten tidak dapat dimuat');
    const [{ data: settings }, { data: projects }, { data: services }] = await Promise.all(responses.map(response => response.json()));
    const text = (selector, value) => {
      const element = document.querySelector(selector);
      if (element && value !== undefined) element.textContent = value;
    };

    document.querySelectorAll('.brand').forEach(brand => {
      const title = brand.querySelector('.site-title') ?? document.createElement('span');
      title.className = 'site-title';
      title.innerHTML = 'Nusa <span class="dot">HomeLab</span>';

      if (settings.logo) {
        let logo = brand.querySelector('.site-logo');
        if (!logo) {
          logo = document.createElement('img');
          logo.className = 'site-logo';
          brand.insertBefore(logo, brand.firstChild);
        }
        logo.src = settings.logo;
        logo.alt = settings.name || 'Logo website';
      } else {
        let icon = brand.querySelector('.brand-icon') ?? document.createElement('span');
        icon.className = 'brand-icon';
        icon.textContent = (settings.name || 'Nusa HomeLab').charAt(0).toUpperCase();
        if (!icon.parentNode || icon.parentNode !== brand) {
          brand.insertBefore(icon, brand.firstChild);
        }
      }

      if (!title.parentNode || title.parentNode !== brand) {
        brand.append(title);
      }
    });
    text('.topbar > div:first-child', settings.topbar_text);
    text('.topbar a:first-child', settings.topbar_link_text);
    text('.hero-kicker', `${settings.role || ''} · ${settings.location || ''}`);
    const heroParts = (settings.hero_title || '').split('|');
    document.querySelector('.hero h1').innerHTML = `${escapeHtml(heroParts[0])}${heroParts.length > 1 ? `<br><em>${escapeHtml(heroParts.slice(1).join(' '))}</em>` : ''}`;
    text('.hero-bottom p', settings.hero_description);
    text('.hero-actions .primary-btn', `${settings.hero_primary_text || ''} →`);
    text('.hero-actions .secondary-btn', settings.hero_secondary_text);
    text('.trust-row > span', settings.trust_label);
    const trustRow = document.querySelector('.trust-row');
    trustRow.querySelectorAll('b').forEach(item => item.remove());
    (settings.trust_items || '').split(',').map(item => item.trim()).filter(Boolean).forEach(item => {
      const label = document.createElement('b');
      label.textContent = item;
      trustRow.append(label);
    });
    text('.card-one b', settings.project_count);
    text('.card-two b', settings.rating);
    text('.card-two span', settings.rating_label);

    document.querySelectorAll('.benefits article').forEach((article, index) => {
      text(`.benefits article:nth-child(${index + 1}) strong`, settings[`benefit_${index + 1}_title`]);
      text(`.benefits article:nth-child(${index + 1}) span`, settings[`benefit_${index + 1}_text`]);
    });
    text('.services .section-intro > span', settings.services_label);
    text('.services .section-intro h2', settings.services_title);
    text('.services .section-intro p', settings.services_description);
    text('.work .section-head span', settings.portfolio_label);
    text('.work .section-head h2', settings.portfolio_title);
    text('.work .section-head > a', `${settings.portfolio_cta || ''} →`);

    document.querySelector('.service-list').innerHTML = services.map((service, index) =>
      `<article class="service reveal visible"><div class="service-icon">✦</div><span>${String(index + 1).padStart(2, '0')}</span><h3>${escapeHtml(service.title)}</h3><p>${escapeHtml(service.description)}</p><i>${escapeHtml(settings.service_link_text || '')} →</i></article>`
    ).join('');

    const projectList = document.querySelector('.projects');
    const projectMoreButton = document.querySelector('.project-more');
    const projectCount = projects.length;
    const projectPreviewLimit = 6;
    const colors = ['#bdc6b4', '#da704d', '#c8d650', '#87b8e8', '#d8b5df'];

    const renderProjects = showAll => {
      const visibleProjects = showAll ? projects : projects.slice(0, projectPreviewLimit);
      projectList.innerHTML = '';
      visibleProjects.forEach((project, index) => {
        const article = document.createElement('article');
        article.className = `project reveal visible${index === 0 ? ' project-large' : ''}`;
        article.innerHTML = `<div class="project-visual"></div><div class="project-meta"><div><h2>${escapeHtml(project.title)}</h2><p>${escapeHtml(project.category)}</p></div><span>${escapeHtml(project.year || '')}</span>${project.description ? `<p class="project-description">${escapeHtml(project.description)}</p>` : ''}</div>`;
        const visual = article.querySelector('.project-visual');
        visual.style.background = project.image ? `linear-gradient(#0002,#0002), url("${encodeURI(project.image)}") center/cover` : colors[index % colors.length];
        if (project.link) {
          article.style.cursor = 'pointer';
          article.addEventListener('click', () => window.open(project.link, '_blank', 'noopener'));
        }
        projectList.append(article);
      });
      if (projectMoreButton) {
        if (projectCount > projectPreviewLimit) {
          projectMoreButton.style.display = 'inline-flex';
          projectMoreButton.textContent = showAll ? 'Sembunyikan' : 'Lihat semua proyek';
        } else {
          projectMoreButton.style.display = 'none';
        }
      }
    };

    let showAllProjects = false;
    projectMoreButton?.addEventListener('click', () => {
      showAllProjects = !showAllProjects;
      renderProjects(showAllProjects);
    });
    renderProjects(false);

    text('.about-content > span', settings.about_label);
    text('.about-content h2', settings.about_title);
    text('.about-lead', settings.about_description);
    text('.about-badge b', settings.experience_years);
    text('.about-badge span', settings.experience_label);
    document.querySelectorAll('.stats .stat').forEach((stat, index) => {
      text(`.stats .stat:nth-child(${index + 1}) strong`, settings[`stat_${index + 1}_value`]);
      text(`.stats .stat:nth-child(${index + 1}) span`, settings[`stat_${index + 1}_label`]);
    });
    text('.contact > div > span', settings.contact_label);
    text('.contact h2', settings.contact_title);
    text('.contact > div > p', settings.contact_description);
    const contactButton = document.querySelector('.contact-button');
    contactButton.href = `mailto:${settings.email || ''}`;
    text('.contact-button span', settings.email);
    text('.contact-button b', `${settings.contact_cta || ''} →`);
    text('footer > p', settings.footer_description);
    text('footer > small', settings.copyright);
    const social = document.querySelector('footer > div');
    if (social?.children[0]) social.children[0].href = settings.instagram || '#';
    if (social?.children[1]) social.children[1].href = settings.linkedin || '#';
  } catch (error) {
    console.warn('Konten dinamis belum dapat dimuat:', error);
  }
}

hydrateContent();

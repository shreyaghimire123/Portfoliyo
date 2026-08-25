// ========================
// PROJECT DATA - 3 PROJECTS ONLY
// ========================
const projectsData = [
    {
        id: 1,
        title: 'Personal Portfolio Website',
        category: 'frontend',
        tag: 'Frontend',
        description: 'Modern, responsive portfolio with smooth animations and dark theme with glassmorphism.',
        tech: ['HTML', 'CSS', 'JavaScript'],
        github: 'https://github.com/shreyaghimire123',
        demo: '#',
        image: 'assets/images/project1.jpg'
    },
    {
        id: 2,
        title: 'E-Commerce Mobile App Design',
        category: 'design',
        tag: 'UI/UX Design',
        description: 'Complete mobile app UI/UX design in Figma with interactive prototyping.',
        tech: ['Figma', 'Adobe XD', 'Prototyping'],
        github: 'https://github.com/shreyaghimire123',
        demo: '#',
        image: 'assets/images/project2.jpg'
    },
    {
        id: 3,
        title: 'Restaurant Landing Page',
        category: 'website',
        tag: 'Website',
        description: 'Beautiful restaurant website with online menu and reservation system.',
        tech: ['HTML', 'CSS', 'JavaScript'],
        github: 'https://github.com/shreyaghimire123',
        demo: '#',
        image: 'assets/images/project3.jpg'
    }
];

// ========================
// RENDER PROJECTS
// ========================
function renderProjects(projects) {
    const grid = document.getElementById('projectsGrid');

    if (!grid) return;

    grid.innerHTML = projects.map(project => `
        <div class="project-card" data-category="${project.category}">
            <div class="project-image">
                <img src="${project.image}" alt="${project.title}">
                <div class="project-overlay">
                    <a href="project-details.html?id=${project.id}" class="btn-view">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
            <div class="project-info">
                <span class="project-tag">${project.tag}</span>
                <h3>${project.title}</h3>
                <p>${project.description}</p>
                <div class="project-tech">
                    ${project.tech.map(t => `<span>${t}</span>`).join('')}
                </div>
                <div class="project-links">
                    <a href="${project.github}" target="_blank" class="project-link"><i class="fab fa-github"></i> Code</a>
                    <a href="${project.demo}" target="_blank" class="project-link"><i class="fas fa-external-link-alt"></i> Demo</a>
                </div>
            </div>
        </div>
    `).join('');
}

// ========================
// PROJECT FILTERING
// ========================
document.addEventListener('DOMContentLoaded', function() {
    // Render all projects
    renderProjects(projectsData);

    // Filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            if (filter === 'all') {
                renderProjects(projectsData);
            } else {
                const filtered = projectsData.filter(p => p.category === filter);
                renderProjects(filtered);
            }
        });
    });
});

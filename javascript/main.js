// ========================
// TYPEWRITER ANIMATION
// ========================
document.addEventListener('DOMContentLoaded', function() {
    const typedElement = document.getElementById('typed-text');

    if (typedElement) {
        const professions = [
            'Frontend Developer',
            'UI/UX Designer',
            'Content Creator',
            'BCA Student at Nepal Mega College'
        ];

        let charIndex = 0;
        let currentText = '';
        let isDeleting = false;
        let professionIndex = 0;

        function typeEffect() {
            const fullText = professions[professionIndex];

            if (isDeleting) {
                currentText = fullText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                currentText = fullText.substring(0, charIndex + 1);
                charIndex++;
            }

            typedElement.textContent = currentText;

            let speed = isDeleting ? 40 : 80;

            if (!isDeleting && charIndex === fullText.length) {
                speed = 2000;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                professionIndex = (professionIndex + 1) % professions.length;
                speed = 500;
            }

            setTimeout(typeEffect, speed);
        }

        typeEffect();
    }
});

// ========================
// NAVIGATION - MOBILE MENU
// ========================
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', function() {
        this.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu when clicking a link
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });
}

// ========================
// NAVBAR SCROLL EFFECT
// ========================
const header = document.getElementById('header');
let lastScroll = 0;

window.addEventListener('scroll', function() {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }

    lastScroll = currentScroll;
});

// ========================
// ACTIVE LINK HIGHLIGHT
// ========================
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-menu a');

window.addEventListener('scroll', function() {
    let current = '';

    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.clientHeight;

        if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});

// ========================
// BACK TO TOP BUTTON
// ========================
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        backToTop.classList.add('visible');
    } else {
        backToTop.classList.remove('visible');
    }
});

backToTop.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ========================
// CONTACT FORM
// ========================
const contactForm = document.getElementById('contactForm');
const formStatus = document.getElementById('formStatus');

if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('.btn-submit');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('php/contact.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                formStatus.className = 'form-status success';
                formStatus.textContent = '✅ Message sent successfully! I\'ll get back to you soon.';
                contactForm.reset();
            } else {
                formStatus.className = 'form-status error';
                formStatus.textContent = '❌ ' + result.message;
            }
        } catch (error) {
            formStatus.className = 'form-status error';
            formStatus.textContent = '❌ Something went wrong. Please try again.';
        }

        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        setTimeout(() => {
            formStatus.className = 'form-status';
            formStatus.textContent = '';
        }, 5000);
    });
}

// ========================
// NEWSLETTER FORM
// ========================
const newsletterForm = document.getElementById('newsletterForm');

if (newsletterForm) {
    newsletterForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;

        try {
            const response = await fetch('php/newsletter.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('✅ Subscribed successfully! Thank you.');
                newsletterForm.reset();
            } else {
                alert('❌ ' + result.message);
            }
        } catch (error) {
            alert('❌ Something went wrong. Please try again.');
        }

        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

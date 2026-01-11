/**
 * Hero Banner Carousel - Supports Multiple Instances (Banner 1, 2, 3)
 */
class HeroCarousel {
    constructor(containerId) {
        this.containerId = containerId;
        this.currentSlide = 0;
        this.totalSlides = 0;
        this.autoSlideInterval = null;
        this.AUTO_SLIDE_DELAY = 5000;

        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) return;

        this.container = container;
        this.wrapper = container.closest('.hero-carousel-wrapper');
        this.slides = container.querySelectorAll('.hero-slide');
        this.totalSlides = this.slides.length;

        if (this.totalSlides === 0) return;

        this.setupNavigation();
        this.setupDots();
        this.setupTouchEvents();
        this.startAutoSlide();
    }

    goToSlide(index) {
        if (index < 0) index = this.totalSlides - 1;
        if (index >= this.totalSlides) index = 0;

        this.currentSlide = index;
        this.updateSlides();
        this.updateDots();
        this.resetAutoSlide();
    }

    nextSlide() {
        this.goToSlide(this.currentSlide + 1);
    }

    prevSlide() {
        this.goToSlide(this.currentSlide - 1);
    }

    updateSlides() {
        this.slides.forEach((slide, index) => {
            if (index === this.currentSlide) {
                slide.style.display = 'flex';
                slide.classList.add('active');
            } else {
                slide.style.display = 'none';
                slide.classList.remove('active');
            }
        });
    }

    updateDots() {
        if (!this.wrapper) return;
        const dotsContainer = this.wrapper.querySelector('.hero-carousel-dots');
        if (!dotsContainer) return;

        const dots = dotsContainer.querySelectorAll('.hero-carousel-dot');
        dots.forEach((dot, index) => {
            if (index === this.currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    setupDots() {
        if (!this.wrapper) return;
        const dotsContainer = this.wrapper.querySelector('.hero-carousel-dots');
        if (!dotsContainer) return;

        const dots = dotsContainer.querySelectorAll('.hero-carousel-dot');
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                this.goToSlide(index);
            });
        });
    }

    setupNavigation() {
        if (!this.wrapper) return;

        const prevBtn = this.wrapper.querySelector('.hero-prev');
        const nextBtn = this.wrapper.querySelector('.hero-next');

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.prevSlide();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.nextSlide();
            });
        }

        // Pause auto-slide on hover
        this.wrapper.addEventListener('mouseenter', () => {
            this.stopAutoSlide();
        });

        this.wrapper.addEventListener('mouseleave', () => {
            this.startAutoSlide();
        });
    }

    setupTouchEvents() {
        if (!this.wrapper) return;

        let touchStartX = 0;
        let touchEndX = 0;

        // Use passive: false to allow preventing default if we wanted to block scroll (we don't here, but good to know)
        // But for horizontal slider in vertical page, pan-y css is best.

        this.wrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length > 1) return; // Ignore multi-touch
            touchStartX = e.touches[0].clientX;
        }, { passive: true });

        this.wrapper.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].clientX;
            this.handleSwipe(touchStartX, touchEndX);
        }, { passive: true });
    }

    handleSwipe(startX, endX) {
        const threshold = 50; // min distance
        const diff = startX - endX;

        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                // Swiped Left (startX > endX) -> Next Slide
                this.nextSlide();
            } else {
                // Swiped Right (startX < endX) -> Prev Slide
                this.prevSlide();
            }
        }
    }

    startAutoSlide() {
        if (this.totalSlides <= 1) return;
        this.autoSlideInterval = setInterval(() => {
            this.nextSlide();
        }, this.AUTO_SLIDE_DELAY);
    }

    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    resetAutoSlide() {
        this.stopAutoSlide();
        this.startAutoSlide();
    }
}

// Initialize instances
document.addEventListener('DOMContentLoaded', () => {
    new HeroCarousel('heroBanner1');
    new HeroCarousel('heroBanner2');
    new HeroCarousel('heroBanner3');
});

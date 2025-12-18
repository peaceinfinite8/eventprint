/**
 * Small Banner Carousel (Vanilla JS)
 * Handles swipe, drag, auto-slide, dots, and arrows.
 * Revised: Enlarge & Arrows
 */
class SmallBannerCarousel {
    constructor(containerId, data) {
        this.container = document.getElementById(containerId);
        this.data = data;
        this.currentSlide = 0;
        this.interval = null;
        this.isDragging = false;
        this.startPos = 0;
        this.currentTranslate = 0;
        this.prevTranslate = 0;
        this.animationID = 0;
        this.wrapper = null;
        this.slideWidth = 0;

        if (this.container && this.data && this.data.length > 0) {
            this.init();
        }
    }

    init() {
        this.render();
        this.initEvents();
        this.startAutoSlide();
        this.updateDots();

        // Resize handler
        window.addEventListener('resize', () => {
            this.wrapper.style.transition = 'none';
            this.updatePosition();
            setTimeout(() => {
                this.wrapper.style.transition = 'transform 0.3s ease-out';
            }, 50);
        });
    }

    render() {
        this.container.innerHTML = `
      <div class="promo-wrapper">
        ${this.data.map(item => `
          <div class="promo-slide">
            <img src="${item.image}" alt="${item.alt}" draggable="false" onerror="this.src='../assets/images/placeholder-location.jpg'">
          </div>
        `).join('')}
      </div>
      
      <button class="promo-arrow promo-prev" aria-label="Previous Slide">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
      </button>
      <button class="promo-arrow promo-next" aria-label="Next Slide">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>

      <div class="promo-dots">
        ${this.data.map((_, i) => `<div class="promo-dot" data-index="${i}"></div>`).join('')}
      </div>
    `;

        this.wrapper = this.container.querySelector('.promo-wrapper');
        this.wrapper.style.width = `${this.data.length * 100}%`;

        // Add dot listeners
        this.container.querySelectorAll('.promo-dot').forEach((dot, index) => {
            dot.addEventListener('click', (e) => {
                e.stopPropagation();
                this.goToSlide(index);
            });
        });

        // Add arrow listeners
        this.container.querySelector('.promo-prev').addEventListener('click', (e) => {
            e.stopPropagation();
            this.prevSlide();
        });
        this.container.querySelector('.promo-next').addEventListener('click', (e) => {
            e.stopPropagation();
            this.nextSlide();
        });
    }

    initEvents() {
        // Touch events
        this.wrapper.addEventListener('touchstart', this.touchStart.bind(this));
        this.wrapper.addEventListener('touchend', this.touchEnd.bind(this));
        this.wrapper.addEventListener('touchmove', this.touchMove.bind(this));

        // Mouse events
        this.wrapper.addEventListener('mousedown', this.touchStart.bind(this));
        this.wrapper.addEventListener('mouseup', this.touchEnd.bind(this));
        this.wrapper.addEventListener('mouseleave', () => {
            if (this.isDragging) this.touchEnd();
            this.startAutoSlide();
        });
        this.wrapper.addEventListener('mousemove', this.touchMove.bind(this));

        // Pause on hover (desktop)
        this.container.addEventListener('mouseenter', () => this.stopAutoSlide());
        this.container.addEventListener('mouseleave', () => this.startAutoSlide());
    }

    touchStart(event) {
        this.isDragging = true;
        this.stopAutoSlide();
        this.startPos = this.getPositionX(event);
        this.animationID = requestAnimationFrame(this.animation.bind(this));
        this.wrapper.style.cursor = 'grabbing';
        window.carouselDragging = false;
    }

    touchMove(event) {
        if (this.isDragging) {
            const currentPosition = this.getPositionX(event);
            if (Math.abs(currentPosition - this.startPos) > 5) {
                window.carouselDragging = true;
            }
            this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
        }
    }

    touchEnd() {
        this.isDragging = false;
        cancelAnimationFrame(this.animationID);
        this.wrapper.style.cursor = 'grab';

        const movedBy = this.currentTranslate - this.prevTranslate;

        if (movedBy < -50) {
            this.nextSlide();
        } else if (movedBy > 50) {
            this.prevSlide();
        } else {
            this.updatePosition();
        }
        this.startAutoSlide();
    }

    getPositionX(event) {
        return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
    }

    animation() {
        this.setSliderPosition();
        if (this.isDragging) requestAnimationFrame(this.animation.bind(this));
    }

    setSliderPosition() {
        this.wrapper.style.transform = `translateX(${this.currentTranslate}px)`;
    }

    updatePosition() {
        const width = this.container.clientWidth;
        this.currentTranslate = this.currentSlide * -width;
        this.prevTranslate = this.currentTranslate;
        this.wrapper.style.transform = `translateX(${this.currentTranslate}px)`;
        this.updateDots();
    }

    goToSlide(index) {
        this.currentSlide = index;
        if (this.currentSlide < 0) this.currentSlide = this.data.length - 1;
        if (this.currentSlide >= this.data.length) this.currentSlide = 0;
        this.updatePosition();
    }

    nextSlide() {
        this.goToSlide(this.currentSlide + 1);
    }

    prevSlide() {
        this.goToSlide(this.currentSlide - 1);
    }

    updateDots() {
        this.container.querySelectorAll('.promo-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentSlide);
        });
    }

    startAutoSlide() {
        this.stopAutoSlide();
        this.interval = setInterval(() => {
            this.nextSlide();
        }, 5000);
    }

    stopAutoSlide() {
        if (this.interval) clearInterval(this.interval);
    }
}

window.SmallBannerCarousel = SmallBannerCarousel;

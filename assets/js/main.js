// Cases Swiper
const swiperCases = new Swiper('.cases__wrapper', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: 20,
    slidesPerView: 1,
    pagination: {
        el: '.cases__slider-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.cases__slider-nav__item--next',
        prevEl: '.cases__slider-nav__item--prev',
    },
    breakpoints: {
        480: {
            slidesPerView: 1.1,
            spaceBetween: 16,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
    },
});

// Articles Swiper
const swiperArticles = new Swiper('.articles__wrapper', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: 20,
    slidesPerView: 1,
    pagination: {
        el: '.articles__wrapper-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.articles__wrapper-nav__item--next',
        prevEl: '.articles__wrapper-nav__item--prev',
    },
    breakpoints: {
        480: {
            slidesPerView: 1.1,
            spaceBetween: 16,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
    },
});

// Reviews Swiper
const swiperReviews = new Swiper('.reviews__wrapper', {
    direction: 'horizontal',
    loop: true,
    spaceBetween: 20,
    slidesPerView: 1,
    pagination: {
        el: '.reviews__slider-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.reviews__slider-nav__item--next',
        prevEl: '.reviews__slider-nav__item--prev',
    },
    breakpoints: {
        480: {
            slidesPerView: 1.1,
            spaceBetween: 16,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
    },
});

// FAQ Accordion - Close other items on open
document.querySelectorAll('.faq__item').forEach((item) => {
    item.addEventListener('toggle', (e) => {
        if (item.open) {
            document.querySelectorAll('.faq__item').forEach((otherItem) => {
                if (otherItem !== item && otherItem.open) {
                    otherItem.open = false;
                }
            });
        }
    });
});

// CTA Form Segmented Method Switcher
const methodLabels = document.querySelectorAll('.cta__form-card-method__label');
methodLabels.forEach((label) => {
    label.addEventListener('click', () => {
        methodLabels.forEach((l) => l.classList.remove('cta__form-card-method__label--active'));
        label.classList.add('cta__form-card-method__label--active');
    });
});
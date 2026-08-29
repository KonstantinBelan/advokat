const swiperCases = new Swiper('.cases__wrapper', {
    direction: 'horizontal',
    loop: true,
    pagination: {
        el: '.cases__slider-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.cases__slider-nav__item--next',
        prevEl: '.cases__slider-nav__item--prev',
    },
    mousewheel: {
        enabled: true,
        thresholdTime: 500,
        forceToAxis: true
    },
    breakpoints: {
        480: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
    },
});

const swiperReviews = new Swiper('.reviews__wrapper', {
    direction: 'horizontal',
    loop: true,
    pagination: {
        el: '.reviews__slider-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.reviews__slider-nav__item--next',
        prevEl: '.reviews__slider-nav__item--prev',
    },
    mousewheel: {
        enabled: true,
        thresholdTime: 500,
        forceToAxis: true
    },
    breakpoints: {
        480: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
    },
});


const swiperArticles = new Swiper('.articles__wrapper', {
    direction: 'horizontal',
    loop: true,
    pagination: {
        el: '.articles__wrapper-dots',
        clickable: true,
    },
    navigation: {
        nextEl: '.articles__wrapper-nav__item--next',
        prevEl: '.articles__wrapper-nav__item--prev',
    },
    mousewheel: {
        enabled: true,
        thresholdTime: 500,
        forceToAxis: true
    },
    breakpoints: {
        480: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
    },
});
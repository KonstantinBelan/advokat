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

// Mobile Burger Menu Toggle
const headerBurger = document.querySelector('.header-burger');
const mobileDropdown = document.querySelector('.header-mobile-dropdown');

if (headerBurger && mobileDropdown) {
    headerBurger.addEventListener('click', () => {
        headerBurger.classList.toggle('is-active');
        mobileDropdown.classList.toggle('is-open');
    });

    // Close menu when clicking on any menu link
    document.querySelectorAll('.header-mobile-dropdown__item, .header-mobile-dropdown__callback').forEach((link) => {
        link.addEventListener('click', () => {
            headerBurger.classList.remove('is-active');
            mobileDropdown.classList.remove('is-open');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!headerBurger.contains(e.target) && !mobileDropdown.contains(e.target)) {
            headerBurger.classList.remove('is-active');
            mobileDropdown.classList.remove('is-open');
        }
    });
}

// Reviews Clamping (> 7 lines) and 'Смотреть отзыв'
function initReviewsClamp() {
    document.querySelectorAll('.reviews__slider-item').forEach((item) => {
        const textEl = item.querySelector('.reviews__slider-item-header__text');
        if (!textEl) return;

        // Approximate line-height in px (14px * 1.6 ≈ 22.4px)
        const computedStyle = window.getComputedStyle(textEl);
        const lineHeight = parseFloat(computedStyle.lineHeight) || (parseFloat(computedStyle.fontSize) * 1.6) || 22.4;
        const maxHeight = lineHeight * 7;

        // If content height exceeds 7 lines
        if (textEl.scrollHeight > maxHeight + 4) {
            textEl.classList.add('is-clamped');

            // Avoid duplicate button if re-run
            let moreBtn = item.querySelector('.reviews__slider-item-more');
            if (!moreBtn) {
                moreBtn = document.createElement('button');
                moreBtn.type = 'button';
                moreBtn.className = 'reviews__slider-item-more';
                moreBtn.textContent = 'Смотреть отзыв';

                moreBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const isClamped = textEl.classList.toggle('is-clamped');
                    moreBtn.textContent = isClamped ? 'Смотреть отзыв' : 'Свернуть';
                    if (typeof swiperReviews !== 'undefined' && swiperReviews.update) {
                        swiperReviews.update();
                    }
                });

                const headerEl = item.querySelector('.reviews__slider-item-header');
                if (headerEl) {
                    headerEl.appendChild(moreBtn);
                }
            }
        }
    });
}

// Media Show More / Hide Toggle
const mediaShowMoreBtn = document.querySelector('.media__show-more');
const hiddenMediaItems = document.querySelectorAll('.media__item--hidden');

if (mediaShowMoreBtn && hiddenMediaItems.length > 0) {
    mediaShowMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = mediaShowMoreBtn.classList.toggle('is-expanded');
        hiddenMediaItems.forEach((item) => {
            if (isExpanded) {
                item.style.setProperty('display', 'grid', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
        mediaShowMoreBtn.textContent = isExpanded ? 'Скрыть' : 'Показать еще';
    });
}

// Reviews Page Show More Toggle
const reviewsPageMoreBtn = document.querySelector('.reviews-page__more');
const hiddenReviewCards = document.querySelectorAll('.reviews-page__card--hidden');

if (reviewsPageMoreBtn && hiddenReviewCards.length > 0) {
    reviewsPageMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = reviewsPageMoreBtn.classList.toggle('is-expanded');
        hiddenReviewCards.forEach((item) => {
            if (isExpanded) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
        reviewsPageMoreBtn.textContent = isExpanded ? 'Скрыть' : 'Показать еще';
    });
}

// Cases Page Show More Toggle
const casesPageMoreBtn = document.querySelector('.cases-page__more');
const hiddenCaseCards = document.querySelectorAll('.cases-page__card--hidden');

if (casesPageMoreBtn && hiddenCaseCards.length > 0) {
    casesPageMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = casesPageMoreBtn.classList.toggle('is-expanded');
        hiddenCaseCards.forEach((item) => {
            if (isExpanded) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
        casesPageMoreBtn.textContent = isExpanded ? 'Скрыть' : 'Показать еще';
    });
}

// Blog Page Show More Toggle
const blogPageMoreBtn = document.querySelector('.blog-page__more');
const hiddenBlogCards = document.querySelectorAll('.blog-page__card--hidden');

if (blogPageMoreBtn && hiddenBlogCards.length > 0) {
    blogPageMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = blogPageMoreBtn.classList.toggle('is-expanded');
        hiddenBlogCards.forEach((item) => {
            if (isExpanded) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
        blogPageMoreBtn.innerHTML = isExpanded
            ? 'Скрыть <span class="btn--outline-more__arrow">▲</span>'
            : 'Показать еще <span class="btn--outline-more__arrow">▼</span>';
    });
}

// News Page Show More Toggle
const newsPageMoreBtn = document.querySelector('.news-page__more');
const hiddenNewsCards = document.querySelectorAll('.news-page__card--hidden');

if (newsPageMoreBtn && hiddenNewsCards.length > 0) {
    newsPageMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isExpanded = newsPageMoreBtn.classList.toggle('is-expanded');
        hiddenNewsCards.forEach((item) => {
            if (isExpanded) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.removeProperty('display');
            }
        });
        newsPageMoreBtn.innerHTML = isExpanded
            ? 'Скрыть <span class="btn--outline-more__arrow">▲</span>'
            : 'Показать еще <span class="btn--outline-more__arrow">▼</span>';
    });
}

// Run review clamp on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReviewsClamp);
} else {
    initReviewsClamp();
}
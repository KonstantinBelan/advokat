// Cases Swiper
function initCasesSwiper() {
    document.querySelectorAll('.cases__slider-container').forEach((container) => {
        const wrapper = container.querySelector('.cases__wrapper');
        if (!wrapper || wrapper.swiper) return;
        new Swiper(wrapper, {
            direction: 'horizontal',
            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            observer: true,
            observeParents: true,
            watchOverflow: true,
            pagination: {
                el: container.querySelector('.cases__slider-dots') || '.cases__slider-dots',
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector('.cases__slider-nav__item--next') || '.cases__slider-nav__item--next',
                prevEl: container.querySelector('.cases__slider-nav__item--prev') || '.cases__slider-nav__item--prev',
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
    });
}
initCasesSwiper();
document.addEventListener('DOMContentLoaded', initCasesSwiper);

// Articles Swiper
if (document.querySelector('.articles__wrapper')) {
    new Swiper('.articles__wrapper', {
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
}

// Inline Article Detail Swipers (supports multiple sliders per article)
document.querySelectorAll('.article-detail__slider, .article-slider').forEach((sliderEl) => {
    new Swiper(sliderEl, {
        direction: 'horizontal',
        loop: true,
        spaceBetween: 10,
        slidesPerView: 1,
        observer: true,
        observeParents: true,
        observeSlideChildren: true,
        watchOverflow: true,
        pagination: {
            el: sliderEl.querySelector('.article-slider-dots, .swiper-pagination'),
            clickable: true,
        },
        navigation: {
            nextEl: sliderEl.querySelector('.article-slider-btn--next, .swiper-button-next'),
            prevEl: sliderEl.querySelector('.article-slider-btn--prev, .swiper-button-prev'),
        },
    });
});

// Reviews Swiper
let swiperReviews;
function initReviewsSwiper() {
    document.querySelectorAll('.reviews__slider-container').forEach((container) => {
        const wrapper = container.querySelector('.reviews__wrapper');
        if (!wrapper || wrapper.swiper) return;
        swiperReviews = new Swiper(wrapper, {
            direction: 'horizontal',
            loop: true,
            spaceBetween: 20,
            slidesPerView: 1,
            observer: true,
            observeParents: true,
            watchOverflow: true,
            pagination: {
                el: container.querySelector('.reviews__slider-dots') || '.reviews__slider-dots',
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector('.reviews__slider-nav__item--next') || '.reviews__slider-nav__item--next',
                prevEl: container.querySelector('.reviews__slider-nav__item--prev') || '.reviews__slider-nav__item--prev',
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
    });
}
initReviewsSwiper();
document.addEventListener('DOMContentLoaded', initReviewsSwiper);

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

// Blog Mobile Tags Collapse Toggle
document.querySelectorAll('.blog-tags-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
        const container = btn.closest('.blog-tags-container');
        if (!container) return;
        const collapse = container.querySelector('.blog-tags-collapse');
        const isExpanded = btn.classList.toggle('is-active');
        btn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        if (collapse) {
            collapse.classList.toggle('is-open', isExpanded);
        }
    });
});

// AJAX Load More for Articles, News, Consultation, Cases, Reviews
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-load-more, .blog-page__more, .news-page__more, .consultation__more-btn, .cases-page__more, .reviews-page__more');
    if (!btn || !btn.dataset.postType) return;
    e.preventDefault();

    const postType = btn.dataset.postType;
    let currentPage = parseInt(btn.dataset.page || '1', 10);
    const maxPages = parseInt(btn.dataset.maxPages || '1', 10);
    const containerSel = btn.dataset.container;
    const category = btn.dataset.category || '';
    const container = containerSel ? document.querySelector(containerSel) : null;

    if (!container || currentPage >= maxPages || btn.classList.contains('is-loading')) return;

    btn.classList.add('is-loading');
    const originalContent = btn.innerHTML;
    btn.innerHTML = 'Загрузка...';

    const formData = new FormData();
    formData.append('action', 'belan_load_more');
    formData.append('nonce', (window.belan_ajax && window.belan_ajax.nonce) ? window.belan_ajax.nonce : '');
    formData.append('post_type', postType);
    formData.append('paged', currentPage + 1);
    if (category) {
        formData.append('category', category);
    }

    const ajaxUrl = (window.belan_ajax && window.belan_ajax.url) ? window.belan_ajax.url : '/wp-admin/admin-ajax.php';

    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
    })
    .then((res) => res.json())
    .then((res) => {
        btn.classList.remove('is-loading');
        btn.innerHTML = originalContent;

        if (res.success && res.data && res.data.html) {
            currentPage++;
            btn.dataset.page = currentPage;

            // Append new items smoothly
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = res.data.html;
            while (tempDiv.firstChild) {
                container.appendChild(tempDiv.firstChild);
            }

            if (!res.data.has_more || currentPage >= maxPages) {
                const wrapper = btn.closest('.blog-page__more-wrapper, .news-page__more-wrapper, .consultation__pagination, .cases-page__more-wrapper, .reviews-page__more-wrapper') || btn;
                wrapper.style.display = 'none';
            }
        } else {
            const wrapper = btn.closest('.blog-page__more-wrapper, .news-page__more-wrapper, .consultation__pagination, .cases-page__more-wrapper, .reviews-page__more-wrapper') || btn;
            wrapper.style.display = 'none';
        }
    })
    .catch((err) => {
        console.error('Load more error:', err);
        btn.classList.remove('is-loading');
        btn.innerHTML = originalContent;
    });
});

// Mobile Menu Submenu Accordions
const mobileArrowBtns = document.querySelectorAll('.header-mobile-dropdown__arrow-btn');
mobileArrowBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const group = btn.closest('.header-mobile-dropdown__group');
        if (group) {
            const submenu = group.querySelector('.header-mobile-dropdown__submenu');
            if (submenu) {
                const isOpen = submenu.classList.toggle('is-open');
                btn.classList.toggle('is-open', isOpen);
            }
        }
    });
});

// Article Video Play on Click (VK iframe embed)
document.querySelectorAll('.article-detail__video-thumb').forEach((thumb) => {
    thumb.addEventListener('click', function () {
        if (this.classList.contains('is-playing')) return;

        const videoSrc = this.getAttribute('data-video-src') || 'https://vkvideo.ru/video_ext.php?oid=-22822305&id=456239103&hd=2&autoplay=1';

        let iframe = this.querySelector('iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.setAttribute('src', videoSrc);
            iframe.setAttribute('width', '100%');
            iframe.setAttribute('height', '100%');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;');
            iframe.setAttribute('allowfullscreen', 'true');
            this.appendChild(iframe);
        } else if (!iframe.getAttribute('src')) {
            iframe.setAttribute('src', videoSrc);
        }

        this.classList.add('is-playing');
    });
});

// Consultation Question Expand Toggle
document.querySelectorAll('.consultation-card__more-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const card = btn.closest('.consultation-card');
        if (card) {
            const text = card.querySelector('.consultation-card__text');
            if (text) {
                const isTruncated = text.classList.toggle('is-truncated');
                btn.textContent = isTruncated ? 'Показать полностью...' : 'Свернуть';
            }
        }
    });
});

// Run review clamp on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReviewsClamp);
} else {
    initReviewsClamp();
}

// AJAX Form Handling
document.addEventListener('DOMContentLoaded', () => {
    // Lead forms
    document.querySelectorAll('.belan-lead-form').forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const feedback = form.querySelector('.form-feedback');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Отправка...';
            }

            const formData = new FormData(form);
            formData.append('action', 'belan_lead');
            if (typeof belan_ajax !== 'undefined') {
                formData.append('nonce', belan_ajax.nonce);
            }

            const ajaxUrl = (typeof belan_ajax !== 'undefined') ? belan_ajax.url : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })
                .then((r) => r.json())
                .then((data) => {
                    if (feedback) {
                        feedback.style.display = 'block';
                        if (data.success) {
                            feedback.style.color = '#2e7d32';
                            feedback.innerText = data.data && data.data.message ? data.data.message : 'Спасибо! Заявка успешно отправлена.';
                            form.reset();
                        } else {
                            feedback.style.color = '#c62828';
                            feedback.innerText = data.data && data.data.message ? data.data.message : 'Ошибка при отправке.';
                        }
                    }
                })
                .catch(() => {
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.color = '#c62828';
                        feedback.innerText = 'Произошла сетевая ошибка. Пожалуйста, попробуйте позже.';
                    }
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
        });
    });

    // Review form
    document.querySelectorAll('.belan-review-form').forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const feedback = form.querySelector('.form-feedback');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Отправка...';
            }

            const formData = new FormData(form);
            formData.append('action', 'belan_review');
            if (typeof belan_ajax !== 'undefined') {
                formData.append('nonce', belan_ajax.nonce);
            }

            const ajaxUrl = (typeof belan_ajax !== 'undefined') ? belan_ajax.url : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })
                .then((r) => r.json())
                .then((data) => {
                    if (feedback) {
                        feedback.style.display = 'block';
                        if (data.success) {
                            feedback.style.color = '#2e7d32';
                            feedback.innerText = data.data && data.data.message ? data.data.message : 'Спасибо за ваш отзыв!';
                            form.reset();
                        } else {
                            feedback.style.color = '#c62828';
                            feedback.innerText = data.data && data.data.message ? data.data.message : 'Ошибка при отправке отзыва.';
                        }
                    }
                })
                .catch(() => {
                    if (feedback) {
                        feedback.style.display = 'block';
                        feedback.style.color = '#c62828';
                        feedback.innerText = 'Произошла ошибка при отправке.';
                    }
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
        });
    });

    // File input attach label update
    document.querySelectorAll('.consultation-form-section__attach input[type="file"]').forEach((input) => {
        input.addEventListener('change', function () {
            const span = this.parentElement.querySelector('span');
            if (span) {
                if (this.files && this.files.length > 0) {
                    span.textContent = this.files.length === 1 ? this.files[0].name : `Выбрано файлов: ${this.files.length}`;
                } else {
                    span.textContent = 'Прикрепить файл';
                }
            }
        });
    });
});
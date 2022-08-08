import 'core-js/stable';
import 'regenerator-runtime/runtime';

import Swiper, { Navigation, Pagination } from 'swiper';

const swiper = new Swiper('.swiper', {
  direction: 'vertical',
  loop: true,

  pagination: {
    el: '.swiper-pagination',
  },

  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },

  scrollbar: {
    el: '.swiper-scrollbar',
  },
  modules: [Navigation, Pagination],
});
console.log('swiperrrr');

import Vue from 'vue';
import Router from 'vue-router';
import ShortUrlForm from '../components/ShortUrlForm.vue';
import UrlHistory from '../components/UrlHistory.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: ShortUrlForm
    },
    {
      path: '/history',
      name: 'history',
      component: UrlHistory
    }
  ]
});

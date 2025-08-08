import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js for minimal JavaScript interactions
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

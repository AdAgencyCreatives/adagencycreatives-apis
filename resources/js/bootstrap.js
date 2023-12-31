import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

console.log("Hi");
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '9fc68307fd984bd9a52d',
  cluster: 'mt1',
  forceTLS: false,
  disableStats: true,
  wsHost: 'websocket.noorsofttechdev.com',
  // wsHost: window.location.hostname,
  wsPort: 6001,
});


console.log(window.Echo);


 window.Echo.private('messanger.2a2f7229-5707-3e4b-ae79-341e3f73170c')
            .listen('.private_msg', (e) => {
                console.log(e.data);
                alert(e.data.message);
            })



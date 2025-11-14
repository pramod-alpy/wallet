import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

let echoInstance = null;

export function useEcho() {
  if (!echoInstance) {
    window.Pusher = Pusher;

    echoInstance = new Echo({
      broadcaster: 'pusher',
      key: 'cc8952fd86ba4ef792b2', // your Pusher key
      cluster: 'mt1',               // your Pusher cluster
      forceTLS: true,
      authEndpoint: '/broadcasting/auth',
      auth: {
        headers: {},               // cookies are sent automatically
      },
      // optional: disable stats for local dev
      disableStats: true,
    });

    // Optional: monitor connection
    echoInstance.connector.pusher.connection.bind('error', (err) => {
      console.error('Pusher connection error:', err);
    });

    echoInstance.connector.pusher.connection.bind('connected', () => {
      console.log('Pusher connected/reconnected!');
    });
  }

  return echoInstance;
}

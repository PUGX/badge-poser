import Raven from 'raven-js';

if (window.globalVars.APP_DEBUG !== '1') {
    Raven
        .config('https://1435e86eef3d46c5a39525e9dd7a0dab@sentry.io/295017')
        .install();
}

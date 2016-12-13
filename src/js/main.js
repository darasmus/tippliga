import 'babelify/polyfill';
import $ from 'jquery';
import app from './app';
import Router from './router';

$(document).ready(() => {
    app.router = new Router();
    app.start();
});

// global ajax config
$(document).ajaxSend(() => {
    app.trigger('ajax:start');
}).ajaxStop(() => {
    app.trigger('ajax:stop');
});

$(document).ajaxError(function (e, xhr) {
    if ( xhr.status === 401 ) {
        app.trigger('ajax:authError');
    }
});

// activate for nocache behaviour
$.ajaxSetup({
    //cache: false 
});

$(window).on('resize', () => {
    app.trigger('window:resize');
});


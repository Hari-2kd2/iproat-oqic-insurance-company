
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import 'bootstrap';

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (error) {

}

window.Vue = require('vue');

window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */



/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });




var notifications = [];

// ...
const NOTIFICATION_TYPES = {
    leave: 'App\\Notifications\\LeaveNotification',
    follow: 'App\\Notifications\\UserFollowed',
    newPost: 'App\\Notifications\\NewPost',
    dashboard: 'App\\Notifications\\DashboardNotification'

};
// ...
window.Pusher = require('pusher-js');
import Echo from "laravel-echo";

window.Echo = new Echo({
    // authEndpoint: 'http://dev.test:8074/bafna-pharmacy/notifications',
    broadcaster: 'pusher',
    key: 'b1d3c90f0f8c903bfabe',
    cluster: 'ap2',
    encrypted: true
});

//...

if (Laravel.userId) {
    //...
    window.Echo.private(`App.User.${Laravel.userId}`)
        .notification((notification) => {
            addNotifications([notification], '#notifications');
        });
}

//...
// check if there's a logged in user
if (Laravel.userId) {
    $.get('/notifications', function (data) {
        addNotifications(data, "#notifications");
    });
}

function addNotifications(newNotifications, target) {
    notifications = _.concat(notifications, newNotifications);
    // show only last 5 notifications
    notifications.slice(0, 5);
    showNotifications(notifications, target);
}

//...
function showNotifications(notifications, target) {
    if (notifications.length) {
        var htmlElements = notifications.map(function (notification) {
            return makeNotification(notification);
        });
        $(target + 'Menu').html(htmlElements.join(''));
        $(target).append('<div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>');
        $(target).addClass('has-notifications');
    } else {
        $(target + 'Menu').html('<li class="dropdown-header">No notifications</li>');
        $(target).removeClass('has-notifications');
    }
}

// Make a single notification string
function makeNotification(notification) {
    var to = routeNotification(notification);
    var notificationText = makeNotificationText(notification);
    return '<li><a href="' + to + '">' + notificationText + '</a></li>';
}



//...
function routeNotification(notification) {
    var to = `?read=${notification.id}`;
    if (notification.type === NOTIFICATION_TYPES.dashboard) {
        to = 'mark-as-read' + to;
    } else if (notification.type === NOTIFICATION_TYPES.newPost) {
        const postId = notification.data.name;
        to = `posts/${postId}` + to;
    }
    return '/' + to;
}

function makeNotificationText(notification) {
    var text = '';
    if (notification.type === NOTIFICATION_TYPES.dashboard) {
        const name = notification.data.name;
        text += `new Notification from <strong>${name}</strong>`;
    } else if (notification.type === NOTIFICATION_TYPES.newPost) {
        const name = notification.data.name;
        text += `<strong>${name}</strong> published a post`;
    }
    return text;
}


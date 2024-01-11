<li class="dropdown">
    <a class="dropdown-toggle" id="notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="fa fa-bell"></span>
    </a>
    <ul class="dropdown-menu" aria-labelledby="notificationsMenu" id="notificationsMenu">
        <li class="dropdown-header">No notifications</li>
    </ul>
</li>

<input type="text" class="user_id" hidden value="{{ auth()->user()->user_id }}">

<script>
    $(document).ready(function() {

        var id = $('.user_id').val();
        var token = '{{ csrf_token() }}';


        var notifications = [];

        // ...
        const NOTIFICATION_TYPES = {
            leave: 'App\\Notifications\\LeaveNotification',
            follow: 'App\\Notifications\\UserFollowed',
            newPost: 'App\\Notifications\\NewPost',
            dashboard: 'App\\Notifications\\DashboardNotification'

        };

        if (Laravel.userId) {
            //...
            window.Echo.private(`App.User.${Laravel.userId}`)
                .notification((notification) => {
                    addNotifications([notification], '#notifications');
                });
        }

        // check if there's a logged in user
        // if (id) {
        //     $.ajax({
        //         type: "get",
        //         url: "{{ route('notifications') }}",
        //         success: function(data) {
        //             console.log(data);
        //             addNotifications(data, "#notifications");
        //         }
        //     });
        // }


        function addNotifications(newNotifications, target) {
            notifications = _.concat(notifications, newNotifications);
            // show only last 5 notifications
            notifications.slice(0, 5);
            showNotifications(notifications, target);
        }

        function showNotifications(notifications, target) {
            if (notifications.length) {
                var htmlElements = notifications.map(function(notification) {
                    return makeNotification(notification);
                });
                $(target + 'Menu').html(htmlElements.join(''));
                $(target).addClass('has-notifications')
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

        function routeNotification(notification) {
            var to = `?read=${notification.id}`;
            if (notification.type === NOTIFICATION_TYPES.dashboard) {
                to = 'bafna-pharmacy/mark-as-read' + to;
            } else if (notification.type === NOTIFICATION_TYPES.dashboard) {
                const postId = notification.data.post_id;
                to = `posts/${postId}` + to;
            }
            return '/' + to;
        }

        function makeNotificationText(notification) {
            var text = '';
            if (notification.type === NOTIFICATION_TYPES.dashboard) {
                const name = notification.data.name;
                text += `<strong>${name}</strong> followed you`;
            } else if (notification.type === NOTIFICATION_TYPES.dashboard) {
                const name = notification.data.name;
                text += `<strong>${name}</strong> published a post`;
            }
            return text;
        }
    });
</script>

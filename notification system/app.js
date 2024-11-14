$(document).ready(function() {
    // Function to fetch notifications
    function fetchNotifications() {
        $.ajax({
            type: 'GET',
            url: '../notification system/fetch_notification.php',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateNotificationList(response);
                } else {
                    console.error('Failed to fetch notifications:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Function to update notification list in the dropdown
    function updateNotificationList(response) {
        const notifications = $('#notifications');
        const notificationCount = $('#notificationCount');

        notifications.html(''); // Clear current list

        if (response.data && response.data.length > 0) {
            notificationCount.text(response.data.length);
            notificationCount.show();

            response.data.forEach(notification => {
                const listItem = $(`
                    <li class="dropdown-item notification-item" data-notification-id="${notification.id}">
                        <a href="${notification.noti_url || '#'}" class="notification-link">
                            ${notification.noti_message} - <small>${new Date(notification.noti_date).toLocaleString()}</small>
                        </a>
                    </li>
                `);
                notifications.append(listItem);
            });
        } else {
            notificationCount.text(0);
            notifications.html('<li class="dropdown-item">No notifications</li>');
            notificationCount.hide();
        }
    }

    // Automatically fetch notifications every 5 seconds
    setInterval(fetchNotifications, 5000);

    // Mark a notification as seen when clicked
    $(document).on('click', '.notification-item', function() {
        const notificationId = $(this).data('notification-id');

        if (!notificationId) {
            console.error('Notification ID is missing.');
            return;
        }

        // Mark notification as seen
        $.ajax({
            type: 'POST',
            url: '../notification system/seen_notification.php',
            data: { notification_id: notificationId },
            success: function(response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'success') {
                        console.log('Notification marked as seen');
                        fetchNotifications(); // Refresh list after marking seen
                    } else {
                        console.error('Failed to mark notification as seen:', res.message);
                    }
                } catch (error) {
                    console.error('Unexpected response format:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as seen:', error);
            }
        });
    });

    // Initial fetch of notifications
    fetchNotifications();
});

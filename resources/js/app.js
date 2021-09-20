require('./bootstrap');

require('alpinejs');

window.Echo.private('orders')
    .listen('.order.created', function (event) {
        alert(`New order created ${event.order.number}`)
    })

window.Echo.private(`App.Models.User.${userId}`)
    .notification(function (e) {
        var count = Number($('#unread').text());
        count++;
        $('.unread').text(count);
        $('#notifications').prepend(`<a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i>
                    <b>*</b>
                    ${e.title}
                    <span class="float-right text-muted text-sm">${e.time}</span>
                </a>
                <div class="dropdown-divider"></div>`)
    })

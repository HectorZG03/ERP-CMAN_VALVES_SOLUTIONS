document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll(
        '[data-dashboard-animate]'
    );

    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(12px)';

        setTimeout(() => {
            element.style.transition =
                'opacity 0.45s ease, transform 0.45s ease';

            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 80);
    });

    const alerts = document.querySelectorAll(
        '[data-dashboard-alert]'
    );

    alerts.forEach((alert, index) => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(15px)';

        setTimeout(() => {
            alert.style.transition =
                'opacity 0.4s ease, transform 0.4s ease';

            alert.style.opacity = '1';
            alert.style.transform = 'translateX(0)';
        }, 250 + (index * 100));
    });
});
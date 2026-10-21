document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = [...document.querySelectorAll('[data-profile-tab]')];
    const tabPanels = [...document.querySelectorAll('[data-profile-panel]')];
    const activeTabInput = document.getElementById('active-profile-tab');
    const birthDateInput = document.getElementById('fecha_nacimiento');
    const ageInput = document.getElementById('edad');

    const activateTab = (tabName, moveFocus = false) => {
        tabButtons.forEach((button) => {
            const isActive = button.dataset.profileTab === tabName;
            button.setAttribute('aria-selected', String(isActive));
            button.tabIndex = isActive ? 0 : -1;
            button.classList.toggle('border-blue-600', isActive);
            button.classList.toggle('text-blue-700', isActive);
            button.classList.toggle('dark:border-blue-400', isActive);
            button.classList.toggle('dark:text-blue-300', isActive);
            button.classList.toggle('border-transparent', !isActive);
            button.classList.toggle('text-gray-500', !isActive);
            button.classList.toggle('dark:text-gray-400', !isActive);
        });

        tabPanels.forEach((panel) => {
            const isActive = panel.dataset.profilePanel === tabName;
            panel.hidden = !isActive;
            panel.classList.toggle('hidden', !isActive);
        });

        if (activeTabInput) {
            activeTabInput.value = tabName;
        }

        if (moveFocus) {
            document.querySelector(`[data-profile-tab="${tabName}"]`)?.focus();
        }
    };

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => activateTab(button.dataset.profileTab));

        button.addEventListener('keydown', (event) => {
            let nextIndex = index;

            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % tabButtons.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
            } else {
                return;
            }

            event.preventDefault();
            activateTab(tabButtons[nextIndex].dataset.profileTab, true);
        });
    });

    const calculateAge = () => {
        if (!birthDateInput || !ageInput || !birthDateInput.value) {
            if (ageInput) ageInput.value = '';
            return;
        }

        const [year, month, day] = birthDateInput.value.split('-').map(Number);
        const today = new Date();

        let age = today.getFullYear() - year;
        const birthdayHasNotOccurred =
            today.getMonth() + 1 < month ||
            (today.getMonth() + 1 === month && today.getDate() < day);

        if (birthdayHasNotOccurred) {
            age--;
        }

        ageInput.value = age >= 0 ? age : '';
    };

    birthDateInput?.addEventListener('input', calculateAge);
    birthDateInput?.addEventListener('change', calculateAge);
    calculateAge();

    const requestedTab = activeTabInput?.value;
    const validTab = tabButtons.some((button) => button.dataset.profileTab === requestedTab)
        ? requestedTab
        : 'laboral';

    activateTab(validTab);
});
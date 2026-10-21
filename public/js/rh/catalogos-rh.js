document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('catalogos-rh');

    if (!root) {
        return;
    }

    const alertBox = document.getElementById('catalogos-alert');
    const csrfToken = root.dataset.csrf;
    const successStorageKey = 'catalogos-rh-success-message';
    const tabStorageKey = 'catalogos-rh-active-tab';

    const labels = {
        area: 'área',
        departamento: 'departamento',
        puesto: 'puesto',
    };

    // Pestañas
    const tabButtons = [...root.querySelectorAll('[data-catalog-tab]')];
    const tabPanels = [...root.querySelectorAll('[data-catalog-panel]')];

    function activateTab(tabName) {
        const tabExists = tabButtons.some(
            (button) => button.dataset.catalogTab === tabName
        );

        if (!tabExists) {
            tabName = tabButtons[0]?.dataset.catalogTab || 'areas';
        }

        tabButtons.forEach((button) => {
            const active = button.dataset.catalogTab === tabName;

            button.setAttribute('aria-selected', String(active));
            button.tabIndex = active ? 0 : -1;

            button.classList.toggle('bg-blue-600', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('border-blue-600', active);

            button.classList.toggle('bg-white', !active);
            button.classList.toggle('text-gray-700', !active);
            button.classList.toggle('border-gray-300', !active);
            button.classList.toggle('dark:bg-gray-800', !active);
            button.classList.toggle('dark:text-gray-200', !active);
            button.classList.toggle('dark:border-gray-600', !active);
        });

        tabPanels.forEach((panel) => {
            const active = panel.dataset.catalogPanel === tabName;

            panel.hidden = !active;
            panel.classList.toggle('hidden', !active);
        });

        sessionStorage.setItem(tabStorageKey, tabName);
    }

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            activateTab(button.dataset.catalogTab);
        });

        button.addEventListener('keydown', (event) => {
            let nextIndex = null;

            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % tabButtons.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = tabButtons.length - 1;
            }

            if (nextIndex === null) {
                return;
            }

            event.preventDefault();
            tabButtons[nextIndex].focus();
            activateTab(tabButtons[nextIndex].dataset.catalogTab);
        });
    });

    activateTab(sessionStorage.getItem(tabStorageKey) || 'areas');

    // Alertas
    function showMessage(message, type = 'success') {
        if (!alertBox) {
            return;
        }

        alertBox.textContent = message;
        alertBox.classList.remove(
            'hidden',
            'border-emerald-200',
            'bg-emerald-50',
            'text-emerald-800',
            'dark:border-emerald-900/60',
            'dark:bg-emerald-950/30',
            'dark:text-emerald-300',
            'border-red-200',
            'bg-red-50',
            'text-red-800',
            'dark:border-red-900/60',
            'dark:bg-red-950/30',
            'dark:text-red-300'
        );

        if (type === 'error') {
            alertBox.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-800',
                'dark:border-red-900/60',
                'dark:bg-red-950/30',
                'dark:text-red-300'
            );
        } else {
            alertBox.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-800',
                'dark:border-emerald-900/60',
                'dark:bg-emerald-950/30',
                'dark:text-emerald-300'
            );
        }
    }

    const previousSuccess = sessionStorage.getItem(successStorageKey);

    if (previousSuccess) {
        sessionStorage.removeItem(successStorageKey);
        showMessage(previousSuccess);
    }

    function getResponseMessage(payload, fallback) {
        if (payload && typeof payload.message === 'string') {
            return payload.message;
        }

        if (payload && payload.errors) {
            const firstError = Object.values(payload.errors)
                .flat()
                .find((error) => typeof error === 'string');

            if (firstError) {
                return firstError;
            }
        }

        return fallback;
    }

    async function readResponse(response) {
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            return response.json();
        }

        return {
            message: 'El servidor devolvió una respuesta inesperada. Recarga la página e inténtalo de nuevo.',
        };
    }

    // Cambia el formulario entre alta y edición.
    function setFormMode(form, mode, id = null) {
        const entity = form.dataset.entity;
        const methodField = form.querySelector('[data-method-field]');
        const submitButton = form.querySelector('[data-submit-label]');
        const cancelButton = form.querySelector('[data-cancel-edit]');
        const nameInput = form.querySelector('[data-name-input]');

        if (mode === 'edit' && id) {
            const updateUrl = form.dataset.updateUrl;

            if (!updateUrl || !updateUrl.includes('__ID__')) {
                showMessage(
                    `No está configurada la URL para actualizar ${labels[entity] || 'este registro'}.`,
                    'error'
                );
                return false;
            }

            form.dataset.editingId = id;
            form.action = updateUrl.replace(
                '__ID__',
                encodeURIComponent(id)
            );

            if (methodField) {
                methodField.value = 'PUT';
            }

            if (submitButton) {
                submitButton.innerHTML =
                    '<i class="fas fa-save" aria-hidden="true"></i> Guardar cambios';
            }

            cancelButton?.classList.remove('hidden');
            nameInput?.focus();

            return true;
        }

        delete form.dataset.editingId;
        form.action = form.dataset.storeUrl;

        if (methodField) {
            methodField.value = 'POST';
        }

        if (submitButton) {
            const buttonLabels = {
                area: 'Agregar área',
                departamento: 'Agregar departamento',
                puesto: 'Agregar puesto',
            };

            submitButton.innerHTML =
                `<i class="fas fa-plus" aria-hidden="true"></i> ${buttonLabels[entity] || 'Agregar'}`;
        }

        cancelButton?.classList.add('hidden');
        form.reset();

        return true;
    }

    // Editar, cancelar edición y eliminar.
    root.addEventListener('click', async (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        const editButton = target.closest('[data-edit]');

        if (editButton) {
            const entity = editButton.dataset.edit;
            const form = document.getElementById(`${entity}-form`);

            if (!form) {
                return;
            }

            const nameInput = form.querySelector('[data-name-input]');
            const parentSelect = form.querySelector('[data-parent-select]');

            if (nameInput) {
                nameInput.value = editButton.dataset.name || '';
            }

            if (parentSelect) {
                parentSelect.value = editButton.dataset.parentId || '';
            }

            if (setFormMode(form, 'edit', editButton.dataset.id)) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            return;
        }

        const cancelButton = target.closest('[data-cancel-edit]');

        if (cancelButton) {
            const form = cancelButton.closest('[data-catalog-form]');

            if (form) {
                setFormMode(form, 'create');
            }

            return;
        }

        const deleteButton = target.closest('[data-delete]');

        if (!deleteButton) {
            return;
        }

        const entity = deleteButton.dataset.delete;
        const name = deleteButton.dataset.name || `este ${labels[entity] || 'registro'}`;

        if (!window.confirm(`¿Eliminar ${labels[entity] || 'registro'} "${name}"?`)) {
            return;
        }

        deleteButton.disabled = true;

        try {
            const response = await fetch(deleteButton.dataset.deleteUrl, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const payload = await readResponse(response);

            if (!response.ok) {
                showMessage(
                    getResponseMessage(
                        payload,
                        `No se pudo eliminar ${labels[entity] || 'el registro'}.`
                    ),
                    'error'
                );

                deleteButton.disabled = false;
                return;
            }

            sessionStorage.setItem(
                successStorageKey,
                getResponseMessage(payload, 'Registro eliminado correctamente.')
            );

            window.location.reload();
        } catch (error) {
            showMessage(
                'No fue posible comunicarse con el servidor. Intenta nuevamente.',
                'error'
            );

            deleteButton.disabled = false;
        }
    });

    // Crear y actualizar.
    root.querySelectorAll('[data-catalog-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const entity = form.dataset.entity;
            const editingId = form.dataset.editingId;
            const methodField = form.querySelector('[data-method-field]');
            const submitButton = form.querySelector('[data-submit-label]');

            // Determina siempre la URL y el método justo antes de enviar.
            if (editingId) {
                const updateUrl = form.dataset.updateUrl;

                if (!updateUrl || !updateUrl.includes('__ID__')) {
                    showMessage(
                        `No está configurada la URL para actualizar ${labels[entity] || 'este registro'}.`,
                        'error'
                    );
                    return;
                }

                form.action = updateUrl.replace(
                    '__ID__',
                    encodeURIComponent(editingId)
                );

                if (methodField) {
                    methodField.value = 'PUT';
                }
            } else {
                if (!form.dataset.storeUrl) {
                    showMessage('No está configurada la URL para guardar este registro.', 'error');
                    return;
                }

                form.action = form.dataset.storeUrl;

                if (methodField) {
                    methodField.value = 'POST';
                }
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('cursor-wait', 'opacity-60');
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const payload = await readResponse(response);

                if (!response.ok) {
                    showMessage(
                        getResponseMessage(
                            payload,
                            'Revisa los datos e intenta nuevamente.'
                        ),
                        'error'
                    );

                    return;
                }

                sessionStorage.setItem(
                    successStorageKey,
                    getResponseMessage(payload, 'Cambios guardados correctamente.')
                );

                window.location.reload();
            } catch (error) {
                showMessage(
                    'No fue posible comunicarse con el servidor. Intenta nuevamente.',
                    'error'
                );
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('cursor-wait', 'opacity-60');
                }
            }
        });
    });
});
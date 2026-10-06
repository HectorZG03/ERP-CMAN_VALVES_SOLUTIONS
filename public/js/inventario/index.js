document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('search');
    const filterInput = document.getElementById('filter-input');
    const inventoryTable = document.getElementById('inventory-table');
    const resultCounter = document.getElementById('result-counter');
    const searchForm = document.getElementById('search-form');

    if (
        !searchInput ||
        !filterInput ||
        !inventoryTable ||
        !resultCounter ||
        !searchForm
    ) {
        return;
    }


    let searchTimeout = null;

    let currentSearch = searchInput.value.trim();

    let currentFilter = filterInput.value || 'all';


    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    function showLoading() {

        inventoryTable.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-14 text-center">

                    <div class="flex flex-col items-center justify-center">

                        <div class="h-8 w-8 animate-spin rounded-full border-2 border-gray-200 border-t-blue-600 dark:border-gray-600 dark:border-t-blue-400"></div>

                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            Buscando productos...
                        </p>

                    </div>

                </td>
            </tr>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    */

    function showError() {

        inventoryTable.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-14 text-center">

                    <div class="flex flex-col items-center justify-center">

                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">

                            <svg class="h-6 w-6"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                            </svg>

                        </div>

                        <p class="mt-3 text-sm font-medium text-red-600 dark:text-red-400">
                            No fue posible cargar los productos.
                        </p>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Intenta nuevamente.
                        </p>

                    </div>

                </td>
            </tr>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Animación
    |--------------------------------------------------------------------------
    */

    function animateRows() {

        const rows =
            document.querySelectorAll('.inventory-row');

        rows.forEach((row, index) => {

            row.style.animationDelay =
                `${index * 0.03}s`;

            row.classList.add(
                'inventory-row-enter'
            );

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Búsqueda AJAX
    |--------------------------------------------------------------------------
    */

    async function performSearch(
        searchTerm,
        filter
    ) {

        showLoading();


        const ajaxUrl =
            searchForm.dataset.ajaxUrl;

        const csrfToken =
            searchForm.dataset.csrfToken;


        if (!ajaxUrl || !csrfToken) {

            console.error(
                'No se encontraron los datos necesarios para realizar la búsqueda AJAX.'
            );

            showError();

            return;
        }


        try {

            const response =
                await fetch(
                    ajaxUrl,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'X-CSRF-TOKEN':
                                csrfToken,
                        },

                        body: JSON.stringify({
                            search:
                                searchTerm.trim(),

                            filter:
                                filter || 'all',
                        }),
                    }
                );


            if (!response.ok) {

                const errorText =
                    await response.text();

                console.error(
                    'Respuesta del servidor:',
                    errorText
                );

                throw new Error(
                    `HTTP ${response.status}`
                );
            }


            const data =
                await response.json();


            if (
                !data ||
                typeof data.html === 'undefined'
            ) {

                throw new Error(
                    'La respuesta del servidor no tiene el formato esperado.'
                );
            }


            inventoryTable.innerHTML =
                data.html;


            const count =
                Number(data.count ?? 0);


            resultCounter.textContent =
                `${count} ${
                    count === 1
                        ? 'resultado encontrado'
                        : 'resultados encontrados'
                }`;


            animateRows();

        } catch (error) {

            console.error(
                'Error al buscar inventario:',
                error
            );

            showError();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Cambiar filtro
    |--------------------------------------------------------------------------
    */

    function changeFilter(filter) {

        currentFilter = filter;

        filterInput.value = filter;


        if (currentSearch) {

            performSearch(
                currentSearch,
                currentFilter
            );

            return;
        }


        const url =
            new URL(
                window.location.href
            );


        url.searchParams.set(
            'filter',
            filter
        );


        url.searchParams.delete(
            'search'
        );


        window.location.href =
            url.toString();
    }


    /*
    |--------------------------------------------------------------------------
    | Búsqueda en tiempo real
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener(
        'input',
        () => {

            const searchTerm =
                searchInput.value.trim();


            currentSearch =
                searchTerm;


            clearTimeout(
                searchTimeout
            );


            if (searchTerm === '') {

                searchForm.submit();

                return;
            }


            searchTimeout =
                setTimeout(
                    () => {

                        performSearch(
                            searchTerm,
                            currentFilter
                        );

                    },
                    500
                );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Enter
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener(
        'keydown',
        (event) => {

            if (event.key !== 'Enter') {
                return;
            }


            event.preventDefault();


            clearTimeout(
                searchTimeout
            );


            searchForm.submit();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Filtro: todos
    |--------------------------------------------------------------------------
    */

    const filterAll =
        document.getElementById(
            'filter-all'
        );


    if (filterAll) {

        filterAll.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                changeFilter('all');
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Filtro: con stock
    |--------------------------------------------------------------------------
    */

    const filterStock =
        document.getElementById(
            'filter-stock'
        );


    if (filterStock) {

        filterStock.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                changeFilter(
                    'with-stock'
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Filtro: sin stock
    |--------------------------------------------------------------------------
    */

    const filterNoStock =
        document.getElementById(
            'filter-no-stock'
        );


    if (filterNoStock) {

        filterNoStock.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                changeFilter(
                    'without-stock'
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Animación inicial
    |--------------------------------------------------------------------------
    */

    animateRows();

});
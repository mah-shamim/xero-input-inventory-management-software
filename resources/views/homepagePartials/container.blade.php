<section class="tw-py-16">
    <div class="tw-flex tw-flex-wrap tw-pt-16">
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <span class="material-icons tw-text-7xl tw-text-red-500">add_shopping_cart</span>
                @endslot
                @slot('title')
                    Increase Sale
                @endslot
                @slot('description')
                    Expand your business using our online inventory management software to track sales, warehouse, and stocks.
                @endslot
            @endcomponent
        </div>
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <span class="material-icons tw-text-7xl tw-text-blue-500">store_mall_directory</span>
                @endslot
                @slot('title')
                    Manage Warehouse
                @endslot
                @slot('description')
                    Manage vendors and suppliers efficiently with our detailed inventory management system.
                @endslot
            @endcomponent
        </div>
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <span class="material-icons tw-text-7xl tw-text-green-500">bar_chart</span>
                @endslot
                @slot('title')
                    Detail Report
                @endslot
                @slot('description')
                    Accurate inventory status and stock-in-hand information any time. Sell by square feet or unit, sell by boxes or pieces.
                @endslot
            @endcomponent
        </div>
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <i class="material-icons tw-text-7xl tw-text-orange-500">shopping_basket</i>
                @endslot
                @slot('title')
                    Manage Purchase
                @endslot
                @slot('description')
                    Expand your business using our online inventory management software to track sales, warehouse, and stocks.
                @endslot
            @endcomponent
        </div>
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <span class="material-icons tw-text-7xl tw-text-teal-500">trending_up</span>
                @endslot
                @slot('title')
                    Data Visualization
                @endslot
                @slot('description')
                    Manage vendors and suppliers efficiently with our detailed inventory management system.
                @endslot
            @endcomponent
        </div>
        <div class="tw-w-full md:tw-w-1/3 tw-p-4">
            @component('components.card')
                @slot('icon')
                    <span class="material-icons tw-text-7xl tw-text-indigo-500">visibility</span>
                @endslot
                @slot('title')
                    End-to-End Tracking
                @endslot
                @slot('description')
                    Accurate inventory status and stock-in-hand information any time. Sell by square feet or unit, sell by boxes or pieces.
                @endslot
            @endcomponent
        </div>
    </div>
</section>


<section class="tw-px-4">
    <h3 class="tw-text-center tw-py-6">What's more </h3>
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-shadow hover:tw-bg-gray-100 dark:tw-border-gray-700 dark:tw-bg-gray-800 dark:hover:tw-bg-gray-700">
                <span class="material-icons tw-text-7xl tw-text-blue-500 tw-p-4">money_off_csred</span>
                <div class="tw-flex tw-flex-col tw-justify-between tw-p-4 tw-leading-normal tw-w-full">
                    <h5 class="tw-mb-2 tw-text-2xl tw-font-bold tw-tracking-tight tw-text-gray-900 dark:tw-text-white">Expenses</h5>
                    <p class="tw-mb-3 tw-font-normal tw-text-gray-700 dark:tw-text-gray-400">
                        The Expense module in XeroInput allows you to manage expenses by entering details such as bill number, amount, and payee.
                    </p>
                </div>
            </div>
        </div>
        <div>
            <div class="tw-flex tw-items-center tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-shadow hover:tw-bg-gray-100 dark:tw-border-gray-700 dark:tw-bg-gray-800 dark:hover:tw-bg-gray-700">
                <span class="material-icons tw-text-7xl tw-text-green-500 tw-p-4">people_alt</span>
                <div class="tw-flex tw-flex-col tw-justify-between tw-p-4 tw-leading-normal tw-w-full">
                    <h5 class="tw-mb-2 tw-text-2xl tw-font-bold tw-tracking-tight tw-text-gray-900 dark:tw-text-white">Payroll</h5>
                    <p class="tw-mb-3 tw-font-normal tw-text-gray-700 dark:tw-text-gray-400">
                        The Payroll module in XeroInput manages employee salaries, deductions, and benefits efficiently and accurately.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>




{{--content area--}}

<section class="tw-py-24">
    <div class="tw-relative">
        <img src="/images/home/double-dot.svg" alt="mdit inventory" class="dot-position-right">
        <div class="tw-mb-24 tw-flex tw-flex-col tw-items-center">
            <h3 class="tw-text-center tw-text-2xl tw-font-bold">Why You Should Use XeroInput?</h3>
            <span class="tw-w-24 tw-p-1 tw-bg-red-500 tw-rounded"></span>
        </div>
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-8">
            <div class="tw-w-full md:tw-w-1/2 tw-flex tw-justify-center tw-items-center">
                <div class="tw-px-12">
                    <ul class="tw-list-disc tw-list-inside tw-text-gray-700">
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Efficient Stock Management</h3>
                            <p>Optimize inventory levels to meet demand and set up automated reordering to avoid stockouts.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">User-Friendly Interface</h3>
                            <p>Enjoy an easy-to-navigate interface that requires minimal training, with customizable dashboards.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Detailed Reporting and Analytics</h3>
                            <p>Generate comprehensive reports on stock levels, sales, and purchase orders, and use analytics to make informed decisions.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Scalable Solutions</h3>
                            <p>Grow with your business with scalable features and manage multiple warehouses from a single platform.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Seamless Integration</h3>
                            <p>Integrate with popular accounting and e-commerce platforms, and access data programmatically with our API.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Enhanced Security</h3>
                            <p>Benefit from advanced security measures to keep your data safe, and control access with customizable user permissions.</p>
                        </li>
                        <li class="tw-list-none tw-mb-4">
                            <h3 class="tw-font-semibold tw-text-lg">Cost-Effective</h3>
                            <p>Reduce operational costs and improve efficiency with affordable plans that offer great value for your investment.</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tw-w-full md:tw-w-1/2 tw-flex tw-justify-center tw-items-center tw-text-center">
                <img src="/images/home/side-graph.png" class="tw-w-full tw-h-auto" alt="XeroInput features">
            </div>
        </div>
    </div>
</section>


<section>
    <div class="tw-bg-red-500 tw-text-white tw-text-xs tw-py-2 tw-rounded-br-lg tw-rounded-bl-lg">
        <div class="tw-flex tw-justify-between tw-items-center">
            <p class="tw-m-0">{{ config('app.name', 'Laravel') }} all rights reserved</p>
            <div class="tw-m-0 tw-flex tw-items-center">
                <a href="#" class="tw-mx-1">
                    <img width="18" src="/images/home/social/facebook.svg" alt="Facebook" title="Facebook">
                </a>
                <a href="#" class="tw-mx-1">
                    <img width="18" src="/images/home/social/twitter.svg" alt="Twitter" title="Twitter">
                </a>
                <a href="#" class="tw-mx-1">
                    <img width="18" src="/images/home/social/instagram.svg" alt="Instagram" title="Instagram">
                </a>
            </div>
            <div class="tw-m-0">
                <a href="" class="tw-text-white tw-mr-2">Privacy Policy</a>
                <a href="" class="tw-text-white">Terms & Conditions</a>
            </div>
        </div>
    </div>
</section>


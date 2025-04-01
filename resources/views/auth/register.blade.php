@extends('layouts.guestApp')
@section('content')
    <div class="tw-container tw-mx-auto tw-w-2/3">
        @include('homepagePartials.navbar')
        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
            <div>
                <h3>Free Inventory Software | Helping small business to grow</h3>
                <ul class="tw-list-disc tw-list-inside tw-space-y-2">
                    <li>
                        <b>Purchase Management:</b>
                        <p class="tw-ml-5">Efficiently handle procurement. Track orders, manage suppliers, and monitor inventory levels.</p>
                    </li>
                    <li>
                        <b>Sales Management:</b>
                        <p class="tw-ml-5">Create and track sales orders, invoices, and customer info effortlessly.</p>
                    </li>
                    <li>
                        <b>Expense Tracking:</b>
                        <p class="tw-ml-5">Monitor and categorize your expenditures to maintain financial control.</p>
                    </li>
                    <li>
                        <b>Income Tracking:</b>
                        <p class="tw-ml-5">Easily track income and get insights into your revenue for better financial management.</p>
                    </li>
                    <li>
                        <b>Salary Management:</b>
                        <p class="tw-ml-5">Simplify payroll with accurate, timely payments and comprehensive records.</p>
                    </li>
                    <li>
                        <b>Employee Management:</b>
                        <p class="tw-ml-5">Centralize employee details, job roles, and performance.</p>
                    </li>
                    <li>
                        <b>Reporting and Analytics:</b>
                        <p class="tw-ml-5">Use powerful tools to generate reports and gain insights for data-driven decisions.</p>
                    </li>
                </ul>
            </div>
            <!-- ... -->
            <div class="tw-rounded tw-p-3 tw-shadow-md tw-bg-gray-100">
                @include('auth.partials.register_form')
            </div>
        </div>
    </div>
@endsection
<style>
    
</style>
<div class="px-4">
         <a href="{{ route('admin.dashboard') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
                                  @if (Route::currentRouteName() === 'admin.dashboard') bg-gray-800 text-white shadow-inner @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <!-- icon -->
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                 <path d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                 <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
             </svg>
             Dashboard
         </a>

         <div class="mt-6 mb-2 text-xs uppercase text-gray-500 font-semibold">Users</div>

         <a href="{{ route('admin.users.customers') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.users.customers') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM16 17v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1h12z" />
             </svg>
             Customers
         </a>

         <a href="{{ route('admin.users.drivers') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.users.drivers') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M3 13l1-5h12l1 5H3z" />
                 <path d="M7 16a1 1 0 100-2 1 1 0 000 2zM13 16a1 1 0 100-2 1 1 0 000 2z" />
             </svg>
             Drivers
         </a>

         <div class="mt-6 mb-2 text-xs uppercase text-gray-500 font-semibold">Management</div>

         <a href="{{ route('admin.verifications.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.verifications.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M9 12l2 2 4-6" />
             </svg>
             Driver Verification
         </a>

         <a href="{{ route('admin.services.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.services.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M4 3h12v4H4zM4 9h12v8H4z" />
             </svg>
             Services
         </a>

         <a href="{{ route('admin.jobs.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.jobs.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M6 2h8v2H6zM4 7h12v9H4z" />
             </svg>
             Jobs
         </a>

         <a href="{{ route('admin.transactions.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.transactions.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M3 3h14v4H3zM3 9h14v8H3z" />
             </svg>
             Transactions
         </a>

         <!-- ✅ ADD THESE TWO NEW MENU ITEMS -->

         <a href="{{ route('admin.bank-details.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.bank-details.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                 <path fill-rule="evenodd"
                     d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                     clip-rule="evenodd" />
             </svg>
             Bank Details
             @if ($pendingBankDetailsCount ?? 0 > 0)
                 <span
                     class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingBankDetailsCount }}</span>
             @endif
         </a>

         <a href="{{ route('admin.withdrawals.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.withdrawals.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path
                     d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                 <path fill-rule="evenodd"
                     d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                     clip-rule="evenodd" />
             </svg>
             Withdrawals
             @if ($pendingWithdrawalsCount ?? 0 > 0)
                 <span
                     class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingWithdrawalsCount }}</span>
             @endif
         </a>
         <!-- 🔔 NOTIFICATIONS MENU ITEM - ADD THIS -->
         <a href="{{ route('admin.notifications.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.notifications.index' ||
                     Route::currentRouteName() === 'admin.notifications.create') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path
                     d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
             </svg>
             Notifications
         </a>

         <div class="mt-6 mb-2 text-xs uppercase text-gray-500 font-semibold">Settings</div>

         <a href="{{ route('admin.settings.index') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.settings.index') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="currentColor" viewBox="0 0 20 20">
                 <path
                     d="M11.3 1.046a1 1 0 00-2.6 0L7.3 3.17a8.015 8.015 0 00-1.77.515l-2.2-.88a1 1 0 00-1.21.33L.32 6.1a1 1 0 00.33 1.21l.88 2.2c-.17.58-.29 1.18-.33 1.77L1.046 11.3a1 1 0 000 2.6L3.17 12.7c.17.58.39 1.14.66 1.66l-.88 2.2a1 1 0 00.33 1.21l2.2 1.55a1 1 0 001.21-.33l.88-2.2c.52.27 1.08.49 1.66.66L11.3 22.954a1 1 0 002.6 0l1.04-2.124c.58-.17 1.14-.39 1.66-.66l.88 2.2a1 1 0 001.21.33l2.2-1.55a1 1 0 00.33-1.21l-.88-2.2c.27-.52.49-1.08.66-1.66L22.954 13.9a1 1 0 000-2.6L20.83 9.7c-.17-.58-.39-1.14-.66-1.66l.88-2.2a1 1 0 00-.33-1.21L18.5 2.06a1 1 0 00-1.21.33l-.88 2.2a8.015 8.015 0 00-1.66-.66L11.3 1.046z" />
             </svg>
             Platform Settings
         </a>

         <div class="mt-6 mb-2 text-xs uppercase text-gray-500 font-semibold">Account</div>
         <a href="{{ route('admin.account.edit') }}"
             class="group flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors @if (Route::currentRouteName() === 'admin.account.edit') bg-gray-800 text-white @else text-gray-200 hover:bg-gray-800 hover:text-white @endif">
             <svg class="w-5 h-5 text-gray-300" ...></svg>
             My Account
         </a>


         <form method="POST" action="{{ route('admin.logout') }}" class="px-0">
             @csrf
             <button type="submit"
                 class="w-full text-left px-3 py-2 rounded-md text-sm text-gray-200 hover:bg-gray-800 hover:text-white transition-colors">
                 <svg class="w-5 h-5 inline-block mr-2 align-middle text-gray-300" fill="currentColor"
                     viewBox="0 0 20 20">
                     <path d="M3 3h8v4H3zM3 9h8v8H3z" />
                 </svg>
                 Logout
             </button>
         </form>
     </div>


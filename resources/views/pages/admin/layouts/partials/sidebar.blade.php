<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="#">
                    <div class="brand-logo">
                        <img class="logo" src="{{ asset('images/cava-logo2-black.png') }}"
                            style="width:150px; height:auto; margin-top:-20px" />
                    </div>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="bx bx-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="">
            <li class=" navigation-header"><span>Xendit</span></li>

            <li class=" nav-item"><a href="#">
                    <i class="bx bx-grid-alt"></i>
                    <span class="menu-title" data-i18n="XenPlatform">XenPlatform</span>
                </a>
                <ul class="menu-content">
                    <li @if (Request::segment(2) == 'xen_platform' && Request::segment(3) == 'transactions') class="active" @endif>
                        <a href="{{ route('admin.xen_platform.transactions.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="partner-accounts">Transactions</span></a>
                    </li>
                    <li @if (Request::segment(2) == 'xen_platform' && Request::segment(3) == 'balance') class="active" @endif>
                        <a href="{{ route('admin.xen_platform.balance.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="partner-accounts">Balance</span></a>
                    </li>
                    <li @if (Request::segment(2) == 'xen_platform' && Request::segment(3) == 'partner-account') class="active" @endif>
                        <a href="{{ route('admin.xen_platform.partner-account.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="partner-accounts">Owner Accounts</span></a>
                    </li>
                    <li @if (Request::segment(2) == 'xen_platform' && Request::segment(3) == 'split-payments') class="active" @endif>
                        <a href="{{ route('admin.xen_platform.split-payments.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="split-payments">Split Payments</span></a>
                    </li>
                    <li @if (Request::segment(2) == 'xen_platform' && Request::segment(3) == 'disbursement') class="active" @endif>
                        <a href="{{ route('admin.xen_platform.disbursement.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="partner-accounts">Disbursements</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#">
                    <i class="bx bx-user-check"></i>
                    <span class="menu-title" data-i18n="OwnerManagement">Owner Management</span>
                </a>
                <ul class="menu-content">
                    <li @if (Request::segment(2) == 'owner-list') class="active" @endif>
                        <a href="{{ route('admin.owner-list.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="OwnerList">Owner List</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item @if (Request::segment(2) == 'owner-verification') active @endif">
                <a href="{{ route('admin.owner-verification') }}">
                    <i class="bx bx-user"></i>
                    <span class="menu-title" data-i18n="OwnerVerification">Owner Verification</span>
                </a>
            </li>
            <li class=" nav-item"><a href="#">
                    <i class="bx bxs-conversation"></i>
                    <span class="menu-title" data-i18n="XenPlatform">Messages & Notif</span>
                </a>
                <ul class="menu-content">
                    <li @if (Request::segment(2) == 'message-notification' && Request::segment(3) == 'messages') class="active" @endif>
                        <a href="{{ route('admin.message-notification.messages.index') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item" data-i18n="partner-accounts">Messages</span></a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

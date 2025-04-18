<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                {{--<li class="menu-title">Main</li>--}}

                <li>
                    <a href="{{ route('dashboard') }}" class=""><i
                                class="ti-home"></i><span> Dashboard </span></a>
                </li>

                @can('po.index')
                <li>
                    <a href="{{ route('purchase-orders.index') }}" class=" {{ Request::is('purchase-orders*') ? 'active' : '' }}"><i class="ti-download"></i>
                        <span> Purchase Orders </span></a>

                    {{--@can('create.purchase-orders')--}}
                    {{--<ul>--}}
                        {{--<li>--}}
                            {{--<a href="{{ route('purchase-orders.create') }}"><span>Create</span></a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                    {{--@endcan--}}

                </li>
                @endcan

                @can('so.index')
                <li>
                    <a href="{{ route('sale-orders.index') }}" class=" {{ Request::is('sale-orders*') ? 'active' : '' }}"><i class="ti-share"></i>
                        <span> Sale Orders </span></a>
                </li>
                @endcan

                @can('batches.index')
                    <li class="has_sub">
                        <a href="javascript:void(0);" class=" {{ Request::is('batches*') ? 'active subdrop' : '' }}"><i class="ti-layers-alt"></i><span> Inventory </span>
                            <span class="menu-arrow"></span></a>

                        <ul class="list-unstyled" style="display: {{ Request::is('batches*') ? 'block' : 'none' }};">
                            <li class=""><a class="" href="{{ route('batches.index') }}">Batches</a></li>
                            <li class=""><a class="" href="{{ route('vault-logs.index') }}">Vault Logs</a></li>
                            @can('batches.reconcile')
                            <li class=""><a class="" href="{{ route('batches.reconcile') }}">Reconcile</a></li>
                            @endcan
                        </ul>

                    </li>
                @endcan

                @can('accounting.receivables.index')
                {{--<li>--}}
                    {{--<a href="/accounting/receivables" class="waves-effect waves-primary{{ Request::is('vendors*') ? 'active' : '' }}"><i class="ti-money"></i>--}}
                        {{--<span> Receivables </span></a>--}}
                {{--</li>--}}

                <li class="has_sub">
                    <a href="javascript:void(0);" class=" {{ Request::is('accounting*') ? 'active subdrop' : '' }} "><i class="ti-money"></i> <span> Accounting </span>
                        <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled" style="display: {{ Request::is('accounting*') ? 'block' : 'none' }};">
                        <li class=""><a class="" href="{{ route('accounting.transactions') }}">Transactions</a></li>
                        <li class=""><a class="" href="{{ route('accounting.payables') }}">Payables</a></li>
                        <li class=""><a class="" href="{{ route('accounting.receivables') }}">Receivables</a></li>
                        <li class=""><a class="" href="{{ route('accounting.inventory-loss') }}">Inventory Loss</a></li>
                        {{--<li class=""><a class="" href="{{ route('accounting.sales_rep_commissions') }}">Sales Rep Commissions</a></li>--}}
                    </ul>
                </li>

                @endcan

                {{--@can('products.index')--}}
                {{--<li>--}}
                    {{--<a href="{{ route('products.index') }}" class=" {{ Request::is('products*') ? 'active' : '' }}"><i--}}
                                {{--class="ti-package"></i><span> Inventory </span></a>--}}

                {{--</li>--}}
                {{--@endcan--}}

                @can('transporters.index')
                    {{--<li>--}}
                        {{--<a href="{{ route('transporters.index') }}" class=" {{ Request::is('transporters*') ? 'active' : '' }}"><i--}}
                                    {{--class=" mdi mdi-car"></i><span> In-Transit </span></a>--}}
                    {{--</li>--}}
                @endcan

                @can('prepacklogs.show')
                <li>
                    <a href="{{ route('prepack-logs.index') }}" class=" {{ Request::is('prepack-logs*') ? 'active' : '' }}"><i
                                class="ion-loop"></i><span> Pre-pack Logs </span></a>
                </li>
                @endcan

                @can('users.index')
                <li class="has_sub">
                    {{--<a href="{{ route('users.index') }}" class=" {{ Request::is('users*') ? 'active' : '' }}"><i--}}
                                {{--class=" mdi mdi-account-multiple-outline"></i><span> User Management </span></a>--}}

                    <a href="javascript:void(0);" class=" {{ Request::is('users*') ? 'active subdrop' : '' }} "><i class="mdi mdi-account-multiple-outline"></i> <span> Users </span>
                        <span class="menu-arrow"></span></a>
                    <ul class="list-unstyled" style="display: {{ Request::is('accounting*') ? 'block' : 'none' }};">
                        <li class=""><a class="" href="{{ route('users.index') }}">All</a></li>
                        <li class=""><a class="" href="{{ route('customers.index') }}">Customers</a></li>
                        {{--<li class=""><a class="" href="{{ route('users.list', 'vendors') }}">Vendors</a></li>--}}
                        {{--<li class=""><a class="" href="{{ route('users.list', 'sales_reps') }}">Sales Reps</a></li>--}}
                    </ul>

                </li>
                @endcan



                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void();" class="waves-effect"><i class=""></i>--}}
                        {{--<span>Users</span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul>--}}
                        {{--<li><a href="/users/all"><span>View All</span></a></li>--}}
                        {{--<li><a href="/users/create"><span>Create</span></a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li>--}}
                    {{--<a href="/reports/" class="waves-effect waves-primary{{ Request::is('reports*') ? 'active' : '' }}"><i--}}
                                {{--class=""></i><span> Reports </span></a>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-paint-bucket"></i> <span> UI Kit </span>--}}
                        {{--<span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="ui-buttons.html">Buttons</a></li>--}}
                        {{--<li><a href="ui-cards.html">Cards</a></li>--}}
                        {{--<li><a href="ui-portlets.html">Portlets</a></li>--}}
                        {{--<li><a href="ui-checkbox-radio.html">Checkboxs-Radios</a></li>--}}
                        {{--<li><a href="ui-tabs.html">Tabs & Accordions</a></li>--}}
                        {{--<li><a href="ui-modals.html">Modals</a></li>--}}
                        {{--<li><a href="ui-progressbars.html">Progress Bars</a></li>--}}
                        {{--<li><a href="ui-notification.html">Notification</a></li>--}}
                        {{--<li><a href="ui-bootstrap.html">BS Elements</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li>--}}
                    {{--<a href="typography.html" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-infinite"></i><span> Typography </span><span--}}
                                {{--class="badge badge-pink pull-right">1</span></a>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-light-bulb"></i><span> Components </span> <span class="menu-arrow"></span> </a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="components-grid.html">Grid</a></li>--}}
                        {{--<li><a href="components-carousel.html">Carousel</a></li>--}}
                        {{--<li><a href="components-widgets.html">Widgets</a></li>--}}
                        {{--<li><a href="components-nestable-list.html">Nesteble</a></li>--}}
                        {{--<li><a href="components-range-sliders.html">Range Sliders </a></li>--}}
                        {{--<li><a href="components-sweet-alert.html">Sweet Alerts </a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-spray"></i>--}}
                        {{--<span> Icons </span> <span class="menu-arrow"></span> </a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="icons-glyphicons.html">Glyphicons</a></li>--}}
                        {{--<li><a href="icons-materialdesign.html">Material Design</a></li>--}}
                        {{--<li><a href="icons-themifyicon.html">Themify Icons</a></li>--}}
                        {{--<li><a href="icons-ionicons.html">Ion Icons</a></li>--}}
                        {{--<li><a href="icons-fontawesome.html">Font awesome</a></li>--}}
                        {{--<li><a href="icons-weather.html">Weather Icons</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-pencil-alt"></i><span> Forms </span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="form-elements.html">General Elements</a></li>--}}
                        {{--<li><a href="form-advanced.html">Advanced Form</a></li>--}}
                        {{--<li><a href="form-validation.html">Form Validation</a></li>--}}
                        {{--<li><a href="form-wizard.html">Form Wizard</a></li>--}}
                        {{--<li><a href="form-wysiwig.html">WYSIWYG Editor</a></li>--}}
                        {{--<li><a href="form-summernote.html">Summernote</a></li>--}}
                        {{--<li><a href="form-uploads.html">Multiple File Upload</a></li>--}}
                        {{--<li><a href="form-xeditable.html">X-editable</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="menu-title">More</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-menu-alt"></i><span> Tables </span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="tables-basic.html">Basic Tables</a></li>--}}
                        {{--<li><a href="tables-datatable.html">Data Table</a></li>--}}
                        {{--<li><a href="tables-editable.html">Editable Table</a></li>--}}
                        {{--<li><a href="tables-responsive.html">Responsive Table</a></li>--}}
                        {{--<li><a href="tables-tablesaw.html">Tablesaw Table</a></li>--}}
                        {{--<li><a href="tables-foo-tables.html">Foo Table</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-stats-up"></i><span> Charts </span> <span--}}
                                {{--class="badge badge-primary pull-right">8</span> </a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="chart-flot.html">Flot Chart</a></li>--}}
                        {{--<li><a href="chart-morris.html">Morris Chart</a></li>--}}
                        {{--<li><a href="chart-chartist.html">Chartist chart</a></li>--}}
                        {{--<li><a href="chart-nvd3.html">Nvd3 charts</a></li>--}}
                        {{--<li><a href="chart-chartjs.html">Chartjs charts</a></li>--}}
                        {{--<li><a href="chart-peity.html">Peity Charts</a></li>--}}
                        {{--<li><a href="chart-sparkline.html">Sparkline Charts</a></li>--}}
                        {{--<li><a href="chart-other.html">Other Chart</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-map"></i><span> Maps </span><span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="map-google.html"> Google Map</a></li>--}}
                        {{--<li><a href="map-vector.html"> Vector Map</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-email"></i><span> Mail </span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="mail-inbox.html">Inbox</a></li>--}}
                        {{--<li><a href="mail-compose.html">Compose Mail</a></li>--}}
                        {{--<li><a href="mail-read.html">View Mail</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="menu-title">Extras</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-files"></i><span> Pages </span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="pages-blank.html">Blank Page</a></li>--}}
                        {{--<li><a href="pages-login.html">Login</a></li>--}}
                        {{--<li><a href="pages-register.html">Register</a></li>--}}
                        {{--<li><a href="pages-recoverpw.html">Recover Password</a></li>--}}
                        {{--<li><a href="pages-lock-screen.html">Lock Screen</a></li>--}}
                        {{--<li><a href="pages-confirmmail.html">Confirm Mail</a></li>--}}
                        {{--<li><a href="pages-404.html">404 Error</a></li>--}}
                        {{--<li><a href="pages-500.html">500 Error</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect waves-primary"><i--}}
                                {{--class="ti-gift"></i><span> Extras </span> <span--}}
                                {{--class="badge badge-success pull-right">12</span></a>--}}
                    {{--<ul class="list-unstyled">--}}
                        {{--<li><a href="extras-profile.html">Profile</a></li>--}}
                        {{--<li><a href="extras-team.html">Team Members</a></li>--}}
                        {{--<li><a href="extras-timeline.html">Timeline</a></li>--}}
                        {{--<li><a href="extras-invoice.html">Invoice</a></li>--}}
                        {{--<li><a href="extras-calendar.html">Calendar</a></li>--}}
                        {{--<li><a href="extras-email-template.html">Email template</a></li>--}}
                        {{--<li><a href="extras-maintenance.html">Maintenance</a></li>--}}
                        {{--<li><a href="extras-coming-soon.html">Coming-soon</a></li>--}}
                        {{--<li><a href="extras-gallery.html">Gallery</a></li>--}}
                        {{--<li><a href="extras-pricing.html">Pricing</a></li>--}}
                        {{--<li><a href="extras-faq.html">FAQ</a></li>--}}
                        {{--<li><a href="extras-treeview.html">Treeview</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
                {{--<li class="has_sub">--}}
                    {{--<a href="javascript:void(0);" class="waves-effect"><i class="ti-share"></i><span>Multi Level </span> <span class="menu-arrow"></span></a>--}}
                    {{--<ul>--}}
                        {{--<li class="has_sub">--}}
                            {{--<a href="javascript:void(0);" class="waves-effect"><span>Menu Level 1.1</span>  <span class="menu-arrow"></span></a>--}}
                            {{--<ul style="">--}}
                                {{--<li><a href="javascript:void(0);"><span>Menu Level 2.1</span></a></li>--}}
                                {{--<li><a href="javascript:void(0);"><span>Menu Level 2.2</span></a></li>--}}
                                {{--<li><a href="javascript:void(0);"><span>Menu Level 2.3</span></a></li>--}}
                            {{--</ul>--}}
                        {{--</li>--}}
                        {{--<li>--}}
                            {{--<a href="javascript:void(0);"><span>Menu Level 1.2</span></a>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

            </ul>

            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
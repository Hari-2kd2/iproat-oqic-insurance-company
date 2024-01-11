<div class="navbar-default sidebar" role="navigation" id="sidebar-component">
    <div class="sidebar-nav slimscrollsidebar">
        <div class="sidebar-head">
            <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span>
            </h3>
        </div>
        <ul class="nav" id="side-menu" style="margin-top: 60px">
            <li>
                <a href="{{ url('dashboard') }}" class="waves-effect">
                    <i class="mdi mdi-home hideMenu" data-icon="v"></i>
                    <span class="hide-menu hideMenu"> {{ __('menu.dashboard') }}</span>
                </a>
            </li>
            <?php
            $sideMenu = showMenu();
            $menuItem = '';

            foreach ($sideMenu as $key => $value) {
                $menuItem .= '<li class="treeview waves-effect"><a href="javascript:void(0)" class="module"><i class="iconFontSize ' . $value['icon_class'] . ' hideMenu"></i> <span class="hide-menu hideMenu">&nbsp;' . __('menu' . '.' . str_replace(' ', '_', strtolower($value['name']))) . '<span class="fa arrow"></span></span></a>';

                if ($value['sub_menu']) {
                    $menuItem .= '<ul class="treeview-menu nav nav-second-level">';

                    foreach ($value['sub_menu'] as $menu) {
                        if ($menu['menu_url'] != '' || $menu['sub_menu']) {
                            $menuItem .= '<li><a href="' . ($menu['menu_url'] ? route($menu['menu_url']) : 'javascript:void(0)') . '"><i data-icon="/" class="linea-icon linea-basic fa-fw"></i><span class="hideMenu">' . __('menu' . '.' . str_replace(' ', '_', strtolower($menu['name']))) . '</span>' . ($menu['sub_menu'] ? '<i class="fa arrow"></i>' : '') . '</a>';

                            if ($menu['sub_menu']) {
                                $menuItem .= '<ul class="treeview-menu nav nav-third-level">';

                                foreach ($menu['sub_menu'] as $subMenu) {
                                    $menuItem .= '<li class=""><a class="hideMenu" href="' . ($subMenu['menu_url'] ? route($subMenu['menu_url']) : 'javascript:void(0)') . '"> <i class="fa fa-circle-o"></i> &nbsp;' . __('menu' . '.' . str_replace(' ', '_', strtolower($subMenu['name']))) . '</a></li>';
                                }

                                $menuItem .= '</ul>';
                            }

                            $menuItem .= '</li>';
                        }
                    }

                    $menuItem .= '</ul>';
                }

                $menuItem .= '</li>';
            }

            echo $menuItem;
            ?>

        </ul>
 </div>
</div>
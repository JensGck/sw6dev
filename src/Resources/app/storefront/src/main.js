import DevSidebar from './js/dev-sidebar.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('DevSidebar', DevSidebar, '.jego-sticky-sidebar');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}
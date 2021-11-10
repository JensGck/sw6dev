//import Plugin from 'src/plugin-system/plugin.class';
import Plugin
    from "../../../../../../../../../vendor/shopware/storefront/Resources/app/storefront/src/plugin-system/plugin.class";

export default class DevSidebar extends Plugin {

    init() {
        this.initEvents()
    }

    initEvents() {
        this.el.addEventListener('mouseover', this.onStickyBarMouseOver.bind(this))
        this.el.addEventListener('mouseout', this.onStickyBarMouseOut.bind(this))
    }

    onStickyBarMouseOver(event) {
        if (!this.el.classList.contains('animation')) {
            this.el.classList.add('animation')
        }
    }

    onStickyBarMouseOut(event) {
        if (this.el.classList.contains('animation')) {
            this.el.classList.remove('animation')
        }
    }

    moveOut() {
    }

}
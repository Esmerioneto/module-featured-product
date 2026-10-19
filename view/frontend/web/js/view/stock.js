/**
 * Esmerio Neto
 *
 * @category Egsn
 * @package Egsn_FeaturedProduct
 * @copyright Copyright (c) 2026 Esmerio Neto.
 */
define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Egsn_FeaturedProduct/stock',
            updateUrl: '',
            refreshInterval: 10000,
            qty: null,
            loading: true,
            tracks: {
                qty: true,
                loading: true
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.refresh();

            // Resume immediately when the tab becomes visible again.
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    this.refresh();
                }
            }.bind(this));

            return this;
        },

        /**
         * Fetch the salable quantity and schedule the next refresh after the response,
         * so slow responses never pile up requests. Paused while the tab is hidden.
         */
        refresh: function () {
            clearTimeout(this.timer);

            if (document.hidden || !this.updateUrl) {
                return;
            }

            $.ajax({
                url: this.updateUrl,
                type: 'GET',
                dataType: 'json',
                cache: false,
                global: false
            }).done(function (response) {
                this.qty = response.qty;
            }.bind(this)).always(function () {
                this.loading = false;
                this.timer = setTimeout(this.refresh.bind(this), this.refreshInterval);
            }.bind(this));
        },

        /**
         * @returns {Boolean}
         */
        isAvailable: function () {
            return this.qty !== null;
        }
    });
});

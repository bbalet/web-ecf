import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss'
import { Tooltip, Toast, Popover } from 'bootstrap'
import Masonry from 'masonry-layout'

import ChartDataLabels from 'chartjs-plugin-datalabels';

// register globally for all charts
document.addEventListener('chartjs:init', function (event) {
    const Chart = event.detail.Chart;
    Chart.register(ChartDataLabels);
});

require('bootstrap-icons/font/bootstrap-icons.css')


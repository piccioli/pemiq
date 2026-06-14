import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import * as polyline from '@mapbox/polyline';
window.L = L;
window.PolylineDecoder = polyline;

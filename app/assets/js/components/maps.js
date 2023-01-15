import * as am5 from "@amcharts/amcharts5";
import * as am5map from "@amcharts/amcharts5/map";
import am5geoData_worldLow from "@amcharts/amcharts5-geodata/worldLow";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import { CryptoJSAesJson } from './CryptoJSAesJson';
import $ from 'jquery';

let root = am5.Root.new("mapContainer");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
    am5themes_Animated.new(root)
]);

// Create the map chart
// https://www.amcharts.com/docs/v5/charts/map-chart/
let chart = root.container.children.push(am5map.MapChart.new(root, {
    panX: "translateX",
    panY: "translateY",
    projection: am5map.geoMercator()
}));

// Create main polygon series for countries
// https://www.amcharts.com/docs/v5/charts/map-chart/map-polygon-series/
let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
    geoJSON: am5geoData_worldLow,
    exclude: ["AQ"]
}));

polygonSeries.mapPolygons.template.setAll({
    toggleKey: "active",
    interactive: true
});

let previousPolygon;

polygonSeries.mapPolygons.template.on("active", function (active, target) {
    if (previousPolygon && previousPolygon !== target) {
        previousPolygon.set("active", false);
    }
    if (target.get("active")) {
        polygonSeries.zoomToDataItem(target.dataItem );
    }
    else {
        chart.goHome();
    }
    previousPolygon = target;
});

// Add zoom control
// https://www.amcharts.com/docs/v5/charts/map-chart/map-pan-zoom/#Zoom_control
chart.set("zoomControl", am5map.ZoomControl.new(root, {}));

// Set clicking on "water" to zoom out
chart.chartContainer.get("background").events.on("click", function () {
    chart.goHome();
})

// Make stuff animate on load
chart.appear(1000, 100);

function htmlDecode(input) {
    var doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
}

$( "#countryNames" ).on("input", function(){

    let normalisedUserAnswer =
        $(this).val()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replaceAll('-', ' ')
            .replaceAll('\'', ' ')
            .replaceAll('’', ' ')
            .toLowerCase();

    for (const country of countries) {
        let decryptedCountry  = CryptoJSAesJson.decrypt(htmlDecode(country), "W0rldQu!z123");
        let normalisedCountry =
            decryptedCountry
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replaceAll('-', ' ')
                .replaceAll('\'', ' ')
                .replaceAll('’', ' ')
                .toLowerCase();

        if(normalisedUserAnswer === normalisedCountry){
            $(this).val('');
            $.ajax({
                url: '/get-country-iso/' + decryptedCountry,
                method: 'GET'
            }).done(function(countryIso) {
                let dataItem = polygonSeries.getDataItemById(countryIso);
                dataItem.get("mapPolygon").set("fill", am5.color('#00ff00'));
            });
        }
    }

});
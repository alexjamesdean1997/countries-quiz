import * as am5 from "@amcharts/amcharts5";
import * as am5map from "@amcharts/amcharts5/map";
import am5geoData_worldLow from "@amcharts/amcharts5-geodata/worldLow";
import am5geoData_data_countries2 from "@amcharts/amcharts5-geodata/data/countries2";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";
import { cryptoJSAesJson } from './CryptoJSAesJson';
import { stringNormalizer } from './StringNormalizer'
import $ from 'jquery';
import { startGameTimer } from './GameTimer';
import { incrementScore } from './ScoreManager';

function htmlDecode(input) {
    var doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
}

function sortFoundCountries() {
    $('.continent ul').each(function() {
        $(this).find("li").sort(ascendingSort).appendTo($(this));
    });
}

function ascendingSort(a, b){
    return ($(b).text()) < ($(a).text()) ? 1 : -1;
}

function isCorrectAnswer(correctAnswer, userAnswer) {
    let normalisedCorrectAnswer = stringNormalizer(correctAnswer);
    let normalisedUserAnswer    = stringNormalizer(userAnswer);

    return normalisedCorrectAnswer === normalisedUserAnswer;
}

function saveCorrectAnswer(countryName) {
    $.ajax({
        url: '/country-name-found/' + countryName,
        method: 'POST'
    }).done(function(countryNamesLeft) {
        if(0 === Number(countryNamesLeft)){
            stopCountryNamesGame();
        }
    });
}

function stopCountryNamesGame() {
    clearInterval(Interval);
    $.ajax({
        url: '/stop-country-names-game',
        method: 'POST'
    }).done(function(forgottenCountries) {
        manageStopGameDisplay(forgottenCountries)
    });
}

function answerAlreadySaved(userAnswer, savedAnswers) {
    let answerAlreadySaved = false
    savedAnswers.forEach((answer) => {
        if (stringNormalizer(userAnswer) === stringNormalizer(answer)) {
            answerAlreadySaved = true;
        }
    });
    return answerAlreadySaved;
}

function addCountryToAnswers(countryInfo, itemClass) {
    const continentCode = am5geoData_data_countries2[countryInfo['iso']].continent_code;
    let   continentEl   = $(`.continent[data-continent-code='${continentCode}']`);
    continentEl.css('display', 'inline-block');
    continentEl.find('ul').append('<li class="country-name bold ' + itemClass + '" data-country-iso="' + countryInfo['iso'] + '">' + countryInfo['name'] + '</li>');
    savedAnswers.push(countryInfo['name']);
}

function highlightCountryMap(countryInfo, success) {
    let color = '#8ac926';

    if (false === success) {
        color = '#dc3545';
    }

    let dataItems           = [];
    let mainCountryDataItem = polygonSeries.getDataItemById(countryInfo['iso']);
    dataItems.push(mainCountryDataItem);

    if ('DK' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('GL'));
    }

    if ('GB' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('AI'));
        dataItems.push(polygonSeries.getDataItemById('KY'));
        dataItems.push(polygonSeries.getDataItemById('MS'));
        dataItems.push(polygonSeries.getDataItemById('TC'));
    }

    if ('FR' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('BL'));
        dataItems.push(polygonSeries.getDataItemById('GF'));
        dataItems.push(polygonSeries.getDataItemById('GP'));
        dataItems.push(polygonSeries.getDataItemById('MF'));
        dataItems.push(polygonSeries.getDataItemById('MQ'));
        dataItems.push(polygonSeries.getDataItemById('NC'));
        dataItems.push(polygonSeries.getDataItemById('PF'));
        dataItems.push(polygonSeries.getDataItemById('PM'));
        dataItems.push(polygonSeries.getDataItemById('RE'));
        dataItems.push(polygonSeries.getDataItemById('WF'));
        dataItems.push(polygonSeries.getDataItemById('YT'));
    }

    if ('NL' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('BQ'));
        dataItems.push(polygonSeries.getDataItemById('CW'));
        dataItems.push(polygonSeries.getDataItemById('SX'));
    }

    if ('NO' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('SJ'));
    }

    if ('US' === countryInfo['iso']){
        dataItems.push(polygonSeries.getDataItemById('AS'));
        dataItems.push(polygonSeries.getDataItemById('VI'));
    }

    dataItems.forEach((dataItem) => {
        dataItem.get("mapPolygon").set("fill", am5.color(color));
        dataItem.get("mapPolygon").set("tooltipText", countryInfo['name']);
    });

    if (true === success) {
        polygonSeries.zoomToDataItem(mainCountryDataItem);

        setTimeout(function(){
            chart.goHome();
        },2000);
    }
}

function manageStopGameDisplay(forgottenCountries) {
    $("#stopCountryNamesGame").hide();
    $("#restartCountryNamesGame").show();

    forgottenCountries.forEach((country) => {
        let dataItem = polygonSeries.getDataItemById(country['iso']);
        dataItem.get("mapPolygon").set("fill", am5.color('#dc3545'));
        dataItem.get("mapPolygon").set("tooltipText", country['name']);
        highlightCountryMap(country, false)
        addCountryToAnswers(country, 'red');
    });

    sortFoundCountries();
}

function manageCorrectAnswerDisplay(decryptedCountry) {
    $.ajax({
        url: '/get-country-info/' + decryptedCountry,
        method: 'GET'
    }).done(function(countryInfo) {
        highlightCountryMap(countryInfo, true);
        addCountryToAnswers(countryInfo, 'green');
        sortFoundCountries();
        incrementScore();
    });
}

let root = am5.Root.new("mapContainer");

root.setThemes([
    am5themes_Animated.new(root)
]);

let chart = root.container.children.push(am5map.MapChart.new(root, {
    panX: "rotateX",
    panY: "translateY",
    projection: am5map.geoNaturalEarth1(),
    maxZoomLevel: 40,
}));

let backgroundSeries = chart.series.unshift(
    am5map.MapPolygonSeries.new(root, {})
);

backgroundSeries.mapPolygons.template.setAll({
    fill: am5.color("#f9f9f9"),
    stroke: am5.color("#ccc"),
});

backgroundSeries.data.push({
    geometry: am5map.getGeoRectangle(90, 180, -90, -180)
});

let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
    geoJSON: am5geoData_worldLow,
    fill: am5.color('#0066ff'),
    exclude: ["AQ"]
}));

polygonSeries.mapPolygons.template.setAll({
    toggleKey: "active",
    interactive: true,
    templateField: "polygonSettings"
});

polygonSeries.data.setAll([
    {
        id: "EH",
        polygonSettings: {
            fill: am5.color("#AAAAAA")
        }
    },
    {
        id: "PS",
        polygonSettings: {
            fill: am5.color("#AAAAAA")
        }
    },
    {
        id: "XK",
        polygonSettings: {
            fill: am5.color("#AAAAAA")
        }
    },
]);

let previousPolygon;

polygonSeries.mapPolygons.template.on("active", function (active, target) {
    if (previousPolygon && previousPolygon !== target) {
        previousPolygon.set("active", false);
    }
    if (target.get("active")) {
        polygonSeries.zoomToDataItem(target.dataItem);
    }
    else {
        chart.goHome();
    }
    previousPolygon = target;
});

chart.set("zoomControl", am5map.ZoomControl.new(root, {}));
chart.get("zoomControl").plusButton.get("background").set("fill", am5.color("#0066ff"));
chart.get("zoomControl").minusButton.get("background").set("fill", am5.color("#0066ff"));

chart.chartContainer.get("background").events.on("click", function () {
    chart.goHome();
})

chart.appear();

let Interval = setInterval(startGameTimer, 1000);

let savedAnswers = [];

$( "#countryNames" ).on("input", function(){
    const userAnswer = $(this).val();

    if (true === answerAlreadySaved(userAnswer, savedAnswers)){
        return;
    }

    for (const country of countries) {
        let decryptedCountry  = cryptoJSAesJson.decrypt(htmlDecode(country), "W0rldQu!z123");

        if (isCorrectAnswer(decryptedCountry, userAnswer)){
            $(this).val('');
            saveCorrectAnswer(decryptedCountry);
            manageCorrectAnswerDisplay(decryptedCountry);
        }
    }
});

$("#stopCountryNamesGame").click(function() {
    stopCountryNamesGame();
});

$(document).on("click", ".country-name" , function() {
    let dataItem = polygonSeries.getDataItemById($(this).attr('data-country-iso'));
    polygonSeries.zoomToDataItem(dataItem);
});

console.log(am5geoData_data_countries2);
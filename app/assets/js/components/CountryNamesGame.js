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
    $.ajax({
        url: '/stop-country-names-game',
        method: 'POST'
    }).done(function() {
        $("#stopCountryNamesGame").hide();
        $("#restartCountryNamesGame").show();
        clearInterval(Interval);
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

function addCountryToSavedAnswers(countryInfo) {
    const continentCode = am5geoData_data_countries2[countryInfo['iso']].continent_code;
    let   continentList = $(`.continent[data-continent-code='${continentCode}']`).find('ul');
    continentList.append('<li>' + countryInfo['name'] + '</li>');
    savedAnswers.push(countryInfo['name']);
}

function highlightCountryMap(countryInfo) {
    let dataItem = polygonSeries.getDataItemById(countryInfo['iso']);
    dataItem.get("mapPolygon").set("fill", am5.color('#8ac926'));
    dataItem.get("mapPolygon").set("tooltipText", countryInfo['name']);
}

function manageCorrectAnswerDisplay(decryptedCountry) {
    $.ajax({
        url: '/get-country-info/' + decryptedCountry,
        method: 'GET'
    }).done(function(countryInfo) {
        highlightCountryMap(countryInfo);
        addCountryToSavedAnswers(countryInfo);
        sortFoundCountries();
        incrementScore();
    });
}

let root = am5.Root.new("mapContainer");

root.setThemes([
    am5themes_Animated.new(root)
]);

let chart = root.container.children.push(am5map.MapChart.new(root, {
    panX: "translateX",
    panY: "translateY",
    projection: am5map.geoMercator()
}));

let polygonSeries = chart.series.push(am5map.MapPolygonSeries.new(root, {
    geoJSON: am5geoData_worldLow,
    fill: am5.color('#1982c4'),
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

chart.set("zoomControl", am5map.ZoomControl.new(root, {}));

chart.chartContainer.get("background").events.on("click", function () {
    chart.goHome();
})

chart.appear();

let Interval = setInterval(startGameTimer, 1000);

let savedAnswers = [];

$( "#countryNames" ).on("input", function(){
    const userAnswer = $(this).val();

    if (true === answerAlreadySaved(userAnswer, savedAnswers)){
        $(this).val('');
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